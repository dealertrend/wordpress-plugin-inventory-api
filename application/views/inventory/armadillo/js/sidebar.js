	var dealertrend = jQuery.noConflict();

	dealertrend('#armadillo-quick-links > ul > li > span').click(function() {
		if(dealertrend(this).parent().hasClass('armadillo-collapsed')) {
			dealertrend(this).parent().removeClass('armadillo-collapsed');
			dealertrend(this).parent().addClass('armadillo-expanded');
		} else {
			dealertrend(this).parent().addClass('armadillo-collapsed');
			dealertrend(this).parent().removeClass('armadillo-expanded');
		}
		if( dealertrend(this).parent().children('ul').is(":hidden")) {
			dealertrend(this).parent().children('ul').slideDown('slow', function() {});
		} else {
			dealertrend(this).parent().children('ul').slideUp('slow', function() {});
		}
	});
