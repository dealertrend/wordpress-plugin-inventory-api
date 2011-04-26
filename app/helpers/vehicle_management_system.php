<?php

if ( class_exists( 'vehicle_management_system' ) ) {
	return false;
}

require_once( dirname( __FILE__ ) . '/http_api_wrapper.php' );

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

		$test = new http_request_wrapper( $this->host );

	}

	function check_host() {
		$request_handler = new http_request_wrapper( $this->host );
		return $request_handler == false ? false : true;
	}

	function check_company_id() {
		$url = $this->routes[ 'company_information' ];
		$request_handler = new http_request_wrapper( $url );
		return $request_handler == false ? false : true;
	}

	function check_inventory() {
		$url = $this->routes[ 'vehicles' ] . '?photo_view=1&per_page=1';
		$request_handler = new http_request_wrapper( $url );
		return $request_handler == false ? false : true;
	}

	function get_company_information() {
		$request_handler = new http_request_wrapper( $this->routes[ 'company_information' ] );
		return json_decode( $request_handler );
	}

	function get_inventory( $parameters = array() ) {
		$parameters[ 'photo_view' ] = isset( $parameters[ 'photo_view' ] ) ? $parameters[ 'photo_view' ] : 1;
		$parameter_string = $this->process_parameters( $parameters );
		$url = $this->routes[ 'vehicles' ] . $parameter_string;
		$request_handler = new http_request_wrapper( $url );
		return json_decode( $request_handler );
	}

	function get_makes( $parameters = array() ) {
		$parameter_string = $this->process_parameters( $parameters );
		$url = $this->routes[ 'makes' ] . $parameter_string;
		$request_handler = new http_request_wrapper( $url );
		return json_decode( $request_handler );
	}

	function get_models( $parameters = array() ) {
		$parameter_string = $this->process_parameters( $parameters );
		$url = $this->routes[ 'models' ] . $parameter_string;
		$request_handler = new http_request_wrapper( $url );
		return json_decode( $request_handler );
	}

	function get_trims( $parameters = array() ) {
		$parameter_string = $this->process_parameters( $parameters );
		$url = $this->routes[ 'trims' ] . $parameter_string;
		$request_handler = new http_request_wrapper( $url );
		return json_decode( $request_handler );
	}

	function get_body_styles( $parameters = array()) {
		$parameter_string = $this->process_parameters( $parameters );
		$url = $this->routes[ 'body_styles' ] . $parameter_string;
		$request_handler = new http_request_wrapper( $url );
		return json_decode( $request_handler );
	}

	function process_parameters( $parameters ) {
		unset( $parameters[ 'taxonomy' ] );

		$parameters[ 'per_page' ] = isset( $parameters[ 'per_page' ] ) && $parameters[ 'per_page' ] <= vehicle_management_system::max_per_page ? $parameters[ 'per_page' ] : 10;

		$parameters = array_map( 'urldecode' , $parameters );

		return !empty( $parameters ) ? '?' . http_build_query( $parameters , '' , '&' ) : false;
	}

}

?>
