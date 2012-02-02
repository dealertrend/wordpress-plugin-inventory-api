	var dealertrend = jQuery.noConflict();

	dealertrend('#cobra-quick-links > ul > li > span').click(function() {
		if(dealertrend(this).parent().hasClass('cobra-collapsed')) {
			dealertrend(this).parent().removeClass('cobra-collapsed');
			dealertrend(this).parent().addClass('cobra-expanded');
		} else {
			dealertrend(this).parent().addClass('cobra-collapsed');
			dealertrend(this).parent().removeClass('cobra-expanded');
		}
		if( dealertrend(this).parent().children('ul').is(":hidden")) {
			dealertrend(this).parent().children('ul').slideDown('slow', function() {});
		} else {
			dealertrend(this).parent().children('ul').slideUp('slow', function() {});
		}
	});
