// Eagle Listing JS
if ( jQuery('#eagle-listing').length ) {
	var eagle_mobile_run_once = false;
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

		if( jQuery(this).attr('class') == 'collapsed'){
			jQuery(this).siblings('ul').css({'display':'block'});
			jQuery(this).attr('class','not-collapsed');
		} else {
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
				jQuery('#eagle-mobile-search-bar').css({
					'position':'fixed',
					'top':'0',
					'left':'50%',
					'width': centerWidth + 'px',
					'margin-left': '-' + leftMargin + 'px'
				})
			} else {
				jQuery('#eagle-mobile-search-bar').css({
					'position':'relative',
					'top':'0',
					'width': '100%',
					'left': '0',
					'margin-left': '0'
				})
			}
		});

		leftHeight = jQuery('#eagle-content-left').height();
		centerHeight = jQuery('#eagle-content-center').height();

		jQuery('#eagle-mobile-search-bar').click( function(){

			if (eagle_mobile_run_once == false){
				eagle_mobile_run_once = true;
				jQuery('.eagle-sidebar-content h4').each(function(){
					jQuery(this).attr('class','collapsed');
					jQuery(this).siblings('ul').css({'display':'none'});
				});
			}

			click_name = jQuery(this).attr('class');
			if( click_name == 'collapsed' ) {
				jQuery(this).attr('class','not-collapsed');
				jQuery(this).css({
					'background-color':'#000'
				});
				jQuery(this).siblings('#eagle-content-left').css({
					'position':'absolute',
					'z-index':'2',
					'background-color':'#E1E1E1',
					'top': '1%',
					'height': leftHeight + 'px',
					'border-bottom': '3px solid #000',
					'margin': '0 auto'
				})
				jQuery(this).children('a').css({'color':'#CA204D'});
				jQuery(this).siblings('#eagle-content-left').slideDown('slow');
				if( leftHeight > centerHeight ){
					jQuery('#eagle-content-center').height(leftHeight);
				}
			} else {
				jQuery(this).attr('class','collapsed');
				jQuery(this).css({
					'background-color':'#CA204D'
				});
				jQuery(this).children('a').css({'color':'#FFF'});
				jQuery(this).siblings('#eagle-content-left').slideUp('slow');
				if( leftHeight == jQuery('#eagle-content-center').height() ){
					jQuery('#eagle-content-center').height(centerHeight);
				}
			}
		});
	});

}

// Eagle Detail JS
if ( jQuery('#eagle-detail').length ) {

	jQuery(document).ready(function() { // Document Ready
		// Eagle Slideshow/Image Cycle
		jQuery('#eagle-image-wrapper #eagle-images')
		.cycle({
			slideExpr: 'img',
			fx: 'fade',
			pager: '#eagle-image-wrapper #eagle-nav-images',
			pagerAnchorBuilder: function(idx, slide) {
				return '<a href="#"><img src="' + slide.src + '" width="70" height="50" /></a>';
	   		},
			fit: 1
		});
		jQuery('#eagle-images a')
		.lightbox({
			imageClickClose: false,
			loopImages: true,
			fitToScreen: true,
			scaleImages: true,
			xScale: 1.0,
			yScale: 1.0,
			displayDownloadLink: true
		});

		// Eagle adjust Nav height to match img height
		function check_img_height() {
			img_height = jQuery('#eagle-images img').height();
			nav_height = jQuery('#eagle-nav-wrapper').height();

			if (img_height != nav_height){
				jQuery('#eagle-nav-wrapper').css({'height':img_height});
				jQuery('#eagle-images').css({'height':img_height});
			}

			setTimeout(check_img_height, 500);
		}
		check_img_height();

	}); // Document Ready *end

	// Show hidden form on click
	jQuery('.eagle-show-form').click(function() {
		jQuery('#vehicle-inquiry-subpre-hidden').val( jQuery(this).attr('name') );
		jQuery('.eagle-hidden-form').dialog({
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

}

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
