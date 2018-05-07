TaskbarContainer = {
	containerId: '',
	
	addWindow: function(winObject) {
		var elementId = 'taskbar-element-' + winObject.id,
		taskbarElement = $(tmpl($('#taskbar-element-template').html(), {id: elementId, title: winObject.title, windowId: winObject.id}));

		$('#' + this.containerId).append(taskbarElement);
		
		$('#' + elementId).addClass('taskbar-element-active');
		
		if ($('#bottom-panel-container').css('display') == 'none') {
			$('#bottom-panel-container').slideToggle();
		}
		
		// click on element
		taskbarElement.click(function(){
			if (document.getElementById(winObject.id))
				if (document.getElementById(winObject.id).style.display == 'none') {
					document.getElementById(winObject.id).style.display = 'block';
					winObject.minimized = false;
					//winObject.setFocus();
					Desktop.setActiveWindow(winObject.id);
				}
				else {
					document.getElementById(winObject.id).style.display = 'none';
					winObject.minimize();
					Desktop.setLastWindowFocus();
				}
			return false;
		});
	},
	removeWindow: function(id) {
		$('#taskbar-element-' + id).remove();
		if (Desktop.windows.length == 0) {
			$('#bottom-panel-container').slideToggle();
		}
	},
	minimizeWindow: function(id) {
		var elementId = 'taskbar-element-' + id;
		$('#' + elementId).removeClass('taskbar-element-active');
	},
	setWindowsInactive: function(id) {
		var elementId = '#taskbar-element-' + id;
		$(id).removeClass('taskbar-element-active');
	}
	
	
}