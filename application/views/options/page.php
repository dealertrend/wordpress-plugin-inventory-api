<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

class Options_Page {

	public $instance = false;
	public $vehicle_management_system = false;
	public $vehicle_reference_system = false;

	public function display() {
		$this->initialize_dependancies();
		$this->check_posted_data( $_POST );
		$this->check_feeds();
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
		$makes = isset( $data[ 'vehicle_reference_system' ][ 'makes' ] ) ? $data[ 'vehicle_reference_system' ][ 'makes' ] : array();
		$models = isset( $data[ 'vehicle_reference_system' ][ 'models' ] ) ? $data[ 'vehicle_reference_system' ][ 'models' ] : array();

		if( count( $makes ) > 0 ) {
			$this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'makes' ] = $makes;
			$this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'models' ] = $models;
		}
		$showcase_theme = isset( $_POST[ 'jquery' ][ 'ui' ][ 'theme' ][ 'showcase' ] ) ? $_POST[ 'jquery' ][ 'ui' ][ 'theme' ][ 'showcase' ] : 'smoothness';
		$this->instance->options[ 'jquery' ][ 'ui' ][ 'showcase-theme' ] = $showcase_theme;

		if( isset( $_POST[ 'vehicle_reference_system' ][ 'makes' ] ) ) {
			$makes = isset( $_POST[ 'vehicle_reference_system' ][ 'makes' ] ) ? $_POST[ 'vehicle_reference_system' ][ 'makes' ] : array();
			$models = isset( $_POST[ 'vehicle_reference_system' ][ 'models' ] ) ? $_POST[ 'vehicle_reference_system' ][ 'models' ] : array();
		} elseif( isset( $_POST[ 'vehicle_management_system' ] ) || isset( $_POST[ 'vehicle_reference_system' ] ) ) {
			$host = isset( $_POST[ 'vehicle_management_system' ][ 'host' ] ) ? rtrim( $_POST[ 'vehicle_management_system' ][ 'host' ] , '/' ) : NULL;
			$this->instance->options[ 'vehicle_management_system' ][ 'host' ] = $host;
			$company_id = isset( $_POST[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] ) ? $_POST[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] : 0;
			if( $company_id != 0 ) {
				$this->instance->options[ 'vehicle_management_system' ][ 'company_information' ] = $_POST[ 'vehicle_management_system' ][ 'company_information' ];
			}
			$host = isset( $_POST[ 'vehicle_reference_system' ][ 'host' ] ) ? rtrim( $_POST[ 'vehicle_reference_system' ][ 'host' ] , '/' ) : NULL;
			$this->instance->options[ 'vehicle_reference_system' ][ 'host' ] = $host;
		} elseif( isset( $_POST[ 'theme' ] ) ) {
			$this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'name' ] = $_POST[ 'theme' ];
			$this->instance->options[ 'vehicle_management_system' ][ 'theme' ][ 'per_page' ] = $_POST[ 'per_page' ];
			$inventory_theme = isset( $_POST[ 'jquery' ][ 'ui' ][ 'theme' ][ 'inventory' ] ) ? $_POST[ 'jquery' ][ 'ui' ][ 'theme' ][ 'inventory' ] : 'smoothness';
			$this->instance->options[ 'jquery' ][ 'ui' ][ 'inventory-theme' ] = $inventory_theme;
		}
	}

	function save_options( $data ) {
		$this->instance->save_options();
	}

	function reset_options( $data ) {
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

	function check_feeds() {
	}

	function show_page() {

		include( dirname( __FILE__ ) . '/uninstall-dialog.php' );

		echo '<div id="icon-dealertrend" class="icon32"><br /></div>';
		echo '<h2>' . $this->instance->plugin_information[ 'Name' ] . '</h2>';
		if( count( $this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'makes' ] ) > 0 ) {
			if( count( $this->instance->options[ 'vehicle_reference_system' ][ 'data' ][ 'models' ] ) == 0 ) {
				echo '<div class="error" style="margin-left:0; margin-bottom:10px; margin-right:20px;"><p>';
				echo
				'<span class="ui-state-error ui-corner-all"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span></span>
				You have not selected any models to show on your Showcase page. Please update your Showcase settings.';
				echo '</p></div>';
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
			'<li><a href="#settings"><span class="ui-state-default ui-corner-all"><span class="ui-icon ui-icon-gear" style="float: left; margin-right: .3em;"></span></span>Settings</a></li>
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
		echo '</div>';
	}

}

$options_page = new Options_Page();

?>
