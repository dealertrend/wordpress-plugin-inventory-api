	var dealertrend = jQuery.noConflict();

	dealertrend('.quick-links > ul > li > span').click(function() {
		if(dealertrend(this).parent().hasClass('collapsed')) {
			dealertrend(this).parent().removeClass('collapsed');
			dealertrend(this).parent().addClass('expanded');
		} else {
			dealertrend(this).parent().addClass('collapsed');
			dealertrend(this).parent().removeClass('expanded');
		}
		if( dealertrend(this).parent().children('ul').is(":hidden")) {
			dealertrend(this).parent().children('ul').slideDown('slow', function() {});
		} else {
			dealertrend(this).parent().children('ul').slideUp('slow', function() {});
		}
	});
