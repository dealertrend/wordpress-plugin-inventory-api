//	Dolphin Listing JS
if ( jQuery('#dolphin-listing').length ) {

	// Dolphin Search
	jQuery('#dolphin-search-submit').click( function() {
		dolphin_search_form( 'false' );
	});

	// Dolphin Advanced Search Show
	jQuery('#dolphin-advance-show').click(function() {
	    name = jQuery(this).attr('name');
	    if ( name == 'hidden' ) {
	        jQuery('#dolphin-search-advance').slideDown();
	        jQuery(this).attr('name', 'active').text('<<Hide');
	    } else {
	        jQuery('#dolphin-search-advance').slideUp();
	        jQuery(this).attr('name', 'hidden').text('Advance>>');
	    }
	});
}

// Dolphin Detail JS
if ( jQuery('#dolphin-detail').length ) {

	// Dolphin Show Images
	jQuery('#dolphin-nav-button').click(function() {

		img_count = jQuery('#dolphin-nav-images > a').length;
	    name = jQuery(this).attr('name');
	    if ( name == 'hidden' ) {
	        jQuery('#dolphin-nav-images').css({'height':'auto'})
	        jQuery(this).attr('name', 'active').text('Hide Images');
		    if( img_count > 15 ) { move_specs( 0 ); }
	    } else {
	        jQuery('#dolphin-nav-images').css({'height':'74px'})
	        jQuery(this).attr('name', 'hidden').text('Show All');
		    if( img_count > 15 ) { move_specs( 1 ); }
	    }
	});

	jQuery(document).ready(function() { // Document Ready
		// Dolphin Slideshow/Image Cycle
    	jQuery('#dolphin-slideshow #dolphin-images')
    	.cycle({
        	slideExpr: 'img',
        	fx: 'fade',
        	pager: '#dolphin-slideshow #dolphin-nav-images',
	        pagerAnchorBuilder: function(idx, slide) {
    	        return '<a href="#"><img src="' + slide.src + '" width="70" height="50" /></a>';
       		},
			fit: 1
    	});
		jQuery('#dolphin-images a')
		.lightbox({
		    imageClickClose: false,
		    loopImages: true,
		    fitToScreen: true,
		    scaleImages: true,
		    xScale: 1.0,
		    yScale: 1.0,
		    displayDownloadLink: true
		});

		// Dolphin Fill in Test Drive Fields
		d = new Date();
		strDate = (d.getMonth()+1) + '/' + d.getDate() + '/' +  d.getFullYear();
		cHours = d.getHours();
		if ( cHours > 11 ) {
			cHours = cHours - 12;
			tID = 'PM';
		} else {
			tID = 'AM';
		}

		if ( cHours == 0 ){
			cHours = 12;
		}
		strTime = cHours + ':' + d.getMinutes() + ' ' + tID;

		jQuery('#vehicle-testdrive-date').val( strDate );
		jQuery('#vehicle-testdrive-time').val( strTime );


	}); // Document Ready *end

	// Dolphin Form Control
	jQuery('.dolphin-form-headers').click(function() {
		form_display = jQuery(this).siblings('.dolphin-form').attr('name');

		if( jQuery(this).attr('name') != 'form-friend' ) {
			jQuery('.dolphin-form-headers').each(function() {
				jQuery(this).siblings('.dolphin-form').attr('name', 'hidden');
				jQuery(this).attr('class','dolphin-form-headers');
				jQuery(this).siblings('.dolphin-form').slideUp();
			});

			if ( form_display.match( /hidden/i ) != null ) {
				jQuery(this).siblings('.dolphin-form').attr('name', 'active');
				jQuery(this).attr('class', 'dolphin-form-headers active-form');
				jQuery(this).siblings('.dolphin-form').slideDown();
			}
		}
	});

	// Dolphin Tab Control
	jQuery('.dolphin-detail-tab').click(function() {
		tab_count = jQuery('.dolphin-detail-tab').length;
		tab_name = jQuery(this).attr('name');

		if ( jQuery(this).attr('class').match(/active/i) != null ) {
		    // Do Nothing;
		} else {
		    if ( tab_count < 2 ) {
		        // Do Nothing;
		    } else {
		        switch( tab_name ) {

		            case 'equip':
		                jQuery('#dolphin-detail-description').slideUp();
		                jQuery('.dolphin-detail-tab').attr('class', 'dolphin-detail-tab');
		                jQuery(this).attr('class', 'dolphin-detail-tab active-tab');
		                jQuery('#dolphin-detail-features').slideDown();
		                break;

		            case 'desc':
		                jQuery('#dolphin-detail-features').slideUp();
		                jQuery('.dolphin-detail-tab').attr('class', 'dolphin-detail-tab');
		                jQuery(this).attr('class', 'dolphin-detail-tab active-tab');
		                jQuery('#dolphin-detail-description').slideDown();
		                break;
		        }
		    }
		}
	});

}

