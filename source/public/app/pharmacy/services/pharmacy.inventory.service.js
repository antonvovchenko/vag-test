(function() {
    'use strict';

    angular
        .module('app.pharmacy')

        .factory('InventoryResource', [
            '$resource', 'API_CONFIG',
            function($resource, API_CONFIG) {
                return $resource(
                    API_CONFIG.url + '/inventory/:inventoryId',
                    {
                        inventoryId: '@id',
                        query: '@query'
                    },
                    {
                        'update': { method:'PUT' }
                    }
                );
            }
        ])

        .factory('InventoryService', [
            '$q', 'SupplierResource', 'InventoryResource',
            function($q, SupplierResource, InventoryResource) {
                return {
                    getFormData: function (itemId) {
                        if (angular.isDefined(itemId)) {
                            return $q.all(
                                [
                                    SupplierResource.get().$promise,
                                    InventoryResource.get({inventoryId: itemId}).$promise
                                ]
                            ).then(function (results) {
                                    return {
                                        suppliers: results[0].data.data,
                                        item: results[1].data
                                    };
                                });
                        } else {
                            return $q.all(
                                [
                                    SupplierResource.get().$promise
                                ]
                            ).then(function (results) {
                                    return {
                                        suppliers: results[0].data.data
                                    };
                                });
                        }
                    }
                };
            }
        ]);

})();