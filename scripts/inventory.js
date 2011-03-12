// TODO: Make fx customizable

var dealertrend = jQuery.noConflict();

dealertrend(document).ready(function() {
	dealertrend('.dealertrend.inventory.detail .slideshow .images')
	.cycle({
		slideExpr: 'img',
  	fx: 'all',
		pager: '.dealertrend.inventory.detail .slideshow .navigation',
    pagerAnchorBuilder: function(idx, slide) { 
        return '<a href="#"><img src="' + slide.src + '" width="50" height"25" /></a>'; 
    } 
	});
});

