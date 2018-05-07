function Window(title, content, url, onload, onclose) {
	var id = 'window' + Math.floor(Math.random()*9999);
	var rootElement = $('.window-template').clone().removeClass('hide').removeClass('window-template').attr('id',id);
	var self = this;
	
	this.minimized = false;
	
	rootElement.appendTo('body');
	
	var header = rootElement.find('.window-header');
	
	var contentContainer = rootElement.find('.window-content');
	
	contentContainer.html(content);
	
	contentContainer.click(setFocus);
	
	onload();
	
	setFocus();
	
	var percent = parseInt(parseInt(document.documentElement.offsetHeight) / 100),
	windowHeight = percent * 80;
	$('#' + id).height(windowHeight + 'px');
	$('#' + id).find('.window-content').height($('#' + id).height() - 3*25 + 'px');
	
	/* set window title */
	header.find('.window-title').html(title);
	/* set window title end */
	function setFocus() {
		$('#' + activeWindow).removeClass('active-window');
		$('#' + id).addClass('active-window');
		self.minimized = false;
		
		$('#taskbar-element-' + activeWindow).removeClass('taskbar-element-active');
		$('#taskbar-element-' + id).addClass('taskbar-element-active');
		activeWindow = id;
	}
	/* function set window as active */
	
	/* buttons */
	header.find('.minimize-button').click(function(){
		Desktop.minimizeWindow(id);
	});
	
	header.find('.maximize-button').click(function(){
		Desktop.maximizeWindow(id);
	});
	
	header.find('.close-button').click(function(){
		Desktop.closeWindow(id);
		onclose();
	});
	/* buttons end */
	
	/* Drag & Drop */
	
	dragMaster.makeDraggable($('#' + id).find('.window-header'));
	
	/* Drag & Drop end */
	
	/* resizing */
	
	Resizer.makeResizable($('#' + id).find('.window-resizer'));
	
	/* resizing end */
	
	/* right click on content */
	contentContainer.bind("contextmenu",function(e){
	   //showPopup(e);
	   //return false;
	}); 
	/* right click on content end */
	
	/* common functions */
	
	function setContent(c) {
		rootElement.find('.window-content').html(c);
	}
	
	function showPopup(e) {
		$('#window-popup').css({'left': e.pageX, 'top' : e.pageY}).removeClass('hide');
		$('#window-popup').find('.popup-item').unbind('click').click(function(){
			if (parseInt($(this).attr('action')) == 0) reload();
			$('#window-popup').addClass('hide');
		});
	}
	
	function hidePopup() {
		$('#window-popup').addClass('hide');
	}
	
	function reload() {
		$.ajax({
			type: 'post',
			url: url,
			success: function(content) {
				setContent(content);
				onload();
			}
		});
	}
	
	/* common functions end */
	
	return {
		id: id,
		url: url,
		title: title,
		minimized: self.minimized,
		setContent: function (c){
			setContent(c);
		},
		reload: function() {
			reload();
		},
		maximize: function() {
			//Desktop.restoreWindow(id);
		},
		minimize: function() {
			Desktop.minimizeWindow(id);
		},
		restoreWindow: function() {
			Desktop.restoreWindow(id);
		},
		setFocus: function() {
			setFocus();
		},
		dragStarted: function() {
			contentContainer.css('visibility','hidden');
		},
		dragStopped: function() {
			contentContainer.css('visibility','visible');
		}
	}
}