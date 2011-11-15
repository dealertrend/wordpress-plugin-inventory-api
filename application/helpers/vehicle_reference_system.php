<?php

namespace WordPress\Plugins\DealerTrend\InventoryAPI;

class vehicle_reference_system {

	public $host = NULL;
	public $tracer = NULL;
	public $request_stack = array();

	private $url = NULL;
	private $parameters = array();

	public function __construct( $host ) {
		$this->host = $host;
		$this->create_sidebar();
	}

	function create_sidebar() {
		add_filter( 'widget_text' , 'do_shortcode' );
		register_sidebar(array(
			'name' => 'Showcase Trim Page',
			'id' => 'showcase-trim-page',
			'description' => 'Widgets in this area will show up on the trim page within Showcase.',
			'before_title' => '<h1>',
			'after_title' => '</h1>',
			'before_widget' => '<div class="showcase widget">',
			'after_widget' => '</div>'
		));
	}

	public function check_host() {
		$this->url = $this->host;
		return $this;
	}

	public function check_feed() {
		$this->url = $this->host . '/makes.json';
		return $this;
	}

	public function get_makes() {
		$this->url = $this->host . '/makes.json';
		return $this;
	}

	public function get_models() {
		$this->url = $this->host . '/models.json';
		return $this;
	}

	public function get_trims() {
		$this->url = $this->host . '/trims.json';
		$this->parameters = array( 'api' => 2 );
		return $this;
	}

	public function get_trim_details( $acode ) {
		$this->url = $this->host . '/trims/' . $acode . '.json';
		return $this;
	}

	public function get_videos( $acode ) {
		$this->url = $this->host . '/extra_vehicle_infos.json';
		$this->parameters = array( 'acode' => $acode , 'api' => 2 );
		return $this;
	}

	public function get_fuel_economy( $acode ) {
		$this->url = $this->host . '/fuel_economies.json';
		$this->parameters = array( 'acode' => $acode , 'api' => 2 );
		return $this;
	}

	public function get_reviews( $acode ) {
		$this->url = $this->host . '/reviews.json';
		$this->parameters = array( 'acode' => $acode , 'api' => 2 );
		return $this;
	}

	public function get_review( $id ) {
		$this->url = $this->host . '/review_titles/' . $id . '.json';
		return $this;
	}

	public function get_features( $acode ) {
		$this->url = $this->host . '/acode_feature_datas.json';
		$this->parameters = array( 'acode' => $acode , 'api' => 2 );
		return $this;
	}

	public function get_equipment( $acode ) {
		$this->url = $this->host . '/trim_equipments.json';
		$this->parameters = array( 'acode' => $acode , 'api' => 2 );
		return $this;
	}

	public function get_standard_equipment( $acode ) {
		$this->url = $this->host . '/equipment/standard.json';
		$this->parameters = array( 'acode' => $acode );
		return $this;
	}

	public function get_options( $acode ) {
		$this->url = $this->host . '/trim_options.json';
		$this->parameters = array( 'acode' => $acode , 'api' => 2 );
		return $this;
	}

	public function get_colors( $acode ) {
		$this->url = $this->host . '/colorizations.json';
		$this->parameters = array( 'acode' => $acode , 'api' => 2 );
		return $this;
	}

	public function get_photos( $acode ) {
		$this->url = $this->host . '/photos.json';
		$this->parameters = array( 'acode' => $acode , 'api' => 2 );
		return $this;
	}

	public function please( $parameters = array() ) {
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
		$parameters = array_map( 'urldecode' , $parameters );

		return !empty( $parameters ) ? '?' . http_build_query( $parameters , '' , '&' ) : false;
	}

}

?>
