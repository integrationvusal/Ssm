var treeDragger = (function() {

	var dragObject;
	var mouseOffset;
	var startPosition = null;
	var startDragLimit = 4;
	var onItemClick = null;
	var onItemSort = null;
	var elementWindow = null;
	var elementOffsetLeft = null;
	var elementOffsetTop = null;

	// получить сдвиг target относительно курсора мыши
	function getMouseOffset(target, e) {
		var docPos	= getPosition(target)
		return {x:e.pageX - docPos.x, y:e.pageY - docPos.y}
	}

	function mouseUp(e){
		e = fixEvent(e)
		
		if (startPosition && ((Math.abs(e.pageX - startPosition.x) > startDragLimit) || (Math.abs(e.pageY - startPosition.y) > startDragLimit))) {
			pasteElement(e.pageX, e.pageY);
			dragObject.css('position', 'relative');
			dragObject.css('top', '0');
			dragObject.css('left', '0');
		}
		
		
		if (startPosition && ((Math.abs(e.pageX - startPosition.x) < startDragLimit) && (Math.abs(e.pageY - startPosition.y) < startDragLimit))) {
			if (onItemClick) onItemClick();
		}
		
		dragObject = null;

		// очистить обработчики, т.к перенос закончен
		document.onmousemove = null;
		document.onmouseup = null;
		document.ondragstart = null;
		document.body.onselectstart = null;
		
		startPosition = null;
		
		$('.tree-node-sibling').removeClass('tree-node-sibling-ondrag');
		$('.tree-node-title').removeClass('tree-node-title-active');
	}

	function mouseMove(e){
		e = fixEvent(e)
		
		if (startPosition && ((Math.abs(e.pageX - startPosition.x) > startDragLimit) || (Math.abs(e.pageY - startPosition.y) > startDragLimit))) {
			dragObject.css('position', 'absolute');
			dragObject.css('top', (parseInt(e.pageY) - parseInt(elementOffsetTop) - mouseOffset.y) + 'px');
			dragObject.css('left', (parseInt(e.pageX) - parseInt(elementOffsetLeft) - mouseOffset.x) + 'px');
		}
		
		return false
	}
	
	function pasteElement(x, y) {
		
		$(dragObject).hide();
		var element = document.elementFromPoint(x,y);
		$(dragObject).show();
		
		if ($(element).hasClass('tree-node-title') && ($(element).parents('.tree-node-container').first().attr('id') != $(dragObject).attr('id'))) {
			$(dragObject).appendTo($(element).parent().siblings('.tree-node-childs'));
			if (onItemSort) onItemSort();
			return true;
		}
		
		if ($(element).hasClass('tree-node-sibling')  && ($(element) != $(dragObject))) {
			$(dragObject).insertAfter($(element).parent());
			if (onItemSort) onItemSort();
			return true;
		}
		
		return false;
	}

	function mouseDown(e) {
	
		$('.tree-node-sibling').addClass('tree-node-sibling-ondrag');
		$('.tree-node-title').addClass('tree-node-title-active');
	
		e = fixEvent(e)
		if (e.which!=1) return
		
		startPosition = {x: e.pageX, y: e.pageY};
		
		dragObject = $(this).parent();

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
		makeDraggable: function(element, onClick, onSort) {
			elementWindow = $(element).parents('.window');
			$(element).mousedown(mouseDown);
			onItemClick = onClick;
			onItemSort = onSort;
			return this;
		}
	}

}())
