<div class="field-container selectfield-div  {if isset($htmlCss.class)}{$htmlCss.class}{/if}">
	<div class="left field-title">
		{$title} {if isset($lang) && !empty($lang)}[{$lang}]{/if}
	</div>
	<div class="right" id="{$elementId}">
		<div class="selectfield-container relative">
			<div class="select-container relative">
				<div class="selected-value-container">
					<div class="selected-value left">
						{if isset($value)}{$keyValue.$value}{else}{$keyValue.$defaultValue}{/if}
					</div>
					<div class="selected-value-pointer"></div>
					<div class="clear"></div>
				</div>
				<div class="select-options-container absolute hide">
					{foreach from=$keyValue key=k item=v}
						<div class="select-option" key="{$k}" >{$v}</div>
					{/foreach}
				</div>
				<input type="hidden" name="{$name}" value="{$value}"/>
			</div>
		</div>
	</div>
	<script>
		new SelectController('#{$elementId}');
	</script>
	<div class="clear"></div>
</div>