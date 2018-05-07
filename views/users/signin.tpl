<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SSM</title>

    <link href="{$static_url}/css/bootstrap.min.css" rel="stylesheet">
    <link href="{$static_url}/datepicker3.css" rel="stylesheet">
    <link href="{$static_url}/css/styles.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic' rel='stylesheet' type='text/css'>

    <!--[if lt IE 9]>
    <link href="{$static_url}/css/rgba-fallback.css" rel="stylesheet">
    <script src="{$static_url}/js/html5shiv.js"></script>
    <script src="{$static_url}/js/respond.min.js"></script>
    <![endif]-->

</head>

<body>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#sidebar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><span>Small Stock Manager</span> <span style="color:white">by Integration</span></a>
            <ul class="nav navbar-top-links navbar-right">
            </ul>
        </div>
    </div><!-- /.container-fluid -->
</nav>

<!--div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">

    <ul class="nav menu">


    </ul>
</div><!--/.sidebar-->


<div class="main">

    <div class="row col-no-gutter-container col-md-6" style="margin: 20px auto; float: none">
        <div class="users">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">{if ! isset($status)}Daxil ol{else}{$status}{/if}</div>
                <div class="panel-body">
                    <form role="form" action="{$app_url}/signin" method="post">
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" placeholder="E-mail" name="login" type="login" autofocus="">
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="Password" name="password" type="password" value="">
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input name="remember" type="checkbox" value="Remember Me">Yadda saxla
                                </label>
                            </div>
                            <button class="btn btn-primary">Daxil ol</button>
                        </fieldset>
                    </form>
                </div>
            </div>

        </div>

    </div><!--/.row-->


</div>	<!--/.main-->




<script src="{$static_url}/js/jquery-1.11.1.min.js"></script>
<script src="{$static_url}/js/bootstrap.min.js"></script>
<script src="{$static_url}/js/chart.min.js"></script>
<script src="{$static_url}/js/chart-data.js"></script>
<script src="{$static_url}/js/easypiechart.js"></script>
<script src="{$static_url}/js/easypiechart-data.js"></script>
<script src="{$static_url}/js/bootstrap-datepicker.js"></script>
<script src="{$static_url}/js/custom.js"></script>
<script src="{$static_url}/js/ssm.js"></script>

<script>
    window.onload = function(){
        var chart1 = document.getElementById("line-chart").getContext("2d");
        window.myLine = new Chart(chart1).Line(lineChartData, {
            responsive : true,
            scaleLineColor: "rgba(255,255,255,.2)",
            scaleGridLineColor: "rgba(255,255,255,.05)",
            scaleFontColor: "#ffffff"
        });
        var chart2 = document.getElementById("bar-chart").getContext("2d");
        window.myBar = new Chart(chart2).Bar(barChartData, {
            responsive : true,
            scaleLineColor: "rgba(255,255,255,.2)",
            scaleGridLineColor: "rgba(255,255,255,.05)",
            scaleFontColor: "#ffffff"
        });
        var chart5 = document.getElementById("radar-chart").getContext("2d");
        window.myRadarChart = new Chart(chart5).Radar(radarData, {
            responsive : true,
            scaleLineColor: "rgba(255,255,255,.05)",
            angleLineColor : "rgba(255,255,255,.2)"
        });

    };
</script>
</body>

</html>
