{extends file="base.tpl"}

{block name="page-title"}
    :: Kategoriyalar
{/block}

{block name="dashboard"}

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="{$app_url}"><span class="glyphicon glyphicon-home"></span></a></li>
                <li class="active">{$model.title} kategoriyaları</li>
            </ol>
        </div><!--/.row-->


        <div class="row col-no-gutter-container">
            <div class="col-xs-12 col-md-2">
            </div>

            <div class="col-xs-12 col-md-8 shops">

                <form class="form-horizontal" method="post" action="{$app_url}/category/create">
                    {if $permissions.category_create}
                    <button type="button" class="btn btn-success btn-lg btn-block" data-toggle="collapse" data-target="#collapseExampleShops" aria-expanded="false" aria-controls="collapseExample">YENİ</button>
                    {/if}

                    <div class="collapse" id="collapseExampleShops">
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Adı:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" placeholder="Kategoriyanın adı" required>
                            </div>
                        </div>
                        <input type="hidden" name="user_id" value="{$user.id}">
                        <input type="hidden" name="goods_type" value="{$subject.goods_type}">
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Qeyd:</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" rows="3" name="description"></textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-danger f-right">ƏLAVƏ ET</button>
                    </div>

                </form>
                {if isset($category)}
                    <div class="col-xs-12 col-md-12 shops">

                        <form class="form-horizontal" method="post" action="{$app_url}/category/edit">

                            <div id="collapseExampleShops">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Adı:</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{$category.name}" name="name" placeholder="Kategoriyanın adı" required>
                                    </div>
                                </div>
                                <input type="hidden" name="user_id" value="{$user.id}">
                                <input type="hidden" name="goods_type" value="{$subject.goods_type}">
                                <input type="hidden" name="id" value="{$category.id}">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Qeyd:</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" rows="3" name="description">{$category.description}</textarea>
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
                <h3>{$model.title} kategoriyaları</h3>
                <div class="table-responsive">
                    <table class="table table-bordered" style="width:100%;">
                        <tr>
                            <th>#</th>
                            <th>Adı</th>
                            <th>Qeyd</th>
                            <th></th>
                            <th></th>
                        </tr>
                        {assign var="i" value=0}
                        {foreach from=$categories item=category}
                        {assign var="i" value=$i+1}
                            <tr>
                                <td>{$i}</td>
                                <td>{$category.name}</td>
                                <td>{$category.description}</td>
                                <td>
                                    {if $permissions.category_update}
                                    <a class="btn btn-warning" href="{$app_url}/category/edit/{$category.id}">Redaktə et</a>
                                    {/if}
                                </td>
                                <td>
                                    {if $permissions.category_delete}
                                    <form action="{$app_url}/category/delete" method="post">
                                        <input type="hidden" value="{$category.id}" name="category_id">
                                        <input type="hidden" value="{$user.id}" name="user_id">
                                        <input type="hidden" value="{$goods_type}" name="goods_type">
                                        <button type="button" class="btn btn-danger delete-category">Sil</button>
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