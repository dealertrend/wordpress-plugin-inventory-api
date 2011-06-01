var dealertrend = jQuery.noConflict();

function docarousel() {
	dealertrend('.vrs-widget.carousel').carousel( { autoSlide: true , loop: true } );
}

dealertrend(document).ready(function() {
	dealertrend('.vrs-widget-item-wrapper').tabs({});
	docarousel();
});
