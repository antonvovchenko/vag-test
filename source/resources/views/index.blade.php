<!doctype html>
<html ng-app="app">
<head>
    <meta charset="utf-8">
    <title>BLD Pharmacy @yield('head-title')</title>
    <meta name="description" content="">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1" />

    <?php $versionBLD = time(); ?>
    <!-- TODO: Remove no cache  -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <!-- load external bower css files -->
    <link rel="stylesheet" href="/build/assets/bower_components/angular-chart.js/dist/angular-chart.css" />
    <link rel="stylesheet" href="/build/assets/bower_components/angular-dragula/dist/dragula.min.css" />
    <link rel="stylesheet" href="/build/assets/bower_components/angular-material-data-table/dist/md-data-table.min.css" />
    <link rel="stylesheet" href="/build/assets/bower_components/fullcalendar/dist/fullcalendar.css" />
    <link rel="stylesheet" href="/build/assets/bower_components/font-awesome/css/font-awesome.css" />
    <link rel="stylesheet" href="/build/assets/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css" />
    <link rel="stylesheet" href="/build/assets/bower_components/textAngular/src/textAngular.css" />
    <link rel="stylesheet" href="/build/assets/bower_components/weather-icons/css/weather-icons.css" />
    <link rel="stylesheet" href="/build/assets/css/angular-material-v1.0.0-rc1/angular-material.css" />
    <link rel="stylesheet" href="/build/assets/bower_components/angular-loading-bar/build/loading-bar.min.css" />

    <!-- load triangular css -->
    <link rel="stylesheet" href="/build/assets/css/triangular.css?versionBLD=<?php echo $versionBLD; ?>" />

    <!-- load app css file -->
    <link rel="stylesheet" href="/build/assets/css/app.css?versionBLD=<?php echo $versionBLD; ?>">

    <script type="application/javascript">
        var versionBLD = '<?php echo $versionBLD; ?>';
        var licenseBLD = '{!! $licenseBLD !!}';
        var userFullName = '{!! $userFullName !!}';
    </script>

</head>

