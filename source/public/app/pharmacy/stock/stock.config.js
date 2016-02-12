(function() {
    'use strict';

    angular
        .module('app.pharmacy.stock')
        .config(moduleConfig);

    /* @ngInject */
    function moduleConfig($translatePartialLoaderProvider, $stateProvider, triMenuProvider) {
        $translatePartialLoaderProvider.addPart('app/pharmacy/stock');

        $stateProvider
        .state('triangular.admin-default.stock-main-page', {
            url: '/stock',
            templateUrl: 'app/pharmacy/stock/stock-page.tmpl.html',
            // set the controller to load for this page
            controller: 'StockPageController',
            controllerAs: 'vm'
        });

    }
})();