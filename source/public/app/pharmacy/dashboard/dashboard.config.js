(function() {
    'use strict';

    angular
        .module('app.pharmacy.dashboard')
        .config(moduleConfig);

    /* @ngInject */
    function moduleConfig($translatePartialLoaderProvider, $stateProvider, triMenuProvider) {
        $translatePartialLoaderProvider.addPart('app/pharmacy/dashboard');

        $stateProvider
        .state('triangular.admin-default.dashboard-main-page', {
            url: '/dashboard',
            templateUrl: 'app/pharmacy/dashboard/dashboard-page.tmpl.html',
            // set the controller to load for this page
            controller: 'DashboardPageController',
            controllerAs: 'vm'
        });

    }
})();