(function() {
    'use strict';

    angular
        .module('app.pharmacy.reports')
        .controller('CashierReportPageController', CashierReportPageController);

    function CashierReportPageController($scope, ReportService, FormService) {

        $scope.isShow = true;

        $scope.currentDate = new Date();

        $scope.form = {
            start: new Date(new Date($scope.currentDate).setMonth($scope.currentDate.getMonth()-6)),
            end: $scope.currentDate
        }

        $scope.get = function() {
            ReportService.cashierReport($scope.form.start, $scope.form.end);
        }

    }
})();