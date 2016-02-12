(function() {
    'use strict';

    angular
        .module('app.pharmacy.administrator')
        .controller('LogPageController', LogPageController);

    /* @ngInject */
    function LogPageController($scope, LogResource, $mdDialog, CommonService) {

        $scope.query = {
            filter: '',
            limit: 10,
            order: '-id',
            page: 1
        };

        $scope.onChange = function () {
            return LogResource.get($scope.query, success).$promise;
        };
        function success(items) {
            $scope.items = items.data;
        }
        $scope.onChange();

        $scope.openViewDialog = function($event, id) {
            LogResource.get({logId: id}).$promise.then(function(item) {

                $scope.item = item.data;
                $scope.item.new_value = JSON.parse( $scope.item.new_value);
                $scope.item.old_value = JSON.parse( $scope.item.old_value);

                $mdDialog.show({
                    templateUrl: '/app/pharmacy/administrator/templates/log-view-dialog.tmpl.html',
                    parent: angular.element(document.body),
                    targetEvent: $event,
                    clickOutsideToClose:true,
                    scope: $scope,
                    preserveScope: true
                });
            });
        }

        $scope.getItemUrl = function(item) {
            var url = '';

            if (!_.isEmpty(item.owner)) {

                switch (item.owner_type) {
                    case "App\\Models\\Tax":
                        url = '/#/administrator/taxes/edit/'+item.owner.id;
                        break;
                    case "App\\Models\\User":
                        url = '/#/administrator/users/edit/'+item.owner.id;
                        break;

                    case "App\\Models\\Product":
                    case "App\\Models\\ProductLabelCode":
                        url = '/#/products/edit/'+item.owner.id;
                        break;
                    case "App\\Models\\ProductMarkupType":
                        url = '/#/products/markup-type/edit/'+item.owner.id;
                        break;

                    case "App\\Models\\Inventory":
                        url = '/#/inventory/edit/'+item.owner.id;
                        break;
                    case "App\\Models\\Supplier":
                        url = '/#/inventory/supplier/edit/'+item.owner.id;
                        break;

                    case "App\\Models\\Prescription":
                    case "App\\Models\\PrescriptionItem":
                    case "App\\Models\\PrescriptionItemLabelCode":
                        url = '/#/prescription';
                        break;
                    case "App\\Models\\LabelCode":
                        url = '/#/label-code/edit/'+item.owner.id;
                        break;
                    case "App\\Models\\Prescriber":
                        url = '/#/prescriber/edit/'+item.owner.id;
                        break;
                    case "App\\Models\\Patient":
                        url = '/#/patient/edit/'+item.owner.id;
                        break;
                }
            }

            return url;
        };

        //close dialog
        $scope.cancel = function($event) {
            CommonService.closeModal($event);
        };

    }
})();