(function() {
    'use strict';

    angular
        .module('app.pharmacy')

        .factory('TransactionResource', [
            '$resource', 'API_CONFIG',
            function($resource, API_CONFIG) {
                return $resource(
                    API_CONFIG.url + '/transaction-request/:transactionId',
                    {
                        transactionId: '@id',
                        query: '@query'
                    },
                    {
                        'update': { method:'PUT' }
                    }
                );
            }
        ])

        .factory('TransactionService', [
            '$q', '$http', 'API_CONFIG', 'SettingsResource',
            function($q, $http, API_CONFIG, SettingsResource) {
                return {
                    sendRequest: function(data){
                        return $http.post(
                                API_CONFIG.url + '/transaction/send-request',
                                data
                            ).then(function(response) {
                                return response.data;
                            });
                    },
                    parseCardInfo: function(cardInformation){
                        return $http.post(
                                API_CONFIG.url + '/transaction/parse-card-info',
                                {cardInformation: cardInformation}
                            ).then(function(response) {
                                return response.data;
                            });
                    },
                    adjudicate: function(data){
                        return $http.post(
                            API_CONFIG.url + '/transaction/adjudicate',
                            data
                        ).then(function(response) {
                                return response.data;
                            });
                    },
                    reversal: function(data){
                        return $http.post(
                            API_CONFIG.url + '/transaction/reversal',
                            data
                        ).then(function(response) {
                                return response.data;
                            });
                    },
                    getInsuranceProviders: function(){
                        return $http.get(
                            API_CONFIG.url + '/transaction/insurance-providers'
                        ).then(function(response) {
                                return response.data;
                            });
                    },
                    getReconciliationFormData: function(){
                        return $q.all(
                            [
                                SettingsResource.get({name: 'gct_percent'}).$promise,
                                SettingsResource.get({name: 'insurance_service_fee_percent'}).$promise,
                                this.getInsuranceProviders()
                            ]
                        ).then(function (results) {
                                return {
                                    gct: results[0].data.data[0],
                                    insurance_service_fee: results[1].data.data[0],
                                    insurance_providers: results[2].data
                                };
                            });
                    }
                };
            }
        ]);

})();