<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

class vehicle_management_system {

	const max_per_page = 50;

	public $host = NULL;
	public $tracer = NULL;
	public $company_id = 0;
	public $request_stack = array();

	private $url = NULL;
	private $parameters = array();

	function __construct( $host , $company_id ) {
		$this->host = $host;
		$this->company_id = $company_id;
	}

	function set_headers( $parameters = array() ) {
		$status = 400;
		$this->tracer = 'Checking inventory feed.';
		$check_inventory = $this->check_inventory()->please( $parameters );
		if( isset( $check_inventory[ 'response' ][ 'code' ] ) && $check_inventory[ 'response' ][ 'code' ] == 200 ) {
			$inventory_json = json_decode( $check_inventory[ 'body' ] );
			if( count( $inventory_json ) > 0 ) {
				$status = 200;
			} else {
				$status = 404;
			}
		}

		status_header( $status );

		return $status;
	}

	function check_host() {
		$this->url = $this->host;
		return $this;
	}

	function check_company_id() {
		$this->url = $this->host . '/api/companies/' . $this->company_id;
		return $this;
	}

	function check_inventory() {
		$this->url = $this->host . '/' . $this->company_id . '/inventory/vehicles.json';
		$this->parameters = array( 'photo_view' => 1 , 'per_page' => 1 );
		return $this;
	}

	function get_company_information() {
		$this->url = $this->host . '/api/companies/' . $this->company_id;
		return $this;
	}

	function get_inventory() {
		$this->url = $this->host . '/' . $this->company_id . '/inventory/vehicles.json';
		$this->parameters[ 'per_page' ] = isset( $parameters[ 'per_page' ] ) && $parameters[ 'per_page' ] <= vehicle_management_system::max_per_page ? $parameters[ 'per_page' ] : 10;
		return $this;
	}

	function get_makes() {
		$this->url = $this->host . '/' . $this->company_id . '/inventory/vehicles/makes.json';
		return $this;
	}

	function get_models() {
		$this->url = $this->host . '/' . $this->company_id . '/inventory/vehicles/models.json';
		return $this;
	}

	function get_trims() {
		$this->url = $this->host . '/' . $this->company_id . '/inventory/vehicles/trims.json';
		return $this;
	}

	function get_body_styles() {
		$this->url = $this->host . '/' . $this->company_id . '/inventory/vehicles/bodies.json';
		return $this;
	}

	public function please( $parameters = array() ) {

		$parameters = array_merge( $this->parameters , $parameters );
		$parameter_string = count( $parameters > 0 ) ? $this->process_parameters( $parameters ) : NULL;
		$parameters[ 'photo_view' ] = isset( $parameters[ 'photo_view' ] ) ? $parameters[ 'photo_view' ] : 1;

		$request = $this->url . $parameter_string;

		$request_handler = new http_request( $request , 'vehicle_management_system' );

		if( $this->tracer !== NULL ) {
			$this->request_stack[] = array( $request , $this->tracer );
			$this->tracer = NULL;
		} else {
			$this->request_stack[] = $request;
		}

		$data = $request_handler->cached() ? $request_handler->cached() : $request_handler->get_file();

		$this->parameters = array();
		return $data;
	}

	function process_parameters( $parameters ) {
		unset( $parameters[ 'taxonomy' ] );
		$parameters = array_map( 'urldecode' , $parameters );

		return !empty( $parameters ) ? '?' . http_build_query( $parameters , '' , '&' ) : false;
	}

}

?>
