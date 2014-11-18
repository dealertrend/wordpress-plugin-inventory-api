<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

class vehicle_management_system {

	const max_per_page = 50;

	public $host = NULL;
	public $tracer = NULL;
	public $company_id = 0;
	public $request_stack = array();

	private $url = NULL;
	private $parameters = array();

	function __construct( $host , $company_id ) {

		if( substr($host, 0, 7) == 'http://' ){
			$this->host = $host;
		} else {
			$this->host = 'http://' . $host;
		}

		$this->company_id = $company_id;
	}

	function set_headers( $parameters = array() ) {
		$status = 500;
		$this->tracer = 'Checking inventory feed.';
		$check_inventory = $this->check_inventory()->please( $parameters );

		if( isset( $check_inventory[ 'response' ][ 'code' ] ) ) {
			$status = $check_inventory[ 'response' ][ 'code' ];
		} else if ( isset( $check_inventory[ 'code' ] ) ) {
			$status = $check_inventory[ 'code' ];
        }
		status_header( $status );

		return $status;
	}

	function check_host() {
		$this->url = $this->host;
		return $this;
	}

	function check_company_id() {
		$this->url = $this->host . '/api/companies/' . $this->company_id;
		return $this;
	}

	function check_inventory() {
		$this->url = $this->host . '/' . $this->company_id . '/inventory/vehicles.json';
		$this->parameters = array( 'photo_view' => 1 , 'per_page' => 1 );
		return $this;
	}

	function get_company_information() {
		$this->url = $this->host . '/api/companies/' . $this->company_id;
		return $this;
	}

	function get_inventory() {
		$this->url = $this->host . '/' . $this->company_id . '/inventory/vehicles.json';
		$this->parameters[ 'per_page' ] = isset( $parameters[ 'per_page' ] ) && $parameters[ 'per_page' ] <= vehicle_management_system::max_per_page ? $parameters[ 'per_page' ] : 10;
		return $this;
	}

	function get_makes() {
		$this->url = $this->host . '/' . $this->company_id . '/inventory/vehicles/makes.json';
		return $this;
	}

	function get_models() {
		$this->url = $this->host . '/' . $this->company_id . '/inventory/vehicles/models.json';
		return $this;
	}

	function get_trims() {
		$this->url = $this->host . '/' . $this->company_id . '/inventory/vehicles/trims.json';
		return $this;
	}

	function get_body_styles() {
		$this->url = $this->host . '/' . $this->company_id . '/inventory/vehicles/bodies.json';
		return $this;
	}
	
	function get_automall_geo_data() {
		if ( false === ( $geo = get_site_transient('dt_geo_data') ) ){
			$geo = array();
			$this->url = $this->host .'/api/companies/'.$this->company_id;
			$data = $this->please();
			$data = isset( $data['body'] ) ? json_decode($data['body']) : array();
			if( $data->automall_ids !== NULL && count($data->automall_ids) > 1){
				foreach( $data->automall_ids as $value ){
					$this->url = $this->host . '/api/companies/'.$value;
					$dealer = $this->please();
					$dealer = isset( $dealer['body'] ) ? json_decode($dealer['body']) : array();
					if( !empty($dealer->city) && !empty($dealer->state) && !empty($dealer->zip) ){
						$geo[$dealer->state][$dealer->city][$dealer->zip][] = $value;
						//error_log('City: '.$dealer->city.' State: '.$dealer->state.' Zip: '.$dealer->zip);
					}
				}
				set_site_transient( 'dt_geo_data', $geo, 60*60*12 ); // 24 Hour Expire
			}		
		}

		return $geo;
	}
	
	function get_geo_dealer_mmt($key, $dealers, $params){
		$geo_array = array();
		if( !empty($dealers) ){
			$dealers = explode(',', $dealers);
			foreach( $dealers as $dealer ){
				$this->url = $this->host . '/' . $dealer . '/inventory/vehicles/'.$key.'.json';
				$temp = $this->please( $params );
				$temp = (isset($temp['body'])) ? json_decode($temp['body']) : array();
				$geo_array = (!empty($temp)) ? array_merge($geo_array, $temp) : $geo_array;
			}
		}
		$geo_array = array_unique($geo_array);
		return $geo_array;
	}

	public function please( $parameters = array() ) {

		//Shortcode ID switch for inventory
		if( isset($parameters['dealer_id']) && $parameters['dealer_id'] > 0 && !isset($parameters['geo_search']) ){
			$api_url = $this->host . '/' . $parameters['dealer_id'] . '/inventory/vehicles.json';
			unset( $parameters['dealer_id'] );
		} else {
			unset($parameters['geo_search']);
			$api_url = $this->url;
		}

		if( empty( $parameters['search_sim'] ) ) {
			$parameters = array_merge( $this->parameters , $parameters );
		} else {
			unset( $parameters['search_sim'] );
		}

		if( strcasecmp($parameters['saleclass'], 'new') == 0 && !empty($parameters['make_filters']) ) {
			$makes_string = '';
			foreach ( $parameters['make_filters'] as $new_make ) {
				if (  empty( $makes_string ) ) {
					$makes_string = 'makes[]=' . $new_make;
				} else {
					$makes_string .=  '&makes[]=' . $new_make;
				}
			}
		}
		unset( $parameters['make_filters'] );

		$parameter_string = count( $parameters > 0 ) ? $this->process_parameters( $parameters ) : NULL;
		$parameters[ 'photo_view' ] = isset( $parameters[ 'photo_view' ] ) ? $parameters[ 'photo_view' ] : 1;

		if( !empty( $makes_string ) ) {
			if ( !empty( $parameter_string ) ) {
				$parameter_string = $parameter_string . '&' . $makes_string;
			} else {
				$parameter_string = $makes_string;
			}
		}

		$request = $api_url . $parameter_string;
		//error_log($request);
		$request_handler = new http_request( $request , 'vehicle_management_system' );

		if( $this->tracer !== NULL ) {
			$this->request_stack[] = array( $request , $this->tracer );
			$this->tracer = NULL;
		} else {
			$this->request_stack[] = $request;
		}

		$data = $request_handler->cached() ? $request_handler->cached() : $request_handler->get_file();

		$this->parameters = array();
		return $data;
	}

	function process_parameters( $parameters ) {
		if( is_array($parameters) ){
			unset( $parameters[ 'taxonomy' ] );
			$parameters = ( !empty($parameters) ) ? array_map( 'urldecode' , $parameters ) : array();	
		}
		return !empty( $parameters ) ? '?' . http_build_query( $parameters , '' , '&' ) : false;
	}

}

?>
