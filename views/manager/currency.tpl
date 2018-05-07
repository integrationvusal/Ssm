{extends file="base.tpl"}

{block name="page-title"}
    :: Valyutalar
{/block}

{block name="dashboard"}

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="{$app_url}"><span class="glyphicon glyphicon-home"></span></a></li>
                <li class="active"><a href="{$app_url}/manager/currency">Valyutalar</a></li>
            </ol>
        </div><!--/.row-->


        <div class="row col-no-gutter-container">
            <div class="col-xs-12 col-md-2"></div>
            <div class="col-xs-12 col-md-8 shops">
                {if isset($model)}
                    <form class="form-horizontal" method="post" action="{$app_url}/manager/currency/edit">
                        <input type="hidden" name="data_id" value="{$model.id}">
                        <div class="collapse in" id="collapseExampleShops">
                            <div class="alert alert-danger" role="alert">
                              <strong>Mərkəzi Bank:</strong> {$model.currency}
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Məzənnə:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="value" value="{$model.value}" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-warning f-right">YENİLƏ</button>
                        </div>

                    </form>
                {else}
                    <form class="form-horizontal" method="post" action="{$app_url}/manager/currency/create">

                        <button type="button" class="btn btn-success btn-lg btn-block" data-toggle="collapse" data-target="#collapseExampleShops" aria-expanded="false" aria-controls="collapseExample">YENİ</button>

                        <div class="collapse" id="collapseExampleShops">
                            <input type="hidden" name="data_id" value="{$model.id}">
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Valyuta:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Məzənnə:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="value" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-danger f-right">DAXİL ET</button>
                        </div>

                    </form>
                {/if}
            </div>

            <div class="col-xs-12 col-md-12 col-no-gutter">
                <h3>Valyutalar</h3>
                <div class="table-responsive">
                    <table class="table table-bordered" style="width:100%;">
                        <tr>
                            <th>#</th>
                            <th>Valyuta</th>
                            <th>Məzənnə</th>
                            <th></th>
                            <th></th>
                        </tr>
                        {assign var=i value=($page - 1)*$limit}
                        {foreach from=$datas item=data}
                            {assign var=i value=$i + 1}
                            <tr>
                                <th>{$i}</th>
                                <th>{$data.name}</th>
                                <th>{$data.value}</th>
                                <th><a href="{$app_url}/manager/currency/edit/{$data.id}" class="btn btn-warning">Redaktə et</a></th>
                                <th>
                                    <form action="{$app_url}/manager/currency/delete" method="post" id="currency_delete_{$data.id}">
                                        <input type="hidden" name="currency_id" value="{$data.id}">
                                        <input type="button" class="btn btn-danger delete-data" rel="{$data.id}" value="Sil">
                                    </form>
                                </th>
                            </tr>
                        {/foreach}
                    </table>
                </div>
            </div>

        </div><!--/.row-->


    </div>	<!--/.main-->
{/block}