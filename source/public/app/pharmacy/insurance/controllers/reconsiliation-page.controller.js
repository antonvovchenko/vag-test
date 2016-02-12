(function() {
    'use strict';

    angular
        .module('app.pharmacy.prescription')
        .controller('ReconciliationPageController', ReconciliationPageController)
        .config(function($mdDateLocaleProvider) {
            $mdDateLocaleProvider.formatDate = function(date) {
                return date ? moment(date).format('D MMM YYYY') : '';
            };

            $mdDateLocaleProvider.parseDate = function(dateString) {
                var m = moment(dateString, 'D MMM YYYY', true);
                return m.isValid() ? m.toDate() : new Date(NaN);
            };
        });

    function ReconciliationPageController($rootScope, $scope, TransactionService, TransactionResource, CommonService, PHARMACY_SETTINGS, $q) {

        $scope.CommonService = CommonService;

        $scope.isShow = false;
        $scope.isShowSearch = false;

        $scope.currentDate = new Date();

        function initForm(){
            $scope.form = {
                insurance_provider: null,
                gct: $scope.gct,
                insurance_service_fee: $scope.insurance_service_fee,
                reference_id: ''
            }
            $rootScope.addFormEditingChecking($scope.form);
        }
        function initSearchForm(){
            $scope.searchForm = {
                from_date: new Date( $scope.currentDate.getFullYear(), $scope.currentDate.getMonth()-1, $scope.currentDate.getDate()),
                to_date: new Date( $scope.currentDate.getFullYear(), $scope.currentDate.getMonth()+1, $scope.currentDate.getDate()),
                items: []
            }
            $scope.query = {
                from_date: $scope.searchForm.from_date,
                to_date: $scope.searchForm.to_date,
                insurance_provider: $scope.form.insurance_provider,
                is_payment_confirmed: 0,
                type: 'adjudicate',
                status: 'success',
                limit: 5,
                order: '-created_at',
                page: 1
            };
            $scope.reconciliation_list = [];
        }

        TransactionService.getReconciliationFormData().then(function(data) {

            $scope.insurance_providers = data.insurance_providers;
            $scope.gct = data.gct.value;
            $scope.insurance_service_fee = data.insurance_service_fee.value;

            initForm();

            $scope.isShow = true;
        });

        $scope.start = function(){
            initSearchForm();
            $scope.isShowSearch = true;
        }

        $scope.resetProvider = function(){
            $scope.isShowSearch = false;
            initForm();
            initSearchForm();
        }

        $scope.search = function(){
            $scope.query.page = 1;
            getItems();
        }

        $scope.clearSearch = function(){
            initSearchForm();
        }

        $scope.addTransaction = function(transaction){
            var is_added = false;
            angular.forEach($scope.reconciliation_list, function(v, k){
                if (v.transaction.id == transaction.id) {
                    is_added = true;
                }
            });
            if (!is_added) {
                $scope.reconciliation_list.push({
                    transaction: transaction,
                    total: transaction.total,
                    insurance_paid: transaction.insurance_paid,
                    actual: transaction.insurance_paid,
                    fee: '',
                    tax: '',
                    paid: ''
                });
                $scope.recalculate();
            }
        }

        $scope.recalculate = function(){
            $scope.totals = {
                total: 0,
                insurance_paid: 0,
                actual: 0,
                fee: 0,
                tax: 0,
                paid: 0
            }
            angular.forEach($scope.reconciliation_list, function(v, k){
                var fee = $scope.form.insurance_service_fee * v.actual / 100;
                $scope.reconciliation_list[k].fee = CommonService.priceFormat(fee);
                var tax = fee * $scope.form.gct / 100;
                $scope.reconciliation_list[k].tax = CommonService.priceFormat(tax);
                $scope.reconciliation_list[k].paid = CommonService.priceFormat(v.actual - fee - tax);

                $scope.totals.total += v.total;
                $scope.totals.insurance_paid += v.insurance_paid;
                $scope.totals.actual += v.actual;
                $scope.totals.fee += v.fee;
                $scope.totals.paid += v.paid;

            });
            $scope.totals.total = CommonService.priceFormat($scope.totals.total);
            $scope.totals.insurance_paid = CommonService.priceFormat($scope.totals.insurance_paid);
            $scope.totals.actual = CommonService.priceFormat($scope.totals.actual);
            $scope.totals.fee = CommonService.priceFormat($scope.totals.fee);
            $scope.totals.paid = CommonService.priceFormat($scope.totals.paid);
        }

        $scope.removeTransaction = function(transaction){
            var transactions = [];
            angular.forEach($scope.reconciliation_list, function(v, k){
                if (v != transaction) {
                    transactions.push(v);
                }
            });
            $scope.reconciliation_list = transactions;
        }

        $scope.updateTransactions = function(){

            if ($scope.reconciliation_list.length == 0) {
                CommonService.message('Please select least one transaction', 'error');
                return;
            }
            if ($scope.form.reference_id.length == 0) {
                CommonService.message('Please set reference #', 'error');
                return;
            }

            $scope.promisies = [];

            angular.forEach($scope.reconciliation_list, function(v, k){
                var update = new TransactionResource({
                    id: v.transaction.id,
                    reference_id: $scope.form.reference_id,
                    actual: v.actual,
                    fee: v.fee,
                    tax: v.tax,
                    paid: v.paid,
                    is_payment_confirmed: 1
                });
                $scope.promisies.push(update.$update());
            });
            $q.all($scope.promisies).then(function(data){
                $rootScope.destroyFormEditingChecking();
                CommonService.message('Successfully updated.', 'success');
                $scope.resetProvider();
            });

        }

        //init transactions search
        $scope.onChange = function () {
            $scope.query.from_date = $scope.searchForm.from_date;
            $scope.query.to_date = $scope.searchForm.to_date;
            return TransactionResource.get($scope.query, success).$promise;
        };
        function success(response) {
            $scope.searchForm.items = response.data.data;
            $scope.searchForm.total = response.data.total;
        }
        function getItems() {
            $scope.onChange();
        }

    }
})();