<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

print_me( __FILE__ );

class http_request {

	public $url = null;

	public $group = null;

	public $request_parameters = array(
		'headers' => array(
			'Referer' => NULL,
			'X-WordPress-Version' => NULL,
			'X-Plugin-Version' => NULL
		)
	);

	public function __construct( $url , $group ) {
print_me( __METHOD__ );
		global $wp , $wp_version , $dealertrend_inventory_api;
		$this->url = $url;
		$this->group = $group;

		$plugin_options = get_option( 'dealertrend_inventory_api' );
		$this->request_parameters[ 'headers' ][ 'Referer' ] = site_url() . '/' . $wp->request;
		$this->request_parameters[ 'headers' ][ 'X-WordPress-Version' ] = $wp_version;
		$this->request_parameters[ 'headers' ][ 'X-Plugin-Version' ] = $dealertrend_inventory_api->plugin_information[ 'Version' ];
		$this->request_parameters[ 'timeout'] = ! isset( $this->request_parameters[ 'timeout'] ) ? $dealertrend_inventory_api->options[ 'requests' ][ 'timeout_seconds' ] : $this->request_parameters[ 'timeout'];
	}

	public function cached() {
print_me( __METHOD__ );
		return wp_cache_get( $this->url , $this->group );
	}

	public function get_file( $sanitize = false ) {
print_me( __METHOD__ );
		$start = timer_stop();
		$response = wp_remote_request( $this->url , $this->request_parameters );
		$stop = timer_stop();
		$response_time = $stop - $start;
		$this->_cache_file( $response );
		if( wp_remote_retrieve_response_code( $response ) == 200 ) {
			if( $sanitize == true ) {
				$response[ 'body' ] = wp_kses_data( $response[ 'body' ] );
			}
			return $response;
		} else {
			if( is_wp_error( $response) ) {
				$error_message = $response->get_error_message();
				$error_code = $response->get_error_message();
			} else {
				$error_code = wp_remote_retrieve_response_code( $response );
				$error_message = wp_remote_retrieve_response_message( $response );
			}
			$error_array = array( 'code' => $error_code , 'message' => $error_message );
			return $error_array;
		}
	}

	public function get_multi_files( $nodes ) {
print_me( __METHOD__ );
		$instances = array();

		$handler = curl_multi_exec();

		foreach( (array) $nodes as $key => $file ) {
			$instance[ $file ] = curl_init( $file );
			$options = array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HEADER =>  true,
				CURLOPT_HTTPHEADER => $this->request_parameters[ 'headers' ]
			);
			curl_setopt_array( $instances[ $file ] , $options );
			curl_multi_add_handle( $handler , $instances[ $file ] );
		}
		$start = timer_stop();

		$running = null;

		do {
			curl_multi_exec( $handler , $running );
		} while( $running > 0 );

		$stop = timer_stop();

		$response_time = $stop - $start;

		foreach( $nodes as $key => $file ) {
			$results[ $file ] = curl_multi_getcontent( $instances[ $file ] );
			$this->url = $file;
			$this->_cache_file( $instances[ $file ] );
			print_r( $results );
			return $results;
		}

	}

	private function _cache_file( $data ) {
print_me( __METHOD__ );
		return wp_cache_add( $this->url , $data , $this->group , 7200 );
	}

}

?>
