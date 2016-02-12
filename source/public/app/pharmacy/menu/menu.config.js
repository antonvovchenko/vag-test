(function() {
    'use strict';

    angular
        .module('app.pharmacy.menu')
        .config(moduleConfig);

    /* @ngInject */
    function moduleConfig($translatePartialLoaderProvider, $stateProvider, triMenuProvider) {
        $translatePartialLoaderProvider.addPart('app/pharmacy/menu');

        triMenuProvider.addMenu({
            name: 'Dashboard',
            state: 'triangular.admin-default.dashboard-main-page',
            type: 'link',
            icon: 'fa fa-dashboard',
            priority: 1.1
        });

        triMenuProvider.addMenu({
            name: 'Administrator',
            icon: 'fa fa-user',
            type: 'dropdown',
            priority: 2.1,
            children: [
                {
                    name: 'Accounts',
                    type: 'link',
                    state: 'triangular.admin-default.administrator-users-management-page'
                },
                {
                    name: 'Logs',
                    type: 'link',
                    state: 'triangular.admin-default.administrator-logs-management-page'
                },
                {
                    name: 'Taxes',
                    type: 'link',
                    state: 'triangular.admin-default.administrator-taxes-management-page'
                },
                {
                    name: 'Transactions',
                    type: 'link',
                    state: 'triangular.admin-default.administrator-transaction-page'
                },
                {
                    name: 'Import Data',
                    type: 'link',
                    state: 'triangular.admin-default.administrator-import-data-page'
                },
                {
                    name: 'Settings',
                    type: 'link',
                    state: 'triangular.admin-default.administrator-settings-page'
                }
            ]
        });

        triMenuProvider.addMenu({
            name: 'Products',
            icon: 'fa fa-flask',
            type: 'dropdown',
            priority: 3.1,
            children: [
                {
                    name: 'Products',
                    type: 'link',
                    state: 'triangular.admin-default.products-management-page'
                },
                {
                    name: 'Categories',
                    type: 'link',
                    state: 'triangular.admin-default.markup-type-management-page'
                }
            ]
        });

        triMenuProvider.addMenu({
            name: 'Inventory',
            icon: 'fa fa-book',
            type: 'dropdown',
            priority: 4.1,
            children: [
                {
                    name: 'Inventory',
                    type: 'link',
                    state: 'triangular.admin-default.inventory-main-page'
                },
                {
                    name: 'Suppliers',
                    type: 'link',
                    state: 'triangular.admin-default.supplier-management-page'
                }
            ]

        });

        triMenuProvider.addMenu({
            name: 'Prescription',
            icon: 'fa fa-file-o',
            type: 'dropdown',
            priority: 5.1,
            children: [
                {
                    name: 'Prescriptions',
                    type: 'link',
                    state: 'triangular.admin-default.prescription-management-page'
                },
                {
                    name: 'Patients',
                    type: 'link',
                    state: 'triangular.admin-default.patient-management-page'
                },
                {
                    name: 'Doctors',
                    type: 'link',
                    state: 'triangular.admin-default.prescriber-management-page'
                },
                {
                    name: 'Label Codes',
                    type: 'link',
                    state: 'triangular.admin-default.label-code-management-page'
                },
                {
                    name: 'Fee',
                    type: 'link',
                    state: 'triangular.admin-default.fee-management-page'
                }
            ]
        });

        triMenuProvider.addMenu({
            name: 'Order',
            icon: 'fa fa-download',
            type: 'dropdown',
            priority: 6.1,
            children: [
                {
                    name: 'Orders',
                    type: 'link',
                    state: 'triangular.admin-default.order-management-page'
                },
                {
                    name: 'Customers',
                    type: 'link',
                    state: 'triangular.admin-default.customer-management-page'
                }
            ]
        });

        triMenuProvider.addMenu({
            name: 'Insurance',
            icon: 'fa fa-tags',
            type: 'dropdown',
            priority: 6.1,
            children: [
                {
                    name: 'Reconciliation',
                    type: 'link',
                    state: 'triangular.admin-default.insurance-reconciliation-page'
                }
            ]
        });

        triMenuProvider.addMenu({
            name: 'Reports',
            icon: 'fa fa-calendar',
            type: 'dropdown',
            priority: 7.1,
            children: [
                {
                    name: 'Cashiers Report',
                    type: 'link',
                    state: 'triangular.admin-default.cashier-report-page'
                },
                {
                    name: 'GCT Report',
                    type: 'link',
                    state: 'triangular.admin-default.gct-report-page'
                },
                {
                    name: 'Purchase Report',
                    type: 'link',
                    state: 'triangular.admin-default.purchase-report-page'
                }
            ]
        });
    }
})();