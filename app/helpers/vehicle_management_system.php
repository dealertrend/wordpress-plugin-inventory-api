<?php

if ( class_exists( 'vehicle_management_system' ) ) {
	return false;
}

class vehicle_management_system {

	const max_per_page = 50;
	const max_request_time = 10;

	public $host = NULL;
	public $company_id = NULL;

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
		$response = wp_remote_head( $this->host , array( 'timeout' => $this::max_request_time ) );

		if( is_wp_error( $response ) || $response[ 'headers' ][ 'status' ] != '200 OK' ) {
			return false;
		}

		return true;
	}

	function check_company_id() {
		$url  = $this->routes[ 'company_information' ];
		$response = wp_remote_head( $url , array( 'timeout' => $this::max_request_time ) );

		if( is_wp_error( $response ) || $response[ 'headers' ][ 'status' ] != '200 OK' ) {
			return false;
		}

		return true;
	}

	function check_inventory() {
		$url = $this->routes[ 'vehicles' ] . '?photo_view=1';
		$response = wp_remote_head( $url , array( 'timeout' => $this::max_request_time ) );

		if( is_wp_error( $response ) || $response[ 'headers' ][ 'status' ] != '200 OK' ) {
			return false;
		}

		return true;
	}

	function get_company_information() {
		return json_decode( $this->get_remote_file( $this->routes[ 'company_information' ] ) );
	}

	function get_inventory( $parameters = array() ) {

		$parameter_string = $this->process_parameters( $parameters );

		$url = $this->routes[ 'vehicles' ] . $parameter_string;

		return json_decode( $this->get_remote_file( $url ) );
	}

	function get_makes( $parameters = array() ) {
		$parameter_string = $this->process_parameters( $parameters );
		$url = $this->routes[ 'makes' ] . $parameter_string;

		return json_decode( $this->get_remote_file( $url ) );
	}

	function get_models( $parameters = array() ) {
		$parameter_string = $this->process_parameters( $parameters );
		$url = $this->routes[ 'models' ] . $parameter_string;

		return json_decode( $this->get_remote_file( $url ) );
	}

	function get_trims( $parameters = array() ) {
		$parameter_string = $this->process_parameters( $parameters );
		$url = $this->routes[ 'trims' ] . $parameter_string;

		return json_decode( $this->get_remote_file( $url ) );
	}

	function get_body_styles( $parameters = array()) {
		$parameter_string = $this->process_parameters( $parameters );
		$url = $this->routes[ 'body_styles' ] . $parameter_string;

		return json_decode( $this->get_remote_file( $url ) );
	}

	function process_parameters( $parameters ) {
		unset( $parameters[ 'taxonomy' ] );

		$parameters[ 'per_page' ] = isset( $parameters[ 'per_page' ] ) && $parameters[ 'per_page' ] <= $this::max_per_page ? $parameters[ 'per_page' ] : 10;

		$parameters = array_map( 'urlencode' , $parameters );

		return !empty( $parameters ) ? '?' . http_build_query( $parameters , '' , '&' ) : false;
	}

	function get_remote_file( $url , $use_cache = true ) {
		$data = wp_cache_get( $url , 'dealertrend_inventory_api' );
		if( $data != false && $use_cache == true ) {
			return $data;
		}
		$response = wp_remote_request( $url , array( 'timeout' => 10 ) );
		if( is_wp_error( $response ) || $response[ 'headers' ][ 'status' ] != '200 OK' ) {
			$error_string = $response->errors[ 'http_request_failed' ][ 0 ];
			# Courtesy error logging ^_^
			error_log( get_bloginfo( 'url' ) . ': WARNING: ' . $error_string, 0 );
			error_log( get_bloginfo( 'url' ) . ': REQUEST: ' . $url , 0 );
			$this->status[ $option_key ] = false;
			return false;
		} else {

			# Returned nothing.
			if( empty( $response ) ) {
				return false;
			}

			$status_header = isset( $response[ 'headers' ][ 'status' ] ) ? $response[ 'headers' ][ 'status' ] : false;
			# The API isn't happy with our parameters or we were given a bad URL.
			if( $status_header == '404 Not Found' || $status_header != '200 OK' ) {
				return false;
			}

			$data = ( trim( $response[ 'body' ] ) != '[]' ) ? $response[ 'body' ] : false;
			wp_cache_add( $url , $data , 'dealertrend_inventory_api' , 0 );

			return $data;
		}
	}

}

?>
