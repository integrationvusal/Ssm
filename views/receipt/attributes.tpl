{extends file="base.tpl"}

{block name="page-title"}
    :: Qəbzin atributları
{/block}

{block name="dashboard"}

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">

        <div class="row">
            <ol class="breadcrumb">
                <li><a href="{$app_url}"><span class="glyphicon glyphicon-home"></span></a></li>
                <li class="active">Qəbzin atributları</li>
            </ol>
        </div><!--/.row-->


        <div class="row col-no-gutter-container">

            <div class="col-md-2"></div>

            <div class="col-md-8">
                {if $attr}
                    <form class="form-horizontal" action="{$app_url}/receipt/attributes/create" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="user_id" value="{$user.id}">
                        <input type="hidden" name="subject_id" value="{$subject.id}">
                        <input type="hidden" name="attribute_id" value="{$attr.id}">
                        <div class="collapse in" id="collapseExampleKontragent">
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Logo:</label>
                                <div class="col-md-8">
                                    <img src="/{$attr.company_logo}" width="60" style="float: left; margin-right: 20px;">
                                    <input type="file" name="company_logo" class="btn btn-primary btn-file" accept="image/*">
                                    <input type="hidden" name="old_logo" value="{$attr.company_logo}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Təşkilatın adı:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="company_name" value="{$attr.company_name}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Təşkilatın VÖEN-i:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="company_voen" value="{$attr.company_voen}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Təşkilatın ünvanı:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="company_address" value="{$attr.company_address}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Digər məlumatlar(üst):</label>
                                <div class="col-md-8">
                                    <textarea class="form-control" name="other_top" id="other_top" cols="30" rows="10">{$attr.other_top}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Digər məlumatlar(alt):</label>
                                <div class="col-md-8">
                                    <textarea class="form-control" name="other_bottom" id="other_top" cols="30" rows="10">{$attr.other_bottom}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-2"></div>
                                <div class="col-md-10">
                                    <button type="submit" class="btn btn-danger form-control">ƏLAVƏ ET</span></button>
                                </div>
                            </div>
                        </div>
                    </form>
                {else}
                    <form class="form-horizontal" action="{$app_url}/receipt/attributes/create" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="user_id" value="{$user.id}">
                        <input type="hidden" name="subject_id" value="{$subject.id}">
                        <div class="collapse in" id="collapseExampleKontragent">
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Logo:</label>
                                <div class="col-md-8">
                                    <input type="file" name="company_logo" class="btn btn-primary btn-file" accept="image/*">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Təşkilatın adı:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="company_name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Təşkilatın VÖEN-i:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="company_voen">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Təşkilatın ünvanı:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="company_address">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Digər məlumatlar(üst):</label>
                                <div class="col-md-8">
                                    <textarea class="form-control" name="other_top" id="other_top" cols="30" rows="10">

                                    </textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Digər məlumatlar(alt):</label>
                                <div class="col-md-8">
                                    <textarea class="form-control" name="other_bottom" id="other_top" cols="30" rows="10">

                                    </textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-2"></div>
                                <div class="col-md-10">
                                    <button type="submit" class="btn btn-danger form-control">ƏLAVƏ ET</span></button>
                                </div>
                            </div>
                        </div>
                    </form>
                {/if}
            </div>

            <div class="col-md-2"></div>

        </div><!--/.row-->


    </div>	<!--/.main-->

{/block}