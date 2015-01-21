jQuery(document).ready(function(){
	jQuery('#dealertrend-inventory-api').parent().addClass('inventory-parentClass');
// Eagle Listing JS
if ( jQuery('#eagle-listing').length ) {

	// Show hidden form on click
	jQuery('.eagle-show-form').click(function() {
		jQuery('#vehicle-inquiry-year-hidden').val( jQuery(this).siblings('.eagle-hidden-form-values').children('.eagle-form-year').text() );
		jQuery('#vehicle-inquiry-make-hidden').val( jQuery(this).siblings('.eagle-hidden-form-values').children('.eagle-form-make').text() );
		jQuery('#vehicle-inquiry-model-hidden').val( jQuery(this).siblings('.eagle-hidden-form-values').children('.eagle-form-model').text() );
		jQuery('#vehicle-inquiry-trim-hidden').val( jQuery(this).siblings('.eagle-hidden-form-values').children('.eagle-form-trim').text() );
		jQuery('#vehicle-inquiry-stock-hidden').val( jQuery(this).siblings('.eagle-hidden-form-values').children('.eagle-form-stock').text() );
		jQuery('#vehicle-inquiry-vin-hidden').val( jQuery(this).siblings('.eagle-hidden-form-values').children('.eagle-form-vin').text() );
		jQuery('#vehicle-inquiry-vehicle-hidden').val( jQuery(this).siblings('.eagle-hidden-form-values').children('.eagle-form-vehicle').text() );
		jQuery('#vehicle-inquiry-price-hidden').val( jQuery(this).siblings('.eagle-hidden-form-values').children('.eagle-form-price').text() );
		jQuery('#vehicle-inquiry-inventory-hidden').val( jQuery(this).siblings('.eagle-hidden-form-values').children('.eagle-form-inventory').text() );
		jQuery('#vehicle-inquiry-saleclass-hidden').val( jQuery(this).siblings('.eagle-hidden-form-values').children('.eagle-form-saleclass').text() );
		jQuery('#vehicle-inquiry-subpost-hidden').val( jQuery(this).siblings('.eagle-hidden-form-values').children('.eagle-form-subject-post').text() );
		jQuery('.eagle-form-headers').text( jQuery(this).siblings('.eagle-hidden-form-values').children('.eagle-form-vehicle').text() + ' ' + jQuery(this).siblings('.eagle-hidden-form-values').children('.eagle-form-trim').text() );
		jQuery('#vehicle-inquiry-subpre-hidden').val( jQuery(this).attr('name') );

		form_url = jQuery(this).siblings('.eagle-hidden-form-values').children('.eagle-form-url').text();
		jQuery('.eagle-form-button .submit').attr("onclick", "return eagle_process_forms(" + form_url + ",'3')");
		jQuery('.eagle-hidden-form').dialog({
			dialogClass: 'eagle-dialog-form',
			autoOpen: true,
			height: 400,
			width: 300,
			modal: true,
			resizable: false,
			draggable: false,
			title: jQuery(this).attr('name'),
			close: function() {
				jQuery('.form-error').css({'display':'none'}).html('');
			}
		});

		return false;
	});

	// Sidebar click
	jQuery('.eagle-sidebar-content h4').click( function() {

		s_name = jQuery(this).attr('name');

		if( jQuery(this).attr('class') == 'collapsed'){
			if(typeof Storage !=="undefined"){
				sessionStorage.removeItem('eagle' + s_name);
			}
			jQuery(this).siblings('ul').css({'display':'block'});
			jQuery(this).attr('class','');
		} else {
			if(typeof Storage !=="undefined"){
				sessionStorage.setItem('eagle' + s_name, '1');
			}
			jQuery(this).siblings('ul').css({'display':'none'});
			jQuery(this).attr('class','collapsed');
		}

	});

	jQuery(function(){
		stickyTop = jQuery('#eagle-content-center').offset().top;

		jQuery(window).scroll(function(){
			windowTop = jQuery(window).scrollTop();
			centerWidth = jQuery('#eagle-content-center').width();
			leftMargin = centerWidth / 2;

			if( stickyTop < windowTop ){
				jQuery('#eagle-mobile-search-wrap').css({
					'position':'fixed',
					'top':'0',
					'width': '40%',
					'left':'6%'
				})
			} else {
				jQuery('#eagle-mobile-search-wrap').css({
					'position':'relative',
					'top':'0',
					'width': '60%',
					'left': '0',
					'margin': '0 auto 0'
				})
			}
		});

		leftHeight = jQuery('#eagle-content-left').height();
		centerHeight = jQuery('#eagle-content-center').height();

		// Check sessionStorage
		if(typeof Storage !=="undefined"){
			if (sessionStorage.mobileview){
				jQuery('#eagle-mobile-search-wrap').attr('class','active');
				jQuery('#eagle-content-left').attr('class','mobileview');
				jQuery('#eagle-content-right').attr('class','mobileview');
				jQuery('#eagle-content-left').css({
					'height': leftHeight + 'px',
					'display': 'block'
				})
				if( leftHeight > centerHeight ){
					jQuery('#eagle-content-center').height(leftHeight);
				}
				if(sessionStorage.eaglecondition){
					jQuery('.eagle-sidebar-content.content-new-used > h4').attr('class','collapsed');
					jQuery('.eagle-sidebar-content.content-new-used > h4').siblings('ul').css({'display':'none'});
				}
				if(sessionStorage.eaglestyles){
					jQuery('.eagle-sidebar-content.content-bodystyle > h4').attr('class','collapsed');
					jQuery('.eagle-sidebar-content.content-bodystyle > h4').siblings('ul').css({'display':'none'});
				}
				if(sessionStorage.eaglevehicles){
					jQuery('.eagle-sidebar-content.content-make-model-trim > h4').attr('class','collapsed');
					jQuery('.eagle-sidebar-content.content-make-model-trim > h4').siblings('ul').css({'display':'none'});
				}
				if(sessionStorage.eagleprice){
					jQuery('.eagle-sidebar-content.content-price-range > h4').attr('class','collapsed');
					jQuery('.eagle-sidebar-content.content-price-range > h4').siblings('ul').css({'display':'none'});
				}
				jQuery('html, body').animate({
					scrollTop: jQuery('#eagle-content-center').offset().top
				}, 600);
				jQuery('#eagle-mobile-search-wrap #eagle-mobile-search-text').text('Close');
			}
		}

		jQuery('#eagle-mobile-search-wrap').click( function(){

			click_name = jQuery(this).attr('class');
			if( click_name == 'inactive' ) {

				if(typeof Storage !=="undefined"){
					sessionStorage.mobileview = '0';
				}

				jQuery('html, body').animate({
					scrollTop: jQuery('#eagle-content-center').offset().top
				}, 600);
				jQuery(this).removeClass('inactive');
				jQuery(this).addClass('active');
				jQuery(this).siblings('#eagle-content-left').addClass('mobileview');
				jQuery(this).siblings('#eagle-content-right').addClass('mobileview');
				jQuery(this).siblings('#eagle-content-left').css({
					'height': leftHeight + 'px',
				})
				jQuery(this).siblings('#eagle-content-left').animate({width: 'toggle'});
				if( leftHeight > centerHeight ){
					jQuery('#eagle-content-center').height(leftHeight);
				}
				jQuery(this).children('#eagle-mobile-search-text').text('Close');
			} else {

				if(typeof Storage !=="undefined"){
					sessionStorage.removeItem('mobileview');
				}
				jQuery(this).removeClass('active');
				jQuery(this).addClass('inactive');
				jQuery(this).siblings('#eagle-content-left').animate({width: 'toggle'});
				jQuery(this).siblings('#eagle-content-left').removeClass('mobileview');
				jQuery(this).siblings('#eagle-content-right').removeClass('mobileview');

				if( leftHeight == jQuery('#eagle-content-center').height() ){
					jQuery('#eagle-content-center').height(centerHeight);
				}
				jQuery(this).children('#eagle-mobile-search-text').text('Search');
			}
		});
	});
}

// Eagle Detail JS
if ( jQuery('#eagle-detail').length ) {
	
	jQuery('#loan-calculator-button').click(function() {
		if( jQuery(this).siblings('#loan-calculator-data').hasClass('active') ){
			jQuery('#loan-calculator-data').removeClass('active');
		} else {
			jQuery('#loan-calculator-data').addClass('active');
		}
	});
	
	// Form Buttons
	jQuery('.form-button').click(function(e) {
		form_name = jQuery(e.target).attr('name');
		jQuery('#'+form_name).dialog({
			autoOpen: true,
			dialogClass: "form-wrap",
			modal: true,
			resizable: false,
			width: 320,
			height: 450
		})
	});

	jQuery(document).ready(function() { // Document Ready
		// Detail Slideshow
		jQuery(document).ready(function() {
			jQuery('#vehicle-images')
			.cycle({
				slides: '> a',
				fx: 'fade',
				pager: '#vehicle-thumbnails',
				pagerTemplate: '<a href="#"><img src="{{href}}" width="70" height="50" /></a>'
			});

			jQuery('#vehicle-images > a')
			.lightbox({
				imageClickClose: false,
				loopImages: true,
				fitToScreen: true,
				scaleImages: true,
				xScale: 1.0,
				yScale: 1.0,
				displayDownloadLink: true
			});
		});

		// Eagle adjust Nav height to match img height
		function check_img_height() {
			img_height = jQuery('#vehicle-images img').height();
			nav_height = jQuery('#vehicle-thumbnails').height();

			if (img_height != nav_height){
				jQuery('#vehicle-thumbnails').css({'height':img_height});
				jQuery('#vehicle-images').css({'height':img_height});
			}

			setTimeout(check_img_height, 500);
		}
		check_img_height();

		// Tab Control
		jQuery('.tabs-button').click(function() {
			tab_name = jQuery(this).attr('name');
			jQuery(this).siblings('.active').removeClass('active');
			jQuery(this).parent().parent().find('.tabs-content.active').removeClass('active');
			jQuery(this).addClass('active');
			jQuery('.tabs-content-'+tab_name).addClass('active');
		});
	
	}); // Document Ready *end

	// Show hidden form on click
	jQuery('.eagle-show-form').click(function() {
		jQuery('#vehicle-inquiry-subpre-hidden').val( jQuery(this).attr('name') );
		jQuery('.eagle-hidden-form').dialog({
			dialogClass: 'eagle-dialog-form',
			autoOpen: true,
			height: 400,
			width: 300,
			modal: true,
			resizable: false,
			draggable: false,
			title: jQuery(this).attr('name')
		});

		return false;
	});
	
	// Video Dialog
	var video_title = jQuery('#title-year').text() + ' ' + jQuery('#title-make').text() + ' ' + jQuery('#title-model').text();
	jQuery('#video-overlay-wrapper-dm').click(function(e) {
		jQuery('#dm-video-wrapper').dialog({
			autoOpen: true,
			dialogClass: "dialog-video-wrapper",
			modal: true,
			resizable: false,
			width: 640,
			height: 520,
			title: video_title
		})
	});

	jQuery('#video-overlay-wrapper').click(function(e) {
		var video_width;
		if( !video_width ){
			video_width = get_video_width();
			video_width = video_width + 35;//Added for dialog padding
		}
		jQuery('#wp-video-shortcode-wrapper').dialog({
			autoOpen: true,
			dialogClass: "dialog-video-wrapper",
			modal: true,
			resizable: false,
			width: video_width,
			title: video_title,
			beforeClose: function( event, ui ){
				jQuery('.mejs-pause > button').click();
			},
			open: function( event, ui ){
				jQuery('#wp-video-shortcode-wrapper .wp-video-shortcode').css({'height':'360px','width':'640px'});
				jQuery('#wp-video-shortcode-wrapper .mejs-overlay-play').css({'height':'329px','width':'640px'});
			}
		})
		jQuery('.mejs-play > button').click();
	});

	function get_video_width(){
		results = jQuery('#wp-video-shortcode-wrapper > div').width();
		//console.log(results);
		return results;
	}
}

});

