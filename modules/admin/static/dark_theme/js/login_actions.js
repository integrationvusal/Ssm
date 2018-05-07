var App = {
	init: function() {
		new CheckBoxController('#save-password-checkbox');
		if (errors.length) this.loginFailed();
		$('#email-field').click(this.fieldsClicked);
		$('#password-field').click(this.fieldsClicked);
		$('#email-field').keypress(this.submitForm);
		$('#password-field').keypress(this.submitForm);
	},
	loginFailed: function() {
		$('#email-field, #password-field').css({
			"background-color":"#F15C41",
			"color":"#444"
		});
	},
	fieldsClicked: function() {
		if ($(this).val() == $(this).attr('default-value')) {
			$(this).val('');
		}
	},
	submitForm: function(e){
		if (e.which == 13) {
			$('#login-form').submit();
		}
	}
}

$(function(){
	App.init();
});