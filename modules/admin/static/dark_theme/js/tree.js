function Tree(items, container, treeModelId) {
	
	var nodeIds = [];
	
	init();
	
	function init() {
		
	}
	var first = true;
	
	function draw(items) {
		var out = '';
		for (var i in items) {
			var randomNum = Math.floor((Math.random()*9999)+1),
			data = {id:items[i].id, pId:items[i].parentId, title:items[i].title, randomNum: randomNum, first: first, treeModelId: treeModelId};
			first = false;
			nodeIds.push('#tree-item-' + items[i].id + '-' + randomNum);
			if (typeof items[i].items != 'undefined') {
				data.childs = draw(items[i].items);
				out += tmpl($('#tree-item-tpl').html(), data);
			} else {
				data.childs = '';
				out += tmpl($('#tree-item-tpl').html(), data);
			}
		}
		return out;
	}
	
	return {
		draw: function(modelId) {
			$(container).append(draw(items));
			
			function sortTree() {
				var treeInfo = [];
				$(container).find('.tree-node-container').each(function(index, e){
					var pId = ($(this).parents('.tree-node-container').length) ? $(this).parents('.tree-node-container').first().attr('elId') : 0;
					var rId = $(this).attr('elId');
					treeInfo.push({
						rId: parseInt(rId),
						parentId: parseInt(pId),
						order: index 
					});
				});
				$.ajax({
					type: 'post',
					url: app['url'] + '/structure/sort/' + modelId,
					data: 'treeInfo=' + $.toJSON(treeInfo),
					success: function(data) {
						var winId = $(container).parents('.window').attr('id');
						reloadWindow(winId);
					}
				});
			}

			for (var i in nodeIds) {
				treeDragger.makeDraggable($(container).find(nodeIds[i]).find('.tree-node'), function(){
					
				}, sortTree);
			}
			
			$('.tree-delete-node').click(function(){
				if (confirm(Lang['ready_to_delete'])) {
					var url = $(this).attr('data-url');
					var winId = $(this).parents('.window').attr('id');
					$.ajax({
						type: 'post',
						dataType: 'text',
						url: url,
						success: function(){
							Desktop.windows[Desktop.getWindow(winId)].reload();
						}
					});
				}
			});
			
			$('.tree-node-slide').unbind('click').click(function(){
				$(this).toggleClass('tree-node-slide-active');
				$(this).parent().siblings('.tree-node-childs').slideToggle('fast', function(){
					getWindow($(container).parents('.window').attr('id')).correctSize();
				});
			});
			
			$('.tree-node').hover(function(){
				$(this).find('.tree-node-operations').addClass('visible');
			}, function() {
				$(this).find('.tree-node-operations').removeClass('visible');
			});
		}
	}
}