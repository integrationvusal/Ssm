{extends file="base.tpl"}

{block name="page-title"}
    :: {$messages.serv_net.title}
{/block}

{block name="dashboard"}
    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">

        <div class="panel panel-default">
            <div class="panel-heading"><span class="glyphicon glyphicon-envelope"></span> Əlaqə forması</div>
            <div class="panel-body">
                <form class="form-horizontal" action="{$app_url}/contact/process" method="post">
                    <input type="hidden" name="user_id" value="{$user.id}">
                    <input type="hidden" name="operator_id" value="{$user.operator.id}">
                    <fieldset>
                        <!-- Name input-->
                        <div class="form-group">
                            <label class="col-md-2 control-label" for="name">Ad:</label>
                            <div class="col-md-5">
                                <input id="name" name="name" type="text" class="form-control" required>
                            </div>
                        </div>

                        <!-- Email input-->
                        <div class="form-group">
                            <label class="col-md-2 control-label" for="email">E-mail:</label>
                            <div class="col-md-5">
                                <input id="email" name="email" type="email" class="form-control" required>
                            </div>
                        </div>

                        <!-- Message body -->
                        <div class="form-group">
                            <label class="col-md-2 control-label" for="message">Mesaj:</label>
                            <div class="col-md-7">
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                        </div>

                        <!-- Form actions -->
                        <div class="form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-7 widget-right">
                                <button type="submit" class="btn btn-primary btn-md pull-left">Göndər</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>

    </div>	<!--/.main-->

{/block}