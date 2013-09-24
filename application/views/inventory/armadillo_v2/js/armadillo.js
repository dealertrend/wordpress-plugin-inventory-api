// Armadillo Detail

if( jQuery('#armadillo-detail').length ){

	jQuery(document).ready(function(){
		// Creates Tabs
		jQuery('#armadillo-inventory-tabs').tabs();

		// Runs Slideshow
		jQuery('.armadillo-slideshow .armadillo-images')
		.cycle({
		    slideExpr: 'img',
		    fx: 'fade',
		    pager: '.armadillo-slideshow .armadillo-navigation',
		    pagerAnchorBuilder: function(idx, slide) {
		        return '<a href="#"><img src="' + slide.src + '" width="70" height="50" /></a>';
		    }
		});
		jQuery('.armadillo-images a')
		.lightbox({
		    imageClickClose: false,
		    loopImages: true,
		    fitToScreen: true,
		    scaleImages: true,
		    xScale: 1.0,
		    yScale: 1.0,
		    displayDownloadLink: true
		});

		// Highlight Tips
		tips = jQuery( ".armadillo-validate-tips" );

		function updateTips( t ) {
		    tips.text( t ).addClass( "ui-state-highlight" );
		    setTimeout(function() {
		        tips.removeClass( "ui-state-highlight", 1500 );
		    }, 500 );
		}

		function checkLength( o, n, min, max ) {
		    if ( o.val().length > max || o.val().length < min ) {
		        o.addClass( "ui-state-error" );
		        updateTips( "Length of " + n + " must be between " + min + " and " + max + "." );
		        return false;
		    } else {
		        return true;
		    }
		}

	    function checkRegexp( o, regexp, n ) {
	        if ( !( regexp.test( o.val() ) ) ) {
	            o.addClass( "ui-state-error" );
	            updateTips( n );
	            return false;
	        } else {
	            return true;
	        }
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

		jQuery('#armadillo-calculate').click(function() {

			if( jQuery(this).attr('class') == 'hide-form' ){
				jQuery(this).attr('class','show-form');
			    jQuery('#armadillo-calculate-form').slideDown();
			} else {
				jQuery(this).attr('class','hide-form');
			    jQuery('#armadillo-calculate-form').slideUp();
			}

		});

		jQuery('#calculate-close-form').click(function() {
			jQuery('#armadillo-calculate').attr('class','hide-form');
			jQuery('#armadillo-calculate-form').slideUp();
		});

	});
}

// Armadillo Listing
if( jQuery('#armadillo-listing').length ) {
	// Quick Links
	jQuery('#armadillo-quick-links > ul > li > span').click(function() {
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
}


// Armadillo General

jQuery(document).ready(function(){
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

	// AIS
	jQuery( '.jquery-ui-button' ).button().each(
		function() {
			if( jQuery( this ).hasClass( 'disabled' ) == true ) {
				jQuery( this ).button( "option", "icons", {primary:'ui-icon-triangle-1-e'} );
				jQuery( this ).button({ disabled: true } ).click(
					function() {
						return false;
					}
				);
			}
		}
	);

	var frame = jQuery('<div class="icanhazmodal"><iframe width="785" src="about:blank" height="415" frameborder="0"></iframe></div>');

	frame.appendTo( 'body' );

	jQuery( '.icanhazmodal' ).dialog({
		autoOpen: false,
		modal: true,
		resizable: false,
		width: 820,
		height: 485,
		open: function( event , ui ) { jQuery( '.ui-widget-overlay').click( function() { jQuery( '.icanhazmodal' ).dialog( 'close' ); } ); },
		title: 'Incentives and Rebates'
	});

	jQuery( '.view-available-rebates > a' ).click(
		function() {
			jQuery( '.icanhazmodal' ).dialog( 'open' );
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
