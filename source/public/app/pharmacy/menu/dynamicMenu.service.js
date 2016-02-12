(function() {
    'use strict';

    angular
        .module('app.pharmacy.menu')
        .service('dynamicMenuService', dynamicMenuService);

    /* @ngInject */
    function dynamicMenuService() {
        this.dynamicMenu = {
            showDynamicMenu: false
        };
    }
})();