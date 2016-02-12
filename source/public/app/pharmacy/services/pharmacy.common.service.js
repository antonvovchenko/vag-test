(function() {
    'use strict';

    angular
        .module('app.pharmacy')
        .factory('CommonService', [
            '$mdToast', '$mdDialog', '$window', '$timeout',
            function($mdToast, $mdDialog, $window, $timeout) {
                return {

                    //types: error, success, info
                    message: function (message, type) {
                        $mdToast.show({
                            template: '<md-toast class="md-toast pharmacy-'+type+'"><div>' + message + '</div></md-toast>',
                            hideDelay: 5000,
                            position: 'top right'
                        });
                    },

                    confirm: function (message, callback) {
                        var confirm = $mdDialog.confirm()
                            .title(message)
                            .ariaLabel('Confirm dialog')
                            .ok('Yes')
                            .cancel('No');
                        $mdDialog.show(confirm).then(callback);
                    },

                    //close modal
                    closeModal: function($event) {
                        if (angular.isDefined($event)) {
                            $event.stopPropagation();
                            $event.preventDefault();
                        }
                        $mdDialog.cancel();
                    },

                    //valid date
                    validDate: function (date, format) {
                        if (!moment(date).isValid()) {
                            return '';
                        }
                        if (angular.isDefined(format)) {
                            return moment(date).format(format);
                        } else {
                            return moment(date).toDate();
                        }
                    },

                    //return prepared template src
                    prepareTemplateSrc: function (src) {
                        return src+'?versionBLD='+versionBLD;
                    },

                    /**
                     * numberFormat
                     *
                     * @param string value: value
                     * @param string type: float(default) or integer
                     */
                    numbersFormat: function(value, type) {
                        if (angular.isUndefined(value)) {
                            value = 0;
                        }
                        if (angular.isUndefined(type)) {
                            type = 'float';
                        }

                        if (value === null) {
                            value = '';
                        }
                        value = value.toString();
                        value = value.replace(" ", "");
                        if (value === '') {
                            if (type == 'float') {
                                return 0.00;
                            } else {
                                return 0;
                            }
                        }
                        if (type == 'float') {
                            return parseFloat(value.replace(",", ""));
                        } else {
                            return parseInt(value.replace(",", ""));
                        }
                    },

                    /**
                     * priceFormat
                     *
                     * @param string value: value
                     * @param integer n: length of decimal
                     * @param integer x: length of whole part
                     * @param mixed   s: sections delimiter
                     * @param mixed   c: decimal delimiter
                     */
                    priceFormat: function (value, n, x, s, c) {
                        if (angular.isUndefined(n)) {
                            n = 2;
                        }
                        if (angular.isUndefined(x)) {
                            x = 3;
                        }
                        if (angular.isUndefined(s)) {
                            s = ',';
                        }
                        if (angular.isUndefined(c)) {
                            c = '.';
                        }
                        var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
                            num = parseFloat(value).toFixed(Math.max(0, ~~n));
                        return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
                    },

                    print: function(type, id) {
                        switch (type) {
                            case 'prescription':
                                var url = '/print-screen?prescription_id='+id
                                break;
                            case 'prescription_labels':
                                var url = '/print-screen?prescription_labels_id='+id
                                break;
                            case 'return_drug':
                                var url = '/print-screen?return_drug_id='+id
                                break;
                            default:
                                return;
                                break;
                        }
                        $timeout(function() {
                            $window.open(url, 'print', "width=700px,height=600px,scrollbars=yes");
                        },0,false);
                    }

                };
            }]);

})();