{extends file="base.tpl"}

{block name="page-title"}
    :: Magazin Satiş
{/block}

{block name="dashboard"}

    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="{$app_url}"><span class="glyphicon glyphicon-home"></span></a></li>
                <li class="active">Kontragentə geri</li>
            </ol>
        </div><!--/.row-->

        <div class="row col-no-gutter-container">
            <div class="col-xs-12 col-md-12 sale">
                <form class="form-inline" method="post" id="sell_goods_code">
                    <input type="hidden" id="user_id" value="{$user.id}">
                    <input type="hidden" id="subject_id" value="{$subject.id}">
                    <div class="form-group">
                        <input type="text" name="return_contragent_code" class="form-control col-md-7" id="return_contragent_code" autocomplete="off" placeholder="Kod və ya barkod ilə axtar">
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-danger" id="return-contragent-search">AXTAR</button>
                    </div>
                </form>
            </div>

            <div class="col-xs-12 col-md-12 col-no-gutter" id="sell-search-table" style="display: none">
                <h3>{$subject.name} <sup><a href="javascript:void(0)" id="close-search-table">x</a></sup><br><small>{$subject.description}</small></h3>
                <div class="table-responsive table_scroll">
                    <table class="table table-bordered">
                        <tr>
                            <th>#</th>
                            <th>Müştəri</th>
                            <th>Kod</th>
                            <th>Barkod(IMEI)</th>
                            <th>Malın adı</th>
                            <th>Sayı</th>
                            <th>Alış qıyməti</th>
                            <th>Satış qıyməti</th>
                            <th></th>
                        </tr>
                        <tr></tr>
                        <tbody id="sell-search-table-tbody">

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-xs-12 col-md-2">
            </div>

            <div class="col-xs-12 col-md-8" style="margin-top: 20px;">

                <form class="form-horizontal" id="returnContragentInvoiceForm">

                    <div class="collapse in" id="collapseExampleShops">
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Qaimə №:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="{$invoice.serial}" disabled>
                                <input type="hidden" class="form-control" name="invoice_serial" value="{$invoice.serial}">
                                <input type="hidden" class="form-control" name="invoice_type" value="{$invoice.type}">
                                <input type="hidden" class="form-control" name="operator" value="{$user.operator.id}">
                                <input type="hidden" class="form-control" name="subject_id" value="{$subject.id}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Kontragent:</label>
                            <div class="col-sm-10">
                                <select name="contragent_id" id="contragent_id" class="form-control">
                                    {foreach from=$contragents item=contragent}
                                        <option value="{$contragent.id}">{$contragent.name}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>

                        <!--<div class="form-group">
                            <label for="" class="col-sm-2 control-label">Məbləğ:</label>
                            <div class="col-sm-10">
                                <span class="total_sell_price">0</span> AZN
                                <input type="hidden" class="form-control" name="amount" id="total_amount">
                            </div>
                        </div>-->

                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Tarix:</label>
                            <div class="col-sm-10">
                                <input type="text" id="date" name="date" class="form-control datepicker" value="{$currentDate}">
                            </div>
                        </div>
                        <input type="hidden" name="user_id" value="{$user.id}">
                        <input type="hidden" name="subject_id" value="{$subject.id}">

                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Qeyd:</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" rows="3" name="notes"></textarea>
                            </div>
                        </div>
                    </div>
                     <input type="hidden" class="form-control" name="amount" id="total_amount">
                     <input type="hidden" class="form-control" name="amount_azn" id="total_amount_azn">
                </form>
            </div>

            <div class="col-xs-12 col-md-2">
            </div>

            <div class="col-xs-12 col-md-12 col-no-gutter sale">
                <h3>KONTRAGENTƏ GERİ</h3>
                <div class="table-responsive">
                    <form id="returnContragentForm">
                        <table class="table table-bordered">
                            <tr>
                                <th>#</th>
                                <th>Kod</th>
                                <th>Barkod(IMEI)</th>
                                <th>Malın adı</th>
                                <th>Sayı</th>
                                <th>Alış qıyməti (USD)</th>
                                <th>Alış qıyməti (AZN)</th>
                                <th>Satış qıyməti (USD)</th>
                                <th>Satış qıyməti (AZN)</th>
                                <th>Cəmi (USD)</th>
                                <th>Cəmi (AZN)</th>
                                <th></th>
                            </tr>
                            <tbody id="return-pendings">
                            <tr></tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="9">Cəmi:</td>
                                <td><span class="total_all_price"></span></td>
                                <td><span class="total_all_price_azn"></span></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="12">
                                    <button type="button" disabled id="confirm-return-contragent" class="btn btn-danger col-md-3 f-right">TƏSDİQLƏ</button>
                                </td>
                            </tr>
                            </tfoot>

                        </table>
                    </form>
                </div>
            </div>

            <div class="modal fade" id="goods-info" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">

                    </div>
                </div>
            </div>

        </div><!--/.row-->


    </div>	<!--/.main-->

{/block}