	var dealertrend = jQuery.noConflict();

	dealertrend(document).ready(function() {
		dealertrend('.dealertrend.inventory.wrapper .detail .slideshow .images')
		.cycle({
			slideExpr: 'img',
			fx: 'fade',
			pager: '.dealertrend.inventory.wrapper .detail .slideshow .navigation',
			pagerAnchorBuilder: function(idx, slide) { 
				return '<a href="#"><img src="' + slide.src + '" width="70" height"50" /></a>'; 
			} 
		});
	});
