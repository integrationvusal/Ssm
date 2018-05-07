{extends file="base.tpl"}

{block name="page-title"}
	:: {$messages.serv_net.title}
{/block}

{block name="dashboard"}

	<div class="modal fade" id="change-password" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form action="" method="post">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">Şifrəni dəyiş</h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-xs-12">

								<dl class="dl-horizontal">
									<dt>Köhnə şifrə:</dt>
									<dd><input type="password" class="form-control" id="old_password"></dd>
									<dt>Yeni şifrə<br>(Min 5 simvol):</dt>
									<dd><input type="password" class="form-control" id="new_password"></dd>
								</dl>

							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger user-password-change-approve">Təsdiqlə</button>
					</div>
				</form>

			</div>
		</div>
	</div>

	<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
		<div class="row">
			<ol class="breadcrumb">
				<li><a href="{$app_url}"><span class="glyphicon glyphicon-home"></span></a></li>
				<li class="active">İstifadəçilər</li>
			</ol>
		</div><!--/.row-->


		<div class="row col-no-gutter-container">
			<div class="col-xs-12 col-md-2">
			</div>

			<!--
				Create new baza form
			-->
			<div class="col-xs-12 col-md-8 shops">

				{if isset($operator_update) && isset($operator)}
				<h3>Yenilə</h3>
				<form class="form-horizontal" method="post" action="{$app_url}/operator/edit">

					<div class="collapse in" id="collapseExampleShops">
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Adı:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="name" value="{$operator.name}" required>
							</div>
						</div>
						<input type="hidden" name="user_id" value="{$user.id}">
						<input type="hidden" name="operator_id" value="{$operator.id}">
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Login:</label>
							<div class="col-sm-10">
								<!--input type="text" class="form-control operator_prefix" data-val="{$user.id}_" name="login" value="{$operator.login}" required-->
								<input pattern="{$user.id}_.{literal}{5,10}{/literal}" required title="Login '{$user.id}_' altsözü ilə başlamalıdır və 5 simvoldan 10 simvola kimi olmalıdır" class="form-control operator_prefix" data-val="{$user.id}_" name="login" value="{$operator.login}">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Şifrəni dəyiş:</label>
							<div class="col-sm-10">
								<input type="checkbox" id="change_password">
							</div>
						</div>
						<div class="form-group change_password" style="display: none">
							<label for="" class="col-sm-2 control-label">Şifrə:</label>
							<div class="col-sm-10">
								<input type="password" class="form-control" name="password" disabled>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Qeyd:</label>
							<div class="col-sm-10">
								<textarea class="form-control" rows="3" name="description">{$operator.description}</textarea>
							</div>
						</div>

						<button type="submit" class="btn btn-warning f-right">YENİLƏ</button>
					</div>

				</form>
				{else}
				<form class="form-horizontal" method="post" action="{$app_url}/operator/create">
					{if $permissions.operator_create}
					<button type="button" class="btn btn-success btn-lg btn-block" data-toggle="collapse" data-target="#collapseExampleShops" aria-expanded="false" aria-controls="collapseExample">YENİ</button>
					{/if}

					<div class="collapse" id="collapseExampleShops">
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Adı:</label>
							<div class="col-sm-10">
								<input type="text" class="form-control" name="name" required>
							</div>
						</div>
						<input type="hidden" name="user_id" value="{$user.id}">
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Login:</label>
							<div class="col-sm-10">
								<!--input type="text" class="form-control operator_prefix" data-val="{$user.id}_" name="login" required-->
								<input pattern="{$user.id}_.{literal}{5,10}{/literal}" required title="Login '{$user.id}_' altsözü ilə başlamalıdır və 5 simvoldan 10 simvola kimi olmalıdır" class="form-control operator_prefix" data-val="{$user.id}_" name="login" value="{$user.id}_">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Şifrə:</label>
							<div class="col-sm-10">
								<input type="password" class="form-control" name="password" required>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Qeyd:</label>
							<div class="col-sm-10">
								<textarea class="form-control" rows="3" name="description"></textarea>
							</div>
						</div>

						<button type="submit" class="btn btn-danger f-right">ƏLAVƏ ET</button>
					</div>

				</form>
				{/if}

			</div>
			<!--
				End create new baza form
			-->

			<div class="col-xs-12 col-md-2">
			</div>

			<div class="col-xs-12 col-md-12 col-no-gutter">
				<h3>İstifadəçilər</h3>
				<div class="table-responsive">
					<table class="table table-bordered" style="width:100%;">
						<tr>
							<th>#</th>
							<th>Ad</th>
							<th>Login</th>
							<th>Qeyd</th>
							<th></th>
							<th></th>
							<th></th>
						</tr>
						{if $user.id == $user.operator.id}
						<tr>
							<th></th>
							<th>{$user.name}</th>
							<th>{$user.login}</th>
							<th>{$user.description}</th>
							<th></th>
							<th><button class="btn btn-default" data-toggle="modal" data-target="#change-password">Şifrəni dəyiş</button></th>
							<th></th>
						</tr>
						{/if}
						{assign var=i value=0}
						{foreach from=$operators item=operator}
							{assign var=i value=$i + 1}
							<tr>
								<th>{$i}</th>
								<th>{$operator.name}</th>
								<th>{$operator.login}</th>
								<th>{$operator.description}</th>
								<th>
									{if $permissions.operator_grant}
									<a href="{$app_url}/permission/{$operator.id}" class="btn btn-info">Modullar</a>
									{/if}
								</th>
								<th>
									{if $permissions.operator_update}
									<a href="{$app_url}/operator/edit/{$operator.id}" class="btn btn-warning">Redaktə et</a>
									{/if}
								</th>
								<th>
									{if $permissions.operator_delete}
									<form action="{$app_url}/operator/delete" method="post" id="operator_delete_{$operator.id}">
										<input type="hidden" name="user_id" value="{$user.id}">
										<input type="hidden" name="operator_id" value="{$operator.id}">

										<button type="button" class="btn btn-danger operator_delete" data-rel="{$operator.id}">Sil</button>
									</form>
									{/if}
								</th>
							</tr>
						{/foreach}
					</table>
				</div>
			</div>

		</div><!--/.row-->

	</div>	<!--/.main-->

{/block}