	<div class="theme-settings-wrapper">
		<h3 class="title">Cobra Theme Settings</h3>
		<div class="settings-group">
			<div class="settings-label">Display Tags:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Cobra' ][ 'display_tags' ]; ?>
				<input type="checkbox" id="cobra-display-tags" name="custom_settings[Cobra][display_tags]" <?php echo ( !empty($value) ) ? ' checked ' : ''; ?> />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Display Similar Vehicles:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Cobra' ][ 'display_similar' ]; ?>
				<input type="checkbox" id="cobra-display-similar" name="custom_settings[Cobra][display_similar]" <?php echo ( !empty($value) ) ? ' checked ' : ''; ?> />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Default Photo/Video:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Cobra' ][ 'default_image' ]; ?>
				<select id="cobra-default-image" name="custom_settings[Cobra][default_image]">
					<option value="0" <?php echo ( !isset($value) || empty($value) ) ? 'selected' : ''; ?> ><?php _e('Photo'); ?></option>
					<option value="1" <?php echo ( $value == 1 ) ? 'selected' : ''; ?> ><?php _e('Video'); ?></option>
				</select>
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Default Vehicle Info:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Cobra' ][ 'default_info' ]; ?>
				<select id="cobra-default-info" name="custom_settings[Cobra][default_info]">
					<option value="0" <?php echo ( !isset($value) || empty($value) ) ? 'selected' : ''; ?> ><?php _e('Options'); ?></option>
					<option value="1" <?php echo ( $value == 1 ) ? 'selected' : ''; ?> ><?php _e('Description'); ?></option>
					<option value="2" <?php echo ( $value == 2 ) ? 'selected' : ''; ?> ><?php _e('Equipment'); ?></option>
				</select>
			</div>
		</div>

		<div id="gravity-settings-wrapper">
			<div><h3 class="title">Gravity Forms</h3><span id="detail-form-id-help" class="get-help">?</span></div>
			<div class="edit-table-button" name="gravity-form-table">Edit Form</div>
		</div>

	</div>

	<?php
		include( dirname( __FILE__ ) . '/gravity_forms_add_on.php' );
	?>

	<div id="hidden-help-wrapper" >
		<div class="detail-form-id-help" style="text-align: center;">
			To pass along vehicle data for detail forms, after adding a field to the form, click <strong>"Advanced"</strong>, then check <strong>"Allow field to be populated dynamically"</strong>.<br>In the <strong>"Parameter Name"</strong> field, add any of the following to dynamically populate field with data.<br><ul><li>dt_stock_number</li><li>dt_vin</li><li>dt_year</li><li>dt_make</li><li>dt_model</li><li>dt_trim</li><li>dt_saleclass</li><li>dt_exterior</li><li>dt_interior</li><li>dt_mileage</li><li>dt_price</li><li>dt_dealer</li><li>dt_dealer_id</li><li>dt_location</li><li>dt_phone</li></ul>
		</div>
	</div>
