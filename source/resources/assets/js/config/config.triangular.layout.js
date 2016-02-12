(function() {
    'use strict';

    angular
        .module('app')
        .config(config);

    /* @ngInject */
    function config(triLayoutProvider) {
        triLayoutProvider.setDefaultOption('toolbarSize', 'default');

        triLayoutProvider.setDefaultOption('toolbarShrink', true);

        triLayoutProvider.setDefaultOption('toolbarClass', '');

        triLayoutProvider.setDefaultOption('contentClass', 'md-main-content-block');

        triLayoutProvider.setDefaultOption('sideMenuSize', 'full');

        triLayoutProvider.setDefaultOption('showToolbar', true);

        triLayoutProvider.setDefaultOption('footer', false);
    }
})();