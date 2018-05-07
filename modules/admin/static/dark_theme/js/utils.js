function showMessage(title, message) {
	var messageTpl = $('#message-tpl').html(),
	id = 'message-' + Math.floor(Math.random()*9999);
	var t, closeInterval = 5000;
	if (typeof arguments[2] != 'undefined') closeInterval = arguments[2];
	
	
	if (!$('.message-container').length) {
		t = tmpl(messageTpl, {title: title, content: message, id: id});
		init();
	} else {
		id = $('.message-container').attr('id');
		setData($('#' + id));
		var i = setInterval(function(){
			hide();
			clearInterval(i);
		}, closeInterval);
	}
	
	function init() {
		$(t).appendTo('body');
		$('#' + id).animate({
			top: '10px',
		}, 1000);
		
		$('#' + id).find('.message-close').click(hide);
		var i = setInterval(function(){
			hide();
			//clearInterval(i);
		}, closeInterval);
	}
	
	function hide() {
		$('#' + id).animate({
			top: '-200px',
		}, 1000, function(){
			$('#' + id).remove();
		});
	}
	
	function setData(el) {
		el.find('.message-title').html(title);
		el.find('.message-content').html(message);
	}
	
	
}