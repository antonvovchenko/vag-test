(function() {
    'use strict';

    angular
        .module('app.pharmacy.authentication')
        .controller('LoginController', LoginController);

    /* @ngInject */
    function LoginController($window, $state, triSettings, $auth, DEFAULT_STATES) {

        if ($auth.isAuthenticated()) {
            $state.go(DEFAULT_STATES.LOGGED_IN);
        }

        var vm = this;
        vm.loginClick = loginClick;
        vm.socialLogins = [{
            icon: 'fa fa-twitter',
            color: '#5bc0de',
            url: '#'
        },{
            icon: 'fa fa-facebook',
            color: '#337ab7',
            url: '#'
        },{
            icon: 'fa fa-google-plus',
            color: '#e05d6f',
            url: '#'
        },{
            icon: 'fa fa-linkedin',
            color: '#337ab7',
            url: '#'
        }];
        vm.triSettings = triSettings;
        // create blank user variable for login form
        vm.user = {
            email: '',
            password: ''
        };

        ////////////////

        function loginClick() {

            vm.loginError = false;

            var credentials = {
                email: vm.user.email,
                password: vm.user.password
            }

            $auth.login(credentials).then(function(data) {
                if (data.status == 200) {
                    $window.userFullName = data.data.full_name;
                    $state.go(DEFAULT_STATES.LOGGED_IN);
                } else {
                    vm.loginError = true;
                    vm.loginErrorText = error.data.error;
                }
            }, function(error) {
                vm.loginError = true;
                vm.loginErrorText = error.data.error;
            });
        }
    }
})();