var dealertrend = jQuery.noConflict();

dealertrend(document).ready(function() {
    dealertrend('.psm-slideshow .psm-images')
    .cycle({
        slideExpr: 'img',
        fx: 'fade',
        pager: '.psm-slideshow .psm-navigation',
        pagerAnchorBuilder: function(idx, slide) {
            return '<a href="#"><img src="' + slide.src + '" width="70" height"50" /></a>';
        }
    });
    dealertrend('.psm-images a')
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
