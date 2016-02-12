(function() {
    'use strict';

    angular
        .module('app.pharmacy')

        .factory('PrescriberResource', [
            '$resource', 'API_CONFIG',
            function($resource, API_CONFIG) {
                return $resource(
                    API_CONFIG.url + '/prescriber/:prescriberId',
                    {
                        prescriberId: '@id',
                        query: '@query'
                    },
                    {
                        'update': { method:'PUT' }
                    }
                );
            }
        ])

        .factory('PrescriberService', [
            '$q', '$http', 'PrescriberResource', 'API_CONFIG',
            function($q, $http, PrescriberResource, API_CONFIG) {
                return {
                    getFormData: function (itemId) {
                        if (angular.isDefined(itemId)) {
                            return $q.all(
                                [
                                    PrescriberResource.get({prescriberId: itemId}).$promise
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
                            url: API_CONFIG.url + '/prescriber-helper/next-id'
                        }).then(function(response) {
                            return response.data;
                        });
                    }
                };
            }
        ]);

})();