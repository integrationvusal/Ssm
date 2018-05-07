{extends file="base.tpl"}

{block name="page-title"}
	:: {$messages.serv_net.title}
{/block}

{block name="dashboard"}

	<div class="modal fade" id="client_payment" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form action="{$app_url}/client/pay" method="post" data-namespace="client">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">ÖDƏNİŞ</h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-xs-12">
								<input type="hidden" name="user_id" id="user_id" value="{$user.id}">
								<input type="hidden" name="client_id" id="client_id">
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
				<li class="active">Müştərilər</li>
			</ol>
		</div><!--/.row-->


		<div class="row col-no-gutter-container" style="float: left;">
			<div class="col-xs-12 col-md-2">
			</div>
			<div class="col-xs-12 col-md-8 clients">
				{if isset($context.client)}
					{assign var="client" value=$context.client}
					<div class="col-xs-16 col-md-12 kontragent">

						<form class="form-horizontal" action="{$app_url}/client/edit" method="post" enctype="multipart/form-data">

							<div>

								<div class="form-group">
									<label for="" class="col-md-2 control-label">Adı:</label>
									<div class="col-md-10">
										<input type="hidden" name="id" value="{$client.id}">
										<input type="text" class="form-control" value="{$client.name.firstname}" name="name[]" required>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-md-2 control-label">Soyadı:</label>
									<div class="col-md-10">
										<input type="text" class="form-control" value="{$client.name.lastname}" name="name[]">
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-md-2 control-label">Atasının adı:</label>
									<div class="col-md-10">
										<input type="text" class="form-control" value="{$client.name.fathername}" name="name[]">
										<input type="hidden" name="user_id" value="{$user.id}">
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-md-2 control-label">Ünvanı:</label>
									<div class="col-md-10">
										<input type="text" class="form-control" value="{$client.address}"  name="address" required>
									</div>
								</div>
								{assign var="phonesCount" value=count($client.phone.phones)}
								{foreach from=$client.phone.phones item=phone key=k}
									<div class="form-group">
										<label for="" class="col-md-2 control-label">Telefon:</label>
										<div class="col-md-2">
											<select class="form-control" name="prefix[]">
												<option>{$client.phone.prefixes[$k]}</option>
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
										<input type="email" class="form-control" id="email" name="email" value="{$client.email}" >
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

								<input type="hidden" name="image" value="{$client.image}">

								<div class="form-group img-set-0 img-set">
									<label for="image" class="col-md-2 control-label">Şəkil:</label>
									<div class="col-md-8">
										{if isset($client.images[0]) && !empty($client.images[0])}
											<img src="{$app_url}/{$client.images[0]}" width="60" class="clientImagePreview0">
										{else}
											<img src="" width="60" style="float: left; margin-right: 20px; display: none;" class="clientImagePreview0">
										{/if}
										<input type="file" disabled name="image[]" data-rel="0" id="clientImageReq0" class="btn btn-primary btn-file clientImage" accept="image/*">
										<input type="hidden" name="old_image[]" value="{$client.images[0]}" id="clientImageOld0">
									</div>
									<div class="col-md-2">
										<button type="button" data-rel="0" class="btn btn-danger btn-block minusImage"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>
										<button type="button" data-rel="0" class="btn btn-primary btn-block plusImage"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span></button>
									</div>
								</div>

								{foreach from=$client.images key=k item=image}
									{if $k > 0}
									<div class="form-group img-set-{$k} img-set">
										<label for="image" class="col-md-2 control-label">Şəkil:</label>
										<div class="col-md-8">
											<img src="{$app_url}/{$image}" width="60" class="clientImagePreview{$k}">
											<input type="hidden" name="old_image[]" value="{$image}">
										</div>
										<div class="col-md-2">
											<button type="button" data-rel="{$k}" class="btn btn-danger btn-block minusImage"><span class="glyphicon glyphicon-minus-sign" aria-hidden="true"></span></button>
										</div>
									</div>
									{/if}
								{/foreach}
								<span class="img-set-container"></span>

								<div class="form-group">
									<div class="col-md-2"></div>
									<div class="col-md-10">
										<button type="submit" class="btn btn-warning form-control">YENILƏ</span></button>
									</div>
								</div>

							</div>
						</form>

					</div>
				{else}
				<form class="form-horizontal" action="{$app_url}/client/create" method="post" enctype="multipart/form-data">
					{if $permissions.client_create}
					<button type="button" class="btn btn-success btn-lg btn-block" data-toggle="collapse" data-target="#collapseExampleClients" aria-expanded="false" aria-controls="collapseExample">YENİ</button>
					{/if}
					<div class="collapse" id="collapseExampleClients">
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
				<h3>Müştərilər</h3>
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
								<th>Borcu ({$c.name})</th>
							{/foreach}
							<th>Ödəniş</th>
							<th>Redaktə et</th>
							<th>Sil</th>
						</tr>
						{foreach from=$clients item=client}
							<tr>
								<td>{$client.id}</td>
								<td>{$client.name}</td>
								<td>
									{assign var=images value=";"|explode:$client.image}
									{foreach from=$images item=img key=k}
										{if !empty($img)}
											<a class="fancybox-thumbs" data-fancybox-group="thumb{$client.id}" href="{$app_url}/{$img}" {if $k > 0}style="display: none"{/if}>
												<img src="{$app_url}/{$img}" width="60">
											</a>
										{/if}
									{/foreach}
								</td>
								<td>{$client.address}</td>
								<td>{$client.phone|replace:';':'<br>'}</td>
								<td>{$client.email}</td>
								{assign var=hasPay value=false}
								{foreach from=$currencies key=k item=c}
									<td data-currency-name="{$c.name}" data-archive="{$c.value}" data-currency-id="{$c.id}" class="debts {if isset($client.debts.$k) && $client.debts.$k > 0}{$hasPay=true}has{/if}">{if isset($client.debts.$k)}{$client.debts.$k}{else}0{/if}</td>
								{/foreach}
								<td>
									{if $permissions.client_pay && $hasPay}
									<button class="btn btn-success client-payment"
											data-client-id="{$client.id}"
											>ÖDƏNİŞ</button>
									{/if}
								</td>
								<td>
									{if $permissions.client_update}
									<a href="{$app_url}/client/edit/{$client.id}" class="btn btn-warning">Redaktə et</a>
									{/if}
								</td>
								<td>
									{if $permissions.client_delete}
									<form action="{$app_url}/client/delete" id="deleteClient{$client.id}" method="post">
										<input type="hidden" name="user_id" value="{$user.id}">
										<input type="hidden" name="id" value="{$client.id}">
										<button type="button" class="btn btn-danger delete-client" data-rel="{$client.id}">Sil</button>
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