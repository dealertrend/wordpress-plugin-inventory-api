<?php

  global $dealertrend_api;
  global $wp_rewrite;

  if( $_POST ) { 
		# Security check.
    if( !wp_verify_nonce( $_POST[ '_wpnonce' ], 'dealertrend_api_options_update' ) ) die( 'Security check failed.' );

		# Do they want to uninstall the plugin?
		$uninstall = isset( $_POST[ 'uninstall' ][ 0 ] ) ? $_POST[ 'uninstall' ][ 0 ] : false;

		if( $uninstall == true ) {
			delete_option( 'dealertrend_api_options' );
			deactivate_plugins( $dealertrend_api->plugin_meta_data[ 'UninstallPath' ] );
			wp_redirect( '/wp-admin/plugins.php' );
			exit;
		}

		# Did they want to save the data?
    if( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'update' ) {
			# Remove trailing slashes as well as https.
			# This plugin should never need https and trailing slashes are ugly. ^_^
			$_POST[ 'api' ][ 'vehicle_management_system' ] = preg_replace( '/^(https?:\/\/)?(.+)([^a-z])$/i' , '$2' , $_POST[ 'api' ][ 'vehicle_management_system' ] );
			$_POST[ 'api' ][ 'vehicle_reference_system' ] = preg_replace( '/^(https?:\/\/)?(.+)([^a-z])$/i' , '$2' , $_POST[ 'api' ][ 'vehicle_reference_system' ] );
      $dealertrend_api->options[ 'company_information' ] = $_POST[ 'company_information' ];
      $dealertrend_api->options[ 'api' ] = $_POST[ 'api' ];
      $dealertrend_api->save_options();
    }   
  }

  $inventory_link = !empty($wp_rewrite->rules) ? '/inventory/' : '?taxonomy=inventory';

  $company_information = $dealertrend_api->get_company_information();

  $inventory_data = $dealertrend_api->get_inventory();

  $site_link = '<span style="white-space:nowrap;"><a href="http://www.dealertrend.com" target="_blank" title="DealerTrend, Inc: Shift Everything">DealerTrend, Inc.</a></span>';

?>

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

<div id="icon-dealertrend" class="icon32"><br /></div>
<h2><?php echo $this->plugin_meta_data[ 'Name' ]; ?> Settings</h2>
<div id="option-tabs" style="clear:both;">
	<ul>
		<li><a href="#feeds">Feeds</a></li>
		<li><a href="#settings">Settings</a></li>
		<li><a href="#help">Help</a></li>
	</ul>
	<div id="feeds">
  	<table width="450">
    	<tr>
				<td colspan="2"><h3 class="title">Inventory Feed</h3></td>
			</tr>
    	<tr>
      	<td width="125">Feed Status:</td>
      	<td><?php echo ( $dealertrend_api->status[ 'inventory_json' ] === true ) ? '<span class="success">Loaded</span>' : '<span class="fail">Unavailable</span>' ?></td>
    	</tr>
    	<tr>
      	<td>&nbsp;</td>
      	<td><small>Response time:<?php echo $this->report[ 'inventory_download_time' ]; ?> seconds</small></td>
    	</tr>
  	</table>
  	<table width="450">
    	<tr>
				<td colspan="2"><h3 class="title">Company Information Feed</h3></td>
			</tr>
    	<tr>
      	<td width="125">Account Status:</td>
      	<td><?php echo ( $dealertrend_api->status[ 'company_information' ] === true ) ? '<span class="success">Loaded</span>' : '<span class="fail">Unavailable</span>' ?></td>
    	</tr>
    	<tr>
				<td>&nbsp;</td>
      	<td><small>Response time: <?php echo $this->report[ 'company_information_download_time']; ?> seconds</small></td>
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
		</table>
    <?php endif; ?>
  </table>

	</div>
	<div id="settings">
  	<form name="dealertrend_api_options_form" method="post" action="">
    	<?php wp_nonce_field( 'dealertrend_api_options_update' ); ?>
    	<table width="450">
				<tr>
					<td colspan="2">
						<h3 class="title">API Settings</h3>
					</td>
				</tr>
      	<tr>
        	<td width="200"><label for="vehicle-management-system">Vehicle Management System:</label></td>
        	<td><input type="text" id="vehicle-management-system" name="api[vehicle_management_system]" value="<?php echo $dealertrend_api->options[ 'api' ][ 'vehicle_management_system' ] ?>" class="long_input" /></td>
      	</tr>
      	<tr>
        	<td width="200"></td>
        	<td><small>Inventory will not be available without providing a valid VMS from <?php echo $site_link; ?></small></td>
      	</tr>
      	<tr>
        	<td width="200"><label for="vehicle-reference-system">Vehicle Reference System:</label></td>
        	<td><input type="text" id="vehicle-reference-system" name="api[vehicle_reference_system]" value="<?php echo $dealertrend_api->options[ 'api' ][ 'vehicle_reference_system' ] ?>" class="long_input" /></td>
      	</tr>
      	<tr>
        	<td width="200"></td>
        	<td><small>Showcase and certain tools will not be available without providing a valid VRS from <?php echo $site_link; ?></small></td>
      	</tr>
    	</table>
    	<table width="450">
      	<tr>
					<td colspan="2"><h3 class="title">Company Settings</h3></td>
				</tr>
      	<tr>
        	<td width="125"><label for="company-id">Company ID:</a></td>
        	<td><input type="text" name="company_information[id]" id="company-id" value="<?php echo $dealertrend_api->options[ 'company_information' ][ 'id' ] ?>" /></td>
      	</tr>
      	<tr>
        	<td width="200"><small>Pulls inventory from a specific dealership.</small></td>
        	<td><small>Inventory will not be retreived without providing a valid company ID from <?php echo $site_link; ?></small></td>
      	</tr>
				<tr>
					<td>
    				<input type="hidden" name="action" value="update" />
    				<p class="submit">
      				<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
    				</p>
					</td>
					<td>
						<button id="uninstall" name="uninstall" value="true">Perform Clean Uninstall</button>
					</td>
				</tr>
			</table>
  	</form>
	</div>
	<div id="help">
		<h3 class="title">Initial Setup</h3>
		<p>To get the plugin started you'll need to specific a VMS and a valid Company ID.</p>
		<p>Both of these will be provided to you upon purchasing a license with <?php echo $site_link; ?></p>
		<p>After you've received a valid VMS and Company ID, you'll need to go to the <a id="settings-link" href="#settings" title="DealerTrend API Settings">settings page</a> and fill in their respective fields. Once you click "Save Changes" it will start pulling in your Inventory and Company Feeds.</p>

		<h3 class="title">Viewing Inventory</h3>
		<p>If the VMS and Company Feed are both loaded, you may view your inventory here: <a href="<?php bloginfo( 'url' ); echo $inventory_link; ?>" target="_blank"><?php bloginfo( 'url' ); echo $inventory_link; ?></a></p>
		<p>Please note that any pages or sub-pages that reside at this permalink will no longer be shown.</p>

		<h3 class="title">Plugin Legend</h3>
		<table width="450" cellspacing="20">
			<tr>
				<td><span class="fail">Unavailable</span></td>
				<td>This means that the feed is currently not available. If this is showing, then that feed will not display information on your site.</td>
			</tr>
			<tr>
				<td><span class="success">Loaded</a></td>
				<td>If you see this, that means the feed is loaded and the information will be displayed on your website.</td>
			</tr>
		</table>
	</div>
</div>
