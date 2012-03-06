var dealertrend = jQuery.noConflict();

dealertrend(document).ready(function() {
    dealertrend('.slideshow .images')
    .cycle({
        slideExpr: 'img',
        fx: 'fade',
        pager: '.slideshow .navigation',
        next: '#next',
        prev: '#prev',
        pagerAnchorBuilder: function(idx, slide) {
            return '<a href="#"><img src="' + slide.src + '" width="70" height="50" /></a>';
        }
    });
    dealertrend('.slideshow #pause').click(function(e) {
        dealertrend('.slideshow .images').cycle('pause');
        e.preventDefault();
    });
    dealertrend('.images a')
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
