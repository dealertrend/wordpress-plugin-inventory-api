<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

class Options_Page {

	public $instance = false;
	public $vehicle_management_system = false;
	public $vehicle_reference_system = false;
	public $host_error = array();

	function __construct() {
		$this->initialize_dependancies();
		$this->check_posted_data( $_POST );
		$this->set_variables();
		$this->show_page();
	}

	function initialize_dependancies() {
		global $dealertrend_inventory_api;
		$this->instance = $dealertrend_inventory_api;
	}

	function check_posted_data( $data ) {

		if( count( $data ) == 0 ) {
			return false;
		}

		$nonce = isset( $data[ '_wpnonce' ] ) ? $data[ '_wpnonce' ] : false;
		$this->check_security( $nonce );

		$action = isset( $data[ 'action' ] ) ? $data[ 'action' ] : false;

		switch( $action ) {
			case 'update':
				$this->process_options( $data );
				$this->save_options( $data );
				$this->update_rewrite_rules();
			break;
			case 'reset':
				$this->reset_options();
				$this->save_options( $data );
			break;
			case 'uninstall':
				$this->uninstall_plugin();
				$this->redirect( 'plugin_page' );
			break;
		}

	}

	function check_security( $nonce ) {
		if( ! wp_verify_nonce( $nonce , 'dealertrend_inventory_api' ) ) die( 'Security check failed.' );
	}

