(function() {
    'use strict';

    angular
        .module('app')
        .config(translateConfig);

    /* @ngInject */
    function translateConfig(triSettingsProvider, APP_LANGUAGES) {
        // set app name & logo (used in loader, sidemenu, login pages, etc)
        triSettingsProvider.setName('');
        triSettingsProvider.setLogo('assets/images/logo.png');
        triSettingsProvider.setLogo2('assets/images/small-logo.png');

        // set current version of app (shown in footer)
        triSettingsProvider.setVersion('0.0.1');

        // setup available languages in triangular
        for (var lang = APP_LANGUAGES.length - 1; lang >= 0; lang--) {
            triSettingsProvider.addLanguage({
                name: APP_LANGUAGES[lang].name,
                key: APP_LANGUAGES[lang].key
            });
        }
    }
})();