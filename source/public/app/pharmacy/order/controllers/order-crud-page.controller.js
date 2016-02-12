(function() {
    'use strict';

    angular
        .module('app.pharmacy.prescription')
        .controller('OrderCrudPageController', OrderCrudPageController)
        .directive('calculatorWidget', CalculatorWidget);

    function OrderCrudPageController(
        $rootScope,
        $scope,
        $state,
        $stateParams,
        $location,
        $mdDialog,
        $timeout,
        hotkeys,

        ProductResource,
        PrescriptionResource,
        OrderResource,
        CustomerResource,

        OrderService,
        CommonService,
        FormService,
        ProductService
    ) {

        //hotkeys
        $scope.hotkeysArray = [
            {
                combo: 'f3',
                allowIn: ['INPUT', 'SELECT', 'TEXTAREA'],
                callback: function(event) {
                    event.preventDefault();
                    $scope.print();
                }
            },
            {
                combo: 'f6',
                allowIn: ['INPUT', 'SELECT', 'TEXTAREA'],
                callback: function(event) {
                    event.preventDefault();
                    $scope.orders();
                }
            },
            {
                combo: 'f7',
                allowIn: ['INPUT', 'SELECT', 'TEXTAREA'],
                callback: function(event) {
                    event.preventDefault();
                    $scope.openTenderedDialog();
                }
            },
            {
                combo: 'f10',
                allowIn: ['INPUT', 'SELECT', 'TEXTAREA'],
                callback: function(event) {
                    event.preventDefault();
                    $scope.saveOrder();
                }
            }
        ];
        angular.forEach($scope.hotkeysArray, function(v) {
            hotkeys.add(v);
        });

        $scope.CommonService = CommonService;

        $scope.showForm = false;

        //auto complete for product search
        $scope.productSearchText = null;
        $scope.productQuerySearch = function(query) {
            return ProductResource.get({ title: query, is_active: 1 }).$promise.then(function(response) {
                return response.data.data;
            });
        }
        $scope.productSelected = function (value) {
            if (value !== null && angular.isDefined(value)) {
                $scope.searchProductForm.product = value;
                $scope.searchProductForm.qoh = value.qoh;
                $scope.searchProductForm.product_id = value.id;
            }
        }

        //auto complete for prescription search
        $scope.prescriptionSearchText = '';
        $scope.prescriptionQuerySearch = function(query) {
            return PrescriptionResource.get({ patient_name: query, status: ['-paid'] }).$promise.then(function(response) {
                var output = [];
                angular.forEach(response.data.data, function(v, k) {
                    var statusLabel = $scope.getStatusLabel(v.status);
                    v.title = v.patient.name + ' - ' + v.id + ' - $' + v.net_due + ' (' + statusLabel + ')';
                    output.push(v);
                });
                $timeout(function(){
                    angular.element('.auto-complete-disabled-option').parent().unbind('click')
                }, 300);
                return output;
            });
        }
        $scope.prescriptionSelected = function (value) {
            if (value !== null && angular.isDefined(value)) {
                if (value.status != 'completed') {
                    $scope.clearPrescriptionSearchForm();
                    CommonService.message('Please choose completed prescription', 'error');
                } else {
                    $scope.searchPrescriptionForm.prescription = value;
                    $scope.searchPrescriptionForm.prescription_id = value.id;
                }
            }
        }

        //toggle form
        $scope.toggleSearchForm = function() {
            $scope.searchForm = $scope.searchForm == 'product' ? 'prescription' : 'product';

            $timeout(
                function() {
                    if ($scope.searchForm == 'product') {
                        angular.element('#productAutoComplete').focus();
                    } else {
                        angular.element('#prescriptionAutoComplete').focus();
                    }
                },
                100
            );
        }

        $scope.$watch('orderForm.order_items', function(oldVal, newVal){
            $scope.calculateTotals();
        }, true);


        $scope.searchForm = 'product';
        $scope.searchProductForm = {
            product: "",
            product_id: "",
            qty: ""
        }
        $scope.searchPrescriptionForm = {
            prescription: "",
            prescription_id: ""
        }
        $scope.orderForm = {
            label: '',
            type: 'sell',
            total: '0.00',
            discount: '0.00',
            taxes: '0.00',
            net_due: '0.00',
            paid_type: '',
            tendered: '0.00',
            change: '0.00',
            order_items: [],
            multi_payment_cash: 0,
            multi_payment_cheque: 0,
            multi_payment_credit_card: 0,
            multi_payment_other: 0,
            customer_id: '',
            description: ''
        }

        if (angular.isUndefined($stateParams.id)) {
            OrderService.getFormData().then(function(results) {
                $scope.is_add = true;
                $scope.showForm = true;
                $timeout(
                    function() {
                        angular.element('#productAutoComplete').focus();
                    },
                    100
                );
                $rootScope.addFormEditingChecking($scope.orderForm);
            });

        } else {
            OrderService.getFormData($stateParams.id).then(function(results) {

                $scope.refunded_orders = [];

                angular.forEach(results.refunded_orders, function(o,k) {
                    angular.forEach(o.order_items, function(oi,k) {
                        $scope.refunded_orders.push({
                            id: o.id,
                            qty: oi.qty,
                            date: oi.created_at_full,
                            product_id: oi.product.id,
                            product: oi.product,
                            prescription_id: oi.prescription.id,
                            prescription: oi.prescription,
                            product_title: oi.product.title
                        });
                    });
                });

                $scope.orderForm.id = results.item.id;
                $scope.orderForm.label = results.item.label;
                $scope.orderForm.total = results.item.total;
                $scope.orderForm.discount = results.item.discount;
                $scope.orderForm.taxes = results.item.taxes;
                $scope.orderForm.net_due = results.item.net_due;
                $scope.orderForm.paid_type = results.item.paid_type;
                $scope.orderForm.tendered = results.item.tendered;
                $scope.orderForm.description = results.item.description;
                $scope.orderForm.status = results.item.status;
                $scope.orderForm.type = results.item.type;
                angular.forEach(results.item.order_items, function(v,k) {
                    if (v.product_id) {
                        $scope.addProduct(v);
                    } else if (v.prescription_id) {
                        $scope.addPrescription(v);
                    }
                });
                $scope.is_add = false;
                $scope.showForm = true;
                $timeout(
                    function() {
                        angular.element('#productAutoComplete').focus();
                    },
                    100
                );
                $rootScope.addFormEditingChecking($scope.orderForm);
            });
        }

        //submit product search form
        $scope.submitProductSearchForm = function() {

            if ($scope.searchProductForm.qty <= 0) {
                CommonService.message('Please set QTY', 'error');
                return;
            }

            if (!$scope.addProduct($scope.searchProductForm)) {
                CommonService.message('QTY can\'t be more than QOH', 'error');
                return;
            }

            $scope.searchProductForm = {
                product: "",
                product_id: "",
                qty: 0
            }

            $timeout(
                function() {
                    angular.element('#productAutoComplete').focus().val('');
                },
                100
            );

        }

        $scope.addProduct = function (formItem){
            if (formItem.qty <= formItem.product.qoh) {
                $scope.productSearchText = null;
                formItem.price = formItem.product.unit_price;
                formItem.tax = 0;
                formItem.net_due = 0;

                ProductService.calculatePrice(formItem);
                $scope.orderForm.order_items.push(formItem);
                return true;
            } else {
                return false;
            }
        }

        //submit prescription search form
        $scope.submitPrescriptionSearchForm = function() {
            $scope.addPrescription($scope.searchPrescriptionForm);
            $scope.clearPrescriptionSearchForm();
        }

        $scope.clearPrescriptionSearchForm = function() {
            $scope.prescriptionSearchText = '';
            $scope.searchPrescriptionForm = {
                prescription: "",
                prescription_id: ""
            }
            $timeout(
                function() {
                    angular.element('#prescriptionAutoComplete').focus().val('');
                },
                100
            );
        }

        $scope.addPrescription = function (formItem){
            $scope.orderForm.order_items.push(formItem);
        }

        $scope.productQuantityChanged = function (){
            $timeout(function(){
                angular.forEach($scope.orderForm.order_items, function(item, k) {
                    if ( angular.isUndefined(item.qty)) {
                        $scope.orderForm.order_items[k].qty = item.product.qoh;
                    }
                    ProductService.calculatePrice(item);
                });
                $scope.calculateTotals();
            }, 700);
        }

        $scope.discountChanged = function (){
            $timeout(function(){
                $scope.calculateTotals();
            }, 500);
        }

        $scope.tenderedChanged = function (value, fieldName){
            if ($scope.orderForm.paid_type == 'multi_payment') {
                if (angular.isDefined($scope.tenderedForm[fieldName])) {
                    $scope.orderForm[fieldName] = $scope.tenderedForm[fieldName];
                    var tendered_value = 0;
                    angular.forEach(['multi_payment_cash', 'multi_payment_cheque', 'multi_payment_credit_card', 'multi_payment_other'], function(item, k) {
                        if (angular.isDefined($scope.orderForm[item]) && $scope.orderForm[item] > 0) {
                            tendered_value += CommonService.numbersFormat($scope.orderForm[item]);
                        }
                    });
                    $scope.orderForm.tendered = CommonService.numbersFormat(tendered_value);
                }
            } else if (angular.isDefined($scope.tenderedForm.tendered)) {
                $scope.orderForm.tendered = $scope.tenderedForm.tendered;
            }

            $scope.calculateTotals();
        }

        $scope.tenderedPaidTypeChanged = function (){
            $scope.orderForm.tendered = 0;
            $scope.orderForm.multi_payment_cash = 0;
            $scope.orderForm.multi_payment_cheque = 0;
            $scope.orderForm.multi_payment_credit_card = 0;
            $scope.orderForm.multi_payment_other = 0;

            $scope.tenderedForm.tendered = 0;
            $scope.tenderedForm.multi_payment_cash = 0;
            $scope.tenderedForm.multi_payment_cheque = 0;
            $scope.tenderedForm.multi_payment_credit_card = 0;
            $scope.tenderedForm.multi_payment_other = 0;
        }

        $scope.calculateTotals = function() {
            $scope.orderForm.total = 0;
            $scope.orderForm.taxes = 0;
            $scope.orderForm.net_due = 0;

            angular.forEach($scope.orderForm.order_items, function(item, k) {
                if (item.product) {
                    $scope.orderForm.total = CommonService.numbersFormat($scope.orderForm.total) + CommonService.numbersFormat(item.total);
                    $scope.orderForm.taxes = CommonService.numbersFormat($scope.orderForm.taxes) + CommonService.numbersFormat(item.tax);
                    $scope.orderForm.net_due = CommonService.numbersFormat($scope.orderForm.net_due) + CommonService.numbersFormat(item.total);
                } else if (item.prescription) {
                    $scope.orderForm.total = CommonService.numbersFormat($scope.orderForm.total) + CommonService.numbersFormat(item.prescription.net_due);
                    $scope.orderForm.taxes = CommonService.numbersFormat($scope.orderForm.taxes) + CommonService.numbersFormat(item.prescription.tax);
                    $scope.orderForm.net_due = CommonService.numbersFormat($scope.orderForm.net_due) + CommonService.numbersFormat(item.prescription.net_due);
                }
            });

            if (CommonService.numbersFormat($scope.orderForm.discount) > 0 && CommonService.numbersFormat($scope.orderForm.discount) < CommonService.numbersFormat($scope.orderForm.net_due)) {
                $scope.orderForm.net_due -= CommonService.numbersFormat($scope.orderForm.discount);
            } else {
                $scope.orderForm.discount = '0.00';
            }

            //tendered and change
            if ($scope.orderForm.tendered >= $scope.orderForm.net_due) {
                $scope.orderForm.change = CommonService.numbersFormat($scope.orderForm.tendered) - CommonService.numbersFormat($scope.orderForm.net_due);
            } else {
                $scope.orderForm.change = '0.00';
            }

            angular.forEach(['total', 'taxes', 'net_due', 'tendered', 'change'], function(item, k) {
                if (angular.isDefined($scope.orderForm[item])) {
                    $scope.orderForm[item] = CommonService.priceFormat($scope.orderForm[item]);
                    $scope.orderForm[item] = $scope.orderForm[item].replace(",", "");
                }
            });
        }

        $scope.removeItem = function (item) {
            $scope.orderForm.order_items = _.filter($scope.orderForm.order_items, function(v) {
                return v !== item;
            });
        }

        //save form
        $scope.saveOrder = function(finish) {

            if ($scope.orderForm.status != 'completed' && $scope.orderForm.type  != 'refund') {
                $scope.is_finish_order = false;
                if (angular.isDefined(finish)) {
                    $scope.is_finish_order = true;
                    $scope.orderForm.status = 'completed';
                    $scope.saveProcess();
                    return;
                }

                var order = new OrderResource($scope.orderForm);

                //type validation, because required not work for md-radio-group
                if (!$scope.validateOrderForm()) {
                    CommonService.message('Order is empty', 'error');
                    return;
                }

                if (order.label.length == 0) {
                    $mdDialog.show({
                        templateUrl: CommonService.prepareTemplateSrc("/app/pharmacy/order/templates/set-order-label-dialog.tmpl.html"),
                        parent: angular.element(document.body),
                        clickOutsideToClose:true,
                        scope: $scope,
                        preserveScope: true,
                        onComplete: function() {
                            angular.element('#order-label').focus();
                        }
                    });
                } else {
                    $scope.saveProcess();
                }
            }

        }

        $scope.validateOrderForm = function() {
            var order = new OrderResource($scope.orderForm);
            if (order.order_items.length == 0) {
                return false;
            }
            return true;
        }

        $scope.saveProcess = function() {

            var order = new OrderResource($scope.orderForm);

            if ($scope.is_add) {
                delete order.id;
                order.$save(function(data) {
                    $rootScope.destroyFormEditingChecking();
                    CommonService.message(data.data.message, 'success');
                    $location.path("/order");
                }, function(response){
                    FormService.showErrors(response);
                });
            } else {
                order.id = $scope.orderForm.id;
                order.$update(function(data) {
                    $rootScope.destroyFormEditingChecking();
                    CommonService.message(data.data.message, 'success');
                    if ($scope.is_finish_order) {
                        $location.path("/order");
                    }
                }, function(response){
                    FormService.showErrors(response);
                });
            }
            $scope.cancel();
        }

        $scope.clearPage = function() {
            $state.go('triangular.admin-default.order-add-page', {}, {reload: true});
        }

        $scope.openTenderedDialog = function() {
            if ($scope.validateOrderForm() && $scope.orderForm.status != 'completed') {

                $scope.tenderedForm = {
                    tendered: 0,
                    multi_payment_cash: 0,
                    multi_payment_cheque: 0,
                    multi_payment_credit_card: 0,
                    multi_payment_other: 0
                }

                OrderService.getTenderedFormData().then(function(results) {
                    $scope.paid_types = results.paidTypes;
                    $scope.customer = false;
                    $scope.orderForm.tendered = '0.00';
                    $scope.orderForm.paid_type = 'cash';
                    $mdDialog.show({
                        templateUrl: CommonService.prepareTemplateSrc("/app/pharmacy/order/templates/tendered-dialog.tmpl.html"),
                        parent: angular.element(document.body),
                        clickOutsideToClose:false,
                        scope: $scope,
                        preserveScope: true,
                        hasBackdrop: false,
                        autoWrap: false,
                        onComplete: function() {
                            angular.element('.md-dialog-container').css('z-index', 'auto');
                            //init auto-complete for customers
                            $scope.customerSearchText = null;
                            $scope.customerQuerySearch = function(query) {
                                return CustomerResource.get({ name: query }).$promise.then(function(response) {
                                    return response.data.data;
                                });
                            }
                            $scope.customerSelected = function (value) {
                                if (value !== null && angular.isDefined(value)) {
                                    $scope.customer = value;
                                    $scope.orderForm.customer_id = value.id;
                                }
                            }
                            angular.element('#tenderedInput').focus().val('');
                        }
                    });
                });
            }
        }

        $scope.submitTenderedForm = function() {
            //check customer id type "charge"
            if ($scope.orderForm.paid_type == 'charge') {
                if (!$scope.customer) {
                    CommonService.message('Please select customer', 'error');
                    return;
                } else if (CommonService.numbersFormat($scope.customer.credit_limit) < CommonService.numbersFormat($scope.orderForm.net_due)) {
                    CommonService.message('The credit limit is too small', 'error');
                    return;
                }
            } else if (CommonService.numbersFormat($scope.orderForm.tendered) < CommonService.numbersFormat($scope.orderForm.net_due)) {
                CommonService.message('The tendered is too small', 'error');
                return;
            }
            $scope.cancel();
            $scope.calculateTotals();
        }

        $scope.getStatusLabel = function (value) {
            var label = '';
            switch (value) {
                case 'in_process':
                    label = 'In Process';
                    break;
                case 'completed':
                    label = 'Completed';
                    break;
                case 'paid':
                    label = 'Paid';
                    break;
                case 'void':
                    label = 'Void';
                    break;

                default :
                    label = value;
                    break;
            }
            return label;
        }

        $scope.validDate = function(date, format) {
            CommonService.validDate(date, format);
        }

        $scope.cancel = function($event) {
            CommonService.closeModal($event);
        }

        $scope.refund = function() {
            $location.path("/order/refund/"+$scope.orderForm.id);
        }

    }

    function CalculatorWidget(CommonService) {
        var directive = {
            bindToController: true,
            controller: CalculatorController,
            restrict: 'E',
            templateUrl: CommonService.prepareTemplateSrc("/app/pharmacy/order/templates/calculator-widget.tmpl.html")
        };
        return directive;
    }

    function CalculatorController($scope, CommonService, $mdDialog) {

        $scope.openCalculatorPopup = function (){
            $mdDialog.show({
                templateUrl: CommonService.prepareTemplateSrc("/app/pharmacy/order/templates/calculator-widget-popup.tmpl.html"),
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
        }

        // calculator state
        $scope.displayValue = 0;                    //current value displayed on calculator screen
        $scope.valueA = 0;                          //first (left) value that will be used for computation
        $scope.valueB = 0;                          //second (right) value that will be used for computation
        $scope.selectedOperation = null;            //last operation selected by user
        $scope.clearValue = true;                   //should value displayed on screen be cleared after new digit pressed?


        //constants
        $scope.equalSignKey = {label: "="};

        $scope.digitKeys = [
            {label: "1", value: 1}, {label: "2", value: 2}, {label: "3", value: 3},
            {label: "4", value: 4}, {label: "5", value: 5}, {label: "6", value: 6},
            {label: "7", value: 7}, {label: "8", value: 8}, {label: "9", value: 9},
            {label: "0", value: 0}, {label: ".", value: '.'}
        ];

        $scope.operationKeys = [
            {label: "+", operation: function (a, b) {return a + b}},
            {label: "-", operation: function (a, b) {return a - b}},
            {label: "/", operation: function (a, b) {return a / b}},
            {label: "*", operation: function (a, b) {return a * b}}
        ];


        // actions
        /**
         * When digit is clicked, it should be added to displayed value or replace displayed value.
         * Also new displayed value should be treated as second operation value.
         * @param digit what digit was clicked
         */
        $scope.digitClicked = function (digit) {
            if ($scope.clearValue) {
                $scope.displayValue = digit;
                $scope.clearValue = false;
            } else {
                var dV = $scope.displayValue + '';
                if (dV.indexOf('.') != -1) {
                    $scope.displayValue = $scope.displayValue + '' + digit;
                } else {
                    $scope.displayValue = parseFloat($scope.displayValue) + '' + digit;
                }

            }
            $scope.valueB = $scope.displayValue
        };

        /**
         * When operation key is clicked operation should be remembered,
         * displayed value should be treated as first and second number to perform operation on
         * and next pushed digit should replace the displayed value
         * @param operation which operation was clicked
         */
        $scope.operationClicked = function (operation) {
            $scope.selectedOperation = operation;
            $scope.valueA = $scope.displayValue;
            $scope.valueB = $scope.displayValue;
            $scope.clearValue = true;
        };

        /**
         * Computes the result based on remembered operation and two values and displays the result.
         * Also next pushed digit should replace the displayed value
         * and current result should be treated as first value for next operation.
         */
        $scope.compute = function () {
            if($scope.selectedOperation!=null) {
                var dV = $scope.selectedOperation(parseFloat($scope.valueA), parseFloat($scope.valueB));
                //$scope.displayValue = Math.floor($scope.selectedOperation($scope.valueA, $scope.valueB));
                $scope.displayValue = dV.toFixed(2);
                $scope.clearValue = true;
                $scope.valueA = $scope.displayValue;
            }
        }
    }
})();