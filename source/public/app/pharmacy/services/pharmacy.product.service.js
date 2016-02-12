(function() {
    'use strict';

    angular
        .module('app.pharmacy')

        .factory('ProductResource', ['$resource', 'API_CONFIG', function($resource, API_CONFIG) {
            return $resource(
                API_CONFIG.url + '/product/:productId',
                { productId:'@id' },
                {
                    'update': { method:'PUT' }
                }
            );
        }])

        .factory('ProductService', [
            '$q', 'ProductResource', 'ProductMarkupTypeResource', 'API_CONFIG', '$http', 'InventoryResource', 'TaxResource', 'CommonService',
            function($q, ProductResource, ProductMarkupTypeResource, API_CONFIG, $http, InventoryResource, TaxResource, CommonService) {
                return {
                    getFormData: function (itemId) {
                        if (angular.isDefined(itemId)) {
                            return $q.all(
                                [
                                    ProductMarkupTypeResource.get().$promise,
                                    ProductResource.get({productId: itemId}).$promise,
                                    InventoryResource.get({product_id: itemId, order: '-created_at'}).$promise,
                                    TaxResource.get().$promise
                                ]
                            ).then(function (results) {
                                    return {
                                        markup_types: results[0].data.data,
                                        item: results[1].data,
                                        inventory: results[2].data.data,
                                        taxes: results[3].data.data
                                    };
                                });
                        } else {
                            return $q.all(
                                [
                                    ProductMarkupTypeResource.get().$promise,
                                    this.getNextId(),
                                    TaxResource.get().$promise
                                ]
                            ).then(function (results) {
                                    return {
                                        markup_types: results[0].data.data,
                                        nextId: results[1].data.nextId,
                                        taxes: results[2].data.data
                                    };
                                });
                        }
                    },

                    getNextId: function(){
                        return $http({
                            method: 'GET',
                            url: API_CONFIG.url + '/product-helper/next-id'
                        }).then(function(response) {
                            return response.data;
                        });
                    },

                    searchNDC: function(text){
                        return $http({
                            method: 'GET',
                            url: API_CONFIG.url + '/product-helper/search-ndc?text='+text
                        }).then(function(response) {
                            return response.data;
                        });
                    },

                    searchNDCProducts: function(text){
                        return $http({
                            method: 'GET',
                            url: API_CONFIG.url + '/product-helper/search-ndc-products?text='+text
                        }).then(function(response) {
                            return response.data;
                        });
                    },

                    calculatePrice: function(item){
                        var singlePrice = CommonService.numbersFormat(item.price);
                        item.tax = 0.00;
                        if (!_.isEmpty(item.product) && !_.isEmpty(item.product.tax)) {
                            var singleTax = CommonService.priceFormat( CommonService.numbersFormat(item.price) * CommonService.numbersFormat(item.product.tax.value) / 100);
                            singlePrice = CommonService.numbersFormat(item.price) + CommonService.numbersFormat(singleTax);
                            item.tax = CommonService.priceFormat(CommonService.numbersFormat(singleTax) * CommonService.numbersFormat(item.qty) );
                        }
                        item.total = CommonService.priceFormat(
                            singlePrice
                                * CommonService.numbersFormat(item.qty)
                                    + ( angular.isDefined(item.fee_value) ? CommonService.numbersFormat(item.fee_value.replace('$', '')) : 0)
                        );
                    }
                };
            }
        ]);

})();