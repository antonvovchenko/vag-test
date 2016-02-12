(function() {
    'use strict';

    angular
        .module('app.pharmacy', [

            //modules
            'app.pharmacy.authentication',
            'app.pharmacy.menu',
            'app.pharmacy.dashboard',
            'app.pharmacy.administrator',
            'app.pharmacy.products',
            'app.pharmacy.inventory',
            'app.pharmacy.prescription',
            'app.pharmacy.reports'
        ])
        .config(moduleConfig);

    function moduleConfig($translatePartialLoaderProvider) {
        $translatePartialLoaderProvider.addPart('app/pharmacy');
    }
})();