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
                <li class="active">Qaimələr</li>
            </ol>
        </div><!--/.row-->


        <div class="row col-no-gutter-container">

            <div class="col-xs-12 col-md-2">
            </div>
            <div class="col-xs-12 col-md-8">
                {if $searchData}
                <form action="{$app_url}/invoice" method="post" id="invoice_search_form">
                    <input type="hidden" id="user_id" name="user_id" value="{$user.id}">
                    <input type="hidden" id="subject_id" name="subject_id" value="{$subject.id}">
                    <input type="hidden" id="search_invoice" name="search_invoice" value="1">
                    <div class="form-group col-sm-4" style="margin-right: 10px">
                        <label class="control-label">Başlanğıc tarixi:</label>
                        <div>
                            <input type="text" class="form-control datepicker" name="date_from" value="{$searchData.date_from}" />
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label class="control-label">Son tarix:</label>
                        <div>
                            <input type="text" class="form-control datepicker" name="date_to" value="{$searchData.date_to}" />
                        </div>
                    </div>
                    <div class="form-group col-sm-4" style="margin-right: 10px">
                        <label class="control-label">Qaimənin növü:</label>
                        <div>
                            <select name="invoice_type" id="invoice_type" class="form-control">
                                <option value="">Hamısını göstər</option>
                                {foreach from=$invoiceTypes item=invoiceType key=k}
                                    {if $k == $searchData.invoice_type}
                                        <option value="{$k}" selected>{$invoiceType.title}</option>
                                    {else}
                                        <option value="{$k}">{$invoiceType.title}</option>
                                    {/if}
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label class="control-label">Qaimənin nömrəsi:</label>
                        <div>
                            <input type="text" class="form-control" name="invoice_serial" value="{$searchData.invoice_serial}" />
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <button type="submit" class="btn btn-danger from-control">AXTAR</button>
                    </div>
                </form>
                {else}
                <form action="{$app_url}/invoice" method="post">
                    <input type="hidden" id="user_id" name="user_id" value="{$user.id}">
                    <input type="hidden" id="subject_id" name="subject_id" value="{$subject.id}">
                    <input type="hidden" id="search_invoice" name="search_invoice" value="1">
                    <div class="form-group col-sm-4" style="margin-right: 10px">
                        <label class="control-label">Başlanğıc tarixi:</label>
                        <div>
                            <input type="text" class="form-control datepicker" name="date_from">
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label class="control-label">Son tarix:</label>
                        <div>
                            <input type="text" class="form-control datepicker" name="date_to">
                        </div>
                    </div>
                    <div class="form-group col-sm-4" style="margin-right: 10px">
                        <label class="control-label">Qaimənin növü:</label>
                        <div>
                            <select name="invoice_type" id="invoice_type" class="form-control">
                                <option value="0">Hamısını göstər</option>
                                {foreach from=$invoiceTypes item=invoiceType key=k}
                                    <option value="{$k}">{$invoiceType.title}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label class="control-label">Qaimənin nömrəsi:</label>
                        <div>
                            <input type="text" class="form-control" name="invoice_serial">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <button class="btn btn-danger">AXTAR</button>
                    </div>
                </form>
                {/if}
            </div>
            <div class="col-xs-12 col-md-2">
            </div>

            <div class="col-xs-12 col-md-12 col-no-gutter">
                <h3>"{$subject.name}" - üçün qaimələr</h3>

                <div class="table-responsive">
                    <table class="table table-bordered" style="width:100%;">
                        <tr>
                            <th>#</th>
                            <th>Növü</th>
                            <th>Qaimə №</th>
                            <th>Kontragent</th>
                            <th>Müştəri</th>
                            <th>Ödənilmiş</th>
                            <th>Məbləğ (USD)</th>
                            <th>Məbləğ (AZN)</th>
                            <th>Tarix</th>
                        </tr>
                        {assign var="i" value=($page - 1) * $limit}
                        {foreach from=$invoices item=invoice}
                            {assign var="i" value=$i+1}
                            <tr>
                                <td>{$i}</td>
                                <td>{$invoice_types[$invoice.type].title}</td>
                                {if $invoice.status == '0'}
                                    <td>{$invoice.serial}</td>
                                {else}
                                    <td><a href="javascript:void(0)"
                                           class="invoice-details"
                                           data-invoice-id="{$invoice.id}"
                                           data-invoice-type="{$invoice.type}">{$invoice.serial}</a></td>
                                {/if}
                                <td>{$invoice.contragent_name}</td>
                                {if $invoice.discount == 1}
                                    <td>{$invoice.discount_card_number}</td>
                                {else}
                                    <td>{$invoice.client_name}</td>
                                {/if}
                                {if $invoice.status == '0'}
                                    <td>Etibarsız</td>
                                    <td>Etibarsız</td>
                                {else}
                                    <td>{$invoice.payed}</td>
                                    {if $invoice.discount == 1}
                                        <td>{if $invoice.currency}{$invoice.amount} ({$invoice.amount + $invoice.discounted_amount}){/if}</td>
                                        <td>{if !$invoice.currency}{$invoice.amount} ({$invoice.amount + $invoice.discounted_amount}){/if}</td>
                                    {else}
                                        <td>{if $invoice.currency}{$invoice.amount}{/if}</td>
                                        <td>{if !$invoice.currency}{$invoice.amount}{/if}</td>
                                    {/if}
                                {/if}
                                <td>{$invoice.date|substr:0:10}</td>
                            </tr>
                        {/foreach}
                    </table>
                </div>
            </div>

            {if !$searchData}
            <div class="col-xs-12 col-md-4">
            </div>
            <div class="col-xs-12 col-md-4">
                <ul class="pagination">
                    {foreach from=$paginator item=p}
                        {if isset($p.disabled) && $p.disabled}
                            <li class="disabled"><a href="javascript:void(0)">{$p.title}</a></li>
                        {else}
                            {if isset($p.active) && $p.active}
                                <li class="active"><a href="{$app_url}/invoice/{$p.page}">{$p.title}</a></li>
                            {else}
                                <li><a href="{$app_url}/invoice/{$p.page}">{$p.title}</a></li>
                            {/if}
                        {/if}
                    {/foreach}
                </ul>
            </div>
            <div class="col-xs-12 col-md-4">
            </div>
            {/if}

        </div><!--/.row-->


    </div>	<!--/.main-->
{/block}