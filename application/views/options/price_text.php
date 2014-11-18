	<div class="price-settings-wrapper">
		<h3 class="title">Set Theme Price Text</h3>
		<h4 class="title-divider">New</h4>
		<div class="settings-group">
			<div class="settings-label">Price: </div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'price' ]['new'][ 'standard_price' ]; ?>
				<input type="text" id="price-standard-price" name="custom_settings[price][new][standard_price]" value="<?php echo ( !empty($value) ) ? $value : ''; ?>" />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Compare At: </div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'price' ]['new'][ 'compare_price' ]; ?>
				<input type="text" id="price-compare-price" name="custom_settings[price][new][compare_price]" value="<?php echo ( !empty($value) ) ? $value : ''; ?>" />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Sale Price: </div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'price' ]['new'][ 'sale_price' ]; ?>
				<input type="text" id="price-sale-price" name="custom_settings[price][new][sale_price]" value="<?php echo ( !empty($value) ) ? $value : ''; ?>" />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Call for Price: </div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'price' ]['new'][ 'default_price' ]; ?>
				<input type="text" id="price-default-price" name="custom_settings[price][new][default_price]" value="<?php echo ( !empty($value) ) ? $value : ''; ?>" />
			</div>
		</div>
		
		<h4 class="title-divider">Used</h4>
		<div class="settings-group">
			<div class="settings-label">Price: </div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'price' ]['used'][ 'standard_price' ]; ?>
				<input type="text" id="price-standard-price" name="custom_settings[price][used][standard_price]" value="<?php echo ( !empty($value) ) ? $value : ''; ?>" />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Compare At: </div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'price' ]['used'][ 'compare_price' ]; ?>
				<input type="text" id="price-compare-price" name="custom_settings[price][used][compare_price]" value="<?php echo ( !empty($value) ) ? $value : ''; ?>" />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Sale Price: </div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'price' ]['used'][ 'sale_price' ]; ?>
				<input type="text" id="price-sale-price" name="custom_settings[price][used][sale_price]" value="<?php echo ( !empty($value) ) ? $value : ''; ?>" />
			</div>
		</div>

		<div class="settings-group">
			<div class="settings-label">Call for Price: </div>
			<div class="settings-input">
				<?php $value = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ 'price' ]['used'][ 'default_price' ]; ?>
				<input type="text" id="price-default-price" name="custom_settings[price][used][default_price]" value="<?php echo ( !empty($value) ) ? $value : ''; ?>" />
			</div>
		</div>

	</div>
