(function() {
    'use strict';

    angular
        .module('app.pharmacy.prescription')
        .controller('PrescriptionPageController', PrescriptionPageController);

    /* @ngInject */
    function PrescriptionPageController($scope, $state, PrescriptionResource, PHARMACY_SETTINGS, CommonService, PrescriptionService, FormService, $location) {

        $scope.query = {
            filter: '',
            limit: PHARMACY_SETTINGS.pagination.per_page,
            order: '-created_at',
            page: 1
        };

        $scope.CommonService = CommonService;

        $scope.onChange = function () {
            return PrescriptionResource.get($scope.query, success).$promise;
        };
        function success(items) {
            $scope.items = items.data;
        }
        function getItems() {
            $scope.onChange();
        }

        $scope.savePrescriptionForm = function (data) {
            var prescription = new PrescriptionResource(data);
            return prescription.$update();
        }


        $scope.voidPrescription = function(item) {
            CommonService.confirm(
                'Are you sure?',
                function() {
                    PrescriptionService.reversePrescriptionTransactions(item.id).then(function(response){
                            var data = item;
                            data.status = 'void';
                            $scope.savePrescriptionForm(data).then(
                                function(data) {
                                    CommonService.message(data.data.message, 'success');
                                    $state.go($state.current, {}, {reload: true});
                                }, function(response){
                                    $location.path("/prescription/edit/"+item.id);
                                    FormService.showErrors(response);
                                }
                            );
                        },
                        function(){
                            CommonService.message('Internal system error. Please try again.', 'error');
                        });
                }
            );
        }

        $scope.refillPrescription = function(item) {
            $location.path("/prescription/refill/"+item.id);
        }
    }
})();