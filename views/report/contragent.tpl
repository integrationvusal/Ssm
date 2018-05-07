{extends file="base.tpl"}

{block name="page-title"}
	:: Kontragentlər üzrə hesabat
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
				<li class="active">Kontragentlər üzrə hesabat</li>
			</ol>
		</div><!--/.row-->

		<div class="row col-no-gutter-container">
			<div class="col-xs-12 col-md-2">
			</div>
			<div class="col-xs-12 col-md-8 kontragent">
				{if $searchData}
					<form class="form-horizontal" action="{$app_url}/report/contragent" method="post">
						<input type="hidden" name="user_id" id="user_id" value="{$user.id}">
						<div>
							<div class="form-group">
								<label for="" class="col-md-2 control-label">Tarix:</label>
								<div class="col-md-5"><input type="text" class="datepicker form-control" name="date_from" value="{$searchData.date_from}"></div>
								<div class="col-md-5"><input type="text" class="datepicker form-control" name="date_to" value="{$searchData.date_to}"></div>
							</div>
							<div class="form-group">
								<label for="" class="col-md-2 control-label">Kontragent:</label>
								<div class="col-md-5"><input type="text" class="contragent_search form-control"></div>
								<div class="col-md-5">
									<select name="contragent_id" id="contragent_id" class="form-control">
										<option value="0">Kontragenti seç</option>
										{foreach from=$contragents item=contragent}
											{if $contragent.id == $searchData.contragent_id}
												<option selected value="{$contragent.id}">{$contragent.name}</option>
											{else}
												<option value="{$contragent.id}">{$contragent.name}</option>
											{/if}
										{/foreach}
									</select>
								</div>
							</div>
							<input type="submit" class="btn btn-danger form-control" name="report_search" value="AXTAR">
						</div>
					</form>
				{else}
					<form class="form-horizontal" action="{$app_url}/report/contragent" method="post">
						<input type="hidden" name="user_id" id="user_id" value="{$user.id}">
						<div>
							<div class="form-group">
								<label for="" class="col-md-2 control-label">Tarix:</label>
								<div class="col-md-5"><input type="text" class="datepicker form-control" name="date_from"></div>
								<div class="col-md-5"><input type="text" class="datepicker form-control" name="date_to"></div>
							</div>
							<div class="form-group">
								<label for="" class="col-md-2 control-label">Kontragent:</label>
								<div class="col-md-5"><input type="text" class="contragent_search form-control"></div>
								<div class="col-md-5">
									<select name="contragent_id" id="contragent_id" class="form-control">
										<option value="0">Kontragenti seç</option>
										{foreach from=$contragents item=contragent}
											<option value="{$contragent.id}">{$contragent.name}</option>
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
				<h3>Əməliyyatlar <a class="btn btn-info" href="{$app_url}/report/contragent/print"><span class="glyphicon glyphicon-print"></span></a></h3>
				<div class="table-responsive">
					<table class="table table-bordered">
						<tr>
							<th>#</th>
							<th>Adı</th>
							<th>Qaimə №</th>
							<th>Borc (USD)</th>
							<th>Borc (AZN)</th>
							<th>Ödəniş (USD)</th>
							<th>Ödəniş (AZN)</th>
							<th>Qalıq (USD)</th>
							<th>Qalıq (AZN)</th>
							<th>Tarix</th>
						</tr>
						{assign var=total_debt value=0}
						{assign var=total_debt_azn value=0}
						{assign var=total_payed value=0}
						{assign var=total_payed_azn value=0}
						{assign var=i value=($page - 1) * $limit}
						{foreach from=$invoices item=invoice}
							{assign var=i value=$i + 1}
							{if $invoice.type == 5}
								{assign var='amount' value = 0}
							{else}
								{assign var='amount' value = $invoice.amount}
							{/if}
							<tr>
								<th>{$i}</th>
								<th>{$invoice.name}</th>
								{if $invoice.status == '0'}
									<td>{$invoice.serial}</td>
								{else}
									<td><a href="javascript:void(0)"
										   class="invoice-details"
										   data-invoice-id="{$invoice.id}"
										   data-invoice-type="{$invoice.type}">{$invoice.serial}</a></td>
								{/if}

								<th>{if $invoice.currency}{$amount}{/if}</th>
								<th>{if !$invoice.currency}{$amount}{/if}</th>

								{if $invoice.currency}
									{$total_debt=$total_debt + $amount}
								{else}
									{$total_debt_azn=$total_debt_azn + $amount}
								{/if}

								{if $invoice.currency}
									{$total_payed=$total_payed + $invoice.payed}
								{else}
									{$total_payed_azn=$total_payed_azn + $invoice.payed}
								{/if}

								<th>{if $invoice.currency}{$invoice.payed}{/if}</th>
								<th>{if !$invoice.currency}{$invoice.payed}{/if}</th>

								{if $invoice.status == '0'}
									<td>Etibarsız</td>
									<td>Etibarsız</td>
								{else}
									<th>{if $invoice.currency}{$invoice.total_amount + $amount - $invoice.payed}{/if}</th>
									<th>{if !$invoice.currency}{$invoice.total_amount + $amount - $invoice.payed}{/if}</th>
								{/if}
								<th>{$invoice.date|substr:0:10}</th>
							</tr>
						{/foreach}
						<tr>
							<th colspan="3">Göstərilənlərin cəmi:</th>
							<th>{$total_debt}</th>
							<th>{$total_debt_azn}</th>
							<th>{$total_payed}</th>
							<th>{$total_payed_azn}</th>
							<th colspan="3"></th>
						</tr>
						<tr>
							<th colspan="3">Cəmi:</th>
							<th>{$summary.debt}</th>
							<th>{$summary.debt_azn}</th>
							<th>{$summary.payed}</th>
							<th>{$summary.payed_azn}</th>
							<th colspan="3"></th>
						</tr>
					</table>
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
									<li class="active"><a href="{$app_url}/report/contragent/{$p.page}">{$p.title}</a></li>
								{else}
									<li><a href="{$app_url}/report/contragent/{$p.page}">{$p.title}</a></li>
								{/if}
							{/if}
						{/foreach}
					</ul>
				</div>
				<div class="col-xs-12 col-md-4">
				</div>
				{/if}
			</div>

		</div><!--/.row-->


	</div>	<!--/.main-->

{/block}