{extends file="base.tpl"}

{block name="page-title"}
    :: Qəbzlər
{/block}

{block name="dashboard"}

    <div class="modal fade" id="receipt_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Qəbz</h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger print-receipt-modal">Çap et</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">İmtina</button>
                </div>
            </div>
        </div>
    </div>


    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">

        <div class="row">
            <ol class="breadcrumb">
                <li><a href="{$app_url}"><span class="glyphicon glyphicon-home"></span></a></li>
                <li class="active">Qəbzlər</li>
            </ol>
        </div><!--/.row-->


        <div class="row col-no-gutter-container" style="float: left;">

            <div class="col-xs-12 col-md-12 col-no-gutter">
                <h3>Qəbzlər</h3>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th>#</th>
                            <th>Logo</th>
                            <th>Şirkətin adı</th>
                            <th>VÖEN</th>
                            <th>Ünvan</th>
                            <th>Operator</th>
                            <th>Qaimə</th>
                            <th>Tarix</th>
                            <th></th>
                        </tr>
                        {assign var="i" value=$start_from}
                        {foreach from=$receipts item=receipt}
                            {assign var="i" value=$i + 1}
                            <tr>
                                <td>{$i}</td>
                                <td><img src="{$receipt.company_logo}" alt="" width="20"></td>
                                <td>{$receipt.company_name}</td>
                                <td>{$receipt.company_voen}</td>
                                <td>{$receipt.company_address}</td>
                                <td>{$receipt.operator}</td>
                                <td>{$receipt.invoice}</td>
                                <td>{$receipt.date_time}</td>
                                <td><button class="btn btn-warning print-receipt" data-rel="{$receipt.id}">Çap</button></td>
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
                                    <li class="active"><a href="{$app_url}/receipt/{$p.page}">{$p.title}</a></li>
                                {else}
                                    <li><a href="{$app_url}/receipt/{$p.page}">{$p.title}</a></li>
                                {/if}
                            {/if}
                        {/foreach}
                    </ul>
                </div>
                <div class="col-xs-12 col-md-4">
                </div>

        </div><!--/.row-->


    </div>	<!--/.main-->
    <script>

        var printReceiptXHR = null,
            user_id = {$user.id},
            subject_id = {$subject.id},
            app_url = '{$app_url}';

        $(document).ready(function(){

            $(".print-receipt").click(function(event) {

                var receipt_id = $(event.target).attr("data-rel");

                if(printReceiptXHR != null) printReceiptXHR = null;
                $.ajax({
                    url: app_url + '/receipt/getcontent',
                    type: 'POST',
                    data: {
                        user_id : user_id,
                        subject_id : subject_id,
                        receipt_id : receipt_id
                    },
                    dataType: 'JSON',
                    beforeSend: function(xhr){
                        Loader.lStart(xhr);
                    },
                    success: function(response){
                        Loader.lStop();
                        if(response){

                            $("#receipt_modal .modal-body").html(response);

                            $("#receipt_modal .modal-body .receipt table table").css("width", "100%");
                            $("#receipt_modal .modal-body .receipt table tr").css("padding-top", "10px");

                            $("#receipt_modal").modal('toggle');

                        } else {
                            dhtmlx.alert({
                                title: 'Qəbz',
                                text: "Heç bir nəticə tapılmadı"
                            });
                        }
                    }
                });

            });

        });

        $(".print-receipt-modal").click(function(event){
            $(".modal-body").printThis({
                debug: false,
                //importCSS: true,
                importStyle: true,
                printContainer: true,
                //loadCSS: "",
                pageTitle: "Qəbz",
                removeInline: false,
                printDelay: 100,
                header: null,
                formValues: true
            });
        });

    </script>
{/block}