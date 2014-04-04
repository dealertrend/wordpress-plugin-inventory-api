<div id="showcase" class="settings-wrapper">
	<div class="form-save-wrapper">
		<div class="form-save-button" name="showcase-form">Save Showcase Settings</div>
	</div>
	<form method="post" action="#showcase" id="showcase-form">
		<table width="450" class="table-left">
		<input type="hidden" value="showcase" name="page" />
		<?php
			$response_code = false;
			if( ! empty( $this->instance->options[ 'vehicle_reference_system' ][ 'host' ] ) ) {
				if( isset( $this->vehicle_reference_system->status[ 'host' ][ 'results' ][ 'response' ][ 'code' ] ) ) {
					$response_code = $this->vehicle_reference_system->status[ 'host' ][ 'results' ][ 'response' ][ 'code' ];
				}
			}
			if( isset( $response_code ) && $response_code == 200 ) {

				echo '<tr><td colspan="2"><h3 class="title">Showcase Settings</h3></td></tr>';

				$makes = isset( $this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'makes' ] ) ? $this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'makes' ] : array();
				$models = isset( $this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'models' ] ) ? $this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'models' ] : array();
				$models_next = isset( $this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'models_manual' ][ 'next' ] ) ? $this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'models_manual' ][ 'next' ] : array();
				$models_current = isset( $this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'models_manual' ][ 'current' ] ) ? $this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'models_manual' ][ 'current' ] : array();

				$make_data = $this->get_make_data();
				$make_values = $this->remove_data_dups( $make_data, 'name');
				natcasesort($make_values);

				echo '<tr><td width="125"><label for="makes">Makes: </label></td>';
				echo '<td><select id="makes" name="vehicle_reference_system[makes][]" class="vrs-makes" size="4" multiple="multiple">';
					$this->create_dd_options($make_values, $makes);
				echo '</select></td></tr>';
				echo '<tr><td><input type="hidden" name="action" value="update" /></td></tr>';

				if( count( $makes ) > 0 ) {
					// Year-Filter
					echo '<tr><td width="125">Year Filter:</td><td><select name="vrs_year_filter">';
					echo '<option value="0" ' . ( ($this->instance->options[ "vehicle_reference_system" ][ "data" ][ "year_filter" ] == "0")?"selected":"" ) . ' >Default</option>';
					echo '<option value="1" ' . ( ($this->instance->options[ "vehicle_reference_system" ][ "data" ][ "year_filter" ] == "1")?"selected":"" ) . ' >Current Year Only</option>';
					echo '<option value="2" ' . ( ($this->instance->options[ "vehicle_reference_system" ][ "data" ][ "year_filter" ] == "2")?"selected":"" ) . ' >Manual Selection</option>';
					echo '</select></td></tr>';

					if( $this->instance->options[ "vehicle_reference_system" ][ "data" ][ "year_filter" ] != 2 ){
						// Models
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
					} else {
						$year_c = date('Y');
						$year_n = $year_c + 1;
						// Next Model Year
						echo '<tr><td><label for="vrs_models_manual_next">Next Model ('. $year_n .'): </label></td>';
						echo '<td><select id="models" name="vrs_models_manual[next][]" class="vrs-models" size="4" multiple="multiple">';
						foreach( $makes as $make ) {
							$model_data = $this->get_model_data( $make );
							$model_values = $this->remove_data_dups( $model_data, 'name');
							natcasesort($model_values);

							echo '<optgroup label="' . $make . '">';
								$this->create_dd_options($model_values, $models_next);
							echo '</optgroup>';
						}
						echo '</select></tr>';
						// Current Model Year
						echo '<tr><td><label for="vrs_models_manual_current">Current Model ('. $year_c .'): </label></td>';
						echo '<td><select id="vrs-models-manual" name="vrs_models_manual[current][]" class="vrs-models" size="4" multiple="multiple">';
						foreach( $makes as $make ) {
							$model_data = $this->get_model_data( $make );
							$model_values = $this->remove_data_dups( $model_data, 'name');
							natcasesort($model_values);

							echo '<optgroup label="' . $make . '">';
								$this->create_dd_options($model_values, $models_current);
							echo '</optgroup>';
						}
						echo '</select></tr>';

					}
				}
			}

			wp_nonce_field( 'dealertrend_inventory_api' ); 

			?>

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
				<td width="125"><label for="gravityform-showcase-id">Gravity Form ID:</a></td>
				<td>
					<input style="width: 110px;" type="text" name="gravityform-showcase-id" id="gravityform-showcase-id" value="<?php echo $this->instance->options[ 'alt_settings' ][ 'gravity_forms' ]['showcase'] ?>" />
				</td>
			</tr>

			<?php
				if( strtolower( $this->instance->options[ 'vehicle_reference_system' ][ 'theme' ] ) == 'apollo' ){
			?>
				<tr>
					<td colspan="2"><h3 class="title">Apollo Theme Settings</h3></td>
				</tr>
				<tr>
					<td>Gravity Form Link</td>
					<td>Set "form-showcase-data" as the class (on a textarea field) on your form to pass user selected data on trim detail page.</td>
				</tr>
				<tr>
					<td width="125">Display Similar<br>VMS Vehicles:</td>
					<td>
						<input type="checkbox" id="apollo_display_vms" name="vrs_theme_settings[apollo][display_vms]" <?php echo ( !empty($this->instance->options[ 'vehicle_reference_system' ][ 'theme_settings' ][ 'apollo' ][ 'display_vms' ]) ) ? ' checked ' : ''; ?> />
					</td>
				</tr>
				<tr>
					<td width="125">Display VMS Count:</td>
					<td>
						<select id="apollo_display_vms_count" name="vrs_theme_settings[apollo][display_vms_count]">
							<?php $this_value = $this->instance->options[ 'vehicle_reference_system' ][ 'theme_settings' ][ 'apollo' ][ 'display_vms_count' ]; ?>
							<option value="4" <?php echo ( $this_value == 4) ? ' selected ' : ''; ?> >4</option>
							<option value="8" <?php echo ( $this_value == 8) ? ' selected ' : ''; ?> >8</option>
							<option value="12" <?php echo ( $this_value == 12) ? ' selected ' : ''; ?> >12</option>
							<option value="16" <?php echo ( $this_value == 16) ? ' selected ' : ''; ?> >16</option>
						</select>
					</td>
				</tr>
				<tr>
					<td><h3 class="title">Videos</h3></td>
					<td>
						<div class="edit-table-button" name="video-table">Add Video</div>
					</td>
				</tr>
				<tr>
					<td width="125">Display Videos:</td>
					<td>
						<input type="checkbox" id="apollo_display_videos" name="vrs_theme_settings[apollo][display_videos]" <?php echo ( !empty($this->instance->options[ 'vehicle_reference_system' ][ 'theme_settings' ][ 'apollo' ][ 'display_videos' ]) ) ? ' checked ' : ''; ?> />
					</td>
				</tr>
				<tr>
					<td><h3 class="title">Messages</h3></td>
					<td>
						<div class="edit-table-button" name="message-table">Create Message</div>
					</td>
				</tr>

			<?php
				}
			?>

			<tr>
				<td>
				<input type="hidden" name="action" value="update" />
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Theme Settings' ) ?>" />
				</p>
				</td>
			</tr>
			</table>

			<div id="video-table" class="hidden-table">
				<div class="hidden-table-buttons-wrapper">
					<input id="apollo-custom-video-counter" type="hidden" name="vrs_theme_settings[apollo][custom_videos][counter]" value="<?php echo $this_value; ?>" readonly />
					<div id="apollo-add-custom-video" class="table-add-button">Add Custom Video</div>
				</div>
				<?php
					$this_value = $this->instance->options[ 'vehicle_reference_system' ][ 'theme_settings' ][ 'apollo' ][ 'custom_videos' ][ 'counter' ];
					$this_array = $this->instance->options[ 'vehicle_reference_system' ][ 'theme_settings' ][ 'apollo' ][ 'custom_videos' ][ 'data' ];
				?>
				<div class="hidden-table-headers">
					<div class="video-make">Vehicle<br>Make</div>
					<div class="video-model">Vehicle<br>Model</div>
					<div class="video-url">Video<br>URL</div>
					<div class="video-remove">Video<br>Remove</div>
				</div>

				<div id="apollo-custom-video-tr">
					<div id="apollo-custom-video-td">
						<?php
							if( !empty($this_array) ){
								foreach( $this_array as $key => $value){
									$id_value = 'apollo-custom-video-'.$key;
									echo '<div class="custom-video-wrapper ' . $id_value . '">';

									echo '<div class="video-make custom-video-value ' . $id_value . '">';
									echo '<input type="text" name="vrs_theme_settings[apollo][custom_videos][data][' . $key . '][make]" id="apollo_custom_video_' . $key . '_make" class="apollo_input_text ' . $id_value . '" value="' . $value['make'] . '" />';
									echo '</div>';
									echo '<div class="video-model custom-video-value ' . $id_value . '">';
									echo '<input type="text" name="vrs_theme_settings[apollo][custom_videos][data][' . $key . '][model]" id="apollo_custom_video_' . $key . '_model" class="apollo_input_text ' . $id_value . '" value="' . $value['model'] . '" />';
									echo '</div>';
									echo '<div class="video-url custom-video-value ' . $id_value . '">';
									echo '<input type="text" name="vrs_theme_settings[apollo][custom_videos][data][' . $key . '][url]" id="apollo_custom_video_' . $key . '_url" class="apollo_input_text ' . $id_value . '" value="' . $value['url'] . '" />';
									echo '</div>';

									echo '<div id="' . $id_value . '" class="video-remove apollo-custom-video-remove ' . $id_value . '"><span>[x]</span></div>';

									echo '</div>';
								}
							}
						?>
					</div>
				</div>
			</div>

			<div id="message-table" class="hidden-table">
				<div class="hidden-table-buttons-wrapper">
					<input id="apollo-custom-message-counter" type="hidden" name="vrs_theme_settings[apollo][custom_message][counter]" value="<?php echo $this_value; ?>" readonly />
					<div id="apollo-add-custom-message" class="table-add-button">Add Custom Message</div>
				</div>
				<?php
					$this_value = $this->instance->options[ 'vehicle_reference_system' ][ 'theme_settings' ][ 'apollo' ][ 'custom_message' ][ 'counter' ];
					$this_array = $this->instance->options[ 'vehicle_reference_system' ][ 'theme_settings' ][ 'apollo' ][ 'custom_message' ][ 'data' ];