<body translate-cloak ng-class="bodyClasses">

    <!-- main app view  -->
    <div class="full-height" ui-view></div>

    <!-- load external bower js -->
    <script src="/build/assets/bower_components/jquery/dist/jquery.js?"></script>
    <script src="/build/assets/bower_components/angular/angular.js"></script>
    <script src="/build/assets/bower_components/angular-animate/angular-animate.js"></script>
    <script src="/build/assets/bower_components/Chart.js/Chart.js"></script>
    <script src="/build/assets/bower_components/angular-chart.js/dist/angular-chart.js"></script>
    <script src="/build/assets/bower_components/angular-cookies/angular-cookies.js"></script>
    <script src="/build/assets/bower_components/angular-digest-hud/digest-hud.js"></script>
    <script src="/build/assets/bower_components/angular-dragula/dist/angular-dragula.js"></script>
    <script src="/build/assets/bower_components/angular-google-chart/ng-google-chart.js"></script>
    <script src="/build/assets/bower_components/lodash/lodash.js"></script>
    <script src="/build/assets/bower_components/angular-google-maps/dist/angular-google-maps.js"></script>
    <script src="/build/assets/bower_components/highlightjs/highlight.pack.js"></script>
    <script src="/build/assets/bower_components/angular-highlightjs/build/angular-highlightjs.js"></script>
    <script src="/build/assets/bower_components/angular-linkify/angular-linkify.js"></script>
    <script src="/build/assets/bower_components/angular-local-storage/dist/angular-local-storage.js"></script>
    <script src="/build/assets/bower_components/angular-aria/angular-aria.js"></script>
    <script src="/build/assets/js/angular-material-v1.0.0-rc1/angular-material.js"></script>
    <script src="/build/assets/bower_components/angular-material-data-table/dist/md-data-table.js"></script>
    <script src="/build/assets/bower_components/angular-messages/angular-messages.js"></script>
    <script src="/build/assets/bower_components/moment/moment.js"></script>
    <script src="/build/assets/bower_components/angular-moment/angular-moment.js"></script>
    <script src="/build/assets/bower_components/angular-resource/angular-resource.js"></script>
    <script src="/build/assets/bower_components/angular-sanitize/angular-sanitize.js"></script>
    <script src="/build/assets/bower_components/angular-touch/angular-touch.js"></script>
    <script src="/build/assets/bower_components/angular-translate/angular-translate.js"></script>
    <script src="/build/assets/bower_components/angular-translate-loader-partial/angular-translate-loader-partial.js"></script>
    <script src="/build/assets/bower_components/angular-translate-storage-cookie/angular-translate-storage-cookie.js"></script>
    <script src="/build/assets/bower_components/angular-translate-storage-local/angular-translate-storage-local.js"></script>
    <script src="/build/assets/bower_components/fullcalendar/dist/fullcalendar.js"></script>
    <script src="/build/assets/bower_components/angular-ui-calendar/src/calendar.js"></script>
    <script src="/build/assets/bower_components/angular-ui-router/release/angular-ui-router.js"></script>
    <script src="/build/assets/bower_components/countUp.js/countUp.js"></script>
    <script src="/build/assets/bower_components/rangy/rangy-core.js"></script>
    <script src="/build/assets/bower_components/rangy/rangy-classapplier.js"></script>
    <script src="/build/assets/bower_components/rangy/rangy-highlighter.js"></script>
    <script src="/build/assets/bower_components/rangy/rangy-selectionsaverestore.js"></script>
    <script src="/build/assets/bower_components/rangy/rangy-serializer.js"></script>
    <script src="/build/assets/bower_components/rangy/rangy-textrange.js"></script>
    <script src="/build/assets/bower_components/textAngular/src/textAngular.js"></script>
    <script src="/build/assets/bower_components/textAngular/src/textAngular-sanitize.js"></script>
    <script src="/build/assets/bower_components/textAngular/src/textAngularSetup.js"></script>
    <script src="/build/assets/bower_components/satellizer/satellizer.min.js"></script>
    <script src="/build/assets/js/angular-dnd.min.js"></script>
    <script src="/build/assets/bower_components/ng-file-upload/ng-file-upload.min.js"></script>
    <script src="/build/assets/bower_components/angular-fcsa-number/src/fcsaNumber.min.js"></script>
    <script src="/build/assets/bower_components/angular-loading-bar/build/loading-bar.min.js"></script>
    <script src="/build/assets/bower_components/angular-hotkeys/build/hotkeys.min.js"></script>

    <!-- load triangular js -->
    <script src="/build/assets/js/triangular.js?versionBLD=<?php echo $versionBLD; ?>"></script>

    <!-- load main app module js -->
    <script src="/build/assets/js/app.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <!-- load config files -->
    <script src="/build/assets/js/config.js?versionBLD=<?php echo $versionBLD; ?>"></script>

    <!-- pharmacy module -->
    <script src="/app/pharmacy/pharmacy.module.js?versionBLD=<?php echo $versionBLD; ?>"></script>

    <!-- pharmacy crud services -->
    <script src="/app/pharmacy/services/pharmacy.common.service.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/services/pharmacy.form.service.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/services/pharmacy.user.service.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/services/pharmacy.product.service.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/services/pharmacy.inventory.service.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/services/pharmacy.markup-type.service.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/services/pharmacy.supplier.service.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/services/pharmacy.prescription.service.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/services/pharmacy.patient.service.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/services/pharmacy.prescriber.service.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/services/pharmacy.label-code.service.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/services/pharmacy.tax.service.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/services/pharmacy.log.service.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/services/pharmacy.order.service.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/services/pharmacy.transaction.service.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/services/pharmacy.fee.service.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/services/pharmacy.settings.service.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/services/pharmacy.import.service.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/services/pharmacy.customer.service.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/services/pharmacy.report.service.js?versionBLD=<?php echo $versionBLD; ?>"></script>

    <!-- pharmacy modules -->
    <script src="/app/pharmacy/authentication/authentication.module.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/authentication/signup/signup.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/authentication/profile/profile.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/authentication/login/login.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/authentication/logout/logout.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/authentication/lock/lock.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/authentication/forgot/forgot.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/authentication/authentication.config.js?versionBLD=<?php echo $versionBLD; ?>"></script>

    <script src="/app/pharmacy/menu/menu.module.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/menu/menu.config.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/menu/level.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>

    <script src="/app/pharmacy/dashboard/dashboard.module.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/dashboard/dashboard.config.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/dashboard/dashboard-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/dashboard/widgets/widget-weather.directive.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/dashboard/widgets/widget-twitter.directive.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/dashboard/widgets/widget-todo.directive.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/dashboard/widgets/widget-server.directive.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/dashboard/widgets/widget-load-data.directive.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/dashboard/widgets/widget-google-geochart.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/dashboard/widgets/widget-contacts.directive.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/dashboard/widgets/widget-chat.directive.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/dashboard/widgets/widget-chartjs-ticker.directive.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/dashboard/widgets/widget-chartjs-pie.directive.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/dashboard/widgets/widget-chartjs-line.directive.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/dashboard/widgets/widget-calendar.directive.js?versionBLD=<?php echo $versionBLD; ?>"></script>


    <script src="/app/pharmacy/administrator/administrator.module.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/administrator/controllers/users-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/administrator/controllers/tax-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/administrator/controllers/log-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/administrator/controllers/transaction-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/administrator/controllers/import-data-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/administrator/controllers/settings-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>

    <script src="/app/pharmacy/products/products.module.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/products/controllers/products-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/products/controllers/products-crud-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/products/controllers/markup-type-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>

    <script src="/app/pharmacy/inventory/inventory.module.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/inventory/controllers/inventory-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/inventory/controllers/inventory-edit-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/inventory/controllers/inventory-in-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/inventory/controllers/inventory-out-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/inventory/controllers/supplier-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>

    <script src="/app/pharmacy/prescription/prescription.module.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/prescription/controllers/prescription-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/prescription/controllers/prescription-crud-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/prescription/controllers/patient-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/prescription/controllers/prescriber-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/prescription/controllers/prescriber-crud-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/prescription/controllers/label-code-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/prescription/controllers/fee-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>

    <script src="/app/pharmacy/order/order.module.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/order/controllers/order-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/order/controllers/order-crud-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/order/controllers/customer-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/order/controllers/order-refund-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>

    <script src="/app/pharmacy/insurance/insurance.module.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/insurance/controllers/reconsiliation-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>

    <script src="/app/pharmacy/reports/reports.module.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/reports/controllers/cashier-report-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/reports/controllers/gct-report-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>
    <script src="/app/pharmacy/reports/controllers/purchase-report-page.controller.js?versionBLD=<?php echo $versionBLD; ?>"></script>

</body>
</html>