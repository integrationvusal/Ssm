{extends file="base.tpl"}

{block name="dashboard"}

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="#"><span class="glyphicon glyphicon-home"></span></a></li>
                <li class="active">İSTİFADƏÇİLƏRİN MODULLARI</li>
            </ol>
        </div><!--/.row-->


        <div class="row col-no-gutter-container">

            <div class="col-xs-12 col-md-12 col-no-gutter">
                <h3>İstifadəçi modulları</h3>
                <div class="table-responsive">
                    <form action="{$app_url}/manager/user/permission/{$user_id}" method="post">
                    <input type="hidden" name="user_id" value="{$user_id}">
                    <table class="table table-bordered col-md-12 col-xs-12">
                        <tr>
                            <th class="col-xs-1">#</th>
                            <th class="col-xs-5">Modulun adı</th>
                            <th class="col-xs-6"></th>
                        </tr>
                        <tr>
                            <th class="col-xs-1"></th>
                            <th class="col-xs-5"></th>
                            <th class="col-xs-6">
                                <a href="javascript:void(0)" class="btn btn-success select-all">Hamısını seç</a>
                                <a href="javascript:void(0)" class="btn btn-danger deselect-all">Hamısını sil</a>
                            </th>
                        </tr>
                        <tr style="display: none;">
                            <th>1</th>
                            <th>Obyektlər</th>
                            <th>
                                {foreach from=$subjects item=subject}
                                    <label for="subject_{$subject.id}" class="control-label col-md-8">{$subject.name}</label>
                                    <input type="checkbox" checked class="col-md-2" id="subject_{$subject.id}" name="subject[{$subject.id}]" value="1">
                                {/foreach}
                            </th>
                        </tr>
                        {assign var=i value=0}
                        {foreach from=$permissions_set item=permission key=k}
                        {if $k == "change_subject"}
                            <input type="hidden" class="form-control" name="change_subject" value="1">
                        {else}
                            {assign var=i value=$i + 1}
                            <tr>
                                <th>{$i}</th>
                                <th>{$permission}</th>
                                <th><input type="checkbox" class="touch form-control" name="{$k}" {if isset($user_permissions[$k]) && $user_permissions[$k] == 1}checked{/if} value="1"></th>
                            </tr>
                        {/if}
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
    <script>
        $(document).ready(function(event){
            $(".select-all").click(function(event) {
                $(".touch").prop("checked", true);
            });

            $(".deselect-all").click(function(event) {
                $(".touch").prop("checked", false);
            });
        });
    </script>
{/block}