//<div id="apollo-help-message">Help <span>?</span></div>
				?>
				<div class="hidden-table-headers">
					<div class="message-count">Value To<br>Evaluate</div>
					<div class="message-operator">Value<br>Operator</div>
					<div class="message-text">Message<br>Text</div>
					<div class="message-form">Form<br>Title</div>
					<div class="message-remove">Message<br>Remove</div>
				</div>


				<div id="apollo-custom-message-tr">
					<div id="apollo-custom-message-td">
					<?php
						if( !empty($this_array) ){
							foreach( $this_array as $key => $value){
								$id_value = 'apollo-custom-message-'.$key;
								echo '<div class="custom-message-wrapper ' . $id_value . '">';
									
								echo '<div class="message-count custom-message-value ' . $id_value . '">';
								echo '<input type="number" name="vrs_theme_settings[apollo][custom_message][data][' . $key . '][count]" id="apollo_custom_message_' . $key . '_count" class="apollo_input_num ' . $id_value . '" value="' . $value['count'] . '" />';
								echo '</div>';

								echo '<div class="message-operator custom-message-value ' . $id_value . '">';
								echo '<select name="vrs_theme_settings[apollo][custom_message][data][' . $key . '][count_operator]" id="apollo_custom_message_[' . $key . ']_count_operator" class="' . $id_value . '" >';
									echo '<option class="' . $id_value . '" value=">" ' . ($value['count_operator'] == ">" ? "selected": "") . ' >&gt;</option>';
									echo '<option class="' . $id_value . '" value="<" ' . ($value['count_operator'] == "<" ? "selected": "") . ' >&lt;</option>';
									echo '<option class="' . $id_value . '" value=">=" ' . ($value['count_operator'] == ">=" ? "selected": "") . ' >&ge;</option>';
									echo '<option class="' . $id_value . '" value="<=" ' . ($value['count_operator'] == "<=" ? "selected": "") . ' >&le;</option>';
									echo '<option class="' . $id_value . '" value="=" ' . ($value['count_operator'] == "=" ? "selected": "") . ' >=</option>';
									echo '<option class="' . $id_value . '" value="!=" ' . ($value['count_operator'] == "!=" ? "selected": "") . ' >&ne;</option>';
								echo '</select>';
								echo '</div>';

								echo '<div class="message-text custom-message-value ' . $id_value . '">';
								echo '<textarea name="vrs_theme_settings[apollo][custom_message][data][' . $key . '][message]" id="apollo_custom_message_' . $key . '_message" class="' . $id_value . '" >' . $value['message'] . '</textarea>';
								echo '</div>';

								echo '<div class="message-form custom-message-value ' . $id_value . '">';
								echo '<input type="text" name="vrs_theme_settings[apollo][custom_message][data][' . $key . '][form_title]" id="apollo_custom_message_' . $key .'_form_title" class="' . $id_value . '" value="' . $value['form_title'] . '"/>';
								echo '</div>';

								echo '<div id="' . $id_value . '" class="message-remove apollo-custom-message-remove ' . $id_value . '"><span>[x]</span></div>';

								echo '</div>';
								}
							}
						?>
						<div id="apollo-help-dialog-box" >
							<h3>Value to Evaluate - </h3>
							<p>Showcase tries to find similar vehicles currently in your inventory, and returns that value. The value entered here, will reflect against the value found.</p>
							<hr>
							<h3>Value Operator - </h3>
							<p>How you want the value entered to reflect against the value found.<br><br>( x=your_entered_value | y=vehicle_count_found )<br><br>e.g. Operators{ &gt;, &ge; }<br> ( y &gt; x ) and ( y &ge; x )<br><br>e.g. Operators{ &lt;, &le;, =, &ne; }<br> ( x &lt; y ), ( x &le; y ), ( x = y ) and ( x &ne; y ) </p>
							<hr>
							<h3>Message - </h3>
							<p>Message that will be displayed if the entered expression is found. Also, use the following variables in your message to display specific content:<br><br>[count] - Number of similar vehicles found.<br>[make] - Current make of vehicle loaded.<br>[model] - Current model of vehicle loaded.</p>
							<hr>
							<h3>Form Title - </h3>
							<p>Change the title of the loaded form. Above variables can also be used.</p>
						</div>
					</div>
				</div>
			</div>
	</form>
</div>
