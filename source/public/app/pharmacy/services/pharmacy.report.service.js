(function() {
    'use strict';

    angular
        .module('app.pharmacy')

        .factory('ReportService', [
            '$q', '$http', 'API_CONFIG', '$window', '$timeout', 'CommonService',
            function($q, $http, API_CONFIG, $window, $timeout, CommonService) {
                return {
                    cashierReport: function(start, end) {
                        var url = '/report/cashier?start='+CommonService.validDate(start, API_CONFIG.db_date_format)+'&end='+CommonService.validDate(end, API_CONFIG.db_date_format);
                        $timeout(function() {
                            $window.open(url, 'cashier-report', "width=800px,height=800px,scrollbars=yes");
                        },0,false);
                    },
                    gctReport: function(start, end) {
                        var url = '/report/gct?start='+CommonService.validDate(start, API_CONFIG.db_date_format)+'&end='+CommonService.validDate(end, API_CONFIG.db_date_format);
                        $timeout(function() {
                            $window.open(url, 'gct-report', "width=800px,height=800px,scrollbars=yes");
                        },0,false);
                    },
                    purchaseReport: function(start, end) {
                        var url = '/report/purchase?start='+CommonService.validDate(start, API_CONFIG.db_date_format)+'&end='+CommonService.validDate(end, API_CONFIG.db_date_format);
                        $timeout(function() {
                            $window.open(url, 'purchase-report', "width=800px,height=800px,scrollbars=yes");
                        },0,false);
                    }
                };
            }
        ]);

})();