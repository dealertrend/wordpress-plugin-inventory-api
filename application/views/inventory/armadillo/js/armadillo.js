// Armadillo Detail

if( jQuery('#armadillo-detail').length ){

	jQuery(document).ready(function(){

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

		// Tab Control
		jQuery('.tabs-button').click(function() {
			tab_name = jQuery(this).attr('name');
			jQuery(this).siblings('.active').removeClass('active');
			jQuery(this).parent().parent().find('.tabs-content.active').removeClass('active');
			jQuery(this).addClass('active');
			jQuery('.tabs-content-'+tab_name).addClass('active');
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
				}
			})

			jQuery('.mejs-play > button').click();
		});

		function get_video_width(){
			results = jQuery('#wp-video-shortcode-wrapper > div').width();
			//console.log(results);
			return results;
		}

		// Detail Form buttons
		jQuery('#armadillo-schedule').click(function() {

		    var name = jQuery( "#formvehicletestdrive-name" ),
		    email = jQuery( "#formvehicletestdrive-email" ),
		    phone = jQuery( "#formvehicletestdrive-phone" ),
		    comments = jQuery( "#formvehicletestdrive-comments" ),
		    allFields = jQuery( [] ).add( name ).add( email ).add( phone ).add( comments );

		    jQuery('#armadillo-schedule-form').dialog({
		        autoOpen: true,
		        height: 500,
		        width: 400,
		        modal: true,
		        buttons: {
		            "Send Inquiry": function() {
		                var bValid = true;
		                allFields.removeClass( "ui-state-error" );
		                bValid = bValid && checkLength( name, "Your Name", 1, 100 );
		                bValid = bValid && checkLength( email, "Your Email", 6, 80 );
		                bValid = bValid && checkLength( phone, "Your Phone Number", 7, 20 );
		                bValid = bValid && checkLength( comments, "Your Comments", 1, 255 );
		                // From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
		                bValid = bValid && checkRegexp( email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "E-mail validation failed. Please try again." );
		                if ( bValid ) {
		                    jQuery( '#formvehicletestdrive' ).submit();
		                    jQuery( this ).dialog( "close" );
		                }
		            },
		            Cancel: function() {
		                jQuery( this ).dialog( "close" );
		            }
		        },
		        close: function() {
		            allFields.val( "" ).removeClass( "ui-state-error" );
		            tips.text( "" ).removeClass( "ui-state-highlight" );
		        }
		    });

		    return false;

		});

		jQuery('#loan-calculator-button').click(function() {
			if( jQuery(this).siblings('#loan-calculator-data').hasClass('active') ){
				jQuery('#loan-calculator-data').removeClass('active');
			} else {
				jQuery('#loan-calculator-data').addClass('active');
			}
		});
		
	});
}

// Armadillo Listing
if( jQuery('#armadillo-listing').length ) {
	// Quick Links
	jQuery('#armadillo-list-sidebar > ul > li .list-sidebar-label').click(function() {
		if(jQuery(this).parent().hasClass('armadillo-collapsed')) {
			jQuery(this).parent().removeClass('armadillo-collapsed');
			jQuery(this).parent().addClass('armadillo-expanded');
		} else {
			jQuery(this).parent().addClass('armadillo-collapsed');
			jQuery(this).parent().removeClass('armadillo-expanded');
		}
		if( jQuery(this).parent().children('ul').is(":hidden")) {
			jQuery(this).parent().children('ul').slideDown('slow', function() {});
		} else {
			jQuery(this).parent().children('ul').slideUp('slow', function() {});
		}
	});
	// Mobile Click
	jQuery('#list-sidebar-label-mobile').click(function(){
		if( jQuery(this).hasClass('mobile-click') ){
			if(jQuery(this).hasClass('mobile-active')){
				jQuery(this).removeClass('mobile-active');
				jQuery('#armadillo-list-sidebar > ul').removeClass('active');
				jQuery(this).text('Refine Your Search');
			} else {
				jQuery(this).addClass('mobile-active');
				jQuery('#armadillo-list-sidebar > ul').addClass('active');
			}
		} else {
			jQuery('#armadillo-list-sidebar > ul').addClass('active');
			jQuery(this).addClass('mobile-click');
			jQuery(this).addClass('mobile-active');
			jQuery('#armadillo-list-sidebar > ul > li .list-sidebar-label').each(function(){
				jQuery(this).click();
			});
			jQuery(this).text('Hide Refined Search');
		}
	});
}


// Armadillo General

jQuery(document).ready(function(){
	jQuery('#dealertrend-inventory-api').parent().addClass('inventory-parentClass');
	
	// Helps Responsive Menu
	if (jQuery('#dealertrend-inventory-api').length){
		jQuery('#armadillo-quick-links').attr('name','hidden');
		jQuery('#armadillo-quick-links > h3').click(function(){
			if (jQuery('#armadillo-quick-links').attr('name').match(/hidden/i) != null){
				jQuery('#armadillo-quick-links').attr('name','show');
				jQuery('#armadillo-quick-links > ul').slideDown();
			} else {
				jQuery('#armadillo-quick-links').attr('name','hidden');
				jQuery('#armadillo-quick-links > ul').slideUp();
			}
		});
	}

	// AIS iFrame
	var frame = jQuery('<div class="aisframe"><iframe id="ais-iframe" width="785" src="about:blank" height="415" frameborder="0"></iframe></div>');

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
	
	jQuery('.armadillo-vehicle .ais-link-js span').click(function(e){
		ais_url = jQuery(e.target).attr('href');
		loadIframe(ais_url);
		jQuery( '.aisframe' ).dialog( 'open' );
		return false;
	});
	
});

function loadIframe( url ) {
		var iframe = jQuery('#ais-iframe');
		if ( iframe.length ) {
				iframe.attr( 'src' , url );
				return false;
		}
		return true;
}

function list_search_field(e){
	e.preventDefault();
	key = encodeURI('search'); value = encodeURI(jQuery('#armadillo-search-box').val());
	var kvp = document.location.search.substr(1).split('&');
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
    document.location.search = kvp.join('&'); 
}