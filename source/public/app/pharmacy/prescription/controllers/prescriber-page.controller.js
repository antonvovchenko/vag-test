(function() {
    'use strict';

    angular
        .module('app.pharmacy.prescription')
        .controller('PrescriberPageController', PrescriberPageController);

    /* @ngInject */
    function PrescriberPageController($scope, PrescriberResource, PHARMACY_SETTINGS) {

        $scope.query = {
            filter: '',
            limit: PHARMACY_SETTINGS.pagination.per_page,
            order: 'first_name',
            page: 1
        };

        $scope.onChange = function () {
            return PrescriberResource.get($scope.query, success).$promise;
        };
        function success(items) {
            $scope.items = items.data;
        }
        function getItems() {
            $scope.onChange();
        }
    }
})();