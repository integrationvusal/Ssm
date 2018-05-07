{extends file="base.tpl"}

{block name="page-title"}
	:: Magazin Satiş
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
					{if isset($flash.sell_data)}
						{$flash.sell_data}
					{/if}
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger print-receipt-modal">Çap et</button>
					<button type="button" class="btn btn-primary" data-dismiss="modal">İmtina</button>
				</div>
			</div>
		</div>
	</div>

	{if $sell_only}
		<div class="row main">
	{else}
		<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
	{/if}
		<div class="row">
			<ol class="breadcrumb">
				<li><a href="{$app_url}"><span class="glyphicon glyphicon-home"></span></a></li>
				<li class="active">Satış</li>
			</ol>
		</div><!--/.row-->

		<div class="row col-no-gutter-container">
			<div class="col-xs-12 col-md-10 sale">
				<form class="form-inline" method="post" id="sell_goods_code">
					<div class="form-group">
						<input type="checkbox" name="sell_code_switcher">
					</div>
					<div class="form-group">
						<input type="text" name="goods_code" class="form-control sell-goods-code" id="goods_code" rel="sell" autocomplete="off" placeholder="Kod və ya barkod(IMEI) daxil et" style="display: none">
					</div>
					<div class="form-group">
						<label class="sell-goods-barcode" for="barcode_reader">Barkod(IMEI) reader</label>
					</div>
					<div class="form-group">
						<input class="sell-goods-barcode" type="checkbox" id="barcode_reader">
					</div>
					<div class="form-group">
						<input type="text" name="barcode" class="form-control sell-goods-barcode" autocomplete="off" id="barcode" placeholder="Barkodu(IMEI) daxil et">
					</div>
					<div class="form-group">
						<button type="button" class="btn btn-success sell-goods-barcode" id="sell-add-goods">ƏLAVƏ ET</button>
						<button type="button" class="btn btn-danger sell-goods-code" id="sell-search-goods" style="display: none">AXTAR</button>
					</div>
				</form>
			</div>

			<div class="col-xs-12 col-md-2">
			</div>

			<div class="col-xs-12 col-md-12 col-no-gutter" id="sell-search-table" style="display: none">
				<h3>{$subject.name} <sup><a href="javascript:void(0)" id="close-search-table">x</a></sup><br><small>{$subject.description}</small></h3>
				<div class="table-responsive table_scroll">
					<table class="table table-bordered" {if !$permissions.buy_price}data-exclude="6"{/if}>
						<tr>
							<th>#</th>
							<th>Kod</th>
							<th>Barkod(IMEI)</th>
							<th>Malın adı</th>
							<th>Sayı</th>
							<th {if !$permissions.buy_price}style="display: none;" {/if}>Alış qıyməti</th>
							<th>Satış qiyməti</th>
							<th></th>
						</tr>
						<tr></tr>
						<tbody id="sell-search-table-tbody">

						</tbody>

					</table>
				</div>
			</div>

			<div class="col-xs-12 col-md-12 col-no-gutter sale">
				<h3>Satış</h3>
				<div class="table-responsive">
					<table class="table table-bordered">
						<tr>
							<th>#</th>
							<th>Kod</th>
							<th>Barkod(IMEI)</th>
							<th>Malın adı</th>
							<th>Qalıq</th>
							<th>Sayı</th>
							<th>Qiyməti</th>
							{foreach from=$currencies item=c}
							    <th>Cəmi ({$c['name']})</th>
							{/foreach}
							<th></th>
						</tr>
						<tbody id="sell-pendings">
						{assign var='amount' value=[]}
							{if $pendings}
								{assign var='i' value=0}
								{foreach from=$pendings item=goods}
									{assign var='i' value=$i + 1}
									
									{$amount[$goods.currency] = $amount[$goods.currency]|default:0 + ($goods.count * $goods.sell_price)}
										
									<tr data-currency="{$currencies[$goods.currency]['name']|lower}" class="{$goods.store_item_id}" id="{$goods.id}" data-rel="{$goods.id}">
										<th><span class="order_num">{$i}</span></th>

										<th>{$goods.goods_code}</th>

										<th>{$goods.barcode}</th>

										<th><a href="javascript:void(0)" data-toggle="modal" data-target="#goods-info" class="goods-info" data-rel="{$goods.store_item_id}">{$goods.short_info}</a></th>

										{if $goods.count > $goods.remain_count}
											<th class="remain-count" id="remain-count-{$goods.id}" data-real-count="{$goods.remain_count}">0</th>
											<th><input type="number" data-rel="{$goods.id}" id="pending-count-{$goods.id}" class="form-control input-sm pending-count" name="count[]" value="{$goods.remain_count}"></th>
										{else}
											<th class="remain-count" id="remain-count-{$goods.id}" data-real-count="{$goods.remain_count}">{$goods.remain_count - $goods.count}</th>
											<th><input type="number" data-rel="{$goods.id}" id="pending-count-{$goods.id}" class="form-control input-sm pending-count" name="count[]" value="{$goods.count}"></th>
										{/if}

										<th><input type="number" data-rel="{$goods.id}" id="pending-sell-price-{$goods.id}" step="0.01" class="form-control input-sm pending-sell-price" name="sell_price[]" value="{$goods.sell_price}"></th>

                                        {foreach from=$currencies key=k item=c}
										    <th><span data-archive="{$goods.currency_archive}" data-currency="{$goods.currency}" class="pending-total" id="pending-total-{$goods.id}">{if $goods.currency == $k}{$goods.sell_price * $goods.count}{/if}</span></th>
                                        {/foreach}

            
										<th class="sell-delete" data-rel="{$goods.id}">
											{if $pendings_count > 1}
											<button class="btn btn-danger delete-pending" data-rel="{$goods.id}">Sil</button>
											{/if}
										</th>
									</tr>
								{/foreach}
								{else}
								<tr></tr>
							{/if}
						</tbody>
						<tfoot>
						<tr>
							<td colspan="7">Cəmi:</td>
							{foreach from=$currencies key=k item=c}
							    <td><span class="total_sell_price_{$c['name']|lower}">{$amount[$k]|default:null}</span></td>
							{/foreach}
							<td></td>
						</tr>
						<tr>
							<td colspan="{8+count($currencies)}">
								<form action="{$app_url}/sell/reject" method="post">
									<input type="hidden" name="user_id" id="user_id" value="{$user.id}">
									<input type="hidden" name="subject_id" id="subject_id" value="{$subject.id}">
									<input type="hidden" name="invoice_serial" id="invoice_serial" value="{$invoice.serial}">
									<input type="hidden" name="invoice_type" id="invoice_type" value="{$invoice.type}">
									<input type="hidden" name="invoice_id" id="invoice_id" value="{$invoice.id}">
									<input type="hidden" name="operator_type" id="operator_type" value="{$user.type}">
									<input type="hidden" name="operator" id="operator" value="{$user.operator.id}">
									{if $cashbox != null}
									<input type="hidden" name="cashbox_id" id="cashbox_id" value="{$cashbox.id}">
									{else}
									<input type="hidden" name="cashbox_id" id="cashbox_id" value="0">
									{/if}
									<button type="submit" class="btn btn-warning col-md-3 f-left">İMTİNA</button>
								</form>
								<button id="confirm-sell-button" class="btn btn-danger col-md-3 f-right">TƏSDİQLƏ</button>
							</td>
						</tr>
						</tfoot>

					</table>
				</div>
			</div>

			<div class="modal fade" id="goods-info" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content">

					</div>
				</div>
			</div>

			<div class="modal fade" id="approve-sell" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<form action="" method="post" id="sell-approve-form">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">Təsdiqlə</h4>
						</div>
						<div class="modal-body">
							<div class="row">
							    {foreach from=$amount key=c item=a}
								<div class="col-xs-12">
									<dl class="dl-horizontal">
										<dt>Qaimə №:</dt>
										<dd>{$invoice.serial}</dd>
										<dt>Məbləğ:</dt>
										<dd><span class="total_sell_price_{$currencies[$c]['name']|lower}">{$a}</span> <span class="changed-currency">{$currencies[$c]['name']}</span></dd>
										<dt>Tarix:</dt>
										<dd><input type="text" id="date" class="form-control datepicker" value="{$currentDate}"></dd>
										<dt>Qeyd:</dt>
										<dd><textarea name="notes" id="notes" cols="20" rows="5" class="form-control"></textarea></dd>
										<span class="returnings">
											<dt>Ödənilib:</dt>
											<dd><input type="text" name="received_payment" target="{$currencies[$c]['name']|lower}" class="form-control" value="{$a}"></dd>
											<dt>Qaytarılıb:</dt>
											<dd id="returned_payment_{$currencies[$c]['name']|lower}">0</dd>
										</span>
										<dt>Satış forması:</dt>
										<dd>
											<label class="radio-inline">
												<input type="radio" name="cash_{$currencies[$c]['name']|lower}" class="inlineRadio1" value="1" checked> Nağd
											</label>
											<label class="radio-inline">
												<input type="radio" name="cash_{$currencies[$c]['name']|lower}" class="inlineRadio2" value="0"> Nisyə
											</label>
											{if $permissions.discount_sell}
											<label class="radio-inline">
												<input type="radio" name="cash_{$currencies[$c]['name']|lower}" class="inlineRadio3" value="2"> Nağd(Endirim kartı)
											</label>
											{/if}
										</dd>
										<dt class="payment">Müştəri:</dt>
										<dd class="sale payment">
											<select id="client_{$currencies[$c]['name']|lower}" name="client" class="form-control">
												<!--<option id="client-default" value="0">Alıcı</option>-->
												{foreach from=$clients item=client}
													<option value="{$client.id}">{$client.name}</option>
												{/foreach}
											</select>
										</dd>
										<dt class="payment">İlkin ödəniş:</dt>
										<dd class="payment">
											<div class="form-group">
												<input type="number" name="debtamount" class="form-control changed-currency" id="payed_{$currencies[$c]['name']|lower}" placeholder="{$currencies[$c]['name']}">
											</div>
										</dd>

										<dt class="discount">Kart:</dt>
										<dd class="discount">
											<div class="form-group">
												<input type="text" class="form-control" name="card_number" id="card_number" autocomplete="off">
											</div>
										</dd>
										<dt class="discount_remain">Müştəri:</dt>
										<dd class="discount_remain client_name"></dd>
										<dt class="discount_remain">Kartın növü:</dt>
										<dd class="discount_remain card_type"></dd>
										<dt class="discount_remain card_type"></dt>
										<dd class="discount_remain">
											<div class="form-group">
												<input type="text" class="form-control" name="discount_or_bonus" id="discount_or_bonus">
											</div>
										</dd>
										<dt class="bonus_remain">Bonus qalığı:</dt>
										<dd class="bonus_remain remaining_bonus"></dd>
										<dt class="discount_remain">Qalıq:</dt>
										<dd class="discount_remain remaining_amount"></dd>
										<dt class="discount_remain">Ödəniləcək məbləğ:</dt>
										<dd class="discount_remain">
											<span class="amount_to_pay"></span> <span class="changed-currency">{$currencies[$c]['name']}</span>
										</dd>
										<dt class="discount_remain">Endirim məbləği:</dt>
										<dd class="discount_remain">
											<span class="amount_of_discount"></span> <span class="changed-currency">{$currencies[$c]['name']}</span>
										</dd>
									</dl>
								</div>
								{/foreach}
								<!--
								<div class="col-xs-6 hide addon">
									<dl class="dl-horizontal">
										<dt>Qaimə №:</dt>
										<dd>{$invoice.serial}</dd>
										<dt>Məbləğ:</dt>
										<dd><span class="total_sell_price">{$amount}</span> <span class="changed-currency">USD</span></dd>
										<dt>Tarix:</dt>
										<dd><input type="text" id="date" class="form-control datepicker" value="{$currentDate}"></dd>
										<dt>Qeyd:</dt>
										<dd><textarea name="notes" id="notes" cols="20" rows="5" class="form-control"></textarea></dd>
										<span class="returnings">
											<dt>Ödənilib:</dt>
											<dd><input type="text" name="received_payment" class="form-control" value="{$amount}"></dd>
											<dt>Qaytarılıb:</dt>
											<dd id="returned_payment">0</dd>
										</span>
										<dt>Satış forması:</dt>
										<dd>
											<label class="radio-inline">
												<input type="radio" name="cash_azn" class="inlineRadio1" value="1" checked> Nağd
											</label>
											<label class="radio-inline">
												<input type="radio" name="cash_azn" class="inlineRadio2" value="0"> Nisyə
											</label>
											{if $permissions.discount_sell}
											<label class="radio-inline">
												<input type="radio" name="cash_azn" class="inlineRadio3" value="2"> Nağd(Endirim kartı)
											</label>
											{/if}
										</dd>
										<dt class="payment">Müştəri:</dt>
										<dd class="sale payment">
											<select id="client" name="client_azn" class="form-control">
												<option id="client-default" value="0">Alıcı</option>
												{foreach from=$clients item=client}
													<option value="{$client.id}">{$client.name}</option>
												{/foreach}
											</select>
										</dd>
										<dt class="payment">İlkin ödəniş:</dt>
										<dd class="payment">
											<div class="form-group">
												<input type="number" name="debtamount_azn" class="form-control changed-currency" id="payed" placeholder="USD">
											</div>
										</dd>

										<dt class="discount">Kart:</dt>
										<dd class="discount">
											<div class="form-group">
												<input type="text" class="form-control" name="card_number" id="card_number" autocomplete="off">
											</div>
										</dd>
										<dt class="discount_remain">Müştəri:</dt>
										<dd class="discount_remain client_name"></dd>
										<dt class="discount_remain">Kartın növü:</dt>
										<dd class="discount_remain card_type"></dd>
										<dt class="discount_remain card_type"></dt>
										<dd class="discount_remain">
											<div class="form-group">
												<input type="text" class="form-control" name="discount_or_bonus" id="discount_or_bonus">
											</div>
										</dd>
										<dt class="bonus_remain">Bonus qalığı:</dt>
										<dd class="bonus_remain remaining_bonus"></dd>
										<dt class="discount_remain">Qalıq:</dt>
										<dd class="discount_remain remaining_amount"></dd>
										<dt class="discount_remain">Ödəniləcək məbləğ:</dt>
										<dd class="discount_remain">
											<span class="amount_to_pay"></span> <span class="changed-currency">USD</span>
										</dd>
										<dt class="discount_remain">Endirim məbləği:</dt>
										<dd class="discount_remain">
											<span class="amount_of_discount"></span> <span class="changed-currency">USD</span>
										</dd>
									</dl>
								</div>
								-->
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-primary" data-dismiss="modal">İmtina</button>
							<button type="button" class="btn btn-danger sell-approve">Təsdiqlə</button>
						</div>
						</form>
					</div>
				</div>
			</div>

			<div class="modal fade" id="goods" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">Adisad</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-xs-12 col-sm-4">
									<img src="{$static_url}/img/adidas-shoe.png">
									<h4 class="text-center">Short info</h4>
								</div>
								<div class="col-xs-12 col-sm-8">
									<dl class="dl-horizontal">
										<dt>Alış qiyməti:</dt>
										<dd>100</dd>
										<dt>Satış qiyməti:</dt>
										<dd>150</dd>
										<dt>Tarix:</dt>
										<dd><input type="text" id="date" class="form-control datepicker" value="26.08.1970"></dd>
										<dt>Satış forması:</dt>
										<dd>
											<label class="radio-inline">
												<input type="radio" name="inlineRadioOptions" class="inlineRadio1" value="option1"> Nağd
											</label>
											<label class="radio-inline">
												<input type="radio" name="inlineRadioOptions" class="inlineRadio2" value="option2"> Nisyə
											</label>
										</dd>
										<dt>Cari qiymət:</dt>
										<dd><div class="form-group"><input type="text" class="form-control" id="" placeholder="90 AZN"></div></dd>
										<dt class="payment">Müştəri:</dt>
										<dd class="sale payment">
											<select id="lunch" class="selectpicker" data-live-search="true" title="Please select a lunch ...">
												<option>XXX1</option>
												<option>XXX2</option>
												<option>XXX3</option>
												<option>XXX4</option>
												<option>XXX5</option>
											</select>
										</dd>
										<dt class="payment">İlkin ödəniş:</dt>
										<dd class="payment"><div class="form-group"><input type="text" class="form-control" id="" placeholder="90 AZN"></div></dd>

									</dl>

								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-danger">SAT</button>
							<button type="button" class="btn btn-primary" data-dismiss="modal">İMTİNA</button>
						</div>
					</div>
				</div>
			</div>

		</div><!--/.row-->


	</div>	<!--/.main-->
	<script>
		var cardInfoXHR = null;
		var submitable = false;

		$(function(){

		    $("input[name=received_payment]").blur(function(event) {
				_this = $(this);
				
				 _this.css("border", "1px solid lightgray");
				 _val = parseFloat(_this.val().replace(',','.'));
				_target = _this.attr('target') || '';
				if(_val >= parseFloat($(".total_sell_price_"+_target).html())){
					$("#returned_payment_"+_target).html((_val - parseFloat($('.total_sell_price_'+_target).html())).toFixed(2));
					$(".sell-approve").removeAttr("disabled");
				} else {
					alert('Ödədiyiniz məbləğ satış qiymətindən azdır!');
					_this.css("border", "1px solid red");
					$("#returned_payment_"+_target).html(0);
					$(".sell-approve").attr("disabled", true);
				}
				
		    });

		    _serial = $('#sell-approve-form dd:first').text();

			$("#confirm-sell-button").unbind().click(function(event){
				
				$('[name="received_payment"]').each(function(index){
				
				    if(index) _serial = generateNextInvoiceSerial(_serial);
				    
				    $(this).parents('.dl-horizontal').find('dd:first').text(_serial);    
				    
					_target = $(this).attr('target') || '';
					$(this).removeAttr('style');
					$(this).val(parseFloat($('table .total_sell_price_'+_target).text()));
				});
				
				//$('.addon').prev().find('.changed-currency').text('AZN').attr('placeholder', 'AZN');
				$("#sell-approve-form .inlineRadio1").trigger("click");
			//	_total_usd = parseFloat($(".main .total_sell_price").html());
				//_total_azn = parseFloat($(".main .total_sell_price_azn").html());

				//if(_total_azn<1 && _total_usd<1) return false;

			//	_serial = _startSerial;
				_cur_currency_id = $('[data-currency]').map(function(){
						if($(this).data('currency') != '0') return $(this).data('currency');
					})[0];
				_cur_currency_archive = $('[data-archive]').map(function(){
						if($(this).attr('data-archive') != '0.00') return $(this).attr('data-archive');
					})[0];
				$('#sell-approve-form').data('currency', _cur_currency_id);
				$('#sell-approve-form').attr('data-archive', _cur_currency_archive);

		/*		if(_total_azn>0 && _total_usd>0){
					_serial = generateNextInvoiceSerial(_startSerial);
					$('#sell-approve-form').parents('.modal-dialog').addClass('two-check');
					$('#sell-approve-form .col-xs-12').attr('class', 'col-xs-6');
					$('#sell-approve-form .addon').removeClass('hide');

					$("input[name=received_payment]").val(_total_azn);
					$('#sell-approve-form .total_sell_price_azn').text(_total_azn);
					$(".addon input[name=received_payment]").val(_total_usd);
					$('#sell-approve-form .total_sell_price').text(_total_usd);
				}else{
					_amount = _total_azn>0?_total_azn:_total_usd;

					$('#sell-approve-form').parents('.modal-dialog').removeClass('two-check');
					$('#sell-approve-form .addon').addClass('hide');
					$('#sell-approve-form .col-xs-6:first:not(".addon")').attr('class', 'col-xs-12');

					$("input[name=received_payment]").val(_amount);
					$('#sell-approve-form .total_sell_price_azn').text(_amount);

					if(_cur_currency_id){
						_nameCur = hasCurrency(_cur_currency_id);
						$('.changed-currency').text(_nameCur).attr('placeholder', _nameCur);
					}
				}*/

				//$('.addon dd:first').text(_serial);
				$("#approve-sell").modal("toggle");
			});

			$(".discount, .discount_remain, .bonus_remain").hide();
			$(".discount input, .discount_remain input").attr("disabled", true);
			$("#card_number").keyup(cardChange);

			function cardChange(event) {
				$("#card_number").css("border", "1px solid lightgray");
				submitable = false;
				$(".discount_remain, .bonus_remain").hide();
				$(".discount_remain input").attr("disabled", true);

				$(".client_name").html("");
				$(".card_type").html("");
				$(".remaining_amount").html("");
				$("#discount_or_bonus").val("");

				if(cardInfoXHR != null) cardInfoXHR.abort();
				cardInfoXHR = $.ajax({
					url: '{$app_url}/discount/card/info',
					type: 'POST',
					data: 'card_number=' + $(event.target).val() + '&user_id={$user.id}&subject_id={$subject.id}&total_amount=' + $("span.total_sell_price").html(),
					dataType: 'JSON',
					beforeSend: function(xhr){
						Loader.lStart(xhr);
					},
					success: function(data){
						Loader.lStop();
						if(data.card_status == 1){
							$(".discount_remain").show();
							$(".discount_remain input").removeAttr("disabled");

							$(".client_name").html(data.client_name);
							var currentAmount = $("span.total_sell_price").html();
							var discountedAmount = currentAmount;
							var amountOfDiscount = 0;
							if(data.card_type == 'discount'){
								$("#discount_or_bonus").val(data.discount).attr("readonly", "readonly");
								$("dd.card_type").html("Endirim");
								$("dt.card_type").html("Endirim:");
								amountOfDiscount = (currentAmount*data.discount/100).toFixed(2);
								discountedAmount = (currentAmount - amountOfDiscount).toFixed(2);
								$(".amount_to_pay").html(discountedAmount);
								$(".amount_of_discount").html(amountOfDiscount);
								$(".remaining_amount").html(data.remaining_amount);
								submitable = true;
							} else if(data.card_type == 'bonus') {
								$(".bonus_remain").show();
								$("#discount_or_bonus").val('0').removeAttr("readonly");
								$("dd.card_type").html("Bonus (" + data.bonus_per + " bonus = 1 AZN)");
								$("dt.card_type").html("Bonus:");

								$("#discount_or_bonus").keyup(function(event) {

									var input = $(event.target),
										used_bonus = parseInt(input.val(), 10),
										remaining_bonus = parseInt(data.bonus, 10),
										amount_to_pay = currentAmount,
										amount_of_discount = 0,
										bonus_per = parseInt(data.bonus_per, 10);

									if(isNaN(used_bonus) || used_bonus == '') used_bonus = 0;
									if(used_bonus < 0) used_bonus = 0;
									if(used_bonus > remaining_bonus) used_bonus = remaining_bonus;

									input.val(used_bonus);

									amount_of_discount = (used_bonus / bonus_per).toFixed(2);
									if(parseFloat(amount_of_discount) > parseFloat(currentAmount)) {
										input.css("border", "2px solid red");
										submitable = false;
									} else {
										input.css("border", "1px solid lightgray");
										submitable = true;
									}
									amount_to_pay = (currentAmount - amount_of_discount).toFixed(2);
									remaining_bonus = remaining_bonus - used_bonus;

									$(".amount_to_pay").html(amount_to_pay);
									$(".amount_of_discount").html(amount_of_discount);
									$(".remaining_bonus").html(remaining_bonus);
								});

								$(".amount_to_pay").html(currentAmount);
								$(".amount_of_discount").html('0');
								$(".remaining_amount").html(data.remaining_amount);
								$(".remaining_bonus").html(data.bonus);
								submitable = true;
							}
						} else if(data.card_status == 0){
							$("#card_number").css("border", "2px solid red").val("Kartın istifadə müddəti bitib");
						} else {
							$("#card_number").css("border", "2px solid red");
						}
					}
				});
			}

			$(".print-receipt-modal").click(function(event){
				$("#receipt_modal .modal-body").printThis({
					debug: false,
					importStyle: true,
					importCSS:true,
					printContainer: true,
					//loadCSS: $('[href$="styles.min.css"]').attr('href'),
					pageTitle: "Qəbz",
					removeInline: false,
					printDelay: 100,
					header: null,
					formValues: true
				});
			});

			{if isset($flash.sell_data)}
				if($('#receipt_modal .receipt').length > 1)
					//$('#receipt_modal .modal-sm').addClass('two-modal');
				$("#receipt_modal .modal-body .receipt table table").css("width", "100%");
				$("#receipt_modal .modal-body .receipt table tr").css("padding-top", "10px");

				$("#receipt_modal").children().addClass('two-modal-small').end().modal('toggle');
				{assign var='flash' value=false}
			{/if}

		});

	</script>
{/block}