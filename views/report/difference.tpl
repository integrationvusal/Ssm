{extends file="base.tpl"}

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
                <li class="active">Ümumi gəlir üzrə hesabat</li>
            </ol>
        </div><!--/.row-->

        <div class="row col-no-gutter-container">
            <div class="col-xs-12 col-md-2">
            </div>
            <div class="col-xs-12 col-md-8 client">
                {if $searchData}
                    <form class="form-horizontal" action="{$app_url}/report/difference" method="post">
                        <input type="hidden" name="user_id" id="user_id" value="{$user.id}">
                        <input type="hidden" name="subject_id" id="subject_id" value="{$subject.id}">
                        <div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Tarix:</label>
                                <div class="col-md-5"><input type="text" class="datepicker form-control" name="date_from" value="{$searchData.date_from}"></div>
                                <div class="col-md-5"><input type="text" class="datepicker form-control" name="date_to" value="{$searchData.date_to}"></div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Obyekt:</label>
                                <div class="col-md-5"><input type="text" class="subject_search form-control"></div>
                                <div class="col-md-5">
                                    <select name="subject_id" id="subject_sb" class="form-control">
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
                                <label for="" class="col-md-2 control-label">Malın adı(kodu, barkodu):</label>
                                <div class="col-md-5"><input type="text" class="goods_search form-control"></div>
                                <div class="col-md-5">
                                    <select name="goods_id" id="goods_id" class="form-control">
                                        <option value="0">Malı seç</option>
                                        {foreach from=$goods item=g}
                                            {if $searchData.goods_id == $g.goods_id}
                                                <option value="{$g.goods_id}" selected>{$g.short_info}</option>
                                            {else}
                                                <option value="{$g.goods_id}">{$g.short_info}</option>
                                            {/if}
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <input type="submit" class="btn btn-danger form-control" name="report_search" value="AXTAR">
                        </div>
                    </form>
                {else}
                    <form class="form-horizontal" action="{$app_url}/report/difference" method="post">
                        <input type="hidden" name="user_id" id="user_id" value="{$user.id}">
                        <input type="hidden" name="subject_id" id="subject_id" value="{$subject.id}">
                        <div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Tarix:</label>
                                <div class="col-md-5"><input type="text" class="datepicker form-control" name="date_from"></div>
                                <div class="col-md-5"><input type="text" class="datepicker form-control" name="date_to"></div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Obyekt:</label>
                                <div class="col-md-5"><input type="text" class="subject_search form-control"></div>
                                <div class="col-md-5">
                                    <select name="subject_id" id="subject_sb" class="form-control">
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
                                <label for="" class="col-md-2 control-label">Malın adı(kodu, barkodu):</label>
                                <div class="col-md-5"><input type="text" class="goods_search form-control"></div>
                                <div class="col-md-5">
                                    <select name="goods_id" id="goods_id" class="form-control">
                                        <option value="0">Malı seç</option>
                                        {foreach from=$goods item=g}
                                            <option value="{$g.goods_id}">{$g.short_info}</option>
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
                <h3>Mallar <a class="btn btn-info" href="{$app_url}/report/difference/print"><span class="glyphicon glyphicon-print"></span></a></h3>
                <div class="table-responsive">
                    <table class="table table-bordered" style="width:100%;">
                        <tr>
                            <th>#</th>
                            <th>Operator</th>
                            <th>Ad</th>
                            <th>Kod</th>
                            <th>Barkod</th>
                            <th>Sayı</th>
                            <th>Qaimə №</th>
                            <th>Alış (USD)</th>
                            <th>Satış (USD)</th>
                            <th>Alış (AZN)</th>
                            <th>Satış (AZN)</th>
                            <th>Cəmi alış (USD)</th>
                            <th>Cəmi satış (USD)</th>
                            <th>Cəmi alış (AZN)</th>
                            <th>Cəmi satış (AZN)</th>
                            <th width="90">Tarix</th>
                        </tr>
                        {assign var=total_count value=0}
                        {assign var=total_count_azn value=0}
                        {assign var=total_buy value=0}
                        {assign var=total_buy_azn value=0}
                        {assign var=total_sell value=0}
                        {assign var=total_sell_azn value=0}
                        {assign var="i" value=(($page-1) * $limit)}
                        {foreach from=$invoices item=invoice}
                            {assign var="i" value=$i+1}
                            <tr>

                                <td>{$i}</td>
                                <td>{if empty($invoice.operator_name)}{$user.name}{else}{$invoice.operator_name}{/if}</td>
                                <th><a href="javascript:void(0)" data-toggle="modal" data-target="#goods-info" class="goods-info" data-rel="{$invoice.store_item_id}">{$invoice.short_info}</a></th>
                                <td>{$invoice.goods_code}</td>
                                <td>{$invoice.barcode}</td>
                                <td>{$invoice.count}</td>
                                <th><a href="javascript:void(0)"
                                       class="invoice-details"
                                       data-invoice-id="{$invoice.invoice_id}"
                                       data-invoice-type="{$invoice.invoice_type}">{$invoice.serial}</a></th>
                                <td>{if $invoice.currency}{$invoice.buy_price}{/if}</td>
                                <td>{if $invoice.currency}{$invoice.sell_price}{/if}</td>
                                <td>{if !$invoice.currency}{$invoice.buy_price}{/if}</td>
                                <td>{if !$invoice.currency}{$invoice.sell_price}{/if}</td>
                                <td>{if $invoice.currency}{$invoice.buy_price * $invoice.count}{/if}</td>
                                <td>{if $invoice.currency}{$invoice.sell_price * $invoice.count}{/if}</td>
                                <td>{if !$invoice.currency}{$invoice.buy_price * $invoice.count}{/if}</td>
                                <td>{if !$invoice.currency}{$invoice.sell_price * $invoice.count}{/if}</td>

                                {if $invoice.currency}
                                    {$total_count = $total_count + $invoice.count}
                                    {$total_buy = $total_buy + ($invoice.buy_price * $invoice.count)}
                                    {$total_sell=$total_sell + ($invoice.sell_price * $invoice.count)}
                                {else}
                                    {$total_count_azn = $total_count_azn + $invoice.count}
                                    {$total_buy_azn = $total_buy_azn + ($invoice.buy_price * $invoice.count)}
                                    {$total_sell_azn=$total_sell_azn + ($invoice.sell_price * $invoice.count)}
                                {/if}

                                <td>{$invoice.date|substr:0:10}</td>
                            </tr>
                        {/foreach}
                        <tr>
                            <th colspan="5">Göstərilənlərin cəmi: (USD)</th>
                            <th>{$total_count}</th>
                            <th colspan="5"></th>
                            <th>{$total_buy}</th>
                            <th>{$total_sell}</th>
                            <th colspan="3"></th>
                        </tr>
                        <tr>
                            <th colspan="5">Göstərilənlərin cəmi: (AZN)</th>
                            <th>{$total_count_azn}</th>
                            <th colspan="7"></th>
                            <th>{$total_buy_azn}</th>
                            <th>{$total_sell_azn}</th>
                            <th></th>
                        </tr>
                        <tr>
                            <th colspan="5">Göstərilənlərin cəmi üzrə gəlir: (USD)</th>
                            <th>{$total_count}</th>
                            <th colspan="5"></th>
                            <th colspan="2">{$total_sell - $total_buy}</th>
                            <th colspan="3"></th>
                        </tr>
                        <tr>
                            <th colspan="5">Göstərilənlərin cəmi üzrə gəlir: (AZN)</th>
                            <th>{$total_count_azn}</th>
                            <th colspan="7"></th>
                            <th colspan="2">{$total_sell_azn - $total_buy_azn}</th>
                            <th></th>
                        </tr>
                        <tr>
                            <th colspan="5">Cəmi: (USD)</th>
                            <th>{$summary.count}</th>
                            <th colspan="5"></th>
                            <th>{$summary.total_buy_price}</th>
                            <th>{$summary.total_sell_price}</th>
                            <th colspan="3"></th>
                        </tr>
                        <tr>
                            <th colspan="5">Cəmi: (AZN)</th>
                            <th>{$summary.count_azn}</th>
                            <th colspan="7"></th>
                            <th>{$summary.total_buy_price_azn}</th>
                            <th>{$summary.total_sell_price_azn}</th>
                            <th></th>
                        </tr>
                        <tr>
                            <th colspan="5">Cəmi gəlir: (USD)</th>
                            <th>{$summary.count}</th>
                            <th colspan="5"></th>
                            <th colspan="2">{$summary.total_sell_price - $summary.total_buy_price}</th>
                            <th colspan="3"></th>
                        </tr>
                        <tr>
                            <th colspan="5">Cəmi gəlir: (AZN)</th>
                            <th>{$summary.count_azn}</th>
                            <th colspan="7"></th>
                            <th colspan="2">{$summary.total_sell_price_azn - $summary.total_buy_price_azn}</th>
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
                                    <li class="active"><a href="{$app_url}/report/difference/{$p.page}">{$p.title}</a></li>
                                {else}
                                    <li><a href="{$app_url}/report/difference/{$p.page}">{$p.title}</a></li>
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