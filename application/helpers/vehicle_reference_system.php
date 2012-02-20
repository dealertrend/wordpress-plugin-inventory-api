<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;
print_me( __FILE__ );

require_once( dirname( __FILE__ ) . '/application/helpers/http_request.php' );

class vehicle_reference_system {

	public $host = NULL;
	public $tracer = NULL;
	public $request_stack = array();

	private $url = NULL;
	private $parameters = array();

	public function __construct( $host , $country_code = 'US' ) {
print_me( __METHOD__ );
		$this->host = $host;
		$this->country_code = $country_code;
	}

	public function check_host() {
print_me( __METHOD__ );
		$this->url = $this->host;
		return $this;
	}

	public function check_feed() {
print_me( __METHOD__ );
		$this->url = $this->host . '/makes.json';
		return $this;
	}

	public function get_makes() {
print_me( __METHOD__ );
		$this->url = $this->host . '/makes.json';
		$this->parameters = array( 'country_code' => $this->country_code );
		return $this;
	}

	public function get_models() {
print_me( __METHOD__ );
		$this->url = $this->host . '/models.json';
		$this->parameters = array( 'country_code' => $this->country_code );
		return $this;
	}

	public function get_trims() {
print_me( __METHOD__ );
		$this->url = $this->host . '/trims.json';
		$this->parameters = array( 'api' => 2 , 'country_code' => $this->country_code );
		return $this;
	}

	public function get_trim_details( $acode ) {
print_me( __METHOD__ );
		$this->url = $this->host . '/trims/' . $acode . '.json';
		return $this;
	}

	public function get_videos( $acode ) {
print_me( __METHOD__ );
		$this->url = $this->host . '/extra_vehicle_infos.json';
		$this->parameters = array( 'acode' => $acode , 'api' => 2 );
		return $this;
	}

	public function get_fuel_economy( $acode ) {
print_me( __METHOD__ );
		$this->url = $this->host . '/fuel_economies.json';
		$this->parameters = array( 'acode' => $acode , 'api' => 2 );
		return $this;
	}

	public function get_reviews( $acode ) {
print_me( __METHOD__ );
		$this->url = $this->host . '/reviews.json';
		$this->parameters = array( 'acode' => $acode , 'api' => 2 );
		return $this;
	}

	public function get_review( $id ) {
print_me( __METHOD__ );
		$this->url = $this->host . '/review_titles/' . $id . '.json';
		return $this;
	}

	public function get_features( $acode ) {
print_me( __METHOD__ );
		$this->url = $this->host . '/acode_feature_datas.json';
		$this->parameters = array( 'acode' => $acode , 'api' => 2 );
		return $this;
	}

	public function get_equipment( $acode ) {
print_me( __METHOD__ );
		$this->url = $this->host . '/trim_equipments.json';
		$this->parameters = array( 'acode' => $acode , 'api' => 2 );
		return $this;
	}

	public function get_standard_equipment( $acode ) {
print_me( __METHOD__ );
		$this->url = $this->host . '/equipment/standard.json';
		$this->parameters = array( 'acode' => $acode );
		return $this;
	}

	public function get_options( $acode ) {
print_me( __METHOD__ );
		$this->url = $this->host . '/trim_options.json';
		$this->parameters = array( 'acode' => $acode , 'api' => 2 );
		return $this;
	}

	public function get_colors( $acode ) {
print_me( __METHOD__ );
		$this->url = $this->host . '/colorizations.json';
		$this->parameters = array( 'acode' => $acode , 'api' => 2 );
		return $this;
	}

	public function get_photos( $acode ) {
print_me( __METHOD__ );
		$this->url = $this->host . '/photos.json';
		$this->parameters = array( 'acode' => $acode , 'api' => 2 );
		return $this;
	}

	public function please( $parameters = array() ) {
print_me( __METHOD__ );
		$parameters = array_merge( $this->parameters , $parameters );
		$parameter_string = count( $parameters > 0 ) ? $this->process_parameters( $parameters ) : NULL;

		$request = $this->url . $parameter_string;
		$request_handler = new http_request( $request , 'vehicle_reference_system' );

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

	private function process_parameters( $parameters ) {
print_me( __METHOD__ );
		$parameters = array_map( 'urldecode' , $parameters );

		return !empty( $parameters ) ? '?' . http_build_query( $parameters , '' , '&' ) : false;
	}

}

?>
