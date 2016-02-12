(function() {
    'use strict';

    angular
        .module('app.pharmacy')

        .factory('OrderResource', ['$resource', 'API_CONFIG', function($resource, API_CONFIG) {
            return $resource(
                API_CONFIG.url + '/order/:orderId',
                { orderId:'@id' },
                {
                    'update': { method:'PUT' }
                }
            );
        }])

        .factory('OrderService', [
            '$q', '$http', 'OrderResource', 'API_CONFIG',
            function($q, $http, OrderResource, API_CONFIG) {
                return {
                    getFormData: function (itemId) {
                        if (angular.isDefined(itemId)) {
                            return $q.all(
                                [
                                    OrderResource.get({orderId: itemId}).$promise,
                                    OrderResource.get({refunded_order_id: itemId}).$promise
                                ]
                            ).then(function (results) {
                                    return {
                                        item: results[0].data,
                                        refunded_orders: results[1].data.data
                                    };
                                });
                        } else {
                            return $q.all(
                                [
                                    this.getNextId()
                                ]
                            ).then(function (results) {
                                    return {
                                        nextId: results[0].data.nextId
                                    };
                                });
                        }
                    },

                    getNextId: function(){
                        return $http({
                            method: 'GET',
                            url: API_CONFIG.url + '/order-helper/next-id'
                        }).then(function(response) {
                            return response.data;
                        });
                    },

                    getTenderedFormData: function () {
                        return $q.all(
                            [
                                this.getPaidTypes()
                            ]
                        ).then(function (results) {
                                return {
                                    paidTypes: results[0].data
                                };
                            });
                    },

                    getPaidTypes: function(){
                        return $http({
                            method: 'GET',
                            url: API_CONFIG.url + '/order-helper/paid-types'
                        }).then(function(response) {
                            return response.data;
                        });
                    }
                };
            }
        ]);

})();