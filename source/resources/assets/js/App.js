(function() {
    'use strict';

    angular
        .module('app', [
            'triangular',
            'ngAnimate', 'ngCookies', 'ngTouch', 'ngSanitize', 'ngMessages', 'ngMaterial', 'ngResource', 'satellizer',
            'ui.router', 'pascalprecht.translate', 'LocalStorageModule', 'googlechart', 'chart.js', 'linkify', 'ui.calendar',
            'angularMoment', 'textAngular', 'uiGmapgoogle-maps', 'hljs', 'md.data.table', angularDragula(angular), 'dnd', 'ngFileUpload', 'fcsa-number', 'angular-loading-bar', 'cfp.hotkeys', //'AngularPrint',
            'app.pharmacy'
        ])
        //common config
        .config(['cfpLoadingBarProvider', function(cfpLoadingBarProvider) {

            //disable loading spinner
            cfpLoadingBarProvider.includeSpinner  = false;

        }])
        // create a constant for languages so they can be added to both triangular & translate
        .constant('APP_LANGUAGES', [{
            name: 'LANGUAGES.ENGLISH',
            key: 'en'
        }])
        // set a constant for the API we are connecting to
        .constant('API_CONFIG', {
            'url':  '/api',
            'db_date_format': 'YYYY-MM-DD HH:mm:ss'
        })
        //default states
        .constant('DEFAULT_STATES', {
            'LOGGED_IN':  'triangular.admin-default.dashboard-main-page',
            'NOT_LOGGED_IN': 'authentication.login'
        })
        // common project settings
        .constant('PHARMACY_SETTINGS', {
            date_format: 'MM/DD/YYYY',
            pagination: {
                per_page: '10'
            }
        }).run([
            '$rootScope','$location', 'CommonService', '$timeout',
            function($rootScope, $location, CommonService, $timeout) {
                //var to determine if some form data changed and not saved
                $rootScope.formDataHasChanged = false;
                //form editing checking
                $rootScope.addFormEditingChecking = function(form) {
                    $timeout(function() {
                        $rootScope.project = form;
                        $rootScope.original = angular.copy(form);

                        $rootScope.initialComparison = !angular.equals($rootScope.project, $rootScope.original);
                        $rootScope.formDataHasChanged = angular.copy($rootScope.initialComparison);

                        $rootScope.$watch('project',function(newValue, oldValue) {
                            if (newValue != oldValue) {
                                //var difference = _.reduce($rootScope.project, function(result, value, key) {
                                //    return _.isEqual(value, $rootScope.original[key]) ?
                                //        result : result.concat(key);
                                //}, []);
                                $rootScope.formDataHasChanged = !angular.equals($rootScope.project, $rootScope.original);
                            }
                        },true);
                    }, 800);
                }

                $rootScope.destroyFormEditingChecking = function(callback) {
                    $rootScope.project = {};
                    $rootScope.original = {};
                    $rootScope.formDataHasChanged = false;
                    if (angular.isDefined(callback)) {
                        callback();
                    }
                };

                $rootScope.$on('$locationChangeStart', function(scope, next, current){
                    //check if user not finished some editing (for now only jobs)
                    if (
                        $rootScope.formDataHasChanged &&
                        current != next
                    ) {
                        scope.preventDefault();
                        CommonService.confirm('Are you sure you want to leave this page without saving your changes?', function(flag){
                            if (flag) {
                                $rootScope.destroyFormEditingChecking();
                                next = next.replace($location.protocol()+'://'+$location.host()+'/#', "");
                                $location.path(next);
                            }
                        })
                    }
                });

                $rootScope.$on('$stateChangeStart', function(scope, toState, toParams, fromState, fromParams){
                    //check if user not finished some editing
                    if (
                        $rootScope.formDataHasChanged &&
                        fromState.url != toState.url
                    ) {
                        scope.preventDefault();
                        CommonService.confirm('Are you sure you want to leave this page without saving your changes?', function(flag){
                            if (flag) {
                                $rootScope.destroyFormEditingChecking();
                                $location.path(toState.url);
                            }
                        })
                    }
                });

            }]);



})();