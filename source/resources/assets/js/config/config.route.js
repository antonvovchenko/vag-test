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