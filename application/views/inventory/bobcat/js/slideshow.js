var dealertrend = jQuery.noConflict();

dealertrend(document).ready(function() {
	dealertrend('.slideshow .images')
	.cycle({
		slideExpr: 'img',
		fx: 'fade',
		pager: '.slideshow .navigation',
		pagerAnchorBuilder: function(idx, slide) { 
			return '<a href="#"><img src="' + slide.src + '" width="60" height"50" /></a>';
		} 
	});
});
