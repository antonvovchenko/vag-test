(function() {
    'use strict';

    angular
        .module('app')
        .config(themesConfig);

    /* @ngInject */
    function themesConfig ($mdThemingProvider, triThemingProvider, triSkinsProvider) {
        /**
         *  PALETTES
         */
        $mdThemingProvider.definePalette('white', {
            '50': 'ffffff',
            '100': 'ffffff',
            '200': 'ffffff',
            '300': 'ffffff',
            '400': 'ffffff',
            '500': 'ffffff',
            '600': 'ffffff',
            '700': 'ffffff',
            '800': 'ffffff',
            '900': 'ffffff',
            'A100': 'ffffff',
            'A200': 'ffffff',
            'A400': 'ffffff',
            'A700': 'ffffff',
            'contrastDefaultColor': 'dark'
        });

        $mdThemingProvider.definePalette('black', {
            '50': 'e1e1e1',
            '100': 'b6b6b6',
            '200': '8c8c8c',
            '300': '646464',
            '400': '3a3a3a',
            '500': 'e1e1e1',
            '600': 'e1e1e1',
            '700': '232323',
            '800': '1a1a1a',
            '900': '121212',
            'A100': '3a3a3a',
            'A200': 'ffffff',
            'A400': 'ffffff',
            'A700': 'ffffff',
            'contrastDefaultColor': 'light'
        });

        var triCyanMap = $mdThemingProvider.extendPalette('cyan', {
            'contrastDefaultColor': 'light',
            'contrastLightColors': '500 700 800 900',
            'contrastStrongLightColors': '500 700 800 900'
        });

        // Register the new color palette map with the name triCyan
        $mdThemingProvider.definePalette('triCyan', triCyanMap);

        /**
         *  SKINS
         */

        // PLUMB PURPLE SKIN
        triThemingProvider.theme('white')
        .primaryPalette('white')
        .accentPalette('white')
        .warnPalette('white');

        triThemingProvider.theme('deep-purple')
            .primaryPalette('deep-purple', {
                'default': '400'
            })
            .accentPalette('deep-orange')
            .warnPalette('red');

        triThemingProvider.theme('white-purple')
        .primaryPalette('white')
        .accentPalette('deep-purple', {
            'default': '400'
        })
        .warnPalette('red');

        triSkinsProvider.skin('plumb-purple', 'Plumb Purple')
        .sidebarTheme('deep-purple')
        .toolbarTheme('white-purple')
        .logoTheme('white')
        .contentTheme('deep-purple');



        /**
         *  FOR DEMO PURPOSES ALLOW SKIN TO BE SAVED IN A COOKIE
         *  This overrides any skin set in a call to triSkinsProvider.setSkin if there is a cookie
         *  REMOVE LINE BELOW FOR PRODUCTION SITE
         */
        triSkinsProvider.useSkinCookie(true);

        /**
         *  SET DEFAULT SKIN
         */
        triSkinsProvider.setSkin('plumb-purple');
    }
})();