var dealertrend = jQuery.noConflict();

dealertrend( document ).ready(
  function() {
    dealertrend( '.vms-widget-content' ).tabs();
    dealertrend( '.vms-widget-content.carousel .vms-widget-content-wrapper' ).carousel( { autoSlide: true , loop: true } );
  }
);
