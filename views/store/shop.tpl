{extends file="base.tpl"}

{block name="page-title"}
    :: {$messages.serv_net.title}
{/block}

{block name="dashboard"}

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">

        <div class="row">
            <ol class="breadcrumb">
                <li><a href="{$app_url}"><span class="glyphicon glyphicon-home"></span></a></li>
                <li class="active">Mallar</li>
            </ol>
        </div><!--/.row-->

        <div class="row col-no-gutter-container">

            <div class="col-xs-12 col-md-12 col-no-gutter">
                <div class="col-xs-12 col-md-2">
                </div>
                <div class="col-xs-12 col-md-8">
                    {if $permissions.store_create}
                    <button type="button" id="store_collapse" class="btn btn-success btn-lg btn-block" data-toggle="collapse" data-target="#collapsable" aria-expanded="false" aria-controls="collapseExample">ƏLAVƏ ET</button>
                    {/if}
                </div>
                <div class="col-xs-12 col-md-2">
                </div>
            </div>

            <div class="collapse {if isset($pendingGoods)}in{/if}" id="collapsable">

                <div class="col-xs-12 col-md-12 col-no-gutter">
                    <div class="col-xs-12 col-md-2">
                    </div>
                    <div class="col-xs-12 col-md-8 store">

                        {if isset($pendingGoods)}
                            <form class="form-horizontal">
                                <input type="hidden" id="user_id" value="{$user.id}">
                                <input type="hidden" id="operator" value="{$operator}">
                                <input type="hidden" id="subject_id" value="{$subject.id}">
                                <input type="hidden" id="goods_type" value="{$subject.goods_type}">

                                <div class="form-group invoice_serial">
                                    <label class="control-label col-sm-2" for="">Qaimə №:</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="invoice_serial" class="form-control" value="{$invoice.serial}" disabled>
                                        <input type="hidden" id="invoice_type" value="{$invoiceType}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="">Kontragent:</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" id="contragent_id" disabled>
                                            <option value="{$contragent.id}">{$contragent.name}</option>
                                        </select>
                                        <input type="hidden" id="contragent" value="{$contragent.name}">
                                    </div>
                                </div>

                                 <div class="form-group">
                                    <label class="control-label col-sm-2" for="">Valyuta:</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" id="currency_id">
                                            <option accesskey="0" value="0">AZN</option>
                                            {foreach from=$currencies item=v}
                                                <option accesskey="{$v.value}" value="{$v.id}">{$v.name}</option>
                                            {/foreach}
                                        </select>
                                        <input type="hidden" id="currency_archive" value="0">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="">Tarix:</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="date" class="form-control datepicker" value="{$date}" disabled>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="">Qeydlər:</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="notes" cols="10" rows="5" disabled>{$notes}</textarea>
                                    </div>
                                </div>
                            </form>
                        {else}
                            <form class="form-horizontal">
                                <input type="hidden" id="user_id" value="{$user.id}">
                                <input type="hidden" id="operator" value="{$operator}">
                                <input type="hidden" id="subject_id" value="{$subject.id}">
                                <input type="hidden" id="goods_type" value="{$subject.goods_type}">

                                <div class="form-group invoice_serial">
                                    <label class="control-label col-sm-2" for="">Qaimə №:</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="invoice_serial" class="form-control" value="{$nextInvoiceNumber}" disabled>
                                        <input type="hidden" id="invoice_type" value="{$invoiceType}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="">Kontragent:</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" id="contragent_id">
                                            {foreach from=$contragents item=c}
                                                <option value="{$c.id}">{$c.name}</option>
                                            {/foreach}
                                        </select>
                                        <input type="hidden" id="contragent" value="{$contragents[0].name}">
                                    </div>
                                </div>

                                 <div class="form-group">
                                    <label class="control-label col-sm-2" for="">Valyuta:</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" id="currency_id">
                                            <option accesskey="0" value="0">AZN</option>
                                            {foreach from=$currencies item=v}
                                                <option accesskey="{$v.value}" value="{$v.id}">{$v.name}</option>
                                            {/foreach}
                                        </select>
                                        <input type="hidden" id="currency_archive" value="0">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="">Tarix:</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="date" class="form-control datepicker" value="{$date}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="">Qeydlər:</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="notes" cols="10" rows="5"></textarea>
                                    </div>
                                </div>
                            </form>
                        {/if}
                    </div>
                    <div class="col-xs-12 col-md-2">
                    </div>
                </div>

                <div class="col-xs-12 col-md-12 col-no-gutter">
                    <div class="table-responsive">
                        <table class="table table-bordered" {if !$permissions.buy_price}data-exclude="5"{/if}>
                            <tr>
                                <th>Malın kodu və ya barkodu</th>
                                <th class="col-md-2">Uyğun malı seçin</th>
                                <th>Barkod(İMEİ)</th>
                                <th>&nbsp;&nbsp;&nbsp;&nbsp;Sayı&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                {if $permissions.buy_price}<th>Alış qiyməti (<span class="live-currency">AZN</span>)</th>{/if}
                                <th>Satış qiyməti (<span class="live-currency">AZN</span>)</th>
                                {if $permissions.buy_price}<th>Cəmi alış qiyməti (<span class="live-currency">AZN</span>)</th>{/if}
                                <th>Cəmi satış qiyməti (<span class="live-currency">AZN</span>)</th>
                                <th></th>
                            </tr>
                            <tr>
                                <td>
                                    <input type="text" class="form-control" id="goods_code" autocomplete="off">
                                    <input type="hidden" id="goods_code_val" value="">
                                </td>
                                <td width="50">
                                    <select class="form-control" name="goods_id" id="goods_id">
                                        <option value="0">Malı seç</option>
                                    </select>
                                    <select name="goods_description" id="goods_description" style="display: none"></select>
                                </td>
                                <td><input type="text" class="form-control" name="barcode" id="barcode"></td>
                                <td><input type="number" min="0"  class="form-control" id="count" autocomplete="off" value="1"></td>
                                {if $permissions.buy_price}<td><input type="number" step="0.01" min="0" class="form-control" id="buy_price" autocomplete="off" value="0"></td>{/if}
                                <td><input type="number" step="0.01" min="0" class="form-control" id="sell_price" autocomplete="off" value="0"></td>
                                {if $permissions.buy_price}<td><input type="text" step="0.01" min="0" class="form-control" id="total_buy_price" autocomplete="off" value="0" disabled></td>{/if}
                                <td><input type="text" step="0.01" min="0" class="form-control" id="total_sell_price" autocomplete="off" value="0" disabled></td>
                                <td>
                                    <button class="btn btn-danger" id="add_goods">ƏLAVƏ ET</button>
                                </td>
                            </tr>
                        </table>
                    </div>

                </div>

                <div class="col-xs-12 col-md-12 col-no-gutter">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Kod</th>
                                    <th>Barkod</th>
                                    <th>Adı</th>
                                    <th>Sayı</th>
                                    {if $permissions.currency}<th>Alış qiyməti (USD)</th>{/if}
                                    {if $permissions.buy_price}<th>Alış qiyməti (AZN)</th>{/if}
                                    {if $permissions.currency}<th>Satış qiyməti (USD)</th>{/if}
                                    <th>Satış qiyməti (AZN)</th>
                                    {if $permissions.currency}<th>Cəmi alış qiyməti (USD)</th>{/if}
                                    {if $permissions.buy_price}<th>Cəmi alış qiyməti (AZN)</th>{/if}
                                    {if $permissions.currency}<th>Cəmi satış qiyməti (USD)</th>{/if}
                                    <th>Cəmi satış qiyməti (AZN)</th>
                                    <th>Sil</th>
                                </tr>
                            </thead>
                            <tbody id="goods_preview" {if !$permissions.buy_price}data-exclude="6,8"{/if}>
                            {assign var="total_buy" value=0}
                            {assign var="total_buy_azn" value=0}
                            {assign var="total_sell" value=0}
                            {assign var="total_sell_azn" value=0}
                            {assign var="i" value=0}
                                {if isset($pendingGoods)}
                                    {foreach from=$pendingGoods item=pgoods}
                                    {assign var="i" value=$i+1}
                                        <tr>
                                            <th><span class="pending_object">{$i}</span></th>
                                            <th>{$pgoods.goods_code}</th>
                                            <th>{$pgoods.barcode}</th>
                                            <th>{$pgoods.short_info}</th>
                                            <th>{$pgoods.pending_count}</th>

                                            {if $permissions.currency}
                                                <th>{if $pgoods.currency}{$pgoods.buy_price}{/if}</th>
                                            {/if}

                                            {if $permissions.buy_price}
                                                <th>{if !$pgoods.currency}{$pgoods.buy_price}{/if}</th>
                                            {/if}

                                            {if $permissions.currency}
                                                <th>{if $pgoods.currency}{$pgoods.sell_price}{/if}</th>
                                            {/if}

                                            <th>{if !$pgoods.currency}{$pgoods.sell_price}{/if}</th>

                                            {if $permissions.currency}
                                                <th>{if $pgoods.currency}{($pgoods.buy_price * $pgoods.pending_count)|string_format:"%.2f"}{/if}</th>
                                            {/if}

                                            {if $permissions.buy_price}
                                                <th>{if !$pgoods.currency}{$pgoods.buy_price * $pgoods.pending_count}{/if}</th>
                                            {/if}

                                            {if $pgoods.currency}
                                                {$total_buy = $total_buy + ($pgoods.buy_price * $pgoods.pending_count)}
                                            {else}
                                                {$total_buy_azn = $total_buy_azn + ($pgoods.buy_price * $pgoods.pending_count)}
                                            {/if}

                                            {if $permissions.currency}
                                                <th>{if $pgoods.currency}{($pgoods.sell_price * $pgoods.pending_count)|string_format:"%.2f"}{/if}</th>
                                            {/if}

                                            <th>{if !$pgoods.currency}{$pgoods.sell_price * $pgoods.pending_count}{/if}</th>

                                            {if $pgoods.currency}
                                                {$total_sell = $total_sell + ($pgoods.sell_price * $pgoods.pending_count)}
                                            {else}
                                                {$total_sell_azn = $total_sell_azn + ($pgoods.sell_price * $pgoods.pending_count)}
                                            {/if}

                                            <th>
                                                <span class="delete_button_container"  {if $pendingGoodsCount < 2}style="display:none"{/if}>
                                                    <button class='btn btn-danger delete_goods'
                                                        data-user-id='{$pgoods.user_id}'
                                                        data-item-id='{$pgoods.id}'
                                                        data-subject-id='{$pgoods.subject_id}'
                                                        data-goods-id='{$pgoods.goods_id}'
                                                        data-buy-price='{$pgoods.buy_price}'
                                                        data-sell-price='{$pgoods.sell_price}'
                                                        data-invoice-id='{$pgoods.invoice_id}'
                                                        data-count='{$pgoods.pending_count}'
                                                        data-currency='{$pgoods.currency}'
                                                        >Sil</button>
                                                </span>
                                            </th>
                                        </tr>
                                    {/foreach}
                                {else}
                                    <tr></tr>
                                {/if}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-left" colspan="{if $permissions.currency && $permissions.buy_price}9{elseif $permissions.currency || $permissions.buy_price}8{else}7{/if}">Cəmi:</td>
                                    {if $permissions.currency}
                                        <td>
                                            <span id="result_buy_price">{if $total_buy > 0}{$total_buy}{/if}</span>
                                        </td>
                                    {/if}

                                    {if $permissions.buy_price}
                                        <td>
                                            <span id="result_buy_price_azn">{if $total_buy_azn > 0}{$total_buy_azn}{/if}</span>
                                        </td>
                                    {/if}

                                    {if $permissions.currency}
                                        <td>
                                            <span id="result_sell_price">{if $total_sell > 0}{$total_sell}{/if}</span>
                                        </td>
                                    {/if}

                                    <td><span id="result_sell_price_azn">{if $total_sell > 0}{$total_sell_azn}{/if}</span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="{if $permissions.currency && $permissions.buy_price}14{elseif $permissions.buy_price}12{else}10{/if}">
                                        <form action="{$app_url}/store/reject" method="post" class="activeforms">
                                            <input type="hidden" name="user_id" value="{$user.id}">
                                            <input type="hidden" name="subject_id" value="{$subject.id}">
                                            {if isset($pendingGoods)}
                                                <input type="hidden" name="contragent_id" class="contragent_id" value="{$contragent.id}">
                                                {foreach from=$invoice.ids key=k item=pg}
                                                    <input type="hidden" name="invoice_id[{$k}]" class="invoice_id" value="{$pg}">
                                                    <input type="hidden" name="invoice_archive[{$k}]" value="{$invoice.archives.$k}">
                                                {/foreach}
                                            {else}
                                                <input type="hidden" name="contragent_id" class="contragent_id">
                                            {/if}

                                            <button type="submit" class="btn btn-warning col-md-3 f-left">İMTİNA</button>
                                        </form>
                                        <form action="{$app_url}/store/approve" class="activeforms" method="post" id="approve_form">
                                            <input type="hidden" name="user_id" value="{$user.id}">
                                            <input type="hidden" name="subject_id" value="{$subject.id}">
                                            {if isset($pendingGoods)}
                                                <input type="hidden" name="contragent_id" class="contragent_id" value="{$contragent.id}">
                                                {foreach from=$invoice.ids key=k item=pg}
                                                    <input type="hidden" name="invoice_id[{$k}]" class="invoice_id" value="{$pg}">
                                                    <input type="hidden" name="invoice_archive[{$k}]" value="{$invoice.archives.$k}">
                                                {/foreach}
                                            {else}
                                                <input type="hidden" name="contragent_id" class="contragent_id">
                                            {/if}
                                            <button type="submit" class="btn btn-danger col-md-3 f-right">TƏSDİQLƏ</button>
                                        </form>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>

            </div>

            <div class="col-xs-12 col-md-12 col-no-gutter">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>#</th>
                            <th>Kod</th>
                            <th>Barkod(IMEI)</th>
                            <th>Ad</th>
                            <th>Say</th>
                            {if $permissions.currency}<th>Alış qiyməti (Valyuta)<br></th>{/if}
                            {if $permissions.buy_price}<th>Alış qiyməti (AZN)<br></th>{/if}
                            {if $permissions.currency}<th>Satış qiyməti<br>(Valyuta)</th>{/if}
                            <th>Satış qiyməti<br>(AZN)</th>
                            {if $permissions.currency}<th>Cəmi alış qiyməti (Valyuta)<br></th>{/if}
                            {if $permissions.buy_price}<th>Cəmi alış qiyməti (AZN)<br></th>{/if}
                            {if $permissions.currency}<th>Cəmi satış qiyməti<br>(Valyuta)</th>{/if}
                            <th>Cəmi satış qiyməti<br>(AZN)</th>
                            <th></th>
                        </tr>
                        <tr>
                            {if $search_params == null}
                                <form action="{$app_url}/store" method="post">
                                    <td></td>
                                    <input type="hidden" name="user_id" value="{$user.id}">
                                    <input type="hidden" name="subject_id" value="{$subject.id}">
                                    <td><input type="text" class="form-control" name="search_code"></td>
                                    <td><input type="text" class="form-control" name="search_barcode"></td>
                                    <td><input type="text" class="form-control" name="search_short_info"></td>
                                    <td><input type="text" class="form-control" name="search_count"></td>
                                    {if $permissions.currency}<td><input type="text" class="form-control" name="search_currency"></td>{/if}
                                    {if $permissions.buy_price}<td><input type="text" class="form-control" name="search_buy_price"></td>{/if}
                                    {if $permissions.currency}<td><input type="text" class="form-control" name="search_sell_currency"></td>{/if}
                                    <td><input type="text" class="form-control" name="search_sell_price"></td>
                                    <td colspan="{if $permissions.buy_price && $permissions.currency}5{elseif $permissions.buy_price || $permissions.currency}4{else}3{/if}"><input type="submit" class="btn btn-danger form-control" name="store_search" value="AXTAR"></td>
                                </form>
                            {else}
                                <form action="{$app_url}/store" method="post">
                                    <td></td>
                                    <input type="hidden" name="user_id" value="{$user.id}">
                                    <input type="hidden" name="subject_id" value="{$subject.id}">
                                    <td><input type="text" class="form-control" name="search_code" value="{$search_params.search_code}"></td>
                                    <td><input type="text" class="form-control" name="search_barcode" value="{$search_params.search_barcode}"></td>
                                    <td><input type="text" class="form-control" name="search_short_info" value="{$search_params.search_short_info}"></td>
                                    <td><input type="text" class="form-control" name="search_count" value="{$search_params.search_count}"></td>
                                    {if $permissions.currency}<td><input type="text" class="form-control" name="search_currency" value="{$search_params.search_currency}"></td>{/if}
                                    {if $permissions.buy_price}<td><input type="text" class="form-control" name="search_buy_price" value="{$search_params.search_buy_price}"></td>{/if}
                                    {if $permissions.currency}<td><input type="text" class="form-control" name="search_sell_currency" value="{$search_params.search_sell_currency}"></td>{/if}
                                    <td><input type="text" class="form-control" name="search_sell_price" value="{$search_params.search_sell_price}"></td>
                                    <td colspan="{if $permissions.buy_price && $permissions.currency}5{elseif $permissions.buy_price || $permissions.currency}4{else}3{/if}"><input type="submit" class="btn btn-danger form-control" name="store_search" value="AXTAR"></td>
                                </form>
                            {/if}
                        </tr>

                        {assign var="i" value=($page-1)*$limit}

                        {assign var=totalCount value=0}
                        {assign var=totalBuyPrice value=0}
                        {assign var=totalBuyPriceAZN value=0}
                        {assign var=totalSellPrice value=0}
                        {assign var=totalSellPriceAZN value=0}
                        {foreach from=$goods item=g}
                            {assign var="i" value=$i+1}
                            <tr>
                                <td>{$i}</td>
                                <td>{$g.goods_code}</td>
                                <td>{$g.barcode}</td>
                                <td>
                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#goods-info" class="goods-info" data-rel="{$g.id}">{$g.short_info}</a>
                                </td>
                                <td>{$g.count}</td>{assign var=totalCount value=$totalCount + $g.count}
                                {if $permissions.currency}
                                    <td>
                                        {if !empty($g.currency)}
                                            {$g.buy_price} {$g.currency} <br/>
                                            <i class="glyphicon glyphicon-{$g.currency|lower} small-icon"></i> <font color="red">{$g.currency_archive}</font><br/>({$g.currency_value*$g.buy_price} AZN)
                                        {/if}
                                    </td>
                                {/if}
                                {if $permissions.buy_price}
                                    <td>{if empty($g.currency)} {$g.buy_price} {/if}</td>
                                {/if}

                                {if $permissions.currency}
                                    <td>
                                        {if !empty($g.currency)}
                                                {$g.sell_price} {$g.currency} <br/>
                                                <i class="glyphicon glyphicon-{$g.currency|lower} small-icon"></i> <font color="red">{$g.currency_archive}</font><br/>({$g.currency_value*$g.sell_price} AZN)
                                        {/if}
                                    </td>
                                {/if}

                                <td>{if empty($g.currency)}{$g.sell_price}{/if}</td>
                                {if $permissions.currency}
                                     <td>
                                        {if !empty($g.currency)}
                                            {number_format((float)($g.buy_price * $g.count), 2)}
                                            <i class="glyphicon glyphicon-{$g.currency|lower} small-icon"></i> <font color="red">{$g.currency_archive}</font><br/>({$g.currency_value * $g.buy_price * $g.count} AZN)
                                            {assign var=totalBuyPrice value=$totalBuyPrice + $g.buy_price * $g.count}
                                        {/if}
                                    </td>
                                {/if}
                                {if $permissions.buy_price}
                                    <td>
                                        {if empty($g.currency)}
                                            {$g.buy_price * $g.count}
                                            {assign var=totalBuyPriceAZN value=$totalBuyPriceAZN + $g.buy_price * $g.count}
                                        {/if}
                                    </td>
                                {/if}

                                {if $permissions.currency}
                                    <td>
                                        {if !empty($g.currency)}
                                            {number_format((float)($g.sell_price * $g.count), 2)}
                                            <i class="glyphicon glyphicon-{$g.currency|lower} small-icon"></i> <font color="red">{$g.currency_archive}</font><br/>({$g.currency_value * $g.sell_price * $g.count} AZN)
                                            
                                        {/if}
                                    </td>
                                {/if}

                                <td>{if empty($g.currency)}{$g.sell_price * $g.count}{/if}</td>

                                {if !empty($g.currency)}
                                    {$totalSellPrice = $totalSellPrice + $g.sell_price * $g.count}
                                {else}
                                    {$totalSellPriceAZN = $totalSellPriceAZN + $g.sell_price * $g.count}
                                {/if}

                                <td>
                                    <button class="btn btn-danger delete-goods" data-goods-count="{$g.count}" data-rel="{$g.goods_id}" data-buy-price="{$g.buy_price}" data-store-item-id="{$g.id}">Sil</button>
                                </td>
                            </tr>
                        {/foreach}
                            <tr>
                                <th colspan="4">Göstərilənlərin cəmi</th>
                                <th>{$totalCount}</th>
                                <th colspan="{if $permissions.buy_price && $permissions.currency}4{elseif $permissions.buy_price || $permissions.currency}3{else}2{/if}"></th>
                                {if $permissions.currency}<th>{number_format((float)$totalBuyPrice, 2)}</th>{/if}
                                {if $permissions.buy_price}<th>{$totalBuyPriceAZN}</th>{/if}

                                {if $permissions.currency}<th>{$totalSellPrice}</th>{/if}
                                <th>{$totalSellPriceAZN}</th>
                                <th></th>
                            </tr>
                            <tr>
                                <th colspan="4">Cəmi</th>
                                <th>{$summary.total_count}</th>
                                <th colspan="{if $permissions.buy_price && $permissions.currency}4{elseif $permissions.buy_price || $permissions.currency}2{else}1{/if}"></th>
                                {if $permissions.currency}<th>{$summary.total_buy_price}</th>{/if}
                                {if $permissions.buy_price}<th>{$summary.total_buy_price_azn}</th>{/if}

                                <th>{$summary.total_sell_price}</th>
                                {if $permissions.currency}<th>{$summary.total_sell_price_azn}</th>{/if}
                                <th></th>
                            </tr>
                    </table>
                </div>

            </div>

            {if $search_params == null}
            <div class="col-xs-12 col-md-4">
            </div>
            <div class="col-xs-12 col-md-4">
                <ul class="pagination">
                    {foreach from=$paginator item=p}
                        {if isset($p.disabled) && $p.disabled}
                            <li class="disabled"><a href="javascript:void(0)">{$p.title}</a></li>
                        {else}
                            {if isset($p.active) && $p.active}
                                <li class="active"><a href="{$app_url}/store/{$p.page}">{$p.title}</a></li>
                            {else}
                                <li><a href="{$app_url}/store/{$p.page}">{$p.title}</a></li>
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

    <div class="modal fade" id="goods-info" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="delete_modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Malların silinməsi</h4>
                </div>
                <div class="modal-body">
                    <form action="{$app_url}/store/_DG_deleteGoods" method="post" id="delete-form">
                        <label for="">Qaimə:</label>
                        <select name="invoice_id" class="form-control" required>

                        </select>
                        <div class="clearfix"></div>
                        <label for="">Silinəcək say Max(<span class="max_goods_count"></span>):</label>
                        <input type="hidden" name="goods_id" class="form-control">
                        <input type="hidden" name="user_id" value="{$user.id}">
                        <input type="hidden" name="subject_id" value="{$subject.id}">
                        <input type="hidden" name="invoice_detail_id">
                        <input type="hidden" name="store_item_id">
                        <input type="hidden" name="buy_price">
                        <input type="number" name="count" class="form-control" min="1" step="1" value="1">
                </div>
                <div class="modal-footer">
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-success form-control">Təsdiqlə</button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-danger form-control" data-dismiss="modal">İmtina et</button>
                    </div>
                </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <script>
        $(function(){
            var delete_goods_xhr = null
            $(".delete-goods").click(function(event) {

                var store_goods_count = $(event.target).attr("data-goods-count");
                $("#delete-form input[name='count']").removeAttr('max');
                $(".max_goods_count").html("");
                var goods_id = $(this).attr("data-rel");
                $("#delete-form input[name='goods_id']").val(goods_id);
                var buy_price = $(this).attr("data-buy-price");
                $("#delete-form input[name='buy_price']").val(buy_price);
                var store_item_id = $(this).attr("data-store-item-id");
                $("#delete-form input[name='store_item_id']").val(store_item_id);
                var data = $("#delete-form").serialize();
                if(delete_goods_xhr != null) delete_goods_xhr.abort();
                delete_goods_xhr = $.ajax({

                    url: '{$app_url}' + '/store/_DG_getAllInvoices',
                    type: 'POST',
                    data: data,
                    dataType: 'JSON',
                    beforeSend: function(xhr){
                        Loader.lStart(xhr);
                    },
                    success: function(response){
                        Loader.lStop();
                        $("#delete-form select[name='invoice_id']").html("");
                        $("#delete-form select[name='invoice_id']").append('<option value="0">Seç</option>');
                        $(response).each(function(key, val) {
                            $("#delete-form select[name='invoice_id']").append('<option value="' + val.invoice_id + '" data-rel="' + val.goods_count + '" data-detail-id="' + val.invoice_detail_id + '">' + val.invoice_info + '</option>');
                        });

                        $("#delete-form select[name='invoice_id']").change(function(event) {

                            if($(this).val() > 0){
                                $("#delete-form select[name='invoice_id']").css("border", "1px solid lightgray");
                                var goods_count = $("#delete-form select[name='invoice_id'] option:selected").attr("data-rel");
                                if(parseInt(goods_count) > parseInt(store_goods_count)) goods_count = store_goods_count;
                                $("#delete-form input[name='count']").attr('max', goods_count);
                                var detail_id = $("#delete-form select[name='invoice_id'] option:selected").attr("data-detail-id");
                                $("#delete-form input[name='invoice_detail_id']").val(detail_id);
                                $(".max_goods_count").html(goods_count);
                            }

                        });

                        $("#delete_modal").modal("show");
                    }

                });
            });

            $("#delete-form").submit(function(event){
                event.preventDefault();
                if($("#delete-form select[name='invoice_id']").val() > 0){
                    $("#delete-form").unbind('submit');
                    $(this).submit()
                } else {
                    $("#delete-form select[name='invoice_id']").css("border", "1px solid red");
                }

            });
        });
    </script>
{/block}