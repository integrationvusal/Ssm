{extends file="base.tpl"}

{block name="page-title"}
	:: {$messages.serv_net.title}
{/block}

{block name="dashboard"}
	<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
		<div class="row">
			<ol class="breadcrumb">
				<li><a href="{$app_url}"><span class="glyphicon glyphicon-home"></span></a></li>
				<li class="active">Katalog</li>
			</ol>
		</div><!--/.row-->

		<div class="row col-no-gutter-container" style="float: left;">
			<div class="col-xs-12 col-md-2">
			</div>
			<div class="col-xs-12 col-md-8 goods">
				{if isset($updateForm)}
					{$updateForm}
				{else}
					{if $permissions.catalog_read}
					<button type="button" class="btn btn-success btn-lg btn-block" data-toggle="collapse" data-target="#collapseExampleGoods" aria-expanded="false" aria-controls="collapseExample">YENİ</button>
					{/if}
					<div class="collapse" id="collapseExampleGoods">
						<div class="form-group row">
							{$form}
						</div>
					</div>
				{/if}


			</div>
			<div class="col-xs-12 col-md-2">
			</div>

			<div class="col-xs-12 col-md-12 col-no-gutter sale">
				<h3>{$model.title}</h3>
				<div class="table-responsive">
				<table class="table table-bordered">
					<tr>
						<th>#</th>
						{foreach from=$table key=attr_key item=th}
							{if $tableattrs[$attr_key].val == 1}
							<th>{$th}</th>
							{/if}
						{/foreach}
						<th>Redaktə et</th>
						<th>Sil</th>
					</tr>
					<tr>
						{if isset($searchData) && $searchData}
							<form action="{$app_url}/goods" method="post">
								<input type="hidden" name="user_id" value="{$user.id}">
								<input type="hidden" name="goods_type" value="{$subject.goods_type}">
								<td></td>
								{foreach from=$table item=cell key=k}
									{if $tableattrs[$k].val == 1}
									<td>
										{if $k != 'image' && $k != 'color'}
											{if $k == 'category'}
												<select name="search_category" id="search_category" class="form-control">
													<option value="0">Bütün kategoriyalar</option>
												{foreach from=$categories item=category}
													{if $searchData.search_category == $category.id}
														<option selected value="{$category.id}">{$category.name}</option>
													{else}
														<option value="{$category.id}">{$category.name}</option>
													{/if}
												{/foreach}
												</select>
											{else}
												<input type="text" name="search_{$k}" class="form-control" value="{$searchData['search_'|cat:$k]}">
											{/if}
										{/if}
									</td>
									{/if}
								{/foreach}
								<td colspan="2">
									<input type="submit" class="form-control btn btn-danger" name="search_goods" value="AXTAR">
								</td>
							</form>
						{else}
							<form action="{$app_url}/goods" method="post">
								<input type="hidden" name="user_id" value="{$user.id}">
								<input type="hidden" name="goods_type" value="{$subject.goods_type}">
								<td></td>
								{foreach from=$table item=cell key=k}
									{if $tableattrs[$k].val == 1}
									<td>
										{if $k != 'image' && $k != 'color'}
											{if $k == 'category'}
												<select name="search_category" id="search_category" class="form-control">
													<option value="0">Bütün kategoriyalar</option>
													{foreach from=$categories item=category}
														<option value="{$category.id}">{$category.name}</option>
													{/foreach}
												</select>
											{else}
												<input type="text" name="search_{$k}" class="form-control">
											{/if}
										{/if}
									</td>
									{/if}
								{/foreach}
								<td colspan="2">
									<input type="submit" class="form-control btn btn-danger" name="search_goods" value="AXTAR">
								</td>
							</form>
						{/if}
					</tr>
					{assign var=i value=($page - 1) * $limit}
					{foreach from=$goods item=data}
					{assign var=i value=$i + 1}
					<tr>
						<td>{$i}</td>
						{foreach from=$table item=cell key=k}
							{if $tableattrs[$k].val == 1}
								{if $k == 'image'}
									<td>
									{assign var=images value=";"|explode:$data[$k]}
									{foreach from=$images item=img key=k}
										{if !empty($img)}
											<a class="fancybox-thumbs" data-fancybox-group="thumb{$data.id}" href="{$app_url}/{$img}" {if $k > 0}style="display: none"{/if}>
												<img src="{$app_url}/{$img}" width="60">
											</a>
										{/if}
									{/foreach}
									</td>
								{elseif $k == 'color'}
									<td>{if isset($colors[$data[$k]])}{$colors[$data[$k]].title}{/if}</td>
								{elseif $k == 'currency'}
									<td>{if empty($data[$k])}AZN{else}{$data[$k]}{/if}</td>
								{else}
									<td>{$data[$k]}</td>
								{/if}
							{/if}
						{/foreach}
						<td>
							{if $permissions.catalog_update}
							<a href="{$app_url}/goods/edit/{$data.id}/{$page}" class="btn btn-warning">Redaktə et</a>
							{/if}
						</td>
						<td>
							{if $permissions.catalog_delete}
							<form action="{$app_url}/goods/delete" method="post">
								<input type="hidden" name="user_id" value="{$user.id}" />
								<input type="hidden" name="goods_id" value="{$data.id}" />
								<button type="button" class="btn btn-danger catalog-goods-delete">Sil</button>
							</form>
							{/if}
						</td>
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
								<li class="active"><a href="{$app_url}/goods/{$p.page}">{$p.title}</a></li>
							{else}
								<li><a href="{$app_url}/goods/{$p.page}">{$p.title}</a></li>
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
	<script>
		$(function(){

			//console.log("I 'm ready");
			$(".goods-rest-field").css("display", "none");

		});
	</script>
{/block}