// Eagle General
jQuery(document).ready(function() {

	// AIS iFrame
	var frame = jQuery('<div class="aisframe"><iframe width="785" src="about:blank" height="415" frameborder="0"></iframe></div>');

	frame.appendTo( 'body' );

	jQuery( '.aisframe' ).dialog({
		autoOpen: false,
		modal: true,
		resizable: false,
		width: 820,
		height: 485,
		open: function( event , ui ) { jQuery( '.ui-widget-overlay').click( function() { jQuery( '.aisframe' ).dialog( 'close' ); } ); },
		title: 'Incentives and Rebates'
	});

	jQuery( '.view-available-rebates > a' ).click(
		function() {
			jQuery( '.aisframe' ).dialog( 'open' );
			return false;
		}
	);

	// Clear Default Form Values
	jQuery( '.eagle-form-full input' ).focus(
		function() {
			if ( jQuery(this).attr('alt') == 'empty' ) {
				jQuery(this).attr('alt','');
				jQuery(this).attr('value','');
				jQuery(this).css({'color':'#000'});
			}
		}
	)
	jQuery( '.eagle-form-full textarea' ).focus(
		function() {
			if ( jQuery(this).attr('alt') == 'empty' ) {
				jQuery(this).attr('alt','');
				jQuery(this).attr('value','');
				jQuery(this).css({'color':'#000'});
			}
		}
	)
});

