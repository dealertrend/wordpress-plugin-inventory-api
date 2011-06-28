var dealertrend = jQuery.noConflict();

dealertrend(document).ready(function() {
	dealertrend('.images')
	.cycle({
		slideExpr: 'img',
		fx: 'all',
		pagerAnchorBuilder: function(idx, slide) { 
			return '<a href="#"><img src="' + slide.src + '" width="60" height"50" /></a>';
		} 
	});
});
