(function() {
    'use strict';

    angular
        .module('app')
        .config(authConfig);

    function authConfig($httpProvider, API_CONFIG, $authProvider) {

        $authProvider.loginUrl = API_CONFIG.url + '/authenticate';
        $authProvider.tokenPrefix = 'pharmacy';
        $authProvider.authHeader = 'pharmacyauth';
        $authProvider.authToken = 'Bearer';

        $httpProvider.interceptors.push(function($q, $injector, DEFAULT_STATES) {
            return {
                responseError: function (rejection) {
                    var $state = $injector.get('$state');
                    var $auth = $injector.get('$auth');
                    var rejectionReasons = ['token_not_provided', 'token_expired', 'token_absent', 'token_invalid'];

                    angular.forEach(rejectionReasons, function (value, key) {
                        if (rejection.data.error === value) {
                            $auth.logout();
                            $state.go(DEFAULT_STATES.NOT_LOGGED_IN);
                        }
                    });

                    return $q.reject(rejection);
                }
            };
        });
    }
})();