function loadIframe( url ) {
		var iframe = jQuery( 'iframe' );
		if ( iframe.length ) {
				iframe.attr( 'src' , url );
				return false;
		}
		return true;
}

function eagle_process_forms( url, form_id ) {

	form_error = '<span style="border-bottom: 1px solid #000; margin-bottom: 1%;"> - ERROR - </span>';
	form_errors = '';
	form = '';

	if ( url ) {
		switch ( form_id ) {
			case '0': //Request Information
				form = jQuery('#vehicle-inquiry');
				required_values = get_eagle_form_required_values( form_id );
				form_errors = eagle_form_error_check( required_values );
				checkbox_string = eagle_build_checkbox_string();
				if ( checkbox_string != '' ) {
					jQuery('#vehicle-inquiry-comments').val(checkbox_string + "\n" + jQuery('#vehicle-inquiry-form-comments').val());
				} else {
					jQuery('#vehicle-inquiry-comments').val(jQuery('#vehicle-inquiry-form-comments').val());
				}
				jQuery('#vehicle-inquiry-name').val(required_values['First Name'] + ' ' + required_values['Last Name']);
				break;
			case '1': //Test Drive
				form = jQuery('#vehicle-testdrive');
				required_values = get_dolphin_detail_form_required_values( form_id );
				form_errors = dolphin_detail_form_error_check( required_values );
				jQuery('#vehicle-testdrive-name').val(required_values['First Name'] + ' ' + required_values['Last Name']);
				jQuery('#vehicle-testdrive-timetocall').val( jQuery('#vehicle-testdrive-date').val() + ' - ' + jQuery('#vehicle-testdrive-time').val() );
				break;
			case '2': //Tell Friend
				form = jQuery('#form-friend');
				required_values = get_dolphin_detail_form_required_values( form_id );
				form_errors = dolphin_detail_form_error_check( required_values );
				jQuery('#form-friend-from-name').val(required_values['First Name'] + ' ' + jQuery('#friend-from-l-name').val() );
				jQuery('#form-friend-name').val(required_values['Friend First Name'] + ' ' + jQuery('#friend-to-l-name').val() );
				break;
			case '3': //Request Hidden Form Information
				form = jQuery('#vehicle-inquiry-hidden');
				required_values = get_eagle_form_required_values( form_id );
				form_errors = eagle_form_error_check( required_values );
				jQuery('#vehicle-inquiry-subject-hidden').val( jQuery('#vehicle-inquiry-subpre-hidden').val() + ' - ' + jQuery('#vehicle-inquiry-subpost-hidden').val() );
				jQuery('#vehicle-inquiry-name-hidden').val(required_values['First Name'] + ' ' + required_values['Last Name']);
				break;
		}
	}

	if ( form ) {
		if ( form_errors ) {
			form_error += form_errors;
			form.find('.form-error').css({'display':'block'}).html( form_error );
			return false;
		} else {
			form.find('.form-error').css({'display':'none'}).html('');
			if( form_id == 3 ){
				jQuery('.eagle-hidden-form').dialog( "close" );
			}

			if ( url.length > 1) {
				jQuery(form).attr('action', url);
				return true;
			} else {
				return false;
			}

		}
	}
}

