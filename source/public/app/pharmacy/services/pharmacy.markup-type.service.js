(function() {
    'use strict';

    angular
        .module('app.pharmacy')
        .factory('ProductMarkupTypeResource', ['$resource', 'API_CONFIG', function($resource, API_CONFIG) {
            return $resource(
                API_CONFIG.url + '/product-markup-type/:markupTypeId',
                {
                    markupTypeId:'@id',
                    query: '@query'
                },
                {
                    'update': { method:'PUT' }
                }
            );
        }]).factory('ProductMarkupTypeService', [
            '$q', '$http', 'ProductMarkupTypeResource', 'API_CONFIG',
            function($q, $http, ProductMarkupTypeResource, API_CONFIG) {
                return {
                    getFormData: function (itemId) {
                        if (angular.isDefined(itemId)) {
                            return $q.all(
                                [
                                    ProductMarkupTypeResource.get({markupTypeId: itemId}).$promise
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
                            url: API_CONFIG.url + '/product-markup-type-helper/next-id'
                        }).then(function(response) {
                            return response.data;
                        });
                    }
                };
            }
        ]);

})();