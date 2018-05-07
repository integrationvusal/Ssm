{extends file="base.tpl"}

{block name="dashboard"}

    <div class="modal fade" id="invoice_details" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Qaimə № <span id="modal_is"></span></h4>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="table-responsive">

                            <table id="invoice_detail_table" class="table" style="color: black;">
                            </table>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">İmtina</button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="{$app_url}"><span class="glyphicon glyphicon-home"></span></a></li>
                <li class="active">Kassa</li>
            </ol>
        </div><!--/.row-->


        <div class="row col-no-gutter-container">


            <div class="col-xs-12 col-md-12" style="margin-bottom: 10px">
                {if $permissions.cashbox_income}
                <button type="button" class="btn btn-info btn-md col-sm-2 cashbox-actions" style="margin-right: 5px" data-target="#income">Mədaxil</button>
                {/if}
                {if $permissions.cashbox_outgoing}
                <button type="button" class="btn btn-warning btn-md col-sm-2 cashbox-actions" style="margin-right: 5px" data-target="#outgoing">Məxaric</button>
                {/if}
                {if $permissions.cashbox_transfer}
                <button type="button" class="btn btn-danger btn-md col-sm-2 cashbox-actions" data-target="#transfer">Transfer</button>
                {/if}
            </div>

            <div class="col-xs-12 col-md-2">
            </div>

            <!--
                Create new baza form
            -->
            <div class="col-xs-12 col-md-8 shops">

                    <div class="collapse" id="income">
                        <form class="form-horizontal" method="post" action="{$app_url}/cashbox/income">
                        <input type="hidden" name="user_id" id="user_id" value="{$user.id}">
                        <input type="hidden" name="subject_id" value="{$subject.id}">
                        <input type="hidden" name="invoice_type" value="2">
                        <input type="hidden" name="cashbox_id" value="{$cashbox.id}">
                        <input type="hidden" name="operator" value="{$operator}">
                        <input type="hidden" name="invoice_serial" value="{$nextInvoice.income}">
                        <h3>Mədaxil</h3>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Qaimə №:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="{$nextInvoice.income}" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Valyuta:</label>
                            <div class="col-sm-10">
                                <select name="currency" class="form-control">
                                    <option accesskey="0" value="0">AZN</option>
                                    {foreach from=$currencies item=v}
                                        <option accesskey="{$v.value}" value="{$v.id}">{$v.name}</option>
                                    {/foreach}
                                </select>
                                <input type="hidden" name="currency_archive" id="currency_archive" value="0">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Məbləğ:</label>
                            <div class="col-sm-10">
                                <input type="number" step="0.01" class="form-control" name="amount" required>
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

                    <div class="collapse" id="outgoing">
                        <form class="form-horizontal" method="post" action="{$app_url}/cashbox/outgoing">
                            <input type="hidden" name="user_id" value="{$user.id}">
                            <input type="hidden" name="subject_id" value="{$subject.id}">
                            <input type="hidden" name="invoice_type" value="3">
                            <input type="hidden" name="cashbox_id" value="{$cashbox.id}">
                            <input type="hidden" name="operator" value="{$operator}">
                            <input type="hidden" name="invoice_serial" value="{$nextInvoice.outgoing}">
                            <h3>Məxaric</h3>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Qaimə №:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="{$nextInvoice.outgoing}" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Valyuta</label>
                                <div class="col-sm-10">
                                    <select name="currency" class="form-control">
                                        <option accesskey="0" value="0">AZN</option>
                                        {foreach from=$currencies item=v}
                                            {if array_key_exists($v.id, $cashbox.currencies_amounts)}
                                                <option accesskey="{$v.value}" value="{$v.id}">{$v.name}</option>
                                            {/if}
                                        {/foreach}
                                    </select>
                                    <input type="hidden" name="currency_archive" id="currency_archive" value="0">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Məbləğ</label>
                                <div class="col-sm-10">
                                    <input type="number" step="0.01" max="{$cashbox.currencies_amounts.0}" class="form-control" name="amount" required>
                                </div>
                                <div class="hint">
                                    Hazırki (AZN): <strong>{$cashbox.currencies_amounts.0}</strong>
                                </div>
                                <div class="hint">
                                    Hazırki (USD): <strong>{$cashbox.currencies_amounts.1}</strong>
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

                    <div class="collapse" id="transfer">
                        <form class="form-horizontal" method="post" action="{$app_url}/cashbox/transfer">
                            <input type="hidden" name="user_id" value="{$user.id}">
                            <input type="hidden" name="subject_id" value="{$subject.id}">
                            <input type="hidden" name="invoice_type" value="4">
                            <input type="hidden" name="cashbox_id" value="{$cashbox.id}">
                            <input type="hidden" name="operator" value="{$operator}">
                            <input type="hidden" name="invoice_serial" value="{$nextInvoice.transfer}">
                            <h3>Transfer</h3>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Qaimə №:</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="{$nextInvoice.transfer}" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Valyuta</label>
                                <div class="col-sm-10">
                                    <select name="currency" class="form-control">
                                        <option accesskey="0" value="0">AZN</option>
                                        {foreach from=$currencies item=v}
                                            {if array_key_exists($v.id, $cashbox.currencies_amounts)}
                                                <option accesskey="{$v.value}" value="{$v.id}">{$v.name}</option>
                                            {/if}
                                        {/foreach}
                                    </select>
                                    <input type="hidden" name="currency_archive" id="currency_archive" value="0">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Göndəriləcək kassa:</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="destination_cb" id="destination_cb" required>
                                        {foreach from=$cbsOfUser item=cb}
                                            {if $cb.id == $cashbox.id}
                                                <option class="azn" value="{$cb.id}" disabled>
                                                    {$cb.name} - {$cb.total_amount_azn}
                                                </option>
                                                <option class="usd hide" value="{$cb.id}" disabled>
                                                    {$cb.name} - {$cb.total_amount}
                                                </option>
                                            {else}
                                                <option class="azn" value="{$cb.id}">
                                                    {$cb.name} - {$cb.total_amount_azn}
                                                </option>
                                                <option class="usd hide" value="{$cb.id}">
                                                    {$cb.name} - {$cb.total_amount}
                                                </option>
                                            {/if}
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Məbləğ:</label>
                                <div class="col-sm-10">
                                    <input type="number" step="0.01" max="{$cashbox.currencies_amounts.0}" class="form-control" name="amount" required>
                                </div>
                                <div class="hint">
                                    Hazırki (AZN): <strong>{$cashbox.currencies_amounts.0}</strong>
                                </div>
                                <div class="hint">
                                    Hazırki (USD): <strong>{$cashbox.currencies_amounts.1}</strong>
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

            </div>
            <!--
                End create new baza form
            -->

            <div class="col-xs-12 col-md-2">
            </div>

            <div class="col-xs-12 col-md-12 col-no-gutter">
                <h3>"{$subject.name}" - nın kassası</h3>
                <div class="table-responsive">
                    <table class="table table-bordered" style="width:100%;">
                        <tr>
                            <th>#</th>
                            <th>Operator</th>
                            <th>Mədaxil</th>
                            <th>Məxaric</th>
                            <th>Qaimə №</th>
                            <th>Tarix</th>
                             <th>Balans (AZN)</th>
                            {foreach from=$currencies item=c}
                                <th>Balans ({$c['name']})</th>
                            {/foreach}
                        </tr>
                        <tr>
                            <th></th>
                            <th>-</th>
                            <th>-</th>
                            <th>-</th>
                            <th>-</th>
                            <th>-</th>
                            <th>{$cashbox.currencies_amounts.0}</th>
                            {foreach from=$currencies key=k item=c}
                                <th>{$cashbox.currencies_amounts[$k+1]}</th>
                            {/foreach}
                        </tr>
                        {assign var="i" value=(($page-1) * $limit)}
                        {foreach from=$cashboxHistory item=ch}
                            {assign var="i" value=$i+1_azn}
                            <tr>
                                <td>{$i}</td>
                                <td>{if !empty($ch.name)}{$ch.name}{else}{$user.name}{/if}</td>
                                <td>{if $ch.operation_type == '+'}+{$ch.amount}{else}0{/if}</td>
                                <td>{if $ch.operation_type == '-'}-{$ch.amount}{else}0{/if}</td>
                                <td><a href="javascript:void(0)"
                                       class="invoice-details"
                                       data-invoice-id="{$ch.invoice_id}"
                                       data-invoice-type="{$ch.invoice_type}">{$ch.serial}</a></td>
                                <td>{$ch.date|substr:0:10}</td>
                                <td>{if $ch.currency == 0}{$ch.total_amount}{/if}</td>
                                {foreach from=$currencies key=k item=c}
                                    <td>{if $ch.currency == $k+1}{$ch.total_amount}{/if}</td>
                                {/foreach}
                                
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
                                <li class="active"><a href="{$app_url}/cashbox/{$p.page}">{$p.title}</a></li>
                            {else}
                                <li><a href="{$app_url}/cashbox/{$p.page}">{$p.title}</a></li>
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