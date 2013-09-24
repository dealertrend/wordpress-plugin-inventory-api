<div id="inventory">
	<form name="dealertrend_inventory_api_theme_settings_inventory" method="post" action="#inventory">
	<?php
		wp_nonce_field( 'dealertrend_inventory_api' );
		$theme_name = ucwords( $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'name' ] );
	?>
		<table width="450">
		<tr>
			<td colspan="2"><h3 class="title">Inventory Theme Settings</h3></td>
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
			<td width="125"><label for="jquery-ui-theme-inventory">jQuery UI Theme:</a></td>
			<td>
			<select id="jquery-ui-theme-inventory" name="jquery[ui][theme][inventory]">
			<?php
				$theme_path = str_replace( 'views/options' , 'assets/jquery-ui/1.8.11/themes' , dirname( __FILE__ ) );
				if( $handle = opendir( $theme_path ) ) {
				$ignore = array( '.' , '..' );
				while( false !== ( $file = readdir( $handle ) ) ) {
					$selected = $this->instance->options[ 'jquery' ][ 'ui' ][ 'inventory-theme' ] == $file ? 'selected' : NULL;
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

			$make_data = $this->get_make_data();
			$make_values = $this->remove_data_dups( $make_data, 'name');
			natcasesort($make_values);
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
			<td width="125"><label for="inv_responsive"><?php _e('Remove Responsive:') ?></label></td>
			<td>
				<input type="checkbox" id="inv_responsive" name="inv_responsive" <?php if ( $this->instance->options[ 'vehicle_management_system' ][ 'inv_responsive' ] != '' ) { echo 'checked'; } ?> />
				<br />
			</td>
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
		<tr>
		<?php
			if( $theme_name == 'Eagle' ){
		?>
			<tr>
				<td colspan="2"><h3 class="title">Eagle Theme Settings</h3></td>
			</tr>
			<tr>
				<td width="125" class="eagle-border-bottom">Display Tags:</td>
				<td>
					<input type="checkbox" id="eagle-display-tags" name="custom_settings[eagle][display_tags]" <?php echo ( !empty($this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'eagle' ][ 'display_tags' ]) ) ? ' checked ' : ''; ?> />
				</td>
			</tr>
			<tr>
				<td width="125" class="eagle-border-bottom">Display Vehicle Headlines:</td>
				<td>
					<input type="checkbox" id="eagle-display-headlines" name="custom_settings[eagle][display_headlines]" <?php echo ( !empty($this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'eagle' ][ 'display_headlines' ]) ) ? ' checked ' : ''; ?> />
				</td>
			</tr>
			<tr>
				<td width="125" class="eagle-border-bottom">Remove Default Sub Headline <small>(Detail Page)</small>:</td>
				<td>
					<input type="checkbox" id="eagle-remove-sub-headline-d" name="custom_settings[eagle][remove_sub_headline_d]" <?php echo ( !empty($this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'eagle' ][ 'remove_sub_headline_d' ]) ) ? ' checked ' : ''; ?> />
				</td>
			</tr>
			<tr>
				<td width="125" class="eagle-border-bottom">Display Vehicle Location in Sub Headline <small>(Detail Page)</small>:</td>
				<td>
					<input type="checkbox" id="eagle-display-vehicle-location-sub-headline-d" name="custom_settings[eagle][display_vehicle_location_sub_headline_d]" <?php echo ( !empty($this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'eagle' ][ 'display_vehicle_location_sub_headline_d' ]) ) ? ' checked ' : ''; ?> />
				</td>
			</tr>
			<tr>
				<td width="125" class="eagle-border-bottom">Display Dealer Name in Sidebar <small>(Detail Page)</small>:</td>
				<td>
					<input type="checkbox" id="eagle-display-dealer-name-sidebar-d" name="custom_settings[eagle][display_dealer_name_sidebar_d]" <?php echo ( !empty($this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'eagle' ][ 'display_dealer_name_sidebar_d' ]) ) ? ' checked ' : ''; ?> />
				</td>
			</tr>
			<tr>
				<td width="125" class="eagle-border-bottom">Display Vehicle Location in Sidebar <small>(Detail Page)</small>:</td>
				<td>
					<input type="checkbox" id="eagle-display-vehicle-location-sidebar-d" name="custom_settings[eagle][display_vehicle_location_sidebar_d]" <?php echo ( !empty($this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'eagle' ][ 'display_vehicle_location_sidebar_d' ]) ) ? ' checked ' : ''; ?> />
				</td>
			</tr>
		<?php
			}
		?>
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
