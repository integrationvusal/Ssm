<div class="field-container checkboxfield-div  {if isset($htmlCss.class)}{$htmlCss.class}{/if}">
	<div class="left field-title">
		{$title} {if isset($lang) && !empty($lang)}[{$lang}]{/if}
	</div>
	<div class="right">
		{foreach from=$keyValue key=k item=v name=chkbx}
			{if $smarty.foreach.chkbx.index != 0 && $smarty.foreach.chkbx.index % 2 == 0}<div class="clear"></div>{/if}
			<div id="checkbox-{$elementId}-{$smarty.foreach.chkbx.index}" class="left checkbox-controller-main-container">
				<div class="checkbox-controller-container" val="{$k}" key="{$name}[]">
					<div class="checkbox-controller-icon {if in_array($k, $value)}checkbox-controller-checked{/if}"></div>
					<div class="checkbox-controller-title">{$v}</div>
					<div class="clear"></div>
					<input type="hidden" name="{$name}[]" value="{if in_array($k, $value)}{$k}{/if}" />
				</div>
			</div>
			<script>
				new CheckBoxController('#checkbox-{$elementId}-{$smarty.foreach.chkbx.index}'{if in_array($k, $value)},true{/if});
			</script>
		{/foreach}
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>