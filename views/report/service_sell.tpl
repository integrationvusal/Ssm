{extends file="base.tpl"}

{block name="page-title"}
    :: Xidmət satışı üzrə hesabat
{/block}

{block name="dashboard"}

    <div class="modal fade" id="goods-info" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

            </div>
        </div>
    </div>

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
                <li><a href="{$app_url}/report/service">Satılmış xidmətlər üzrə hesabat</a></li>
            </ol>
        </div><!--/.row-->

        <div class="row col-no-gutter-container">
            <div class="col-xs-12 col-md-2">
            </div>
            <div class="col-xs-12 col-md-8 client">
                {if $searchData}
                    <form class="form-horizontal" action="{$app_url}/report/service" method="post">
                        <input type="hidden" name="user_id" id="user_id" value="{$user.id}">
                        <div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Tarix:</label>
                                <div class="col-md-5"><input type="text" class="datepicker form-control" name="date_from" value="{$searchData.date_from}"></div>
                                <div class="col-md-5"><input type="text" class="datepicker form-control" name="date_to" value="{$searchData.date_to}"></div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Obyekt:</label>
                                <div class="col-md-5"><input type="text" class="service_subject_search form-control"></div>
                                <div class="col-md-5">
                                    <select name="subject_id" id="service_subject_sb" class="form-control">
                                        <option value="0">Obyekti seç</option>
                                        {foreach from=$subjects item=sub}
                                            {if $searchData.subject_id == $sub.id}
                                                <option selected value="{$sub.id}">{$sub.name}</option>
                                            {else}
                                                <option value="{$sub.id}">{$sub.name}</option>
                                            {/if}
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Xidmətin adı:</label>
                                <div class="col-md-5"><input type="text" class="service_search form-control"></div>
                                <div class="col-md-5">
                                    <select name="service_id" id="service_id" class="form-control">
                                        <option value="0">Xidmət növünü seç</option>
                                        {foreach from=$services item=ser}
                                            {if $searchData.service_id == $ser.id}
                                                <option value="{$ser.id}" selected>{$ser.name}</option>
                                            {else}
                                                <option value="{$ser.id}">{$ser.name}</option>
                                            {/if}
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <input type="submit" class="btn btn-danger form-control" name="report_search" value="AXTAR">
                        </div>
                    </form>
                {else}
                    <form class="form-horizontal" action="{$app_url}/report/service" method="post">
                        <input type="hidden" name="user_id" id="user_id" value="{$user.id}">
                        <div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Tarix:</label>
                                <div class="col-md-5"><input type="text" class="datepicker form-control" name="date_from"></div>
                                <div class="col-md-5"><input type="text" class="datepicker form-control" name="date_to"></div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Obyekt:</label>
                                <div class="col-md-5"><input type="text" class="service_subject_search form-control"></div>
                                <div class="col-md-5">
                                    <select name="subject_id" id="service_subject_sb" class="form-control">
                                        <option value="0">Obyekti seç</option>
                                        {foreach from=$subjects item=sub}
                                            {if $subject.id == $sub.id}
                                                <option selected value="{$sub.id}">{$sub.name}</option>
                                            {else}
                                                <option value="{$sub.id}">{$sub.name}</option>
                                            {/if}
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Xidmətin adı:</label>
                                <div class="col-md-5"><input type="text" class="service_search form-control"></div>
                                <div class="col-md-5">
                                    <select name="service_id" id="service_id" class="form-control">
                                        <option value="0">Xidmət növünü seç</option>
                                        {foreach from=$services item=service}
                                            <option value="{$service.id}">{$service.name}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <input type="submit" class="btn btn-danger form-control" name="report_search" value="AXTAR">
                        </div>
                    </form>
                {/if}
            </div>


            <div class="col-xs-12 col-md-2">
            </div>

            <div class="col-xs-12 col-md-12 col-no-gutter">
                <h3>Mallar <a class="btn btn-info" href="{$app_url}/report/service/print"><span class="glyphicon glyphicon-print"></span></a></h3>
                <div class="table-responsive">
                    <table class="table table-bordered" style="width:100%;">
                        <tr>
                            <th>#</th>
                            <th>Obyekt</th>
                            <th>Operator</th>
                            <th>Xidmətin adı</th>
                            <th>Qaimə №</th>
                            <th>Sayı</th>
                            <th>Məbləğ (USD)</th>
                            <th>Məbləğ (AZN)</th>
                            <th>Tarix</th>
                        </tr>
                        {assign var=total_count value=0}
                        {assign var=total_sell value=0}
                        {assign var=total_sell_azn value=0}
                        {assign var="i" value=(($page-1) * $limit)}
                        {foreach from=$invoices item=invoice}
                            {assign var="i" value=$i+1}
                            <tr>

                                <td>{$i}</td>
                                <td>{$invoice.subject_name}</td>
                                <td>{if empty($invoice.name)}{$user.name}{else}{$invoice.name}{/if}</td>
                                <td>{$invoice.short_info}</td>
                                <th><a href="javascript:void(0)"
                                       class="invoice-details"
                                       data-invoice-id="{$invoice.id}"
                                       data-invoice-type="{$invoice.type}">{$invoice.serial}</a></th>
                                <td>{$invoice.count}</td>
                                <td>{if $invoice.currency}{$invoice.amount}{/if}</td>
                                <td>{if !$invoice.currency}{$invoice.amount}{/if}</td>
                                {if $invoice.currency}
                                    {$total_sell = $total_sell + ($invoice.sell_price * $invoice.count)}
                                {else}
                                    {$total_sell_azn = $total_sell_azn + ($invoice.sell_price * $invoice.count)}
                                {/if}

                                {$total_count = $total_count + $invoice.count}

                                <td>{$invoice.date|substr:0:10}</td>
                            </tr>
                        {/foreach}
                        <tr>
                            <th colspan="5">Göstərilənlərin cəmi:</th>
                            <th>{$total_count}</th>
                            <th>{$total_sell|string_format:"%.2f"}</th>
                            <th>{$total_sell_azn|string_format:"%.2f"}</th>
                            <th></th>
                        </tr>
                        <tr>
                            <th colspan="5">Bütün obyektlər üzrə cəmi:</th>
                            <th>{$summary.count}</th>
                            <th>{$summary.total_sell_price}</th>
                            <th>{$summary.total_sell_price_azn}</th>
                            <th></th>
                        </tr>
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
                                    <li class="active"><a href="{$app_url}/report/service/{$p.page}">{$p.title}</a></li>
                                {else}
                                    <li><a href="{$app_url}/report/service/{$p.page}">{$p.title}</a></li>
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