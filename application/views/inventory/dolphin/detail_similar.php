<?php
	if ( !empty( $inventory_sims ) ) {
		$sim_value = '<div id="dolphin-similar-vehicles">';
		$sim_value .= '<div id="dolphin-similar-title">Similar Vehicles</div>';
		$sim_value .= '<div id="dolphin-similar-items">';
		$sim_counter = 0;
		foreach( $inventory_sims as $inventory_sim):
			$sim_prices = $inventory_sim->prices;
			$sim_use_was_now = $sim_prices->{ 'use_was_now?' };
			$sim_use_price_strike_through = $sim_prices->{ 'use_price_strike_through?' };
			$sim_on_sale = $sim_prices->{ 'on_sale?' };
			$sim_sale_price = isset( $sim_prices->sale_price ) ? $sim_prices->sale_price : NULL;
			$sim_retail_price = $sim_prices->retail_price;
			$sim_default_price_text = $sim_prices->default_price_text;
			$sim_asking_price = $sim_prices->asking_price;

			$sim_vin = $inventory_sim->vin;
			$sim_stock_number = $inventory_sim->stock_number;
			$sim_year = $inventory_sim->year;
			$sim_make = urldecode( $inventory_sim->make );
			$sim_make_safe = str_replace( '/' , '%252' ,  $sim_make );
			$sim_model = urldecode( $inventory_sim->model_name );
			$sim_model_safe = str_replace( '/' , '%252' ,  $sim_model );
			$sim_trim = urldecode( $inventory_sim->trim );
			$sim_trim_safe = str_replace( '/' , '%252' ,  $sim_trim );
			$sim_thumbnail = urldecode( $inventory_sim->photos[ 0 ]->small );
			$sim_saleclass = $inventory_sim->saleclass;

			if( !empty( $wp_rewrite->rules ) ) {
				$sim_inventory_url = $site_url . '/inventory/' . $sim_year . '/' . $sim_make_safe . '/' . $sim_model_safe . '/' . $state . '/' . $city . '/'. $sim_vin . '/';
			} else {
				$sim_inventory_url = '?taxonomy=inventory&amp;saleclass=' . $sim_saleclass . '&amp;make=' . $sim_make_safe . '&amp;model=' . $sim_model_safe . '&amp;state=' . $state . '&amp;city=' . $city . '&amp;vin='. $sim_vin;
			}

			$sim_generic_vehicle_title = $sim_year . ' ' . $sim_make . ' ' . $sim_model;

			// AIS Info
			$sim_ais_incentive = isset( $inventory_sim->ais_incentive->to_s ) ? $inventory_sim->ais_incentive->to_s : NULL;
			$sim_incentive_price = 0;
			if( $sim_ais_incentive != NULL ) {
				preg_match( '/\$\d*(\s)?/' , $sim_ais_incentive , $sim_incentive );
				$sim_incentive_price = isset( $sim_incentive[ 0 ] ) ? str_replace( '$' , NULL, $sim_incentive[ 0 ] ) : 0;
			}

			if( $sim_on_sale && $sim_sale_price > 0 ) {
				if( $sim_incentive_price > 0 ) {
					$sim_main_price = '<div class="dolphin-similar-price">Price $' . number_format( $sim_sale_price - $sim_incentive_price , 0 , '.' , ',' ) . '</div>';
				} else {
					$sim_main_price = '<div class="dolphin-similar-price">Price $' . number_format( $sim_sale_price , 0 , '.' , ',' ) . '</div>';
				}
			} else {
				if( $sim_asking_price > 0 ) {
					if( $sim_incentive_price > 0 ) {
						$sim_main_price = '<div class="dolphin-similar-price">Price $' . number_format( $sim_asking_price - $sim_incentive_price , 0 , '.' , ',' ) . '</div>';
					} else {
						$sim_main_price = '<div class="dolphin-similar-price">Price $' . number_format( $sim_asking_price , 0 , '.' , ',' ) . '</div>';
					}
				} else {
					$sim_main_price = '<div class="dolphin-similar-price">' . $sim_default_price_text . '</div>';
				}
			}

			if ( $vin != $sim_vin && $sim_counter < 3 ) {
				$sim_counter = $sim_counter + 1;
				// Similar Start
				$sim_value .= '<div class="dolphin-similar-item">';
				// Similar Headline
				$sim_value .= '<div class="dolphin-similar-headline">';
				$sim_value .= '<span class="dolphin-similar-saleclass">' . $sim_saleclass . '</span>';
				$sim_value .= '<a href="' . $sim_inventory_url . '" title="' . $sim_generic_vehicle_title . '" >';
				$sim_value .= '<span class="dolphin-similar-make">' . $sim_make . '</span>';
				$sim_value .= '<span class="dolphin-similar-make">' . $sim_model . '</span>';
				$sim_value .= '</a></div>';
				// Similar Photo
				$sim_value .= '<div class="dolphin-similar-column-left">';
				$sim_value .= '<div class="dolphin-similar-photo">';
				$sim_value .= '<a href="' . $sim_inventory_url . '" title="' . $sim_generic_vehicle_title . '">';
				$sim_value .= '<img src="' . $sim_thumbnail . '" alt="' . $sim_generic_vehicle_title . '" title="' . $sim_generic_vehicle_title . '" />';
				$sim_value .= '</a></div></div>';
				// Similar Info
				$sim_value .= '<div class="dolphin-similar-column-right">';
				$sim_value .= '<div class="dolphin-similar-info">';
				$sim_value .= '<div class="dolphin-similar-details">';
				$sim_value .= '<span class="dolphin-similar-stock-number">Stock #: ' . $sim_stock_number . '</span>';
				$sim_value .= '<span class="dolphin-similar-vin">VIN: ' . $sim_vin . '</span>';
				$sim_value .= '<span class="dolphin-similar-year">Year: ' . $sim_year . '</span>';
				$sim_value .= '<span class="dolphin-similar-trim">Trim: ' . $sim_trim . '</span>';
				$sim_value .= '</div>';
				$sim_value .= '<div class="dolphin-similar-price">';
				$sim_value .= $sim_main_price;
				$sim_value .= '</div>';
				$sim_value .= '</div></div>';
				// Similar Button
				$sim_value .= '<div class="dolphin-similar-button">';
				$sim_value .= '<a href="' . $sim_inventory_url . '" title="More Information: "' . $sim_generic_vehicle_title . '">More Information</a>';
				$sim_value .= '</div>';
				// Similar End
				$sim_value .= '</div>';
			}
		endforeach;
		$sim_value .= '</div></div>';
		echo $sim_value;
	}
?>
