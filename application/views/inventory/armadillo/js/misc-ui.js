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

    var overlay = dealertrend(
      '<div class="ui-overlay"><div class="icanhazmodal"><iframe width="785" src="about:blank" height="415" frameborder="0"></iframe></div><div class="ui-widget-overlay"></div></div>'
    ).hide().appendTo('body');

    dealertrend( '.icanhazmodal' ).dialog({
      autoOpen: false,
      modal: false,
      resizable: false,
      width: 820,
      height: 485,
      close: function( event , ui ) { overlay.fadeOut(); },
      title: 'Incentives and Rebates'
    });

    dealertrend( '.view-available-rebates > a' ).click(
      function() {
        dealertrend( '.ui-widget-overlay' ).height( dealertrend( document ).height() );
        overlay.fadeIn();
        dealertrend( '.icanhazmodal' ).dialog( 'open' );
      }
    );

    dealertrend( '.ui-widget-overlay').click(
      function() {
        dealertrend( '.icanhazmodal' ).dialog( 'close' );;
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
