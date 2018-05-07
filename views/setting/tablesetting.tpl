{extends file="base.tpl"}

{block name="page-title"}
    :: CƏDVƏL
{/block}

{block name="dashboard"}

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="#"><span class="glyphicon glyphicon-home"></span></a></li>
                <li class="active">CƏDVƏLİN XÜSUSİYYƏTLƏRİ</li>
            </ol>
        </div><!--/.row-->


        <div class="row col-no-gutter-container">

            <div class="col-xs-12 col-md-12 col-no-gutter">
                <h3>CƏDVƏLİN XÜSUSİYYƏTLƏRİ</h3>
                <div class="table-responsive">
                    <form action="{$app_url}/settings/table/create" method="post">
                        <input type="hidden" name="user_id" value="{$user.id}">
                        <input type="hidden" name="subject_id" value="{$subject.id}">
                        <input type="hidden" name="view_type" value="table">
                        <table class="table table-bordered col-md-12 col-xs-12">
                            <tr>
                                <th class="col-xs-2">#</th>
                                <th class="col-xs-6">Atributun adı</th>
                                <th class="col-xs-4"></th>
                            </tr>
                            {assign var=i value=0}
                            {foreach from=$attrs item=attr key=k}
                                {assign var=i value=$i + 1}
                                <tr>
                                    <th>{$i}</th>
                                    <th>{$attr.title}</th>
                                    <th><input type="checkbox" class="form-control" name="set[{$k}]" {if $attr.val == 1}checked{/if} value="1"></th>
                                </tr>
                            {/foreach}
                            <tr>
                                <td colspan="3">
                                    <button type="submit" class="btn btn-danger f-right">TƏSDİQLƏ</button>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>

        </div><!--/.row-->

    </div>	<!--/.main-->

{/block}