// Dolphin General
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
});

function loadIframe( url ) {
		var iframe = jQuery( 'iframe' );
		if ( iframe.length ) {
				iframe.attr( 'src' , url );
				return false;
		}
		return true;
}

function dolphin_filter_select( type ) {

	switch( type ) {
		case 'make':
			jQuery('#dolphin-models select').val('');
			jQuery('#dolphin-trims select').val('');
			break;

		case 'model':
			jQuery('#dolphin-trims select').val('');
			break;
	}

	add_search = jQuery('#dolphin-apply-search input:checked').val();

	if ( add_search ) {
		jQuery('#dolphin-search-submit').click();
	} else {
		filter_url = build_url( 'filter' );
		window.location = filter_url;
	}
}

function dolphin_search_form() {

	form = jQuery('#dolphin-search');
	form_url = build_url( 'search' );

	form.submit(function(e) {
		if ( form_url.length > 1) {
			jQuery(form).attr('action', form_url);
			return true;
		} else {
			return false;
		}
	});

}

function dolphin_detail_forms( url, form_id ) {

	form_error = '<span style="border-bottom: 1px solid #000; margin-bottom: 1%;">ERROR - </span>';
	form_errors = '';
	form = '';

	if ( url ) {
		switch ( form_id ) {
			case '0': //Request Information
				form = jQuery('#vehicle-inquiry');
				required_values = get_dolphin_detail_form_required_values( form_id );
				form_errors = dolphin_detail_form_error_check( required_values );
				jQuery('#vehicle-inquiry-name').val(required_values['First Name'] + ' ' + required_values['Last Name']);
				break;
			case '1': //Test Drive
				form = jQuery('#vehicle-testdrive');
				required_values = get_dolphin_detail_form_required_values( form_id );
				form_errors = dolphin_detail_form_error_check( required_values );
				jQuery('#vehicle-testdrive-name').val(required_values['First Name'] + ' ' + required_values['Last Name']);
				jQuery('#vehicle-testdrive-timetocall').val( jQuery('#vehicle-testdrive-date').val() + ' - ' + jQuery('#vehicle-testdrive-time').val() );
				break;
		}
	}

	if ( form ) {
		if ( form_errors ) {
			form_error += form_errors;
			form.find('.form-error').css({'display':'block'}).html( form_error );
		} else {
			form.find('.form-error').css({'display':'none'}).html('');
			form.submit(function(e) {
				if ( url.length > 1) {
					jQuery(form).attr('action', url);
					return true;
				} else {
					return false;
				}
			});
		}
	}
}

function get_dolphin_detail_form_required_values( id ) {
	obj = {};

	switch ( id ) {
		case '0':
			obj['First Name'] = jQuery('#vehicle-inquiry-f-name').val();
			obj['Last Name'] = jQuery('#vehicle-inquiry-l-name').val();
			obj['Email'] = jQuery('#vehicle-inquiry-email').val();
			obj['Privacy'] = jQuery('#vehicle-inquiry-privacy:checked').val();
			break;
		case '1':
			obj['First Name'] = jQuery('#vehicle-testdrive-f-name').val();
			obj['Last Name'] = jQuery('#vehicle-testdrive-l-name').val();
			obj['Email'] = jQuery('#vehicle-testdrive-email').val();
			obj['Privacy'] = jQuery('#vehicle-testdrive-privacy:checked').val();
			break;
	}

	return obj;
}

