var Controls = {};
Controls.selects = [];

var App = {
	horizontalSlider: null,
	
	init: function() {
		this.horizontalSlider = new HorizontalSliderController('#partners-slider-container-bottom');
		this.onResize();
		
		var categorySelect = new SelectController('#category-select');
		var catSelf = categorySelect.getSelf();
		catSelf.onChange = this.loadObjectsToCalc;
		categorySelect.setSelf(catSelf);
		
		var objectSelect = new SelectController('#object-select');
		var objSelf = objectSelect.getSelf();
		objSelf.onChange = this.getObjectDiscount;
		objectSelect.setSelf(objSelf);
		
		Controls.selects.push(categorySelect);
		Controls.selects.push(objectSelect);
		Controls.selects.push(new SelectController('#horoscope-sex-select-container'));
		Controls.selects.push(new SelectController('#horoscope-zodiac-select-container'));
		Controls.selects.push(new SelectController('#profile-sex-select'));
		Controls.selects.push(new SelectController('#phone-code'));
		Controls.selects.push(new SelectController('#call-to-bank-time-m-select'));
		new MSliderController('#banner-slider-middle');
		
		// events
		$('.dd-menu-pointer').click(this.showDDMenuIn2columns);
		$('.show-block').click(this.showBlock);
		
		// dropdown menu initialization
		$('.menu-item').hover(showMenu, hideMenu);
		$('.menu-item').hover(this.activateMenuItemPointer, this.deActivateMenuItemPointer);
		
		// reg button
		$('.registration').click(this.showRegistrationContainer);
		$('.reg-login-close, .popup-close').live('click', this.hideRegistrationContainer);
		$('.registration-submit-button').click(this.submitRegistration);
		$('.login-submit-button').click(this.submitLogin);
		$('.show-login-container').click(this.showLoginContainer);
		$('.show-reg-container').click(this.showRegContainer);
		
		// profile page
		$('#avatar-change-button').click(function(){
			$('#avatar-input').click();
		});
		this.initProfileTabs();
		
		// call to
		$('#call-to-bank-submit').click(this.callTo);
		
		// horoscope
		$('.horoscope-close').click(this.hideHoroscope);
		$('.show-horoscope').click(this.showHoroscope);
		$('#horoscope-submit').click(this.submitHoroscope);
		
		// add comment
		$('#add-comment').click(this.addComment);
		
		$('html').click(function(e){
			return;
			for (var i in Controls.selects) {
				Controls.selects[i].hideOptions();
			}
		});
	},
	onResize: function() {
		this.horizontalSlider.onResize();
	},
	showBlock: function() {
		var blockIdentifier = $(this).attr('block-identifier')
		$(blockIdentifier).fadeIn('fast');
	},
	showDDMenuIn2columns: function() {
		var menuContent = $(this).parent().siblings('.menu-content');
		if (menuContent.hasClass('hide')) menuContent.removeClass('hide');
		else menuContent.addClass('hide');
	},
	activateMenuItemPointer: function() {
		$(this).prev('.menu-pointer').addClass('menu-pointer-active');
	},
	deActivateMenuItemPointer: function() {
		$(this).prev('.menu-pointer').removeClass('menu-pointer-active');
	},
	
	// registration & login
	showRegistrationContainer: function() {
		$('.registration-block').fadeIn('fast');
	},
	hideRegistrationContainer: function() {
		$(this).parents('.block-ui').fadeOut('fast');
	},
	showLoginContainer: function() {
		$('.show-login-container').hide();
		$('.registration-container').fadeOut('fast',function(){
			$('.login-container').fadeIn();
			$('.show-reg-container').show();
		});
	},
	showRegContainer: function() {
		$('.show-reg-container').hide();
		$('.login-container').fadeOut('fast', function(){
			$('.registration-container').fadeIn();
			$('.show-login-container').show();
		});
	},
	submitRegistration: function() {
		var email = $('#reg-email').val(),
		csrfKey = $('#csrf-key').val();
		$.ajax({
			url: appData.regSubmitUrl,
			type: 'post',
			dataType: 'json',
			data: 'email=' + email + '&csrf_key=' + csrfKey,
			success: function(data) {
				$('#csrf-key').val(data.csrf_key);
				if (data.success) {
					$('#registration-content').html(Lang['reg_success']);
					//$('.registration').hide();
					//$('.registration-block').fadeOut('fast');
					$('.show-login-container').hide();
					$('.show-reg-container').hide();
				} else {
					$('#registration-errors').html(Lang[data.error]);
				}
			}
			
		});
	},
	submitLogin: function() {
		var email = $('#login-email-input').val(),
		password = $('#login-password-input').val(),
		csrfKey = $('#csrf-key').val();
		$.ajax({
			url: appData.loginSubmitUrl,
			type: 'post',
			dataType: 'json',
			data: 'email=' + email + '&password=' + password + '&csrf_key=' + csrfKey,
			success: function(data) {
				$('#csrf-key').val(data.csrfKey);
				if (data.success) {
					location.reload();
				} else {
					$('#login-errors').html(Lang[data.error]);
				}
			}
			
		});
	},
	// registration & login end
	
	// calculator
	loadObjectsToCalc: function(key) {
		$.ajax({
			type: 'post',
			url: appData.objectsListUrl + key,
			dataType: 'json',
			success: function(data) {
				var optionsContainer = $('#object-select-container').find('.select-options-container');
				optionsContainer.html('');
				var options = '';
				for (var i in data) {
					options += '<div class="select-option" key="' + data[i].r_id + '" >' + data[i].objTitle + '</div>';
				}
				optionsContainer.html(options);
			}
		});
	},
	getObjectDiscount: function(key) {
		$.ajax({
			type: 'post',
			url: appData.objectsGetDiscountUrl + key,
			dataType: 'json',
			success: function(data) {
				$('#object-discount-value').html(data);
			}
		});
	},
	
	/* horoscope */
	showHoroscope: function() {
		var sex = parseInt($(this).attr('sex'));
		var sexData = [];
		sexData.push('man');
		sexData.push('woman');
		$('.horoscope-inner-content-' + sexData[sex]).removeClass('hide');
		$('.horoscope-inner-content-' + sexData[boolToInt(!sex)]).addClass('hide');
		$('.horoscope-block').fadeIn('fast');
	},
	hideHoroscope: function() {
		$('.horoscope-block').fadeOut('fast');
	},
	submitHoroscope: function() {
		var horoscopeSex = parseInt($('#horoscope-sex-select-container').find('input').val()),
		horoscopeZodiac = parseInt($('#horoscope-zodiac-select-container').find('input').val()),
		url = appData.horoscopeSubmitUrl + horoscopeSex + '/' + horoscopeZodiac;
		$.ajax({
			type: 'post',
			url: url,
			dataType: 'json',
			success: function(data) {
				if (data.success) {
					$('#horoscope-title').html(data.title);
					$('#horoscope-title').html(data.title);
					var sexData = [];
					sexData.push('man');
					sexData.push('woman');
					$('.horoscope-inner-content-' + sexData[boolToInt(horoscopeSex - 1)]).html(data.horoscope);
					$('.horoscope-inner-content-' + sexData[horoscopeSex - 1]).removeClass('hide');
					$('.horoscope-inner-content-' + sexData[boolToInt(!(horoscopeSex - 1))]).addClass('hide');
				} else {
					$('#horoscope-error').html(Lang['horoscope_error']);
				}
			},
			failure: function() {
				
			}
		});
	},
	/* horoscope end */
	
	/* comments */
	addComment: function() {
		var commentText = $('#comment-text').val();
		var newsId = parseInt($(this).attr('news-id'));
		var csrfKey = $('#csrf-key').val();
		$.ajax({
			type: 'post',
			url: appData.addCommentUrl + newsId,
			data: 'commentText=' + commentText + '&csrf_key=' + csrfKey,
			dataType: 'json',
			success: function(data) {
				$('#csrf-key').val(data.csrfKey);
				if (data.success) {
					location.reload();
				} else {
					$('#comments-error-container').html(Lang['comment_add_error']);
				}
			}
		});
	},
	/* comments end */
	
	/* profile page */
	initProfileTabs: function() {
		$('.profile-left-tab').click(function(){
			var index = $(this).index();
			$('.profile-content').hide();
			$($('.profile-content').get(index)).show();
			$('.profile-left-tab').removeClass('profile-left-tab-active');
			$(this).addClass('profile-left-tab-active');
		});
	},
	/* profile page end */
	
	/* Call to */
	callTo: function() {
		var pNumber = $('#call-to-bank-number').val();
		var mTime = $('#call-to-bank-time-m-select').find('input').val();
		var sTime = $('#call-to-bank-time-s-select').find('input').val();
		var csrfKey = $('.csrf-key').val();
		var url = appData.callToBankUrl;
		if (pNumber.length > 5) {
			$.ajax({
				type: 'post',
				url: url,
				dataType: 'json',
				data: 'tSeconds=' + mTime + '&tMinutes=' + sTime + '&number=' + pNumber + '&csrf_key=' + csrfKey,
				success: function(result) {
					$('.csrf-key').val(result.csrfKey);
					if (result.success) {
						$('.call-to-success').show();
						$('.call-to-error').hide();
					} else {
						$('.call-to-success').hide();
						$('.call-to-error').show();
					}
				}
			});
		}
	}
	/* Call to end */
	
}

