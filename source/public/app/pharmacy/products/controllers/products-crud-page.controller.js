(function() {
    'use strict';

    angular
        .module('app.pharmacy.products')
        .controller('ProductsCrudPageController', ProductsCrudPageController);

    function ProductsCrudPageController(ProductResource, $scope, LabelCodeResource, $stateParams, $location, ProductService, PHARMACY_SETTINGS, CommonService, FormService, $mdDialog, $timeout, $rootScope) {

        $scope.showForm = false;

        //auto complete for generic
        $scope.searchText = '';
        $scope.querySearch = function(query) {
            var query = {
                    title: query,
                    is_generic: 1
                };
            return ProductResource.get(query).$promise.then(function(response) {
                return response.data.data;
            });
        }
        $scope.productSelected = function (value) {
            if (value !== null && angular.isDefined(value)) {
                $scope.itemForm.generic_product_id = value.id;
            }
        }

        //auto complete for Label Codes
        $scope.labelCodeSearchText = null;
        $scope.selectedLabelCodes = [];
        $scope.labelCodeSearch = function (query, form) {
            return LabelCodeResource.get({title: query}).$promise.then(function(response) {
                return _.filter(response.data.data, function(n) {
                    return _.isEmpty(_.find(form.label_codes, n));
                });
            });
        }
        $scope.labelCodeSelected = function (labelCode, form) {
            if (!_.isEmpty(labelCode)) {
                form.label_codes.push(labelCode);
            }
        }

        if (angular.isUndefined($stateParams.id)) {
            $scope.itemForm = {

                id: "",
                title: "",
                is_prescription: "",
                is_generic: 0,
                generic_product_id: "",
                barcode: "",
                ndc: "",

                pkg_size: "",
                markup: "",
                tax_id: "",
                pkg_price: "",
                pkg_cost: "",
                unit_cost: "",
                unit_price: "",

                label_codes: [],
                description: "",

                markup_type_id: "",
                qoh: "",
                min_qty: "",
                max_qty: "",
                location: "",
                lead_time: "",
                is_active: 1,

                last_purchase: "",
                last_supplier: "",
                last_sale: "",
                closest_expiration_date: "",

                errors: []
            }

            ProductService.getFormData().then(function(results) {
                setFormData(results);
                //set first tax
                if ($scope.taxes.length > 0) {
                    var tax = $scope.taxes[0];
                    $scope.itemForm.tax_id = tax.id;
                }
                $scope.itemForm.id = results.nextId;
                $scope.is_add = true;
                $scope.showForm = true;
                $rootScope.addFormEditingChecking($scope.itemForm);
            });

        } else {
            ProductService.getFormData($stateParams.id).then(function(results) {

                setFormData(results);

                if (angular.isDefined(results.item.generic.title)) {
                    $scope.searchText = results.item.generic.title;
                }

                $scope.itemForm = results.item;

                setInventories(results.inventory);

                $scope.is_add = false;
                $scope.showForm = true;
            });

        }

        //submit add form
        $scope.submit = function() {

            var product = new ProductResource($scope.itemForm);

            //type validation, because required not work for md-radio-group
            if ($scope.itemForm.is_prescription === '') {
                CommonService.message('Please set type of product', 'error');
                return;
            }

            if (product.is_generic) {
                product.generic_product_id = "";
            }

            if ($scope.is_add) {
                delete product.id;
                product.$save(function(data) {
                    CommonService.message(data.data.message, 'success');
                    $location.path("/products");
                }, function(response){
                    FormService.showErrors(response);
                });
            } else {
                product.id = $scope.itemForm.id;
                product.$update(function() {
                    $location.path("/products");
                }, function(response){
                    FormService.showErrors(response);
                });
            }

            $rootScope.destroyFormEditingChecking();
        }

        $scope.categoryChanged = function() {
            if (!_.isEmpty($scope.itemForm.markup_type_id)) {
                var category = _.first(
                                    _.filter($scope.markup_types, function(n) {
                                        return n.id == $scope.itemForm.markup_type_id;
                                    })
                                );

                if (!_.isEmpty(category)) {
                    if (CommonService.numbersFormat($scope.itemForm.markup) != CommonService.numbersFormat(category.default_markup)) {
                        CommonService.confirm(
                            'The category default markup is different than the product markup. Do you want to change markup?',
                            function() {
                                $scope.itemForm.markup = category.default_markup;
                                $scope.recalculatePrices();
                            }
                        );
                    } else {
                        $scope.itemForm.markup = category.default_markup;
                    }
                }
            }
            $scope.recalculatePrices();
        }

        $scope.recalculatePrices = function (){

            $timeout(function() {
                //set number format for all values
                angular.forEach(['pkg_price', 'unit_cost', 'unit_price', 'markup', 'pkg_size'], function(v, k) {
                    if (angular.isDefined($scope.itemForm[v])) {
                        $scope.itemForm[v] = CommonService.numbersFormat($scope.itemForm[v]);
                    }
                });

                //calculations
                if ($scope.itemForm.pkg_size > 0 && $scope.itemForm.pkg_cost > 0) {
                    $scope.itemForm.unit_cost = $scope.itemForm.pkg_cost / $scope.itemForm.pkg_size;
                }
                if ($scope.itemForm.pkg_cost > 0) {
                    $scope.itemForm.pkg_price = $scope.itemForm.pkg_cost;

                }
                var markupValue = 0;
                if ($scope.itemForm.markup > 0) {
                    markupValue= $scope.itemForm.pkg_price * $scope.itemForm.markup / 100;
                }
                $scope.itemForm.pkg_price = $scope.itemForm.pkg_price + markupValue;
                $scope.itemForm.unit_price = 0;
                if ($scope.itemForm.pkg_size > 0) {
                    $scope.itemForm.unit_price = CommonService.numbersFormat($scope.itemForm.pkg_price)/$scope.itemForm.pkg_size;
                }

                //set price format for all values
                angular.forEach(['pkg_price', 'unit_cost', 'unit_price', 'markup'], function(v, k) {
                    $scope.itemForm[v] = CommonService.priceFormat($scope.itemForm[v]);
                });
            }, 500);

        }

        $scope.validDate = function(date, format) {
            CommonService.validDate(date, format);
        };

        $scope.cancel = function($event) {
            CommonService.closeModal($event);
        };

        $scope.searchNDC = function($event) {
            $event.stopPropagation();
            $event.preventDefault();

            var title = $scope.itemForm.title;

            $scope.searchNDCForm = {
                text: title,
                ndc: '',
                product: ''
            }

            $scope.ndcSearchSelected = function (value) {
                if (value !== null && angular.isDefined(value)) {
                    $scope.itemForm.generic_product_id = value.id;
                }
            }

            $mdDialog.show({
                templateUrl: '/app/pharmacy/products/templates/product-ndc-search-dialog.tmpl.html',
                parent: angular.element(document.body),
                clickOutsideToClose:false,
                scope: $scope,
                preserveScope: true,
                hasBackdrop: false,
                autoWrap: false,
                onComplete: function() {
                    angular.element('.md-dialog-container').css('z-index', 'auto');
                    angular.element('#searchNDCInput').focus();
                }
            });
        }

        $scope.setNDCToForm = function(obj) {
            var ndc = '';
            angular.forEach(obj, function(v, k) {
                if (v.title == 'NDC') {
                    ndc = v.value;
                }
            });
            $scope.itemForm.ndc = ndc;
            $scope.ndcProducts = [];
            $scope.ndcProductNames = [];
            $scope.ndcProductItems = [];
            $scope.isProcessNDCSearch = false;
            $scope.cancel();
        }

        $scope.searchNDCProducts = function() {
            if (!$scope.isProcessNDCSearch) {
                $scope.isProcessNDCSearch = true;
                ProductService.searchNDCProducts($scope.searchNDCForm.text).then(function(response){
                    $scope.isProcessNDCSearch = false;
                    $scope.ndcProducts = response.data.products;
                }, function(response){
                    $scope.isProcessNDCSearch = false;
                    FormService.showErrors(response);
                });
            }

        }

        $scope.setNDCProduct = function() {
            if (!$scope.isProcessNDCSearch) {
                $scope.isProcessNDCSearch = true;
                ProductService.searchNDC($scope.searchNDCForm.product).then(function (response) {
                    $scope.isProcessNDCSearch = false;
                    $scope.ndcProductNames = response.data.names;
                    $scope.ndcProductItems = response.data.items;
                }, function(response){
                    $scope.isProcessNDCSearch = false;
                    FormService.showErrors(response);
                });
            }
        }

        $scope.viewInventory = function($event) {
            $event.stopPropagation();
            $event.preventDefault();
            $mdDialog.show({
                templateUrl: '/app/pharmacy/products/templates/product-inventory-dialog.tmpl.html',
                parent: angular.element(document.body),
                targetEvent: $event,
                clickOutsideToClose:true,
                scope: $scope,
                preserveScope: true
            });
        }

        function setFormData(data) {
            $scope.markup_types = data.markup_types;
            $scope.taxes = data.taxes;
        }

        function setInventories(data) {
            $scope.inventories = data;
            if (!_.isEmpty(data)) {
                var last_supplier = '';
                var last_supplier_date = '';
                var last_sale = ''; //todo: get info from orders
                var closest_expiration_date = '';

                angular.forEach(data, function (v, k) {
                    //set first value
                    if (_.isEmpty(last_supplier)) {
                        last_supplier = v.supplier.name+' ('+ v.created_at+')';
                        last_supplier_date = v.created_at;
                    }
                    if (_.isEmpty(closest_expiration_date)) {
                        closest_expiration_date = CommonService.validDate(v.expiry_date, PHARMACY_SETTINGS.date_format);
                    }
                    //check others
                    if ( moment(new Date(last_supplier_date)).diff(new Date(v.created_at), 'days') < 0) {
                        last_supplier = v.supplier.name+' ('+ v.created_at+')';
                        last_supplier_date = v.created_at;
                    }
                    if ( moment(new Date(closest_expiration_date)).diff(v.expiry_date, 'days') > 0) {
                        closest_expiration_date = CommonService.validDate(v.expiry_date, PHARMACY_SETTINGS.date_format);
                    }
                });

                $scope.itemForm.last_supplier = last_supplier;
                $scope.itemForm.last_sale = last_sale;
                $scope.itemForm.closest_expiration_date = closest_expiration_date;
            }
            $rootScope.addFormEditingChecking($scope.itemForm);
        }
    }
})();