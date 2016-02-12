(function() {
    'use strict';

    angular
        .module('app.pharmacy.administrator')
        .controller('UsersPageController', UsersPageController);

    /* @ngInject */
    function UsersPageController($mdDialog, $http, UserResource, $scope, API_CONFIG, PHARMACY_SETTINGS, CommonService, FormService, $stateParams) {

        $scope.query = {
            filter: '',
            limit: PHARMACY_SETTINGS.pagination.per_page,
            order: 'full_name',
            page: 1
        };

        function success(users) {
            $scope.users = users.data;
        }

        $scope.onChange = function () {
            return UserResource.get($scope.query, success);
        };

        $scope.userForm = {
            id: "",
            logon_name: "",
            full_name: "",
            email: "",
            pass: "",
            type: "",
            pharmacist_registration_number: "",
            is_active: 1,
            errors: []
        }

        //open add dialog
        $scope.openAddUserDialog = function($event) {

            $http({
                method: 'GET',
                url: API_CONFIG.url + '/user-helper/next-id'
            }).success(function(data) {

                $scope.userForm.id = data.data.nextId;

                $mdDialog.show({
                    templateUrl: '/app/pharmacy/administrator/templates/user-add-dialog.tmpl.html',
                    parent: angular.element(document.body),
                    targetEvent: $event,
                    clickOutsideToClose:true,
                    scope: $scope,
                    preserveScope: true
                });
            });
        }

        //open edit dialog
        $scope.openEditUserDialog = function($event, id) {
            UserResource.get({userId: id})
                .$promise.then(function(user) {
                    $scope.userForm = user.data;

                    $mdDialog.show({
                        templateUrl: '/app/pharmacy/administrator/templates/user-edit-dialog.tmpl.html',
                        parent: angular.element(document.body),
                        targetEvent: $event,
                        clickOutsideToClose:true,
                        scope: $scope,
                        preserveScope: true
                    });
                });
        }

        if (angular.isDefined($stateParams.id)) {
            $scope.openEditUserDialog(null, $stateParams.id);
        }

        $scope.user_types = {
            "Cashier": 1,
            "Pharmacist": 2,
            "User": 3,
            "Administrator": 4,
            "Pharmacy Technician": 5,
            "Accountant": 6,
            "Intern Pharmacist": 7
        };

        //submit add form
        $scope.submit = function(is_adding) {

            var user = new UserResource();
            user.logon_name = $scope.userForm.logon_name;
            user.full_name = $scope.userForm.full_name;
            user.email = $scope.userForm.email;
            user.password = $scope.userForm.pass;
            user.type = $scope.userForm.type;
            user.pharmacist_registration_number = $scope.userForm.pharmacist_registration_number;
            user.is_active = $scope.userForm.is_active;

            if (is_adding) {
                user.$save(function(data) {
                    CommonService.message(data.data.message, 'success');
                    $scope.cancel();
                    $scope.onChange();
                }, function(response){
                    FormService.showErrors(response);
                });
            } else {
                user.id = $scope.userForm.id;
                user.$update(function(data) {
                    CommonService.message(data.data.message, 'success');
                    $scope.cancel();
                    $scope.onChange();
                }, function(response){
                    FormService.showErrors(response);
                });
            }

        }
        //close dialog
        $scope.cancel = function($event) {
            CommonService.closeModal($event);
        };

        $scope.getTypeLabel = function(typeId) {
            var typeLabel = typeId;
            angular.forEach($scope.user_types, function(id, label) {
                if (typeId == id) {
                    typeLabel = label;
                }
            });
            return typeLabel;
        };
    }
})();