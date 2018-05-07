{extends file="base.tpl"}

{block name="page-title"}
    :: Bonus kartlar üzrə hesabat
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
                <li class="active">Bonus kartlar üzrə hesabat</li>
            </ol>
        </div><!--/.row-->

        <div class="row col-no-gutter-container" style="float: left">
            <div class="col-xs-12 col-md-2">
            </div>
            <div class="col-xs-12 col-md-8 client">
                {if $searchData}
                    <form class="form-horizontal" action="{$app_url}/report/discount" method="post">
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
                                    <select name="subject_sb" id="subject_sb" class="form-control">
                                        <option value="%">Obyekti seç</option>
                                        {foreach from=$subjects item=sub}
                                            {if $searchData.subject_sb == $sub.id}
                                                <option selected value="{$sub.id}">{$sub.name}</option>
                                            {else}
                                                <option value="{$sub.id}">{$sub.name}</option>
                                            {/if}
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Kartın nömrəsi:</label>
                                <div class="col-md-10"><input type="text" name="card_number" class="form-control" value="{$searchData.card_number}"></div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Kartın növü:</label>
                                <div class="col-md-10">
                                    <select class="form-control" name="card_type">
                                        <option value="%">Seç</option>
                                        {foreach from=$discount_card_types key=k item=card_type}
                                            {if $searchData.card_type == $k}
                                                <option selected value="{$k}">{$card_type}</option>
                                            {else}
                                                <option value="{$k}">{$card_type}</option>
                                            {/if}
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Əməliyyat növü:</label>
                                <div class="col-md-10">
                                    <select class="form-control" name="operation_type">
                                        <option value="%">Seç</option>
                                        <option value="+" {if $searchData.operation_type == "+"}selected{/if}>+</option>
                                        <option value="-" {if $searchData.operation_type == "-"}selected{/if}>-</option>
                                        <option value="0" {if $searchData.operation_type == "0"}selected{/if}>Ləğv</option>
                                    </select>
                                </div>
                            </div>
                            <input type="submit" class="btn btn-danger form-control" name="report_search" value="AXTAR">
                        </div>
                    </form>
                {else}
                    <form class="form-horizontal" action="{$app_url}/report/discount" method="post">
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
                                    <select name="subject_sb" id="subject_sb" class="form-control">
                                        <option value="%">Obyekti seç</option>
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
                                <label for="" class="col-md-2 control-label">Kartın nömrəsi:</label>
                                <div class="col-md-10"><input type="text" name="card_number" class="form-control"></div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Kartın növü:</label>
                                <div class="col-md-10">
                                    <select class="form-control" name="card_type">
                                        <option value="%">Seç</option>
                                        {foreach from=$discount_card_types key=k item=card_type}
                                            <option value="{$k}">{$card_type}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-md-2 control-label">Əməliyyat növü:</label>
                                <div class="col-md-10">
                                    <select class="form-control" name="operation_type">
                                        <option value="%">Seç</option>
                                        <option value="+">+</option>
                                        <option value="-">-</option>
                                        <option value="0">Ləğv</option>
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
                <h3>Kartlar <a class="btn btn-info" href="{$app_url}/report/discount/print"><span class="glyphicon glyphicon-print"></span></a></h3>
                <div class="table-responsive">
                    <table class="table table-bordered" style="width:100%;">
                        <tr>
                            <th>#</th>
                            <th>Kartın nömrəsi</th>
                            <th>Qaimə</th>
                            <th>Müştəri</th>
                            <th>Kartın növü</th>
                            <th>Əməliyyat</th>
                            <th>Əvvəlki bonus</th>
                            <th>Yeni bonus</th>
                            <th>Ümumi məbləğ</th>
                            <th>Endirim məbləği</th>
                            <th>Qalıq</th>
                            <th>Valyuta</th>
                            <th>Tarix</th>
                        </tr>
                        {assign var="i" value=(($page-1) * $limit)}
                        {foreach from=$histories item=history}
                            {assign var="i" value=$i+1}
                            <tr>

                                <td>{$i}</td>
                                <td>{$history.card_number}</td>
                                <th><a href="javascript:void(0)"
                                       class="invoice-details"
                                       data-invoice-id="{$history.invoice_id}"
                                       data-invoice-type="{$history.invoice_type}">{$history.invoice_serial}</a></th>
                                <td>{$history.card_user}</td>
                                <td>{$discount_card_types[$history.card_type]}</td>
                                {if $history.operation_type == '0'}
                                    <td>Ləğv</td>
                                {else}
                                    <td>{$history.operation_type}</td>
                                {/if}
                                <td>{$history.previous_discount}</td>
                                <td>{$history.current_discount}</td>
                                <td>{$history.total_amount}</td>
                                <td>{$history.discounted_amount}</td>
                                <td>{$history.remaining_amount}</td>
                                <td>{$history.currency}</td>
                                <td>{$history.created_at}</td>

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
                                    <li class="active"><a href="{$app_url}/report/discount/{$p.page}">{$p.title}</a></li>
                                {else}
                                    <li><a href="{$app_url}/report/discount/{$p.page}">{$p.title}</a></li>
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