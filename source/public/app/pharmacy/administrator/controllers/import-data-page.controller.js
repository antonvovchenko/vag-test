(function() {
    'use strict';

    angular
        .module('app.pharmacy.administrator')
        .controller('ImportDataController', ImportDataController);

    function ImportDataController($scope, Upload, $timeout, API_CONFIG, CommonService, ImportService) {

        $scope.CommonService = CommonService;

        $scope.matchProductForm = {
            title: 'Title',
            description: 'Description',
            ndc: 'NDC',
            pkg_size: 'Package size',
            pkg_cost: 'Package cost',
            tax: 'Tax',
            markup: 'Markup',
            barcode: 'Barcode',
            location: 'Location',
            markup_type: 'Category',
            lead_time: 'Lead time',
            min_qty: 'Min quantity',
            max_qty: 'Max quantity',
            is_active: 'Is active',
            is_prescription: 'Is RX',
            label_codes: 'Label codes'
        };
        $scope.productColumns = angular.copy($scope.matchProductForm);
        $scope.productsQuery = {
            limit: 10,
            page: 1
        }

        $scope.matchInventoryForm = {
            product_title: 'Product Title',
            quantity: 'Quantity',
            supplier: 'Supplier',
            expiry_date: 'Expiry date',
            reason: 'Reason'
        };
        $scope.inventoryColumns = angular.copy($scope.matchInventoryForm);
        $scope.inventoryQuery = {
            limit: 10,
            page: 1
        }

        $scope.matchPatientsForm = {
            name: 'Name',
            email: 'Email',
            address: 'Address',
            city: 'City',
            phone: 'Phone'
        };
        $scope.patientsColumns = angular.copy($scope.matchPatientsForm);
        $scope.patientsQuery = {
            limit: 10,
            page: 1
        }

        $scope.matchDoctorsForm = {
            first_name: 'First Name',
            last_name: 'Last Name',
            registration_number: 'Registration Number',
            notes: 'Notes'
        };
        $scope.doctorsColumns = angular.copy($scope.matchDoctorsForm);
        $scope.doctorsQuery = {
            limit: 10,
            page: 1
        }

        $scope.matchCategoriesForm = {
            title: 'Title',
            description: 'Description',
            default_markup: 'Default Markup'
        };
        $scope.categoriesColumns = angular.copy($scope.matchCategoriesForm);
        $scope.categoriesQuery = {
            limit: 10,
            page: 1
        }

        $scope.matchLabelCodesForm = {
            title: 'Title',
            description: 'Description'
        };
        $scope.labelCodesColumns = angular.copy($scope.matchLabelCodesForm);
        $scope.labelCodesQuery = {
            limit: 10,
            page: 1
        }

        $scope.matchFeesForm = {
            card_type: 'Card Type',
            description: 'Description',
            fees: 'Fee',
            sort: 'Sort'
        };
        $scope.feesColumns = angular.copy($scope.matchFeesForm);
        $scope.feesQuery = {
            limit: 10,
            page: 1
        }

        $scope.matchSuppliersForm = {
            name: 'Name',
            address: 'Address',
            city: 'City',
            phone: 'Phone',
            fax: 'Fax',
            contact: 'Contact'
        };
        $scope.suppliersColumns = angular.copy($scope.matchSuppliersForm);
        $scope.suppliersQuery = {
            limit: 10,
            page: 1
        }

        $scope.getItems = function(type) {
            if (type == 'products') {
                $scope.uploaded_products_file.items_paginated = [];
                angular.forEach($scope.uploaded_products_file.items, function(v, k) {
                    if ($scope.isOnPage(k, $scope.productsQuery)) {
                        $scope.uploaded_products_file.items_paginated.push(v);
                    }
                });
            } else if (type == 'inventory') {
                $scope.uploaded_inventory_file.items_paginated = [];
                angular.forEach($scope.uploaded_inventory_file.items, function(v, k) {
                    if ($scope.isOnPage(k, $scope.inventoryQuery)) {
                        $scope.uploaded_inventory_file.items_paginated.push(v);
                    }
                });
            } else if (type == 'patients') {
                $scope.uploaded_patients_file.items_paginated = [];
                angular.forEach($scope.uploaded_patients_file.items, function(v, k) {
                    if ($scope.isOnPage(k, $scope.patientsQuery)) {
                        $scope.uploaded_patients_file.items_paginated.push(v);
                    }
                });
            } else if (type == 'doctors') {
                $scope.uploaded_doctors_file.items_paginated = [];
                angular.forEach($scope.uploaded_doctors_file.items, function(v, k) {
                    if ($scope.isOnPage(k, $scope.doctorsQuery)) {
                        $scope.uploaded_doctors_file.items_paginated.push(v);
                    }
                });
            } else if (type == 'categories') {
                $scope.uploaded_categories_file.items_paginated = [];
                angular.forEach($scope.uploaded_categories_file.items, function(v, k) {
                    if ($scope.isOnPage(k, $scope.categoriesQuery)) {
                        $scope.uploaded_categories_file.items_paginated.push(v);
                    }
                });
            } else if (type == 'label_codes') {
                $scope.uploaded_label_codes_file.items_paginated = [];
                angular.forEach($scope.uploaded_label_codes_file.items, function(v, k) {
                    if ($scope.isOnPage(k, $scope.labelCodesQuery)) {
                        $scope.uploaded_label_codes_file.items_paginated.push(v);
                    }
                });
            } else if (type == 'fees') {
                $scope.uploaded_fees_file.items_paginated = [];
                angular.forEach($scope.uploaded_fees_file.items, function(v, k) {
                    if ($scope.isOnPage(k, $scope.feesQuery)) {
                        $scope.uploaded_fees_file.items_paginated.push(v);
                    }
                });
            } else if (type == 'suppliers') {
                $scope.uploaded_suppliers_file.items_paginated = [];
                angular.forEach($scope.uploaded_suppliers_file.items, function(v, k) {
                    if ($scope.isOnPage(k, $scope.suppliersQuery)) {
                        $scope.uploaded_suppliers_file.items_paginated.push(v);
                    }
                });
            }
        }

        $scope.isOnPage = function(k, query) {
            if (query.page == 1 && k < query.limit*query.page) {
                return true;
            } else if ( k >= query.limit*(query.page - 1 ) && k < query.limit*query.page) {
                return true;
            }
            return false;
        }

        $scope.uploadFile = function(file) {
            file.upload = Upload.upload({
                url: API_CONFIG.url + '/import-data/upload',
                data: {file: file},
            });

            file.upload.then(function (response) {
                $timeout(function () {
                    file.result = response.data.data;
                    file.fields = [];
                    angular.forEach(response.data.data, function(v, k) {
                        if (k == 0) {
                            angular.forEach(v, function(value, name) {
                                file.fields.push(name);
                            });
                        }
                    });
                    file.preview = true;
                });
            }, function (response) {
                CommonService.message(response.data.data.error, 'error');
            });
        }

        $scope.importData = function(type, data) {

            switch (type) {
                case "products":
                    var queryData = {'products': data};
                    break;
                case "patients":
                    var queryData = {'patients': data};
                    break;
                case "doctors":
                    var queryData = {'doctors': data};
                    break;
                case "inventory":
                    var queryData = {'inventory': data};
                    break;
                case "categories":
                    var queryData = {'categories': data};
                    break;
                case "label_codes":
                    var queryData = {'label_codes': data};
                    break;
                case "fees":
                    var queryData = {'fees': data};
                    break;
                case "suppliers":
                    var queryData = {'suppliers': data};
                    break;
            }

            ImportService.importData(type, queryData).then(function(response){
                CommonService.message(response.data.message, 'success');
                switch (type) {
                    case "products":
                        $scope.uploaded_products_file = null;
                        break;
                    case "patients":
                        $scope.uploaded_patients_file = null;
                        break;
                    case "doctors":
                        $scope.uploaded_doctors_file = null;
                        break;
                    case "inventory":
                        $scope.uploaded_inventory_file = null;
                        break;
                    case "categories":
                        $scope.uploaded_categories_file = null;
                        break;
                    case "label_codes":
                        $scope.uploaded_label_codes_file = null;
                        break;
                    case "fees":
                        $scope.uploaded_fees_file = null;
                        break;
                    case "suppliers":
                        $scope.uploaded_suppliers_file = null;
                        break;
                }
            });
        }

        $scope.applyMatching = function(type, data, matches) {
            ImportService.checkData(type, data, matches).then(function(response){
                switch (type) {
                    case "products":
                        $scope.uploaded_products_file.items = response.data.data;
                        $scope.uploaded_products_file.preview = false;
                        break;
                    case "patients":
                        $scope.uploaded_patients_file.items = response.data.data;
                        $scope.uploaded_patients_file.preview = false;
                        break;
                    case "doctors":
                        $scope.uploaded_doctors_file.items = response.data.data;
                        $scope.uploaded_doctors_file.preview = false;
                        break;
                    case "inventory":
                        $scope.uploaded_inventory_file.items = response.data.data;
                        $scope.uploaded_inventory_file.preview = false;
                        break;
                    case "categories":
                        $scope.uploaded_categories_file.items = response.data.data;
                        $scope.uploaded_categories_file.preview = false;
                        break;
                    case "label_codes":
                        $scope.uploaded_label_codes_file.items = response.data.data;
                        $scope.uploaded_label_codes_file.preview = false;
                        break;
                    case "fees":
                        $scope.uploaded_fees_file.items = response.data.data;
                        $scope.uploaded_fees_file.preview = false;
                        break;
                    case "suppliers":
                        $scope.uploaded_suppliers_file.items = response.data.data;
                        $scope.uploaded_suppliers_file.preview = false;
                        break;
                }
            });
        }

    }
})();