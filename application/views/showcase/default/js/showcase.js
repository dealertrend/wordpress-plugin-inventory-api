(function () {

    var dealertrend = jQuery.noConflict();

    dealertrend(document).ready(function () {
        dealertrend('#showcase-tabs').tabs();
        dealertrend('#overview-tabs').tabs();
        var current_image, next_image, color_text;
        color_text = dealertrend('#color-text');
        dealertrend('#swatches a').click(function (e) {
            current_image = dealertrend('#spotlight .active');
            next_image = dealertrend('#spotlight ' + e.target.hash);
            current_image.removeClass('active').hide();

            color_text.text(e.target.title);

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
            color_text.text(e.target.title);
        }, function (e) {
            next_image.removeClass('active').hide();
            current_image.show().addClass('active');
            color_text.text(dealertrend('#swatches .active').attr('title'));
        });
    });

}());
