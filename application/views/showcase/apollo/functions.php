<?php

	/*******************
		get_data
	*******************/

	function get_valid_years( $filter ){

		$current_year = date( 'Y' );

		switch( $filter ){
			case 0: // Default Next, Current and Past Year
				$value = array( ($current_year + 1), $current_year, ($current_year - 1) );
				break;
			case 1: // Current Year Only
				$value = array( $current_year );
				break;
			case 2: // Manual Next and Current Year
				$value = array( ($current_year + 1), $current_year );
				break;
		}

		return $value;
	}

	function get_make_array( $vrs_object, $years ){
		$value = array();
		foreach( $years as $year ){
			$temp = $vrs_object->get_makes()->please( array( 'year' => $year ) );
			$temp = isset( $temp[ 'body' ] ) ? json_decode( $temp[ 'body' ] ) : array();
			$value = array_merge( $value, $temp );
		}

		return $value;
	}

	function get_model_array( $vrs_object, $years, $make, $filter, $options ){
		$value = array();
		foreach( $years as $year ){
			$temp = $vrs_object->get_models()->please( array( 'make' => $make , 'year' => $year ) );
			$temp = isset( $temp[ 'body' ] ) ? json_decode( $temp[ 'body' ] ) : array();
			if( !empty( $temp ) ) {
				foreach( $temp as $item ) {
					if( !in_array_r( $item->name, $value ) ) {
						if( check_selected_models( $item->name, $filter, $year, $options ) ){
							$value[] = array( 'name' => $item->name, 'class' => $item->classification, 'year' => $year, 'img' => $item->image_urls->small );
						}
					}
				}
			} 
		}

		return $value;
	}

	function get_trim_data( $vrs_object, $years, $make, $model, $filter, $options, &$trim_year, $trim = '' ){
		$value = array();
		foreach( $years as $year ){
			if( check_selected_models( $model, $filter, $year, $options ) ){
				if( empty($trim) ){
					$temp = $vrs_object->get_trims()->please( array( 'make' => $make , 'model_name' => $model , 'year' => $year , 'api' => 2 ) );
				} else {
					$temp = $vrs_object->get_trims()->please( array( 'make' => $make , 'model_name' => $model , 'year' => $year , 'name' => urlencode($trim) , 'api' => 2 ) );
				}
				$temp = isset( $temp[ 'body' ] ) ? json_decode( $temp[ 'body' ] ) : array();
				if( !empty($temp) ){
					$value = $temp;
					$trim_year = $year;
					break;
				}
			} 
		}

		return $value;
	}

	function get_custom_message( &$count_message, &$form_message, $data, $count, $val_make, $val_model ){
		if( !empty($data) ){
			usort( $data, 'sort_custom_message' );
			foreach( $data as $value ){
				switch ($value['count_operator']) {
					case '>':
						$count > $value['count'] ? $match = true : $match = false ;
						break;
					case '>=':
						$count >= $value['count'] ? $match = true : $match = false ;
						break;
					case '<':
						$value['count'] < $count ? $match = true : $match = false ;
						break;
					case '<=':
						$value['count'] <= $count ? $match = true : $match = false ;
						break;
					case '=':
						$value['count'] == $count ? $match = true : $match = false ;
						break;
					case '!=':
						$value['count'] != $count ? $match = true : $match = false ;
						break;
				}

				if( $match ) {
					!empty( $value['message'] ) ? $new_message = $value['message'] : $new_message = '';
					!empty( $value['form_title'] ) ? $new_form_title = $value['form_title'] : $new_form_title = '';

					$search_a = array( "[count]", "[make]", "[model]" );
					$replace_a = array( $count, $val_make, $val_model);

					$new_message = str_replace( $search_a, $replace_a, $new_message );
					$new_form_title = str_replace( $search_a, $replace_a, $new_form_title );

					if( !empty( $new_message ) ) { $count_message = $new_message; }
					if( !empty( $new_form_title ) ) { $form_message = $new_form_title; }

					break;
				}
			}
		}
	}

	function get_similar_vehicles( $object, $variables ){

		$data = array();
		if( empty($variables[ 'code' ]) ){
			$sim_array = array( 'search_sim' => 1 , 'photo_view' => 1 , 'saleclass' => 'New' , 'make' => $variables[ 'make' ], 'model' => $variables[ 'model' ] );
		} else {
			$sim_array = array( 'search_sim' => 1 , 'photo_view' => 1 , 'saleclass' => 'New' , 'model_code' => $variables[ 'code' ] );
		}

		$object->tracer = 'Obtaining Similar Vehicles - Showcase';
		$data = $object->get_inventory()->please( $sim_array );
		$data = isset( $data[ 'body' ] ) ? json_decode( $data[ 'body' ] ) : array();

		return $data;

	}

	function get_similar_vehicles_count( $object, $variables ){

		$data = array();
		if( empty($variables[ 'code' ]) ){
			$sim_array = array( 'search_sim' => 1 , 'photo_view' => 1 , 'per_page' => 1 , 'saleclass' => 'New' , 'make' => $variables[ 'make' ], 'model' => $variables[ 'model' ] );
		} else{
			$sim_array = array( 'search_sim' => 1 , 'photo_view' => 1 , 'per_page' => 1 , 'saleclass' => 'New' , 'model_code' => $variables[ 'code' ] );
		}

		$object->tracer = 'Obtaining Similar Vehicles - Showcase';
		$data = $object->get_inventory()->please( $sim_array );
		$data = isset( $data[ 'body' ] ) ? json_decode( $data[ 'body' ] ) : array();

		$data_count = isset( $data[ 0 ]->pagination->total ) ? $data[ 0 ]->pagination->total : 0;

		return $data_count;

	}

	function get_transmission( $object, $key ){
		$value = '';
		$data = $object->get_equipment( $key )->please();
		$data = isset( $data[ 'body' ] ) ? json_decode( $data[ 'body' ] ) : array();
		foreach( $data as $item ){
			if( strtolower($item->group) == 'powertrain' && strtolower($item->name) == 'transmission' ){
				$value = $item->data;
				break;
			}
		}

		return $value;
	}

	function get_bed_length( $object, $key ){
		$value = '';
		$data = $object->get_features( $key )->please();
		$data = isset( $data[ 'body' ] ) ? json_decode( $data[ 'body' ] ) : array();
		foreach( $data as $item ){
			if( strtolower($item->row_group) == 'header' && strtolower($item->row_title) == 'bed length' ){
				$value = $item->row_data;
				break;
			}
		}

		return $value;
	}

	function get_trim_video( $object, $key, $data, $mk, $md ){
		$video = '';

		if( !empty( $data ) ){
			foreach( $data as $value ){
				if( strtolower( $value['make'] ) == strtolower( $mk ) && strtolower( $value['model'] ) == strtolower( $md )  ){
					$video_link = $value['url'];
					$type = wp_check_filetype( $video_link, wp_get_mime_types() );
					if( empty( $type['ext'] ) ){
						$video = '<iframe id="showcase-video-iframe" src="'.$video_link.'?feature=player_detailpage" frameborder="0" allowfullscreen></iframe>';
					} else {
						$attr = array( 'src' => $video_link );
						$video = wp_video_shortcode($attr);
					}
					break;
				}
			}
		}

		if( empty($video) ){
			$videos = $object->get_videos( $key )->please();
			if( isset( $videos[ 'response' ][ 'code' ] ) && $videos[ 'response' ][ 'code' ] == 200 ) {
				$videos = json_decode( $videos[ 'body' ] );
				if( $videos != false && (isset($videos[ 0 ]->filename) && !empty($videos[ 0 ]->filename) ) ) {
					$video_link = $videos[ 0 ]->flash_video_url;
					$attr = array( 'src' => $video_link );
					$video = wp_video_shortcode($attr);
				}
			}
		}

		return $video;
	}

	function get_name_variation( $name, $name_var, $bed, $body ){
		$value = '';

		if( $name != $name_var && empty($bed) ){
			$value = ucwords( $name_var );
		} elseif( $name != $name_var ) {
			$value = ucwords( $name_var ) . ' - ' . $bed;
		}

		( empty($value) && !empty($bed) )? $value = $bed : $value = $body;

		return $value;
		
	}

	/*******************
		helpers
	*******************/

	function build_array_trims( $data, &$a_trims ){
		if( !empty( $data ) ){
			foreach( $data as $value ){
				if( !in_array_r( $value->name, $a_trims ) ){
					$a_trims[] = array( 'name' => $value->name, 'msrp' => $value->msrp, 'image' => $value->images->medium );
				}
			}
		}
	}

	function build_variation_array_trims( $data, &$v_trims ){
		if( !empty( $data ) ){
			foreach( $data as $value){
				if( empty($v_trims[$value->name]) ) {
					$v_trims[$value->name][] = array( 'drive_train' => $value->drive_train, 'cab_type' => $value->oem_cab_type  );
				} else { 
					if( !in_array(array( 'drive_train' => $value->drive_train, 'cab_type' => $value->oem_cab_type  ), $v_trims[$value->name]) ){
						$v_trims[$value->name][] = array( 'drive_train' => $value->drive_train, 'cab_type' => $value->oem_cab_type  );
					}
				}
			}
		}
	}

	function build_variation_array_details( $data, &$v_trims, &$v_codes ){
		if( !empty( $data ) ){
			$i = 0;
			$temp = array();
			$current_value = array();
			//Trim Variations -
			foreach( $data as $value){
				if( empty($v_trims[$value->name]) ) {
					$i++;
					$v_trims[$value->name][$value->acode] = array( 'drive_train' => $value->drive_train, 'cab_type' => $value->oem_cab_type );
					$temp[ $i ] = array( 'drive_train' => $value->drive_train, 'cab_type' => $value->oem_cab_type );
				} else { 
					if( !in_array(array( 'drive_train' => $value->drive_train, 'cab_type' => $value->oem_cab_type  ), $v_trims[$value->name]) ){
						$i++;
						$v_trims[$value->name][$value->acode] = array( 'drive_train' => $value->drive_train, 'cab_type' => $value->oem_cab_type );
						$temp[ $i ] = array( 'drive_train' => $value->drive_train, 'cab_type' => $value->oem_cab_type );
					}
				}
			}
			//Trim Acode/Model Code Variations -
			foreach( $temp as $key => $value ){
				foreach( $data as $value2 ){
					if( $value['drive_train'] == $value2->drive_train && $value['cab_type'] == $value2->oem_cab_type ){
						$v_codes[$key][] = array( 'acode' => $value2->acode, 'model_code' => $value2->mfg_code, 'msrp' => $value2->msrp, 'name' => $value2->name, 'name_v' => trim( preg_replace( '/\([^)]+\)/i', '', $value2->name_variation ) ), 'body' => $value2->body_style );
					}
				}
			}
		}
	}

	function check_selected_models( $item, $filter, $year, $options ){
		$check = false;
		switch( $filter ){
			case 2: // Manual Check
				if( $year != date( 'Y' ) ){
					( in_array( str_replace( '&', '&amp;', $item ), $options[ 'models_manual' ][ 'next' ] ) ) ? $check = true : $check = false;
				} else {
					( in_array( str_replace( '&', '&amp;', $item ), $options[ 'models_manual' ][ 'current' ] ) ) ? $check = true : $check = false;
				}
				break;

			default: // Default Check
				( in_array( str_replace( '&', '&amp;', $item ), $options[ 'models' ] ) ) ? $check = true : $check = false;
				break;
		}

		return $check;

	}

	function in_array_r($needle, $haystack, $strict = false) {
		foreach ($haystack as $item) {
		    if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
		        return true;
		    }
		}

		return false;
	}

	function make_transparent( $url ) {
		return preg_replace( '/IMG=(.*)\.\w{3,4}/i', 'IMG=\1.png' , $url );
	}

	/*******************
		sorts
	*******************/
	function sort_makes( $a , $b ) {
		return ( strtolower( $a->name ) < strtolower( $b->name ) ) ? -1 : 1;
	}

	function sort_models( $a , $b ) {
		if( $a['class'] == $b['class'] ) {
			if( $a['name'] > $b['name'] ) {
				return +1;
			} else {
				return -1;
			}
			return 0;
		}
		return ( $a['class'] > $b['class'] ) ? +1 : -1;
	}

	function sort_custom_message( $a , $b ) {
		return ( $a->count > $b->count ) ? +1 : -1;
	}

	function sort_trims( $a , $b ) {
		return ( $a->msrp > $b->msrp ) ? +1 : -1;
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

	function sort_equipment( $a , $b ) {
		return ( $a->group > $b->group ) ? +1 : -1;
	}

	function sort_photos( $a , $b ) {
		if( $a->shot_id > $b->shot_id ) {
			return 1;
		}
		if( $a->shot_id == $b->shot_id ) {
			return -1;
		}
	}

?>
