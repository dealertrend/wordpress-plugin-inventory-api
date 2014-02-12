	<div class="loan-settings-wrapper">
		<h3 class="title">Loan Calculator Settings</h3>
		<div class="settings-group">
			<div class="settings-label">Display Calculator:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'loan' ][ 'display_calc' ]; ?>
				<input type="checkbox" id="loan-display-calc" name="custom_settings[loan][display_calc]" <?php echo ( !empty($value) ) ? ' checked ' : ''; ?> />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Default Interest Rate:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'loan' ][ 'default_interest' ]; ?>
				<input type="text" id="loan-default-interest" name="custom_settings[loan][default_interest]" value="<?php echo ( !empty($value) ) ? $value : '7.35'; ?>" />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Default Trade Value:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'loan' ][ 'default_trade' ]; ?>
				<input type="text" id="loan-default-trade" name="custom_settings[loan][default_trade]" value="<?php echo ( !empty($value) ) ? $value : '5000'; ?>" />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Default Term (months):</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'loan' ][ 'default_term' ]; ?>
				<input type="text" id="loan-default-term" name="custom_settings[loan][default_term]" value="<?php echo ( !empty($value) ) ? $value : '72'; ?>" />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Default Down Payment:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'loan' ][ 'default_down' ]; ?>
				<input type="text" id="loan-default-down" name="custom_settings[loan][default_down]" value="<?php echo ( !empty($value) ) ? $value : '3000'; ?>" />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Default Sales Tax:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'loan' ][ 'default_tax' ]; ?>
				<input type="text" id="loan-default-tax" name="custom_settings[loan][default_tax]" value="<?php echo ( !empty($value) ) ? $value : '8.00'; ?>" />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Display Monthly Cost:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'loan' ][ 'display_monthly' ]; ?>
				<input type="checkbox" id="loan-display-monthly" name="custom_settings[loan][display_monthly]" <?php echo ( !empty($value) ) ? ' checked ' : ''; ?> />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Display Bi-Monthly Cost:</div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'loan' ][ 'display_bi_monthly' ]; ?>
				<input type="checkbox" id="loan-display-bi-monthly" name="custom_settings[loan][display_bi_monthly]" <?php echo ( !empty($value) ) ? ' checked ' : ''; ?> />
			</div>
		</div>


	</div>
