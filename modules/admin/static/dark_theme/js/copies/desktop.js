TaskbarContainer = {
	containerId: '',
	
	addWindow: function(winObject) {
		var elementId = 'taskbar-element-' + winObject.id;
		var taskbarElement = $('.taskbar-element-template').clone().removeClass('hide').removeClass('taskbar-element-template').attr('id', elementId).html(winObject.title);
		$('.' + this.containerId).append(taskbarElement);
		
		$('#' + elementId).addClass('taskbar-element-active');
		
		// click on element
		taskbarElement.click(function(){
			if (document.getElementById(winObject.id))
				if (document.getElementById(winObject.id).style.display == 'none') {
					document.getElementById(winObject.id).style.display = 'block';
					winObject.minimized = false;
					winObject.setFocus();
				}
				else {
					document.getElementById(winObject.id).style.display = 'none';
					winObject.minimize();
					Desktop.setLastWindowFocus();
				}
			return false;
		});
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

Taskbar = {
	container: TaskbarContainer,
	
	addWindow: function(winObject) {
		this.container.addWindow(winObject);
	},
	closeWindow: function(winObject) {
		
	}
}

Desktop = {
	windows: [],
	taskbar: Taskbar,
	
	init: function() {
		this.taskbar.container.containerId = 'taskbar-container';
		$('#start-button').click(this.toggleInstrumentsPanel);
	},
	
	toggleInstrumentsPanel: function() {
		if (!$('#instruments-panel').hasClass('hide')) $('#instruments-panel').addClass('hide');
		else $('#instruments-panel').removeClass('hide');
	},
	
	getWindow: function(id) {
		for (var i in this.windows) {
			if (this.windows[i].id == id) {
				return i;
			}
		}
	},
	addWindow: function(winObject) {
		this.windows.push(winObject);
		this.taskbar.addWindow(winObject);
	},
	closeWindow: function(id) {
		var winId = this.getWindow(id);
		delete this.windows[winId];
		var taskbarElementId = 'taskbar-element-' + id;
		$('#' + taskbarElementId).remove();
		$('#' + id).remove();
	},
	minimizeWindow: function(id) {
		var winId = this.getWindow(id);
		this.windows[winId].minimized = true;
		document.getElementById(id).style.display = 'none';
		this.setLastWindowFocus();
		this.taskbar.container.minimizeWindow(id);
	},
	maximizeWindow: function(id) {
		if (document.getElementById(id).style.width == '100%') {
			document.getElementById(id).style.width = '50%';
			document.getElementById(id).style.height = '50%';
		}
		else {
			document.getElementById(id).style.width = '100%';
			document.getElementById(id).style.height = '100%';
			document.getElementById(id).style.left = '0';
			document.getElementById(id).style.top = '0';
		}
	},
	restoreWindow: function(id) {
		var winId = this.getWindow(id);
		document.getElementById(id).style.display = 'block';
	},
	setLastWindowFocus: function() {
		for (var i in this.windows) {
			if (!this.windows[i].minimized) {
				this.windows[i].setFocus();
				break;
			}
		}
	},
	reloadWindow: function(id) {
		this.windows[this.getWindow(id)].reload();;
	}
}