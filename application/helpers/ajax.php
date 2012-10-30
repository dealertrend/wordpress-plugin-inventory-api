<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

class ajax {

	public $parameters = array();

	function __construct( $parameters , $instance ) {

		$this->parameters = $parameters;
		$this->check_security();

		$meta = isset( $parameters[ 'meta-taxonomy' ] ) ? $parameters[ 'meta-taxonomy' ] : false;
		$make = isset( $parameters[ 'make' ] ) ? urldecode( $parameters[ 'make' ] ) : false;
		$model = isset( $parameters[ 'model' ] ) ? urldecode( $parameters[ 'model' ] ) : false;
		$trim = isset( $parameters[ 'trim' ] ) ? urldecode( $parameters[ 'trim' ] ) : false;
		$acode = isset( $parameters[ 'acode' ] ) ? urldecode( $parameters[ 'acode' ] ) : false;
		$mode = isset( $parameters[ 'mode' ] ) ? urldecode( $parameters[ 'mode' ] ) : 'default';
		$type = ( $make == false ) ? 'makes' : ( ( $model == false ) ? 'models' : 'trims' );
		$country_code = $parameters[ 'country_code' ] ? $parameters[ 'country_code' ] : 'US';

		$current_year = date( 'Y' );
		$last_year = $current_year - 1;
		$next_year = $current_year + 1;

		if( $meta == 'showcase' ) {
			$json = array();
			$vehicle_reference_system = new vehicle_reference_system( $instance->options[ 'vehicle_reference_system' ][ 'host' ] , $country_code );
			if( $type == 'trims' ) {
				if( $mode == 'default' ) {
					$trim_data[ $last_year ] = $vehicle_reference_system->get_trims()->please( array( 'make' => $make , 'model_name' => $model , 'year' => $last_year , 'api' => 2 ) );
					$trim_data[ $last_year ] = isset( $trim_data[ $last_year ][ 'body' ] ) ? json_decode( $trim_data[ $last_year ][ 'body' ] ) : NULL;
					$trim_data[ $current_year ] = $vehicle_reference_system->get_trims()->please( array( 'make' => $make , 'model_name' => $model , 'year' => $current_year , 'api' => 2 ) );
					$trim_data[ $current_year ] = isset( $trim_data[ $current_year ][ 'body' ] ) ? json_decode( $trim_data[ $current_year ][ 'body' ] ) : NULL;
					$trim_data[ $next_year ] = $vehicle_reference_system->get_trims()->please( array( 'make' => $make , 'model_name' => $model , 'year' => $next_year , 'api' => 2 ) );
					$trim_data[ $next_year ] = isset( $trim_data[ $next_year ][ 'body' ] ) ? json_decode( $trim_data[ $next_year ][ 'body' ] ) : NULL;
					$trim_data[ $last_year ] = is_array( $trim_data[ $last_year ] ) ? $trim_data[ $last_year ] : array();
					$trim_data[ $current_year ] = is_array( $trim_data[ $current_year ] ) ? $trim_data[ $current_year ] : array();
					$trim_data[ $next_year ] = is_array( $trim_data[ $next_year ] ) ? $trim_data[ $next_year ] : array();
					$trims = array_merge( $trim_data[ $next_year ] , $trim_data[ $current_year ] , $trim_data[ $last_year ] );
					usort( $trims , array( &$this , 'sort_trims' ) );

					$trim = str_replace( '_' , '/' , $trim );

					foreach( $trims as $trim_data ) {
						if( $trim_data->acode == $trim ) {
							$json[] = $trim_data;
						}
					}
				} elseif( $mode == 'fuel_economy' ) {
					$data = $vehicle_reference_system->get_fuel_economy( $acode )->please();
					$json = isset( $data[ 'body' ] ) ? json_decode( $data[ 'body' ] ) : NULL;
					usort( $json , array( &$this , 'sort_fuel_economies' ) );
				} elseif( $mode == 'colors' ) {
					$colors = $vehicle_reference_system->get_colors( $acode )->please();
					$colors = isset( $colors[ 'body' ] ) ? json_decode( $colors[ 'body' ] ) : NULL;
					foreach( $colors as $color ) {
						if( isset( $color->file ) && $color->file != NULL && $color->type == 'Pri' ) {
							$json[] = $color;
						}
					}
				} elseif( $mode == 'equipment' ) {
				} elseif( $mode == 'photos' ) {
					$data = $vehicle_reference_system->get_photos( $acode )->please( array( 'type' => 'oem_exterior_standard' ) );
					$json = isset( $data[ 'body' ] ) ? json_decode( $data[ 'body' ] ) : array();
				} elseif( $mode == 'reviews' ) {
					$data = $vehicle_reference_system->get_reviews( $acode )->please();
					$json = isset( $data[ 'body' ] ) ? json_decode( $data[ 'body' ] ) : array();
					foreach( $json as $key => $value ) {
						if( $json->titles->titles != 'LIKED_MOST' ) {
							unset( $json[ $key ] );
						}
					}
					$index = mt_rand( 0 , count( $json ) - 1 );
					$json = $json[ $index ];
					$review = $vehicle_reference_system->get_review( $options[ $index ] )->please();

				}
				echo json_encode( $json );
			}
		}
	}

		function check_security() {
			$nonce = isset( $_REQUEST['_ajax_nonce'] ) ? $_REQUEST['_ajax_nonce'] : false;

			if( ! wp_verify_nonce( $nonce , 'ajax!' ) ) {
				status_header( '403' );
				die('<img src="http://www.schlagging.com/storage/dennis_nedry.gif" /><br /><a href="http://nedry.ytmnd.com/">You didn\'t say the magic word...!</a>');
			}
		}

		function sort_trims( $a , $b ) {
				if( $a->msrp == 0 ) {
						return 1;
				}
				if( $b->msrp == 0 ) {
						return 0;
				}
				if( $a->year >= $b->year ) {
						if( $a->year == $b->year && $a->msrp > $b->msrp ) {
								return 1;
						}
						return -1;
				} else {
						return 1;
				}
		}

	function sort_fuel_economies( $a , $b ) {
		$a_sum = $a->city_mpg + $a->highway_mpg;
		$b_sum = $b->city_mpg + $b->highway_mpg;
		if( $a_sum > $b_sum ) {
			return -1;
		} elseif( $a_sum < $b_sum ) {
			return 1;
		}
		return 0;
	}


}

?>
