<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

print_me( __FILE__ );

require_once( dirname( __FILE__ ) . '/http_request.php' );

class vehicle_management_system {

	public $host = NULL;
	public $company_id = 0;
	private $_url = NULL;
	private $_parameters = array();
	private $_requests = array();
	private $_nodes = array();

	public function __construct( $host , $company_id ) {
print_me( __METHOD__ );
		$this->host = $host;
		$this->company_id = $company_id;
	}

	private function _max_per_page() {
print_me( __METHOD__ );
			return 50;
	}

	public function set_headers( $inventory ) {
print_me( __METHOD__ );
		$status = 400;
		if( isset( $inventory[ 'response' ][ 'code' ] ) && $inventory[ 'response' ][ 'code' ] == 200 ) {
			$inventory_json = json_decode( $inventory[ 'body' ] );
			if( count( $inventory_json ) > 0 ) {
				$status = 200;
			} else {
				$status = 404;
			}
		}
		
		status_header( $status );

		return $status;
	}

	public function get_company_information() {
print_me( __METHOD__ );
		$this->url = $this->host . '/api/companies/' . $this->company_id;
		return $this;
	}

	public function get_inventory() {
print_me( __METHOD__ );
		$this->url = $this->host . '/' . $this->company_id . '/inventory/vehicles.json';
		$this->parameters[ 'per_page' ] = isset( $parameters[ 'per_page' ] ) && $parameters[ 'per_page' ] <= $this->_max_per_page ? $parameters[ 'per_page' ] : 10;
		return $this;
	}

	public function get_makes() {
print_me( __METHOD__ );
		$this->url = $this->host . '/' . $this->company_id . '/inventory/vehicles/makes.json';
		return $this;
	}

	public function get_models() {
print_me( __METHOD__ );
		$this->url = $this->host . '/' . $this->company_id . '/inventory/vehicles/models.json';
		return $this;
	}

	public function get_trims() {
print_me( __METHOD__ );
		$this->url = $this->host . '/' . $this->company_id . '/inventory/vehicles/trims.json';
		return $this;
	}

	public function get_body_styles() {
print_me( __METHOD__ );
		$this->url = $this->host . '/' . $this->company_id . '/inventory/vehicles/bodies.json';
		return $this;
	}

	public function please( $parameters = array() , $last_request = false ) {
print_me( __METHOD__ );

		$parameters = array_merge( $this->_parameters , $parameters );
		$parameter_string = count( $parameters > 0 ) ? $this->_process_parameters( $parameters ) : NULL;
		$parameters[ 'photo_view' ] = isset( $parameters[ 'photo_view' ] ) ? $parameters[ 'photo_view' ] : 1;
		$this->_requests[] = $this->url . $parameter_string;

		$queue = array();
		$this->parameters = array();

		if( $this->_do_parallel() && $last_request == true ) {
			$request_handler = new http_request();
			$request_handler->set_cache_key( 'vehicle_management_system' );
			foreach( $this->_requests as $node ) {
				if( $request_handler->cached( $node ) ) {
					$request_handler->cached( $node );
				} else {
					$this->_nodes[] = $node;
				}
			}
			return $request_handler->get_multi_files( $this->_nodes );
		} elseif( ! $this->_do_parallel() ) {
			$request_handler = new http_request();
			$request_handler->set_cache_key( 'vehicle_management_system' );
			$node = $this->_requests[ 0 ];
			return $request_handler->cached( $node ) ? $request_handler->cached( $node ) : $request_handler->get_file( $node );
		}
	}

	private function _do_parallel() {
print_me( __METHOD__ );
		global $dealertrend_inventory_api;
		return $dealertrend_inventory_api->options[ 'requests' ][ 'do_parallel' ];
	}

	private function _process_parameters( $parameters ) {
print_me( __METHOD__ );
		unset( $parameters[ 'taxonomy' ] );
		$parameters = array_map( 'urldecode' , $parameters );

		return !empty( $parameters ) ? '?' . http_build_query( $parameters , '' , '&' ) : false;
	}

}

?>
