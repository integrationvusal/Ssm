<div class="field-container selectfield-div">
	<div class="left form-field-title">
		{$title}:
	</div>
	<div class="right form-field-own">
		<select name="{$name}">
			{foreach from=$options key=k item=v}
				<option value="{$k}">{$v}</option>
			{/foreach}
		</select>
	</div>
	<div class="clear"></div>
</div>