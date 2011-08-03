<?php

if ( class_exists( 'vehicle_reference_system' ) ) {
	return false;
}

/**
 * This is the primary class for the VRS.
 *
 * It's sole responsibility is to get and return resarch data from the VRS API.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 3.0.0
 */
class vehicle_reference_system {

	/**
	 * This is the address of the API we'll be querying against.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	public $host = NULL;

	/**
	 * Public array all requests made within the instance of the object.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var array
	 */
	public $request_stack = array();

	/**
	 * Private variable containing the routes for making API calls.
	 *
	 * @since 3.0.0
	 * @access private
	 * @var array
	 */
	private $routes = array();

	/**
	 * PHP 5 constructor.
	 *
	 * Sets up the routes for the VMS api.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var array
	 */
	function __construct( $host ) {
		$this->host = $host;

		$this->routes[ 'makes' ] = $this->host . '/makes.json';
		$this->routes[ 'models' ] = $this->host . '/models.json';
		$this->routes[ 'trims' ] = $this->host . '/trims.json';
	}

	/**
	 * Checks to see if the API's host can be reached.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var array
	 */
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

	/**
	 * Checks to see if data can be retreived from the API. Smallest request possible.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var array
	 */
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

	/**
	 * Retreives all makes avilable within the current context of the current request from the API.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var array
	 */
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

	/**
	 * Retreives all models avilable within the current context of the current request from the API.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var array
	 */
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

	/**
	 * Takes the given parameters and sanitizes them before submitting the call to the API.
	 *
	 * Also asserts some assumptions for improved performance.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var array
	 */
	function process_parameters( $parameters ) {
		$parameters = array_map( 'urldecode' , $parameters );

		return !empty( $parameters ) ? '?' . http_build_query( $parameters , '' , '&' ) : false;
	}

}

?>
