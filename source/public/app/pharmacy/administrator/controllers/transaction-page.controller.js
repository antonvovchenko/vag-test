(function() {
    'use strict';

    angular
        .module('app.pharmacy.administrator')
        .controller('TransactionPageController', TransactionPageController);

    /* @ngInject */
    function TransactionPageController($scope, $mdDialog, TransactionResource, CommonService, $stateParams) {

        $scope.query = {
            filter: '',
            limit: 10,
            order: '-id',
            page: 1
        };

        $scope.onChange = function () {
            return TransactionResource.get($scope.query, success).$promise;
        };
        function success(items) {
            $scope.items = items.data;
        }
        $scope.onChange();

        $scope.openViewDialog = function(id) {
            TransactionResource.get({transactionId: id}).$promise.then(function(item) {

                $scope.transactionRequestItem = item.data;

                $mdDialog.show({
                    templateUrl: '/app/pharmacy/administrator/templates/transaction-request-view-dialog.tmpl.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose:true,
                    scope: $scope,
                    preserveScope: true
                });
            });
        }

        $scope.isErrorDetails = function(name) {
            return name.indexOf('_details') !== -1;
        };

        //close dialog
        $scope.cancel = function($event) {
            CommonService.closeModal($event);
        };

        if (angular.isDefined($stateParams.id)) {
            $scope.openViewDialog($stateParams.id);
        }

    }
})();