	<h3 class="title">Eagle Theme Settings</h3>

	<div id="theme-settings-wrapper">
		
		<div class="settings-group">
			<div class="settings-label">Display Tags:</div>
			<div class="settings-input">
				<input type="checkbox" id="eagle-display-tags" name="custom_settings[Eagle][display_tags]" <?php echo ( !empty($this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Eagle' ][ 'display_tags' ]) ) ? ' checked ' : ''; ?> />
			</div>
		</div>
		
		<div class="settings-group">
			<div class="settings-label">Display Geo Search:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Eagle' ][ 'display_geo' ]; ?>
				<input type="checkbox" id="Eagle-display-geo" name="custom_settings[Eagle][display_geo]" <?php echo ( !empty($value) ) ? ' checked ' : ''; ?> />
			</div>
		</div>
		
		<div class="settings-group">
			<div class="settings-label">Add Geo Zip to Search:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Eagle' ][ 'add_geo_zip' ]; ?>
				<input type="checkbox" id="Eagle-geo-zip" name="custom_settings[Eagle][add_geo_zip]" <?php echo ( !empty($value) ) ? ' checked ' : ''; ?> />
			</div>
		</div>
		
		<div class="settings-group">
			<div class="settings-label">Display Similar Vehicles:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Eagle' ][ 'display_similar' ]; ?>
				<input type="checkbox" id="Eagle-display-similar" name="custom_settings[Eagle][display_similar]" <?php echo ( !empty($value) ) ? ' checked ' : ''; ?> />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Default Photo/Video:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Eagle' ][ 'default_image' ]; ?>
				<select id="Eagle-default-image" name="custom_settings[Eagle][default_image]">
					<option value="0" <?php echo ( !isset($value) || empty($value) ) ? 'selected' : ''; ?> ><?php _e('Photo'); ?></option>
					<option value="1" <?php echo ( $value == 1 ) ? 'selected' : ''; ?> ><?php _e('Video'); ?></option>
				</select>
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Default Vehicle Info:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Eagle' ][ 'default_info' ]; ?>
				<select id="Eagle-default-info" name="custom_settings[Eagle][default_info]">
					<option value="0" <?php echo ( !isset($value) || empty($value) ) ? 'selected' : ''; ?> ><?php _e('Options'); ?></option>
					<option value="1" <?php echo ( $value == 1 ) ? 'selected' : ''; ?> ><?php _e('Description'); ?></option>
				</select>
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">List Info Button:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Eagle' ][ 'list_info_button' ]; ?>
				<input type="text" class="Eagle-list-button-text" name="custom_settings[Eagle][list_info_button]" value="<?php echo $value; ?>" />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Display Vehicle Headlines:</div>
			<div class="settings-input">
				<input type="checkbox" id="eagle-display-headlines" name="custom_settings[Eagle][display_headlines]" <?php echo ( !empty($this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Eagle' ][ 'display_headlines' ]) ) ? ' checked ' : ''; ?> />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Remove Default Sub Headline <small>(Detail Page)</small>:</div>
			<div class="settings-input">
				<input type="checkbox" id="eagle-remove-sub-headline-d" name="custom_settings[Eagle][remove_sub_headline_d]" <?php echo ( !empty($this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Eagle' ][ 'remove_sub_headline_d' ]) ) ? ' checked ' : ''; ?> />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Display Location in Sub Headline <br/> <small>(Detail Page)</small>:</div>
			<div class="settings-input">
				<input type="checkbox" id="eagle-display-vehicle-location-sub-headline-d" name="custom_settings[Eagle][display_vehicle_location_sub_headline_d]" <?php echo ( !empty($this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Eagle' ][ 'display_vehicle_location_sub_headline_d' ]) ) ? ' checked ' : ''; ?> />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Display Dealer Name in Sidebar <small>(Detail Page)</small>:</div>
			<div class="settings-input">
				<input type="checkbox" id="eagle-display-dealer-name-sidebar-d" name="custom_settings[Eagle][display_dealer_name_sidebar_d]" <?php echo ( !empty($this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Eagle' ][ 'display_dealer_name_sidebar_d' ]) ) ? ' checked ' : ''; ?> />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Display Location in Sidebar <br/> <small>(Detail Page)</small>:</div>
			<div class="settings-input">
				<input type="checkbox" id="eagle-display-vehicle-location-sidebar-d" name="custom_settings[Eagle][display_vehicle_location_sidebar_d]" <?php echo ( !empty($this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Eagle' ][ 'display_vehicle_location_sidebar_d' ]) ) ? ' checked ' : ''; ?> />
			</div>
		</div>
		
		<div class="settings-group">
			<div class="settings-label">Detail Form ID: <span id="detail-default-form-id-help" class="get-help">?</span></div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'Eagle' ][ 'detail_gform_id' ]; ?>
				<input type="text" id="Eagle-detail-gform-id" class="input-short-value" name="custom_settings[Eagle][detail_gform_id]" pattern= "[0-9]" value="<?php echo $value; ?>" />
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
