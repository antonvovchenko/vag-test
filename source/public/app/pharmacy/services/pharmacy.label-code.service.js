(function() {
    'use strict';

    angular
        .module('app.pharmacy')

        .factory('LabelCodeResource', [
            '$resource', 'API_CONFIG',
            function($resource, API_CONFIG) {
                return $resource(
                    API_CONFIG.url + '/label-code/:labelCodeId',
                    {
                        labelCodeId: '@id',
                        query: '@query'
                    },
                    {
                        'update': { method:'PUT' }
                    }
                );
            }
        ])

        .factory('LabelCodeService', [
            '$q', '$http', 'LabelCodeResource', 'API_CONFIG',
            function($q, $http, LabelCodeResource, API_CONFIG) {
                return {
                    getFormData: function (itemId) {
                        if (angular.isDefined(itemId)) {
                            return $q.all(
                                [
                                    LabelCodeResource.get({labelCodeId: itemId}).$promise
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
                            url: API_CONFIG.url + '/label-code-helper/next-id'
                        }).then(function(response) {
                            return response.data;
                        });
                    }
                };
            }
        ]);

})();