function search() {
	var searchText = $('#search-text').val();
	if (searchText.length > 2) {
		location.href = appData.url + '/' + appData.lang + '/search/' + searchText;
	}
}

function searchKeyPressed(e) {
	if (e.which == '13') search();
}

function showMap() {
	var objectId = parseInt($(this).attr('object-id'));
	var url = appData.getMapUrl + '/' + objectId;
	$.ajax({
		url: url,
		type: 'post',
		success: function(r) {
			$('body').append(tmpl($('#block-template').html(), {title : Lang['show_map'], content: r}));
		}
	});
}

function ratingIn() {
	var index = $(this).index();
	
	$(this).parent().find('.object-rating-icon').each(function(i){
		if (i <= index) {
			$(this).find('.inactive').hide();
			$(this).find('.active').show();
		} else {
			$(this).find('.active').hide();
			$(this).find('.inactive').show();
		}
	});
}

function ratingOut() {
	
}

function clearRatings() {
	$(this).find('.object-rating-icon').each(function(i){
		$(this).find('.active').hide();
		$(this).find('.inactive').show();
	});
}

function incRating() {
	var rate = parseInt($(this).index());
	var objectId = parseInt($(this).parent().attr('object-id'));
	
	var self = this;
	$.ajax({
		url: appData.addRatingUrl,
		type: 'post',
		dataType: 'json',
		data: 'csrfKey=' + $('.csrf-key').val() + '&rate=' + rate + '&objectId=' + objectId,
		success: function(r) {
			$('.csrf_key').val(r.csrfKey);
			$(self).parents('.object-links').find('.rating-value').html(r.rating);
		}
	});

	var rCont = $(this).parents('.object-links').find('.object-links-bottom-layer');
	if (rCont.css('left') == '0px') {
		rCont.animate({'left': '60%'}, 'slow');
	} else rCont.animate({'left': '0'}, 'slow');
}

function showHideRating() {
	var rCont = $(this).parents('.object-links').find('.object-links-bottom-layer');
	if (rCont.css('left') == '0px') {
		rCont.animate({'left': '60%'}, 'slow');
	} else rCont.animate({'left': '0'}, 'slow');
}

function init() {
	$('#search-button').click(search);
	$('#search-text').keypress(searchKeyPressed);
	$('.show-map').click(showMap);
	$('.object-rating-icon').hover(ratingIn, ratingOut);
	$('.object-rating-icon').click(incRating);
	$('.object-link-right a').click(showHideRating);
	//$('.object-rating-ribbon').mouseout(clearRatings);
}

$(function(){
	App.init();
	init();
});


