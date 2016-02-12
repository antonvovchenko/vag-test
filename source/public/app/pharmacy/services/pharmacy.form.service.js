(function() {
    'use strict';

    angular
        .module('app.pharmacy')
        .factory('FormService', [
            'CommonService',
            function(CommonService) {
                return {
                    showErrors: function (response) {
                        var html = '';
                        if (angular.isArray(response.data.data.errors)) {
                            angular.forEach(response.data.data.errors, function (error, key) {
                                if (angular.isDefined(error.message)) {
                                    html += '<p flex="100">'+error.message+'</p>';
                                } else {
                                    html += '<p flex="100">'+error+'</p>';
                                }
                            });
                        } else {
                            html += '<p flex="100">'+response.data.data.errors+'</p>';
                        }
                        CommonService.message(html, 'error');
                    }
                };
            }])
        .directive(
        'ngIncludeTemplate',
            [
                'CommonService',
                function (CommonService) {
                    return {
                        templateUrl: function ($elem, $attrs) {
                            return CommonService.prepareTemplateSrc($attrs.ngIncludeTemplate);
                        },
                        restrict: 'A',
                        scope: {
                            'ngIncludeVariables': '&'
                        },
                        link: function ($scope, $elem, $attrs) {
                            var vars = $scope.ngIncludeVariables();
                            angular.forEach(vars, function (v, k) {
                                $scope[k] = v;
                            });
                        }
                    }
                }
            ]
        )
        .directive('stringToNumber', function() {
            return {
                require: 'ngModel',
                link: function(scope, element, attrs, ngModel) {
                    ngModel.$parsers.push(function(value) {
                        return '' + value;
                    });
                    ngModel.$formatters.push(function(value) {
                        return parseFloat(value, 10);
                    });
                }
            };
        });

})();