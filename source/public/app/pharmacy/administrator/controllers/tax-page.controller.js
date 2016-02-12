(function() {
    'use strict';

    angular
        .module('app.pharmacy.administrator')
        .controller('TaxPageController', TaxPageController);

    /* @ngInject */
    function TaxPageController($scope, TaxResource, TaxService, PHARMACY_SETTINGS, CommonService, $mdDialog, FormService, $stateParams) {

        $scope.query = {
            filter: '',
            limit: PHARMACY_SETTINGS.pagination.per_page,
            order: 'id',
            page: 1
        };

        $scope.onChange = function () {
            return TaxResource.get($scope.query, success).$promise;
        };
        function success(items) {
            $scope.items = items.data;
        }

        //open add dialog
        $scope.openAddDialog = function($event) {
            $scope.is_add = true;
            $scope.itemForm = {
                value: "",
                description: ""
            }
            $mdDialog.show({
                templateUrl: '/app/pharmacy/administrator/templates/tax-add-dialog.tmpl.html',
                parent: angular.element(document.body),
                targetEvent: $event,
                clickOutsideToClose:true,
                scope: $scope,
                preserveScope: true
            });
        }

        //open edit dialog
        $scope.openEditDialog = function($event, id) {
            TaxService.getFormData(id).then(function(results) {
                $scope.itemForm = results.item;
                $scope.is_add = false;
                $mdDialog.show({
                    templateUrl: '/app/pharmacy/administrator/templates/tax-add-dialog.tmpl.html',
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
        $scope.submitTaxDialogForm = function() {

            var item = new TaxResource($scope.itemForm);

            if ($scope.is_add) {
                item.$save(function(data) {
                    CommonService.message(data.data.message, 'success');
                    $scope.cancel();
                    $scope.onChange();
                }, function(response){
                    FormService.showErrors(response);
                });
            } else {
                item.id = $scope.itemForm.id;
                item.$update(function(data) {
                    CommonService.message(data.data.message, 'success');
                    $scope.cancel();
                    $scope.onChange();
                }, function(response){
                    FormService.showErrors(response);
                });
            }
        }

        $scope.removeTax = function(id) {
            var confirm = $mdDialog.confirm()
                .title('Are you sure you want to delete the tax?')
                .content('')
                .ariaLabel('')
                .ok('Remove')
                .cancel('Cancel');
            $mdDialog.show(confirm).then(function() {
                TaxResource.delete({taxId: id})
                    .$promise.then(function(response) {
                        CommonService.message(response.data.message, 'success');
                        $scope.cancel();
                        $scope.onChange();
                    }, function(response){
                        FormService.showErrors(response);
                    });
            });
        }

        //close dialog
        $scope.cancel = function($event) {
            CommonService.closeModal($event);
        };

    }
})();