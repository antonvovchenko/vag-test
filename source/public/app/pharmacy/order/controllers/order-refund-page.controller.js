(function() {
    'use strict';

    angular
        .module('app.pharmacy.prescription')
        .controller('OrderRefundPageController', OrderRefundPageController)

    function OrderRefundPageController(
        $q,
        $rootScope,
        $scope,
        $stateParams,
        $location,
        OrderResource,
        OrderService,
        CommonService,
        FormService,
        ProductService,
        PrescriptionService,
        PrescriptionResource
    ) {

        $scope.CommonService = CommonService;

        $scope.showForm = false;

        $scope.orderForm = {
        }

        if (angular.isUndefined($stateParams.id)) {
            CommonService.message('Order not found', 'error');
            $location.path('/order');

        }

        OrderService.getFormData($stateParams.id).then(function(results) {
            $scope.orderForm = results.item;
            $scope.orderForm.refund_items = [];
            angular.forEach(results.item.order_items, function(v,k) {
                if (v.product_id) {
                    $scope.addProduct(v);
                } else if (v.prescription_id) {
                    $scope.addPrescription(v);
                }
            });
            $scope.showForm = true;
            $rootScope.addFormEditingChecking($scope.orderForm);
            $scope.calculateTotals();
        });

        $scope.productQuantityChanged = function() {
            $scope.calculateTotals();
        }

        $scope.addProduct = function (formItem){
            if (formItem.qty <= formItem.product.qoh) {
                $scope.productSearchText = null;
                formItem.price = formItem.product.unit_price;
                formItem.tax = 0;
                formItem.net_due = 0;

                ProductService.calculatePrice(formItem);
                formItem.refund_qty = formItem.qty;
                $scope.orderForm.refund_items.push(formItem);
                return true;
            } else {
                return false;
            }
        }

        $scope.addPrescription = function (formItem){
            formItem.refund_qty = 1;
            $scope.orderForm.refund_items.push(formItem);
        }

        $scope.calculateTotals = function() {
            $scope.orderForm.total = 0;
            $scope.orderForm.taxes = 0;
            $scope.orderForm.net_due = 0;

            angular.forEach($scope.orderForm.refund_items, function(item, k) {
                if (item.product) {
                    $scope.orderForm.total = CommonService.numbersFormat($scope.orderForm.total) + CommonService.numbersFormat(item.total);
                    $scope.orderForm.taxes = CommonService.numbersFormat($scope.orderForm.taxes) + CommonService.numbersFormat(item.tax);
                    $scope.orderForm.net_due = CommonService.numbersFormat($scope.orderForm.net_due) + CommonService.numbersFormat(item.total);
                }
            });

            if (CommonService.numbersFormat($scope.orderForm.discount) > 0 && CommonService.numbersFormat($scope.orderForm.discount) < CommonService.numbersFormat($scope.orderForm.net_due)) {
                $scope.orderForm.net_due -= CommonService.numbersFormat($scope.orderForm.discount);
            } else {
                $scope.orderForm.discount = '0.00';
            }

            angular.forEach(['total', 'taxes', 'net_due', 'tendered', 'change'], function(item, k) {
                if (angular.isDefined($scope.orderForm[item])) {
                    $scope.orderForm[item] = CommonService.priceFormat($scope.orderForm[item]);
                    $scope.orderForm[item] = $scope.orderForm[item].replace(",", "");
                }
            });
        }

        $scope.apply = function (){

            var refunded_count = 0;

            angular.forEach($scope.orderForm.refund_items, function(v,k) {
                refunded_count += v.refund_qty;
            });

            if (refunded_count == 0) {
                CommonService.message('Nothing to refund. Please update QTY');
                return;
            }

            var order = new OrderResource({
                label: 'Refund Order#'+$scope.orderForm.id,
                total: $scope.orderForm.total,
                taxes: $scope.orderForm.taxes,
                discount: $scope.orderForm.discount,
                net_due: $scope.orderForm.net_due,
                status: 'completed',
                type: 'refund',
                refunded_order_id: $scope.orderForm.id,
                order_items: []
            });

            var refunded_prescriptions = [];
            angular.forEach($scope.orderForm.refund_items, function(v,k) {
                var qty = v.refund_qty;
                if (v.product_id && qty > 0) {
                    order.order_items.push({
                         product_id: v.product_id,
                         qty: qty
                    });
                } else if (v.prescription_id && qty > 0) {
                    order.order_items.push({
                        prescription_id: v.prescription_id
                    });
                    refunded_prescriptions.push(v.prescription);
                }
            });

            order.$save(function(data) {

                if (refunded_prescriptions.length > 0) {

                    $scope.promisies = [];

                    angular.forEach(refunded_prescriptions, function(prescription,k) {
                        //reverse transaction
                        $scope.promisies.push(PrescriptionService.reversePrescriptionTransactions(prescription.id));
                        //set void status
                        var prescriptionItem = new PrescriptionResource(prescription);
                        prescriptionItem.status = 'void';
                        $scope.promisies.push(prescriptionItem.$update());
                    });

                    $q.all($scope.promisies).then(function(data){
                        $scope.finishRefund();
                    }, function(response) {
                        FormService.showErrors(response);
                    });

                } else {
                    $scope.finishRefund();
                }
            }, function(response){
                FormService.showErrors(response);
            });
        }

        $scope.finishRefund = function() {
            $rootScope.destroyFormEditingChecking();
            CommonService.message('Successfully refunded!', 'success');
            $location.path("/order/edit/"+$scope.orderForm.id);
        }

    }
})();