(function() {
    'use strict';

    angular
        .module('app.pharmacy.inventory')
        .controller('InventoryInPageController', InventoryInPageController);

    /* @ngInject */
    function InventoryInPageController($scope, ProductResource, InventoryResource, InventoryService, $q, PHARMACY_SETTINGS, CommonService, $location, API_CONFIG, $rootScope) {

        $scope.PHARMACY_SETTINGS = PHARMACY_SETTINGS;
        $scope.CommonService = CommonService;

        $scope.showForm = false;

        //auto complete for product
        $scope.searchText = '';
        $scope.querySearch = function(query) {
            var query = {title: query, is_active: 1};
            return ProductResource.get(query).$promise.then(function(response) {
                return response.data.data;
            });
        }
        $scope.productSelected = function (value) {
            if (value !== null && angular.isDefined(value)) {
                $scope.inventoryForm.product_id = value.id;
                $scope.inventoryForm.product = value;
                $scope.inventoryForm.qoh = value.qoh;
            }
        }


        $scope.initForm = function () {
            InventoryService.getFormData().then(function(results) {
                $scope.inventories = [];
                $scope.suppliers = results.suppliers;

                $scope.showForm = true;
                $scope.searchText = '';
                $scope.searchSupplierText = '';
                $scope.inventoryForm = {
                    product_id: "",
                    qoh: "",
                    quantity_on: "",
                    product: null,
                    reason: '',
                    expiry_date: ""
                }
                angular.element('#productAutoComplete').focus().val('');
                $rootScope.addFormEditingChecking($scope.inventoryForm);
            });
        }

        $scope.initForm();

        $scope.addItem = function() {

            $scope.inventories.push({
                product_id: $scope.inventoryForm.product_id,
                quantity_on: $scope.inventoryForm.quantity_on,
                supplier_id: $scope.inventoryForm.supplier_id,
                supplier_name: $scope.getSupplierName($scope.inventoryForm.supplier_id),
                product: $scope.inventoryForm.product,
                expiry_date: $scope.inventoryForm.expiry_date,
                reason: $scope.inventoryForm.reason,
                expiry_date_short: CommonService.validDate($scope.inventoryForm.expiry_date, PHARMACY_SETTINGS.date_format)
            });

            //clear product form
            var reason = $scope.inventoryForm.reason;
            $scope.inventoryForm = {
                product_id: "",
                qoh: "",
                quantity_on: "",
                product: null,
                reason: reason,
                expiry_date: ''
            }
            angular.element('#productAutoComplete').focus().val('');
        }

        $scope.removeItem = function(item) {
            var inventories = [];
            angular.forEach($scope.inventories, function(v,k) {
                if (v != item) {
                    inventories.push(v);
                }
            });
            $scope.inventories = inventories;
        }

        //submit form
        $scope.submitForm = function() {

            if ($scope.inventories.length == 0) {
                CommonService.message('Please add at least one item', 'error');
                return;
            }

            $scope.promises = [];

            angular.forEach($scope.inventories, function(v, k){
                var insert = new InventoryResource({
                    product_id: v.product_id,
                    quantity_on: v.quantity_on,
                    type: 'in',
                    reason: v.reason,
                    expiry_date: CommonService.validDate(v.expiry_date, API_CONFIG.db_date_format),
                    supplier_id: v.supplier_id
                });
                $scope.promises.push(insert.$save());
            });

            $q.all($scope.promises).then(function(data){
                $rootScope.destroyFormEditingChecking();
                CommonService.message('Successfully saved.', 'success');
                $location.path('/inventory');
            });
        }

        $scope.getSupplierName = function(id) {
            var name = id;
            angular.forEach($scope.suppliers, function(v, k) {
                if (v.id == id) {
                    name = v.name;
                }
            });
            return name;
        }

    }
})();