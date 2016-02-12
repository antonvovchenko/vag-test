(function() {
    'use strict';

    angular
        .module('app.pharmacy.products')
        .controller('ProductsPageController', ProductsPageController);

    /* @ngInject */
    function ProductsPageController($mdDialog, ProductResource, $scope, ProductMarkupTypeResource, moment, PHARMACY_SETTINGS) {

        $scope.query = {
            filter: '',
            limit: PHARMACY_SETTINGS.pagination.per_page,
            order: 'title',
            page: 1
        };

        $scope.isShow = false;

        ProductMarkupTypeResource.get()
            .$promise.then(function(data) {
                $scope.markupTypes = data.data.data;
                $scope.isShow = true;
            });

        function success(products) {
            $scope.products = products.data;
        }

        $scope.onChange = function () {
            return ProductResource.get($scope.query, success).$promise;
        };

        function getProducts() {
            $scope.onChange();
        }

        $scope.removeProductConfirm = function($event, id) {
            var confirm = $mdDialog.confirm()
                .title('Are you sure you want to delete the record?')
                .content('')
                .ariaLabel('')
                .ok('Remove')
                .cancel('Cancel')
                .targetEvent($event);
            $mdDialog.show(confirm).then(function() {
                ProductResource.delete({productId: id})
                    .$promise.then(function() {
                        getProducts();
                    });
            });
        }
        $scope.getMarkupTypeLabel = function(typeId) {
            var typeLabel = '';
            angular.forEach($scope.markupTypes, function (item, key){
                 if (item.id == typeId) {
                     typeLabel = item.title;
                 }
            });
            return typeLabel;
        };

        $scope.validDate = function (date) {
            if (!moment(date).isValid()) {
                return '';
            }
            return date;
        }
    }
})();