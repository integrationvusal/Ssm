{extends file="base.tpl"}

{block name="page-title"}
	:: {$messages.search.title}
{/block}

{block name="content"}
	<div class="view-page-content font16" style="width: 96%;">
		<div class="page-title news-page-title font25">{$messages.search.title}</div>
		
		{if count($foundData)}
		{foreach from=$foundData item=n}
			<div class="news-archive-item">
				<div class="item-title"><a href="{$app_url}/{$currentLang}/{$n->url->value}" class="font20">{$n->recordTitle->value}</a></div>
				<div class="item-description font13">
					{$n->content->value}
				</div>
			</div>
		{/foreach}
		{else}
		{$messages.search.not_found}
		{/if}
		
		{if count($paginator) > 3}
		<div class="paginator-container">
			<div class="paginator-content">
				{foreach from=$paginator item=p}
					{if $currentPage == $p.key}
						<div class="paginator-item paginator-item-active">
							<span class="font13" >{$p.title}</span>
						</div>
					{else}
						<div class="paginator-item">
							{if isset($p.inactive)}
								<span class="font13" style="color: #000;" >{$p.title}</span>
							{else}
								<a href="{$app_url}/{$currentLang}/search/{$searchText}/page/{$p.key}" class="font13" >{$p.title}</a>
							{/if}
						</div>
					{/if}
				{/foreach}
				<div class="clear"></div>
			</div>
		</div>
		{/if}
		
	</div>
	<div class="clear"></div>
{/block}