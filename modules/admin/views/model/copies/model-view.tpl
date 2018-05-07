<div class="window-tab-content tab-0" title="tab button">
	<div class="block-container">
		<div class="block-content-container">
			<div class="block-content">
				{$model_content}
				{$paginator}
			</div>
			<div class="gray-button model-delete-checked" model-id="{$model_id}">Delete checked</div>
			<div class="new-window gray-button" title="Add" reload-parent="1" data-url="{$admin_title}/add/{$model_id}">Add</div>
		</div>
		<div class="clear"></div>
	</div>
</div>
<div class="window-tab-content tab-1"></div>
<div class="window-tab-content tab-2"></div>