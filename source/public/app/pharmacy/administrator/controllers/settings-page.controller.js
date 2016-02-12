(function() {
    angular
        .module('app.pharmacy.administrator')
        .controller('SettingsPageController', SettingsPageController);

    function SettingsPageController($q, $scope, SettingsService, CommonService, $mdDialog, PHARMACY_SETTINGS, $rootScope, SettingsResource, FormService) {

        $scope.isShow = false;

        $scope.printers = [];
        $scope.settings = [];

        $scope.commonForm = {
            common_session_lifetime: '',
            common_pharmacy_name: '',
            common_pharmacy_address: '',
            common_pharmacy_city: '',
            common_pharmacy_phone: '',
            common_pharmacy_gct: ''
        }
        $scope.insuranceForm = {
            company_name: '',

            insurance_service_fee_percent: '',
            gct_percent: '',

            bcj_provider_number: '',
            loj_provider_number: '',
            eba_provider_number: '',
            med_provider_number: '',
            jadep_provider_number: '',
            nhf_provider_number: '',
            clk_provider_number: '',
            medecus_provider_number: '',

            bcj_bin_number: '',
            nhf_bin_number: '',
            eba_bin_number: '',
            flb_bin_number: '',
            clk_bin_number: '',
            med_bin_number: ''
        }

        $scope.printerForm = {
            printers_default_print_type: '',
            printers_default_printer: '',
            printers_prescription_print_type: '',
            printers_prescription_printer: '',
            printers_inventory_print_type: '',
            printers_inventory_printer: ''
        }

        SettingsService.getFormData().then(function(data){

            $scope.printers = data.printers;
            $scope.settings = data.settings;
            $scope.setLicense(data.license);

            angular.forEach(data.settings, function(v,k) {
                if (v.name.indexOf('common_') !== -1) {
                    $scope.commonForm[v.name] = v.value;
                } else if (v.name.indexOf('printers_') !== -1) {
                    $scope.printerForm[v.name] = v.value;
                } else if (v.name != 'license') {
                    $scope.insuranceForm[v.name] = v.value;
                }
            });
        });

        $scope.save = function(form){

            $scope.promisies = [];

            angular.forEach(form, function(v,k) {
                var item = new SettingsResource({name: k, value: v});
                var id = $scope.getId(k);
                if (!id) {
                    $scope.promisies.push(item.$save());
                } else {
                    item.id = id;
                    $scope.promisies.push(item.$update());
                }
            });

            $q.all($scope.promisies).then(function(data){
                CommonService.message('Successfully updated.', 'success');
                $rootScope.destroyFormEditingChecking();
            });
        }

        $scope.getId = function(name) {
            var id = false;
            angular.forEach($scope.settings, function(v,k) {
                if (v.name == name) {
                    id = v.id;
                }
            });
            return id;
        }

        $scope.setLicenseKeyDialog = function() {
            $mdDialog.show({
                templateUrl: 'app/pharmacy/administrator/templates/settings/_settings-license-dialog.tmpl.html',
                parent: angular.element(document.body),
                clickOutsideToClose:true,
                scope: $scope,
                preserveScope: true
            });
        }

        //submit form
        $scope.submitLicenseDialogForm = function() {
            SettingsService.setLicense($scope.form.key).then(function(response) {
                $scope.setLicense(response);
                $scope.cancel();
            }, function (response){
                $scope.setLicense(response);
                CommonService.message(response.data.data.error, 'error');
                $scope.cancel();
            });
        }

        //close dialog
        $scope.cancel = function($event) {
            CommonService.closeModal($event);
        };

        $scope.setLicense = function(data) {
            $scope.license = data.license;
            $scope.license.expire = CommonService.validDate( $scope.license.expire, PHARMACY_SETTINGS.date_format);
            $scope.error = data.error;
            $scope.isShow = true;
            if ($scope.error != false) {
                $rootScope.showToolbarNotification($scope.error);
            } else {
                $rootScope.hideToolbarNotification();
            }
        }

        $scope.tabSelected = function(tab) {
            switch(tab) {
                case 'common':
                    $rootScope.addFormEditingChecking($scope.commonForm);
                    break;
                case 'insurance':
                    $rootScope.addFormEditingChecking($scope.insuranceForm);
                    break;
                case 'printers':
                    $rootScope.addFormEditingChecking($scope.printerForm);
                    break;
                default:
                    $rootScope.destroyFormEditingChecking();
                    break;
            }
        };
    }
})();