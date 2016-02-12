(function() {
    'use strict';

    angular
        .module('app.pharmacy.prescription')
        .controller('CustomerPageController', CustomerPageController);

    function CustomerPageController($scope, CustomerResource, CustomerService, PHARMACY_SETTINGS, $mdDialog, FormService, $stateParams, CommonService) {

        $scope.query = {
            filter: '',
            limit: PHARMACY_SETTINGS.pagination.per_page,
            order: 'name',
            page: 1
        };

        $scope.onChange = function () {
            return CustomerResource.get($scope.query, success).$promise;
        };
        function success(items) {
            $scope.items = items.data;
        }
        function getItems() {
            $scope.onChange();
        }


        //open add dialog
        $scope.openAddDialog = function() {
            CustomerService.getFormData().then(function(results) {
                $scope.is_add = true;
                $scope.customerDialogForm = {
                    id: results.nextId,
                    name: "",
                    credit_limit: "0.00",
                    balance: "0.00",
                    address: "",
                    city: "",
                    email: "",
                    phone: "",
                    contact: "",
                    errors: []
                }
                $mdDialog.show({
                    templateUrl: '/app/pharmacy/order/templates/customer-add-dialog.tmpl.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose:true,
                    scope: $scope,
                    preserveScope: true
                });
            });
        }

        //open edit dialog
        $scope.openEditDialog = function(id) {
            CustomerService.getFormData(id).then(function(results) {
                $scope.customerDialogForm = results.item;
                $scope.is_add = false;
                $mdDialog.show({
                    templateUrl: '/app/pharmacy/order/templates/customer-add-dialog.tmpl.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose:true,
                    scope: $scope,
                    preserveScope: true
                });
            });
        }

        if (angular.isDefined($stateParams.id)) {
            $scope.openEditDialog(null, $stateParams.id);
        }

        //submit add form
        $scope.submitCustomerDialogForm = function() {

            var item = new CustomerResource($scope.customerDialogForm);

            if ($scope.is_add) {
                delete item.id;
                item.$save(function(data) {
                    CommonService.message(data.data.message, 'success');
                    $scope.cancel();
                    getItems();
                }, function(response){
                    FormService.showErrors(response);
                });
            } else {
                item.id = $scope.customerDialogForm.id;
                item.$update(function(data) {
                    CommonService.message(data.data.message, 'success');
                    $scope.cancel();
                    getItems();
                }, function(response){
                    FormService.showErrors(response);
                });
            }
        }

        //close dialog
        $scope.cancel = function($event) {
            CommonService.closeModal($event);
        };

    }
})();