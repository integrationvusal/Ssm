<div class="field-container textfield-div  {if isset($htmlCss.class)}{$htmlCss.class}{/if}">	<div class="left field-title">		{$title} {if isset($lang) && !empty($lang)}[{$lang}]{/if}	</div>	<div class="right">		<textarea name="{$name}" class="textarea field" >{$value}</textarea>		{if isset($forContent)}			<input type="hidden" name="fieldLang[{$randId}][]" value="{$lang}" />			<input type="hidden" name="fieldId[{$randId}][]" value="{$id}" />			<input type="hidden" name="fieldType[{$randId}][]" value="{$type}" />		{/if}	</div>	<div class="clear"></div></div>