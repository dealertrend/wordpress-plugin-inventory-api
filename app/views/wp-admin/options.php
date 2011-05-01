<?php

	global $dealertrend_inventory_api;
	global $wp_rewrite;

	if( $_POST ) {

		# Security check.
		if( !wp_verify_nonce( $_POST[ '_wpnonce' ], 'dealertrend_inventory_api' ) ) die( 'Security check failed.' );

		# Do they want to uninstall the plugin?
		$uninstall = isset( $_POST[ 'uninstall' ][ 0 ] ) ? $_POST[ 'uninstall' ][ 0 ] : false;

		$_POST = array_map( array( &$dealertrend_inventory_api , 'sanitize_inputs' ) , &$_POST );

		# If they chose to uninstall, delete our options from the database, deactivate the plugin, and send them to the plugin page.
		if( $uninstall == true ) {
			delete_option( 'dealertrend_inventory_api' );
			delete_option( 'vehicle_management_system' );
			deactivate_plugins( $dealertrend_inventory_api->meta_information[ 'PluginBaseName' ] );
			echo '<script type="text/javascript">window.location.replace("/wp-admin/plugins.php");</script>';
			exit;
		}

		# Did they want to save the data?
		if( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'update' ) {
			# Remove trailing slashes as well as https.
			# This plugin should never need https and trailing slashes are ugly. ^_^
			if( isset( $_POST[ 'vehicle_management_system' ] ) ) {
				$_POST[ 'vehicle_management_system' ][ 'host' ] = preg_replace( '/^(https?:\/\/)?(.+)([^a-z])$/i' , '$2' , $_POST[ 'vehicle_management_system' ][ 'host' ] );
				$dealertrend_inventory_api->options[ 'vehicle_management_system' ][ 'host' ] = $_POST[ 'vehicle_management_system' ][ 'host' ];
				$dealertrend_inventory_api->options[ 'vehicle_management_system' ][ 'company_information' ] = $_POST[ 'vehicle_management_system' ][ 'company_information' ];
			} elseif( isset( $_POST[ 'theme' ] ) ) {
				$dealertrend_inventory_api->options[ 'vehicle_management_system' ][ 'theme' ][ 'name' ] = $_POST[ 'theme' ];
				$dealertrend_inventory_api->options[ 'vehicle_management_system' ][ 'theme' ][ 'per_page' ] = $_POST[ 'per_page' ];
			}
			$dealertrend_inventory_api->save_options();
		}

	}

	$inventory_link = !empty( $wp_rewrite->rules ) ? '/inventory/' : '?taxonomy=inventory';
	$site_link = '<span style="white-space:nowrap;"><a href="http://www.dealertrend.com" target="_blank" title="DealerTrend, Inc: Shift Everything">DealerTrend, Inc.</a></span>';

	$vehicle_management_system = new vehicle_management_system(
		$this->options[ 'vehicle_management_system' ][ 'host' ],
		$this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ]
	);

	?>

<div id="uninstall-dialog" title="Confirm Uninstall" style="display:none;">
	<p>Are you sure you want to do this?.</p>
	<p>Click 'Proceed' below to <strong>permanently</strong> delete all current plugin data and deactivate the plugin.</p>
	<p>The plugin will revert back to its <strong>default settings</strong> upon reactivation.</p>
	<form name="confirm-uninstall" action="" method="post">
		<?php wp_nonce_field( 'dealertrend_inventory_api' ); ?>
		<button id="uninstall-yes" name="uninstall[]" value="true">Proceed</button>
		<button id="uninstall-cancel" name="uninstall[]" value="false">Cancel</button>
	</form>
</div>

<div id="icon-dealertrend" class="icon32"><br /></div>
<h2><?php echo $this->meta_information[ 'Name' ]; ?> Settings</h2>
<?php flush(); ?>
<div id="option-tabs" style="clear:both;">
	<ul>
		<li><a href="#feeds">Feeds</a></li>
		<li><a href="#themes">Themes</a></li>
		<li><a href="#settings">Settings</a></li>
		<li><a href="#help">Help</a></li>
	</ul>
