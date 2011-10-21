(function () {

    var dealertrend = jQuery.noConflict();

    dealertrend(document).ready(function () {
        dealertrend('#showcase-tabs').tabs();
        var current_image, next_image;
        dealertrend('#swatches a').click(function (e) {
            current_image = dealertrend('#spotlight .active');
            next_image = dealertrend('#spotlight ' + e.target.hash);
            current_image.removeClass('active').hide();
            next_image.show().addClass('active');
            current_image = next_image;
            dealertrend('#swatches .active').removeClass('active');
            dealertrend('#' + e.target.id).addClass('active');
            e.preventDefault();
        });
        dealertrend('#swatches a').hover(function (e) {
            current_image = dealertrend('#spotlight .active');
            next_image = dealertrend('#spotlight ' + e.target.hash);
            current_image.removeClass('active').hide();
            next_image.show().addClass('active');
        }, function (e) {
            next_image.removeClass('active').hide();
            current_image.show().addClass('active');
        });
    });

}());
