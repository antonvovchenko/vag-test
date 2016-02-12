(function() {
    'use strict';

    angular
        .module('app.pharmacy.products')
        .controller('MarkupTypePageController', MarkupTypePageController);

    /* @ngInject */
    function MarkupTypePageController($mdDialog, ProductMarkupTypeResource, $scope, PHARMACY_SETTINGS, CommonService, ProductMarkupTypeService, FormService, $stateParams) {

        $scope.query = {
            filter: '',
            limit: PHARMACY_SETTINGS.pagination.per_page,
            order: 'title',
            page: 1
        };

        $scope.onChange = function () {
            return ProductMarkupTypeResource.get($scope.query, success).$promise;
        };
        function success(items) {
            $scope.items = items.data;
        }
        function getItems() {
            $scope.onChange();
        }

        //open add dialog
        $scope.openAddDialog = function($event) {
            ProductMarkupTypeService.getFormData().then(function(results) {
                $scope.itemForm = {
                    id: results.nextId,
                    title: "",
                    description: "",
                    default_markup: "0.00",
                    errors: []
                }
                $scope.is_add = true;
                $mdDialog.show({
                    templateUrl: '/app/pharmacy/products/templates/markup-type-add-dialog.tmpl.html',
                    parent: angular.element(document.body),
                    targetEvent: $event,
                    clickOutsideToClose:true,
                    scope: $scope,
                    preserveScope: true
                });
            });
        }

        //$scope.removeConfirm = function($event, id) {
        //    var confirm = $mdDialog.confirm()
        //        .title('Are you sure you want to delete the record?')
        //        .content('')
        //        .ariaLabel('')
        //        .ok('Remove')
        //        .cancel('Cancel')
        //        .targetEvent($event);
        //    $mdDialog.show(confirm).then(function() {
        //        ProductMarkupTypeResource.delete({markupTypeId: id})
        //            .$promise.then(function() {
        //                getItems();
        //            });
        //    });
        //}

        //open edit dialog
        $scope.openEditDialog = function($event, id) {
            ProductMarkupTypeService.getFormData(id).then(function(results) {
                $scope.itemForm = results.item;
                $scope.is_add = false;
                $mdDialog.show({
                    templateUrl: '/app/pharmacy/products/templates/markup-type-add-dialog.tmpl.html',
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

            var item = new ProductMarkupTypeResource($scope.itemForm);

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

    }
})();