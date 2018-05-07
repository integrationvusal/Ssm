{extends file="model/action-base.tpl"}
{block name="content"}
	<form action="{$url}" method="post" target="submitForm" enctype="multipart/form-data">
		<div class="form-container">
		{$modelForm}
		{if isset($existItems)}{$existItems}{/if}
		</div>
		{foreach from=$allBlocks item=v key=k}
			<a href="javascript:void(0)" block-id="{$k}" lang="{$lang}" class="add-block" style="color: #fff;" >{$v}</a><br/>
		{/foreach}
		{if isset($existsBlocks)}{$existsBlocks}{/if}
		<input type="submit" value="ok" name="saveItem" />
	</form>
	<iframe src="" name="submitForm" style="display: none;"></iframe>
{/block}