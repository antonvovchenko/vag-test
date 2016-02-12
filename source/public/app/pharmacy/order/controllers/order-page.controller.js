(function() {
    'use strict';

    angular
        .module('app.pharmacy.prescription')
        .controller('OrderPageController', OrderPageController);

    function OrderPageController($scope, OrderResource, PHARMACY_SETTINGS) {

        $scope.query = {
            filter: '',
            limit: PHARMACY_SETTINGS.pagination.per_page,
            order: '-id',
            page: 1
        };

        $scope.onChange = function () {
            return OrderResource.get($scope.query, success).$promise;
        };
        function success(items) {
            $scope.items = items.data;
        }
        function getItems() {
            $scope.onChange();
        }
    }
})();