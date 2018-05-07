{if $current_page > 0}
	<a href="javascript:void(0)" url="view/{$model_id}/page/{$current_page - 1}" class="block-reload paginator-item paginator-item-arrow" ><img src="{$static_url}/{$theme_folder}/img/paginator-to-left-arrow.png" /></a>
{/if}
{section start=1 loop=$count + 1 step=1 name=paginator }
	<a href="javascript:void(0)" url="view/{$model_id}/page/{$smarty.section.paginator.index - 1}" class="block-reload paginator-item {if $smarty.section.paginator.index == ($current_page + 1)}paginator-item-active{/if}" >{$smarty.section.paginator.index}</a>
{/section}
{if $current_page < count($count)}
	<a href="javascript:void(0)" url="view/{$model_id}/page/{$current_page + 1}" class="block-reload paginator-item paginator-item-arrow" ><img src="{$static_url}/{$theme_folder}/img/paginator-to-right-arrow.png" /></a>
{/if}