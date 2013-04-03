<div id="showcase">
	<table width="450">
		<?php
			$response_code = false;
			if( ! empty( $this->instance->options[ 'vehicle_reference_system' ][ 'host' ] ) ) {
				if( isset( $this->vehicle_reference_system->status[ 'host' ][ 'results' ][ 'response' ][ 'code' ] ) ) {
					$response_code = $this->vehicle_reference_system->status[ 'host' ][ 'results' ][ 'response' ][ 'code' ];
				}
			}
			if( isset( $response_code ) && $response_code == 200 ) {
				echo '<form method="post" action="#showcase">';
				echo '<tr><td colspan="2"><h3 class="title">Showcase Settings</h3></td></tr>';

				$makes = isset( $this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'makes' ] ) ? $this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'makes' ] : array();
				$models = isset( $this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'models' ] ) ? $this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'models' ] : array();

				$make_data = $this->get_make_data();
				$make_values = $this->remove_data_dups( $make_data, 'name');
				natcasesort($make_values);

				echo '<tr><td width="125"><label for="makes">Makes: </label></td>';
				echo '<td><select id="makes" name="vehicle_reference_system[makes][]" class="vrs-makes" size="4" multiple="multiple">';
					$this->create_dd_options($make_values, $makes);
				echo '</select></td></tr>';
				echo '<tr><td><input type="hidden" name="action" value="update" /></td></tr>';

				if( count( $makes ) > 0 ) {
					echo '<tr><td><label for="models">Models: </label></td>';
					echo '<td><select id="models" name="vehicle_reference_system[models][]" class="vrs-models" size="4" multiple="multiple">';
					foreach( $makes as $make ) {
						$model_data = $this->get_model_data( $make );
						$model_values = $this->remove_data_dups( $model_data, 'name');
						natcasesort($model_values);

						echo '<optgroup label="' . $make . '">';
							$this->create_dd_options($model_values, $models);
						echo '</optgroup>';
					}
					echo '</select></tr>';
				}
				echo '<tr><td></td><td style="padding-left:175px;"><input type="submit" class="button-primary" value="Save"></td></tr>';
				wp_nonce_field( 'dealertrend_inventory_api' );
				echo '</form>';
			}
			?>
	</table>
	<form name="dealertrend_inventory_api_theme_settings_showcase" method="post" action="#showcase">
		<?php wp_nonce_field( 'dealertrend_inventory_api' ); ?>
		<table width="450">
		<tr>
			<td colspan="2"><h3 class="title">Theme Settings</h3></td>
		</tr>
		<tr>
			<td width="125">Current Theme:</td>
			<td><strong><?php echo ucwords( $this->instance->options[ 'vehicle_reference_system' ][ 'theme' ] ); ?></strong></td>
		</tr>
		<tr>
			<td width="125">Change Theme:</td>
			<td>
			<select name="showcase_theme" style="width:110px">
				<?php
				$themes = $this->instance->get_themes( 'showcase' );
				foreach( $themes as $key => $value ) {
					$selected = ( $value == $this->instance->options[ 'vehicle_reference_system' ][ 'theme' ] ) ? 'selected' : NULL;
					echo '<option ' . $selected . ' value="' . $value . '">' . ucwords( $value ) .'</option>';
				}
				?>
			</select>
			</td>
		</tr>
		<tr>
			<td width="125"><label for="jquery-ui-theme-showcase">jQuery UI Theme:</a></td>
			<td>
			<select id="jquery-ui-theme-showcase" name="jquery[ui][theme][showcase]">
			<?php
				$theme_path = str_replace( 'views/options' , 'assets/jquery-ui/1.8.11/themes' , dirname( __FILE__ ) );
				if( $handle = opendir( $theme_path ) ) {
				$ignore = array( '.' , '..' );
				while( false !== ( $file = readdir( $handle ) ) ) {
					$selected = $this->instance->options[ 'jquery' ][ 'ui' ][ 'showcase-theme' ] == $file ? 'selected' : NULL;
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