function get_eagle_form_required_values( id ) {
	obj = {};

	switch ( id ) {
		case '0':
			if ( jQuery('#vehicle-inquiry-f-name').attr('alt') == 'empty' ){
				obj['First Name'] = '';
			} else {
				obj['First Name'] = jQuery('#vehicle-inquiry-f-name').val();
			}
			if ( jQuery('#vehicle-inquiry-l-name').attr('alt') == 'empty' ){
				obj['Last Name'] = '';
			} else {
				obj['Last Name'] = jQuery('#vehicle-inquiry-l-name').val();
			}
			if ( jQuery('#vehicle-inquiry-email').attr('alt') == 'empty' ){
				obj['Email'] = '';
			} else {
				obj['Email'] = jQuery('#vehicle-inquiry-email').val();
			}
			obj['Privacy'] = jQuery('#vehicle-inquiry-privacy:checked').val();
			break;
		case '1':
			obj['First Name'] = jQuery('#vehicle-testdrive-f-name').val();
			obj['Last Name'] = jQuery('#vehicle-testdrive-l-name').val();
			obj['Email'] = jQuery('#vehicle-testdrive-email').val();
			obj['Privacy'] = jQuery('#vehicle-testdrive-privacy:checked').val();
			break;
		case '2':
			obj['First Name'] = jQuery('#friend-from-f-name').val();
			obj['Friend First Name'] = jQuery('#friend-to-f-name').val();
			obj['Email'] = jQuery('#friend-from-email').val();
			obj['Email2'] = jQuery('#friend-to-email').val();
			obj['Privacy'] = jQuery('#friend-privacy:checked').val();
			break;
		case '3':
			if ( jQuery('#vehicle-inquiry-f-name-hidden').attr('alt') == 'empty' ){
				obj['First Name'] = '';
			} else {
				obj['First Name'] = jQuery('#vehicle-inquiry-f-name-hidden').val();
			}
			if ( jQuery('#vehicle-inquiry-l-name-hidden').attr('alt') == 'empty' ){
				obj['Last Name'] = '';
			} else {
				obj['Last Name'] = jQuery('#vehicle-inquiry-l-name-hidden').val();
			}
			if ( jQuery('#vehicle-inquiry-email-hidden').attr('alt') == 'empty' ){
				obj['Email'] = '';
			} else {
				obj['Email'] = jQuery('#vehicle-inquiry-email-hidden').val();
			}
			obj['Privacy'] = jQuery('#vehicle-inquiry-privacy-hidden:checked').val();
			break;
	}

	return obj;
}

