var dealertrend_admin = jQuery.noConflict();

function implement_ui() {

    dealertrend_admin( 'select[multiple]="multiple"' ).each( function() {
        var label;
        var classes;
        var instance =  dealertrend_admin( this );

        if( instance.has( 'button' ).length ) {
            return true;
        }

        if( instance.hasClass( 'vrs-makes' ) ) {
            label = 'Select a Make';
            classes = 'makes';
        } else if( instance.hasClass( 'vrs-models' ) ) {
            label = 'Select a Model';
            classes = 'models';
        } else {
            return true;
        }

        instance
        .multiselect( {
             noneSelectedText: label,
             classes: classes,
             selectedList: 4
        } )
        .multiselectfilter();

    } );
}

dealertrend_admin( document ).ready( function() {
    implement_ui();
} );
