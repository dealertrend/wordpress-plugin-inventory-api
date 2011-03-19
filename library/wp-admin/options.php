<div id="uninstall-dialog" title="Confirm Uninstall" style="display:none;">
	<p>Are you sure you want to do this?.</p>
	<p>Click 'Proceed' below to <strong>permanently</strong> delete all current plugin data and deactivate the plugin.</p>
	<p>The plugin will revert back to its <strong>default settings</strong> upon reactivation.</p>
	<form name="confirm-uninstall" action="" method="post">
		<?php wp_nonce_field( 'dealertrend_api_options_update' ); ?>
		<button id="uninstall-yes" name="uninstall[]" value="true">Proceed</button>
		<button id="uninstall-cancel" name="uninstall[]" value="false">Cancel</button>
	</form>
</div>
<?php

  global $dealertrend_api;

  if( $_POST ) { 
    if( !wp_verify_nonce( $_POST[ '_wpnonce' ], 'dealertrend_api_options_update' ) ) die( 'Security check failed.' );

		if( $_POST[ 'uninstall' ][ 0 ] == 'true' ) {
			delete_option( 'dealertrend_api_options' );
			deactivate_plugins( $dealertrend_api->plugin_meta_data[ 'UninstallPath' ] );
			wp_redirect( '/wp-admin/plugins.php' );
			exit;
		}

    if( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'update' ) {        
      $dealertrend_api->options[ 'company_information' ] = $_POST[ 'company_information' ];
      $dealertrend_api->options[ 'api' ] = $_POST[ 'api' ];
      $dealertrend_api->save_options();
    }   
  }

  $start_feed_timer = timer_stop();
  $company_information = $dealertrend_api->get_company_information();
  $stop_feed_timer = timer_stop();
  $company_feed_timer_results = $stop_feed_timer - $start_feed_timer;

  $start_feed_timer = timer_stop();
  $inventory_data = $dealertrend_api->get_inventory();
  $stop_feed_timer = timer_stop();
  $inventory_feed_timer_results = $stop_feed_timer - $start_feed_timer;

  $site_link = '<span style="white-space:nowrap;"><a href="http://www.dealertrend.com" target="_blank" title="DealerTrend, Inc: Shift Everything">DealerTrend, Inc.</a></span>';
  
?>
<div class="wrap">
  <div id="icon-dealertrend" class="icon32"><br /></div>
  <h2><?php echo $this->plugin_meta_data[ 'Name' ]; ?> Settings</h2>
  <table width="450">
    <tr>
			<td colspan="2"><h3 class="title">Inventory Information</h3></td>
		</tr>
    <tr>
      <td width="125">Feed Status:</td>
      <td><?php echo ( $dealertrend_api->status[ 'inventory_json' ] === true ) ? '<span class="success">Loaded</span>' : '<span class="fail">Unavailable</span>' ?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><small>Response time:<?php echo $inventory_feed_timer_results; ?> seconds</small></td>
    </tr>
  </table>

  <table width="450">
    <tr>
			<td colspan="2"><h3 class="title">Company Information</h3></td>
		</tr>
    <tr>
      <td width="125">Account Status:</td>
      <td><?php echo ( $dealertrend_api->status[ 'company_information' ] === true ) ? '<span class="success">Loaded</span>' : '<span class="fail">Unavailable</span>' ?></td>
    </tr>
    <?php if( $dealertrend_api->status[ 'company_information' ] === true ): ?>
    <tr>
      <td>Name:</td>
      <td><strong><?php echo $company_information->name; ?></strong></td>
    </tr>
    <tr>
      <td>Address:</td>
      <td><strong><?php echo $company_information->street . ' ' . $company_information->city . ' ' . $company_information->state . ' ' . $company_information->zip; ?></strong></td>
    </tr>
    <tr>
      <td>Phone:</td>
      <td><strong><?php echo $company_information->phone; ?></strong></td>
    </tr>
    <tr>
      <td>Fax:</td>
      <td><strong><?php echo $company_information->fax; ?></strong></td>
    </tr>
    <tr>
      <td>Country Code:</td>
      <td><strong><?php echo $company_information->country_code; ?></strong></td>
    </tr>
    <?php endif; ?>
    <tr>
      <td>&nbsp;</td>
      <td><small>Response time:<?php echo $company_feed_timer_results; ?> seconds</small></td>
    </tr>
  </table>

  <form name="dealertrend_api_options_form" method="post" action="">
    <?php wp_nonce_field( 'dealertrend_api_options_update' ); ?>
    <table width="450">
      <tr>
				<td colspan="2"><h3 class="title">Plugin Settings</h3></td>
			</tr>
      <tr valign="top">
        <td width="200"><label for="vehicle-management-system">Vehicle Management System:</label></td>
        <td><input type="text" id="vehicle-management-system" name="api[vehicle_management_system]" value="<?php echo $dealertrend_api->options[ 'api' ][ 'vehicle_management_system' ] ?>" class="long_input" /></td>
      </tr>
      <tr valign="bottom">
        <td width="200"><small>Provides inventory data.</small></td>
        <td><small>Inventory will not be available without providing a valid VMS from <?php echo $site_link; ?></small></td>
      </tr>
      <tr valign="top">
        <td width="200"><label for="vehicle-reference-system">Vehicle Reference System:</label></td>
        <td><input type="text" id="vehicle-reference-system" name="api[vehicle_reference_system]" value="<?php echo $dealertrend_api->options[ 'api' ][ 'vehicle_reference_system' ] ?>" class="long_input" /></td>
      </tr>
      <tr valign="bottom">
        <td width="200"><small>Provides vehicle reference data.</small></td>
        <td><small>Showcase and certain tools will not be available without providing a valid VRS from <?php echo $site_link; ?></small></td>
      </tr>
    </table>
    <table width="450">
      <tr>
				<td colspan="2"><h3 class="title">Company Settings</h3></td>
			</tr>
      <tr valign="top">
        <td width="125"><label for="company-id">Company ID:</a></td>
        <td><input type="text" name="company_information[id]" id="company-id" value="<?php echo $dealertrend_api->options[ 'company_information' ][ 'id' ] ?>" /></td>
      </tr>
      <tr valign="bottom">
        <td width="200"><small>Pulls inventory from a specific dealership.</small></td>
        <td><small>Inventory will not be retreived without providing a valid company ID from <?php echo $site_link; ?></small></td>
      </tr>
		</table>
		<table>
			<tr>
				<td><h3 class="title">Plugin Settings</h3></td>
			</tr>
			<tr>
				<td width="200"><label for="uninstall">Perform Clean Uninstall:</a></td>
				<td><button id="uninstall" name="uninstall" value="true">Uninstall</button></td>
			</tr>
      <tr valign="bottom">
        <td width="125"></td>
        <td width="200"><small>This will wipe all plugin data and deacivate the plugin.</small></td>
      </tr>
		</table>
		<table width="450">
			<tr>
				<td colspan="2">
    			<input type="hidden" name="action" value="update" />
    			<p class="submit">
      			<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
    			</p>
				</td>
			</tr>
		</table>
  </form>
</div>
