{extends file="base.tpl"}

{block name="page-title"}
	:: Kontragentlər
{/block}

{block name="dashboard"}

	<div class="modal fade" id="contragent_payment" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form action="{$app_url}/contragent/pay" method="post" data-namespace="contragent">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">ÖDƏNİŞ</h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-xs-12">
								<input type="hidden" name="user_id" id="user_id" value="{$user.id}">
								<input type="hidden" name="contragent_id" id="contragent_id">
								<input type="hidden" name="operator" value="{$operator}">
								<input type="hidden" name="invoice_type" value="{$invoice.type}">
								<dl class="dl-horizontal">
									<dt>Qaimə №:</dt>
									<dd>
										{$invoice.serial}
										<input type="hidden" name="invoice_serial" id="invoice_serial" value="{$invoice.serial}">
									</dd>
									<dt>Ümumi borc:</dt>
									<dd>
										<span id="total-debt"></span>
										<input type="hidden" name="total_debt" id="total_debt">
									</dd>
									<dt>Valyuta:</dt>
									<dd>
										<select name="currency" class="form-control">
											{foreach from=$currencies item=c}
												<option accesskey="{$c.value}" value="{$c.id}">{$c.name}</option>
											{/foreach}
										</select>
										<input type="hidden" val="0" name="currency_archive"/>
									</dd>
									<dt>Ödəniləcək məbləğ:</dt>
									<dd class="amounts"></dd>
									<dt>Kassa:</dt>
									<dd>
										<select name="subject_id" class="form-control">
											{foreach from=$cashboxes item=cb}
												{if $cb.id == $cashbox_id.id}
													<option selected value="{$cb.subject_id}">{$cb.name}</option>
												{else}
													<option value="{$cb.subject_id}">{$cb.name}</option>
												{/if}
											{/foreach}
										</select>
									</dd>
									<dt>Tarix:</dt>
									<dd><input type="text" id="date" name="date" class="form-control datepicker" value="{$currentDate}"></dd>
									<dt>Qeyd:</dt>
									<dd><textarea name="notes" id="notes" cols="20" rows="5" class="form-control"></textarea></dd>

								</dl>

							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal">İmtina</button>
						<button type="submit" class="btn btn-danger">Təsdiqlə</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">

		<div class="row">
			<ol class="breadcrumb">
				<li><a href="{$app_url}"><span class="glyphicon glyphicon-home"></span></a></li>
				<li class="active">Kontragentlər</li>
			</ol>
		</div><!--/.row-->


		<div class="row col-no-gutter-container" style="float: left;">
			<div class="col-xs-12 col-md-2">
			</div>
			<div class="col-xs-12 col-md-8 kontragent">
				{if isset($context.contragent)}
					{assign var="contragent" value=$context.contragent}

					<form class="form-horizontal" action="{$app_url}/contragent/edit" method="post" enctype="multipart/form-data">

						<div>

							<div class="form-group">
								<label for="" class="col-md-2 control-label">Adı:</label>
								<div class="col-md-10">
									<input type="hidden" name="id" value="{$contragent.id}">
									<input type="text" class="form-control" value="{$contragent.name.firstname}" name="name[]" required>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-md-2 control-label">Soyadı:</label>
								<div class="col-md-10">
									<input type="text" class="form-control" value="{$contragent.name.lastname}" name="name[]">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-md-2 control-label">Atasının adı:</label>
								<div class="col-md-10">
									<input type="text" class="form-control" value="{$contragent.name.fathername}" name="name[]">
									<input type="hidden" name="user_id" value="{$user.id}">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-md-2 control-label">Ünvanı:</label>
								<div class="col-md-10">
									<input type="text" class="form-control" value="{$contragent.address}"  name="address" required>
								</div>
							</div>
							{assign var="phonesCount" value=count($contragent.phone.phones)}
							{foreach from=$contragent.phone.phones item=phone key=k}
								<div class="form-group">
									<label for="" class="col-md-2 control-label">Telefon:</label>
									<div class="col-md-2">
										<select class="form-control" name="prefix[]">
											<option>{$contragent.phone.prefixes[$k]}</option>
											<option>(012)</option>
											<option>(050)</option>
											<option>(051)</option>
											<option>(055)</option>
											<option>(070)</option>
											<option>(077)</option>
										</select>
									</div>
									<div class="col-md-6">
										<input type="text" class="form-control" value="{$phone}"  name="phone[]">
									</div>
									<div class="col-md-2">
										{if $k == 0}
											<button type="button" class="btn btn-primary btn-block plus-sign"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>
										{else}
											<button type="button" class="btn btn-primary btn-block minus-sign"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>
										{/if}
									</div>
								</div>
							{/foreach}
							<div class="after_phone">

							</div>

							<div class="tmp" style="display:none;">
								<div class="form-group">
									<label for="" class="col-md-2 control-label">Telefon:</label>
									<div class="col-md-2">
										<select class="form-control" name="prefix[]">
											<option>(012)</option>
											<option>(050)</option>
											<option>(051)</option>
											<option>(055)</option>
											<option>(070)</option>
											<option>(077)</option>
										</select>
									</div>
									<div class="col-md-6">
										<input type="text" class="form-control" name="phone[]">
									</div>
									<div class="col-md-2">
										<button type="button" class="btn btn-primary btn-block minus-sign"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label for="email" class="col-md-2 control-label">E-mail:</label>
								<div class="col-md-10">
									<input type="email" class="form-control" id="email" name="email" value="{$contragent.email}" >
								</div>
							</div>

							<div class="form-group">
								<label for="description" class="col-md-2 control-label">Qeyd:</label>
								<div class="col-md-10">
									<textarea class="form-control" name="description" id="description" cols="30" rows="5">{$contragent.description}</textarea>
								</div>
							</div>

							<div class="img-set" style="display: none">
								<label for="image" class="col-md-2 control-label">Şəkil:</label>
								<div class="col-md-8">
									<img src="" width="60" style="float: left; margin-right: 20px; display: none;" class="clientImagePreview">
									<input type="file" name="image[]" data-rel="0" class="btn btn-primary btn-file clientImage" accept="image/*">
								</div>
								<div class="col-md-2">
									<button type="button" data-rel="0" class="btn btn-danger btn-block minusImage"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>
								</div>
							</div>

							<input type="hidden" name="old_images_str" value="{$contragent.image}">

							<span class="img-set-container">

								{if !empty($contragent.images)}
									{assign var="i" value=0}
									{foreach from=$contragent.images item=oldImage}
										<div class="form-group img-set-{$i} img-set">
											<label for="image" class="col-md-2 control-label">Şəkil:</label>
											<div class="col-md-8">
												<img src="{$app_url}/{$oldImage}" width="60" style="float: left; margin-right: 20px;" class="clientImagePreview{$i}">
												<input type="file" name="image[]" data-rel="{$i}" id="clientImageReq{$i}" class="btn btn-primary btn-file clientImage" accept="image/*">
												<input type="hidden" class="oldImage{$i}" name="old_image[]" value="{$oldImage}">
											</div>
											<div class="col-md-2">
												<button type="button" data-rel="{$i}" class="btn btn-danger btn-block minusImage"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>
												{if $i == 0}
													<button type="button" data-rel="0" class="btn btn-primary btn-block plusImage"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>
												{/if}
											</div>
										</div>
										{assign var="i" value=$i+1}
									{/foreach}
								{else}
								<div class="form-group img-set-0 img-set">
									<label for="image" class="col-md-2 control-label">Şəkil:</label>
									<div class="col-md-8">
										<img src="" width="60" style="float: left; margin-right: 20px; display: none;" class="clientImagePreview0">
										<input type="file" name="image[]" data-rel="0" id="clientImageReq0" class="btn btn-primary btn-file clientImage" accept="image/*">
									</div>
									<div class="col-md-2">
										<button type="button" data-rel="0" class="btn btn-danger btn-block minusImage"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>
										<button type="button" data-rel="0" class="btn btn-primary btn-block plusImage"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>
									</div>
								</div>
								{/if}

							</span>

							<div class="form-group">
								<div class="col-md-2"></div>
								<div class="col-md-10">
									<button type="submit" class="btn btn-warning form-control">YENILƏ</span></button>
								</div>
							</div>

						</div>
					</form>
				{else}
				<form class="form-horizontal" action="{$app_url}/contragent/create" method="post" enctype="multipart/form-data">
					{if $permissions.contragent_create}
					<button type="button" class="btn btn-success btn-lg btn-block" data-toggle="collapse" data-target="#collapseExampleKontragent" aria-expanded="false" aria-controls="collapseExample">YENİ</button>
					{/if}
					<div class="collapse" id="collapseExampleKontragent">


						<div class="form-group">
							<label for="" class="col-md-2 control-label">Adı:</label>
							<div class="col-md-10">
								<input type="text" class="form-control" id="" placeholder="" name="name[]" required>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-md-2 control-label">Soyadı:</label>
							<div class="col-md-10">
								<input type="text" class="form-control" id="" placeholder="" name="name[]">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-md-2 control-label">Atasının adı:</label>
							<div class="col-md-10">
								<input type="text" class="form-control" id="" placeholder="" name="name[]">
								<input type="hidden" name="user_id" value="{$user.id}">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-md-2 control-label">Ünvanı:</label>
							<div class="col-md-10">
								<input type="text" class="form-control" id="" placeholder="" name="address" required>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-md-2 control-label">Telefon:</label>
							<div class="col-md-2">
								<select class="form-control" name="prefix[]">
									<option>(012)</option>
									<option>(050)</option>
									<option>(051)</option>
									<option>(055)</option>
									<option>(070)</option>
									<option>(077)</option>
								</select>
							</div>
							<div class="col-md-6">
								<input type="text" class="form-control" id="" placeholder="" name="phone[]">
							</div>
							<div class="col-md-2">
								<button type="button" class="btn btn-primary btn-block plus-sign"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>
							</div>
						</div>

						<div class="after_phone">

						</div>

						<div class="tmp" style="display:none;">
							<div class="form-group">
								<label for="" class="col-md-2 control-label">Telefon:</label>
								<div class="col-md-2">
									<select class="form-control" name="prefix[]">
										<option>(012)</option>
										<option>(050)</option>
										<option>(051)</option>
										<option>(055)</option>
										<option>(070)</option>
										<option>(077)</option>
									</select>
								</div>
								<div class="col-md-6">
									<input type="text" class="form-control" name="phone[]">
								</div>
								<div class="col-md-2">
									<button type="button" class="btn btn-primary btn-block minus-sign"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="email" class="col-md-2 control-label">E-mail:</label>
							<div class="col-md-10">
								<input type="email" class="form-control" id="email" name="email">
							</div>
						</div>

						<div class="form-group">
							<label for="description" class="col-md-2 control-label">Qeyd:</label>
							<div class="col-md-10">
								<textarea class="form-control" name="description" id="description" cols="30" rows="5"></textarea>
							</div>
						</div>

						<div class="img-set" style="display: none">
							<label for="image" class="col-md-2 control-label">Şəkil:</label>
							<div class="col-md-8">
								<img src="" width="60" style="float: left; margin-right: 20px; display: none;" class="clientImagePreview">
								<input type="file" name="image[]" data-rel="0" class="btn btn-primary btn-file clientImage" accept="image/*">
							</div>
							<div class="col-md-2">
								<button type="button" data-rel="0" class="btn btn-danger btn-block minusImage"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>
							</div>
						</div>

						<div class="form-group img-set-0 img-set">
							<label for="image" class="col-md-2 control-label">Şəkil:</label>
							<div class="col-md-8">
								<img src="" width="60" style="float: left; margin-right: 20px; display: none;" class="clientImagePreview0">
								<input type="file" name="image[]" data-rel="0" id="clientImageReq0" class="btn btn-primary btn-file clientImage" accept="image/*">
							</div>
							<div class="col-md-2">
								<button type="button" data-rel="0" class="btn btn-danger btn-block minusImage"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>
								<button type="button" data-rel="0" class="btn btn-primary btn-block plusImage"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>
							</div>
						</div>

						<span class="img-set-container"></span>

						<div class="form-group">
							<div class="col-md-2"></div>
							<div class="col-md-10">
								<button type="submit" class="btn btn-danger form-control">ƏLAVƏ ET</span></button>
							</div>
						</div>

					</div>
				</form>
				{/if}
			</div>


			<div class="col-xs-12 col-md-2">
			</div>

			<div class="col-xs-12 col-md-12 col-no-gutter">
				<h3>Kontragentlər</h3>
				<div class="table-responsive">
					<table class="table table-bordered">
						<tr>
							<th>#</th>
							<th>Adı, soyadı, atasının adı</th>
							<th>Şəkil</th>
							<th>Ünvanı</th>
							<th width="130">Telefonu</th>
							<th>E-mail</th>
							{foreach from=$currencies item=c}
								<th>Borc ({$c.name})</th>
							{/foreach}
							<th>Ödə</th>
							<th>Redaktə et</th>
							<th>Sil</th>
						</tr>
						{assign var="i" value=0}
						{foreach from=$contragents item=agent}
						{assign var="i" value=$i + 1}
						<tr>
							<td>{$i}</td>
							<td>{$agent.name}</td>
							<td>
								{if !empty($agent.image)}
									{assign var=images value=";"|explode:$agent.image}
									{foreach from=$images item=img key=k}
										{if !empty($img)}
											<a class="fancybox-thumbs" data-fancybox-group="thumb{$agent.id}" href="{$app_url}/{$img}" {if $k > 0}style="display: none"{/if}>
												<img src="{$app_url}/{$img}" width="60">
											</a>
										{/if}
									{/foreach}
								{/if}
							</td>
							<td>
								{if $agent.address != 'spec1'}
									{$agent.address}
								{/if}
							</td>
							<td>{$agent.phone|replace:';':'<br>'}</td>
							<td>{$agent.email}</td>
							{assign var="hasPay" value=false}
							{foreach from=$currencies key=k item=c}
								<td data-currency-name="{$c.name}" data-archive="{$c.value}" data-currency-id="{$c.id}" class="debts {if isset($agent.debts.$k) && $agent.debts.$k > 0}{$hasPay=true}has{/if}">{if isset($agent.debts.$k)}{$agent.debts.$k}{else}0{/if}</td>
							{/foreach}
							<td>
								{if $permissions.contragent_pay && $hasPay}
								<button class="btn btn-info contragent-payment"
										data-contragent-id="{$agent.id}"
										>ÖDƏ</button>
								{/if}
							</td>
							<td>
								{if $permissions.contragent_update && $agent.address != 'spec1'}
								<a href="{$app_url}/contragent/edit/{$agent.id}" class="btn btn-warning">Redaktə et</a>
								{/if}
							</td>
							<td>
								{if $permissions.contragent_delete && $agent.address != 'spec1'}
								<form action="{$app_url}/contragent/delete" id="deleteContragent{$agent.id}" method="post">
									<input type="hidden" name="user_id" value="{$user.id}">
									<input type="hidden" name="id" value="{$agent.id}">
									<button type="button" class="btn btn-danger delete-contragent" data-rel="{$agent.id}">Sil</button>
								</form>
								{/if}
							</td>
						</tr>
						{/foreach}
					</table>
				</div>

			</div>

		</div><!--/.row-->


	</div>	<!--/.main-->

{/block}