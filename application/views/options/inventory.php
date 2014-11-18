<div id="inventory" class="settings-wrapper">
	<div class="form-save-wrapper">
		<div class="form-save-button" name="inventory-form">Save Inventory Settings</div>
	</div>
	<div id="inventory-tab-buttons">
		<div class="tab-button tab-button-inventory active" name="inventory">Inventory</div>
		<div class="tab-button tab-button-theme" name="theme">Theme</div>
		<div class="tab-button tab-button-loan" name="loan">Loan Calculator</div>
		<div class="tab-button tab-button-price" name="price">Price Text</div>
		<div class="tab-button tab-button-tags" name="tags">Tags</div>
		
	</div>
	<div id="inventory-content-wrapper">
		<form id="inventory-form" name="dealertrend_inventory_api_theme_settings_inventory" method="post" action="#inventory">
		<?php 
			wp_nonce_field( 'dealertrend_inventory_api' ); 
			$theme_name = ucwords( $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'name' ] );
		?>
			<div class="tab-content tab-content-inventory active">
				<input type="hidden" value="inventory" name="page" />
				<table width="450" class="table-left">
				<tr>
					<td colspan="2"><h3 class="title">Inventory Settings</h3></td>
				</tr>
				<tr>
					<td width="125">Current Theme:</td>
					<td><strong><?php echo $theme_name; ?></strong></td>
				</tr>
				<tr>
					<td width="125">Change Theme:</td>
					<td>
					<select name="theme">
						<?php
						$themes = $this->instance->get_themes( 'inventory' );
						foreach( $themes as $key => $value ) {
							$selected = ( $value == $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'name' ] ) ? 'selected' : NULL;
							echo '<option ' . $selected . ' value="' . $value . '">' . ucwords( $value ) .'</option>';
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
							$selected = ( $i == $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'per_page' ] ) ? 'selected' : NULL;
							echo '<option ' . $selected . ' value="' . $i . '">'. $i .'</option>';
						}
						?>
					</select>
					</td>
				</tr>
				<tr>
					<td width="125">Sale Class Filter:</td>
					<td>
					<select name="saleclass">
						<option value="all" <?php if ( $this->instance->options[ 'vehicle_management_system' ][ 'saleclass' ] == 'all' ) { echo 'selected'; } ?> ><?php _e('All'); ?></option>
						<option value="new" <?php if ( $this->instance->options[ 'vehicle_management_system' ][ 'saleclass' ] == 'new' ) { echo 'selected'; } ?> ><?php _e('New'); ?></option>
						<option value="used" <?php if ( $this->instance->options[ 'vehicle_management_system' ][ 'saleclass' ] == 'used' ) { echo 'selected'; } ?> ><?php _e('Used'); ?></option>
						<option value="certified" <?php if ( $this->instance->options[ 'vehicle_management_system' ][ 'saleclass' ] == 'certified' ) { echo 'selected'; } ?> ><?php _e('Certified'); ?></option>
					</select>
					</td>
				</tr>

				<?php
					$vms_makes = isset( $this->instance->options[ 'vehicle_management_system' ][ 'data' ][ 'makes_new' ] ) ? $this->instance->options[ 'vehicle_management_system' ][ 'data' ][ 'makes_new' ] : array();

					if( $this->vehicle_reference_system ){
						$make_data = $this->get_make_data();
						$make_values = $this->remove_data_dups( $make_data, 'name');
						natcasesort($make_values);
					} else {
						$makes_values = array();
					}
				

				?>
				<tr>
					<td width="125"><label for="vms-makes">New Make Filter: </label></td>
					<td>
						<select id="vms-makes" name="vehicle_management_system[makes_new][]" class="vms-makes" size="4" multiple="multiple">
							<?php $this->create_dd_options($make_values, $vms_makes); ?>
						</select>
					</td>
				</tr>

				<tr valign="top">
					<td width="125"><label for="show_standard_eq"><?php _e('Show Standard Equipment:') ?></label></td>
					<td>
						<input type="checkbox" id="show_standard_eq" name="show_standard_eq" <?php if ( $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'show_standard_eq' ] != '' ) { echo 'checked'; } ?> />
						<br />
					</td>
				</tr>
				<tr valign="top">
					<td width="125"><label for="inv_responsive"><?php _e('Remove Responsive:') ?></label></td>
					<td>
						<input type="checkbox" id="inv_responsive" name="inv_responsive" <?php if ( $this->instance->options[ 'vehicle_management_system' ][ 'inv_responsive' ] != '' ) { echo 'checked'; } ?> />
						<br />
					</td>
				</tr>
				<tr>
					<td colspan="2"><h4 class="title-divider">Company Override</h4></td>
				</tr>
				<tr>
				  <td width="200"><label for="phone_new">Phone # New:</label></td>
				  <td><input type="text" id="phone_new" name="phone_new" value="<?php echo $this->instance->options[ 'vehicle_management_system' ][ 'custom_contact' ][ 'phone_new' ] ?>" class="long_input" /></td>
				</tr>
				<tr>
				  <td width="200"><label for="phone_used">Phone # Used:</label></td>
				  <td><input type="text" id="phone_used" name="phone_used" value="<?php echo $this->instance->options[ 'vehicle_management_system' ][ 'custom_contact' ][ 'phone_used' ] ?>" class="long_input" /></td>
				</tr>
				<tr>
				  <td width="200"><label for="name_new">Name New:</label></td>
				  <td><input type="text" id="name_new" name="name_new" value="<?php echo $this->instance->options[ 'vehicle_management_system' ][ 'custom_contact' ][ 'name_new' ] ?>" class="long_input" /></td>
				</tr>
				<tr>
				  <td width="200"><label for="name_used">Name Used:</label></td>
				  <td><input type="text" id="name_used" name="name_used" value="<?php echo $this->instance->options[ 'vehicle_management_system' ][ 'custom_contact' ][ 'name_used' ] ?>" class="long_input" /></td>
				</tr>
				</table>

			</div>

			<div class="tab-content tab-content-theme">
				<?php
					switch($theme_name){
						case 'Armadillo':
							include( dirname( __FILE__ ) . '/themes/armadillo.php' );
							break;
						case 'Bobcat':
							include( dirname( __FILE__ ) . '/themes/bobcat.php' );
							break;
						case 'Cobra':
							include( dirname( __FILE__ ) . '/themes/cobra.php' );
							break;
						case 'Dolphin':
							include( dirname( __FILE__ ) . '/themes/dolphin.php' );
							break;
						case 'Eagle':
							include( dirname( __FILE__ ) . '/themes/eagle.php' );
							break;
						default:
							echo 'Theme not found.';
					}
				?>
			</div>

			<div class="tab-content tab-content-loan">
				<?php
					include( dirname( __FILE__ ) . '/loan_calc.php' );
				?>
			</div>
		
			<div class="tab-content tab-content-price">
				<?php
					include( dirname( __FILE__ ) . '/price_text.php' );
				?>
			</div>
		
			<div class="tab-content tab-content-tags">
				<?php
					include( dirname( __FILE__ ) . '/tags.php' );
				?>
			</div>


			<input type="hidden" name="action" value="update" />
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
			</p>
		</form>
	</div>
</div>
