<?php

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

	function cached() {
		return wp_cache_get( $this->url , $this->group );
	}

	function get_file( $sanitize = false ) {
		$response = wp_remote_request( $this->url , $this->request_parameters );
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

	function cache_file( $data ) {
		return wp_cache_add( $url , $data , $group , 0 );
	}

}

?>
