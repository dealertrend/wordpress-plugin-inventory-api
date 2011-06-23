var dealertrend = jQuery.noConflict();

dealertrend(document).ready(
  function(){
    dealertrend( '.jquery-ui-button' ).button().each(
      function() {
        if( dealertrend( this ).hasClass( 'disabled' ) == true ) {
          dealertrend( this ).button({ disabled: true }).click(
            function() {
              return false;
            }
          );
        }
      }
    );

    var overlay = dealertrend(
      '<div class="ui-overlay"><div class="icanhazmodal"><iframe width="785" src="about:blank" height="615" frameborder="0"></iframe></div><div class="ui-widget-overlay"></div></div>'
    ).hide().appendTo('body');

    dealertrend( '.icanhazmodal' ).dialog({
      autoOpen: false,
      modal: false,
      resizable: false,
      width: 820,
      height: 700,
      close: function( event , ui ) { overlay.fadeOut(); },
      title: 'Incentives and Rebates'
    });

    dealertrend( '.view-available-rebates > a' ).click(
      function() {
        overlay.fadeIn();
        dealertrend( '.icanhazmodal' ).dialog( 'open' );
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
