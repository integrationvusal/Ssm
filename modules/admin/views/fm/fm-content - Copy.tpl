<div class="fm-main-container">
	<div class="filemanager-container">
		{if $currentDir != $startDir}
		<div class="fm-item fm-back" path="{$currentDir}/..">
			<div class="fm-entry fm-icon">
				back
			</div> 
		</div>
		{/if}
		{foreach from=$entries item=e}
			{if $e.title != '..'}
			<div class="fm-item relative left" path="{$e.path}" type="{$e.type}">
				<div class="fm-entry fm-icon">
					{if $e.is_image == 1}
						<div class="fm-image-container"><img src="{$app_url}/imageresizer/resize/50/50/{$e.path}" /></div>
					{else}
						{if $e.type == '1'}
							<div class="fm-image-container"><img src="{$app_url}/modules/{$admin_title}/static/{$theme_folder}/img/file-icon.png" /></div>
						{else}
							<div class="fm-image-container"><img src="{$app_url}/modules/{$admin_title}/static/{$theme_folder}/img/folder-icon.png" /></div>
						{/if}
					{/if}
					<div class="fm-item-title">{$e.title}</div> 
				</div> 
				<div class="fm-item-delete rm-item"><img src="{$app_url}/modules/{$admin_title}/static/{$theme_folder}/img/window-close-button.png" /></div>
			</div>
			{/if}
		{/foreach}
		<div class="clear"></div>
	</div>
</div>