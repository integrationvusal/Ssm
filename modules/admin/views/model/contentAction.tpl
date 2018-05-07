{extends file="model/action-base.tpl"}
{block name="content"}
	<form action="" method="post" enctype="multipart/form-data">
		<div id="content-template-cnt">
			{$modelForm}
			{foreach from=$editItemsForm item=i}
				<div class="content-field">
					{$i}
					<a href="javascript:void(0)" class="remove-content-field-tpl">remove</a>
				</div>
			{/foreach}
			<div class="content-field-template">
				{$fieldForm}
			</div>
		</div>
		<br/>
		<a href="javascript:void(0)" onclick="addContentFieldTemplate();">add</a>
		<input type="submit" value="ok" name="saveItem" />
	</form>
	{literal}
	<script>
		function addContentFieldTemplate() {
			$('.content-field-template').clone().removeClass('content-field-template').addClass('content-field').appendTo('#content-template-cnt');
		}
	</script>
	{/literal}
{/block}