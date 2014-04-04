	<div class="theme-settings-wrapper">
		<h3 class="title">Bobcat Theme Settings</h3>
		<div class="settings-group">
			<div class="settings-label">Display Tags:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Bobcat' ][ 'display_tags' ]; ?>
				<input type="checkbox" id="Bobcat-display-tags" name="custom_settings[Bobcat][display_tags]" <?php echo ( !empty($value) ) ? ' checked ' : ''; ?> />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Display Similar Vehicles:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Bobcat' ][ 'display_similar' ]; ?>
				<input type="checkbox" id="Bobcat-display-similar" name="custom_settings[Bobcat][display_similar]" <?php echo ( !empty($value) ) ? ' checked ' : ''; ?> />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Default Photo/Video:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Bobcat' ][ 'default_image' ]; ?>
				<select id="Bobcat-default-image" name="custom_settings[Bobcat][default_image]">
					<option value="0" <?php echo ( !isset($value) || empty($value) ) ? 'selected' : ''; ?> ><?php _e('Photo'); ?></option>
					<option value="1" <?php echo ( $value == 1 ) ? 'selected' : ''; ?> ><?php _e('Video'); ?></option>
				</select>
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Default Vehicle Info:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Bobcat' ][ 'default_info' ]; ?>
				<select id="Bobcat-default-info" name="custom_settings[Bobcat][default_info]">
					<option value="0" <?php echo ( !isset($value) || empty($value) ) ? 'selected' : ''; ?> ><?php _e('Options'); ?></option>
					<option value="1" <?php echo ( $value == 1 ) ? 'selected' : ''; ?> ><?php _e('Description'); ?></option>
					<option value="2" <?php echo ( $value == 2 ) ? 'selected' : ''; ?> ><?php _e('Equipment'); ?></option>
				</select>
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">List Price Button:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Bobcat' ][ 'list_price_button' ]; ?>
				<input type="text" class="Bobcat-list-button-text" name="custom_settings[Bobcat][list_price_button]" value="<?php echo $value; ?>" />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">List Info Button:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Bobcat' ][ 'list_info_button' ]; ?>
				<input type="text" class="Bobcat-list-button-text" name="custom_settings[Bobcat][list_info_button]" value="<?php echo $value; ?>" />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">List Form ID: <span id="list-form-id-help" class="get-help">?</span></div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Bobcat' ][ 'list_gform_id' ]; ?>
				<input type="text" id="Bobcat-list-gform-id" name="custom_settings[Bobcat][list_gform_id]" pattern= "[0-9]" value="<?php echo $value; ?>" />
			</div>
		</div>

		<div id="gravity-settings-wrapper">
			<div><h3 class="title">Gravity Forms</h3> <span id="detail-form-id-help" class="get-help">?</span></div>
			<div class="edit-table-button" name="gravity-form-table">Edit Form</div>
		</div>

	</div>

	<?php
		include( dirname( __FILE__ ) . '/gravity_forms_add_on.php' );
	?>

	<div id="hidden-help-wrapper" >
		<div class="list-form-id-help" style="text-align: center;">
			To pass along vehicle data for the list form, add a <br><strong>"Paragraph Text"</strong> field to the form. <br><br> After the field has been added, click on <strong>"Advanced"</strong> tab and in the <strong>"CSS Class Name"</strong> field add, <strong>"vehicle-info-form-data"</strong>. <br><br> This will allow Bobcat to pass vehicle data to the form and lock the field from being edited.
<br><br><hr><br><br>
			To pass along dealer ID for the list form, add a <br><strong>"Single Line Text"</strong> field to the form. <br><br> After the field has been added, click on <strong>"Advanced"</strong> tab and in the <strong>"CSS Class Name"</strong> field add, <strong>"vehicle-info-form-data-dealer"</strong>. <br><br> This will allow Bobcat to pass dealer ID to the form and lock/hide the field from being edited. The <strong>"Field Label"</strong> will still be visible, so this may want to be left blank on the form side.
		</div>

		<div class="detail-form-id-help" style="text-align: center;">
			To pass along vehicle data for detail forms, after adding a field to the form, click <strong>"Advanced"</strong>, then check <strong>"Allow field to be populated dynamically"</strong>.<br>In the <strong>"Parameter Name"</strong> field, add any of the following to dynamically populate field with data.<br><ul><li>dt_stock_number</li><li>dt_vin</li><li>dt_year</li><li>dt_make</li><li>dt_model</li><li>dt_trim</li><li>dt_saleclass</li><li>dt_exterior</li><li>dt_interior</li><li>dt_mileage</li><li>dt_price</li><li>dt_dealer</li><li>dt_dealer_id</li><li>dt_location</li><li>dt_phone</li></ul>
		</div>
	</div>
