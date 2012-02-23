<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

print_me( __FILE__ );

class http_request {

	private $_request_parameters = array();
	private $_cache_key = null;

	public function __construct() {
print_me( __METHOD__ );
		$this->_set_timeout();
		$this->_set_headers();
	}

	private function _set_headers() {
print_me( __METHOD__ );
		$headers[ 'Referer' ] = $this->_get_referrer();
		$headers[ 'X-WordPress-Version' ] = $this->_get_wordpress_version();
		$headers[ 'X-Plugin-Version' ] = $this->_get_plugin_version();
		$this->_request_parameters[ 'headers' ] = $headers;
		
	}

	private function _get_plugin_version() {
print_me( __METHOD__ );
		global $dealertrend_inventory_api;
		return $dealertrend_inventory_api->plugin_information[ 'Version' ];
	}

	private function _set_timeout() {
print_me( __METHOD__ );
		$this->_request_parameters[ 'timeout'] =  $dealertrend_inventory_api->options[ 'requests' ][ 'timeout_seconds' ];
	}

	private function _get_wordpress_version() {
print_me( __METHOD__ );
		global $wp_version;
		return $wp_version;
	}

	private function _get_referrer() {
print_me( __METHOD__ );
		global $wp;
		return site_url() . '/' . $wp->request;
	}

	public function set_cache_key( $key ) {
print_me( __METHOD__ );
		$this->_cache_key = $key;
	}

	public function cached( $node ) {
print_me( __METHOD__ );
		return wp_cache_get( $node , $this->_cache_key );
	}

	public function get_file( $node , $sanitize = false ) {
print_me( __METHOD__ );
		$response = wp_remote_request( $node , $this->request_parameters );
		$this->_cache_file( $node , $response );
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

		$handler = curl_multi_init();

		foreach( (array) $nodes as $key => $file ) {
			$instances[ $file ] = curl_init( $file );
			$options = array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HEADER => true,
				CURLOPT_HTTPHEADER => $this->_request_parameters[ 'headers' ]
			);
			curl_setopt_array( $instances[ $file ] , $options );
			curl_multi_add_handle( $handler , $instances[ $file ] );
		}

		$running = null;

		do {
			curl_multi_exec( $handler , $running );
		} while( $running > 0 );

		foreach( $nodes as $key => $file ) {
			$results[] = curl_multi_getcontent( $instances[ $file ] );
			$this->_cache_file( $file , $instances[ $file ] );
		}

		return $results;

	}

	private function _process_headers() {
	}

	private function _cache_file( $node , $data ) {
print_me( __METHOD__ );
		return wp_cache_add( $node , $data , $this->_cacke_key , 7200 );
	}

}

?>
