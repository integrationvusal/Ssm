function HorizontalSliderController(identifier) {
	
	var self = $(identifier),
		sliderToLeftButton = self.find('.horizontal-slider-to-left'),
		sliderToRightButton = self.find('.horizontal-slider-to-right'),
		sliderWrapper = self.find('.horizontal-slider-wrapper'),
		sliderRibbon = self.find('.horizontal-slider-ribbon'),
		sliderItemId = '.horizontal-slider-item';
	
	var slider = {};
	
	init();
	
	function init() {
		//sliderToLeft.css('visibility', 'hidden');
		slider.left = 0;
		onResize();
		
		sliderToLeftButton.click(slideToLeft);
		sliderToRightButton.click(slideToRight);
		correctSliderButtons();
	}
	
	function onResize() {
		var sliderItem = $(sliderItemId);
		var sliderItemWidth = parseInt(sliderItem.width()) + 
			parseInt(sliderItem.css('margin-left')) +
			parseInt(sliderItem.css('margin-right')) +
			parseInt(sliderItem.css('padding-left')) +
			parseInt(sliderItem.css('padding-right'));
		
		slider.width = sliderItemWidth * sliderItem.length;
		slider.wrapperWidth = parseInt(sliderWrapper.width());
		slider.itemWidth = sliderItemWidth;
		slider.left = parseInt(slider.left - (slider.left % slider.itemWidth));
		//slideTo(slider.left);
	}
	
	// actions
	
	function correctSliderButtons() {
		if (slider.left < 0) {
			sliderToLeftButton.find('.active-pointer').removeClass('hide');
			sliderToLeftButton.find('.inactive-pointer').addClass('hide');
		}
		else {
			sliderToLeftButton.find('.active-pointer').addClass('hide');
			sliderToLeftButton.find('.inactive-pointer').removeClass('hide');
		}
		if ((slider.left + slider.width) > slider.wrapperWidth) {
			sliderToRightButton.find('.active-pointer').removeClass('hide');
			sliderToRightButton.find('.inactive-pointer').addClass('hide');
		}
		else {
			sliderToRightButton.find('.inactive-pointer').removeClass('hide');
			sliderToRightButton.find('.active-pointer').addClass('hide');
		}
	}
	
	function slideToLeft() {
		if (slider.left < 0) {
			slider.left += slider.itemWidth;
			slideTo(slider.left, 10);
		}
	}
	
	function slideToRight() {
		if ((slider.left + slider.width) > slider.wrapperWidth) {
			slider.left -= slider.itemWidth;
			slideTo(slider.left, -10);
		}
	}
	
	function slideTo(left, infelicity) {
		sliderRibbon.animate({
			left: (left + infelicity) + 'px'
		}, 500, function() {
			sliderRibbon.animate({
				left: left + 'px'
			}, 100, function() {
				slider.left = left;
				correctSliderButtons();
			});
		});
	}
	
	return {
		onResize: function() {
			onResize();
		}
	}
}