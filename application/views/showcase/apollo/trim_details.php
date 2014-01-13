<?php

	$trims = get_trim_data( $vehicle_reference_system, $years, $make, $model, $year_filter, $this->options[ 'vehicle_reference_system' ][ 'data' ], $trim_year, $trim );

	usort( $trims, 'sort_trims' );
	$v_trims = array();
	$v_codes = array();
	build_variation_array_details( $trims, $v_trims, $v_codes );
	$clean_trim = str_ireplace('base','' , $trim );//Remove Base from trim

	echo '<div id="showcase-trim-details">';
	echo '<h2><a class="red-link" href="/showcase/">Showcase</a> &rsaquo; <a id="trim-detail-make" class="red-link" href="/showcase/'. $make .'/">' . $make . '</a> &rsaquo; <a id="trim-detail-model" class="red-link" href="/showcase/'. $make .'/' . $model .'/">' . $model . '</a> &rsaquo; <span id="trim-detail-year">' . $trim_year . '</span> <span id="trim-detail-trim">' . $clean_trim . '</span></h2>';
	echo '<hr />';
	//Trim - Left
	echo '<div id="showcase-trims-left">';
	//Trim - Variations
	echo '<div id="showcase-variations">';
		$active = true;
		$i = 0;
		foreach( $v_trims[ $trim ] as $var ){
			( $active == false ) ? $class = NULL : $class = 'active'; 
			$active = false;
			$i++;
			echo '<div id="trim-variation-' . $i . '" class="trim-detail-variation ' . $class . '">' . $var['drive_train'] . '<br>' . $var['cab_type'] . '</div>';
		}
	echo '<hr>';
	echo '</div>';
	//Trim - Details
	echo '<div id="showcase-detail">';
		$active = true;
		$i = 0;
		foreach( $v_trims[ $trim ] as $key => $value ){
			( $active == false ) ? $class = NULL : $class = 'active'; 
			$active = false;
			$i++;

			//Transmission
			echo '<div id="trim-variation-sub-' . $i . '" class="trim-transmissions trim-variation-'. $i . ' ' . $class . '" >';
			$active_sub = true;
			$i_s = 0;
			$a_count = count( $v_codes[ $i ] );
			( $a_count != 1 ) ? $class_width = 'half-width' : $class_width = 'full-width';
			foreach( $v_codes[ $i ] as $code ){
				( $active_sub == false ) ? $checked = NULL : $checked = 'checked';
				$active_sub = false;
				$i_s++;
				$transmission = get_transmission( $vehicle_reference_system, $code[ 'acode' ] );
				$bed_length = '';
				if( !empty($value['cab_type']) ){
					$bed_length = get_bed_length( $vehicle_reference_system, $code[ 'acode' ] );
					( $bed_length != 'Not Available' ) ? $bed_length = 'Bed Length: ' . $bed_length : $bed_length = '';
				}
				$name_variation = get_name_variation( $code[ 'name' ], $code[ 'name_v' ], $bed_length, $code[ 'body' ] );
				$trim_var_s = '<span class="trim-name-variation">' . $name_variation . '</span>';
				echo '<div class="trim-radio-wrapper ' . $class_width . '" >';
				echo '<input type="radio" name="trim-variation-sub-' . $i . '" value="trim-variation-sub-' . $i . '-'. $i_s .'" ' . $checked . ' class="' . $checked . '" /><span class="trim-transmission-value">' . ucwords( $transmission ) . '</span>' . $trim_var_s;
				echo '</div>';

			}
			echo '</div>';

			//Build All Trim Variations - 
			$i_s = 0;
			foreach( $v_codes[ $i ] as $code ){
				$i_s++;
				$sub_class = 'trim-variation-sub-' . $i . '-'. $i_s;
				echo '<div class="trim-detail '. $sub_class . ' ' . $class . '">';
					//Fuel Economy
					$fuel_economy = $vehicle_reference_system->get_fuel_economy( $code['acode'] )->please();
					if( isset( $fuel_economy[ 'body' ] ) ) {
						$fuel_economy = json_decode( $fuel_economy[ 'body' ] );
						usort( $fuel_economy , 'sort_fuel_economies' );
						$fuel_economy = $fuel_economy[ 0 ];
					} else {
						$fuel_economy = array();
					}

					//Colors - Image and Swatches
					$colors = $vehicle_reference_system->get_colors( $code['acode'] )->please();
					$colors = isset( $colors[ 'body' ] ) ? json_decode( $colors[ 'body' ] ) : array();

					//Detail - Top
					echo '<div class="trim-detail-top">';
						//Image Wrap
						echo '<div class="trim-detail-img-wrap">';
							$active_s = true;
							$count = 0;
							foreach( $colors as $color ) {
								if( isset( $color->image_urls ) && $color->type == 'Pri' ) {
									( $active_s == false ) ? $class = NULL : $class = 'active';
									$active_s = false;
									$count++;
									echo '<img src="' . make_transparent( $color->image_urls->medium ) . '" class="img-' . $color->code . ' ' . $class . '" />';
								}
							}
							if( $count == 0 ) {
								foreach( $trims as $value2){
									( $active_s == false ) ? $class = NULL : $class = 'active';
									$active_s = false;
									echo ( $key == $value2->acode ) ? '<img src="' . make_transparent( $value2->images->medium ) . '" class="'.$class.'" />' : '';
								}
							}
						echo '</div>';
						//Info Wrap
						echo '<div class="trim-detail-info-wrap">';
							echo !empty( $clean_trim ) ? '<span class="trim-name-text">Trim: '. $clean_trim . '</span>' : '<span class="trim-name-text"></span>' ;
							//echo '<span class="trim-year">' . $trim_year . '</span>';
							echo ( !empty($value['drive_train']) ) ? '<span class="trim-drive-train">' . $value['drive_train'] . '</span>' : '';
							echo ( !empty($code['body']) ) ? '<span class="trim-body-style">' . $code['body'] . '</span>' : '';
							echo ( !empty($value['cab_type']) && $code['body'] != $value['cab_type'] ) ? '<span class="trim-cab-type">' . $value['cab_type'] . '</span>' : '';
							echo '<div class="trim-price-wrap">';
							echo !empty( $code['msrp'] ) ? '<span class="trim-price-text">Starting At:</span><span class="trim-price-symbol">$</span><span class="trim-price-value">' . number_format( $code['msrp'] , 0 , '.' , ',' ) . '</span>' : '';
							echo '</div>';
							echo '<div class="trim-fuel-economy-wrap">';
								if( !empty( $fuel_economy ) ){
									if( $country_code == 'CA' ) {
										echo '<div class="fuel-city"><div class="fuel-label">CITY:</div><div class="fuel-number">' . $fuel_economy->city_lp_100km . '</div></div>';
										echo '<div class="fuel-icon"><img src="http://static.dealer.com/v8/tools/automotive/showroom/v4/images/white/mpg.gif" /></div>';
										echo '<div class="fuel-hwy"><div class="fuel-label">HWY:</div><div class="fuel-number">' . $fuel_economy->highway_lp_100km . '</div></div>';
									} else {
										echo '<div class="fuel-city"><div class="fuel-label">CITY:</div><div class="fuel-number">' . $fuel_economy->city_mpg . '</div></div>';
										echo '<div class="fuel-icon"><img src="http://static.dealer.com/v8/tools/automotive/showroom/v4/images/white/mpg.gif" /></div>';
										echo '<div class="fuel-hwy"><div class="fuel-label">HWY:</div><div class="fuel-number">' . $fuel_economy->highway_mpg . '</div></div>';
									}
									echo '<div class="fuel-disclaimer">Actual rating will vary with options, driving conditions, habits and vehicle condition.</div>';
								}
							echo '</div>';
						echo '</div>';
						//Color Swatches
						echo '<div class="color-swatches">';
							$active_s = true;
							$default_set = false;
							foreach( $colors as $color ) {
								if( ! empty( $color->file ) ) {
									if( ! in_array( $color->rgb , $colors ) ) {
										$type = $color->type == 'Pri' ? 'Exterior' : 'Interior';
										if( $type == 'Exterior' ) {
											$colors[] = $color->rgb;
											echo ( $default_set == false ) ? '<div class="color-text">Color: ' . $color->name . '</div>' : '';
											$default_set = true;
											( $active_s == false ) ? $class = NULL : $class = 'active'; 
											$active_s = false;
											echo '<a title="Color: ' . $color->name . '" name="' . $color->code . '" class="swatch swatch-' . $color->code . ' ' . $class . '" style="background-color:rgb(' . $color->rgb .')">';
											echo $color->name . '</a>';
										}
									}
								}
							}
						echo '</div>';
					echo '</div>';
					//Detail - Bottom
					echo '<div class="trim-detail-bottom">';
						//Equipment
						$equipment = $vehicle_reference_system->get_equipment( $code['acode'] )->please();
						$equipment = isset( $equipment[ 'body' ] ) ? json_decode( $equipment[ 'body' ] ) : array();
						usort( $equipment , 'sort_equipment' );
						$equipment_groups = array();
						$equipment_data = array();
						foreach( $equipment as $item ) {
							$equipment_data[ $item->group ][] = $item;
							if( ! in_array( $item->group , $equipment_groups ) ) {
								$equipment_groups[] = $item->group;
							}
						}
						//Photos
						$photos = $vehicle_reference_system->get_photos( $code['acode'] )->please( array( 'type' => 'standard' ) );
						$photos = isset( $photos[ 'body' ] ) ? json_decode( $photos[ 'body' ] ) : array();
						usort( $photos , 'sort_photos' );
						echo '<div class="trim-detail-tabs">';
							echo '<div title="video" class="detail-tab tab-video active " style="display:none;">Video</div>';
							echo '<div title="photos" class="detail-tab tab-photos ">Photos</div>';
							echo '<div title="equipment" class="detail-tab tab-equipment">Equipment</div>';
						echo '</div>';
						echo '<div class="trim-detail-tab-info">';
							echo '<div class="tab-info tab-info-photos active">';
								$shown = array();
								foreach( $photos as $photo ) {
									if( $photo->shot_id != 0 && ( $photo->category == 'Interior Standard' || $photo->category == 'Exterior Standard' ) ){
										if( ! in_array( $photo->filename , $shown ) ) {
											$shown[] = $photo->filename;
											echo '<img src="' . $photo->image_urls->small . '" />';
										}
									}
								}
							echo '</div>';

							echo '<div class="tab-info tab-info-equipment">';
							foreach( $equipment_groups as $group ) {
									echo '<div class="group">';
									echo '<h4>' . $group . '</h4>';
									echo '<ul class="">';
									foreach( $equipment_data[ $group ] as $data ) {
										echo '<li>' . $data->name;
										echo ! empty( $data->data ) ? ': ' . $data->data : NULL;
										echo '</li>';
									}
									echo '</ul>';
									echo '</div>';
								}
							echo '</div>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			}
		}
	echo '</div>';

	//Trim - Left:e
	echo '</div>';

	//Trim - Right:s
	echo '<div id="showcase-trims-right">';
		$active = true;
		$i = 0;
		$custom_form_message = '';
		foreach( $v_trims[ $trim ] as $key => $value ){
			$count_message = 'Currently found ' . $inventory_count . ' In-stock'; //Default
			$form_message = 'Inquire More Info'; //Default
			$i++;
			$i_s = 0;
			foreach( $v_codes[ $i ] as $code){
				( $active == false ) ? $class = NULL : $class = 'active';
				$active = false;
				$i_s++;
				$sub_class = 'trim-variation-sub-' . $i . '-'. $i_s;
				$variable = array( 'code' => $code['model_code'] );

				$inventory_count = get_similar_vehicles_count( $vehicle_management_system, $variable );
				get_custom_message( $count_message, $form_message, $custom_message, $inventory_count, $make, $model );
				echo '<div class="showcase-count-message ' . $sub_class . ' ' . $class . '">';
				echo $count_message;
				echo '</div>';

				if( !empty( $inventory_count ) ){
					echo '<div class="showcase-similar-link ' . $sub_class . ' ' . $class .'">';
					echo '<a href="/inventory/New/' . $make . '/' . $model .'/?saleclass=New&model_code=' . $code['model_code'] . '" title="Listings for ' . $make . ' ' . $model . ' ' . $code['name'] . '" >View All In-Stock</a>';
					echo '</div>';
				}

				if( empty($custom_form_message) ) { $custom_form_message = $form_message; }
				echo '<div style="display: none;" class="hidden-form-message ' . $sub_class . '">' . $form_message . '</div>';
			}
		}

		if( function_exists(gravity_form) ){
			echo '<div id="showcase-form-wrapper">';
			echo '<div class="trim-headline form-message">' . $custom_form_message . '</div>';
			echo '<div id="showcase-form">';
				gravity_form($form_id, false);
			echo '</div>';
			echo '</div>';
		}
	//Trim - Right:e
	echo '</div>';

	//Trim Bottom
	echo '<div id="showcase-trims-bottom">';
		if( !empty( $display_vms ) ) {
			$active = true;
			$i = 0;
			foreach( $v_trims[ $trim ] as $key => $value ){
				$i++;
				$i_s = 0;
				foreach( $v_codes[ $i ] as $code ){
					( $active == false ) ? $class = NULL : $class = 'active';
					$active = false;
					$i_s++;
					$sub_class = 'trim-variation-sub-' . $i . '-'. $i_s;
					$variable = array( 'code' => $code['model_code'] );

					$inventory = get_similar_vehicles( $vehicle_management_system, $variable );
					if( !empty( $inventory ) ){
						$added_param = '?saleclass=New&model_code=' . $code['model_code'];
						echo '<div class="showcase-trim-similar-wrapper ' . $sub_class . ' ' . $class . '">';
						echo '<div class="trim-headline">' . $model . '&#39;s In-Stock</div>';
						include( dirname( __FILE__ ) . '/vms_inventory.php' );
						echo '</div>';
					}
				}
			}
		}
	echo '</div>';

	echo '</div>';

	//Video Check
	if( !empty( $display_videos ) ){
		$video = get_trim_video( $vehicle_reference_system, $v_codes[1][0]['acode'], $custom_videos, $make, $model );
		if( !empty( $video ) ){
			echo '<div id="video-check" style="display: none;"><div class="tab-info tab-info-video video-container"><div class="video">' . $video . '</div></div></div>';
		}
	}

?>
