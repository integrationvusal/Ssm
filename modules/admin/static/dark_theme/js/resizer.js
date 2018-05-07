
var Resizer = (function() {

	var resizeObject;
	var mouseOffset;
	var winObj = null;

	function getMouseOffset(target, e) {
		var docPos = getPosition(target)
		return {x:e.pageX - docPos.x, y:e.pageY - docPos.y}
	}

	function mouseUp(e){
	
		var id = resizeObject.attr('id');

		//if (parseInt(resizeObject.css('top')) < 0) resizeObject.css('top', '0px');
		
		resizeObject = null
		document.onmousemove = null
		document.onmouseup = null
		document.ondragstart = null
		document.body.onselectstart = null
	}

	function mouseMove(e){
		e = fixEvent(e)
		var left = parseInt(resizeObject.css('left')),
		top = parseInt(resizeObject.css('top'));
		
		//resizeObject.css('height', e.pageY + mouseOffset.y - top + 'px');
		resizeObject.css('width', e.pageX + mouseOffset.x - left + 'px');
		
		winObj.onResize();
		
		return false
	}

	function mouseDown(e) {
		e = fixEvent(e)
		if (e.which!=1) return;
		resizeObject  = $(this).parents('.window');
		winObj = getWindow($(this).parents('.window').attr('id'));
		mouseOffset = getMouseOffset(this, e)
		document.onmousemove = mouseMove;
		document.onmouseup = mouseUp;

		return false
	}

	return {
		makeResizable: function(element) {
			$(element).mousedown(mouseDown);
			return this;
		}
	}

}())