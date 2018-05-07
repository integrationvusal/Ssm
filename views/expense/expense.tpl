{extends file="base.tpl"}

{block name="page-title"}
    :: Xərclər
{/block}

{block name="dashboard"}

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="{$app_url}"><span class="glyphicon glyphicon-home"></span></a></li>
                <li class="active"><a href="{$app_url}/expense">Xərclər</a></li>
            </ol>
        </div><!--/.row-->


        <div class="row col-no-gutter-container">
            <div class="col-xs-12 col-md-2">
            </div>

            <div class="col-xs-12 col-md-8 shops">

                <form class="form-horizontal" method="post" action="{$app_url}/expense/create">
                    {if $permissions.expense_create}
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
                {if isset($expense)}
                    <div class="col-xs-12 col-md-12 shops">

                        <form class="form-horizontal" method="post" action="{$app_url}/expense/edit">
                            <div class="collapse in">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Adı:</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="name" value="{$expense.name}" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Qiyməti:</label>
                                    <div class="col-sm-10">
                                        <input type="number" step="0.01" class="form-control" name="price" value="{$expense.price}"  required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Valyuta:</label>
                                    <div class="col-sm-10">
                                        <select name="currency" class="form-control" id="currency_id">
                                            <option accesskey="0" value="0">AZN</option>
                                            {foreach from=$currencies item=v}
                                                <option {if $expense.currency == $v.id}selected="selected" {/if} accesskey="{$v.value}" value="{$v.id}">{$v.name}</option>
                                            {/foreach}
                                        </select>
                                        <input type="hidden" name="currency_archive" id="currency_archive" value="0">
                                    </div>
                                </div>
                                <input type="hidden" name="user_id" value="{$user.id}">
                                <input type="hidden" name="subject_id" value="{$subject.id}">
                                <input type="hidden" name="id" value="{$expense.uid}">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Qeyd:</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" rows="3" name="description">{$expense.description}</textarea>
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
                <h3>Xərclər</h3>
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
                        {foreach from=$expenses item=exp}
                            {assign var="i" value=$i+1}
                            <tr>
                                <td>{$i}</td>
                                <td>{$exp.name}</td>
                                <td>{$exp.price}</td>
                                <td>{if empty($exp.currency)}AZN{else}{$exp.currency}{/if}</td>
                                <td>{$exp.description}</td>
                                <td>
                                    {if $permissions.expense_update}
                                        <a class="btn btn-warning" href="{$app_url}/expense/edit/{$exp.uid}">Redaktə et</a>
                                    {/if}
                                </td>
                                <td>
                                    {if $permissions.expense_delete}
                                        <form action="{$app_url}/expense/delete" method="post" id="delete-expense-{$exp.uid}">
                                            <input type="hidden" value="{$exp.uid}" name="expense_id">
                                            <input type="hidden" value="{$user.id}" name="user_id">
                                            <button type="button" class="btn btn-danger delete-expense" rel="{$exp.uid}">Sil</button>
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