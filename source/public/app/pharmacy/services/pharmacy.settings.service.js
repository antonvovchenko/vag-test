(function() {
    'use strict';

    angular
        .module('app.pharmacy')

        .factory('SettingsResource', ['$resource', 'API_CONFIG', function($resource, API_CONFIG) {
            return $resource(
                API_CONFIG.url + '/settings/:settingsId',
                { settingsId:'@id' },
                {
                    'update': { method:'PUT' }
                }
            );
        }])

        .factory('SettingsService', [
            '$q', 'SettingsResource', 'API_CONFIG', '$http', 'SettingsResource',
            function($q, ProductResource, API_CONFIG, $http, SettingsResource) {
                return {
                    getLicense: function(){
                        return $http({
                            method: 'GET',
                            url: API_CONFIG.url + '/settings-helper/license'
                        }).then(function(response) {
                            return response.data;
                        });
                    },
                    setLicense: function(key){
                        return $http({
                            method: 'POST',
                            data: {key: key},
                            url: API_CONFIG.url + '/settings-helper/license'
                        }).then(function(response) {
                            return response.data;
                        });
                    },
                    getPrinters: function(){
                        return $http({
                            method: 'GET',
                            url: API_CONFIG.url + '/settings-helper/printers'
                        }).then(function(response) {
                            return response.data;
                        });
                    },
                    getFormData: function () {
                        return $q.all(
                            [
                                SettingsResource.get().$promise,
                                this.getPrinters(),
                                this.getLicense()
                            ]
                        ).then(function (results) {
                                return {
                                    settings: results[0].data.data,
                                    printers: results[1].data,
                                    license: results[2].data
                                };
                            });
                    }
                };
            }
        ]);

})();