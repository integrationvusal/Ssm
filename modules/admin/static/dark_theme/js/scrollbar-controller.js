function ScrollBarController(scrollBar, scrollableLayer, maxHeight) {
	init();
	function init() {
		$(scrollBar).height(((maxHeight * maxHeight) / scrollableLayer.height()));
		scrollbarDragger.makeDraggable(scrollBar, scrollableLayer, maxHeight);
	}
	
}