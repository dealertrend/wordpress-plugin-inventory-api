var dealertrend_admin = jQuery.noConflict();

dealertrend_admin(document).ready(function(){
	dealertrend_admin('#uninstall').click(function(){
		dealertrend_admin('#uninstall-dialog').dialog();
		dealertrend_admin('#uninstall-dialog #uninstall-cancel').click(function(){
			dealertrend_admin('#uninstall-dialog').dialog('close');
			return false;
		});
		return false;
	});
	dealertrend_admin('#option-tabs').tabs();
});
