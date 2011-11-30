<div id="uninstall-dialog" title="Confirm Uninstall" style="display:none;">
	<p>Are you sure you want to do this?.</p>
	<p>Click 'Proceed' below to <strong>permanently</strong> delete all current plugin data and deactivate the plugin.</p>
	<p>The plugin will revert back to its <strong>default settings</strong> upon reactivation.</p>
	<form name="confirm-uninstall" action="" method="post">
		<input type="hidden" name="action" id="action" value="uninstall" />
		<?php wp_nonce_field( 'dealertrend_inventory_api' ); ?>
		<button id="uninstall-yes" name="uninstall[]" value="true">Proceed</button>
		<button id="uninstall-cancel" name="uninstall[]" value="false">Cancel</button>
	</form>
</div>
