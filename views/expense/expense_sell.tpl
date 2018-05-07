{extends file="base.tpl"}

{block name="dashboard"}

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="{$app_url}"><span class="glyphicon glyphicon-home"></span></a></li>
                <li><a href="{$app_url}/expense/sell">Xərclər</a></li>
            </ol>
        </div><!--/.row-->


        <div class="row col-no-gutter-container">

            <div class="col-md-2"></div>

            <div class="col-xs-12 col-md-8 shops">

                <div class="collapse in" id="income">
                    <form class="form-horizontal" method="post" action="{$app_url}/expense/sell/approve">
                        <input type="hidden" name="user_id" id="user_id" value="{$user.id}">
                        <input type="hidden" name="subject_id" value="{$subject.id}">
                        <input type="hidden" name="invoice_type" value="{$invoice.type}">
                        <input type="hidden" name="cashbox_id" value="{$cashbox.id}">
                        <input type="hidden" name="operator" value="{$user.operator.id}">
                        <input type="hidden" name="invoice_serial" value="{$invoice.serial}">
                        <input type="hidden" name="currency" value="0">
                        <h3>Xərc</h3>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Qaimə №:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="{$invoice.serial}" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Xərcin növü:</label>
                            <div class="col-sm-10">
                                <select name="expense_id" id="expense_id" class="form-control">
                                    <option value="0">Digər</option>
                                    {foreach from=$expenses item=expense}
                                        <option data-currency="{$expense.id}" rel="{$expense.price}" value="{$expense.uid}">{$expense.name}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Məbləğ:</label>
                            <div class="col-sm-10">
                                <input type="number" step="0.01" class="form-control" name="amount" id="amount" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Valyuta:</label>
                            <div class="col-sm-10">
                                <select name="currency" class="form-control" id="currency_id" disabled>
                                    <option accesskey="0" value="0">AZN</option>
                                    {foreach from=$currencies item=v}
                                        <option accesskey="{$v.value}" value="{$v.id}">{$v.name}</option>
                                    {/foreach}
                                </select>
                                <input type="hidden" name="currency_archive" id="currency_archive" value="0">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Qeyd:</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" rows="3" name="notes"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Tarix:</label>
                            <div class="col-sm-10">
                                <input type="text" name="date" class="form-control datepicker" value="{$currentDate}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-danger f-right">DAXIL ET</button>
                    </form>
                </div>


                <div class="col-md-2"></div>

            </div>
            <!--
                End create new baza form
            -->

        </div><!--/.row-->


    </div>	<!--/.main-->
{/block}