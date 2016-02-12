(function() {
    'use strict';

    angular
        .module('app.pharmacy')

        .factory('PatientResource', [
            '$resource', 'API_CONFIG',
            function($resource, API_CONFIG) {
                return $resource(
                    API_CONFIG.url + '/patient/:patientId',
                    {
                        patientId: '@id',
                        query: '@query'
                    },
                    {
                        'update': { method:'PUT' }
                    }
                );
            }
        ])

        .factory('PatientService', [
            '$q', '$http', 'PatientResource', 'API_CONFIG',
            function($q, $http, PatientResource, API_CONFIG) {
                return {
                    getFormData: function (itemId) {
                        if (angular.isDefined(itemId)) {
                            return $q.all(
                                [
                                    PatientResource.get({patientId: itemId}).$promise
                                ]
                            ).then(function (results) {
                                    return {
                                        item: results[0].data
                                    };
                                });
                        } else {
                            return $q.all(
                                [
                                    this.getNextId()
                                ]
                            ).then(function (results) {
                                    return {
                                        nextId: results[0].data.nextId
                                    };
                                });
                        }
                    },

                    getNextId: function(){
                        return $http({
                            method: 'GET',
                            url: API_CONFIG.url + '/patient-helper/next-id'
                        }).then(function(response) {
                            return response.data;
                        });
                    },

                    getHistory: function(patient_id){
                        return $http({
                            method: 'POST',
                            data: {patient_id: patient_id},
                            url: API_CONFIG.url + '/patient-helper/history'
                        }).then(function(response) {
                            return response.data;
                        });
                    }
                };
            }
        ]);

})();