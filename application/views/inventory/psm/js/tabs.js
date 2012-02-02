var dealertrend = jQuery.noConflict();

dealertrend(document).ready(function(){

    // Let's create awesome tabular stuff.
    dealertrend('#psm-inventory-tabs').tabs({
    	show: function(event, ui) { 
    		dealertrend('#psm-inventory-tabs .ui-tabs-panel').each(function(){
    			if (dealertrend(this).hasClass('ui-tabs-hide')) {
    			
    			} else {
    				dealertrend(this).jScrollPane({verticalDragMaxHeight: 13, horizontalGutter: 15});
    			}
    		});
    	}
    });
});
