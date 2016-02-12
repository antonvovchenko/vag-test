(function() {
    'use strict';

    angular
        .module('app.pharmacy')
        .factory('SupplierResource', ['$resource', 'API_CONFIG', function($resource, API_CONFIG) {
            return $resource(
                API_CONFIG.url + '/supplier/:supplierId',
                {
                    supplierId:'@id',
                    query: '@query'
                },
                {
                    'update': { method:'PUT' }
                }
            );
        }])

        .factory('SupplierService', [
            '$q', '$http', 'SupplierResource', 'API_CONFIG',
            function($q, $http, SupplierResource, API_CONFIG) {
                return {
                    getFormData: function (itemId) {
                        if (angular.isDefined(itemId)) {
                            return $q.all(
                                [
                                    SupplierResource.get({supplierId: itemId}).$promise
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
                            url: API_CONFIG.url + '/supplier-helper/next-id'
                        }).then(function(response) {
                            return response.data;
                        });
                    }
                };
            }
        ]);

})();