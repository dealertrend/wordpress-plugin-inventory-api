// TODO: Make fx customizable

var dealertrend = jQuery.noConflict();

/* Slideshow */
dealertrend(document).ready(function() {

	dealertrend('.dealertrend.inventory.detail .slideshow .images')
	.cycle({
		slideExpr: 'img',
  	fx: 'all',
		pager: '.dealertrend.inventory.detail .slideshow .navigation',
    pagerAnchorBuilder: function(idx, slide) { 
        return '<a href="#"><img src="' + slide.src + '" width="50" height"25" /></a>'; 
    } 
	});

});

/* Tabs */
dealertrend('#inventory-tabs').tabs();

/* If we want to have the tabs be a point of focus... */
//var anchor = dealertrend(document).attr('location').hash; // the anchor in the URL
//var index = dealertrend('#inventory-tabs div.ui-tabs-panel').index(dealertrend(anchor)); // in tab index of the anchor in the URL
//dealertrend('#inventory-tabs').tabs('select', index); // select the tab
//dealertrend('#inventory-tabs').bind('tabsshow', function(event, ui){document.location = dealertrend(document).attr('location').pathname + '#' + ui.panel.id;}); // change the url anchor when we click on a tab

