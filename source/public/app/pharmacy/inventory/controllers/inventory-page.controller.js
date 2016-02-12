(function() {
    'use strict';

    angular
        .module('app.pharmacy.inventory')
        .controller('InventoryPageController', InventoryPageController);

    /* @ngInject */
    function InventoryPageController($scope, InventoryResource, PHARMACY_SETTINGS) {

        $scope.query = {
            filter: '',
            limit: PHARMACY_SETTINGS.pagination.per_page,
            order: '-created_at',
            page: 1
        };

        //get items
        $scope.onChange = function () {
            return InventoryResource.get($scope.query, success).$promise;
        };
        function success(items) {
            $scope.items = items.data;
        }
    }
})();