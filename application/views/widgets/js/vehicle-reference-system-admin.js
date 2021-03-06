function implement_ui() {

    jQuery( 'select[multiple="multiple"]' ).each( function() {
        var label;
        var classes;
        var instance =  jQuery( this );

        if( instance.has( 'button' ).length ) {
            return true;
        }

        if( instance.attr( 'name' ) == 'widget-vehicle_reference_system_widget[__i__][makes][]' ) {
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

jQuery( document ).ready( function() {
    implement_ui();
} );

jQuery( document ).ajaxSuccess( function( evt, request, settings ) {
	if ( settings != undefined ) {
		if( typeof( settings.data ) !== 'undefined' ) {
		    if( settings.data.search( '/id_base=vehicle_reference_system_widget.*action=save-widget/ig' ) ) {
		        implement_ui();
		    }
		}
	}
});
