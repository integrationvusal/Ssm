<div class="field-container checkboxfield-div">
	<div class="left">
		{$title}
	</div>
	<div class="right">
		{foreach from=$options key=k item=v}
			<div class="checkbox-field left" name="{$name}[]" value="{$k}">
				<div class="checkbox-icon left"></div>
				<div class="checkbox-title left">{$v}</div>
				<div class="clear"></div>
			</div>
		{/foreach}
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>