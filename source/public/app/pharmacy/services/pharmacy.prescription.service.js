(function() {
    'use strict';

    angular
        .module('app.pharmacy')

        .factory('PrescriptionResource', [
            '$resource', 'API_CONFIG',
            function($resource, API_CONFIG) {
                return $resource(
                    API_CONFIG.url + '/prescription/:prescriptionId',
                    {
                        prescriptionId: '@id',
                        query: '@query'
                    },
                    {
                        'update': { method:'PUT' }
                    }
                );
            }
        ])

        .factory('PrescriptionService', [
            '$q', 'UserResource', '$http', 'API_CONFIG', 'FeeResource', 'PrescriptionResource',
            function($q, UserResource, $http, API_CONFIG, FeeResource, PrescriptionResource) {
                return {
                    getFormData: function (itemId) {

                        if (angular.isDefined(itemId)) {
                            return $q.all(
                                [
                                    UserResource.get({type: '2,5,7'}).$promise,
                                    FeeResource.get({order: 'sort'}).$promise,
                                    PrescriptionResource.get({prescriptionId: itemId}).$promise
                                ]
                            ).then(function (results) {
                                    return {
                                        users: results[0].data.data,
                                        fees: results[1].data.data,
                                        item: results[2].data
                                    };
                                });
                        } else {
                            return $q.all(
                                [
                                    UserResource.get({type: '2,5,7'}).$promise,
                                    FeeResource.get({order: 'sort'}).$promise
                                ]
                            ).then(function (results) {
                                    return {
                                        users: results[0].data.data,
                                        fees: results[1].data.data
                                    };
                                });
                        }
                    },

                    reversePrescriptionTransactions: function(prescriptionId) {
                        return $http({
                            method: 'POST',
                            data: {prescriptionId: prescriptionId},
                            url: API_CONFIG.url + '/prescription-helper/reverse-transactions'
                        }).then(function(response) {
                            return response.data;
                        });
                    }
                };
            }
        ]);

})();