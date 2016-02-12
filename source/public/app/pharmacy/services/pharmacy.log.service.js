(function() {
    'use strict';

    angular
        .module('app.pharmacy')

        .factory('LogResource', [
            '$resource', 'API_CONFIG',
            function($resource, API_CONFIG) {
                return $resource(
                    API_CONFIG.url + '/log/:logId',
                    {
                        logId: '@id',
                        query: '@query'
                    },
                    {
                        'update': { method:'PUT' }
                    }
                );
            }
        ])

        .factory('LogService', [
            '$q', 'LogResource',
            function($q, LogResource) {
                return {
                    //
                };
            }
        ]);

})();