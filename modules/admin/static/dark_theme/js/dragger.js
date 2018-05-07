var activeWindow;

function fixEvent(e) {
	e = e || window.event
	if ( e.pageX == null && e.clientX != null ) {
		var html = document.documentElement
		var body = document.body
		e.pageX = e.clientX + (html && html.scrollLeft || body && body.scrollLeft || 0) - (html.clientLeft || 0)
		e.pageY = e.clientY + (html && html.scrollTop || body && body.scrollTop || 0) - (html.clientTop || 0)
	}
	if (!e.which && e.button) {
		e.which = e.button & 1 ? 1 : ( e.button & 2 ? 3 : ( e.button & 4 ? 2 : 0 ) )
	}
	return e
}

var dragMaster = (function() {

	var dragObject;
	var mouseOffset;

	// получить сдвиг target относительно курсора мыши
	function getMouseOffset(target, e) {
		var docPos	= getPosition(target)
		return {x:e.pageX - docPos.x, y:e.pageY - docPos.y}
	}

	function mouseUp(e){
	
		var id = dragObject.attr('id');
		Desktop.windows[Desktop.getWindow(id)].dragStopped();

		if (parseInt(dragObject.css('top')) < 0) dragObject.css('top', '0px');
		
		dragObject = null

		// очистить обработчики, т.к перенос закончен
		document.onmousemove = null
		document.onmouseup = null
		document.ondragstart = null
		document.body.onselectstart = null
	}

	function mouseMove(e){
		e = fixEvent(e)
		dragObject.css('top', e.pageY - mouseOffset.y + 'px');
		dragObject.css('left', e.pageX - mouseOffset.x + 'px');
		return false
	}

	function mouseDown(e) {
		e = fixEvent(e)
		if (e.which!=1) return
		
		dragObject  = $(this).parents('.window');
		var id = $(this).parents('.window').attr('id');
		
		Desktop.windows[Desktop.getWindow(id)].setFocus();
		Desktop.windows[Desktop.getWindow(id)].dragStarted();

		// получить сдвиг элемента относительно курсора мыши
		mouseOffset = getMouseOffset(this, e)

		// эти обработчики отслеживают процесс и окончание переноса
		document.onmousemove = mouseMove
		document.onmouseup = mouseUp

		// отменить перенос и выделение текста при клике на тексте
		document.ondragstart = function() { return false }
		document.body.onselectstart = function() { return false }

		return false
	}

	return {
		makeDraggable: function(element) {
			$(element).mousedown(mouseDown);
			return this;
		}
	}

}())

function getPosition(e){
	var left = 0
	var top  = 0

	while (e.offsetParent){
		left += e.offsetLeft
		top  += e.offsetTop
		e	 = e.offsetParent
	}

	left += e.offsetLeft
	top  += e.offsetTop

	return {x:left, y:top}
}

/*

function Dragger(rootElement, header, id) {
	var allowDrag = false;
	var position, mouseOffset;

	document.onmousemove = mouseMove;

	function mouseMove(event) {
		event = fixEvent(event);
		if (allowDrag) {
			rootElement.style.left = e.pageX - mouseOffset.x + 'px';
			rootElement.style.top = e.pageY - mouseOffset.y + 'px';
		}
	}

	
	function dragStart() {
		$('#' + id).addClass('drag-started');
	}
	
	function dragEnd() {
		$('#' + id).removeClass('drag-started')
	}
	
	header.mousedown(function(e){
		allowDrag = true;
		
		// setFocus
		Desktop.windows[Desktop.getWindow(id)].setFocus();
		if (document.getElementById(activeWindow)) $('#' + activeWindow).removeClass('active-window');
		$('#' + id).addClass('active-window');
		activeWindow = id;
		
		position = {
			x: parseInt(rootElement.offsetLeft),
			y: parseInt(rootElement.offsetTop),
		}
		
		mouseOffset = {
			x: parseInt(e.pageX) - position.x,
			y: parseInt(e.pageY) - position.y
		}
		
		dragStart();
		
		return false;
	});
	
	header.mouseup(function(e){
		allowDrag = false;
		dragEnd();
		return false;
	});
	
	header.mouseout(function(e){
		allowDrag = false;
		dragEnd();
		return false;
	});
	
	header.mousemove(function(e){
		
		return false;
	});
	
	header.click(function(){
		
	});
	
	return {
		
	}
}





/*
function Resizer(rootElement, borderWidth) {
	var allowResize = false;
	var position, mouseOffset;

	rootElement.mousedown(function(e){
		allowResize = true;
		
		position = rootElement.position();
		
		mouseOffset = {
			x: e.pageX - position.left,
			y: e.pageY - position.top,
		}
		
		return false;
	});
	
	rootElement.mouseup(function(e){
		allowResize = false;
		return false;
	});
	
	rootElement.mouseout(function(e){
		//allowResize = false;
		return false;
	});
	
	rootElement.mousemove(function(e){
		if (allowResize) {
			rootElement.width(e.pageX - position.left + 'px');
			console.log(mouseOffset);
			//rootElement.css('top', e.pageY - mouseOffset.y + 'px');
		}
		return false;
	});
}
*/