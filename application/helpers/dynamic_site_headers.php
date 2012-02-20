<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

print_me( __FILE__ );

require_once( dirname( __FILE__ ) . '/application/helpers/http_request.php' );

class dynamic_site_headers {

		public $host = NULL;
		public $company_id = 0;
		public $parameters = array();
		public $headers = array();
		public $request_stack = array();
		function __construct( $host , $company_id , $parameters ) {
print_me( __METHOD__ );
			$this->host = $host;
			$this->company_id = $company_id;
			$this->parameters = $parameters;
			$this->get_headers();
			if( $this->headers != false ) {
				add_filter( 'wp_title' , array( &$this , 'set_title' ) );
				add_action( 'wp_head' , array( &$this , 'set_head_information' ) , 1 );
			}
		}

		function get_headers() {
print_me( __METHOD__ );
			$taxonomy = isset( $this->parameters[ 'taxonomy' ] ) ? $this->parameters[ 'taxonomy' ] : 'inventory';
			$sale_class = isset( $this->parameters[ 'saleclass' ] ) ? $this->parameters[ 'saleclass' ] : 'All';
			$year = isset( $this->parameters[ 'year' ] ) ? $this->parameters[ 'year' ] : false;
			$make = isset( $this->parameters[ 'make' ] ) ? urlencode( $this->parameters[ 'make' ] ) : 'All';
			$model = isset( $this->parameters[ 'model' ] ) ? urlencode( $this->parameters[ 'model' ] ) : 'All';
			$trim = isset( $this->parameters[ 'trim' ] ) ? urlencode( $this->parameters[ 'trim' ] ) : 'All';
			$city = isset( $this->parameters[ 'city' ] ) ? urlencode( $this->parameters[ 'city' ] ) : false;
			$state = isset( $this->parameters[ 'state' ] ) ? urlencode( $this->parameters[ 'state' ] ) : false;
			$vin = isset( $this->parameters[ 'vin' ] ) ? $this->parameters[ 'vin' ] : false;
			$base = $year != false ? $year : $sale_class;
			if( $year == false ) {
				$url = $this->host . '/' . $this->company_id . '/seo_helpers.json?cu=/' . $taxonomy . '/' . $base . '/All/' . $make . '/' . $model . '/' . $city . '/' . $state . '/';
			} else {
				$url = $this->host . '/' . $this->company_id . '/seo_helpers.json?cu=/' . $taxonomy . '/' . $base . '/' . $make . '/' . $model . '/' . $vin . '/' . $city . '/' . $state . '/';
			}

			if( strtolower( $trim ) != 'all' ) {
				$url .= '?trim=' . urlencode( $trim );
			}
			$request_handler = new http_request( $url , 'dynamic_site_headers' );
			$this->request_stack[] = $url;
			$data = $request_handler->cached() ? $request_handler->cached() : $request_handler->get_file( true );
			$body = isset( $data[ 'body' ] ) ? json_decode( $data[ 'body' ] ) : false;
			if( $body ) {
				$this->headers[ 'page_title' ] = rawurldecode( $body->page_title );
				$this->headers[ 'page_description' ] = $body->page_description;
				$this->headers[ 'page_keywords' ] = $body->page_keywords;
				$this->headers[ 'follow' ] = $body->follow;
				$this->headers[ 'index' ] = $body->index;
			} else {
				$headers = false;
			}
		}

		function set_title() {
print_me( __METHOD__ );
			return $this->headers[ 'page_title' ] . ' ';
		}

		function set_head_information() {
print_me( __METHOD__ );
			if( isset( $this->headers[ 'page_description' ] ) && !empty( $this->headers[ 'page_description' ] ) ) {
				echo '<meta name="Description" content="' . $this->headers[ 'page_description' ] . '" />' . "\n";
			}
			if( isset( $this->headers[ 'page_keywords' ] ) && !empty( $this->headers[ 'page_keywords' ] ) ) {
				echo '<meta name="Keywords" content="' . $this->headers[ 'page_keywords' ] . '" />' . "\n";
			}
			$robots = array();
			if( isset( $this->headers[ 'follow' ] ) && $this->headers[ 'follow' ] != true ) {
				$robots[] = 'nofollow';
			}
			if( isset( $this->headers[ 'index' ] ) && $this->headers[ 'index' ] != true ) {
				$robots[] = 'noindex';
			}
			if( !empty( $robots ) ) {
				echo '<meta name="robots" content="' . implode( $robots , ',' ) . '" />' . "\n";
			}
		}

}

?>
