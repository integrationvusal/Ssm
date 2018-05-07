Desktop = {
	activeWindow: '',
	windows: [],
	taskbar: TaskbarContainer,
	
	init: function() {
		this.taskbar.containerId = 'bottom-panel-content';
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
		this.setActiveWindow(winObject.id);
	},
	onWindowClose: function(id) {
		var winId = this.getWindow(id);
		delete this.windows[winId];
		var taskbarElementId = 'taskbar-element-' + id;
		$('#' + taskbarElementId).remove();
		$('#' + id).remove();
	},
	reloadWindow: function(id) {
		this.windows[this.getWindow(id)].reload();
	},
	removeWindow: function(id) {
		this.windows.splice(this.getWindow(id), 1);
		this.taskbar.removeWindow(id);
	},
	setLastWindowFocus: function() {
		for (var i in this.windows) {
			if (!this.windows[i].minimized) {
				this.windows[i].setFocus();
				break;
			}
		}
	},
	setActiveWindow: function(id) {
		if (id != this.activeWindow) {
			if (typeof this.windows[this.getWindow(this.activeWindow)] != "undefined") {
				this.windows[this.getWindow(this.activeWindow)].resetFocus();
			}
			this.activeWindow = id;
			this.windows[this.getWindow(this.activeWindow)].setFocus();
		}
	},
	getActiveWindow: function() {
		return this.activeWindow;
	}
}