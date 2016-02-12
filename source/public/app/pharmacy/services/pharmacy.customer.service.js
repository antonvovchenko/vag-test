(function() {
    'use strict';

    angular
        .module('app.pharmacy')

        .factory('CustomerResource', ['$resource', 'API_CONFIG', function($resource, API_CONFIG) {
            return $resource(
                API_CONFIG.url + '/customer/:customerId',
                { customerId:'@id' },
                {
                    'update': { method:'PUT' }
                }
            );
        }])

        .factory('CustomerService', [
            '$q', '$http', 'CustomerResource', 'API_CONFIG',
            function($q, $http, CustomerResource, API_CONFIG) {
                return {
                    getFormData: function (itemId) {
                        if (angular.isDefined(itemId)) {
                            return $q.all(
                                [
                                    CustomerResource.get({customerId: itemId}).$promise
                                ]
                            ).then(function (results) {
                                    return {
                                        item: results[0].data
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
                            url: API_CONFIG.url + '/customer-helper/next-id'
                        }).then(function(response) {
                            return response.data;
                        });
                    }
                };
            }
        ]);

})();