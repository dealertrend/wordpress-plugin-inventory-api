<?php

class ajax_widgets {

	function __construct() {

		$this->load_options();

		$this->vms = new Wordpress\Plugins\Dealertrend\Inventory\Api\vehicle_management_system(
			$this->options[ 'vehicle_management_system' ][ 'host' ],
			$this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ]
		);

		$this->filtered_makes = $this->options[ 'vehicle_management_system' ][ 'data' ][ 'makes_new' ];

	}

	function load_options() {
		$this->options = get_option( 'dealertrend_inventory_api' );
	}

	function ajax_widgets_handle_request(){

		switch($_REQUEST['fn']){
			case 'getSearchBox':
				switch($_REQUEST['data']['id']){
					case 'sc':
						$output = $this->ajax_widgets_saleclass_change($_REQUEST['data']);
						break;
					case 'makes':
						$output['models'] = $this->ajax_widgets_get_models($_REQUEST['data']['sc'],$_REQUEST['data']['makes']);
						if( count( $output['models'] ) == 1 ){
							$output['trims'] = $this->ajax_widgets_get_trims($_REQUEST['data']['sc'],$_REQUEST['data']['makes'],$output['models'][0] );
						}
						break;
					case 'models':
						$output['trims'] = $this->ajax_widgets_get_trims($_REQUEST['data']['sc'],$_REQUEST['data']['makes'],$_REQUEST['data']['models'] );
						break;
					case 'trims':
						break;
				}
				break;
			default:
				$output = 'That is not a valid FN parameter. Please check your string and try again.';
				break;
		}

		$output = json_encode($output);
		if(is_array($output)){
			echo $output;
			wp_die();
		}else{
			echo $output;
			wp_die();
		}


	}

	function ajax_widgets_saleclass_change( $data ){
		$output = array();
		$output['makes'] = $this->ajax_widgets_get_makes( $data['sc'] );

		if( count( $output['makes'] ) == 1 ){
			$output['models'] = $this->ajax_widgets_get_models( $data['sc'], $output['makes'][0] );
		}

		return $output;
	}

	function ajax_widgets_get_makes( $sc = 'New' ){

		if( empty($this->filtered_makes) || strtolower($sc) == 'used' ) {

			$this->vms->tracer = 'Getting Makes for Widget Call';

			$makes = $this->vms->get_makes()->please( array( 'saleclass' => $sc ) );
			$makes = ( !empty($makes[ 'body' ]) ? json_decode( $makes[ 'body' ] ) : array() );

			foreach( $makes as $make ){
				$clean = str_replace( '/' , '%2F' , $make );
				$data[] = $clean;
			}

		} else {
			$data = $this->filtered_makes;
		}

		return $data;
	}

	function ajax_widgets_get_models( $sc = 'New', $make = '' ){

		if( empty($make) ){
			return;
		}

		$models = $this->vms->get_models()->please( array( 'saleclass' => $sc, 'make' => $make ) );
		$models = ( !empty($models[ 'body' ]) ? json_decode( $models[ 'body' ] ) : array() );

		foreach( $models as $model ){
			$clean = str_replace( '/', '%2F', $model);
			$data[] = $clean;
		}

		return $data;

	}

	function ajax_widgets_get_trims( $sc = 'New', $make = '', $model = '' ){

		if( empty($make) || empty($model) ){
			return;
		}

		$trims = $this->vms->get_trims()->please( array( 'saleclass' => $sc , 'make' => $make , 'model' => $model ) );
		$trims = ( !empty($trims[ 'body' ]) ? json_decode( $trims[ 'body' ] ) : array() );

		foreach( $trims as $trim ){
			$clean = str_replace( '/', '%2f', $trim);
			$data[] = $clean;
		}

		return $data;

	}

}

?>