function dolphin_detail_form_error_check( required ) {

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

function get_search_params() {
	obj = {};

	if ( jQuery('#dolphin-search-top select').val() == 'Certified' ) {
		obj['saleclass'] = 'Used';
		obj['certified'] = 'yes';
	} else {
		obj['saleclass'] = jQuery('#dolphin-saleclass').val();
		obj['certified'] = '';
	}

	obj['search'] = jQuery('#dolphin-search-box').val();
	obj['price_from'] = jQuery('#dolphin-price-from').val();
	obj['price_to'] = jQuery('#dolphin-price-to').val();
	obj['year_from'] = jQuery('#dolphin-year-from').val();
	obj['year_to'] = jQuery('#dolphin-year-to').val();
	obj['mileage_from'] = jQuery('#dolphin-mileage-from').val();
	obj['mileage_to'] = jQuery('#dolphin-mileage-to').val();
	obj['vehicleclass'] = jQuery('#dolphin-vehicleclass').val();

	return obj;
}

function get_filter_params() {
	obj = {};

	obj['make'] = jQuery('#dolphin-makes select').val();
	obj['model'] = jQuery('#dolphin-models select').val();
	obj['trim'] = jQuery('#dolphin-trims select').val();

	return obj;
}

function get_hidden_params() {
	obj = {};

	obj['rewrite'] = jQuery('#hidden-rewrite').val();
	obj['saleclass'] = jQuery('#hidden-saleclass').val();

	return obj;
}

function build_url( type ) {

	s_params = get_search_params();
	f_params = get_filter_params();
	h_params = get_hidden_params();

	add_search = jQuery('#dolphin-apply-search input:checked').val();

	url_text = '';
	url_query = '';
	f_text = '';
	f_query = '';
	s_query = '';

	jQuery.each(s_params, function(key, value) { // Search Params
		if ( key != 'saleclass' ) {
			if ( value ) {
				if ( s_query.length > 1 ) {
					s_query += '&'+ key + '=' + value;
				} else {
					s_query = key + '=' + value;
				}
			}
		}
	});

	if ( h_params['rewrite'] ) {
		url_text = '/inventory/';
		url_query = '?';
		if ( f_params['make'] ) { // Filter Params (clean)
			f_text = f_params['make'] + '/';
			if ( f_params['model'] ) {
				f_text += f_params['model'] + '/';
				if  ( f_params['trim'] ) {
					f_query = 'trim=' + f_params['trim'];
				}
			}
		}

		if ( add_search ) { // Saleclass (clean)
			url_text += s_params['saleclass'] + '/';
		} else {
			if ( type == 'filter' ) {
				url_text += h_params['saleclass'] + '/';
			} else {
				url_text += s_params['saleclass'] + '/';
			}
		}

		if ( add_search ) { // String Query
			if ( f_query && s_query ) {
				url_query += f_query + '&' + s_query;
			} else if ( f_query ) {
				url_query += f_query;
			} else {
				url_query += s_query;
			}
		} else {
			if ( type == 'filter' ) {
				url_query += f_query;
			} else {
				url_query += s_query;
			}
		}
		if ( url_query.length <= 2 ) {
			url_query = '';
		}

	} else {
		url_text = '?taxonomy=inventory';
		if ( f_params['make'] ) { // Filter Params (not clean)
			f_query = '&make=' + f_params['make'];
			if ( f_params['model'] ) {
				f_query += '&model=' + f_params['model'];
				if  ( f_params['trim'] ) {
					f_query += '&trim=' + f_params['trim'];
				}
			}
		}

		if ( add_search ) { // Saleclass (not clean)
			url_text += '&saleclass=' + s_params['salaclass'];
		} else {
			if ( type == 'filter' ) {
				url_text += '&saleclass=' + h_params['salaclass'];
			} else {
				url_text += '&saleclass=' + s_params['salaclass'];
			}
		}

		if ( add_search ) { // String Query
			if ( f_query && s_query ) {
				url_query += '&' + f_query + '&' + s_query;
			} else if ( f_query ) {
				url_query += '&' + f_query;
			} else {
				url_query += '&' + s_query;
			}
		} else {
			if ( type == 'filter' ) {
				url_query += '&' + f_query;
			} else {
				url_query += '&' + s_query;
			}
		}
		if ( url_query.length <= 2 ) {
			url_query = '';
		}
	}

	if ( type == 'filter' ) { // Filter
		url_text += f_text + url_query;
	} else { // Search
		if ( add_search ) {
			url_text += f_text + url_query;
		} else {
			url_text += url_query;
		}
	}

	return url_text;

}

function move_specs ( reset ) {
    if ( reset == 0 ) {
        jQuery('#dolphin-detail-specs').css({'clear':'none','float':'left','margin':'0 0 1% 4.5%','width':'60%'});
        jQuery('.dolphin-detail-tab').css({'width':'32%'});
    } else {
        jQuery('#dolphin-detail-specs').css({'clear':'both','float':'none','margin':'0 auto 1%','width':'94%'});
        jQuery('.dolphin-detail-tab').css({'width':'20%'});
    }
}

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(emailAddress);
}
