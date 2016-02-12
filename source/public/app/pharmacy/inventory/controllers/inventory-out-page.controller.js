(function() {
    'use strict';

    angular
        .module('app.pharmacy.inventory')
        .controller('InventoryOutPageController', InventoryOutPageController);

    function InventoryOutPageController($scope, ProductResource, InventoryResource, InventoryService, CommonService, PHARMACY_SETTINGS, $timeout, $location, $rootScope) {

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
                $scope.batches = [];
                $scope.inventoryForm.product_id = value.id;
                $scope.inventoryForm.product = value;

                //get batches (inventory with type "in")
                if (value.qoh > 0) {
                    InventoryResource.get({order: '-created_at', product_id: value.id, type: 'in'}).$promise.then(function(response) {
                        $scope.batches = response.data.data;
                    });
                } else {
                    CommonService.message('QOH = 0. Please select different product.', 'error');
                    $scope.initForm();
                }
            }
        }

        $scope.inventories = [];

        $scope.initForm = function () {

            InventoryService.getFormData().then(function(results) {
                $scope.suppliers = results.suppliers;
                $scope.pharmacies = [
                    {
                        title: "Pharmacy #2"
                    },
                    {
                        title: "Pharmacy #3"
                    },
                    {
                        title: "Pharmacy #4"
                    }
                ];

                $scope.showForm = true;
                $scope.searchText = '';
                $scope.searchSupplierText = '';
                $scope.inventoryForm = {
                    product_id: "",
                    qoh: "0",
                    quantity_on_out: "",
                    batch_id: "",
                    batch: null,
                    product: null,
                    type: 'supplier',
                    pharmacy: null,
                    reason: ''
                }
                angular.element('#returnDrugsProductAutoComplete').focus().val('');
                $scope.batches = [];
                $rootScope.addFormEditingChecking($scope.inventoryForm);
            });
        }

        $scope.initForm();

        $scope.addItem = function() {

            if ($scope.inventoryForm.quantity_on_out > $scope.inventoryForm.quantity_on_hold) {
                CommonService.message('QTY OUT can\'t be bigger than QOH', 'error');
                return;
            }
            if (!$scope.inventoryForm.batch_id) {
                CommonService.message('Please select batch', 'error');
                return;
            }

            $scope.inventories.push({
                product_id: $scope.inventoryForm.product_id,
                quantity_on_out: $scope.inventoryForm.quantity_on_out,
                batch_id: $scope.inventoryForm.batch_id,
                supplier_id: $scope.inventoryForm.batch.supplier_id,
                product: $scope.inventoryForm.product,
                batch: $scope.inventoryForm.batch
            });

            //clear product form
            var type = $scope.inventoryForm.type;
            var pharmacy = $scope.inventoryForm.pharmacy;
            var reason = $scope.inventoryForm.reason;
            $scope.inventoryForm = {
                product_id: "",
                qoh: "0",
                quantity_on_out: "",
                batch_id: "",
                batch: null,
                product: null,
                reason: reason,
                pharmacy: pharmacy,
                type: type
            }
            angular.element('#returnDrugsProductAutoComplete').focus().val('');
            $scope.batches = [];
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

        $scope.batchSelected = function(batch_id) {
            angular.forEach($scope.batches, function(v,k) {
                if (batch_id == v.batch_id) {
                    $scope.inventoryForm.batch = v;
                    $scope.inventoryForm.batch_id = v.batch_id;
                }
            });
            if ($scope.inventoryForm.batch) {
                $scope.inventoryForm.quantity_on_hold = $scope.inventoryForm.batch.batch_qoh;
            }
        }

        //submit form
        $scope.submitForm = function() {

            if ($scope.inventories.length == 0) {
                CommonService.message('Please add at least one item', 'error');
                return;
            }

            if ($scope.inventoryForm.type == 'shipment' && !$scope.inventoryForm.pharmacy) {
                CommonService.message('Please set pharmacy', 'error');
                return;
            }

            var items = [];

            angular.forEach($scope.inventories, function(v,k) {

                var quantity_on = 0;
                if (v.quantity_on_out.length > 0) {
                    quantity_on = quantity_on - v.quantity_on_out*1;
                }

                var inventory = new InventoryResource();
                inventory.product_id = v.product_id;
                inventory.supplier_id = $scope.inventoryForm.type == 'supplier' ? v.batch.supplier_id  : $scope.inventoryForm.supplier_id;
                inventory.quantity_on = quantity_on;
                inventory.pharmacy = $scope.inventoryForm.type == 'shipment' ? $scope.inventoryForm.pharmacy : '';
                inventory.type = $scope.inventoryForm.type == 'supplier' ? 'return' : 'shipment';
                inventory.batch_id = v.batch_id;
                inventory.reason = $scope.inventoryForm.reason;
                inventory.$save();
            });

            $timeout(function(){
                $rootScope.destroyFormEditingChecking();
                CommonService.message('Successfully saved.', 'success');
                $location.path('/inventory');
            },
            1500);
        }
    }
})();