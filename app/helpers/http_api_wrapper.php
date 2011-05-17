<?php

if ( class_exists( 'http_api_wrapper' ) ) {
	return false;
}

/**
 * This is the primary class for our HTTP API wrapper.
 *
 * It is intended to save us many repeated processes involving verification of requests and caching.
 *
 * @package WordPress
 * @subpackage Plugin
 * @since 3.0.0
 */
class http_api_wrapper {

	/**
	 * Constant for defining how long a request should take before timing out.
	 *
	 * @since 3.0.0
	 * @var integer
	 */
	const timeout = 20;

	/**
	 * Public variable intended to contain the URL being requested.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	public $url = null;

	/**
	 * Public variable intended to contain the group or the requested file to be cached under.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var string
	 */
	public $group = null;

	/**
	 * Public variable containing the parameters used when making requests.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var array
	 */
	public $request_parameters = array(
		'timeout' => http_api_wrapper::timeout,
		'headers' => array( 'Referer' => NULL )
	);

	/**
	 * Sets up object properties. PHP 5 style constructor.
	 *
	 * @param string $url The requested file.
	 * @param string $group The group we'll be caching the file under.
	 * @since 3.0.0
	 * @return void
	 */
	function __construct( $url , $group ) {
		$this->url = $url;
		$this->group = $group;
		$this->request_parameters[ 'headers' ][ 'Referer' ] = get_site_url();
	}

	/**
	 * Try to fetch a cached object. If it's not cached, return false.
	 *
	 * @since 3.0.0
	 * @return mixed Object on success. False on fail.
	 */
	function cached() {
		return wp_cache_get( $this->url , $this->group );
	}

	/**
	 * Attempt to get the requested file.
	 *
	 * @param boolean $sanitize If true, then whatever is returned from the file request, sanitize using kses.
	 * @since 3.0.0
	 * @return mixed Error response array on fail. HTTP Object on success.
	 */
	function get_file( $sanitize = false ) {
		$start = timer_stop();
		$response = wp_remote_request( $this->url , $this->request_parameters );
		$stop = timer_stop();
		$response_time = $stop - $start;
		error_log( 'Requested file: ' . $this->url . ' , Response Time: ' . $response_time , 0 );
		if( wp_remote_retrieve_response_code( $response ) == 200 ) {
			if( $sanitize === true ) {
				$response[ 'body' ] = wp_kses_data( $response[ 'body' ] );
			}
			return $response;
		} else {
			if( is_wp_error( $response) ) {
				$error_message = $response->get_error_message();
				$error_code = $response->get_error_message();
				error_log( get_bloginfo( 'url' ) . ' , WP Error: ' . $error_message , 0 );
			} else {
				$error_code = wp_remote_retrieve_response_code( $response );
				$error_message = wp_remote_retrieve_response_message( $response );
				error_log( get_bloginfo( 'url' ) . ' , HTTP Error: ' . $error_message , 0 );
			}
			$error_array = array( 'code' => $error_code , 'message' => $error_message );
			return $error_array;
		}
	}

	/**
	 * Attempt to get the requested file.
	 *
	 * @param mixed Anything submitted  will try to be cached.
	 * @since 3.0.0
	 * @return boolean
	 */
	function cache_file( $data ) {
		return wp_cache_add( $url , $data , $group , 0 );
	}

}

?>
