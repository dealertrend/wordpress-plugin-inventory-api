<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

class Http_Request extends Plugin {

	private $_cache_key = null;
	private $_request_parameters = array();

	public function __construct() {
		$this->_set_timeout();
		$this->_set_headers();
	}

	private function _set_timeout() {
		$this->_request_parameters[ 'timeout'] =  $this->_plugin_options[ 'requests' ][ 'timeout_seconds' ];
	}

	private function _set_headers() {
		$this->_get_plugin_information();
		$headers[ 'Referer' ] = $this->_get_referrer();
		$headers[ 'X-Software-Name' ] = 'WordPress';
		$headers[ 'X-Software-Version' ] = $this->_get_wordpress_version();
		$headers[ 'X-Software-Extension-Type' ] = 'Plugin';
		$headers[ 'X-Software-Extension-Name' ] = $this->_plugin_information[ 'Name' ];
		$headers[ 'X-Extension-Version' ] = $this->_get_plugin_version();
		$this->_request_parameters[ 'headers' ] = $headers;
	}

	private function _get_referrer() {
		global $wp;
		return site_url() . '/' . $wp->request;
	}

	public function set_cache_key( $key ) {
		$this->_cache_key = $key;
	}

	private function _get_plugin_version() {
		return $this->_plugin_information[ 'Version' ];
	}

	private function _get_wordpress_version() {
		global $wp_version;
		return $wp_version;
	}

	public function get_cached_data( $node ) {
		return wp_cache_get( $node , $this->_cache_key );
	}

	public function get_file( $node , $sanitize = false ) {
		$response = wp_remote_request( $node , $this->request_parameters );
		$this->_cache_file( $node , $response );
		return $this->_check_response( $response );
	}

	private function _check_response( $response ) {
		if( wp_remote_retrieve_response_code( $response ) == 200 ) {
			return $response;
		} else {
			if( is_wp_error( $response) ) {
				$message = $response->get_error_message();
				$code = $response->get_error_message();
			} else {
				$code = wp_remote_retrieve_response_code( $response );
				$message = wp_remote_retrieve_response_message( $response );
			}
			return compact( 'code' , 'message' );
		}
	}

	public function get_multi_files( $nodes ) {
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

	private function _cache_file( $node , $data ) {
		return wp_cache_add( $node , $data , $this->_cacke_key , 7200 );
	}

}

?>
