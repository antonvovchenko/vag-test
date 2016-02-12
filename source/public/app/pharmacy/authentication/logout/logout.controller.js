(function() {
    'use strict';

    angular
        .module('app.pharmacy.authentication')
        .controller('LogoutController', LogoutController);

    /* @ngInject */
    function LogoutController($state, $auth, DEFAULT_STATES) {
        $auth.logout();
        $state.go(DEFAULT_STATES.NOT_LOGGED_IN);
    }
})();