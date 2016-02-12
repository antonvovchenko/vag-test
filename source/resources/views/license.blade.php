<!doctype html>
<html ng-app="app">
<head>
    <meta charset="utf-8">
    <title>BLD Pharmacy @yield('head-title')</title>
    <meta name="description" content="">
    <meta name="viewport" content="initial-scale=1, maximum-scale=1" />

    <!-- TODO: Remove no cache  -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <!-- load fonts   -->
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=RobotoDraft:100,100italic,300,300italic,400,500,700,900,400italic">

    <!-- load app css file -->
    <link rel="stylesheet" href="/build/assets/css/license.css">

</head>

<body>

<div class="container">
    <div class="row">

        <div class="col-md-4 col-md-offset-4">
            <br><br><br>
            <div class="panel panel-default">

                <div class="panel-heading">
                    <h3 class="panel-title">Please enter your license key</h3>
                </div>

                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="/">

                        @if (Session::has('error'))
                            <div class="alert alert-danger"><% Session::get('error') %></div>
                        @endif

                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" placeholder="License key" name="key" type="text" autofocus value="">
                            </div>
                            <!-- Change this to a button or input when using this as a form -->
                            <button class="btn btn-lg btn-success btn-block">Submit</button>
                        </fieldset>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>