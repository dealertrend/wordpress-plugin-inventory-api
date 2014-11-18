	<div class="theme-settings-wrapper">
		<h3 class="title">Dolphin Theme Settings</h3>
		
		<div class="settings-group">
			<div class="settings-label">Display Tags:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Dolphin' ][ 'display_tags' ]; ?>
				<input type="checkbox" id="Dolphin-display-tags" name="custom_settings[Dolphin][display_tags]" <?php echo ( !empty($value) ) ? ' checked ' : ''; ?> />
			</div>
		</div>
		
		<div class="settings-group">
			<div class="settings-label">Display Geo Search:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Dolphin' ][ 'display_geo' ]; ?>
				<input type="checkbox" id="Dolphin-display-geo" name="custom_settings[Dolphin][display_geo]" <?php echo ( !empty($value) ) ? ' checked ' : ''; ?> />
			</div>
		</div>
		
		<div class="settings-group">
			<div class="settings-label">Add Geo Zip to Search:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Dolphin' ][ 'add_geo_zip' ]; ?>
				<input type="checkbox" id="Dolphin-geo-zip" name="custom_settings[Dolphin][add_geo_zip]" <?php echo ( !empty($value) ) ? ' checked ' : ''; ?> />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Display Similar Vehicles:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Dolphin' ][ 'display_similar' ]; ?>
				<input type="checkbox" id="Dolphin-display-similar" name="custom_settings[Dolphin][display_similar]" <?php echo ( !empty($value) ) ? ' checked ' : ''; ?> />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Default Photo/Video:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Dolphin' ][ 'default_image' ]; ?>
				<select id="Dolphin-default-image" name="custom_settings[Dolphin][default_image]">
					<option value="0" <?php echo ( !isset($value) || empty($value) ) ? 'selected' : ''; ?> ><?php _e('Photo'); ?></option>
					<option value="1" <?php echo ( $value == 1 ) ? 'selected' : ''; ?> ><?php _e('Video'); ?></option>
				</select>
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Default Vehicle Info:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Dolphin' ][ 'default_info' ]; ?>
				<select id="Dolphin-default-info" name="custom_settings[Dolphin][default_info]">
					<option value="0" <?php echo ( !isset($value) || empty($value) ) ? 'selected' : ''; ?> ><?php _e('Options'); ?></option>
					<option value="1" <?php echo ( $value == 1 ) ? 'selected' : ''; ?> ><?php _e('Description'); ?></option>
				</select>
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">List Info Button:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Dolphin' ][ 'list_info_button' ]; ?>
				<input type="text" class="Dolphin-list-button-text" name="custom_settings[Dolphin][list_info_button]" value="<?php echo $value; ?>" />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Detail Form ID: <span id="detail-default-form-id-help" class="get-help">?</span></div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Dolphin' ][ 'detail_gform_id' ]; ?>
				<input type="text" id="Dolphin-detail-gform-id" class="input-short-value" name="custom_settings[Dolphin][detail_gform_id]" pattern= "[0-9]" value="<?php echo $value; ?>" />
			</div>
		</div>



	</div>

	<?php
		include( dirname( __FILE__ ) . '/gravity_forms_add_on.php' );
	?>

	<div id="hidden-help-wrapper" >
		<div class="detail-default-form-id-help" style="text-align: center;">
			To display a default gravity form on the detail page, enter the form ID.
			<br><br>
			To pass along vehicle data for detail forms, after adding a field to the form, click <strong>"Advanced"</strong>, then check <strong>"Allow field to be populated dynamically"</strong>.<br>In the <strong>"Parameter Name"</strong> field, add any of the following to dynamically populate field with data.<br><ul><li>dt_stock_number</li><li>dt_vin</li><li>dt_year</li><li>dt_make</li><li>dt_model</li><li>dt_trim</li><li>dt_saleclass</li><li>dt_exterior</li><li>dt_interior</li><li>dt_mileage</li><li>dt_price</li><li>dt_dealer</li><li>dt_dealer_id</li><li>dt_location</li><li>dt_phone</li></ul>
		</div>

		<div class="detail-form-id-help" style="text-align: center;">
			To pass along vehicle data for detail forms, after adding a field to the form, click <strong>"Advanced"</strong>, then check <strong>"Allow field to be populated dynamically"</strong>.<br>In the <strong>"Parameter Name"</strong> field, add any of the following to dynamically populate field with data.<br><ul><li>dt_stock_number</li><li>dt_vin</li><li>dt_year</li><li>dt_make</li><li>dt_model</li><li>dt_trim</li><li>dt_saleclass</li><li>dt_exterior</li><li>dt_interior</li><li>dt_mileage</li><li>dt_price</li><li>dt_dealer</li><li>dt_dealer_id</li><li>dt_location</li><li>dt_phone</li></ul>
		</div>
	</div>
