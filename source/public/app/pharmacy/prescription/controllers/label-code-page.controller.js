(function() {
    'use strict';

    angular
        .module('app.pharmacy.prescription')
        .controller('LabelCodePageController', LabelCodePageController);

    /* @ngInject */
    function LabelCodePageController($scope, LabelCodeResource, LabelCodeService, PHARMACY_SETTINGS, CommonService, $mdDialog, FormService, $stateParams) {

        $scope.query = {
            filter: '',
            limit: PHARMACY_SETTINGS.pagination.per_page,
            order: 'title',
            page: 1
        };

        $scope.onChange = function () {
            return LabelCodeResource.get($scope.query, success).$promise;
        };
        function success(items) {
            $scope.items = items.data;
        }
        function getItems() {
            $scope.onChange();
        }

        //open add dialog
        $scope.openAddDialog = function($event) {
            LabelCodeService.getFormData().then(function(results) {
                $scope.is_add = true;
                $scope.labelCodeDialogForm = {
                    id: results.nextId,
                    name: "",
                    address: "",
                    city: "",
                    email: "",
                    phone: "",
                    errors: []
                }
                $mdDialog.show({
                    templateUrl: '/app/pharmacy/prescription/templates/label-code-add-dialog.tmpl.html',
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
            LabelCodeService.getFormData(id).then(function(results) {
                $scope.labelCodeDialogForm = results.item;
                $scope.is_add = false;
                $mdDialog.show({
                    templateUrl: '/app/pharmacy/prescription/templates/label-code-add-dialog.tmpl.html',
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
        $scope.submitLabelCodeDialogForm = function() {

            var item = new LabelCodeResource($scope.labelCodeDialogForm);

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
                item.id = $scope.labelCodeDialogForm.id;
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