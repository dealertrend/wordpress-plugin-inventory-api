<?php

namespace WordPress\Plugins\DealerTrend\InventoryAPI;

	global $dealertrend_inventory_api;
	global $wp_rewrite;

	if( $_POST ) {

		if( ! wp_verify_nonce( $_POST[ '_wpnonce' ], 'dealertrend_inventory_api' ) ) die( 'Security check failed.' );

		$uninstall = isset( $_POST[ 'uninstall' ][ 0 ] ) ? $_POST[ 'uninstall' ][ 0 ] : false;

		$_POST = array_map( array( &$dealertrend_inventory_api , 'sanitize_inputs' ) , $_POST );

		if( $uninstall == true ) {
			delete_option( 'dealertrend_inventory_api' );
			delete_option( 'vehicle_management_system' );
			delete_option( 'vehicle_reference_system' );
			deactivate_plugins( $dealertrend_inventory_api->plugin_information[ 'PluginBaseName' ] );
			echo '<script type="text/javascript">window.location.replace("/wp-admin/plugins.php");</script>';
			exit;
		}

		if( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'update' ) {
			if( isset( $_POST[ 'vehicle_reference_system' ][ 'makes' ] ) ) {
				$dealertrend_inventory_api->options[ 'vehicle_reference_system' ][ 'data' ][ 'makes' ] = isset( $_POST[ 'vehicle_reference_system' ][ 'makes' ] ) ? $_POST[ 'vehicle_reference_system' ][ 'makes' ] : array();
				$dealertrend_inventory_api->options[ 'vehicle_reference_system' ][ 'data' ][ 'models' ] = isset( $_POST[ 'vehicle_reference_system' ][ 'models' ] ) ? $_POST[ 'vehicle_reference_system' ][ 'models' ] : array();
			} elseif( isset( $_POST[ 'vehicle_management_system' ] ) || isset( $_POST[ 'vehicle_reference_system' ] ) ) {
				$dealertrend_inventory_api->options[ 'vehicle_management_system' ][ 'host' ] = isset( $_POST[ 'vehicle_management_system' ][ 'host' ] ) ? rtrim( $_POST[ 'vehicle_management_system' ][ 'host' ] , '/' ) : NULL;
				if(
					isset( $_POST[ 'vehicle_management_system' ][ 'company_information' ] ) && 
					isset( $_POST[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] ) &&
					$_POST[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] != NULL
				) {
					$dealertrend_inventory_api->options[ 'vehicle_management_system' ][ 'company_information' ] = $_POST[ 'vehicle_management_system' ][ 'company_information' ];
				}
				$dealertrend_inventory_api->options[ 'vehicle_reference_system' ][ 'host' ] = isset( $_POST[ 'vehicle_reference_system' ][ 'host' ] ) ? rtrim( $_POST[ 'vehicle_reference_system' ][ 'host' ] , '/' ) : NULL;
			} elseif( isset( $_POST[ 'theme' ] ) ) {
				$dealertrend_inventory_api->options[ 'vehicle_management_system' ][ 'theme' ][ 'name' ] = $_POST[ 'theme' ];
				$dealertrend_inventory_api->options[ 'vehicle_management_system' ][ 'theme' ][ 'per_page' ] = $_POST[ 'per_page' ];
				$dealertrend_inventory_api->options[ 'jquery' ][ 'ui' ][ 'theme' ] = isset( $_POST[ 'jquery' ][ 'ui' ][ 'theme' ] ) ? $_POST[ 'jquery' ][ 'ui' ][ 'theme' ] : 'black-tie';
				echo '<script type="text/javascript">window.location.replace("admin.php?page=dealertrend_inventory_api");</script>';
			}
			$dealertrend_inventory_api->save_options();
		}

	}

	$inventory_link = ! empty( $wp_rewrite->rules ) ? '/inventory/' : '?taxonomy=inventory';
	$site_link = '<span style="white-space:nowrap;"><a href="http://www.dealertrend.com" target="_blank" title="DealerTrend, Inc: Shift Everything">DealerTrend, Inc.</a></span>';

	$vehicle_management_system = new vehicle_management_system(
		$this->options[ 'vehicle_management_system' ][ 'host' ],
		$this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ]
	);

	$vehicle_reference_system = new vehicle_reference_system (
		$this->options[ 'vehicle_reference_system' ][ 'host' ]
	);

	$check_vms_host = $vehicle_management_system->check_host()->please();

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
<h2><?php echo $this->plugin_information[ 'Name' ]; ?></h2>
<?php flush(); ?>
<div id="option-tabs" style="clear:both; margin-right:20px;">
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
				<td colspan="2"><h3 class="title">Vehicle Management System Feed</h3></td>
			</tr>
			<tr>
				<td width="125">Feed Status:</td>
				<td>
					<?php
						if( ! empty( $this->options[ 'vehicle_management_system' ][ 'host' ] ) ) {
							$start = timer_stop();
							$check_inventory = $vehicle_management_system->check_inventory()->please();
							$stop = timer_stop();
							$response_code = isset( $check_inventory[ 'response' ][ 'code' ] ) ? $check_inventory[ 'response' ][ 'code' ] : false;
							if( $response_code == 200 ) {
								echo '<span class="success">Loaded</span>';
							} else {
								echo '<span class="fail">Unavailable</span>';
								echo '<br /><small>Returned Message: ' . $check_inventory[ 'message' ] . '</small>';
								if( $check_inventory[ 'message' ] == 'Forbidden' ) {
									echo '<br /><small style="color: red; font-weight:bold;">Check your <a id="settings-link" href="#settings" title="DealerTrend API Settings">Company ID</a>.';
									echo '<br />Your current Company ID is "' . $this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] . '" and appears to not exist or be suspended.</small>';
								}
							}
							$time = $stop - $start;
							echo '</td></tr><tr><td>&nbsp;</td><td><small>Response time: ' . $time . ' seconds</small></td>';
						} else {
							echo '<span class="fail">Not Configured</span>';
						}
					?>
			</tr>
		</table>
		<table width="450">
			<tr>
				<td colspan="2"><h3 class="title">Vehicle Reference System Feed</h3></td>
			</tr>
			<tr>
				<td width="125">Feed Status:</td>
				<td>
					<?php
						$response_code = false;
						if( ! empty( $this->options[ 'vehicle_reference_system' ][ 'host' ] ) ) {
							$start = timer_stop();
							$check_feed = $vehicle_reference_system->check_feed()->please();
							$response_code = isset( $check_feed[ 'response' ][ 'code' ] ) ? $check_feed[ 'response' ][ 'code' ] : false;
							$stop = timer_stop();
							if( $response_code == 200 ) {
								echo '<span class="success">Loaded</span>';
							} else {
								echo '<span class="fail">Unavailable</span>';
								echo '<br/><small>Returned Message: ' . $check_feed[ 'data' ][ 'message' ] . '</small>';
							}
							$time = $stop - $start;
							echo '</td></tr><tr><td>&nbsp;</td><td><small>Response time: ' . $time . ' seconds</small></td>';
						} else {
							echo '<span class="fail">Not Configured</span>';
						}
					?>
				</td>
			</tr>
		<table>
		<table width="450">
			<?php
				if( isset( $response_code ) && $response_code == 200 ) {
					echo '<form method="post" action="#">';
					$makes = isset( $this->options[ 'vehicle_reference_system' ][ 'data' ][ 'makes' ] ) ? $this->options[ 'vehicle_reference_system' ][ 'data' ][ 'makes' ] : array();
					$models = isset( $this->options[ 'vehicle_reference_system' ][ 'data' ][ 'models' ] ) ? $this->options[ 'vehicle_reference_system' ][ 'data' ][ 'models' ] : array();

					$current_year = date( 'Y' );
					$last_year = $current_year - 1;
					$next_year = $current_year + 1;

					$make_data[ $last_year ] = $vehicle_reference_system->get_makes()->please( array( 'year' => $last_year ) );
					$make_data[ $last_year ] = json_decode( $make_data[ $last_year ][ 'body' ] );
					$make_data[ $current_year ] = $vehicle_reference_system->get_makes()->please( array( 'year' => $current_year ) );
					$make_data[ $current_year ] = json_decode( $make_data[ $current_year ][ 'body' ] );
					$make_data[ $next_year ] = $vehicle_reference_system->get_makes()->please( array( 'year' => $next_year ) );
					$make_data[ $next_year ] = json_decode( $make_data[ $next_year ][ 'body' ] );

					$make_data = array_merge( $make_data[ $next_year ] , $make_data[ $current_year ] , $make_data[ $last_year ] );

					# It would be cool if there was a better way to do this.
					$i_can_haz_make = array();
					foreach( $make_data as $key => $value ) {
						$existing_data = array_search( $value->name , $i_can_haz_make );
						if( $existing_data == false ) {
							$i_can_haz_make[ $key ] = $value->name;
						} else {
							$make_data[ $existing_data ] = $value;
							unset( $make_data[ $key ] );
						}
					}

					$make_values = $make_data;

					echo '<tr>';
					echo '<td colspan="2"><h3 class="title">Showcase</h3></td>';
					echo '</tr>';
					echo '<tr><td width="125"><label for="makes">Makes: </label></td>';
					echo '<td><select id="makes" name="vehicle_reference_system[makes][]" class="vrs-makes" size="4" multiple="multiple">';
					foreach( $make_values as $make ) {
						$selected = in_array( $make->name , $makes ) ? 'selected' : NULL;
						echo '<option value="' . $make->name . '" ' . $selected . '>' . $make->name . '</option>';
					}
					echo '</select></td></tr>';
					echo '<tr><td><input type="hidden" name="action" value="update" /></td></tr>';

					if( count( $makes ) > 0 ) {
						echo '<tr><td><label for="models">Models: </label></td>';
						echo '<td><select id="models" name="vehicle_reference_system[models][]" class="vrs-models" size="4" multiple="multiple">';
						foreach( $makes as $make ) {
							$model_data[ $last_year ] = $vehicle_reference_system->get_models()->please( array( 'make' => $make , 'year' => $last_year ) );
							$model_data[ $last_year ] = isset( $model_data[ $last_year ][ 'body' ] ) ? json_decode( $model_data[ $last_year ][ 'body' ] ) : NULL;
							$model_data[ $current_year ] = $vehicle_reference_system->get_models()->please( array( 'make' => $make , 'year' => $current_year ) );
							$model_data[ $current_year ] = isset( $model_data[ $current_year ][ 'body' ] ) ?json_decode( $model_data[ $current_year ][ 'body' ] ) : NULL;
							$model_data[ $next_year ] = $vehicle_reference_system->get_models()->please( array( 'make' => $make , 'year' => $next_year ) );
							$model_data[ $next_year ] = isset( $model_data[ $next_year ][ 'body' ] ) ? json_decode( $model_data[ $next_year ][ 'body' ] ) : NULL;

							$model_data[ $last_year ] = is_array( $model_data[ $last_year ] ) ? $model_data[ $last_year ] : array();
							$model_data[ $current_year ] = is_array( $model_data[ $current_year ] ) ? $model_data[ $current_year ] : array();
							$model_data[ $next_year ] = is_array( $model_data[ $next_year ] ) ? $model_data[ $next_year ] : array();

							$model_data = array_merge( $model_data[ $last_year ] , $model_data[ $current_year ] , $model_data[ $next_year ] );

							# It would be cool if there was a better way to do this.
							$i_can_haz_model = array();
							foreach( $model_data as $key => $value ) {
								$existing_data = array_search( $value->name , $i_can_haz_model );
								if( $existing_data == false ) {
									$i_can_haz_model[ $key ] = $value->name;
								} else {
									$model_data[ $existing_data ] = $value;
									unset( $model_data[ $key ] );
								}
							}
							$model_values = $model_data;
							echo '<optgroup label="' . $make . '">';
							foreach( $model_values as $model ) {
								$selected = in_array( $model->name , $models ) ? 'selected' : NULL;
								echo '<option value="' . $model->name . '" ' . $selected . '>' . $model->name . '</option>';
							}
							echo '</optgroup>';
						}
						echo '</select></td></tr>';
					}
					echo '<tr><td></td><td style="padding-left:175px;"><input type="submit" class="button-primary" value="Save"></td></tr>';
				}

				wp_nonce_field( 'dealertrend_inventory_api' );
				echo '</form>';
			?>
		</table>
		<table width="450">
			<tr>
				<td colspan="2"><h3 class="title">Company Information Feed</h3></td>
			</tr>
			<tr>
				<td width="125">Account Status:</td>
				<td>
					<?php
						if( $this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] != 0 ) {
							$response_code = isset( $check_vms_host[ 'response' ][ 'code' ] ) ? $check_vms_host[ 'response' ][ 'code' ] : false;
							if( $response_code == 200 ) {
								$start = timer_stop();
								$check_company = $vehicle_management_system->check_company_id()->please();
								$response_code = isset( $check_company[ 'response' ][ 'code' ] ) ? $check_company[ 'response' ][ 'code' ] : false;
								$stop = timer_stop();
								if( $response_code == 200 ) {
									echo '<span class="success">Loaded</span>';
									} else {
									echo '<span class="fail">Unavailable</span>';
									echo '<br/><small>Returned Message: ' . $check_company[ 'message' ] . '</small>';
								}
								$time = $stop - $start;
								echo '</td></tr><tr><td>&nbsp;</td><td><small>Response time: ' . $time . ' seconds</small></td>';
							} else {
								echo '<span class="fail">' . $check_vms_host[ 'message' ] . '</span>';
							}
						} else {
							echo '<span class="fail">Not Configured</span>';
						}
					?>
				</td>
			</tr>
			<?php if( isset( $check_company ) && $check_company[ 'response' ][ 'code' ] == 200 ): ?>
			<?php
				$company_information = $vehicle_management_system->get_company_information()->please();
				$company_information = json_decode( $company_information[ 'body' ] );
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
			<?php if( strlen( $company_information->fax ) > 0 ) { ?>
			<tr>
				<td>Fax:</td>
				<td><strong><?php echo $company_information->fax; ?></strong></td>
			</tr>
			<?php } ?>
			<tr>
				<td>Country Code:</td>
				<td><strong><?php echo $company_information->country_code; ?></strong></td>
			</tr>
			<?php
				$api_keys = isset( $company_information->api_keys ) ? $company_information->api_keys : array();
				if( count( $api_keys ) > 0 ) {
			?>
			<tr>
				<td>API Keys:</td>
				<td>
					<ol>
						<?php
							foreach( $api_keys as $key => $value ) {
								echo '<li>' . $key . ': ' . $value . '</li>';
							}
						?>
					</ol>
				</td>
			</tr>
			<?php } ?>
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
								foreach( $themes as $key => $value	) {
									$selected = ( $value == $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'name' ] ) ? 'selected' : NULL;
									echo '<option ' . $selected . ' value="' . $value . '">' . ucwords( $value ) .'</option>';
								}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td width="125"><label for="jquery-ui-theme">jQuery UI Theme:</a></td>
					<td>
						<select id="jquery-ui-theme" name="jquery[ui][theme]">
						<?php
							$theme_path = str_replace( 'views/options' , 'assets/jquery-ui/1.8.11/themes' , dirname( __FILE__ ) );
							if( $handle = opendir( $theme_path ) ) {
								$ignore = array( '.' , '..' );
								while( false !== ( $file = readdir( $handle ) ) ) {
									$selected = $dealertrend_inventory_api->options[ 'jquery' ][ 'ui' ][ 'theme' ] == $file ? 'selected' : NULL;
									if( ! in_array( $file , $ignore ) ) {
										$file_title = str_replace( '-' , ' ' , $file );
										echo '<option value="' . $file . '" ' . $selected . '>' . ucwords( $file_title ) . '</option>';
									}
								}
								closedir( $handle );
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
		<form name="dealertrend_inventory_api_plugin_settings" method="post" action="#feeds">
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
					<td width="200"><label for="vehicle-reference-system">Vehicle Reference System:</label></td>
					<td><input type="text" id="vehicle-reference-system" name="vehicle_reference_system[host]" value="<?php echo $dealertrend_inventory_api->options[ 'vehicle_reference_system' ][ 'host' ] ?>" class="long_input" /></td>
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
			</table>
			<table>
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
			if( isset( $check_inventory[ 'status' ] ) && $check_inventory[ 'status' ] == false ) {
				echo '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Alert:</strong> Inventory is not working. Please check your settings.</p></div></div>';
			}
		?>
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
