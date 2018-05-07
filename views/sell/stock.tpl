{extends file="base.tpl"}

{block name="page-title"}
	:: Magazin Satiş
{/block}

{block name="dashboard"}

	<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
		<div class="row">
			<ol class="breadcrumb">
				<li><a href="#"><span class="glyphicon glyphicon-home"></span></a></li>
				<li class="active">TRANSFER</li>
			</ol>
		</div><!--/.row-->

		<div class="row col-no-gutter-container">
			<div class="col-xs-12 col-md-12 sale">
				<form class="form-inline" method="post" id="sell_goods_code">
					<input type="hidden" id="user_id" value="{$user.id}">
					<input type="hidden" id="subject_id" value="{$subject.id}">
					<div class="form-group">
						<input type="text" name="stock_goods_code" class="form-control col-md-7" id="stock_goods_code" autocomplete="off" placeholder="Kod və ya barkod ilə axtar">
					</div>
					<div class="form-group">
						<button type="button" class="btn btn-danger" id="stock-goods-search">AXTAR</button>
					</div>
				</form>
			</div>

			<div class="col-xs-12 col-md-12 col-no-gutter" id="sell-search-table" style="display: none">
				<h3>{$subject.name} <sup><a href="javascript:void(0)" id="close-search-table">x</a></sup><br><small>{$subject.description}</small></h3>
				<div class="table-responsive table_scroll">
					<table class="table table-bordered">

						<tr>
							<th>#</th>
							<th>Kod</th>
							<th>Barkod(IMEI)</th>
							<th>Malın adı</th>
							<th>Sayı</th>
							<th>Alış qiyməti</th>
							<th>Satış qiyməti</th>
							<th>Valyuta</th>
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

				<form class="form-horizontal" id="stockGoodsInvoiceForm">

					<div class="collapse in" id="collapseExampleShops">
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Qaimə №:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" value="{$invoice.serial}" disabled>
								<input type="hidden" name="invoice_serial" value="{$invoice.serial}">
								<input type="hidden" name="invoice_type" value="{$invoice.type}">
								<input type="hidden" name="operator" value="{$user.operator.id}">
								<input type="hidden" name="subject_id" value="{$subject.id}">
								<input type="hidden" name="user_id" value="{$user.id}">
							</div>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Mağaza:</label>
							<div class="col-sm-10">
								<select name="subject_to" id="subject_to" class="form-control">
									{foreach from=$subjects item=sub}
										{if $sub.id != $subject.id}
											<option value="{$sub.id}">{$sub.name}</option>
										{/if}
									{/foreach}
								</select>
							</div>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Tarix:</label>
							<div class="col-sm-10">
								<input type="text" id="date" name="date" class="form-control datepicker" value="{$currentDate}">
							</div>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Qeyd:</label>
							<div class="col-sm-10">
								<textarea class="form-control" rows="3" name="notes"></textarea>
							</div>
						</div>
					</div>

				</form>
			</div>

			<div class="col-xs-12 col-md-2">
			</div>

			<div class="col-xs-12 col-md-12 col-no-gutter sale">
				<h3>TRANSFER</h3>
				<div class="table-responsive">
					<form id="stockGoodsForm">
						<table class="table table-bordered">
							<tr>
								<th>#</th>
								<th>Kod</th>
								<th>Barkod(IMEI)</th>
								<th>Malın adı</th>
								<th>Sayı</th>
								<th>Alış qiyməti (USD)</th>
								<th>Alış qiyməti (AZN)</th>
								<th>Satış qiyməti (USD)</th>
								<th>Satış qiyməti (AZN)</th>
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
									<button type="button" disabled id="confirm-return-button" data-rel="stock" class="btn btn-danger col-md-3 f-right">TƏSDİQLƏ</button>
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