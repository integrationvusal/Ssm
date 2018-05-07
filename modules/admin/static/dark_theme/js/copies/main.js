function showMessage(message) {
	alert(message);
}

/* window */

function newWindow(e,t,u, onLoad, onClose, postData) {
	var title = (typeof t == 'undefined') ? $(this).attr('title') : t,
	url = (typeof u == 'undefined') ? $(this).attr('data-url') : u;
	
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

/* Checkbox field */
function checkBoxClicked() {
	if ($(this).find('.checkbox-icon').hasClass('checkbox-checked')) {
	//uncheck
		$(this).find('input').remove();
		$(this).find('.checkbox-icon').removeClass('checkbox-checked');
	} else {
	//check
		$(this).find('.checkbox-icon').addClass('checkbox-checked');
		var name = $(this).attr('name');
		var value = $(this).attr('value');
		$(this).append('<input type="hidden" name="' + name + '" value="'+ value +'" />');
	}
}
/* Checkbox field end */

/* Selectfield clicked */
function selectClicked() {
	if ($(this).siblings('.selectfield-options').hasClass('hide')) {
		$(this).siblings('.selectfield-options').removeClass('hide');
		$(this).siblings('.selectfield-options').css({'z-index':'20'});
		$(this).css({'z-index':'21'});
	} else {
		$(this).siblings('.selectfield-options').addClass('hide');
		$(this).siblings('.selectfield-options').css({'z-index':'1'});
		$(this).css({'z-index':'2'});
	}
}
function setSelectValue() {
	var key = $(this).attr('key');
	var value = $(this).html();
	$(this).parent().siblings('.selectfield-value').find('span').html(value);
	$(this).parents('.selectfield-container').find('input').val(key);
	if (!$(this).parent().hasClass('hide')) $(this).parent().addClass('hide');
}
/* Selectfield clicked end */

/* Radio button */
function radioButtonClicked() {
	if ($(this).attr('isselected') == '0') {
		var index = $(this).index();
		var name = $(this).parent().attr('name');
		var value = $(this).attr('value');
		$(this).parent().find('.radio-button').each(function(i){
			if (index != i) {
				if ($(this).attr('isselected') == '1') {
					$(this).find('input').remove();
					$(this).attr('isselected','0');
					$(this).find('.radio-icon').css({'background-position':'0 0'});
				}
			}
		});
		$(this).append('<input type="hidden" name="' + name + '" value="'+ value +'" />');
		$(this).attr('isselected','1');
		$(this).find('.radio-icon').css({'background-position':'0 -53px'});
	}
	
}
/* Radio button end */

/* Content */
function removeBlockFieldTpl() {
	if (confirm("?")) {
		var id = $(this).parent().find('.block-field-id').val();
		self = this;
		$.ajax({
			type: 'post',
			url: adminUrl + '/content/blocktemplate/deletefield/' + id,
			success: function(data) {
				$(self).parent().remove();
			}
		});
	}
}
/* Content end */


/* Model functions */
function modelDeleteCheckedItems(modelId) {
	if (confirm("?")) {
		var windowId = $(this).parents('.window').attr('id');
		
		var deleteId = [];
		$(this).siblings('.block-content').find('.delete-id:checked').each(function(){
			deleteId.push($(this).attr('item-id'));
		});
		var params = 'delete_id[]=' + deleteId.join('&delete_id[]=');
		var blockId = parseInt($(this).parents('.block-container').attr('blockId'));
		var modelId = parseInt($(this).attr('model-id'));
		$.ajax({
			type: 'post',
			data: { 'delete_id[]': deleteId},
			url: adminUrl + '/delete/' + modelId,
			success: function(data) {
				Desktop.reloadWindow(windowId);
			}
		});
	}
}
/* Model functions end */

/* Model list check uncheck */
function checkAllListItems() {
	if ($(this).attr('allchecked') == '0') {
		$(this).attr('allchecked','1');
		$(this).parents('.model-list-table').find('.delete-id').each(function(e, index){
			$(this).attr('checked', true);
		});
	} else {
		$(this).attr('allchecked','0');
		$(this).parents('.model-list-table').find('.delete-id').each(function(e, index){
			$(this).attr('checked', false);
		});
	}
}
/* Model list check uncheck end */

/* Blocks */
function addBlockToContent() {
	var blockId = parseInt($(this).attr('block-id'));
	var lang = $(this).attr('lang');
	$.ajax({
		type: 'post',
		url: adminUrl + '/content/block/getblock/' + blockId,
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
			url: adminUrl + '/content/block/removeblock/' + blockId,
			success: function(data) {
				$(self).parents('.content-block-template').remove();
			}
		});
	}
	
}
/* Blocks end */

$(function(){
	Desktop.init();
	
	$('.checkbox-field').live('click',checkBoxClicked);
	$('.selectfield-value').live('click',selectClicked);
	$('.selectfield-option').live('click',setSelectValue);
	$('.radio-button').live('click',radioButtonClicked);
	$('.remove-content-field-tpl').live('click',removeBlockFieldTpl);
	
	$('.check-all').live('click', checkAllListItems);
	$('.model-delete-checked').live('click', modelDeleteCheckedItems);
	
	$('.new-window').live('click', newWindow);
	
	$('.add-block').live('click', addBlockToContent);
	$('.remove-content-block').live('click',removeContentBlock);
	$('.remove-block').live('click', removeContentBlock);

});