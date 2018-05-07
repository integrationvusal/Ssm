{assign var="id" value=10000|rand:99999}
<div class="filemanager-main-container" id="fm-{$id}">
	<div class="fm-instruments-panel">
		<div class="fm-button create-folder-container">
			<div class="button-std create-folder-button">{$messages.file_manager.create_folder}</div>
			<div class="folder-name-container hide">
				<input type="text" class="folder-name textfield-input left" style="box-shadow: none; width: auto;" onclick="this.value=''" value="{$messages.file_manager.folder_name}" />
				<div class="fm-button create-folder button-std create-folder-submit-button left">ok</div>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
		</div>
		<div class="fm-button button-std upload-file">{$messages.file_manager.upload_file}</div>
		<div class="clear"></div>
		<input type="file" class="file-to-upload-input hide" />
	</div>
	<div class="fm-entry">
		{if isset($entries)}{$entries}{/if}
	</div>
	<div class="clear"></div>
</div>

<script>
	new FileManager({
		container: $('#fm-{$id}'),
		allowActions: 1,
	});
</script>