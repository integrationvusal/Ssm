{extends file="base.tpl"}

{block name="page-title"}
    :: Infografika
{/block}

{block name="dashboard"}
    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="{$app_url}"><span class="glyphicon glyphicon-home"></span></a></li>
                <li class="active">İnfoqrafika</li>
            </ol>
        </div><!--/.row-->

        <div class="row col-no-gutter-container">
            <div class="col-xs-12 col-md-6 col-lg-3 col-no-gutter">
                <div class="panel panel-blue panel-widget">
                    <div class="row no-padding">
                        <div class="col-sm-3 col-lg-5 widget-left">
                            <em class="glyphicon glyphicon-shopping-cart glyphicon-l"></em>
                        </div>
                        <div class="col-sm-9 col-lg-7 widget-right">
                            <div class="large">{$stat.total_sold_goods_count}</div>
                            <div class="text-muted">Ay ərzində mal</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-6 col-lg-3 col-no-gutter">
                <div class="panel panel-orange panel-widget">
                    <div class="row no-padding">
                        <div class="col-sm-3 col-lg-5 widget-left">
                            <em class="glyphicon glyphicon-user glyphicon-l"></em>
                        </div>
                        <div class="col-sm-9 col-lg-7 widget-right">
                            <div class="large">{$stat.total_sell}</div>
                            <div class="text-muted">Ay ərzində alıcı</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-6 col-lg-3 col-no-gutter">
                <div class="panel panel-teal panel-widget">
                    <div class="row no-padding">
                        <div class="col-sm-3 col-lg-5 widget-left">
                            <!--em class="glyphicon glyphicon-usd glyphicon-l"></em-->
                            <img src="{$static_url}/img/azn-manat.png" width="60" alt="">
                        </div>
                        <div class="col-sm-9 col-lg-7 widget-right">
                            <div class="large">{$stat.total_sell_amount}</div>
                            <div class="text-muted">Ay ərzində satış</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-6 col-lg-3 col-no-gutter">
                <div class="panel panel-red panel-widget">
                    <div class="row no-padding">
                        <div class="col-sm-3 col-lg-5 widget-left">
                            <em class="glyphicon glyphicon-stats glyphicon-l"></em>
                        </div>
                        <div class="col-sm-9 col-lg-7 widget-right">
                            <div class="large">{$stat.difference}%</div>
                            <div class="text-muted">Artım</div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!--/.row-->

        <div class="row col-no-gutter-container row-margin-top">
            <div class="col-md-12 col-no-gutter">
                <div class="panel panel-default">
                    <div class="panel-heading" id="lineChartLegend">
                    </div>
                    <div class="panel-body">
                        <div class="canvas-wrapper">
                            <canvas class="main-chart" id="line-chart" height="200" width="600"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div><!--/.row-->

        <div class="row row-no-gutter col-no-gutter-container">
            <div class="col-md-12 col-no-gutter">
                <div class="panel panel-default">
                    <div class="panel-heading"><span id="barChartLegend"></span></div>
                    <div class="panel-body">
                        <div class="canvas-wrapper">
                            <canvas class="chart" id="bar-chart" ></canvas>
                        </div>
                    </div>
                </div>
            </div><!--/.col-->
        </div><!--/.row-->

        <div class="row col-no-gutter-container">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Ən çox satılan mallar</div>
                    <div class="panel-body table-responsive">
                        <table data-toggle="table" data-url="{$app_url}/statistics/gettop" id="table-style" data-method="post" data-row-style="rowStyle">
                            <thead>
                                <tr>
                                    <th data-field="image" data-align="center" >Şəkil</th>
                                    <th data-field="short_info" data-align="right" >Malın adı</th>
                                    <th data-field="total_count" data-align="right" >Ümumi satış sayı</th>
                                    <th data-field="total_amount" data-align="right" >Ümumi satış məbləği</th>
                                </tr>
                            </thead>
                        </table>
                        <script>
                            $(function () {
                                $('#hover, #striped, #condensed').click(function () {
                                    var classes = 'table';

                                    if ($('#hover').prop('checked')) {
                                        classes += ' table-hover';
                                    }
                                    if ($('#condensed').prop('checked')) {
                                        classes += ' table-condensed';
                                    }
                                    $('#table-style').bootstrapTable('destroy')
                                            .bootstrapTable({
                                                classes: classes,
                                                striped: $('#striped').prop('checked')
                                            });
                                });
                            });

                            function rowStyle(row, index) {
                                var classes = ['highlighted', 'green', 'blue', 'orange', 'red'];

                                if (index % 2 === 0 && index / 2 < classes.length) {
                                    return {
                                        classes: classes[index / 2]
                                    };
                                }
                                return {};
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div><!--/.row-->
    </div>	<!--/.main-->

    <script>
        var randomScalingFactor = function(){ return Math.round(Math.random()*1000)};
        var lineChartData = {
            labels : ["Yanvar","Fevral","Mart","Aprel","May","İyun","İyul","Avqust","Sentyabr","Oktyabr","Noyabr","Dekabr"],
            datasets : [
                {
                    label: "Ümumi satış",
                    fillColor : "rgba(220,220,220,0.2)",
                    strokeColor : "rgba(220,220,220,1)",
                    pointColor : "rgba(220,220,220,1)",
                    pointStrokeColor : "#fff",
                    pointHighlightFill : "#fff",
                    pointHighlightStroke : "rgba(220,220,220,1)",
                    data : [{foreach from=$stat.total_sell_monthly_data item=data key=k}{if ($k > 0) && ($k < 12)}'{$data}',{else}{if $k == 12}'{$data}'{/if}{/if}{/foreach}]
                },
                {
                    label: "Satılmış malların sayı",
                    fillColor : "rgba(48, 164, 255, 0.2)",
                    strokeColor : "rgba(48, 164, 255, 1)",
                    pointColor : "rgba(48, 164, 255, 1)",
                    pointStrokeColor : "#fff",
                    pointHighlightFill : "#fff",
                    pointHighlightStroke : "rgba(48, 164, 255, 1)",
                    data : [{foreach from=$stat.sold_goods_count_monthly item=data key=k}{if ($k > 0) && ($k < 12)}'{$data}',{else}{if $k == 12}'{$data}'{/if}{/if}{/foreach}]

                },
                /*
                {
                    label: "Müştərilərin borcu",
                    fillColor : "rgba(113, 191, 85, 0.2)",
                    strokeColor : "rgba(113, 191, 85, 0.2)",
                    pointColor : "rgba(113, 191, 85, 1)",
                    pointStrokeColor : "#fff",
                    pointHighlightFill : "#fff",
                    pointHighlightStroke : "rgba(220,220,220,1)",
                    data : [randomScalingFactor(),]
                }
                */
            ]

        }

        var barChartData = {
            labels : ["Yanvar","Fevral","Mart","Aprel","May","İyun","İyul","Avqust","Sentyabr","Oktyabr","Noyabr","Dekabr"],
            datasets : [
                {
                    label: "Mədaxil",
                    fillColor : "rgba(220,220,220,0.5)",
                    strokeColor : "rgba(220,220,220,0.8)",
                    highlightFill: "rgba(220,220,220,0.75)",
                    highlightStroke: "rgba(220,220,220,1)",
                    data : [{foreach from=$stat.cashbox_monthly_data.income item=data key=k}{if ($k > 0) && ($k < 12)}'{$data}',{else}{if $k == 12}'{$data}'{/if}{/if}{/foreach}]
                },
                {
                    label: "Məxaric",
                    fillColor : "rgba(48, 164, 255, 0.2)",
                    strokeColor : "rgba(48, 164, 255, 1)",
                    highlightFill : "rgba(48, 164, 255, 1)",
                    highlightStroke : "rgba(48, 164, 255, 1)",
                    data : [{foreach from=$stat.cashbox_monthly_data.outgoing item=data key=k}{if ($k > 0) && ($k < 12)}'{$data}',{else}{if $k == 12}'{$data}'{/if}{/if}{/foreach}]
                }
            ]
        }

        window.onload = function(){
            var chart1 = document.getElementById("line-chart").getContext("2d");
            window.myLine = new Chart(chart1).Line(lineChartData, {
                responsive : true,
                scaleLineColor: "rgba(255,255,255,.2)",
                scaleGridLineColor: "rgba(255,255,255,.05)",
                scaleFontColor: "#ffffff",
                legendTemplate : '<ul class="legend">'
                +'<% for (var i=0; i<datasets.length; i++) { %>'
                +'<li>'
                +'<span style=\"background-color:<%=datasets[i].fillColor%>\"></span>'
                +'<% if (datasets[i].label) { %><%= datasets[i].label %><% } %>'
                +'</li>'
                +'<% } %>'
                +'</ul>'

            });
            var legend = window.myLine.generateLegend();
            $("#lineChartLegend").html(legend);

            var chart2 = document.getElementById("bar-chart").getContext("2d");
            window.myBar = new Chart(chart2).Bar(barChartData, {
                responsive : true,
                scaleLineColor: "rgba(255,255,255,.2)",
                scaleGridLineColor: "rgba(255,255,255,.05)",
                scaleFontColor: "#ffffff",
                legendTemplate : '<ul class="legend">'
                +'<% for (var i=0; i<datasets.length; i++) { %>'
                +'<li>'
                +'<span style=\"background-color:<%=datasets[i].fillColor%>\"></span>'
                +'<% if (datasets[i].label) { %><%= datasets[i].label %><% } %>'
                +'</li>'
                +'<% } %>'
                +'</ul>'
            });
            var barLegend = window.myBar.generateLegend();
            $("#barChartLegend").html(barLegend);

        };
    </script>
{/block}