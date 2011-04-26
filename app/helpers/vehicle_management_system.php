<?php

class vehicle_management_system {

	const max_per_page = 50;

	public $host = NULL;
	public $company_id = 0;

	private $routes = array();

	function __construct( $host , $company_id ) {

		$this->host = $host;
		$this->company_id = $company_id;

		$this->routes[ 'company_information' ] = $this->host . '/api/companies/' . $this->company_id;
		$this->routes[ 'vehicles' ] = $this->host . '/' . $this->company_id . '/inventory/vehicles.json';
		$this->routes[ 'makes' ] = $this->host . '/' . $this->company_id . '/inventory/vehicles/makes.json';
		$this->routes[ 'models' ] = $this->host . '/' . $this->company_id . '/inventory/vehicles/models.json';
		$this->routes[ 'trims' ] = $this->host . '/' . $this->company_id . '/inventory/vehicles/trims.json';
		$this->routes[ 'body_styles' ] = $this->host . '/' . $this->company_id . '/inventory/vehicles/bodies.json';

	}

	function check_host() {
		$request_handler = new http_api_wrapper( $this->host , 'vehicle_management_system' );
		$data = $request_handler->cached() ? $request_handler->cached() : $request_handler->get_file();
		$data_array = array( 'status' => false , 'data' => $data );
		if( isset( $data[ 'body' ] ) ) {
			$data_array[ 'status' ] = true;
		}
		return $data_array;
	}

	function check_company_id() {
		$url = $this->routes[ 'company_information' ];
		$request_handler = new http_api_wrapper( $url , 'vehicle_management_system' );
		$data = $request_handler->cached() ? $request_handler->cached() : $request_handler->get_file();
		$data_array = array ( 'status' => false , 'data' => $data );
		if( isset( $data[ 'body' ] ) ) {
			$data_array[ 'status' ] = true;
		}
		return $data_array;
	}

	function check_inventory() {
		$url = $this->routes[ 'vehicles' ] . '?photo_view=1&per_page=1';
		$request_handler = new http_api_wrapper( $url , 'vehicle_management_system' );
		$data = $request_handler->cached() ? $request_handler->cached() : $request_handler->get_file();
		if( isset( $data[ 'body' ] ) ) {
			if( trim( $data[ 'body' ] ) != '[]' ) {
				$data_array[ 'status' ] = true;
			} else {
				$data_array[ 'status' ] = false;
				$data[ 'code' ] = 200;
				$data[ 'message' ] = 'Inventory does not exist.';
				
			}
		} else {
			$data_array[ 'status' ] = false;
		}
		$data_array[ 'data' ] = $data;
		return $data_array;
	}

	function get_company_information() {
		$request_handler = new http_api_wrapper( $this->routes[ 'company_information' ] , 'vehicle_management_system' );
		$data = $request_handler->cached() ? $request_handler->cached() : $request_handler->get_file();
		$data_array = array ( 'status' => false, 'data' => json_decode( $data[ 'body' ] ) );
		if( isset( $request_hander[ 'body ' ] ) ) {
			$data_array[ 'status' ] = true;
		}
		return $data_array;
	}

	function get_inventory( $parameters = array() ) {
		$parameters[ 'photo_view' ] = isset( $parameters[ 'photo_view' ] ) ? $parameters[ 'photo_view' ] : 1;
		$parameter_string = $this->process_parameters( $parameters );
		$url = $this->routes[ 'vehicles' ] . $parameter_string;
		$request_handler = new http_api_wrapper( $url , 'vehicle_management_system' );
		$data = $request_handler->cached() ? $request_handler->cached() : $request_handler->get_file();
		if( isset( $data[ 'body' ] ) ) {
			return json_decode( $data[ 'body' ] );
		} else {
			return false;
		}
	}

	function get_makes( $parameters = array() ) {
		$parameter_string = $this->process_parameters( $parameters );
		$url = $this->routes[ 'makes' ] . $parameter_string;
		$request_handler = new http_api_wrapper( $url , 'vehicle_management_system' );
		$data = $request_handler->cached() ? $request_handler->cached() : $request_handler->get_file();
		return json_decode( $data[ 'body' ] );
	}

	function get_models( $parameters = array() ) {
		$parameter_string = $this->process_parameters( $parameters );
		$url = $this->routes[ 'models' ] . $parameter_string;
		$request_handler = new http_api_wrapper( $url , 'vehicle_management_system' );
		$data = $request_handler->cached() ? $request_handler->cached() : $request_handler->get_file();
		return json_decode( $data[ 'body' ] );
	}

	function get_trims( $parameters = array() ) {
		$parameter_string = $this->process_parameters( $parameters );
		$url = $this->routes[ 'trims' ] . $parameter_string;
		$request_handler = new http_api_wrapper( $url , 'vehicle_management_system' );
		$data = $request_handler->cached() ? $request_handler->cached() : $request_handler->get_file();
		return json_decode( $data[ 'body' ] );
	}

	function get_body_styles( $parameters = array()) {
		$parameter_string = $this->process_parameters( $parameters );
		$url = $this->routes[ 'body_styles' ] . $parameter_string;
		$request_handler = new http_api_wrapper( $url , 'vehicle_management_system' );
		$data = $request_handler->cached() ? $request_handler->cached() : $request_handler->get_file();
		return json_decode( $data[ 'body' ] );
	}

	function process_parameters( $parameters ) {
		unset( $parameters[ 'taxonomy' ] );

		$parameters[ 'per_page' ] = isset( $parameters[ 'per_page' ] ) && $parameters[ 'per_page' ] <= vehicle_management_system::max_per_page ? $parameters[ 'per_page' ] : 10;

		$parameters = array_map( 'urldecode' , $parameters );

		return !empty( $parameters ) ? '?' . http_build_query( $parameters , '' , '&' ) : false;
	}

}

?>
