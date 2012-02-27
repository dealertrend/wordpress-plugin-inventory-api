<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

require_once( dirname( __FILE__ ) . '/http_request.php' );

class Dynamic_Site_Headers extends Plugin {

	private $_headers = array();

	# START!
	public function execute() {
		if( $this->_get_headers() ) {
			$this->_hook_into_wordpress();
		}
	}

	#execute
	private function _get_headers() {
		$request = $this->_get_request();
		$request_handler = new Http_Request();
		$request_handler->set_cache_key( $this->_get_cache_key() );

		$data = $request_handler->cached( $request ) ? $request_handler->cached( $request ) : $request_handler->get_file( $request );

		$json = json_decode( $data);
		if( $json ) {
			$this->_headers[ 'page_title' ] = rawurldecode( $json->page_title );
			$this->_headers[ 'page_description' ] = $json->page_description;
			$this->_headers[ 'page_keywords' ] = $json->page_keywords;
			$this->_headers[ 'follow' ] = $json->follow;
			$this->_headers[ 'index' ] = $json->index;
		} else {
			$this->_headers = false;
		}

		return $this->headers;
	}

	#execute > _get_headers
	private function _get_request() {
		$host = $this->_get_vms_host() . '/' . $this->_get_vms_company_id() . '/seo_helpers.json?cu=/' . $this->_get_taxonomy() . '/';
		if( ! $this->_get_parameter( 'year' ) ) {
			$host .= $this->_get_parameter( 'saleclass' , 'all' ) . '/All/' .
			$this->_get_parameter( 'make' , 'all' ) . '/' .
			$this->_get_parameter( 'model' , 'all' ) . '/';
		} else {
			$host .= $this->_get_parameter( 'year' ) . '/' .
			$this->_get_parameter( 'make' , 'all' ) . '/' .
			$this->_get_parameter( 'model' , 'all' ) . '/' .
			$this->_get_parameter( 'vin' ) . '/';
		}
		$host .= $this->_get_parameter( 'city' ) . '/' . $this->_get_parameter( 'state' ) . '/';
		if( $this->_get_parameter( 'trim' , 'all' ) != 'all' ) {
			$host .= '?trim=' . urlencode( $this->_get_parameter( 'trim' ) );
		}

		return $host;
	}

	#execute > _get_headers
	private function _get_cache_key() {
		return 'dynamic_site_headers';
	}

	#execute
	private function _hook_into_wordpress() {
		add_filter( 'wp_title' , array( &$this , 'get_title' ) );
		add_action( 'wp_head' , array( &$this , 'get_head_information' ) , 1 );
	}

	#execute > _hook_into_wordpress
	public function get_title() {
		return $this->_headers[ 'page_title' ] . ' ';
	}

	#execute > _hook_into_wordpress
	public function get_header_information() {
		echo $this->_get_page_description();
		echo $this->_get_keywords();
		echo $this->_get_robots();
	}

	#execute > _hook_into_wordpress > get_header_information
	private function _get_page_description() {
		if( isset( $this->_headers[ 'page_description' ] ) ) {
			if( ! empty( $this->_headers[ 'page_description' ] ) ){
				return '<meta name="Description" content="' . $this->_headers[ 'page_description' ] . '" />' . "\n";
			}
		}

		return false;
	}

	#execute > _hook_into_wordpress > get_header_information
	private function _get_keywords() {
		if( isset( $this->_headers[ 'page_keywords' ] ) ) {
			if( ! empty( $this->_headers[ 'page_keywords' ] ) ){
				return '<meta name="Keywords" content="' . $this->_headers[ 'page_keywords' ] . '" />' . "\n";
			}
		}

		return false;
	}

	#execute > _hook_into_wordpress > get_header_information
	private function _get_robots() {
		if( $this->_get_follow() ) {
			$robots[] = $this->_get_follow();
		}
		if( $this->_get_index() ) {
			$robots[] = $this->_get_index();
		}
		if( ! isset( $robots ) ) {
			return false;
		}

		return '<meta name="robots" content="' . implode( $robots , ',' ) . '" />' . "\n";
	}

	private function _get_follow() {
		if( isset( $this->_headers[ 'follow' ] ) ) {
			if( $this->_headers[ 'follow' ] != true ) {
				return 'nofollow';
			}
		}

		return false;
	}

	public function _get_index() {
		if( isset( $this->_headers[ 'index' ] ) ) {
			if( $this->_headers[ 'index' ] != true ) {
				return 'noindex';
			}
		}

		return false;
	}

}

?>
