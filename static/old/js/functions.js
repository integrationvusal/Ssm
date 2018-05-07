function Stack() {
	var items = [];
	var pointer = 0;
	this.push = function(item) {
		items[pointer++] = item;
	}
	this.pop = function() {
		if (pointer > 0) return items[--pointer];
	}
	this.empty = function()  {
		if (pointer == 0) return true;
		else return false;
	}
}

/* Dropdown menu */
var interval = 500;
var menuStack = [];
menuStack[0] = new Stack();
menuStack[1] = new Stack();

var currentShowing = [];
currentShowing[0] = 0;
currentShowing[1] = 0;

var timers = [];
timers[0] = 0;
timers[1] = 0;

function clearStack(type) {
	if (typeof menuStack[type] != 'undefined') {
		while (!menuStack[type].empty()) {
			var id = menuStack[type].pop();
			$('#' + id).slideUp();
			$('#' + id).css("z-index","20");
		}
	}
}

function mOpen(id, type) {
	killTimer(type);
	if (currentShowing[type] != id) {
		clearStack(type);
		menuStack[type].push(id);
		currentShowing[type] = id;
		$('#' + id).slideDown();
		$('#' + id).css("z-index","2000");
	}
}

function startTimer(id, type) {
	timers[type] = window.setTimeout("mClose('"+id+"', '"+type+"')", interval);
}

function killTimer(type) {
	window.clearTimeout(timers[type]);
}

function mClose(id, type) {
	currentShowing[type] = 0;
	clearStack(type);
}

function showMenu() {
	var id = $(this).attr('toopen');
	var type = $(this).attr('menutype');
	if (typeof id != 'undefined') {
		mOpen(id, type);
	}
}

function hideMenu() {
	var id = $(this).attr('toopen');
	var type = $(this).attr('menutype');
	if (typeof id != 'undefined') {
		startTimer(id, type);
	}
}

/* Dropdown menu end */

function showMessage(title, message) {
	alert(title + ' ' + message);
}

function boolToInt(bool) {
	return (bool) ? 1 : 0;
}