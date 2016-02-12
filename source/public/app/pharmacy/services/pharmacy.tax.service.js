(function() {
    'use strict';

    angular
        .module('app.pharmacy')

        .factory('TaxResource', [
            '$resource', 'API_CONFIG',
            function($resource, API_CONFIG) {
                return $resource(
                    API_CONFIG.url + '/tax/:taxId',
                    {
                        taxId: '@id',
                        query: '@query'
                    },
                    {
                        'update': { method:'PUT' }
                    }
                );
            }
        ])

        .factory('TaxService', [
            '$q', 'TaxResource',
            function($q, TaxResource) {
                return {
                    getFormData: function (itemId) {
                        return $q.all(
                            [
                                TaxResource.get({taxId: itemId}).$promise
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