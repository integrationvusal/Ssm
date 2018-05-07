var scrollbarDragger = (function() {

	var dragObject;
	var mouseOffset;
	var startPosition = null;
	var startDragLimit = 4;
	var elementOffsetLeft = null;
	var elementOffsetTop = null;
	var scrollableLayer;
	var containerHeight;

	// получить сдвиг target относительно курсора мыши
	function getMouseOffset(target, e) {
		var docPos	= getPosition(target)
		return {x:e.pageX - docPos.x, y:e.pageY - docPos.y}
	}

	function mouseUp(e){
		e = fixEvent(e)
		
		dragObject = null;
		// очистить обработчики, т.к перенос закончен
		document.onmousemove = null;
		document.onmouseup = null;
		document.ondragstart = null;
		document.body.onselectstart = null;
		
		startPosition = null;
	}

	function mouseMove(e){
		e = fixEvent(e)
		
		if (startPosition && ((Math.abs(e.pageX - startPosition.x) > startDragLimit) || (Math.abs(e.pageY - startPosition.y) > startDragLimit))) {
			var top = (parseInt(e.pageY) - parseInt(elementOffsetTop) - mouseOffset.y);
			var drObjHeight = parseInt(dragObject.height());
			if ((top > 0) && ((top + drObjHeight) <= containerHeight)) {
				dragObject.css('top', top + 'px');
				var percent = top / ((containerHeight) / 100);
				var layerTop = (percent * scrollableLayer.height) / 100;
				scrollableLayer.obj.css({
					'top': (-layerTop) + 'px'
				});
			}
		}
		
		return false
	}

	function mouseDown(e) {
		e = fixEvent(e)
		if (e.which!=1) return
		
		startPosition = {x: e.pageX, y: e.pageY};
		
		dragObject = $(this);

		// получить сдвиг элемента относительно курсора мыши
		mouseOffset = getMouseOffset(this, e)

		// эти обработчики отслеживают процесс и окончание переноса
		document.onmousemove = mouseMove
		document.onmouseup = mouseUp

		// отменить перенос и выделение текста при клике на тексте
		document.ondragstart = function() { return false }
		document.body.onselectstart = function() { return false }

		elementOffsetLeft = dragObject.parent().offset().left;
		elementOffsetTop = dragObject.parent().offset().top;
		
		return false
	}

	return {
		makeDraggable: function(element, layer, height) {
			containerHeight = height;
			scrollableLayer = {
				obj: layer,
				height: parseInt(layer.height())
			};
			$(element).unbind('mousedown').mousedown(mouseDown);
			return this;
		}
	}

}())
