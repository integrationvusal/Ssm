<div class="field-container selectfield-div">
	<div class="left">
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
		if (typeof selectIndex == 'undefined') selectIndex = 0;
		if (typeof select == 'undefined') select = [];
		if (typeof selectSelf == 'undefined') selectSelf = [];
		select[selectIndex] = new SelectController('#{$elementId}');
		selectSelf[selectIndex] = select[selectIndex].getSelf();
		selectSelf[selectIndex].onChange = function(key){
			switch(parseInt(key)) {
				case 1:
					$(this.identifier).parents('.window').find('.objects-item-map').hide();
					$(this.identifier).parents('.window').find('.objects-item-object').show();
					break;
				case 2:
					$(this.identifier).parents('.window').find('.objects-item-map').show();
					$(this.identifier).parents('.window').find('.objects-item-object').hide();
					break;
				case 3:
					$(this.identifier).parents('.window').find('.objects-item-map, .objects-item-object').show();
					break;
			}
		}
		select[selectIndex].setSelf(selectSelf[selectIndex]);
		{if isset($value)}
			select[selectIndex].setValue('{$value}');
		{else}
			select[selectIndex].setValue(3);
		{/if}
		selectIndex++;
	</script>
	<div class="clear"></div>
</div>