(function() {
    'use strict';

    angular
        .module('app.pharmacy')

        .factory('FeeResource', [
            '$resource', 'API_CONFIG',
            function($resource, API_CONFIG) {
                return $resource(
                    API_CONFIG.url + '/fee/:feeId',
                    {
                        feeId: '@id',
                        query: '@query'
                    },
                    {
                        'update': { method:'PUT' }
                    }
                );
            }
        ])

        .factory('FeeService', [
            '$q', 'FeeResource',
            function($q, FeeResource) {
                return {
                    getFormData: function (itemId) {
                        return $q.all(
                            [
                                FeeResource.get({feeId: itemId}).$promise
                            ]
                        ).then(function (results) {
                                return {
                                    item: results[0].data
                                };
                            });
                    },
                };
            }
        ]);

})();