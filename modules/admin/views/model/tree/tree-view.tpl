<div class="tree-items-container" id="tree-{$randomNum}">
</div>
<script>
	var items = {$tree_items};
	(new Tree(items, '#tree-{$randomNum}', {$tree_model_id})).draw('{$tree_model_id}');
</script>