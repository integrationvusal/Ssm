function BlockUI(title, message) {
	
	var blockId = 'block-' + Math.floor(Math.random()*9999);
	
	init();
	
	function init() {
		var tpl = $('#block-ui-template').html(),
		block = tmpl(tpl, {title: title, message: message, id: blockId});
		
		$(block).appendTo('body');
		
		$('#' + blockId).find('.block-ui-window-close-button').click(closeBlock);
		
	}
	
	function closeBlock() {
		$('#' + blockId).remove();
	}
	
	function setMessage(m) {
		$('#' + blockId).find('.block-ui-window-content').html(m);
	}
	
	return {
		setMessage: function(m) {
			setMessage(m);
		}
	}
}

function showDeleteButton() {
	identifier = arguments[0];
	$(identifier).parents('.window').find('.model-delete-checked').parent().show();
}

function hideDeleteButton(identifier) {
	identifier = arguments[0];
	console.log($(identifier).parents('.window').find('.delete-id:checked').length);
	if ($(identifier).parents('.window').find('.delete-id:checked').length <= 1) {
		$(identifier).parents('.window').find('.model-delete-checked').parent().hide();
	}
}

/* window */

function closeWindow(id) {
	Desktop.windows[Desktop.getWindow(id)].closeWindow();
}

function getWindow(id) {
	return Desktop.windows[Desktop.getWindow(id)];
}

function reloadWindow(id) {
	Desktop.windows[Desktop.getWindow(id)].reload();
}

function newWindow(e,t,u, onLoad, onClose, postData) {
	var title = (typeof t == 'undefined') ? $(this).attr('title') : t;
	if ($(this).attr('have-parent')) {
		var parentId = $(this).parents('.window').attr('id'),
		parentTitle = Desktop.windows[Desktop.getWindow(parentId)].title;
		title =  parentTitle + ' &raquo; ' + title;
	}
	var url = (typeof u == 'undefined') ? $(this).attr('data-url') : u;
	
	if (typeof onLoad == 'undefined') onLoad = new Function();
	if (typeof onClose == 'undefined') {
		if ($(this).attr('reload-parent') == '1') {
			var windowId = $(this).parents('.window').attr('id');
			onClose = function() {
				Desktop.windows[Desktop.getWindow(windowId)].reload();
			}
		} else {
			onClose = new Function();
		}
	}
	
	// sending ajax query
	$.ajax({
		type: 'post',
		url: url,
		data: postData,
		success: function(content) {
			try {
				for (var i in Desktop.windows) {
					if (Desktop.windows[i].url == url) {
						Desktop.windows[i].setContent(content);
						Desktop.windows[i].restoreWindow();
						return;
					}
				}
				Desktop.addWindow(new Window(title, content, url, onLoad, onClose));
			} catch (e) {
				showMessage(e);
			}
		}
	});
}

/* window end */

/* Content */
function removeBlockFieldTpl() {
	if (confirm("?")) {
		var id = $(this).parent().find('.block-field-id').val();
		self = this;
		$.ajax({
			type: 'post',
			url: app['url'] + '/content/blocktemplate/deletefield/' + id,
			success: function(data) {
				$(self).parent().remove();
			}
		});
	}
}
/* Content end */

/* Model functions */
function modelDeleteCheckedItems(modelId) {
	if (confirm(Lang['ready_to_delete'])) {
		var windowId = $(this).parents('.window').attr('id');
		
		var deleteId = [];
		$(this).parents('.window').find('.delete-id:checked').each(function(){
			deleteId.push($(this).attr('item-id'));
		});
		
		var params = 'delete_id[]=' + deleteId.join('&delete_id[]=');
		var blockId = parseInt($(this).parents('.block-container').attr('blockId'));
		var modelId = parseInt($(this).attr('model-id'));
		$.ajax({
			type: 'post',
			data: { 'delete_id[]': deleteId},
			url: app['url'] + '/delete/' + modelId,
			success: function(data) {
				Desktop.reloadWindow(windowId);
			}
		});
	}
}
/* Model functions end */

/* Blocks */
function addBlockToContent() {
	var blockId = parseInt($(this).attr('block-id'));
	var lang = $(this).attr('lang');
	$.ajax({
		type: 'post',
		url: app['url'] + '/content/block/getblock/' + blockId,
		data: 'lang=' + lang,
		success: function(data) {
			$(data).appendTo('.form-container');
		}
	});
}

function removeContentBlock() {
	$(this).parent().remove();
}

function removeContentBlock() {
	var blockId = parseInt($(this).attr('blockId'));
	var self = this;
	if (typeof blockId != 'NaN') {
		$.ajax({
			type: 'get',
			url: app['url'] + '/content/block/removeblock/' + blockId,
			success: function(data) {
				$(self).parents('.content-block-template').remove();
			}
		});
	}
	
}
/* Blocks end */

/* Toggle user settings */
function toggleUserSettings() {
	$('#settings-dropdown').slideToggle();
}
/* Toggle user settings end */

/* paginator */
function paginatorClicked() {
	var url = app['url'] + '/' + $(this).attr('url'),
	windowId = $(this).parents('.window').attr('id');
	$.ajax({
		url: url,
		type: 'post',
		success: function(content) {
			getWindow(windowId).setContent(content);
		}
	});
}
/* paginator end */

$(function(){
	Desktop.init();
	
	$('.paginator-item').live('click', paginatorClicked);
	
	$('.remove-content-field-tpl').live('click',removeBlockFieldTpl);
	
	$('.model-delete-checked').live('click', modelDeleteCheckedItems);
	
	$('.new-window').live('click', newWindow);
	
	$('.add-block').live('click', addBlockToContent);
	$('.remove-content-block').live('click',removeContentBlock);
	$('.remove-block').live('click', removeContentBlock);
	
	$('.show-file-manager').live('click', function(){
		$(this).parent().find('.fm-main-container').slideToggle();
	});
	
	$('#user-avatar-container').click(toggleUserSettings);
	$('.setting-dropdown-item').click(toggleUserSettings);
	

	$('.save-item').live('click', function(){
		var winId = $(this).parents('.window').attr('id');
		$('#' + winId).find('iframe').load(function(){
			getWindow(winId).setContent($(this).contents().find('body').html());
			getWindow(winId).reInit();
		});
	});
	
});