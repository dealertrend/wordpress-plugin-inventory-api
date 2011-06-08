<?php

if ( class_exists( 'vehicle_reference_system' ) ) {
	return false;
}

class vehicle_reference_system {

	public $host = NULL;

	public $request_stack = array();

	private $routes = array();

	function __construct( $host ) {
		$this->host = $host;

		$this->routes[ 'makes' ] = $this->host . '/makes.json';
		$this->routes[ 'models' ] = $this->host . '/models.json';
		$this->routes[ 'trims' ] = $this->host . '/trims.json';
	}

	function check_host() {
		$request_handler = new http_api_wrapper( $this->host , 'vehicle_reference_system' );
		$this->request_stack[] = $this->host;
		$data = $request_handler->cached() ? $request_handler->cached() : $request_handler->get_file();
		$data_array = array( 'status' => false , 'data' => $data );
		if( isset( $data[ 'body' ] ) ) {
			$data_array[ 'status' ] = true;
		}
		return $data_array;
	}

	function check_feed() {
		$url = $this->routes[ 'makes' ];
		$request_handler = new http_api_wrapper( $url , 'vehicle_reference_system' );
		$this->request_stack[] = $url;
		$data = $request_handler->cached() ? $request_handler->cached() : $request_handler->get_file();
		$data_array = array( 'status' => false , 'data' => $data );
		if( isset( $data[ 'body' ] ) ) {
			$data_array[ 'status' ] = true;
		}
		return $data_array;
	}

	function get_makes( $parameters = array() ) {
		$parameter_string = $this->process_parameters( $parameters );
		$url = $this->routes[ 'makes' ] . $parameter_string;
		$request_handler = new http_api_wrapper( $url , 'vehicle_reference_system' );
		$this->request_stack[] = $url;
		$data = $request_handler->cached() ? $request_handler->cached() : $request_handler->get_file();
		$body = isset( $data[ 'body' ] ) ? json_decode( $data[ 'body' ] ) : false;
		$data_array = array ( 'status' => false, 'data' => $body );
		if( isset( $request_hander[ 'body ' ] ) ) {
			$data_array[ 'status' ] = true;
		}
		return $data_array;
	}

	function get_models( $parameters = array() ) {
		$parameter_string = $this->process_parameters( $parameters );
		$url = $this->routes[ 'models' ] . $parameter_string;
		$request_handler = new http_api_wrapper( $url , 'vehicle_reference_system' );
		$this->request_stack[] = $url;
		$data = $request_handler->cached() ? $request_handler->cached() : $request_handler->get_file();
		$body = isset( $data[ 'body' ] ) ? json_decode( $data[ 'body' ] ) : false;
		$data_array = array ( 'status' => false, 'data' => $body );
		if( isset( $request_hander[ 'body ' ] ) ) {
			$data_array[ 'status' ] = true;
		}
		return $data_array;
	}

	function process_parameters( $parameters ) {
		$parameters = array_map( 'urldecode' , $parameters );

		return !empty( $parameters ) ? '?' . http_build_query( $parameters , '' , '&' ) : false;
	}

}

?>
