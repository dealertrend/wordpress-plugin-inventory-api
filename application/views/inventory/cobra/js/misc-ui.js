var dealertrend = jQuery.noConflict();

dealertrend(document).ready(
	function(){
		dealertrend( '.jquery-ui-button' ).button().each(
			function() {
				if( dealertrend( this ).hasClass( 'disabled' ) == true ) {
					dealertrend( this ).button( "option", "icons", {primary:'ui-icon-triangle-1-e'} );
					dealertrend( this ).button({ disabled: true } ).click(
						function() {
							return false;
						}
					);
				}
			}
		);

		var frame = dealertrend('<div class="icanhazmodal"><iframe width="785" src="about:blank" height="415" frameborder="0"></iframe></div>');

		frame.appendTo( 'body' );

		dealertrend( '.icanhazmodal' ).dialog({
			autoOpen: false,
			modal: true,
			resizable: false,
			width: 820,
			height: 485,
			open: function( event , ui ) { dealertrend( '.ui-widget-overlay').click( function() { dealertrend( '.icanhazmodal' ).dialog( 'close' ); } ); },
			title: 'Incentives and Rebates'
		});

		dealertrend( '.view-available-rebates > a' ).click(
			function() {
				dealertrend( '.icanhazmodal' ).dialog( 'open' );
				return false;
			}
		);
	}
)

function loadIframe( url ) {
		var iframe = dealertrend( 'iframe' );
		if ( iframe.length ) {
				iframe.attr( 'src' , url );
				return false;
		}
		return true;
}
