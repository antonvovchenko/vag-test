(function() {
    'use strict';

    angular
        .module('app.pharmacy.dashboard')
        .directive('chartjsPieWidget', chartjsPieWidget);

    /* @ngInject */
    function chartjsPieWidget($timeout) {
        // Usage:
        //
        // Creates:
        //
        var directive = {
            require: 'triWidget',
            link: link,
            restrict: 'A'
        };
        return directive;

        function link($scope, $element, attrs, widgetCtrl) {
            widgetCtrl.setLoading(true);

            $timeout(function() {
                widgetCtrl.setLoading(false);
            }, 1500);

            widgetCtrl.setMenu({
                icon: 'zmdi zmdi-more-vert',
                items: [{
                    icon: 'zmdi zmdi-refresh',
                    title: 'DASHBOARDS.WIDGETS.MENU.REFRESH',
                    click: function() {
                        widgetCtrl.setLoading(true);
                        $timeout(function() {
                            widgetCtrl.setLoading(false);
                        }, 1500);
                    }
                },{
                    icon: 'zmdi zmdi-share',
                    title: 'DASHBOARDS.WIDGETS.MENU.SHARE'
                },{
                    icon: 'zmdi zmdi-print',
                    title: 'DASHBOARDS.WIDGETS.MENU.PRINT'
                }]
            });

            $scope.pieChart = {
                labels: ['Total Sales', 'RX Sales', 'OTC Sales'],
                data: [300, 500, 100]
            };
        }
    }
})();