{extends file="base.tpl"}

{block name="page-title"}
    :: Kategoriyalar
{/block}

{block name="dashboard"}

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="{$app_url}"><span class="glyphicon glyphicon-home"></span></a></li>
                <li><a href="{$app_url}/manager">İstifadəçilər</a></li>
                <li class="active">"{$manager.name}" obyektlər</li>
            </ol>
        </div><!--/.row-->


        <div class="row col-no-gutter-container">
            <div class="col-xs-12 col-md-2">
            </div>

            <div class="col-xs-12 col-md-8 shops">
                {if isset($model)}
                    <form class="form-horizontal" method="post" action="{$app_url}/manager/subject/edit">

                        <div class="collapse in" id="collapseExampleShops">
                            <input type="hidden" name="manager_id" value="{$manager.id}">
                            <input type="hidden" name="subject_id" value="{$model.id}">
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Adı:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="name" value="{$model.name}" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Növü:</label>
                                <div class="col-sm-10">
                                    <select name="type" id="type" class="form-control">
                                        {foreach from=$subjectTypes item=subjectType key=k}
                                            {if $k == $model.type}
                                                <option selected value="{$k}">{$subjectType}</option>
                                            {else}
                                                <option value="{$k}">{$subjectType}</option>
                                            {/if}
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Mal növü:</label>
                                <div class="col-sm-10">
                                    <select name="goods_type" id="goods_type" class="form-control">
                                        {foreach from=$goodsTypes item=goodsType key=k}
                                            {if $k == $model.goods_type}
                                                <option selected value="{$k}">{$goodsType}</option>
                                            {else}
                                                <option value="{$k}">{$goodsType}</option>
                                            {/if}
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Qeyd:</label>
                                <div class="col-sm-10">
                                    <textarea name="description" id="description" cols="30" class="form-control" rows="5">{$model.description}</textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-warning f-right">YENİLƏ</button>
                        </div>

                    </form>
                {else}
                    <form class="form-horizontal" method="post" action="{$app_url}/manager/subject/create">

                        <button type="button" class="btn btn-success btn-lg btn-block" data-toggle="collapse" data-target="#collapseExampleShops" aria-expanded="false" aria-controls="collapseExample">YENİ</button>

                        <div class="collapse" id="collapseExampleShops">
                            <input type="hidden" name="manager_id" value="{$manager.id}">
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Adı:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Növü:</label>
                                <div class="col-sm-10">
                                    <select name="type" id="type" class="form-control">
                                        {foreach from=$subjectTypes item=subjectType key=k}
                                            <option value="{$k}">{$subjectType}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Mal növü:</label>
                                <div class="col-sm-10">
                                    <select name="goods_type" id="goods_type" class="form-control">
                                        {foreach from=$goodsTypes item=goodsType key=k}
                                            <option value="{$k}">{$goodsType}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Qeyd:</label>
                                <div class="col-sm-10">
                                    <textarea name="description" id="description" cols="30" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-danger f-right">ƏLAVƏ ET</button>
                        </div>

                    </form>
                {/if}
            </div>

            <div class="col-xs-12 col-md-2">
            </div>

            <div class="col-xs-12 col-md-12 col-no-gutter">
                <h3>"{$manager.name}" obyektlər</h3>
                <div class="table-responsive">
                    <table class="table table-bordered" style="width:100%;">
                        <tr>
                            <th>#</th>
                            <th>Adı</th>
                            <th>Növü</th>
                            <th>Mal növü</th>
                            <th>Qeyd</th>
                            <th></th>
                            <th></th>
                        </tr>
                        {assign var=i value=0}
                        {foreach from=$subjects item=sub}
                            {assign var=i value=$i + 1}
                            <tr>
                                <th>{$i}</th>
                                <th>{$sub.name}</th>
                                <th>{$subjectTypes[$sub.type]}</th>
                                <th>{$goodsTypes[$sub.goods_type]}</th>
                                <th>{$sub.description}</th>
                                <th><a href="{$app_url}/manager/subject/{$manager.id}/edit/{$sub.id}" class="btn btn-warning">Redaktə et</a></th>
                                <th>
                                    <form action="{$app_url}/manager/subject/delete">
                                        <input type="hidden" name="subject_id" value="{$sub.id}">
                                        <input type="button" class="btn btn-danger" value="Sil">
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