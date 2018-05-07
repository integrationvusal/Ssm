{extends file="base.tpl"}

{block name="page-title"}
    :: BONUS XÜSUSİYYƏTLƏRİ
{/block}

{block name="dashboard"}

    <span id="rule_template" style="display: none;">
        <span>
            <input type="hidden" name="rule_type[]" value="plus">
            <div class="row" style="margin-top: 10px">
                <div class="col-md-3" style="padding-left: 0">
                    <input type="number" min="0" step="1" class="form-control" name="first_param[]" required>
                </div>
                <label class="col-md-2 control-label auto-currency" style="text-align: left">AZN</label>
                <div class="col-md-3">
                    <input type="number" min="1" step="1" class="form-control" name="second_param[]" required>
                </div>
                <label class="col-md-2 control-label" style="text-align: left">%</label>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary delete-rule"><span class="glyphicon glyphicon-minus"></span></button>
                </div>
            </div>
        </span>
    </span>

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">

        <div class="row">
            <ol class="breadcrumb">
                <li><a href="{$app_url}"><span class="glyphicon glyphicon-home"></span></a></li>
                <li class="active">BONUS XÜSUSİYYƏTLƏRİ</li>
            </ol>
        </div><!--/.row-->


        <div class="row col-no-gutter-container" style="float: left;">
            <div class="col-xs-12 col-md-2">
            </div>
            <div class="col-xs-12 col-md-8 kontragent">

                {if $rule}
                    <form class="form-horizontal" action="{$app_url}/discount/rule/update" method="post" enctype="multipart/form-data">
                        {if $permissions.discount_rule}
                            <button type="button" class="btn btn-success btn-lg btn-block" data-toggle="collapse" data-target="#collapseExampleKontragent" aria-expanded="false" aria-controls="collapseExample">REDAKTƏ ET</button>
                        {/if}
                        <input type="hidden" name="user_id" value="{$user.id}">
                        <input type="hidden" name="subject_id" value="{$subject.id}">
                        <input type="hidden" name="rule_id" value="{$rule.id}">
                        <div class="collapse in" id="collapseExampleKontragent">
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Adı:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="" placeholder="" name="rule_name" value="{$rule.rule_name}" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Bonus kartının növü:</label>
                                <div class="col-md-8">
                                    <select class="form-control" disabled>
                                        {foreach from=$card_types key=k item=card_type}
                                            <option {if $k == $rule.card_type}selected{/if} value="{$k}">{$card_type}</option>
                                        {/foreach}
                                    </select>
                                    <input type="hidden" name="card_type" value="{$rule.card_type}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Bonus kartının növü:</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="currency">
                                        <option value="0" accesskey="0">AZN</option>
                                        {foreach from=$currencies item=currency}
                                            <option {if $rule.currency == $currency.id}selected="selected"{/if} accesskey="{$currency.value}" value="{$currency.id}">{$currency.name}</option>
                                        {/foreach}
                                    </select>
                                    <input name="currency_archive" type="hidden" value="0"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Kartın müddəti bitdikdə bonusları təmizlə:</label>
                                <div class="col-md-8">
                                    <input type="checkbox" class="form-control" name="clear_on_expire" value="1" {if $rule.clear_on_expire == 1}checked{/if}>
                                </div>
                            </div>
                            <div class="form-group bonus_prop">
                                <label for="" class="col-md-4 control-label">Qalıqı hesabla:</label>
                                <div class="col-md-8">
                                    <input type="checkbox" class="form-control" name="save_remaining" value="1" {if $rule.save_remaining == 1}checked{/if}>
                                </div>
                            </div>
                            {if $rule.card_type == 'bonus'}
                                <div class="form-group bonus_prop">
                                    <label for="" class="col-md-4 control-label">Mədaxil qaydası 1 BONUS:</label>
                                    <input type="hidden" name="rule_type[]" value="plus">
                                    <div class="col-md-6">
                                        <input type="hidden" name="second_param[]" value="1">
                                        <input type="number" min="1" step="1" class="form-control" name="first_param[]" value="{$rule.plus_rules[0].first_param}" required>
                                    </div>
                                    <label class="col-md-2 control-label auto-currency" style="text-align: left">AZN</label>
                                </div>
                                <div class="form-group bonus_prop">
                                    <label for="" class="col-md-4 control-label ">Məxaric qaydası 1 <span class="auto-currency">AZN</span>:</label>
                                    <span>
                                        <input type="hidden" name="rule_type[]" value="minus">
                                        <div class="col-md-6">
                                            <input type="hidden" name="first_param[]" value="{$rule.minus_rules[0].first_param}">
                                            <input type="number" min="1" step="1"  class="form-control" name="second_param[]" value="{$rule.minus_rules[0].second_param}" required>
                                        </div>
                                        <label class="col-md-2 control-label" style="text-align: left">BONUS</label>
                                    </span>
                                </div>
                            {else}
                                <div class="form-group bonus_prop">
                                    <label for="" class="col-md-4 control-label">Mədaxil qaydası 1 BONUS:</label>
                                    <input type="hidden" name="rule_type[]" value="plus">
                                    <div class="col-md-6">
                                        <input type="hidden" name="second_param[]" value="1">
                                        <input type="number" min="1" step="1" class="form-control" name="first_param[]" required>
                                    </div>
                                    <label class="col-md-2 control-label auto-currency" style="text-align: left">AZN</label>
                                </div>
                                <div class="form-group bonus_prop">
                                    <label for="" class="col-md-4 control-label">Məxaric qaydası:</label>
                                    <span>
                                        <input type="hidden" name="rule_type[]" value="minus">
                                        <div class="col-md-6">
                                            <input type="hidden" name="first_param[]" value="1">
                                            <input type="number" min="1" step="1"  class="form-control" name="second_param[]" required>
                                        </div>
                                        <label class="col-md-2 control-label" style="text-align: left">BONUS</label>
                                    </span>
                                </div>
                            {/if}
                            {if $rule.card_type == 'discount'}
                                <div class="form-group discount_prop">
                                    <label for="" class="col-md-4 control-label">Mədaxil qaydası:</label>
                                    <div class="col-md-8">
                                        {foreach from=$rule.plus_rules key=i item=plus_rule}
                                        <span class="plus-rule">
                                            <input type="hidden" name="rule_type[]" value="plus">
                                            <div class="row" {if $i > 0}style="margin-top: 10px"{/if}>
                                                <div class="col-md-3" style="padding-left: 0">
                                                    <input type="number" min="0" step="1" class="form-control" value="{$plus_rule.first_param}" name="first_param[]" required>
                                                </div>
                                                <label class="col-md-2 control-label auto-currency" style="text-align: left">AZN</label>
                                                <div class="col-md-3">
                                                    <input type="number" min="1" step="1" class="form-control" value="{$plus_rule.second_param}" name="second_param[]" required>
                                                </div>
                                                <label class="col-md-2 control-label" style="text-align: left">%</label>
                                                <div class="col-md-2">
                                                    {if $i == 0}
                                                    <button type="button" class="btn btn-danger add-rule"><span class="glyphicon glyphicon-plus"></span></button>
                                                    {else}
                                                    <button type="button" class="btn btn-primary delete-rule"><span class="glyphicon glyphicon-minus"></span></button>
                                                    {/if}
                                                </div>
                                            </div>
                                        </span>
                                        {/foreach}
                                    </div>
                                </div>
                            {else}
                                <div class="form-group discount_prop">
                                    <label for="" class="col-md-4 control-label">Mədaxil qaydası:</label>
                                    <div class="col-md-8">
                                    <span class="plus-rule">
                                        <input type="hidden" name="rule_type[]" value="plus">
                                        <div class="row">
                                            <div class="col-md-3" style="padding-left: 0">
                                                <input type="number" min="0" step="1" class="form-control" name="first_param[]" required>
                                            </div>
                                            <label class="col-md-2 control-label auto-currency" style="text-align: left">AZN</label>
                                            <div class="col-md-3">
                                                <input type="number" min="1" step="1" class="form-control" name="second_param[]" required>
                                            </div>
                                            <label class="col-md-2 control-label" style="text-align: left">%</label>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger add-rule"><span class="glyphicon glyphicon-plus"></span></button>
                                            </div>
                                        </div>
                                    </span>
                                    </div>
                                </div>
                            {/if}
                            <div class="form-group">
                                <div class="col-md-2"></div>
                                <div class="col-md-10">
                                    <button type="submit" class="btn btn-warning form-control">YENİLƏ</span></button>
                                </div>
                            </div>
                        </div>
                    </form>
                {else}
                    <form class="form-horizontal" action="{$app_url}/discount/rule/create" method="post" enctype="multipart/form-data">
                        {if $permissions.discount_rule}
                            <button type="button" class="btn btn-success btn-lg btn-block" data-toggle="collapse" data-target="#collapseExampleKontragent" aria-expanded="false" aria-controls="collapseExample">YENİ</button>
                        {/if}
                        <input type="hidden" name="user_id" value="{$user.id}">
                        <input type="hidden" name="subject_id" value="{$subject.id}">
                        <div class="collapse" id="collapseExampleKontragent">
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Adı:</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="" placeholder="" name="rule_name" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Bonus kartının növü:</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="card_type">
                                        {foreach from=$card_types key=k item=card_type}
                                            <option value="{$k}">{$card_type}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Bonus kartının növü:</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="currency">
                                        <option value="0" accesskey="0">AZN</option>
                                        {foreach from=$currencies item=currency}
                                            <option accesskey="{$currency.value}" value="{$currency.id}">{$currency.name}</option>
                                        {/foreach}
                                    </select>
                                    <input name="currency_archive" type="hidden" value="0"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-4 control-label">Kartın müddəti bitdikdə bonusları təmizlə:</label>
                                <div class="col-md-8">
                                    <input type="checkbox" class="form-control" name="clear_on_expire" value="1">
                                </div>
                            </div>
                            <div class="form-group bonus_prop">
                                <label for="" class="col-md-4 control-label">Qalıqı hesabla:</label>
                                <div class="col-md-8">
                                    <input type="checkbox" class="form-control" name="save_remaining" value="1">
                                </div>
                            </div>
                            <div class="form-group bonus_prop">
                                <label for="" class="col-md-4 control-label">Mədaxil qaydası 1 BONUS:</label>
                                <input type="hidden" name="rule_type[]" value="plus">
                                <div class="col-md-6">
                                    <input type="hidden" name="second_param[]" value="1">
                                    <input type="number" min="1" step="1" class="form-control" name="first_param[]" required>
                                </div>
                                <label class="col-md-2 control-label auto-currency" style="text-align: left">AZN</label>
                            </div>
                            <div class="form-group bonus_prop">
                                <label for="" class="col-md-4 control-label">Məxaric qaydası 1 <span class="auto-currency">AZN</span>:</label>
                            <span>
                                <input type="hidden" name="rule_type[]" value="minus">
                                <div class="col-md-6">
                                    <input type="hidden" value="1" name="first_param[]" required>
                                    <input type="number" min="1" step="1"  class="form-control" name="second_param[]" required>
                                </div>
                                <label class="col-md-2 control-label" style="text-align: left">BONUS</label>
                            </span>
                            </div>
                            <div class="form-group discount_prop">
                                <label for="" class="col-md-4 control-label">Mədaxil qaydası:</label>
                                <div class="col-md-8">
                                <span class="plus-rule">
                                    <input type="hidden" name="rule_type[]" value="plus">
                                    <div class="row">
                                        <div class="col-md-3" style="padding-left: 0">
                                            <input type="number" min="0" step="1" class="form-control" name="first_param[]" required>
                                        </div>
                                        <label class="col-md-2 control-label auto-currency" style="text-align: left">AZN</label>
                                        <div class="col-md-3">
                                            <input type="number" min="1" step="1" class="form-control" name="second_param[]" required>
                                        </div>
                                        <label class="col-md-2 control-label" style="text-align: left">%</label>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger add-rule"><span class="glyphicon glyphicon-plus"></span></button>
                                        </div>
                                    </div>
                                </span>
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
                <h3>BONUS XÜSUSİYYƏTLƏRİ</h3>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>#</th>
                            <th>Qaydanın adı</th>
                            <th>Kartın növü</th>
                            <th>Kartın müddəti bitdikdə bonusları təmizlə:</th>
                            <th>Qalıqı hesabla</th>
                            <th>Valyuta</th>
                            <th>Redaktə et</th>
                            <th>Sil</th>
                        </tr>
                        {assign var="i" value="0"}
                        {foreach from=$rules item=rl}
                            {assign var="i" value=$i + 1}
                            <tr>
                                <td>{$i}</td>
                                <td>{$rl.rule_name}</td>
                                <td>{$card_types[$rl.card_type]}</td>
                                <td>{$yesno_trigger[$rl.clear_on_expire]}</td>
                                <td>{$yesno_trigger[$rl.save_remaining]}</td>
                                <td>{$rl.currency}</td>
                                <td><a href="{$app_url}/discount/rule/edit/{$rl.id}" class="btn btn-warning">Redaktə et</a></td>
                                <td>
                                    <form action="{$app_url}/discount/rule/delete" method="post">
                                        <input type="hidden" name="user_id" value="{$user.id}">
                                        <input type="hidden" name="subject_id" value="{$subject.id}">
                                        <input type="hidden" name="rule_id" value="{$rl.id}">
                                        <button type="button" class="btn btn-danger rule-delete-button">Sil</button>
                                    </form>
                                </td>
                            </tr>
                        {/foreach}
                    </table>
                </div>

            </div>

        </div><!--/.row-->


    </div>	<!--/.main-->
    <script>

        $(function() {

            {if $rule && $rule.card_type == 'discount'}
                $(".bonus_prop").css("display", "none");
                $(".bonus_prop input").attr("disabled", true);
            {else}
                $(".discount_prop").css("display", "none");
                $(".discount_prop input").attr("disabled", true);
            {/if}

            $("select[name='card_type']").change(function(event){
                if($("select[name='card_type'] option:selected").val() == 'discount'){
                    $(".bonus_prop").css("display", "none");
                    $(".bonus_prop input").attr("disabled", true);
                    $(".discount_prop").css("display", "block");
                    $(".discount_prop input").removeAttr("disabled");
                } else {
                    $(".bonus_prop").css("display", "block");
                    $(".bonus_prop input").removeAttr("disabled");
                    $(".discount_prop").css("display", "none");
                    $(".discount_prop input").attr("disabled", true);
                }
            });

            $(".add-rule").click(function(event){

                var tmp = $("#rule_template").html();
                tmp = $(tmp).first("span").addClass("plus-rule");
                $("span.plus-rule:last-child").after(tmp);

                $(".delete-rule").unbind().click(deleteRuleAction);

            });

            $(".delete-rule").unbind().click(deleteRuleAction);

            function deleteRuleAction(event){
                if($(".plus-rule").length > 1){
                    $(event.target).closest("span.plus-rule").remove();
                }
                return false;
            }

            var ruleExistanceCheckXHR = null;

            $(".rule-delete-button").click(function(event) {
                event.preventDefault();
                dhtmlx.confirm({
                    title: "Silinmə",
                    text: "Silinməsini təsdiqlə",
                    ok: "Təsdiq",
                    cancel: "İmtina",
                    callback: function(res){
                        if(res){
                            if(ruleExistanceCheckXHR != null) ruleExistanceCheckXHR.abort();
                            ruleExistanceCheckXHR = $.ajax({
                                url: '{$app_url}/discount/rule/exists',
                                type: 'POST',
                                data: $(event.target).closest("form").serialize(),
                                dataType: 'JSON',
                                beforeSend: function(xhr){
                                    Loader.lStart(xhr);
                                },
                                success: function(data){
                                    Loader.lStop();
                                    if(data == true){
                                        dhtmlx.alert({
                                            title: "Xüsusiyyətin silinməsi",
                                            text: "Xüsusiyyətin silinməsi mümkün olmadı. <br>Bu xüsussiyətdən istifadə edən kart mövcuddur."
                                        });
                                    } else {
                                        $(event.target).closest("form").submit();
                                    }
                                }
                            });
                        }
                        return false;
                    }
                });
            });
        });
    </script>
{/block}