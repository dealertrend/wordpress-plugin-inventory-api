var dealertrend_admin = jQuery.noConflict();

dealertrend_admin(document).ready(function(){

  // If they want to abandon the uninstall, let's make that easy.
  dealertrend_admin('#uninstall').click(function(){
    dealertrend_admin('#uninstall-dialog').dialog();
    dealertrend_admin('#uninstall-dialog #uninstall-cancel').click(function(){
      dealertrend_admin('#uninstall-dialog').dialog('close');
      return false;
    });
    return false;
  });

  // Let's create awesome tabular stuff.
  dealertrend_admin('#option-tabs').tabs();

  // If they click on the settings ink on the options page - let's take them to our settings page.
  dealertrend_admin('#settings-link').click(function() {
    dealertrend_admin('#option-tabs').tabs('select', '#settings');
    return false;
  });

dealertrend_admin("#option-tabs").bind("tabsshow", function(event, ui) { 
    window.location.hash = ui.tab.hash;
})

function implement_ui() {

    dealertrend_admin( 'select[multiple]="multiple"' ).each( function() {
        var label;
        var classes;
        var instance =  dealertrend_admin( this );

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

dealertrend_admin( document ).ready( function() {
    implement_ui();
} );

dealertrend_admin( 'body' ).ajaxSuccess( function( evt, request, settings ) { 
    if( typeof( settings.data ) !== 'undefined' ) {
        if( settings.data.search( '/id_base=vehicle_reference_system_widget.*action=save-widget/ig' ) ) {
            implement_ui();
        }
    }
});

});
