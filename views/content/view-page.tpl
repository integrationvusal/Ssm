{extends file="base.tpl"}

{block name="page-title"}
	:: {$page->menuItemTitle->value}
{/block}

{block name="content"}
	<div class="fixed show-in-767 content-child-elements">
		<div class="content-child-elements-left">
			{if count($childPages)}
				<div class="view-page-childs">
					{foreach from=$childPages item=p}
						<div class="child-menu">
							<div class="child-menu-pointer">
								<img src="{$static_url}/img/child-page-pointer.png" />
							</div>
							<div class="child-menu-text">
								{if $page->r_id->value == $p->r_id->value}
									<span class="font15" >{$p->menuItemTitle->value}</span>
								{else}
									<a href="{$p->url->value}" class="font15" >{$p->menuItemTitle->value}</a>
								{/if}
							</div>
							<div class="clear"></div>
						</div>
					{/foreach}
				</div>
			{/if}
		</div>
		<div class="content-child-elements-right">
			<div class="content-child-elements-pointer" id="show-content-childs-menu">
				<img src="{$static_url}/img/arrow-to-right.png" class="to-right" />
				<img src="{$static_url}/img/arrow-to-left.png" class="to-left hide" />
			</div>
		</div>
		<div class="clear"></div>
	</div>
	{if count($childPages)}
		<div class="view-page-childs hide-in-767">
			{foreach from=$childPages item=p}
				<div class="child-menu">
					<div class="child-menu-pointer">
						<img src="{$static_url}/img/child-page-pointer.png" />
					</div>
					<div class="child-menu-text">
						{if $page->r_id->value == $p->r_id->value}
							<span class="font15" >{$p->menuItemTitle->value}</span>
						{else}
							<a href="{$p->url->value}" class="font15" >{$p->menuItemTitle->value}</a>
						{/if}
					</div>
					<div class="clear"></div>
				</div>
			{/foreach}
		</div>
	{/if}
	<div class="clear show-in-767"></div>
	<div class="view-page-content view-page-content-small font16 relative" {if !count($childPages)}style="width: 96%;"{/if}>
		<div class="page-title font25">{$page->menuItemTitle->value}</div>
		{$page->content->value}
		<br/>
		<div class="fb-like" data-href="{$request_url}" data-send="true" data-layout="button_count" data-width="450" data-show-faces="false" data-font="arial"></div>
	</div>
	<div class="clear"></div>
	
{/block}