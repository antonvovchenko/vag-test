(function() {
    'use strict';

    angular
        .module('app.pharmacy.prescription')
        .controller('PrescriptionCrudPageController', PrescriptionCrudPageController);

    function PrescriptionCrudPageController(
        $rootScope,
        $scope,
        $stateParams,
        $state,
        $location,
        $mdDialog,
        $timeout,
        ProductResource,
        PatientResource,
        PrescriberResource,
        LabelCodeResource,
        PrescriptionResource,
        PrescriptionService,
        PatientService,
        PrescriberService,
        CommonService,
        FormService,
        TransactionService,
        ProductService,
        hotkeys) {

        //hotkeys
        $scope.hotkeysArray = [
            {
                combo: 'f2',
                allowIn: ['INPUT', 'SELECT', 'TEXTAREA'],
                callback: function() {
                    $scope.submitPrescriptionForm();
                }
            },
            {
                combo: 'f3',
                allowIn: ['INPUT', 'SELECT', 'TEXTAREA'],
                callback: function() {
                    $scope.printPrescription();
                }
            },
            {
                combo: 'f7',
                allowIn: ['INPUT', 'SELECT', 'TEXTAREA'],
                callback: function() {
                    $scope.swipeCardOpen();
                }
            },
            {
                combo: 'f8',
                allowIn: ['INPUT', 'SELECT', 'TEXTAREA'],
                callback: function() {
                    $scope.adjudicate();
                }
            }
        ];
        angular.forEach($scope.hotkeysArray, function(v) {
            hotkeys.add(v);
        });

        $scope.CommonService = CommonService;

        $scope.showForm = false;
        $scope.is_refill = false;


        $scope.printPrescriptionButton = true;
        $scope.printPrescriptionLabelsButton = true;

        $scope.prescriptionForm = {
            patient_id: null,
            pharmacist_user_id: null,
            prescriber_id: null,
            prescription_items: [],
            insurance_cards: [],
            total: 0,
            insurance: 0,
            patient: 0,
            discount_percent: 0,
            discount_flat: 0,
            net_due: 0
        }

        if (angular.isDefined($stateParams.id) || angular.isDefined($stateParams.refill_id)) {

            if (angular.isDefined($stateParams.refill_id)) {
                $scope.is_refill = true;
            }

            PrescriptionService.getFormData(angular.isDefined($stateParams.id) ? $stateParams.id : $stateParams.refill_id).then(function(results) {

                $scope.prescriptionForm = _.merge($scope.prescriptionForm, results.item);

                setFormData(results);

                setPatient($scope.prescriptionForm.patient);
                setPrescriber($scope.prescriptionForm.prescriber);

                $scope.prescriptionForm.prescription_items = [];
                angular.forEach(results.item.prescription_items, function(v,k) {
                    setPrescriptionItemForm(v.product);
                    $scope.prescriptionItemForm.id = v.id;
                    $scope.prescriptionItemForm.qty = v.qty;
                    $scope.prescriptionItemForm.course_days = v.course_days;
                    $scope.prescriptionItemForm.refill_allowed_times = v.refill_allowed_times;
                    $scope.prescriptionItemForm.fee_id = v.fee_id;
                    $scope.prescriptionItemForm.label_codes = v.label_codes;
                    $scope.prescriptionItemForm.prescription_item_requests = v.prescription_item_requests;
                    $scope.prescriptionItemForm = $scope.setAdjudicateStatus($scope.prescriptionItemForm);
                    $scope.prescriptionItemForm.is_readable = v.is_readable;
                    $scope.setFeeValue($scope.prescriptionItemForm);
                    $scope.addPrescriptionItem();
                    setNHFCardToTop();
                });

                $scope.updateCounters();

                $rootScope.addFormEditingChecking($scope.prescriptionForm);
            });

        } else {

            setPrescriptionItemForm();
            setPatient();
            setPrescriber();

            PrescriptionService.getFormData().then(function(results) {
                $scope.updateCounters();
                setFormData(results);
                $rootScope.addFormEditingChecking($scope.prescriptionForm);
            });
        }

        $scope.submitPrescriptionForm = function(is_complete) {

            if (!$scope.validatePrescriptionForm()) {
                return;
            }

            if (angular.isDefined(is_complete)) {
                $scope.prescriptionForm.status = 'completed';
            }

            $scope.savePrescriptionForm().then(
                function(data) {
                    CommonService.message(data.data.message, 'success');
                    $location.path("/prescription/edit/"+data.data.item.id);
                }, function(response){
                    FormService.showErrors(response);
                }
            );

        }

        $scope.savePrescriptionForm = function () {
            var prescription = new PrescriptionResource($scope.prescriptionForm);

            if ($scope.is_refill) {
                delete prescription.id;
                prescription.status = 'in_process';
            }

            $rootScope.destroyFormEditingChecking();

            if (angular.isUndefined(prescription.id)) {
                return prescription.$save();
            } else {
                return prescription.$update();
            }
        }

        $scope.viewPatientHistory = function () {
            PatientService.getHistory($scope.patientForm.id).then(function (results) {
                $scope.patientHistoryItems = results.data;
                $mdDialog.show({
                    templateUrl: CommonService.prepareTemplateSrc("/app/pharmacy/prescription/templates/patient-history-dialog.tmpl.html"),
                    parent: angular.element(document.body),
                    clickOutsideToClose:false,
                    scope: $scope,
                    preserveScope: true,
                    hasBackdrop: false,
                    autoWrap: false,
                    onComplete: function() {
                        angular.element('.md-dialog-container').css('z-index', 'auto');
                    }
                });
            });
        }

        $scope.voidPrescription = function() {
            CommonService.confirm(
                'Are you sure?',
                function() {
                    PrescriptionService.reversePrescriptionTransactions($scope.prescriptionForm.id).then(function(response){
                            $scope.prescriptionForm.status = 'void';
                            $scope.savePrescriptionForm().then(
                                function(data) {
                                    CommonService.message(data.data.message, 'success');
                                    $state.go($state.current, {}, {reload: true});
                                }, function(response){
                                    FormService.showErrors(response);
                                }
                            );
                        },
                        function(response){
                            FormService.showErrors(response);
                        });
                }
            );
        }

        $scope.reverseAllTransactions = function() {
            CommonService.confirm(
                'Are you sure?',
                function() {
                    PrescriptionService.reversePrescriptionTransactions($scope.prescriptionForm.id).then(function(response){
                            CommonService.message(response.data.message, 'success');
                            $state.go($state.current, {}, {reload: true});
                        },
                        function(response){
                            FormService.showErrors(response);
                        });
                }
            );
        }

        $scope.validatePrescriptionForm = function () {
            if ($scope.prescriptionForm.prescription_items.length == 0) {
                CommonService.message('Please set at least one product', 'error');
                return false;
            }
            if (!$scope.prescriptionForm.patient_id) {
                CommonService.message('Please set Patient', 'error');
                return false;
            }
            if (!$scope.prescriptionForm.pharmacist_user_id) {
                CommonService.message('Please set RXIST', 'error');
                return false;
            }
            if (!$scope.prescriptionForm.prescriber_id) {
                CommonService.message('Please set Doctor', 'error');
                return false;
            }
            return true;
        }

        function setFormData(data) {
            $scope.users = data.users;
            $scope.fees = data.fees;
            $scope.showForm = true;

            $scope.$watch('prescriptionForm.prescription_items', function(oldVal, newVal){
                $scope.updateCounters();
            }, true);
        }

        $scope.updateCounters = function(discount) {
            $scope.rxCounter = 0;
            $scope.otcCounter = 0;
            $scope.price = {
                rx: 0.00,
                otc: 0.00
            }
            if (angular.isDefined($scope.prescriptionForm)) {

                $scope.prescriptionForm.total= 0.00;
                angular.forEach($scope.prescriptionForm.prescription_items, function(item, index){
                    if (item.is_prescription == 1) {
                        $scope.rxCounter ++;
                        $scope.price.rx += CommonService.numbersFormat(item.total);
                    } else {
                        $scope.otcCounter ++;
                        $scope.price.otc += CommonService.numbersFormat(item.total);
                    }
                    $scope.prescriptionForm.total += CommonService.numbersFormat(item.total);
                });

                $scope.prescriptionForm.patient = CommonService.numbersFormat($scope.prescriptionForm.total - $scope.prescriptionForm.insurance);

                //discount
                if (angular.isDefined(discount) && discount) {
                    updateDiscounts(discount);
                } else if ($scope.prescriptionForm.discount_percent) {
                    updateDiscounts('percent');
                } else if ($scope.prescriptionForm.discount_flat) {
                    updateDiscounts('flat');
                }
            }
        }

        $scope.$watch('prescriptionForm.discount_flat', function(oldVal, newVal){
            $timeout(function(){
                $scope.updateCounters('flat');
            }, 1000);
        });

        $scope.$watch('prescriptionForm.discount_percent', function(oldVal, newVal){
            $timeout(function(){
                $scope.updateCounters('percent');
            }, 1000);
        });

        function updateDiscounts(discountType) {
            if (!$scope.discounts_update_in_progress) {
                $scope.prescriptionForm.net_due = CommonService.numbersFormat($scope.prescriptionForm.total - $scope.prescriptionForm.insurance);
                if (discountType == 'flat' && $scope.prescriptionForm.discount_flat > 0) {
                    if (CommonService.numbersFormat($scope.prescriptionForm.discount_flat) > $scope.prescriptionForm.net_due) {
                        $scope.prescriptionForm.discount_flat = 0;
                    } else if (CommonService.numbersFormat($scope.prescriptionForm.discount_flat) > 0 ) {
                        $scope.prescriptionForm.discount_percent = CommonService.priceFormat(
                            CommonService.numbersFormat($scope.prescriptionForm.discount_flat)*100/$scope.prescriptionForm.net_due
                        );
                        $scope.prescriptionForm.net_due = CommonService.priceFormat(
                            $scope.prescriptionForm.net_due - CommonService.numbersFormat($scope.prescriptionForm.discount_flat)
                        );
                    }
                } else if (discountType == 'percent' && $scope.prescriptionForm.discount_percent > 0) {
                    if (CommonService.numbersFormat($scope.prescriptionForm.discount_percent) > 100) {
                        $scope.prescriptionForm.discount_percent = 0;
                    } else if (CommonService.numbersFormat($scope.prescriptionForm.discount_percent) > 0 ) {
                        $scope.prescriptionForm.discount_flat = $scope.prescriptionForm.net_due * CommonService.numbersFormat($scope.prescriptionForm.discount_percent) / 100;
                        if (CommonService.numbersFormat($scope.prescriptionForm.discount_flat) > $scope.prescriptionForm.net_due) {
                            $scope.prescriptionForm.discount_flat = 0;
                        } else {
                            $scope.prescriptionForm.net_due = CommonService.priceFormat(
                                $scope.prescriptionForm.net_due - CommonService.numbersFormat($scope.prescriptionForm.discount_flat)
                            );
                        }
                    }
                }
                $scope.prescriptionForm.discount_flat = parseFloat($scope.prescriptionForm.discount_flat).toFixed(Math.max(0, ~~2));

                //lock changes on 2 sec
                $scope.discounts_update_in_progress = true;
                $timeout(function(){
                    $scope.discounts_update_in_progress = false;
                }, 2000);
            }
        }

        function setIndexes(array) {
            angular.forEach(array, function(item, index){
                item.index = index;
            });
        }

        /******************************PRESCRIPTION ITEMS*******************************************************/

        //auto complete for Label Codes in prescription item form
        $scope.labelCodeSearchText = null;
        $scope.labelCodeSearch = function (query, form) {
            return LabelCodeResource.get({title: query}).$promise.then(function(response) {
                return _.filter(response.data.data, function(n) {
                    return _.isEmpty(_.find(form.label_codes, n));
                });
            });
        }
        $scope.labelCodeSelected = function (labelCode, form) {
            if (!_.isEmpty(labelCode)) {
                form.label_codes.push(labelCode);
            }
        }

        //auto complete for product in prescription item form
        $scope.prItemQuerySearch = function(query) {
            return ProductResource.get({title: query, is_active: 1}).$promise.then(function(response) {
                return response.data.data;
            });
        }
        $scope.prItemSelected = function (value) {
            if (angular.isDefined(value) && angular.isObject(value)) {
                setPrescriptionItemForm(value);
            }
        }

        $scope.addPrescriptionItem = function (prescriptionItemFormHtml) {
            if ($scope.prescriptionItemForm.product_id != '') {
                //add to array
                var item = angular.copy($scope.prescriptionItemForm);
                $scope.prescriptionForm.prescription_items.push(item);
                setIndexes($scope.prescriptionForm.prescription_items);

                //counters
                $scope.updateCounters();

                //clear form
                setPrescriptionItemForm();
                angular.element('#presriptionItemProductAutoComplete').focus().val('');
                if (angular.isDefined(prescriptionItemFormHtml)) {
                    prescriptionItemFormHtml.$setPristine();
                    prescriptionItemFormHtml.$setUntouched();
                    prescriptionItemFormHtml.$setValidity();
                }

            } else {
                CommonService.message('Please set valid product', 'error');
            }
        }

        $scope.removePrescriptionItem = function (index) {
            if (angular.isDefined($scope.prescriptionForm.prescription_items[index])) {
                var array = [];
                angular.forEach($scope.prescriptionForm.prescription_items, function (v,k){
                    if (k != index) {
                        array.push(v);
                    }
                });
                setIndexes(array);
                $scope.prescriptionForm.prescription_items = array;
                $scope.updateCounters();
            }
        }

        function setPrescriptionItemForm (product) {
            $scope.prescriptionItemForm = {
                product_id: '',
                product: null,
                qoh: '',
                price: '0.00',
                tax:'0.00',
                total: '0.00',
                fee_id: '',
                fee_value: '0.00',
                qty: '',
                course_days: '',
                refill_allowed_times: '',
                label_codes: [],
                is_prescription: null,
                prItemSearchText: ''
            }
            if (angular.isDefined(product) && angular.isObject(product)) {
                setItemValues($scope.prescriptionItemForm, product);
            }

        }

        $scope.prAddedItemSelected = function (product, item) {
            if (angular.isDefined(product) && angular.isObject(product)) {
                setItemValues(item, product);
            }
        }

        $scope.quantityChanged = function (form) {
            ProductService.calculatePrice(form);
        }

        $scope.setGeneric = function ($event, form) {
            $event.stopPropagation();
            $event.preventDefault();
            setItemValues(form, form.product_generic);
        }

        function setItemValues(item, product) {
            if (angular.isDefined(product) && product) {
                item.is_prescription = product.is_prescription;

                if (!_.isEmpty(product.generic)) {
                    item.product_generic = product.generic;
                } else if (!_.isEmpty(item.product_generic)) {
                    item.product_generic = item.product;
                } else {
                    item.product_generic = null;
                }

                item.product_id = product.id;
                item.product_title = product.title;
                item.product = product;
                item.qoh = product.qoh;
                item.price = product.unit_price;
                item.label_codes = product.label_codes;
                item.prItemSearchText = product.title;
                $scope.quantityChanged(item);
            }
        }

        $scope.setFeeValue = function(form) {
            form.fee_value = '0.00';
            angular.forEach ($scope.fees, function (v) {
                if (v.id*1 == form.fee_id*1 && v.fees.length > 0) {
                    form.fee_value = v.fees;
                }
            });
            ProductService.calculatePrice(form);
            $scope.updateCounters();
        }

        $scope.setAdjudicateStatus = function(item) {
            item.adjudicateItemStatus = null;
            angular.forEach(item.prescription_item_requests, function(v,k) {
                if (v.status == 'success' && v.type == 'adjudicate') {
                    item.adjudicateItemStatus = 'success';
                }
                if (item.adjudicateItemStatus != 'success' && v.status == 'error' && (v.type == 'reversal' || v.type == 'adjudicate')) {
                    item.adjudicateItemStatus = 'reversal';
                }
            });
            return item;
        }

        $scope.hasGeneric = function (form) {
            if (form.product_generic)
                return true;
            else
                return false;
        }

        /******************************PATIENT*******************************************************/

        //auto complete for patient
        $scope.patientSearchText = null;
        $scope.selectedPatient = null;
        $scope.patientQuerySearch = function(query) {
            return PatientResource.get({name: query}).$promise.then(function(response) {
                return response.data.data;
            });
        }
        $scope.patientSelected = function (value) {
            setPatient(value);
        }

        $scope.addPatientDialog = function($event, defaultValues) {
            if (angular.isDefined($event) && $event) {
                $event.stopPropagation();
                $event.preventDefault();
            }

            PatientService.getFormData().then(function(results) {
                $scope.patientDialogForm = {
                    id: results.nextId,
                    name: "",
                    address: "",
                    city: "",
                    email: "",
                    phone: "",
                    errors: []
                }

                if (angular.isDefined(defaultValues)) {
                    $scope.patientDialogForm.name = defaultValues.first_name + ' ' + defaultValues.last_name;
                }

                $mdDialog.show({
                    templateUrl: CommonService.prepareTemplateSrc('/app/pharmacy/prescription/templates/patient-add-dialog.tmpl.html'),
                    parent: angular.element(document.body),
                    clickOutsideToClose:true,
                    scope: $scope,
                    preserveScope: true
                });
            });
        }

        $scope.submitPatientDialogForm = function() {
            var patient = new PatientResource($scope.patientDialogForm);
            delete patient.id;
            patient.$save(function(data) {
                setPatient(data.data.item);
                $scope.cancel();
            }, function(response){
                FormService.showErrors(response);
            });
        }

        function setPatient(patient) {

            $scope.patientSearchText = null;
            $scope.selectedPatient = null;

            $scope.patientForm = {
                address: "",
                city: "",
                email: "",
                phone: ""
            }

            if (angular.isDefined(patient)) {
                $scope.patientForm.id = patient.id;
                $scope.patientForm.address = patient.address;
                $scope.patientForm.city = patient.city;
                $scope.patientForm.phone = patient.phone;
                $scope.patientForm.name = patient.name;

                $scope.prescriptionForm.patient_id = patient.id;

                $scope.selectedPatient = patient;
                $scope.patientSearchText = patient.name;
            }
        }

        //swipe insurance cards
        $scope.swipe = {
            is_open_form: false,
            card_data: ""
        }
        $scope.swipeCardOpen = function () {
            $scope.swipe.is_open_form = !$scope.swipe.is_open_form;
        }
        $scope.swipeCardExit = function ($event) {
            $event.stopPropagation();
            $event.preventDefault();
            $scope.swipe.card_data = '';
            $scope.swipe.is_open_form = false;
        }
        $scope.swipeCardSubmit = function () {
            TransactionService.parseCardInfo($scope.swipe.card_data).then(function(response) {
                if (response) {
                    if (!response.data.cardInformationData) {
                        CommonService.message('Card is not valid', 'error');
                    } else {
                        $scope.addCard(response.data.cardInformationData);
                        if (!response.data.patient) {
                            $scope.addPatientDialog(false, response.data.cardInformationData);
                        } else {
                            setPatient(response.data.patient);
                        }
                        $scope.swipe.card_data = '';
                    }
                }
            });
        }
        $scope.addCard = function(card) {
            var isAdded = false;
            angular.forEach($scope.prescriptionForm.insurance_cards, function (v,k){
                if (v.id == card.id) {
                    isAdded = true;
                }
            });
            if (!isAdded) {
                $scope.prescriptionForm.insurance_cards.push(card);
            }
            setNHFCardToTop();
        }
        $scope.removeCard = function(id) {
            var array = [];
            angular.forEach($scope.prescriptionForm.insurance_cards, function (v,k){
                if (v.id != id) {
                    array.push(v);
                }
            });
            $scope.prescriptionForm.insurance_cards = array;
            setNHFCardToTop();
        }
        function setNHFCardToTop() {
            $scope.prescriptionForm.insurance_cards.sort(function(a,b) {
                if (a.bin_number == b.bin_number)
                    return 0;
                if (a.bin_number == 'NHF' && b.bin_number != 'NHF')
                    return -1;
                if (a.bin_number != 'NHF' && b.bin_number == 'NHF')
                    return 1;
            });
        }
        $scope.showAdjudicateInfo = function (index) {
            if (angular.isDefined($scope.prescriptionForm.prescription_items[index]) && angular.isDefined($scope.prescriptionForm.prescription_items[index].prescription_item_requests) && $scope.prescriptionForm.prescription_items[index].prescription_item_requests.length > 0) {
                $scope.adjudicateInfoItem = $scope.prescriptionForm.prescription_items[index];
                $mdDialog.show({
                    templateUrl: CommonService.prepareTemplateSrc('/app/pharmacy/prescription/templates/prescription-form/_prescription_adjudicate_dialog.tmpl.html'),
                    parent: angular.element(document.body),
                    clickOutsideToClose:true,
                    scope: $scope,
                    preserveScope: true
                });
            }
        }

        /******************************PRESCRIBER*******************************************************/

        //auto complete for prescriber
        $scope.prescriberSearchText = null;
        $scope.prescriberQuerySearch = function(query) {
            return PrescriberResource.get({name: query}).$promise.then(function(response) {
                return response.data.data;
            });
        }
        $scope.prescriberSelected = function (value) {
            setPrescriber(value);
        }

        $scope.addPrescriberDialog = function($event) {
            $event.stopPropagation();
            $event.preventDefault();

            PrescriberService.getFormData().then(function(results) {
                $scope.is_add = true;
                $scope.prescriberDialogForm = {
                    id: results.nextId,
                    first_name: "",
                    last_name: "",
                    registration_number: "",
                    notes: "",
                    errors: []
                }
                $mdDialog.show({
                    templateUrl: CommonService.prepareTemplateSrc('/app/pharmacy/prescription/templates/prescriber-add-dialog.tmpl.html'),
                    parent: angular.element(document.body),
                    targetEvent: $event,
                    clickOutsideToClose:true,
                    scope: $scope,
                    preserveScope: true
                });
            });
        }

        $scope.submitPrescriberDialogForm = function() {
            var prescriber = new PrescriberResource($scope.prescriberDialogForm);
            prescriber.$save(function(data) {
                setPrescriber(data.data.item);
                $scope.cancel();
            }, function(response){
                FormService.showErrors(response.data.data.errors);
            });
        }

        function setPrescriber(prescriber) {

            $scope.prescriberSearchText = null;
            $scope.selectedPrescriber = null;

            $scope.prescriberForm = {}

            if (angular.isDefined(prescriber)) {
                $scope.prescriberForm.first_name = prescriber.first_name;
                $scope.prescriberForm.last_name = prescriber.last_name;

                $scope.prescriptionForm.prescriber_id = prescriber.id;

                $scope.selectedPrescriber = prescriber;
                $scope.prescriberSearchText = prescriber.name;
            }
        }

        /******************************ADJUDICATE*******************************************************/

        $scope.adjudicate = function() {

            if (!$scope.validatePrescriptionForm()) {
                return;
            }

            if ($scope.prescriptionForm.insurance_cards.length == 0) {
                CommonService.message('Please swipe card', 'error');
                return;
            }

            $scope.savePrescriptionForm().then(
                function(data) {
                    $scope.prescriptionForm.id = data.data.item.id;
                    $scope.adjudicateProcess(data.data.item);
                }, function(response){
                    FormService.showErrors(response);
                }
            );
        }

        $scope.adjudicateProcess = function(form) {
            TransactionService.adjudicate({ 'prescription': form}).then(function(response) {
                if (response) {
                    CommonService.message('Adjudicate finished', 'success');
                    $scope.cancel();
                    if (angular.isDefined($state.params.id)) {
                        $state.go($state.current, {}, {reload: true});
                    } else {
                        $location.path("/prescription/edit/"+form.id);
                    }
                }
            });
        }

        $scope.reversalRequest = function (item) {
            TransactionService.reversal({ 'transactionId': item.id}).then(function(response) {
                CommonService.message('Successfully reversed', 'success');
                $scope.cancel();
                $state.go($state.current, {}, {reload: true});
            });
        }

        $scope.prepareError = function(value) {
            if (angular.isDefined(value))
                return value.replace('<br />', '.');
            else
                return '';
        }

        //close dialog
        $scope.isErrorDetails = function(name) {
            return name.indexOf('_details') !== -1;
        }

        $scope.cancel = function($event) {
            CommonService.closeModal($event);
        }

        $scope.printPrescription = function() {
            $scope.printPrescriptionButton = false;
            $timeout(function(){
                $scope.printPrescriptionButton = true;
            }, 1500);
            CommonService.print('prescription', $scope.prescriptionForm.id);
        }

        $scope.printLabels = function() {
            $scope.printPrescriptionLabelsButton = false;
            $timeout(function(){
                $scope.printPrescriptionLabelsButton = true;
            }, 1500);
            CommonService.print('prescription_labels', $scope.prescriptionForm.id);
        }
    }
})();