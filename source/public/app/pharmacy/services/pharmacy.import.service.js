(function() {
    'use strict';

    angular
        .module('app.pharmacy')
        .factory('ImportService', [
            '$q', 'API_CONFIG', '$http',
            function($q, API_CONFIG, $http) {
                return {
                    importData: function(type, data){
                        return $http({
                            method: 'POST',
                            data: {type: type, data: data},
                            url: API_CONFIG.url + '/import-data/import'
                        }).then(function(response) {
                            return response.data;
                        });
                    },
                    checkData: function(type, data, matches){
                        return $http({
                            method: 'POST',
                            data: {type: type, data: data, matches: matches},
                            url: API_CONFIG.url + '/import-data/check'
                        }).then(function(response) {
                            return response.data;
                        });
                    }
                };
            }
        ]);

})();