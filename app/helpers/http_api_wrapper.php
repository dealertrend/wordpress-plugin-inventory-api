<?php
if ( class_exists( 'http_api_wrapper' ) ) {
  return false;
}

class http_api_wrapper {

	const timeout = 10;

	public $url = null;
	public $group = null;

	public $request_parameters = array(
		'timeout' => http_api_wrapper::timeout
	);

	function __construct( $url , $group ) {
		$this->url = $url;
		$this->group = $group;
	}

	function is_cached() {
		return wp_cache_get( $this->url , $this->group );
	}

	function get_file() {
		$response = wp_remote_request( $this->url , $this->request_parameters );
		if( wp_remote_retrieve_response_code( $response ) == 200 ) {
			return $response[ 'body' ];
		} else {
			if( is_wp_error( $response) ) {
				$error_message = $response->get_error_message();
				error_log( get_bloginfo( 'url' ) . ' , WP Error: ' . $error_string , 0 );
				return $error_message;
			} else {
				$error_message = wp_remote_retrieve_response_message( $response );
				error_log( get_bloginfo( 'url' ) . ' , HTTP Error: ' . $error_string , 0 );
				return $error_message;
			}
		}
	}

	function cache_file( $data ) {
		return wp_cache_add( $url , $data , $group , 0 );
	}

}

?>
