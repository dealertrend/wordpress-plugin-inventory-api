<?php

if ( class_exists( 'inventory_seo_headers' ) ) {
	return false;
}

class inventory_seo_headers {

		public $company_id = 0;
		public $parameters = array();

		function __construct( $company_id , $parameters ) {
			$this->company_id = $company_id;
			$this->parameters = $parameters;

			# get_headers
			# hooks

			add_filter( 'wp_title' , array( &$this , 'get_title' ) );
			add_action( 'wp_head' , array( &$this , 'get_meta' ) , 1 );

		}

		function get_headers() {
			
		}

		function get_title() {
		}

		function get_meta() {
		}

}
