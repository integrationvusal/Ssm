{block name="content"}
		<form action="{$url}" target="submitForm" method="post" enctype="multipart/form-data">
			<div class="window-inner-content">
				{$treeForm}
				{$modelForm}
			</div>
			<br/>
			<input type="submit" value="{$messages.interface_common.save}" name="saveItem" class="button-std input-std  save-item" />
		</form>
		<iframe src="" name="submitForm" style="display: none;"></iframe>
{/block}