	function process_options( &$data ) {

		$data = array_map( array( &$this->instance , 'sanitize_inputs' ) , $data );

		switch( $data['page'] ){

			case 'inventory':
				$vms_makes = isset( $data[ 'vehicle_management_system' ][ 'makes_new' ] ) ? $data[ 'vehicle_management_system' ][ 'makes_new' ] : array();				
				$this->instance->options[ 'vehicle_management_system' ][ 'data' ][ 'makes_new' ] = $vms_makes;

				$this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'name' ] = $_POST[ 'theme' ];
				$this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'per_page' ] = $_POST[ 'per_page' ];
				$this->instance->options[ 'vehicle_management_system' ][ 'saleclass' ] = $_POST[ 'saleclass' ];
				$this->instance->options[ 'vehicle_management_system' ][ 'inv_responsive' ] = $_POST[ 'inv_responsive' ];
				$this->instance->options[ 'vehicle_management_system' ][ 'custom_contact' ][ 'phone_new' ] = $_POST[ 'phone_new' ];
				$this->instance->options[ 'vehicle_management_system' ][ 'custom_contact' ][ 'phone_used' ] = $_POST[ 'phone_used' ];
				$this->instance->options[ 'vehicle_management_system' ][ 'custom_contact' ][ 'name_new' ] = $_POST[ 'name_new' ];
				$this->instance->options[ 'vehicle_management_system' ][ 'custom_contact' ][ 'name_used' ] = $_POST[ 'name_used' ];
				$this->instance->options[ 'vehicle_management_system' ][ 'tags' ][ 'counter' ] = $_POST[ 'inventory_tags_counter' ];
				$this->instance->options[ 'vehicle_management_system' ][ 'tags' ][ 'data' ] = ( !empty($_POST[ 'inventory_tag' ]) ) ? $_POST[ 'inventory_tag' ]: array();
				$this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ] = $_POST[ 'custom_settings' ];
				$this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'show_standard_eq' ] = $_POST[ 'show_standard_eq' ];
				break;

			case 'showcase':
				$makes = isset( $data[ 'vehicle_reference_system' ][ 'makes' ] ) ? $data[ 'vehicle_reference_system' ][ 'makes' ] : array();
				if( !empty($makes) ){
					$models = isset( $data[ 'vehicle_reference_system' ][ 'models' ] ) ? $data[ 'vehicle_reference_system' ][ 'models' ] : array();
				} else {
					$models = array();
				}
				$this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'makes' ] = $makes;
				$this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'models' ] = $models;
				$this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'year_filter' ] = $_POST[ 'vrs_year_filter' ];
				$this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'models_manual' ] = $_POST[ 'vrs_models_manual' ];
				$showcase_theme = isset( $_POST[ 'showcase_theme' ] ) ? $_POST[ 'showcase_theme' ] : 'default';
				$this->instance->options[ 'vehicle_reference_system' ][ 'theme' ] = $showcase_theme;
				$this->instance->options[ 'alt_settings' ][ 'gravity_forms' ]['showcase'] = $_POST[ 'gravityform-showcase-id' ];
				$this->instance->options[ 'vehicle_reference_system' ][ 'theme_settings' ] = $_POST[ 'vrs_theme_settings' ];
				break;

			case 'settings':
				if( $_POST[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] ){
					$vms_check = $this->check_feeds( $this->instance->options[ 'vehicle_management_system' ][ 'host' ] ,'', $_POST[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ]);
					if( $vms_check ){
						$this->instance->options[ 'vehicle_management_system' ][ 'company_information' ] = $_POST[ 'vehicle_management_system' ][ 'company_information' ];
					} else {
						$this->host_error['vms'] = 'The VMS URL entered was not valid. Please enter a valid URL.';
						$this->instance->options[ 'vehicle_management_system' ][ 'company_information' ]['id'] = 0;
					}
				}
				
				if( $_POST[ 'vehicle_reference_system' ][ 'host' ] ){
					$vrs_check = $this->check_feeds('', $_POST[ 'vehicle_reference_system' ][ 'host' ] ,'');
					if( $vrs_check ){
						$this->instance->options[ 'vehicle_reference_system' ][ 'host' ] = rtrim( $_POST[ 'vehicle_reference_system' ][ 'host' ] , '/' );
					} else {
						$this->host_error['vrs'] = 'The VRS URL entered was not valid. Please enter a valid URL.';
						$this->instance->options[ 'vehicle_reference_system' ][ 'host' ] = '';
					}
				}
				
				$this->instance->options[ 'alt_settings' ][ 'discourage_seo_visibility' ] = $_POST[ 'discourage_seo_visibility' ];
				break;

		}

	}

	function save_options( $data ) {
		$this->instance->save_options();
	}

	function reset_options( $data ) {
	}

	function update_rewrite_rules() {
		$this->instance->flush_rewrite_rules( true );
	}

	function uninstall_plugin() {
		delete_option( 'dealertrend_inventory_api' );
		delete_option( 'vehicle_management_system' );
		delete_option( 'vehicle_reference_system' );
		deactivate_plugins( $this->instance->plugin_information[ 'PluginBaseName' ] );
	}

	function redirect( $where ) {
		switch( $where ) {
			case 'plugin_page':
				echo '<script type="text/javascript">window.location.replace("/wp-admin/plugins.php");</script>';
			break;
			case 'dealertrend_inventory_api':
				echo '<script type="text/javascript">window.location.replace("admin.php?page=dealertrend_inventory_api");</script>';
			break;
		}
		exit;
	}
	
	function set_variables(){
		$response = array();
		// Check VMS and Set VMS
		if( $this->instance->options[ 'vehicle_management_system' ][ 'host' ] ){
			$this->vehicle_management_system = new vehicle_management_system(
				$this->instance->options[ 'vehicle_management_system' ][ 'host' ],
				$this->instance->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ]
			);
			
			$response['vms']['results'] = $this->vehicle_management_system->check_company_id()->please();
			$vms_check = isset( $response['vms']['results']['response']['code'] ) ? isset( $response['vms']['results']['response']['code'] ) : FALSE;
			if( $vms_check == '200' ){
				$this->vehicle_management_system->status[ 'host' ][ 'results' ][ 'response' ][ 'code' ] = '200';
				$this->vehicle_management_system->status[ 'company_feed' ][ 'results' ][ 'response' ][ 'code' ] = '200';
				$this->vehicle_management_system->status[ 'inventory_feed' ][ 'results' ] = $this->vehicle_management_system->check_inventory()->please();
				$company_information = $this->vehicle_management_system->get_company_information()->please();
				$company_information = json_decode( $company_information[ 'body' ] );
			}
			
		}
		
		// Set Country Code
		$country_code = isset( $company_information->country_code ) ? $company_information->country_code : 'US';
		
		// Check VRS and Set VRS
		if( $this->instance->options[ 'vehicle_reference_system' ][ 'host' ] ){
			$this->vehicle_reference_system = new vehicle_reference_system (
				$this->instance->options[ 'vehicle_reference_system' ][ 'host' ],
				$country_code
			);
			
			$response['vrs']['results'] = $this->vehicle_reference_system->check_feed()->please();
			$vrs_check = isset( $response['vrs']['results']['response']['code'] ) ? isset( $response['vrs']['results']['response']['code'] ) : FALSE;
			if( $vrs_check == '200' ){
				$this->vehicle_reference_system->status[ 'host' ][ 'results' ][ 'response' ][ 'code' ] = '200';
				$this->vehicle_reference_system->status[ 'feed' ][ 'results' ][ 'response' ][ 'code' ] = '200';
			}
		}
	}

	function check_feeds( $vms, $vrs, $id ) {
		//Check VMS on save
		if( $vms ){
			$vms_class = new vehicle_management_system( $vms, $id );
			$response['results'] = $vms_class->check_company_id()->please();
			$check = isset( $response['results']['response']['code'] ) ? $response['results']['response']['code'] : FALSE;
			if( $check == '200' ){
				return TRUE;
			}
		}
		
		// Check VRS on save
		if( $vrs ){
			$vrs_class = new vehicle_reference_system ( $vrs, 'US' ); //Country Code is US only to check
			$response['results'] = $vrs_class->check_feed()->please();
			$check = isset( $response['results']['response']['code'] ) ? $response['results']['response']['code'] : FALSE;
			if( $check == '200' ){
				return TRUE;
			}
		}

		return FALSE;

	}

	function get_make_data() {
		//Sets the variables for Vehicle Year
		$current_year = date( 'Y' );
		$last_year = $current_year - 1;
		$next_year = $current_year + 1;
		//Gets vehicle make data for each year
		$make_data[ $last_year ] = $this->vehicle_reference_system->get_makes()->please( array( 'year' => $last_year ) );
		$make_data[ $last_year ] = json_decode( $make_data[ $last_year ][ 'body' ] );
		$make_data[ $current_year ] = $this->vehicle_reference_system->get_makes()->please( array( 'year' => $current_year ) );
		$make_data[ $current_year ] = json_decode( $make_data[ $current_year ][ 'body' ] );
		$make_data[ $next_year ] = $this->vehicle_reference_system->get_makes()->please( array( 'year' => $next_year ) );
		$make_data[ $next_year ] = json_decode( $make_data[ $next_year ][ 'body' ] );

		$make_data[ $last_year ] = is_array( $make_data[ $last_year ] ) ? $make_data[ $last_year ] : array();
		$make_data[ $current_year ] = is_array( $make_data[ $current_year ] ) ? $make_data[ $current_year ] : array();
		$make_data[ $next_year ] = is_array( $make_data[ $next_year ] ) ? $make_data[ $next_year ] : array();

		$make_data = array_merge( $make_data[ $next_year ], $make_data[ $current_year ] , $make_data[ $last_year ] );

		return $make_data;
	}

	function get_model_data( $make_data ) {
		//Sets the variables for Vehicle Year
		$current_year = date( 'Y' );
		$last_year = $current_year - 1;
		$next_year = $current_year + 1;
		//Gets vehicle model data for each selected make & year
		$model_data[ $last_year ] = $this->vehicle_reference_system->get_models()->please( array( 'make' => $make_data , 'year' => $last_year ) );
		$model_data[ $last_year ] = isset( $model_data[ $last_year ][ 'body' ] ) ? json_decode( $model_data[ $last_year ][ 'body' ] ) : NULL;
		$model_data[ $current_year ] = $this->vehicle_reference_system->get_models()->please( array( 'make' => $make_data , 'year' => $current_year ) );
		$model_data[ $current_year ] = isset( $model_data[ $current_year ][ 'body' ] ) ?json_decode( $model_data[ $current_year ][ 'body' ] ) : NULL;
		$model_data[ $next_year ] = $this->vehicle_reference_system->get_models()->please( array( 'make' => $make_data , 'year' => $next_year ) );
		$model_data[ $next_year ] = isset( $model_data[ $next_year ][ 'body' ] ) ? json_decode( $model_data[ $next_year ][ 'body' ] ) : NULL;

		$model_data[ $last_year ] = is_array( $model_data[ $last_year ] ) ? $model_data[ $last_year ] : array();
		$model_data[ $current_year ] = is_array( $model_data[ $current_year ] ) ? $model_data[ $current_year ] : array();
		$model_data[ $next_year ] = is_array( $model_data[ $next_year ] ) ? $model_data[ $next_year ] : array();
		//Merge model data
		$model_data = array_merge( $model_data[ $last_year ] , $model_data[ $current_year ] , $model_data[ $next_year ] );
		//Return merged array data
		return $model_data;
	}

	function remove_data_dups( $data, $name) {
		//Cleans data by removing duplicate entries
		$cleaned_data = array();
		foreach($data as $data_scrub){
			array_push($cleaned_data, $data_scrub->$name);
		}

		return array_unique($cleaned_data);
	}

	function create_dd_options( $data, $data_check ){
		//Checks to see if data has been saved and sets selected flag, than displays the option.
		foreach($data as $data_name){
			$selected = in_array( str_replace( '&', '&amp;', $data_name ) , $data_check ) ? 'selected' : NULL;
			echo '<option value="' . $data_name . '" ' . $selected . '>' . $data_name . '</option>';
		}
	}

	function show_page() {

		if(function_exists( 'wp_enqueue_media' )){
			wp_enqueue_media();
		}else{
			wp_enqueue_style('thickbox');
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
		}

		include( dirname( __FILE__ ) . '/uninstall-dialog.php' );

		echo '<div id="icon-dealertrend" class="icon32"><br /></div>';
		echo '<h2>' . $this->instance->plugin_information[ 'Name' ] . '</h2>';
		if( count( $this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'makes' ] ) > 0 ) {
			if( count( $this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'models' ] ) == 0 && count( $this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'models_manual' ] ) == 0 ){
				echo '<div class="error" style="margin-left:0; margin-bottom:10px; margin-right:20px;"><p>';
				echo
				'<span class="ui-state-error ui-corner-all"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span></span>
				You have not selected any models to show on your Showcase page. Please update your Showcase settings.';
				echo '</p></div>';
			}
		}
		if( !empty($this->host_error) ){
			foreach( $this->host_error as $error ){
				echo '<div class="error">'.$error.'</div>';
			}
		}
		echo '<div id="option-tabs" style="clear:both; margin-right:20px;">';
		echo
		'<ul>
			<li><a href="#feeds"><span class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-power" style="float: left; margin-right: .3em;"></span></span>Feed Status</a></li>';
			if( strlen( $this->instance->options[ 'vehicle_management_system' ][ 'host' ] ) > 0 ) {
				echo '<li><a href="#inventory">';
				if( isset( $this->vehicle_management_system->status[ 'inventory_feed' ][ 'results' ][ 'response' ][ 'code' ] ) ) {
					if( $this->vehicle_management_system->status[ 'inventory_feed' ][ 'results' ][ 'response' ][ 'code' ] == '200' ) {
						echo '<span class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-check" style="float: left; margin-right: .3em;"></span></span>';
					}
				} else {
					echo '<span class="ui-state-error ui-corner-all"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span></span>';
				}
				echo 'Inventory</a></li>';
			}
			if( strlen( $this->instance->options[ 'vehicle_reference_system' ][ 'host' ] ) > 0 ) {
				echo '<li><a href="#showcase">';
				if( isset( $this->vehicle_reference_system->status[ 'feed' ][ 'results' ][ 'response' ][ 'code' ] ) ) {
					if( $this->vehicle_reference_system->status[ 'feed' ][ 'results' ][ 'response' ][ 'code' ] == '200' ) {
						echo '<span class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-check" style="float: left; margin-right: .3em;"></span></span>';
					}
				} else {
					echo '<span class="ui-state-error ui-corner-all"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span></span>';
				}
				echo 'Showcase</a></li>';
			}
			echo
			'<li><a href="#shortcode"><span class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-gear" style="float: left; margin-right: .3em;"></span></span>Shortcode</a></li>
			<li><a href="#settings"><span class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-gear" style="float: left; margin-right: .3em;"></span></span>Settings</a></li>
			<li><a href="#help">Help</a></li>
		</ul>';
		include( dirname( __FILE__ ) . '/feed-status.php' );
		if( strlen( $this->instance->options[ 'vehicle_management_system' ][ 'host' ] ) > 0 ) {
			if( isset( $this->vehicle_management_system->status[ 'inventory_feed' ][ 'results' ][ 'response' ][ 'code' ] ) ) {
				if( $this->vehicle_management_system->status[ 'inventory_feed' ][ 'results' ][ 'response' ][ 'code' ] == '200' ) {
					include( dirname( __FILE__ ) . '/inventory.php' );
				}
			}
		}
		if( strlen( $this->instance->options[ 'vehicle_reference_system' ][ 'host' ] ) > 0 ) {
			if( isset( $this->vehicle_reference_system->status[ 'feed' ][ 'results' ][ 'response' ][ 'code' ] ) ) {
				if( $this->vehicle_reference_system->status[ 'feed' ][ 'results' ][ 'response' ][ 'code' ] == '200' ) {
					include( dirname( __FILE__ ) . '/showcase.php' );
				}
			}
		}
		include( dirname( __FILE__ ) . '/settings.php' );
		include( dirname( __FILE__ ) . '/help.php' );
		include( dirname( __FILE__ ) . '/shortcode.html' );
		echo '</div>';
	}

}

$options_page = new Options_Page();

?>
