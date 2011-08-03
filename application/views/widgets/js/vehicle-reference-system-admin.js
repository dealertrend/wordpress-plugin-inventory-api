var dealertrend_admin = jQuery.noConflict();

function implement_ui() {

    console.log( 'vrs-widget: applying ui styles/scripts' );

    dealertrend_admin( 'select[multiple]="multiple"' ).each( function() {
        var label;
        var classes;
        var instance =  dealertrend_admin( this );

        console.log( 'vrs-widget: ' + instance.attr( 'id' ) + ' -> ' + instance.attr( 'class' ) + ' -> starting' );

        if( instance.has( 'button' ).length ) {
            console.log( 'vrs-widget: aborting! already styled' );
            return true;
        }

        if( instance.attr( 'name' ) == 'widget-vehicle_reference_system_widget[__i__][makes][]' ) {
            console.log( 'vrs-widget: aborting! not an active widget' );
            return true;
        }

        if( instance.hasClass( 'vrs-makes' ) ) {
            label = 'Select a Make';
            classes = 'makes';
        } else if( instance.hasClass( 'vrs-models' ) ) {
            label = 'Select a Model';
            classes = 'models';
        } else {
            console.log( 'vrs-widget: what do i do with this?' );
            return true;
        }

        instance
        .multiselect( {
             noneSelectedText: label,
             classes: classes,
             selectedList: 4
        } )
        .multiselectfilter();

        console.log( 'vrs-widget: ' + instance.attr( 'id' ) + ' -> ' + instance.attr( 'class' ) + ' -> finished' );

    } );
}

dealertrend_admin( document ).ready( function() {
    implement_ui();
} );


dealertrend_admin( 'body' ).ajaxSuccess( function( evt, request, settings ) {
    if( settings.data.search( '/id_base=vehicle_reference_system_widget.*action=save-widget/ig' ) ) {
        implement_ui();
    }
});
