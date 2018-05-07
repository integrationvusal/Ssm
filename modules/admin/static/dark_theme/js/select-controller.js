function SelectController(identifier) {
	var value, self = $(identifier), optionsVisible = false, optionsContainer = self.find('.select-options-container');
	
	var _self = {
		// props
		identifier: identifier,
		
		// events
		onChange: function(key) {}
	};
	
	this.setSelf = function(__self) {
		_self = __self;
	}
	
	this.getSelf = function() {
		return _self;
	}
	
	this.setValue = function(key) {
		setValue(key);
	}
	
	init();
	
	function init() {
		var firstOption = self.find('.select-option').first();
		if (getInput().val() == "") setValue(firstOption.attr('key'), firstOption.html());
		
		self.find('.selected-value, .selected-value-pointer').click(toggleOptions);
		self.find('.select-option').click(optionClicked);
	}
	
	function toggleOptions() {
		if (optionsVisible) hideOptions();
		else showOptions();
	}
	
	function showOptions() {
		optionsVisible = true;
		optionsContainer.show();
	}
	
	function hideOptions() {
		optionsVisible = false;
		optionsContainer.hide();
	}
	
	function getValue() {
		return value;
	}
	
	function getInput() {
		return self.find('input');
	}
	
	function optionClicked() {
		var key = $(this).attr('key'),
		val = $(this).html();
		setValue(key, val);
		hideOptions();
	}
	
	
	
	function setValue(key, val) {
		value = key;
		getInput().val(value);
		self.find('.selected-value').html(val);
		_self.onChange(key);
	}
	

}