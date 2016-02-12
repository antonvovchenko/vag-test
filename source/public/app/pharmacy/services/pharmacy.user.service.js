(function() {
    'use strict';

    angular
        .module('app.pharmacy')
        .factory('UserResource', ['$resource', 'API_CONFIG', function($resource, API_CONFIG) {
            return $resource(
                API_CONFIG.url + '/user/:userId',
                { userId:'@id' },
                {
                    'update': { method:'PUT' }
                }
            );
        }]);

})();