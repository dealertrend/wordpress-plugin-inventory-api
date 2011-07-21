var dealertrend = jQuery.noConflict();

dealertrend(document).ready(function() {
    dealertrend('.armadillo-slideshow .armadillo-images')
    .cycle({
        slideExpr: 'img',
        fx: 'fade',
        pager: '.armadillo-slideshow .armadillo-navigation',
        pagerAnchorBuilder: function(idx, slide) { 
            return '<a href="#"><img src="' + slide.src + '" width="70" height"50" /></a>'; 
        } 
    });
});