function eagle_form_error_check( required ) {

	error_text = '';

	jQuery.each(required, function(key, value) {
		if ( !value ) {
			error_text += '<span>Missing ' + key + ' </span>';
		} else if ( key == 'Email' || key == 'Email2') {
			if ( !isValidEmailAddress( value ) ) {
				error_text += '<span>Invalid Email Address</span>';
			}
		}
	});

	return error_text;

}

function eagle_build_checkbox_string() {

	checkbox_text = '';
	jQuery('.eagle-form-top-checkboxes .eagle-checkbox:checked').each(
		function() {
			checkbox_temp = jQuery(this).attr('name');
			checkbox_temp = checkbox_temp.replace('eagle-checkbox-','').replace('-', ' ');

			checkbox_text += checkbox_temp + "\n";
		}
	);
	if ( checkbox_text != '' ){
		checkbox_text = 'Selected Checkboxes: ' + "\n" + "\n" + checkbox_text;
	}

	return checkbox_text;
}

function video_popup(url , title) {
	if (! window.focus) return true;
	var href;
	if (typeof(url) == 'string') {
		href=url;
	} else {
		href=url.href;
		window.open(href, title, 'width=640,height=480,scrollbars=no');
		return false;
	}
}

function isValidEmailAddress(emailAddress) {
	var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
	return pattern.test(emailAddress);
}
