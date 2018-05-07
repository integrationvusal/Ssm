<div class="block-container">
	<div class="block-content-container">
		<div class="block-content">
			{$model_content}
		</div>
	</div>
	<div class="clear"></div>
</div>
<div class="paginator-container">
	<p align="center">{$paginator}</p>
</div>
<div class="left action-buttons hide">
	<div class="button-std model-delete-checked" model-id="{$model_id}">
		{$messages.interface_common.delete_checked}
	</div>
</div>
<div class="left action-buttons">
	<div class="button-std new-window" model-id="{$model_id}" title="{$messages.interface_common.add}" reload-parent="1" have-parent="1" data-url="{$admin_title}/add/{$model_id}">
		{$messages.interface_common.add}
	</div>
</div>
<div class="clear"></div>