<?php flush(); ?>
	<div id="feeds">
		<table width="450">
			<tr>
				<td colspan="2"><h3 class="title">Inventory Feed</h3></td>
			</tr>
			<tr>
				<td width="125">Feed Status:</td>
				<td>
					<?php
						$start = timer_stop();
						$check_inventory = $vehicle_management_system->check_inventory();
						$stop = timer_stop();
						if( $check_inventory[ 'status' ] == true ) {
							echo '<span class="success">Loaded</span>';
						} else {
							echo '<span class="fail">Unavailable</span>';
							echo '<br/><small>Returned Message: ' . $check_inventory[ 'data' ][ 'message' ] . '</small>';
						}
					?>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><small>Response time:<?php echo $stop - $start; ?> seconds</small></td>
			</tr>
		</table>
		<table width="450">
			<tr>
				<td colspan="2"><h3 class="title">Company Information Feed</h3></td>
			</tr>
			<tr>
				<td width="125">Account Status:</td>
				<td>
					<?php
						$start = timer_stop();
						$check_company = $vehicle_management_system->check_company_id();
						$stop = timer_stop();
						if( $check_company[ 'status' ] == true ) {
							echo '<span class="success">Loaded</span>';
						} else {
							echo '<span class="fail">Unavailable</span>';
							echo '<br/><small>Returned Message: ' . $check_company[ 'data' ][ 'message' ] . '</small>';
						}
					?>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><small>Response time:<?php echo $stop - $start; ?> seconds</small></td>
			</tr>
			<?php if( $check_company[ 'status' ] == true ): ?>
			<?php
				$company_information = $vehicle_management_system->get_company_information();
				$company_information = $company_information[ 'data' ];
			?>
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
		</table>
	</div>
	<?php flush(); ?>
	<div id="themes">
		<form name="dealertrend_inventory_api_theme_settings" method="post" action="#feeds">
		<?php wp_nonce_field( 'dealertrend_inventory_api' ); ?>
			<table width="450">
				<tr>
					<td colspan="2"><h3 class="title">Inventory Theme Settings</h3></td>
				</tr>
				<tr>
					<td width="125">Current Theme:</td>
					<td><strong><?php echo ucwords( $dealertrend_inventory_api->options[ 'vehicle_management_system' ][ 'theme' ][ 'name' ] ); ?></strong></td>
				</tr>
				<tr>
					<td width="125">Change Theme:</td>
					<td>
						<select name="theme">
							<?php
								$themes = $dealertrend_inventory_api->get_themes( 'inventory' );
								foreach( $themes as $key => $value  ) {
									$selected = ( $value == $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'name' ] ) ? 'selected' : NULL;
									echo '<option ' . $selected . ' value="' . $value . '">' . $value .'</option>';
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td width="125">Vehicles Per Page:</td>
					<td>
						<select name="per_page">
							<?php
								for( $i = 1; $i <= 50; $i ++ ) {
									$selected = ( $i == $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'per_page' ] ) ? 'selected' : NULL;
									echo '<option ' . $selected . ' value="' . $i . '">'. $i .'</option>';
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<input type="hidden" name="action" value="update" />
						<p class="submit">
							<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
						</p>
					</td>
				</tr>
			</table>
		</form>
	</div>
	<?php flush(); ?>
	<div id="settings">
		<form name="dealertrend_inventory_api_api_settings" method="post" action="#feeds">
			<?php wp_nonce_field( 'dealertrend_inventory_api' ); ?>
			<table width="450">
				<tr>
					<td colspan="2">
						<h3 class="title">API Settings</h3>
					</td>
				</tr>
				<tr>
					<td width="200"><label for="vehicle-management-system">Vehicle Management System:</label></td>
					<td><input type="text" id="vehicle-management-system" name="vehicle_management_system[host]" value="<?php echo $dealertrend_inventory_api->options[ 'vehicle_management_system' ][ 'host' ] ?>" class="long_input" /></td>
				</tr>
				<tr>
					<td width="200"></td>
					<td><small>Inventory will not be available without providing a valid VMS from <?php echo $site_link; ?></small></td>
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
					<td>
						<input type="text" name="vehicle_management_system[company_information][id]" id="company-id" value="<?php echo $dealertrend_inventory_api->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] ?>" />
					</td>
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
	<?php flush(); ?>
	<div id="help">
		<h3 class="title">Initial Setup</h3>
		<p>To get the plugin started you'll need to specify a VMS and a valid Company ID.</p>
		<p>Both of these will be provided to you upon purchasing a license with <?php echo $site_link; ?></p>
		<p>After you've received a valid VMS and Company ID, you'll need to go to the <a id="settings-link" href="#settings" title="DealerTrend API Settings">settings page</a> and fill in their respective fields. Once you click "Save Changes" it will start pulling in your Inventory and Company Feeds.</p>
		<h3 class="title">Viewing Inventory</h3>
		<?php
			if( $check_inventory[ 'status' ] == false ) {
				echo '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Alert:</strong> Inventory is not working. Please check your settings.</p></div></div>';
			}
		?>
		<p>If the VMS and Company Feed are both loaded, you may view your inventory here: <a href="<?php bloginfo( 'url' ); echo $inventory_link; ?>" target="_blank"><?php bloginfo( 'url' ); echo $inventory_link; ?></a></p>
				</tr>
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
