{extends file="base.tpl"}

{block name="page-title"}
    :: Xidmətlər
{/block}

{block name="dashboard"}

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="{$app_url}"><span class="glyphicon glyphicon-home"></span></a></li>
                <li class="active"><a href="{$app_url}/service">Xidmətlər</a></li>
            </ol>
        </div><!--/.row-->


        <div class="row col-no-gutter-container">
            <div class="col-xs-12 col-md-2">
            </div>

            <div class="col-xs-12 col-md-8 shops">

                <form class="form-horizontal" method="post" action="{$app_url}/service/create">
                    {if $permissions.service_create}
                        <button type="button" class="btn btn-success btn-lg btn-block" data-toggle="collapse" data-target="#collapseExampleShops" aria-expanded="false" aria-controls="collapseExample">YENİ</button>
                    {/if}

                    <div class="collapse" id="collapseExampleShops">
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Adı:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Qiyməti:</label>
                            <div class="col-sm-10">
                                <input type="number" step="0.01" class="form-control" name="price" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Valyuta:</label>
                            <div class="col-sm-10">
                                <select name="currency" class="form-control" id="currency_id">
                                    <option accesskey="0" value="0">AZN</option>
                                    {foreach from=$currencies item=v}
                                        <option accesskey="{$v.value}" value="{$v.id}">{$v.name}</option>
                                    {/foreach}
                                </select>
                                <input type="hidden" name="currency_archive" id="currency_archive" value="0">
                            </div>
                        </div>
                        <input type="hidden" name="user_id" value="{$user.id}">
                        <input type="hidden" name="subject_id" value="{$subject.id}">
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Qeyd:</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" rows="3" name="description"></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-danger f-right">ƏLAVƏ ET</button>
                    </div>

                </form>
                {if isset($service)}
                    <div class="col-xs-12 col-md-12 shops">

                        <form class="form-horizontal" method="post" action="{$app_url}/service/edit">
                            <div class="collapse in">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Adı:</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="name" value="{$service.name}" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Qiyməti:</label>
                                    <div class="col-sm-10">
                                        <input type="number" step="0.01" class="form-control" name="price" value="{$service.price}"  required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Valyuta:</label>
                                    <div class="col-sm-10">
                                        <select name="currency" class="form-control" id="currency_id">
                                            <option accesskey="0" value="0">AZN</option>
                                            {foreach from=$currencies item=v}
                                                <option {if $service.currency_id == $v.id}selected="selected" {/if} accesskey="{$v.value}" value="{$v.id}">{$v.name}</option>
                                            {/foreach}
                                        </select>
                                        <input type="hidden" name="currency_archive" id="currency_archive" value="0">
                                    </div>
                                </div>
                                <input type="hidden" name="user_id" value="{$user.id}">
                                <input type="hidden" name="subject_id" value="{$subject.id}">
                                <input type="hidden" name="id" value="{$service.id}">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Qeyd:</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" rows="3" name="description">{$service.description}</textarea>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-warning f-right">YENİLƏ</button>
                            </div>

                        </form>

                    </div>
                {/if}
            </div>

            <div class="col-xs-12 col-md-2">
            </div>

            <div class="col-xs-12 col-md-12 col-no-gutter">
                <h3>XİDMƏTLƏR</h3>
                <div class="table-responsive">
                    <table class="table table-bordered" style="width:100%;">
                        <tr>
                            <th>#</th>
                            <th>Adı</th>
                            <th>Qiyməti</th>
                            <th>Valyuta</th>
                            <th>Qeyd</th>
                            <th></th>
                            <th></th>
                        </tr>
                        {assign var="i" value=0}
                        {foreach from=$services item=ser}
                            {assign var="i" value=$i+1}
                            <tr>
                                <td>{$i}</td>
                                <td>{$ser.name}</td>
                                <td>{$ser.price}</td>
                                <td>{if $ser.currency}{$ser.currency}{else}AZN{/if}</td>
                                <td>{$ser.description}</td>
                                <td>
                                    {if $permissions.service_update}
                                        <a class="btn btn-warning" href="{$app_url}/service/edit/{$ser.id}">Redaktə et</a>
                                    {/if}
                                </td>
                                <td>
                                    {if $permissions.service_delete}
                                        <form action="{$app_url}/service/delete" method="post" id="delete-service-{$ser.id}">
                                            <input type="hidden" value="{$ser.id}" name="service_id">
                                            <input type="hidden" value="{$user.id}" name="user_id">
                                            <button type="button" class="btn btn-danger delete-service" rel="{$ser.id}">Sil</button>
                                        </form>
                                    {/if}
                                </td>
                            </tr>
                        {/foreach}
                    </table>
                </div>
            </div>

        </div><!--/.row-->


    </div>	<!--/.main-->
{/block}