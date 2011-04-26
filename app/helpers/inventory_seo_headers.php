<?php

if ( class_exists( 'inventory_seo_headers' ) ) {
	return false;
}

class inventory_seo_headers {

		public $host = NULL;
		public $company_id = 0;
		public $parameters = array();
		public $headers = array();

		function __construct( $host , $company_id , $parameters ) {
			$this->host = $host;
			$this->company_id = $company_id;
			$this->parameters = $parameters;
			$this->get_headers();
			if( $this->headers != false ) {
				add_filter( 'wp_title' , array( &$this , 'get_title' ) );
				add_action( 'wp_head' , array( &$this , 'get_meta' ) , 1 );
			}
		}

		function get_headers() {
			$sale_class = isset( $this->parameters[ 'saleclass' ] ) ? $this->parameters[ 'saleclass' ] : 'All';
			$year = isset( $this->parameters[ 'year' ] ) ? $this->parameters[ 'year' ] : false; 
			$make = isset( $this->parameters[ 'make' ] ) ? $this->parameters[ 'make' ] : 'All'; 
			$model = isset( $this->parameters[ 'model' ] ) ? $this->parameters[ 'model' ] : 'All';	
			$trim = isset( $this->parameters[ 'trim' ] ) ? $this->parameters[ 'trim' ] : 'All'; 
			$base = $year != false ? $year : $sale_class;
			$url = $this->host . '/' . $this->company_id . '/seo_helpers.phps?cu=/inventory/' . $base . '/' . $make . '/' . $model . '/' . $trim . '/';
			$request_handler = new http_api_wrapper( $url , 'inventory_seo_headers' );
			$data = $request_handler->cached() ? $request_handler->cached() : $request_handler->get_file( true );
			$body = $data[ 'body' ];
			$body = str_replace( '&lt;?php' , NULL , $body );
			$body = preg_replace( '/\/\*.*\*\//ixsm' , NULL , $body ); 
			if( !preg_match( '/dtarray.*seo_helpers.*false/' , $body ) ) {
				preg_match_all( '/=.*;/i' , trim( $body ) , $results );
				$this->headers[ 'page_title' ] = trim( preg_replace( '/\&quot;|;|=/' , NULL , $results[ 0 ][ 1 ] ) );
				$this->headers[ 'page_description' ] = trim( preg_replace( '/\&quot;|;|=/' , NULL , $results[ 0 ][ 2 ] ) );
				$this->headers[ 'page_keywords' ] = trim( preg_replace( '/\&quot;|;|=/' , NULL , $results[ 0 ][ 3 ] ) );
				$this->headers[ 'follow' ] = trim( preg_replace( '/\&quot;|;|=/' , NULL , $results[ 0 ][ 4 ] ) );
				$this->headers[ 'index' ] = trim( preg_replace( '/\&quot;|;|=/' , NULL , $results[ 0 ][ 5 ] ) );
			} else {
				$headers = false;
			}
		}

		function get_title() {
			return $this->headers[ 'page_title' ] . ' ';
		}

		function get_meta() {

			if( isset( $this->headers[ 'page_description' ] ) && !empty( $this->headers[ 'page_description' ] ) ) {
				echo '<meta name="Description" content="' . $this->headers[ 'page_description' ] . '" />' . "\n";
			}

			if( isset( $this->headers[ 'page_keywords' ] ) && !empty( $this->headers[ 'page_keywords' ] ) ) {
				echo '<meta name="Keywords" content="' . $this->headers[ 'page_keywords' ] . '" />' . "\n";
			}

			$robots = array();
			if( isset( $this->headers[ 'follow' ] ) && !empty( $this->headers[ 'follow' ] ) && $this->headers[ 'follow' ] == false ) {
				$robots[] = 'nofollow';
			}
			
			if( isset( $this->headers[ 'index' ] ) && !empty( $this->headers[ 'index' ] ) && $this->headers[ 'index' ] == false ) {
				$robots[] = 'noindex';
			}

			if( !empty( $robots ) ) {
				echo '<meta name="robots" content="' . implode( $robots , ',' ) . '" />' . "\n";
			}

		}

}
