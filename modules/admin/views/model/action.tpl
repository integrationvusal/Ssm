<span id="span-{$randId}"></span>
<script>
	{if isset($success)}
		var success = '{$success}',
		errors = {$errors},
		winId = $('#span-{$randId}').parents('.window').attr('id'),
		multiLang = '{$multilang}';
		if (success) {
			closeWindow(winId);
			showMessage(Lang['info'], Lang['saved']);
		} else {
			var errorsText = '';
			if (multiLang) {
				for (var i in errors) {
					errorsText += Lang[i] + ' ' + Lang['in_lang'] + ':<br/>';
					for (var j in errors[i]) {
						errorsText += errors[i][j] + ' ' + Lang['filled_not_correct'] + '; <br/> ';
					}
				}
			} else {
				// 
			}
			//getWindow(winId).setErrors(errorsText);
			showMessage(Lang['error'], errorsText, 15000);
		}
	{/if}
</script>
<form action="{$url}" target="submitForm" method="post" enctype="multipart/form-data">
	<div class="window-inner-content">
		{$modelForm}
	</div>
	<br/>
	<input type="submit" value="{$messages.interface_common.save}" name="saveItem" class="button-std input-std save-item" />
</form>
<iframe src="" name="submitForm" style="display: none;" ></iframe>