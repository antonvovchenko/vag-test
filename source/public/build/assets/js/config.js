(function() {
    'use strict';

    angular
        .module('app')
        .config(authConfig);

    function authConfig($httpProvider, API_CONFIG, $authProvider) {

        $authProvider.loginUrl = API_CONFIG.url + '/authenticate';
        $authProvider.tokenPrefix = 'pharmacy';
        $authProvider.authHeader = 'pharmacyauth';
        $authProvider.authToken = 'Bearer';

        $httpProvider.interceptors.push(function($q, $injector, DEFAULT_STATES) {
            return {
                responseError: function (rejection) {
                    var $state = $injector.get('$state');
                    var $auth = $injector.get('$auth');
                    var rejectionReasons = ['token_not_provided', 'token_expired', 'token_absent', 'token_invalid'];

                    angular.forEach(rejectionReasons, function (value, key) {
                        if (rejection.data.error === value) {
                            $auth.logout();
                            $state.go(DEFAULT_STATES.NOT_LOGGED_IN);
                        }
                    });

                    return $q.reject(rejection);
                }
            };
        });
    }
})();
(function() {
    'use strict';

    angular
        .module('app')
        .config(config);

    /* @ngInject */
    function config(ChartJsProvider) {
        // Configure all charts to use material design colors
        ChartJsProvider.setOptions({
            colours: [
                '#4285F4',    // blue
                '#DB4437',    // red
                '#F4B400',    // yellow
                '#0F9D58',    // green
                '#AB47BC',    // purple
                '#00ACC1',    // light blue
                '#FF7043',    // orange
                '#9E9D24',    // browny yellow
                '#5C6BC0'     // dark blue
            ],
            responsive: true
        });
    }
})();
(function() {
    'use strict';

    angular
        .module('app')
        .config(routeConfig);

    /* @ngInject */
    function routeConfig($stateProvider, $urlRouterProvider) {

        function prepareTemplateSrc(src) {
            return src+'?versionBLD='+versionBLD;
        }

        // Auth
        $stateProvider
            .state('authentication', {
                abstract: true,
                templateUrl: prepareTemplateSrc('app/pharmacy/authentication/layouts/authentication.tmpl.html')
            })
            .state('authentication.login', {
                url: '/login',
                templateUrl: prepareTemplateSrc('app/pharmacy/authentication/login/login.tmpl.html'),
                controller: 'LoginController',
                controllerAs: 'vm'
            })
            .state('authentication.logout', {
                url: '/logout',
                controller: 'LogoutController',
                controllerAs: 'vm'
            })
            .state('authentication.signup', {
                url: '/signup',
                templateUrl: prepareTemplateSrc('app/pharmacy/authentication/signup/signup.tmpl.html'),
                controller: 'SignupController',
                controllerAs: 'vm'
            })
            .state('authentication.lock', {
                url: '/lock',
                templateUrl: prepareTemplateSrc('app/pharmacy/authentication/lock/lock.tmpl.html'),
                controller: 'LockController',
                controllerAs: 'vm'
            })
            .state('authentication.forgot', {
                url: '/forgot',
                templateUrl: prepareTemplateSrc('app/pharmacy/authentication/forgot/forgot.tmpl.html'),
                controller: 'ForgotController',
                controllerAs: 'vm'
            })
            .state('triangular.admin-default.profile', {
                url: '/profile',
                templateUrl: prepareTemplateSrc('app/pharmacy/authentication/profile/profile.tmpl.html'),
                controller: 'ProfileController',
                controllerAs: 'vm'
            });


        //Products
        $stateProvider
            .state('triangular.admin-default.products-management-page', {
                url: '/products',
                templateUrl: prepareTemplateSrc('app/pharmacy/products/templates/products-page.tmpl.html'),
                controller: 'ProductsPageController'
            })
            .state('triangular.admin-default.product-add-page', {
                url: '/products/add',
                templateUrl: prepareTemplateSrc('app/pharmacy/products/templates/product-add.tmpl.html'),
                controller: 'ProductsCrudPageController'
            })
            .state('triangular.admin-default.product-edit-page', {
                url: '/products/edit/:id',
                templateUrl: prepareTemplateSrc('app/pharmacy/products/templates/product-add.tmpl.html'),
                controller: 'ProductsCrudPageController'
            })
            .state('triangular.admin-default.markup-type-management-page', {
                url: '/products/markup-type',
                templateUrl: prepareTemplateSrc('app/pharmacy/products/templates/markup-type-page.tmpl.html'),
                controller: 'MarkupTypePageController'
            })
            .state('triangular.admin-default.markup-type-edit-page', {
                url: '/products/markup-type/edit/:id',
                templateUrl: prepareTemplateSrc('app/pharmacy/products/templates/markup-type-page.tmpl.html'),
                controller: 'MarkupTypePageController'
            });

        //Inventory
        $stateProvider
            .state('triangular.admin-default.inventory-main-page', {
                url: '/inventory',
                templateUrl: prepareTemplateSrc('app/pharmacy/inventory/templates/inventory-page.tmpl.html'),
                controller: 'InventoryPageController'
            })
            .state('triangular.admin-default.inventory-in-page', {
                url: '/inventory/in',
                templateUrl: prepareTemplateSrc('app/pharmacy/inventory/templates/inventory-in.tmpl.html'),
                controller: 'InventoryInPageController'
            })
            .state('triangular.admin-default.inventory-out-page', {
                url: '/inventory/out',
                templateUrl: prepareTemplateSrc('app/pharmacy/inventory/templates/inventory-out.tmpl.html'),
                controller: 'InventoryOutPageController'
            })
            .state('triangular.admin-default.inventory-edit-page', {
                url: '/inventory/edit/:id',
                templateUrl: prepareTemplateSrc('app/pharmacy/inventory/templates/inventory-edit.tmpl.html'),
                controller: 'InventoryEditPageController'
            })
            .state('triangular.admin-default.supplier-management-page', {
                url: '/inventory/supplier',
                templateUrl: prepareTemplateSrc('app/pharmacy/inventory/templates/supplier-page.tmpl.html'),
                controller: 'SupplierPageController'
            })
            .state('triangular.admin-default.supplier-edit-page', {
                url: '/inventory/supplier/edit/:id',
                templateUrl: prepareTemplateSrc('app/pharmacy/inventory/templates/supplier-page.tmpl.html'),
                controller: 'SupplierPageController'
            });

        //Administrator
        $stateProvider
            .state('triangular.admin-default.administrator-users-management-page', {
                url: '/administrator/users',
                templateUrl: prepareTemplateSrc('app/pharmacy/administrator/templates/users-page.tmpl.html'),
                controller: 'UsersPageController'
            })
            .state('triangular.admin-default.administrator-users-edit-page', {
                url: '/administrator/users/edit/:id',
                templateUrl: prepareTemplateSrc('app/pharmacy/administrator/templates/users-page.tmpl.html'),
                controller: 'UsersPageController'
            })
            .state('triangular.admin-default.administrator-taxes-management-page', {
                url: '/administrator/taxes',
                templateUrl: prepareTemplateSrc('app/pharmacy/administrator/templates/taxes-page.tmpl.html'),
                controller: 'TaxPageController'
            })
            .state('triangular.admin-default.administrator-taxes-edit-page', {
                url: '/administrator/taxes/edit/:id',
                templateUrl: prepareTemplateSrc('app/pharmacy/administrator/templates/taxes-page.tmpl.html'),
                controller: 'TaxPageController'
            })
            .state('triangular.admin-default.administrator-logs-management-page', {
                url: '/administrator/logs',
                templateUrl: prepareTemplateSrc('app/pharmacy/administrator/templates/logs-page.tmpl.html'),
                controller: 'LogPageController'
            })
            .state('triangular.admin-default.administrator-transaction-page', {
                url: '/administrator/transaction',
                templateUrl: prepareTemplateSrc('app/pharmacy/administrator/templates/transaction-page.tmpl.html'),
                controller: 'TransactionPageController'
            })
            .state('triangular.admin-default.administrator-transaction-view-page', {
                url: '/administrator/transaction/view/:id',
                templateUrl: prepareTemplateSrc('app/pharmacy/administrator/templates/transaction-page.tmpl.html'),
                controller: 'TransactionPageController'
            })
            .state('triangular.admin-default.administrator-import-data-page', {
                url: '/administrator/import-data',
                templateUrl: prepareTemplateSrc('app/pharmacy/administrator/templates/import-data/import-data-page.tmpl.html'),
                controller: 'ImportDataController'
            })
            .state('triangular.admin-default.administrator-settings-page', {
                url: '/administrator/settings',
                templateUrl: prepareTemplateSrc('app/pharmacy/administrator/templates/settings/settings-page.tmpl.html'),
                controller: 'SettingsPageController'
            });

        //Prescription
        $stateProvider
            .state('triangular.admin-default.prescription-management-page', {
                url: '/prescription',
                templateUrl: prepareTemplateSrc('app/pharmacy/prescription/templates/prescription-page.tmpl.html'),
                controller: 'PrescriptionPageController'
            })
            .state('triangular.admin-default.prescription-add-page', {
                url: '/prescription/add',
                templateUrl: prepareTemplateSrc('app/pharmacy/prescription/templates/prescription-form/prescription-add.tmpl.html'),
                controller: 'PrescriptionCrudPageController'
            })
            .state('triangular.admin-default.prescription-edit-page', {
                url: '/prescription/edit/:id',
                templateUrl: prepareTemplateSrc('app/pharmacy/prescription/templates/prescription-form/prescription-add.tmpl.html'),
                controller: 'PrescriptionCrudPageController'
            })
            .state('triangular.admin-default.prescription-refill-page', {
                url: '/prescription/refill/:refill_id',
                templateUrl: prepareTemplateSrc('app/pharmacy/prescription/templates/prescription-form/prescription-add.tmpl.html'),
                controller: 'PrescriptionCrudPageController'
            })
            //patient
            .state('triangular.admin-default.patient-management-page', {
                url: '/patient',
                templateUrl: prepareTemplateSrc('app/pharmacy/prescription/templates/patient-page.tmpl.html'),
                controller: 'PatientPageController'
            })
            .state('triangular.admin-default.patient-edit-page', {
                url: '/patient/edit/:id',
                templateUrl: prepareTemplateSrc('app/pharmacy/prescription/templates/patient-page.tmpl.html'),
                controller: 'PatientPageController'
            })
            //prescriber
            .state('triangular.admin-default.prescriber-management-page', {
                url: '/prescriber',
                templateUrl: prepareTemplateSrc('app/pharmacy/prescription/templates/prescriber-page.tmpl.html'),
                controller: 'PrescriberPageController'
            })
            .state('triangular.admin-default.prescriber-add-page', {
                url: '/prescriber/add',
                templateUrl: prepareTemplateSrc('app/pharmacy/prescription/templates/prescriber-add-page.tmpl.html'),
                controller: 'PrescriberCrudPageController'
            })
            .state('triangular.admin-default.prescriber-edit-page', {
                url: '/prescriber/edit/:id',
                templateUrl: prepareTemplateSrc('app/pharmacy/prescription/templates/prescriber-add-page.tmpl.html'),
                controller: 'PrescriberCrudPageController'
            })
            //label code
            .state('triangular.admin-default.label-code-management-page', {
                url: '/label-code',
                templateUrl: prepareTemplateSrc('app/pharmacy/prescription/templates/label-code-page.tmpl.html'),
                controller: 'LabelCodePageController'
            })
            .state('triangular.admin-default.label-code-edit-page', {
                url: '/label-code/edit/:id',
                templateUrl: prepareTemplateSrc('app/pharmacy/prescription/templates/label-code-page.tmpl.html'),
                controller: 'LabelCodePageController'
            })
            //fee
            .state('triangular.admin-default.fee-management-page', {
                url: '/fee',
                templateUrl: prepareTemplateSrc('app/pharmacy/prescription/templates/fee-page.tmpl.html'),
                controller: 'FeePageController'
            })
            .state('triangular.admin-default.fee-edit-page', {
                url: '/fee/edit/:id',
                templateUrl: prepareTemplateSrc('app/pharmacy/prescription/templates/fee-page.tmpl.html'),
                controller: 'FeePageController'
            });

        //Order
        $stateProvider
            .state('triangular.admin-default.order-management-page', {
                url: '/order',
                templateUrl: prepareTemplateSrc('app/pharmacy/order/templates/order-page.tmpl.html'),
                controller: 'OrderPageController'
            })
            .state('triangular.admin-default.order-add-page', {
                url: '/order/add',
                templateUrl: prepareTemplateSrc('app/pharmacy/order/templates/order-add.tmpl.html'),
                controller: 'OrderCrudPageController'
            })
            .state('triangular.admin-default.order-edit-page', {
                url: '/order/edit/:id',
                templateUrl: prepareTemplateSrc('app/pharmacy/order/templates/order-add.tmpl.html'),
                controller: 'OrderCrudPageController'
            })
            .state('triangular.admin-default.customer-management-page', {
                url: '/customer',
                templateUrl: prepareTemplateSrc('app/pharmacy/order/templates/customer-page.tmpl.html'),
                controller: 'CustomerPageController'
            })
            .state('triangular.admin-default.order-refund-page', {
                url: '/order/refund/:id',
                templateUrl: prepareTemplateSrc('app/pharmacy/order/templates/order-refund.tmpl.html'),
                controller: 'OrderRefundPageController'
            });

        //Insurance
        $stateProvider
            .state('triangular.admin-default.insurance-reconciliation-page', {
                url: '/insurance/reconciliation',
                templateUrl: prepareTemplateSrc('app/pharmacy/insurance/templates/reconciliation-page.tmpl.html'),
                controller: 'ReconciliationPageController'
            });

        //Reports
        $stateProvider
            .state('triangular.admin-default.cashier-report-page', {
                url: '/reports/cashier',
                templateUrl: prepareTemplateSrc('app/pharmacy/reports/templates/cashier-report-page.tmpl.html'),
                controller: 'CashierReportPageController'
            })
            .state('triangular.admin-default.gct-report-page', {
                url: '/reports/gct',
                templateUrl: prepareTemplateSrc('app/pharmacy/reports/templates/gct-report-page.tmpl.html'),
                controller: 'GctReportPageController'
            })
            .state('triangular.admin-default.purchase-report-page', {
                url: '/reports/purchase',
                templateUrl: prepareTemplateSrc('app/pharmacy/reports/templates/purchase-report-page.tmpl.html'),
                controller: 'PurchaseReportPageController'
            });

        // 404 & 500 pages
        $stateProvider
        .state('404', {
            url: '/404',
            templateUrl: prepareTemplateSrc('404.tmpl.html'),
            controller: function($state) {
                var vm = this;
                vm.goHome = function() {
                    $state.go('triangular.admin-default.dashboard-analytics');
                };
            }
        })

        .state('500', {
            url: '/500',
            templateUrl: prepareTemplateSrc('500.tmpl.html'),
            controller: function($state) {
                var vm = this;
                vm.goHome = function() {
                    $state.go('triangular.admin-default.dashboard-analytics');
                };
            }
        });

        $urlRouterProvider.otherwise('/login');

        // always goto 404 if route not found
        //$urlRouterProvider.otherwise('/404');
    }
})();
(function() {
    'use strict';

    angular
        .module('app')
        .config(translateConfig);

    /* @ngInject */
    function translateConfig($translateProvider, $translatePartialLoaderProvider, APP_LANGUAGES) {
        /**
         *  each module loads its own translation file - making it easier to create translations
         *  also translations are not loaded when they aren't needed
         *  each module will have a il8n folder that will contain its translations
         */
        $translateProvider.useLoader('$translatePartialLoader', {
            urlTemplate: '{part}/il8n/{lang}.json'
        });

        // $translatePartialLoaderProvider.addPart('app');

        // make sure all values used in translate are sanitized for security
        $translateProvider.useSanitizeValueStrategy('sanitize');

        // cache translation files to save load on server
        //$translateProvider.useLoaderCache(true);

        // setup available languages in translate
        var languageKeys = [];
        for (var lang = APP_LANGUAGES.length - 1; lang >= 0; lang--) {
            languageKeys.push(APP_LANGUAGES[lang].key);
        }

        /**
         *  try to detect the users language by checking the following
         *      navigator.language
         *      navigator.browserLanguage
         *      navigator.systemLanguage
         *      navigator.userLanguage
         */
        $translateProvider
        .registerAvailableLanguageKeys(languageKeys, {
            'en_US': 'en',
            'en_UK': 'en'
        })
        .use('en');

        // store the users language preference in a cookie
        //$translateProvider.useLocalStorage();
    }
})();
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