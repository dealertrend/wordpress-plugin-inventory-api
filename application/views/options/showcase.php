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
				echo '<form method="post" action="#">';
				$makes = isset( $this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'makes' ] ) ? $this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'makes' ] : array();
				$models = isset( $this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'models' ] ) ? $this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'models' ] : array();

				$current_year = date( 'Y' );
				$last_year = $current_year - 1;
				$next_year = $current_year + 1;

				$make_data[ $last_year ] = $this->vehicle_reference_system->get_makes()->please( array( 'year' => $last_year ) );
				$make_data[ $last_year ] = json_decode( $make_data[ $last_year ][ 'body' ] );
				$make_data[ $current_year ] = $this->vehicle_reference_system->get_makes()->please( array( 'year' => $current_year ) );
				$make_data[ $current_year ] = json_decode( $make_data[ $current_year ][ 'body' ] );
				$make_data[ $next_year ] = $this->vehicle_reference_system->get_makes()->please( array( 'year' => $next_year ) );
				$make_data[ $next_year ] = json_decode( $make_data[ $next_year ][ 'body' ] );

				$make_data = array_merge( $make_data[ $next_year ] , $make_data[ $current_year ] , $make_data[ $last_year ] );

				$i_can_haz_make = array();
				foreach( $make_data as $key => $value ) {
					$existing_data = array_search( $value->name , $i_can_haz_make );
					if( $existing_data == false ) {
						$i_can_haz_make[ $key ] = $value->name;
					} else {
						$make_data[ $existing_data ] = $value;
						unset( $make_data[ $key ] );
					}
				}

				$make_values = $make_data;

				echo '<tr>';
				echo '<td colspan="2"><h3 class="title">Showcase</h3></td>';
				echo '</tr>';
				echo '<tr><td width="125"><label for="makes">Makes: </label></td>';
				echo '<td><select id="makes" name="vehicle_reference_system[makes][]" class="vrs-makes" size="4" multiple="multiple">';
				foreach( $make_values as $make ) {
					$selected = in_array( $make->name , $makes ) ? 'selected' : NULL;
					echo '<option value="' . $make->name . '" ' . $selected . '>' . $make->name . '</option>';
				}
				echo '</select></td></tr>';
				echo '<tr><td><input type="hidden" name="action" value="update" /></td></tr>';

				if( count( $makes ) > 0 ) {
					echo '<tr><td><label for="models">Models: </label></td>';
					echo '<td><select id="models" name="vehicle_reference_system[models][]" class="vrs-models" size="4" multiple="multiple">';
					foreach( $makes as $make ) {
							$model_data[ $last_year ] = $this->vehicle_reference_system->get_models()->please( array( 'make' => $make , 'year' => $last_year ) );
							$model_data[ $last_year ] = isset( $model_data[ $last_year ][ 'body' ] ) ? json_decode( $model_data[ $last_year ][ 'body' ] ) : NULL;
							$model_data[ $current_year ] = $this->vehicle_reference_system->get_models()->please( array( 'make' => $make , 'year' => $current_year ) );
							$model_data[ $current_year ] = isset( $model_data[ $current_year ][ 'body' ] ) ?json_decode( $model_data[ $current_year ][ 'body' ] ) : NULL;
							$model_data[ $next_year ] = $this->vehicle_reference_system->get_models()->please( array( 'make' => $make , 'year' => $next_year ) );
							$model_data[ $next_year ] = isset( $model_data[ $next_year ][ 'body' ] ) ? json_decode( $model_data[ $next_year ][ 'body' ] ) : NULL;

							$model_data[ $last_year ] = is_array( $model_data[ $last_year ] ) ? $model_data[ $last_year ] : array();
							$model_data[ $current_year ] = is_array( $model_data[ $current_year ] ) ? $model_data[ $current_year ] : array();
							$model_data[ $next_year ] = is_array( $model_data[ $next_year ] ) ? $model_data[ $next_year ] : array();

							$model_data = array_merge( $model_data[ $last_year ] , $model_data[ $current_year ] , $model_data[ $next_year ] );

							# It would be cool if there was a better way to do this.
							$i_can_haz_model = array();
							foreach( $model_data as $key => $value ) {
								$existing_data = array_search( $value->name , $i_can_haz_model );
								if( $existing_data == false ) {
									$i_can_haz_model[ $key ] = $value->name;
								} else {
									$model_data[ $existing_data ] = $value;
									unset( $model_data[ $key ] );
								}
							}
							$model_values = $model_data;
							echo '<optgroup label="' . $make . '">';
							foreach( $model_values as $model ) {
								$selected = in_array( $model->name , $models ) ? 'selected' : NULL;
								echo '<option value="' . $model->name . '" ' . $selected . '>' . $model->name . '</option>';
							}
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
	<form name="dealertrend_inventory_api_theme_settings_showcase" method="post" action="#feeds">
		<?php wp_nonce_field( 'dealertrend_inventory_api' ); ?>
		<table width="450">
		<tr>
			<td colspan="2"><h3 class="title">Theme Settings</h3></td>
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
