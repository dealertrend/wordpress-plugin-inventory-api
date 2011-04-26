<?php

if ( class_exists( 'inventory_seo_headers' ) ) {
	return false;
}

class inventory_seo_headers {

		public $host = NULL;
		public $company_id = 0;
		public $parameters = array();

		function __construct( $host , $company_id , $parameters ) {
			$this->host = $host;
			$this->company_id = $company_id;
			$this->parameters = $parameters;

#echo '<pre>';
#print_r($this);
#echo '</pre>';

			# get_headers
			# hooks

			add_filter( 'wp_title' , array( &$this , 'get_title' ) );
			add_action( 'wp_head' , array( &$this , 'get_meta' ) , 1 );

		}

		function get_headers() {
			#http://api.dealertrend.com/46/seo_helpers.phps?cu=/inventory/New/All/All/All/Reno/NV/

			$saleclass = 'All';
			$make = NULL; 
			$model = NULL; 
			$trim = NULL; 
			$city = NULL; 
			$state = NULL; 

			$url = $this->host . '/' . $this->company_id . '/seo_helpers.phps?cu=/inventory/';

			$request_handler = new http_api_wrapper( $this->host , 'inventory_seo_headers' );
			$data = $request_handler->cached() ? $request_handler->cached() : $request_handler->get_file();
		}

		function get_title() {
		}

		function get_meta() {
		}

}
