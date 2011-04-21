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

});
