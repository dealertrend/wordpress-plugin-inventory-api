	<div id="gravity-form-table" class="settings-table-wrapper">
		<div id="tag-top-wrapper" class="settings-table-top-wrapper">
			<?php
				$this_value = !isset($this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ $theme_name ]['gravity_form'][ 'counter' ]) ? '0' : $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ $theme_name ]['gravity_form'][ 'counter' ];
				$this_array = $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ][ $theme_name ]['gravity_form'][ 'data' ];
			?>
			<h3 class="title">Gravity Forms <span id="detail-form-id-help" class="get-help">?</span></h3>
			<div id="tag-add-wrapper" class="settings-table-add-wrapper">
				<input id="inventory-form-counter" type="hidden" name="custom_settings[<?php echo $theme_name; ?>][gravity_form][counter]" value="<?php echo $this_value; ?>" readonly />
				<input id="inventory-form-theme" type="hidden" name="inventory_form_theme" value="<?php echo $theme_name; ?>" readonly />
				<div id="inventory-add-form" class="table-add-button">Add Form</div>
			</div>
		</div>

		<div id="gf-table-headers" class="table-headers">
			<div class="form-id">ID</div>
			<div class="form-button">Button</div>
			<div class="form-title">Button Title</div>
			<div class="form-saleclass">Sale Class</div>
			<div class="form-remove">Remove</div>
		</div>

		<div id="inventory-form-tr">
			<div id="inventory-form-td">
				<?php
					if( !empty($this_array) ){
						foreach( $this_array as $key => $value){
							$id_value = 'inventory-form-'.$key;
							echo '<div class="inventory-form-wrapper ' . $id_value . '">';

							echo '<div class="form-id inventory-form-value ' . $id_value . '">';
							echo '<input type="number" name="custom_settings['.$theme_name.'][gravity_form][data][' . $key . '][id]" id="inventory-form-' . $key . '-id" class="inventory-form-number ' . $id_value . '" value="' . $value['id'] . '" />';
							echo '</div>';

							echo '<div class="form-button inventory-form-value ' . $id_value . '">';
							echo '<input type="checkbox" name="custom_settings['.$theme_name.'][gravity_form][data][' . $key . '][button]" id="inventory-form-' . $key . '-button" class="inventory-form-checkbox ' . $id_value . '" '.( $value['button'] ? 'checked' : '' ).' />';
							echo '</div>';

							echo '<div class="form-title inventory-form-value ' . $id_value . '">';
							echo '<input type="text" name="custom_settings['.$theme_name.'][gravity_form][data][' . $key . '][title]" id="inventory-form-' . $key . '-title" class="inventory-form-text ' . $id_value . '" value="' . $value['title'] . '" />';
							echo '</div>';

							echo '<div class="form-saleclass inventory-form-value ' . $id_value . '">';
							echo '<select name="custom_settings['.$theme_name.'][gravity_form][data][' . $key . '][saleclass]" id="inventory-form-' . $key . '-saleclass" class="inventory-form-number ' . $id_value . '" >';
								echo '<option value="0" '.( $value['saleclass'] == '0' ? 'selected' : '' ).'>All</option>';
								echo '<option value="1" '.( $value['saleclass'] == '1' ? 'selected' : '' ).'>New</option>';
								echo '<option value="2" '.( $value['saleclass'] == '2' ? 'selected' : '' ).'>Used</option>';
							echo '</select>';
							echo '</div>';

							echo '<div id="' . $id_value . '" class="form-remove inventory-form-remove ' . $id_value . '"><span>[x]</span></div>';

							echo '</div>';
						}
					}
				?>
			</div>
		</div>
	</div>
