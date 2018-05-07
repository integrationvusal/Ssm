{extends file="base.tpl"}

{block name="page-title"}
    :: Elanlar
{/block}

{block name="dashboard"}

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="{$app_url}"><span class="glyphicon glyphicon-home"></span></a></li>
                <li class="active"><a href="{$app_url}/manager/notice">Elanlar</a></li>
            </ol>
        </div><!--/.row-->


        <div class="row col-no-gutter-container">
            <div class="col-xs-12 col-md-2">
            </div>

            <div class="col-xs-12 col-md-8 shops">
                {if isset($model)}
                    <form class="form-horizontal" method="post" action="{$app_url}/manager/notice/edit">

                        <div class="collapse in" id="collapseExampleShops">
                            <input type="hidden" name="user_id" value="{$user.id}">
                            <input type="hidden" name="notice_id" value="{$model.id}">
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Başlıq:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="title" value="{$model.title}" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Məzmun:</label>
                                <div class="col-sm-10">
                                    <textarea name="content" id="content" cols="30" rows="5" class="form-control">{$model.content}</textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Başlama tarixi:</label>
                                <div class="col-sm-10">
                                    <input type="text" name="start_date" class="form-control datepicker" value="{$model.start_date}">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-warning f-right">YENİLƏ</button>
                        </div>

                    </form>
                {else}
                    <form class="form-horizontal" method="post" action="{$app_url}/manager/notice/create">

                        <button type="button" class="btn btn-success btn-lg btn-block" data-toggle="collapse" data-target="#collapseExampleShops" aria-expanded="false" aria-controls="collapseExample">YENİ</button>

                        <div class="collapse" id="collapseExampleShops">
                            <input type="hidden" name="user_id" value="{$user.id}">
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Başlıq:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="title" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Məzmun:</label>
                                <div class="col-sm-10">
                                    <textarea name="content" id="content" cols="30" rows="5" class="form-control"></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Başlama tarixi:</label>
                                <div class="col-sm-10">
                                    <input type="text" name="start_date" class="form-control datepicker" value="{$currentDate}">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-danger f-right">DAXİL ET</button>
                        </div>

                    </form>
                {/if}
            </div>

            <div class="col-xs-12 col-md-2">
            </div>

            <div class="col-xs-12 col-md-12 col-no-gutter">
                <h3>Elanlar</h3>
                <div class="table-responsive">
                    <table class="table table-bordered" style="width:100%;">
                        <tr>
                            <th>#</th>
                            <th>Başlıq</th>
                            <th>Məzmun</th>
                            <th>Başlama tarixi</th>
                            <th>Yerləşdirmə tarixi</th>
                            <th></th>
                            <th></th>
                        </tr>
                        {assign var=i value=($page - 1)*$limit}
                        {foreach from=$notices item=notice}
                            {assign var=i value=$i + 1}
                            <tr>
                                <th>{$i}</th>
                                <th>{$notice.title}</th>
                                <th>{$notice.content}</th>
                                <th>{$notice.start_date}</th>
                                <th>{$notice.create_date}</th>
                                <th><a href="{$app_url}/manager/notice/edit/{$notice.id}" class="btn btn-warning">Redaktə et</a></th>
                                <th>
                                    <form action="{$app_url}/manager/notice/delete" method="post" id="notice_delete_{$notice.id}">
                                        <input type="hidden" name="notice_id" value="{$notice.id}">
                                        <input type="button" class="btn btn-danger delete-notice" rel="{$notice.id}" value="Sil">
                                    </form>
                                </th>
                            </tr>
                        {/foreach}
                    </table>
                </div>
            </div>

            <div class="col-xs-12 col-md-4">
            </div>
            <div class="col-xs-12 col-md-4">
                <ul class="pagination">
                    {foreach from=$paginator item=p}
                        {if isset($p.disabled) && $p.disabled}
                            <li class="disabled"><a href="javascript:void(0)">{$p.title}</a></li>
                        {else}
                            {if isset($p.active) && $p.active}
                                <li class="active"><a href="{$app_url}/notice/{$p.page}">{$p.title}</a></li>
                            {else}
                                <li><a href="{$app_url}/notice/{$p.page}">{$p.title}</a></li>
                            {/if}
                        {/if}
                    {/foreach}
                </ul>
            </div>
            <div class="col-xs-12 col-md-4">
            </div>

        </div><!--/.row-->


    </div>	<!--/.main-->
{/block}