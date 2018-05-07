{extends file="base.tpl"}

{block name="page-title"}
    :: Kategoriyalar
{/block}

{block name="dashboard"}

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="{$app_url}"><span class="glyphicon glyphicon-home"></span></a></li>
                <li class="active">İstifadəçilər</li>
            </ol>
        </div><!--/.row-->


        <div class="row col-no-gutter-container">
            <div class="col-xs-12 col-md-2">
            </div>

            <div class="col-xs-12 col-md-8 shops">
                {if isset($model)}
                    <form class="form-horizontal" method="post" action="{$app_url}/manager/user/edit">

                        <div class="collapse in" id="collapseExampleShops">
                            <input type="hidden" name="user_id" value="{$user.id}">
                            <input type="hidden" name="manager_id" value="{$model.id}">
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Adı:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="name" value="{$model.name}" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Email:</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" name="email" value="{$model.email}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Login:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="login" value="{$model.login}" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Şifrə:</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" name="password">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Doğum tarixi:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control datepicker" name="birthdate" value="{$model.birthdate}">
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
                    <form class="form-horizontal" method="post" action="{$app_url}/manager/user/create">

                        <button type="button" class="btn btn-success btn-lg btn-block" data-toggle="collapse" data-target="#collapseExampleShops" aria-expanded="false" aria-controls="collapseExample">YENİ</button>

                        <div class="collapse" id="collapseExampleShops">
                            <input type="hidden" name="user_id" value="{$user.id}">
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Adı:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Email:</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" name="email">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Login:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="login" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Şifrə:</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Doğum tarixi:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control datepicker" name="birthdate">
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
                <h3>İstifadəçilər</h3>
                <div class="table-responsive">
                    <table class="table table-bordered" style="width:100%;">
                        <tr>
                            <th>#</th>
                            <th>Adı</th>
                            <th>Login</th>
                            <th>Email</th>
                            <th>Doğum tarixi</th>
                            <th>Qeyd</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                        {assign var=i value=($page - 1)*$limit}
                        {foreach from=$siteUsers item=siteUser}
                            {assign var=i value=$i + 1}
                            <tr>
                                <th>{$i}</th>
                                <th>{$siteUser.name}</th>
                                <th>{$siteUser.login}</th>
                                <th>{$siteUser.email}</th>
                                <th>{$siteUser.birthdate|substr:0:10}</th>
                                <th>{$siteUser.description}</th>
                                <th><a href="{$app_url}/manager/user/permission/{$siteUser.id}" class="btn btn-default">Modullar</a></th>
                                <th><a href="{$app_url}/manager/subject/{$siteUser.id}" class="btn btn-info">Obyektlər</a></th>
                                <th><a href="{$app_url}/manager/user/edit/{$siteUser.id}" class="btn btn-warning">Redaktə et</a></th>
                                <th>
                                    <form action="{$app_url}/manager/user/delete" method="post" id="delete-manager-{$siteUser.id}">
                                        <input type="hidden" name="user_id" value="{$siteUser.id}">
                                        <button type="button" class="btn btn-danger delete-manager" rel="{$siteUser.id}">Sil</button>
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
                                <li class="active"><a href="{$app_url}/manager/{$p.page}">{$p.title}</a></li>
                            {else}
                                <li><a href="{$app_url}/manager/{$p.page}">{$p.title}</a></li>
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