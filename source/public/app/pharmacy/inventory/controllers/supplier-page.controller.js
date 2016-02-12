(function() {
    'use strict';

    angular
        .module('app.pharmacy.inventory')
        .controller('SupplierPageController', SupplierPageController);

    /* @ngInject */
    function SupplierPageController(SupplierResource, $mdDialog, $scope, SupplierService, PHARMACY_SETTINGS, CommonService, FormService, $stateParams) {

        $scope.query = {
            filter: '',
            limit: PHARMACY_SETTINGS.pagination.per_page,
            order: 'name',
            page: 1
        };

        $scope.onChange = function () {
            return SupplierResource.get($scope.query, success).$promise;
        };

        function success(items) {
            $scope.items = items.data;
        }
        function getItems() {
            $scope.onChange();
        }

        //open add dialog
        $scope.openAddDialog = function($event) {
            SupplierService.getFormData().then(function(results) {
                $scope.is_add = true;
                $scope.itemForm = {
                    id: results.nextId,
                    name: "",
                    address: "",
                    city: "",
                    postal_code: "",
                    eorder_number: "",
                    last_date: "",
                    lead_time: "",
                    contact: "",
                    phone: "",
                    fax: "",
                    email: "",
                    errors: []
                }
                $mdDialog.show({
                    templateUrl: '/app/pharmacy/inventory/templates/supplier-add-dialog.tmpl.html',
                    parent: angular.element(document.body),
                    targetEvent: $event,
                    clickOutsideToClose:true,
                    scope: $scope,
                    preserveScope: true
                });
            });
        }

        //open edit dialog
        $scope.openEditDialog = function($event, id) {

            SupplierService.getFormData(id).then(function(results) {
                $scope.itemForm = results.item;
                $scope.itemForm.last_date = $scope.validDate($scope.itemForm.last_date, PHARMACY_SETTINGS.date_format)
                $scope.is_add = false;
                $mdDialog.show({
                    templateUrl: '/app/pharmacy/inventory/templates/supplier-add-dialog.tmpl.html',
                    parent: angular.element(document.body),
                    targetEvent: $event,
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
        $scope.submit = function() {

            var item = new SupplierResource($scope.itemForm);
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
        //return valid date
        $scope.validDate = function (date, format) {
            return CommonService.validDate(date, format);
        }

    }
})();