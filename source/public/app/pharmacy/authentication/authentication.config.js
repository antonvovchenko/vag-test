(function() {
    'use strict';

    angular
        .module('app.pharmacy.authentication')
        .config(moduleConfig);

    /* @ngInject */
    function moduleConfig($translatePartialLoaderProvider) {
        $translatePartialLoaderProvider.addPart('app/pharmacy/authentication');
    }
})();