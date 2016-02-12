(function() {
    'use strict';

    angular
        .module('app.pharmacy.inventory')
        .controller('InventoryEditPageController', InventoryEditPageController);

    /* @ngInject */
    function InventoryEditPageController($scope, InventoryService, CommonService, $stateParams, hotkeys) {

        //hotkeys
        $scope.hotkeysArray = [
            {
                combo: 'f3',
                allowIn: ['INPUT', 'SELECT', 'TEXTAREA'],
                callback: function() {
                    $scope.print();
                }
            }
        ];
        angular.forEach($scope.hotkeysArray, function(v) {
            hotkeys.add(v);
        });

        $scope.showForm = false;

        InventoryService.getFormData($stateParams.id).then(function(results) {
            setFormData(results);
            $scope.showForm = true;
            $scope.inventoryForm = results.item;
        });

        function setFormData(data) {
            $scope.suppliers = data.suppliers;
        }

        //return valid date
        $scope.validDate = function (date, format) {
            return CommonService.validDate(date, format);
        }

        $scope.print = function () {
            console.log($scope.inventoryForm)
            CommonService.print('return_drug', $scope.inventoryForm.id);
        }

    }
})();