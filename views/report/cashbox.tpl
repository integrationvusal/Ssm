{extends file="base.tpl"}

{block name="page-title"}
	:: Kassa üzrə hesabat
{/block}

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
				<li class="active">Kassa üzrə hesabat</li>
			</ol>
		</div><!--/.row-->

		<div class="row col-no-gutter-container">
			<div class="col-xs-12 col-md-2">
			</div>
			<div class="col-xs-12 col-md-8 client">
				{if $searchData}
					<form class="form-horizontal" action="{$app_url}/report/cashbox" method="post">
						<input type="hidden" name="user_id" id="user_id" value="{$user.id}">
						<div>
							<div class="form-group">
								<label for="" class="col-md-2 control-label">Tarix:</label>
								<div class="col-md-5"><input type="text" class="datepicker form-control" name="date_from" value="{$searchData.date_from}"></div>
								<div class="col-md-5"><input type="text" class="datepicker form-control" name="date_to" value="{$searchData.date_to}"></div>
							</div>
							<div class="form-group">
								<label for="" class="col-md-2 control-label">Kassa:</label>
								<div class="col-md-5"><input type="text" class="cashbox_search form-control"></div>
								<div class="col-md-5">
									<select name="cashbox_id" id="client_id" class="form-control">
										<option value="0">Kassanı seç</option>
										{foreach from=$cashboxes item=cashbox}
											{if $cashbox.id == $searchData.cashbox_id}
												<option selected value="{$cashbox.id}">{$cashbox.name}</option>
											{else}
												<option value="{$cashbox.id}">{$cashbox.name}</option>
											{/if}
										{/foreach}
									</select>
								</div>
							</div>
							<input type="submit" class="btn btn-danger form-control" name="report_search" value="AXTAR">
						</div>
					</form>
				{else}
					<form class="form-horizontal" action="{$app_url}/report/cashbox" method="post">
						<input type="hidden" name="user_id" id="user_id" value="{$user.id}">
						<div>
							<div class="form-group">
								<label for="" class="col-md-2 control-label">Tarix:</label>
								<div class="col-md-5"><input type="text" class="datepicker form-control" name="date_from"></div>
								<div class="col-md-5"><input type="text" class="datepicker form-control" name="date_to"></div>
							</div>
							<div class="form-group">
								<label for="" class="col-md-2 control-label">Kassa:</label>
								<div class="col-md-5"><input type="text" class="cashbox_search form-control"></div>
								<div class="col-md-5">
									<select name="cashbox_id" id="cashbox_id" class="form-control">
										<option value="0">Kassanı seç</option>
										{foreach from=$cashboxes item=cashbox}
											{if $cashbox.id == $cashbox_id}
												<option selected value="{$cashbox.id}">{$cashbox.name}</option>
											{else}
												<option value="{$cashbox.id}">{$cashbox.name}</option>
											{/if}
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
				<h3>Əməliyyatlar <a class="btn btn-info" href="{$app_url}/report/cashbox/print"><span class="glyphicon glyphicon-print"></span></a></h3>
				<div class="table-responsive">
					<table class="table table-bordered" style="width:100%;">
						<tr>
							<th>#</th>
							<th>Kassa</th>
							<th>Operator</th>
							<th>Mədaxil</th>
							<th>Məxaric</th>
							<th>Qaimə №</th>
							<th>Tarix</th>
							<th>Balans (USD)</th>
							<th>Balans (AZN)</th>
						</tr>
						{assign var="i" value=(($page-1) * $limit)}
						{foreach from=$invoices item=invoice}
							{assign var="i" value=$i+1}
							<tr>
								<td>{$i}</td>
								<td>{$invoice.cashbox_name}</td>
								<td>{$invoice.name}</td>
								<td>{if $invoice.operation_type == '+'}+{$invoice.amount}{else}0{/if}</td>
								<td>{if $invoice.operation_type == '-'}-{$invoice.amount}{else}0{/if}</td>
								<td><a href="javascript:void(0)"
									   class="invoice-details"
									   data-invoice-id="{$invoice.invoice_id}"
									   data-invoice-type="{$invoice.invoice_type}">{$invoice.serial}</a></td>
								<td>{$invoice.date|substr:0:10}</td>
								<td>{if $invoice.currency}{$invoice.total_amount}{/if}</td>
								<td>{if !$invoice.currency}{$invoice.total_amount}{/if}</td>
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
								<li class="active"><a href="{$app_url}/report/cashbox/{$p.page}">{$p.title}</a></li>
							{else}
								<li><a href="{$app_url}/report/cashbox/{$p.page}">{$p.title}</a></li>
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