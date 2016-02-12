(function() {
    'use strict';

    angular
        .module('app.pharmacy.prescription')
        .controller('PrescriberCrudPageController', PrescriberCrudPageController);

    /* @ngInject */
    function PrescriberCrudPageController($scope, PrescriberResource, PrescriberService, $location, CommonService, $mdDialog, FormService, $stateParams, $rootScope) {

        $scope.showForm = false;

        if (angular.isUndefined($stateParams.id)) {
            PrescriberService.getFormData().then(function(results) {
                $scope.showForm = true;
                $scope.is_add = true;
                $scope.itemForm = {
                    id: results.nextId,
                    first_name: "",
                    last_name: "",
                    registration_number: "",
                    notes: "",
                    errors: []
                }
                $rootScope.addFormEditingChecking($scope.itemForm);
            });
        } else {
            PrescriberService.getFormData($stateParams.id).then(function(results) {
                $scope.showForm = true;
                $scope.is_add = false;
                $scope.itemForm = results.item;
                $rootScope.addFormEditingChecking($scope.itemForm);
            });
        }

        $scope.searchPrescriber = function($event) {
            $event.stopPropagation();
            $event.preventDefault();
            $mdDialog.show({
                templateUrl: '/app/pharmacy/prescription/templates/prescriber-search-dialog.tmpl.html',
                parent: angular.element(document.body),
                clickOutsideToClose:false,
                scope: $scope,
                preserveScope: true,
                hasBackdrop: false,
                autoWrap: false,
                onComplete: function() {
                    angular.element('.md-dialog-container').css('z-index', 'auto');
                }
            });
        }

        //submit add form
        $scope.submitItemForm = function() {

            var item = new PrescriberResource($scope.itemForm);

            if ($scope.is_add) {
                delete item.id;
                item.$save(function(data) {
                    $rootScope.destroyFormEditingChecking();
                    CommonService.message(data.data.message, 'success');
                    $location.path('/prescriber/edit/'+data.data.item);
                    $scope.cancel();
                }, function(response){
                    FormService.showErrors(response);
                });
            } else {
                item.$update(function(data) {
                    $rootScope.destroyFormEditingChecking();
                    CommonService.message(data.data.message, 'success');
                }, function(response){
                    FormService.showErrors(response);
                });
            }

        }


        //close dialog
        $scope.cancel = function($event) {
            CommonService.closeModal($event);
        };

    }
})();