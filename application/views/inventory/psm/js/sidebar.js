	var dealertrend = jQuery.noConflict();

	dealertrend('#psm-quick-links > ul > li > span').click(function() {
		if(dealertrend(this).parent().hasClass('psm-collapsed')) {
			dealertrend(this).parent().removeClass('psm-collapsed');
			dealertrend(this).parent().addClass('psm-expanded');
		} else {
			dealertrend(this).parent().addClass('psm-collapsed');
			dealertrend(this).parent().removeClass('psm-expanded');
		}
		if( dealertrend(this).parent().children('ul').is(":hidden")) {
			dealertrend(this).parent().children('ul').slideDown('slow', function() {});
		} else {
			dealertrend(this).parent().children('ul').slideUp('slow', function() {});
		}
	});
