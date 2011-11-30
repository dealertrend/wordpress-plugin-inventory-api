<div id="inventory">
	<form name="dealertrend_inventory_api_theme_settings_inventory" method="post" action="#feeds">
	<?php wp_nonce_field( 'dealertrend_inventory_api' ); ?>
		<table width="450">
		<tr>
			<td colspan="2"><h3 class="title">Inventory Theme Settings</h3></td>
		</tr>
		<tr>
			<td width="125">Current Theme:</td>
			<td><strong><?php echo ucwords( $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'name' ] ); ?></strong></td>
		</tr>
		<tr>
			<td width="125">Change Theme:</td>
			<td>
			<select name="theme">
				<?php
				$themes = $this->instance->get_themes( 'inventory' );
				foreach( $themes as $key => $value ) {
					$selected = ( $value == $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'name' ] ) ? 'selected' : NULL;
					echo '<option ' . $selected . ' value="' . $value . '">' . ucwords( $value ) .'</option>';
				}
				?>
			</select>
			</td>
		</tr>
		<tr>
			<td width="125"><label for="jquery-ui-theme-inventory">jQuery UI Theme:</a></td>
			<td>
			<select id="jquery-ui-theme-inventory" name="jquery[ui][theme][inventory]">
			<?php
				$theme_path = str_replace( 'views/options' , 'assets/jquery-ui/1.8.11/themes' , dirname( __FILE__ ) );
				if( $handle = opendir( $theme_path ) ) {
				$ignore = array( '.' , '..' );
				while( false !== ( $file = readdir( $handle ) ) ) {
					$selected = $this->instance->options[ 'jquery' ][ 'ui' ][ 'inventory-theme' ] == $file ? 'selected' : NULL;
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
			<td width="125">Vehicles Per Page:</td>
			<td>
			<select name="per_page">
				<?php
				for( $i = 1; $i <= 50; $i ++ ) {
					$selected = ( $i == $this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'per_page' ] ) ? 'selected' : NULL;
					echo '<option ' . $selected . ' value="' . $i . '">'. $i .'</option>';
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
