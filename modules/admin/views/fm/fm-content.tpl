<div class="fm-current-dir">
	{foreach from=$currentDir item=d name=fm_dirs}
		<div class="fm-item" path="{$d.path}">
			<div class="fm-dir-item fm-folder">
				{$d.title}
			</div>
		</div>
		{if !$smarty.foreach.fm_dirs.last}<div class="left" style="color: #fff; margin-top: 2px;">&raquo;</div>{/if}
	{/foreach}
	<div class="clear"></div>
</div>

<div class="filemanager-container">
	{if $d.path != $startDir}
		<div class="fm-item current-dir" path="{$d.path}/.." type="0">
			<div class="fm-icon fm-folder">
				<img src="{$app_url}/modules/{$admin_title}/static/{$theme_folder}/img/folder-icon.png" />
			</div>
			<div class="fm-item-title fm-folder">
				{$d.title}
			</div>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	{/if}
	{if count($entries) > 1}
	<table class="file-manager-table" cellpadding="0" cellspacing="0">
		{foreach from=$entries item=e}
			{if $e.title != '..'}
			<tr class="{if $e.is_image == 1}fm-image{/if} fm-item" path="{$e.path}" type="{$e.type}">
				<td class="{if $e.type == 1}fm-file{/if}" {if $d.path != $startDir}style="padding-left: 50px;"{/if}>
					<div class="fm-icon {if $e.type == '0'}fm-folder{/if}">
						{if $e.type == '1'}
							{if $e.is_image == 1}
								<img src="{$app_url}/imageresizer/resize/50/50/{$e.path}" />
							{else}
								<img src="{$app_url}/modules/{$admin_title}/static/{$theme_folder}/img/file-icon.png" />
							{/if}
						{else}
							<img src="{$app_url}/modules/{$admin_title}/static/{$theme_folder}/img/folder-icon.png" />
						{/if}
						
					</div>
				</td>
				<td class="fm-item-title {if $e.type == '0'}fm-folder{/if}" style="vertical-align: middle;">
					{if $e.title == '..'}
						{$messages.file_manager.back}
					{else}
						{$e.title}
					{/if}
				</td>
				<td style="vertical-align: middle;">
					{if $allowActions}
						<div class="fm-item-delete rm-item"><img src="{$app_url}/modules/{$admin_title}/static/{$theme_folder}/img/delete-icon-dark.png" /></div>
					{/if}
					
				</td>
			</tr>
			{/if}
		{/foreach}
	</table>
	{/if}
</div>
<div class="clear"></div>