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
					<option value="2" <?php echo ( $value == 1 ) ? 'selected' : ''; ?> ><?php _e('Equipment'); ?></option>
				</select>
			</div>
		</div>

		<div id="gravity-settings-wrapper">
			<div><h3 class="title">Gravity Forms</h3></div>
			<div class="edit-table-button" name="gravity-form-table">Edit Form</div>
		</div>

	</div>

	<?php
		include( dirname( __FILE__ ) . '/gravity_forms_add_on.php' );
	?>
