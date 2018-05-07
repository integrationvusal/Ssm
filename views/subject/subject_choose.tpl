<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SSM</title>

    <link href="{$static_url}/css/bootstrap.min.css" rel="stylesheet">
    <link href="{$static_url}/datepicker3.css" rel="stylesheet">
    <link href="{$static_url}/css/styles.css" rel="stylesheet">

    <!--[if lt IE 9]>
    <link href="{$static_url}/css/rgba-fallback.css" rel="stylesheet">
    <script src="{$static_url}/js/html5shiv.js"></script>
    <script src="{$static_url}/js/respond.min.js"></script>
    <![endif]-->

</head>

<body>

{if isset($notices)}
    {foreach from=$notices item=notice}
    <div class="modal fade show_modal" id="show_modal_{$notice.id}" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">{$notice.title}</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12">
                            {$notice.content}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary form-control notice-expire" rel="{$notice.id}">Tanış oldum</button>
                </div>
            </div>
        </div>
    </div>
    {/foreach}
{/if}

<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#sidebar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="{$app_url}"><span>Small Stock Manager</span></a>
            <ul class="nav navbar-top-links navbar-right">
                <li style="display:inline-table"><a href="login.html"><span class="glyphicon glyphicon-user"></span> {if isset($user)}{$user.name|strtoupper}{else}User{/if}</a></li>
                <li style="display:inline-table"><a href="{$app_url}/signout"><span class="glyphicon glyphicon-off"></span> Çıxış</a></li>
            </ul>
        </div>
    </div><!-- /.container-fluid -->
</nav>

<!--div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">

    <ul class="nav menu">


    </ul>
</div><!--/.sidebar-->


<div class="main">

    <div class="row col-no-gutter-container col-md-4" style="margin: 20px auto; float: none; padding: 20px">
        <div class="users">
            <form class="form-horizontal" method="post" action="{$app_url}/subject/change">

                <input type="hidden" name="user_id" value="{$user.id}">
                <div class="form-group" style="padding: 10px;">
                    <label for="" class="control-label" style="margin-bottom: 10px">İşləyəcəyiniz obyekti seçin:</label>
                    <div>
                        <select class="form-control selectpicker" name="id" required>
                            {foreach from=$subjects item=subject}
								{assign var="selected" value=($subject.id==$current_subject)?'selected="selected"':null}
								<option {$selected} value="{$subject.id}">{$subject.name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-danger f-right">Seç</button>

            </form>

        </div>

    </div><!--/.row-->


</div>	<!--/.main-->




<script src="{$static_url}/js/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){

        $(".show_modal").modal('show');

    });
</script>
<script src="{$static_url}/js/bootstrap.min.js"></script>
<script src="{$static_url}/js/bootstrap-datepicker.js"></script>
<script src="{$static_url}/js/bootstrap-switch.min.js"></script>
<script src="{$static_url}/fancybox/jquery.fancybox.js"></script>
<script src="{$static_url}/js/custom.js"></script>
<script>
    var URL = '{$app_url}';
</script>
<script src="{$static_url}/js/ssm.js"></script>


</body>

</html>
