function Window(title, content, url, onLoad, onClose) {
	var id = 'window' + Math.floor(Math.random()*9999),
	rootElement = $($('#window-template').html()).attr('id',id),
	self = this,
	minimized = false,
	header = rootElement.find('.window-header'),
	contentContainer = rootElement.find('.window-content'),
	contentContainerWrapper = rootElement.find('.window-content-container'),
	activeTabButton = 0,
	scrollableLayer = rootElement.find('.window-content'),
	scrollBar = rootElement.find('.scroll-bar-container'),
	tabButtonsContainer = rootElement.find('.window-header-tabs-container'),
	errorsContainer = rootElement.find('.window-errors-container');
	
	init();
	
	function init() {
	
		show();
		onLoad();
		setTitle(title);
		setContent(content);
		
		// init tabs
		initTabs();
		
		correctSize();
		
		// calculate window position
		var windowPosition = {
			left: (Desktop.windows.length % 6) * 50 + ((Desktop.windows.length / 6) * 50) + 200,
			top: (Desktop.windows.length % 6) * 50 + 67
		}
		
		$('#' + id).css({
			left: windowPosition.left,
			top: windowPosition.top
		});
		/* buttons */
		header.find('.minimize-button').click(minimizeWindow);
		
		header.find('.maximize-button').click(restoreWindow);
		
		header.find('.close-button').click(closeWindow);
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
		
		rootElement.mousedown(function(){
			Desktop.setActiveWindow(id);
		});
		
		header.mousedown(function(){
			Desktop.setActiveWindow(id);
		});
	}
	
	function setErrors(errors) {
		errorsContainer.html(errors);
	}
	
	function reInit() {
		// init tabs
		initTabs();
		
		correctSize();
	}
	
	function initTabs() {
		header.find('.tab-' + activeTabButton).removeClass('window-header-tab-button-active');
		contentContainer.find('.tab-' + activeTabButton).hide();
		activeTabButton = 0;
		
		if (tabsCount == 0) return;
		var tabsCount = contentContainer.find('.window-tab-content').length,
		tabsContainer = rootElement.find('.window-header-tabs-container');
		
		tabsContainer.html('');
		var tabButtonTemplate = $('#tab-button-container').html();
		
		for (var i = 0; i < tabsCount; i++) {
			var title = contentContainer.find('.tab-' + i).attr('title');
			var tabButton = tmpl(tabButtonTemplate, {index: i, title:title});
			$(tabsContainer).append(tabButton);
			(i == 0) ? contentContainer.find('.tab-' + i).show() : contentContainer.find('.tab-' + i).hide();
		}
		$(tabsContainer).append('<div class="clear"></div>');
		tabsContainer.find('.tab-0').addClass('window-header-tab-button-active');
		$('.window-header-tab-button').click(tabButtonClicked);
	}
	
	function tabButtonClicked() {
		tabButtonsContainer.find('.tab-' + activeTabButton).removeClass('window-header-tab-button-active');
		contentContainer.find('.tab-' + activeTabButton).hide();
		activeTabButton = parseInt($(this).index());
		tabButtonsContainer.find('.tab-' + activeTabButton).addClass('window-header-tab-button-active');
		contentContainer.find('.tab-' + activeTabButton).show();
		
	}
	
	function correctSize() {
		var headerHeight = parseInt(header.height()),
		percent = parseInt(parseInt(document.documentElement.offsetHeight) / 100),
		windowHeight = percent * 78,
		contentMaxHeight = parseInt(windowHeight - (2 * headerHeight)),
		contentContainerHeight = parseInt($('#' + id).find('.window-content').height());
		
		if (contentContainerHeight > contentMaxHeight) {
			$('#' + id).find('.window-content-container').css({
				'height': contentMaxHeight + 'px',
				'overflow-y': 'scroll'
			});
			$('#' + id).height(parseInt(contentMaxHeight + 2*headerHeight + parseInt(tabButtonsContainer.height())));
		} else {
			$('#' + id).find('.window-content-container').css({
				'height': 'auto',
				'overflow': 'auto'
			});
			$('#' + id).height('auto');
		}
		
		//$('#' + id).height(windowHeight + 'px');
		//$('#' + id).find('.window-content-container').height(contentMaxHeight + 'px');
		
	}
	
	function show() {
		rootElement.appendTo('#desktop-container');
	}
	
	function setFocus() {
		rootElement.addClass('active-window');
	}
	
	function resetFocus() {
		rootElement.removeClass('active-window');
	}
	
	function setTitle(title) {
		header.find('.window-title').html(title);
	}
	
	function reload() {
		$.ajax({
			type: 'post',
			url: url,
			success: function(content) {
				setContent(content);
				initTabs();
				onLoad();
			}
		});
	}
	
	function setContent(c) {
		contentContainer.html(c);
	}
	
	function minimizeWindow() {
		$('#' + id).hide();
	}
	
	function restoreWindow() {
		$('#' + id).show();
		initTabs();
	}
	
	function closeWindow() {
		$('#' + id).remove();
		Desktop.removeWindow(id);
		onClose();
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

	return {
		id: id,
		url: url,
		title: title,
		minimized: minimized,
		closeWindow: function() {
			closeWindow();
		},
		setContent: function (c) {
			setContent(c);
		},
		reload: function() {
			reload();
		},
		reInit: function(){
			reInit();
		},
		minimize: function() {
			minimizeWindow();
		},
		restoreWindow: function() {
			restoreWindow();
		},
		setFocus: function() {
			setFocus();
		},
		setErrors: function(e) {
			setErrors(e);
		},
		resetFocus: function() {
			resetFocus();
		},
		dragStarted: function() {
			//contentContainer.css('visibility','hidden');
			rootElement.addClass('window-drag-started');
		},
		dragStopped: function() {
			//contentContainer.css('visibility','visible');
			rootElement.removeClass('window-drag-started');
		},
		correctSize: function() {
			correctSize();
		}
	}
}