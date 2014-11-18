
	if( jQuery('#cobra-listing').length){

		// Cobra Search
		jQuery('#cobra-search-submit').click( function() {
			//cobra_search_form( 'false' );
		});
		
		jQuery('input.list-search-value').keypress(function(e) {
	    	if(e.which == 13) {
				get_list_input_values(e);
	    	}
		});
		
		// Cobra Advanced Search Show
		jQuery('#cobra-advance-show').click(function() {
			name = jQuery(this).attr('name');
			if ( name == 'hidden' ) {
			    jQuery('#cobra-search-advance').slideDown();
			    jQuery(this).attr('name', 'active').text('Hide Advanced');
			} else {
			    jQuery('#cobra-search-advance').slideUp();
			    jQuery(this).attr('name', 'hidden').text('Advanced Search');
			}
		});
	}

	if( jQuery('#cobra-detail').length){
		// Cobra Slideshow
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

		// Cobra Tab Control
		jQuery('.tabs-button').click(function() {
			tab_name = jQuery(this).attr('name');
			jQuery(this).siblings('.active').removeClass('active');
			jQuery(this).parent().parent().find('.tabs-content.active').removeClass('active');
			jQuery(this).addClass('active');
			jQuery('.tabs-content-'+tab_name).addClass('active');
		});

		// Cobra Form
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

		// Cobra Caclulator
		jQuery('#loan-calculator-button').click(function(e) {
			form_name = jQuery(e.target).attr('name');
			jQuery('#loan-calculator').dialog({
				autoOpen: true,
				dialogClass: "dialog-loan-wrapper",
				title: "Loan Calculator",
				modal: true,
				resizable: false,
				width: 320,
				height: 500
			})
		});

		// Video Dialog
		var video_title = jQuery('#top-year').text() + ' ' + jQuery('#top-make').text() + ' ' + jQuery('#top-model').text();
		jQuery('#video-overlay-wrapper-dm').click(function(e) {
			jQuery('#dm-video-wrapper').dialog({
				autoOpen: true,
				dialogClass: "dialog-video-wrapper",
				modal: true,
				resizable: false,
				width: 640,
				height: 480,
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
				}
			})

			jQuery('.mejs-play > button').click();
		});

		function get_video_width(){
			results = jQuery('#wp-video-shortcode-wrapper > div').width();
			return results;
		}

	}

	jQuery(document).ready(function(){
		jQuery('#dealertrend-inventory-api').parent().addClass('inventory-parentClass');
		
		// AIS iFrame
		var frame = jQuery('<div class="aisframe"><iframe width="785" src="about:blank" height="415" frameborder="0"></iframe></div>');

		frame.appendTo( 'body' );

		jQuery( '.aisframe' ).dialog({
			autoOpen: false,
			dialogClass: "dialog-ais-wrapper",
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
	
	function get_list_input_values(e){
		e.preventDefault();
		query = document.location.search;
		jQuery('.list-search-value').each(function(){
			key = jQuery(this).attr('name');
			value = jQuery(this).val();
			if( value && !jQuery(this).hasClass('invalid') ){
				//console.log('Name: '+key+' | Value: '+value );
				query = '?'+add_query_field(query, key, value);
				//console.log(query);
			} else {
				query = '?'+remove_query_field(query, key);
			}
		
		})
		if( query.length == 1 ){
			document.location.search = '';
		} else{
			document.location.search = query;		
		}

	}

	function add_query_field(query, key, value){
		//e.preventDefault();
		key = encodeURI(key); value = encodeURI(value);
		var kvp = query.substr(1).split('&');
	    var i=kvp.length; var x; while(i--) 
	    {
			x = kvp[i].split('=');
	        if (x[0]==key)
	        {
				x[1] = value;
		        kvp[i] = x.join('=');
		        break;
		    }
		}
		if(i<0) {kvp[kvp.length] = [key,value].join('=');}
	    return kvp.join('&');
	}

	function remove_query_field(query, key){
		key = encodeURI(key);
		var kvp = query.substr(1).split('&');
	    var i=kvp.length; var x; while(i--) 
	    {
			x = kvp[i].split('=');
	        if (x[0]==key)
	        {
				kvp.splice(i,1)
		        break;
		    }
		}
	    return kvp.join('&');
	}

	/*
	function cobra_filter_select( type ) {

		switch( type ) {
			case 'make':
				jQuery('.cobra-select-models').val('');
				jQuery('.cobra-select-trims').val('');
				break;

			case 'model':
				jQuery('.cobra-select-trims').val('');
				break;
		}

		filter_url = build_url();
		window.location = filter_url;

	}

	function cobra_search_form() {

		form = jQuery('#cobra-search');
		form_url = build_url();

		if ( form_url.length > 1) {
			jQuery(form).attr('action', form_url);
			jQuery('#search-form-submit').click();
		}

	}

	function get_search_params() {
		obj = {};

		if ( jQuery('#cobra-saleclass').val() == 'Certified' ) {
			obj['saleclass'] = 'Used';
			obj['certified'] = 'yes';
		} else {
			obj['saleclass'] = jQuery('#cobra-saleclass').val();
			obj['certified'] = '';
		}

		if( jQuery('#cobra-search-box.invalid').length ){
		obj['search'] = '';
		} else {
			obj['search'] = jQuery('#cobra-search-box').val();
		}

		obj['price_from'] = jQuery('#cobra-price-from').val();
		obj['price_to'] = jQuery('#cobra-price-to').val();
		obj['year_from'] = jQuery('#cobra-year-from').val();
		obj['year_to'] = jQuery('#cobra-year-to').val();
		obj['mileage_from'] = jQuery('#cobra-mileage-from').val();
		obj['mileage_to'] = jQuery('#cobra-mileage-to').val();
		obj['vehicleclass'] = jQuery('#cobra-vehicleclass').val();

		return obj;
	}

	function get_filter_params() {
		obj = {};

		obj['make'] = jQuery('.cobra-select-makes').val();
		obj['model'] = jQuery('.cobra-select-models').val();
		obj['trim'] = jQuery('.cobra-select-trims').val();

		return obj;
	}

	function get_hidden_params() {
		obj = {};

		obj['rewrite'] = jQuery('#hidden-rewrite').val();
		obj['saleclass'] = jQuery('#hidden-saleclass').val();

		return obj;
	}

	function build_url() {

		s_params = get_search_params();
		f_params = get_filter_params();
		h_params = get_hidden_params();

		url_text = '';
		url_query = '';
		url_return = '';

		jQuery.each(s_params, function(key, value) { // Search Params
			if ( key != 'saleclass' ) {
				if ( value ) {
					if ( url_query.length > 1 ) {
						url_query += '&'+ key + '=' + value;
					} else {
						url_query = key + '=' + value;
					}
				}
			}
		});

		if ( h_params['rewrite'] ) {
			url_text = '/inventory/' + s_params['saleclass'] + '/';

			if ( f_params['make'] ) { // Filter Params (clean)
				url_text += f_params['make'] + '/';
				if ( f_params['model'] ) {
					url_text += f_params['model'] + '/';
					if  ( f_params['trim'] ) {
						if( url_query.length > 1 ){
							url_query += '&trim=' + f_params['trim'];
						} else {
							url_query = 'trim=' + f_params['trim'];
						}
					}
				}
			}

			if ( url_query.length <= 2 ) {
				url_query = '';
			}

		} else {
			url_text = '?taxonomy=inventory' + '&saleclass=' + s_params['salaclass'];
			if ( f_params['make'] ) { // Filter Params (not clean)
				url_text += '&make=' + f_params['make'];
				if ( f_params['model'] ) {
					url_text += '&model=' + f_params['model'];
					if  ( f_params['trim'] ) {
						url_text += '&trim=' + f_params['trim'];
					}
				}
			}

			if ( url_query.length <= 2 ) {
				url_query = '';
			}
		}

		if( url_query.length > 1 ) {
			url_return = url_text + '?' + url_query;
		} else {
			url_return = url_text;
		}

		return url_return;

	}
*/