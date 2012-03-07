var dealertrend = jQuery.noConflict();

dealertrend(document).ready(function(){

    tips = dealertrend( ".validate-tips" );

    function updateTips( t ) {
        tips.text( t ).addClass( "ui-state-highlight" );
        setTimeout(function() {
            tips.removeClass( "ui-state-highlight", 1500 );
        }, 500 );
    }

    function checkLength( o, n, min, max ) {
        if ( o.val().length > max || o.val().length < min ) {
            o.addClass( "ui-state-error" );
            updateTips( "Length of " + n + " must be between " + min + " and " + max + "." );
            return false;
        } else {
            return true;
        }
    }

        function checkRegexp( o, regexp, n ) {
            if ( !( regexp.test( o.val() ) ) ) {
                o.addClass( "ui-state-error" );
                updateTips( n );
                return false;
            } else {
                return true;
            }
        }

    dealertrend('#calculate').click(function() {

        dealertrend('#calculate-form').dialog({
            autoOpen: true,
            height: 300,
            width: 500,
            modal: true
        });

        return false;

    });

    // Let's create awesome tabular stuff.
    dealertrend('#cobra-inventory-tabs').tabs({
      show: function(event, ui) { 
        dealertrend('#cobra-inventory-tabs .ui-tabs-panel').each(function(){
        	dealertrend(this).jScrollPane({verticalDragMaxHeight: 13, horizontalGutter: 25});
        });
      }
    });

});
