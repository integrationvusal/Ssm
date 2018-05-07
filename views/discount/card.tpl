{extends file="base.tpl"}

{block name="page-title"}
    :: BONUS KARTLARI
{/block}

{block name="dashboard"}
    <select id="discount_rules" disabled style="display: none">
        {foreach from=$discount_rules item=dr}
            <option value="{$dr.id}">{$dr.rule_name}</option>
        {/foreach}
    </select>
    <select id="bonus_rules" disabled style="display: none">
        {foreach from=$bonus_rules item=br}
            <option value="{$br.id}">{$br.rule_name}</option>
        {/foreach}
    </select>

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">

        <div class="row">
            <ol class="breadcrumb">
                <li><a href="{$app_url}"><span class="glyphicon glyphicon-home"></span></a></li>
                <li class="active">BONUS KARTLARI</li>
            </ol>
        </div><!--/.row-->


        <div class="row col-no-gutter-container" style="float: left;">
            <div class="col-xs-12 col-md-2">
            </div>
            <div class="col-xs-12 col-md-8 kontragent">

                {if $card}
                    <form class="form-horizontal" action="{$app_url}/discount/card/update" method="post">
                        {if $permissions.discount_card}
                            <button type="button" class="btn btn-success btn-lg btn-block" data-toggle="collapse" data-target="#collapseExampleKontragent" aria-expanded="false" aria-controls="collapseExample">REDAKTƏ ET</button>
                        {/if}
                        <input type="hidden" name="user_id" value="{$user.id}">
                        <input type="hidden" name="subject_id" value="{$subject.id}">
                        <input type="hidden" name="card_id" value="{$card.id}">
                        <div class="collapse in" id="collapseExampleKontragent">
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Kartın nömrəsi:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" value="{$card.card_number}" disabled>
                                    <input type="hidden" name="card_number" value="{$card.card_number}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Bonus kartının növü:</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="card_type" required>
                                        {foreach from=$card_types key=k item=card_type}
                                            <option value="{$k}" {if $card.card_type == $k}selected{/if}>{$card_type}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Bonus hesablanma qaydası:</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="rule_id" required>
                                        {foreach from=$bonus_rules item=br}
                                            <option value="{$br.id}" {if $card.rule_id == $br.id}selected{/if}>{$br.rule_name}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Müştərinin adı:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="client_name" value="{$card.client_name}" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Müştərinin soyadı:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="client_surname" value="{$card.client_surname}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Müştərinin ata adı:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="client_patronymic" value="{$card.client_patronymic}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Müştərinin telefon nömrəsi:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="client_phone" value="{$card.client_phone}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Diqər məlumatlar:</label>
                                <div class="col-md-8">
                                    <textarea class="form-control" name="client_description" rows="5" cols="30">{$card.client_description}</textarea>
                                </div>
                            </div>
                            <div class="form-group discount_prop">
                                <label for="" class="col-md-4 control-label">Endirim:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="discount" value="{$card.discount}">
                                </div>
                            </div>
                            <div class="form-group bonus_prop">
                                <label for="" class="col-md-4 control-label">Bonus:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="bonus" value="{$card.bonus}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Qalıq:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="remaining_amount" value="{$card.remaining_amount}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Kartın bitmə tarixi:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control datepicker" name="expire_at" value="{$card.expire_at|substr:0:10}" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-2"></div>
                                <div class="col-md-10">
                                    <button type="submit" class="btn btn-warning form-control">YENILƏ</span></button>
                                </div>
                            </div>
                        </div>
                    </form>
                {else}
                    <form class="form-horizontal" action="{$app_url}/discount/card/create" method="post">
                        {if $permissions.discount_card}
                            <button type="button" class="btn btn-success btn-lg btn-block" data-toggle="collapse" data-target="#collapseExampleKontragent" aria-expanded="false" aria-controls="collapseExample">YENİ</button>
                        {/if}
                        <input type="hidden" name="user_id" value="{$user.id}">
                        <input type="hidden" name="subject_id" value="{$subject.id}">
                        <div class="collapse" id="collapseExampleKontragent">
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Kartın nömrəsi:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="card_number" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Bonus kartının növü:</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="card_type" required>
                                        {foreach from=$card_types key=k item=card_type}
                                            <option value="{$k}">{$card_type}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Bonus hesablanma qaydası:</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="rule_id" required>
                                        {foreach from=$bonus_rules item=br}
                                            <option value="{$br.id}">{$br.rule_name}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Müştərinin adı:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="client_name" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Müştərinin soyadı:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="client_surname">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Müştərinin ata adı:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="client_patronymic">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Müştərinin telefon nömrəsi:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="client_phone">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Diqər məlumatlar:</label>
                                <div class="col-md-8">
                                    <textarea class="form-control" name="client_description" rows="5" cols="30"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Kartın bitmə tarixi:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control datepicker" name="expire_at" required>
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


            <div class="col-xs-12 col-md-2">
            </div>

            <div class="col-xs-12 col-md-12 col-no-gutter">
                <h3>BONUS KARTLARI</h3>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>#</th>
                            <th>Nömrəsi</th>
                            <th>Növü</th>
                            <th>Müştərinin adı</th>
                            <th>Müştərinin nömrəsi</th>
                            <th>Yaranma tarixi</th>
                            <th>Bitmə tarixi</th>
                            <th>Bonus/Endirim</th>
                            <th>Qalıq</th>
                            <th>Redaktə et</th>
                            <th>Sil</th>
                        </tr>
                        <tr>
                            {if $search && $search_data}
                                <form action="{$app_url}/discount/card/search" method="post">
                                    <input type="hidden" name="user_id" value="{$user.id}">
                                    <input type="hidden" name="subject_id" value="{$subject.id}">
                                    <input type="hidden" name="search" value="1">
                                    <td></td>
                                    <td><input type="text" name="card_number" class="form-control" value="{$search_data.card_number}"></td>
                                    <td>
                                        <select name="card_type" class="form-control">
                                            <option value="%">Hamısı</option>
                                            {foreach from=$card_types key=k item=ct}
                                                <option {if $search_data.card_type == $k}selected{/if} value="{$k}">{$ct}</option>
                                            {/foreach}
                                        </select>
                                    </td>
                                    <td><input type="text" name="client_name" class="form-control" value="{$search_data.client_name}"></td>
                                    <td><input type="text" name="client_phone" class="form-control" value="{$search_data.client_phone}"></td>
                                    <td><input type="text" name="created_at" class="form-control datepicker" value="{$search_data.created_at}"></td>
                                    <td><input type="text" name="expire_at" class="form-control datepicker" value="{$search_data.expire_at}"></td>
                                    <td><input type="text" name="bonus_or_discount" class="form-control" value="{$search_data.bonus_or_discount}"></td>
                                    <td><input type="text" name="remaining_amount" class="form-control" value="{$search_data.remaining_amount}"></td>
                                    <td colspan="2">
                                        <input type="submit" class="btn btn-danger form-control" value="Axtar">
                                    </td>
                                </form>
                            {else}
                                <form action="{$app_url}/discount/card/search" method="post">
                                    <input type="hidden" name="user_id" value="{$user.id}">
                                    <input type="hidden" name="subject_id" value="{$subject.id}">
                                    <input type="hidden" name="search" value="1">
                                    <td></td>
                                    <td><input type="text" name="card_number" class="form-control"></td>
                                    <td>
                                        <select name="card_type" class="form-control">
                                            <option value="%">Hamısı</option>
                                            {foreach from=$card_types key=k item=ct}
                                                <option value="{$k}">{$ct}</option>
                                            {/foreach}
                                        </select>
                                    </td>
                                    <td><input type="text" name="client_name" class="form-control"></td>
                                    <td><input type="text" name="client_phone" class="form-control"></td>
                                    <td><input type="text" name="created_at" class="form-control datepicker"></td>
                                    <td><input type="text" name="expire_at" class="form-control datepicker"></td>
                                    <td><input type="text" name="bonus_or_discount" class="form-control"></td>
                                    <td><input type="text" name="remaining_amount" class="form-control"></td>
                                    <td colspan="2">
                                        <input type="submit" class="btn btn-danger form-control" value="Axtar">
                                    </td>
                                </form>
                            {/if}
                        </tr>
                        {assign var="i" value="0"}
                        {foreach from=$cards item=cr}
                            {assign var="i" value=$i + 1}
                            <tr {if $cr.card_status == 0}class="danger" style="color: #000000"{/if}>
                                <td>{$i}</td>
                                <td>{$cr.card_number}</td>
                                <td>{$card_types[$cr.card_type]} ({$cr.rule_name})</td>
                                <td>{$cr.client_name} {$cr.client_surname} {$cr.client_patronymic}</td>
                                <td>{$cr.client_phone}</td>
                                <td>{$cr.created_at}</td>
                                <td {if $cr.card_status == 0}style="color: #CC0000"{/if}>{$cr.expire_at|substr:0:10}</td>
                                <td>{$cr[$cr.card_type]}{if $cr.card_type == discount} %{/if}</td>
                                <td>{$cr.remaining_amount}</td>
                                <td><a href="{$app_url}/discount/card/edit/{$cr.id}" class="btn btn-warning">Redaktə et</a></td>
                                <td>
                                    <form action="{$app_url}/discount/card/delete" method="post">
                                        <input type="hidden" name="user_id" value="{$user.id}">
                                        <input type="hidden" name="subject_id" value="{$subject.id}">
                                        <input type="hidden" name="card_id" value="{$cr.id}">
                                        <button type="button" class="btn btn-danger btn-delete">Sil</button>
                                    </form>
                                </td>
                            </tr>
                        {/foreach}
                    </table>
                </div>

            </div>

                <div class="col-xs-12 col-md-4">
                </div>
                <div class="col-xs-12 col-md-4">
                    <ul class="pagination">
                        {if $search}
                            {foreach from=$paginator item=p}
                                {if isset($p.disabled) && $p.disabled}
                                    <li class="disabled"><a href="javascript:void(0)">{$p.title}</a></li>
                                {else}
                                    {if isset($p.active) && $p.active}
                                        <li class="active"><a href="{$app_url}/discount/card/search/{$p.page}">{$p.title}</a></li>
                                    {else}
                                        <li><a href="{$app_url}/discount/card/search/{$p.page}">{$p.title}</a></li>
                                    {/if}
                                {/if}
                            {/foreach}
                        {else}
                            {foreach from=$paginator item=p}
                                {if isset($p.disabled) && $p.disabled}
                                    <li class="disabled"><a href="javascript:void(0)">{$p.title}</a></li>
                                {else}
                                    {if isset($p.active) && $p.active}
                                        <li class="active"><a href="{$app_url}/discount/card/{$p.page}">{$p.title}</a></li>
                                    {else}
                                        <li><a href="{$app_url}/discount/card/{$p.page}">{$p.title}</a></li>
                                    {/if}
                                {/if}
                            {/foreach}
                        {/if}
                    </ul>
                </div>
                <div class="col-xs-12 col-md-4">
                </div>

        </div><!--/.row-->


    </div>	<!--/.main-->
    <script>
        $(function() {

            $(".discount_prop").css("display", "none");
            $(".discount_prop input").attr("disabled", true);

            $("select[name='card_type']").change(function(event) {
                var type = $(this).find('option:selected').val();
                $("select[name='rule_id']").html($("#" + type + "_rules").html());

                if(type == "bonus"){
                    $(".discount_prop").css("display", "none");
                    $(".discount_prop input").attr("disabled", true);
                    $(".bonus_prop").css("display", "block");
                    $(".bonus_prop input").attr("disabled", false);
                } else {
                    $(".bonus_prop").css("display", "none");
                    $(".bonus_prop input").attr("disabled", true);
                    $(".discount_prop").css("display", "block");
                    $(".discount_prop input").attr("disabled", false);
                }

            });

            $(".btn-delete").click(function(event) {

                dhtmlx.confirm({
                    title: "Silinmə",
                    text: "Kartın silinməsini təsdiqlə",
                    ok: "Təsdiq",
                    cancel: "İmtina",
                    callback: function(res){
                        if(res){
                            $(event.target).closest("form").submit();
                        }
                        return false;
                    }
                });

            });

        });
    </script>
{/block}