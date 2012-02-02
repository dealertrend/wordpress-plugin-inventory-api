var dealertrend = jQuery.noConflict();

dealertrend(document).ready(function() {
    dealertrend('.cobra-slideshow .cobra-images')
    .cycle({
        slideExpr: 'img',
        fx: 'fade',
        pager: '.cobra-slideshow .cobra-navigation',
        pagerAnchorBuilder: function(idx, slide) {
            return '<a href="#"><img src="' + slide.src + '" width="70" height="50" /></a>';
        }
    });
    dealertrend('.cobra-images a')
    .lightbox({
        imageClickClose: false,
        loopImages: true,
        fitToScreen: true,
        scaleImages: true,
        xScale: 1.0,
        yScale: 1.0,
        displayDownloadLink: true
    });
});
