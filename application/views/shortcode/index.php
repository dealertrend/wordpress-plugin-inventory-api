<?php

	global $wp_rewrite;

	$vehicle_management_system->tracer = 'Obtaining requested sc inventory.';
	$inventory_information = $vehicle_management_system->get_inventory()->please( $sc_atts );
	$inventory = isset( $inventory_information[ 'body' ] ) ? json_decode( $inventory_information[ 'body' ] ) : false;

	$company_information = json_decode( $company_information[ 'body' ] );
	$state = $company_information->seo->state;
	$city = $company_information->seo->city;

	$site_url = site_url();

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );

	if( strtolower($sc_style) != 'clear' ){
		wp_enqueue_style(
			'dealertrend-shortcode-style',
			$this->plugin_information[ 'PluginURL' ] . '/application/views/shortcode/css/sc_' . $sc_style . '.css',
			false,
			1.0
		);
	}

	$sc_content = '<div id="sc-inventory-wrapper" >';

	if( empty( $inventory ) ) {
		$sc_content .= '<div class="sc-not-found"><h2><strong>Shortcode criteria did not return any results.</strong></h2></div>';
	} else {
		foreach( $inventory as $inventory_item ){
			$prices = $inventory_item->prices;
			$use_was_now = $prices->{ 'use_was_now?' };
			$use_price_strike_through = $prices->{ 'use_price_strike_through?' };
			$on_sale = $prices->{ 'on_sale?' };
			$sale_price = isset( $prices->sale_price ) ? $prices->sale_price : NULL;
			$sale_expire = isset( $prices->sale_expire ) ? $prices->sale_expire : NULL;
			$retail_price = $prices->retail_price;
			$default_price_text = $prices->default_price_text;
			$asking_price = $prices->asking_price;
			$year = $inventory_item->year;
			$make = urldecode( $inventory_item->make );
			$make_safe = str_replace( '/' , '%2F' ,  $make );
			$model = urldecode( $inventory_item->model_name );
			$model_safe = str_replace( '/' , '%2F' ,  $model );
			$vin = $inventory_item->vin;
			$trim = urldecode( $inventory_item->trim );
			$trim_safe = str_replace( '/' , '%2F' ,  $trim );
			$engine = $inventory_item->engine;
			$transmission = $inventory_item->transmission;
			$exterior_color = $inventory_item->exterior_color;
			$interior_color = $inventory_item->interior_color;
			$stock_number = $inventory_item->stock_number;
			$odometer = $inventory_item->odometer;
			$icons = $inventory_item->icons;
			$thumbnail = urldecode( $inventory_item->photos[ 0 ]->small );
			$body_style = $inventory_item->body_style;
			$drive_train = $inventory_item->drive_train;
			$doors = $inventory_item->doors;
			$headline = $inventory_item->headline;
			$saleclass = $inventory_item->saleclass;
			$certified = $inventory_item->certified;

			if( !empty( $wp_rewrite->rules ) ) {
				$inventory_url = $site_url . '/inventory/' . $year . '/' . $make_safe . '/' . $model_safe . '/' . $state . '/' . $city . '/'. $vin . '/';
			} else {
				$inventory_url = '?taxonomy=inventory&amp;saleclass=' . $sale_class . '&amp;make=' . $make_safe . '&amp;model=' . $model_safe . '&amp;state=' . $state . '&amp;city=' . $city . '&amp;vin='. $vin;
			}

			//wrapper -s
			$sc_content .= '<div class="sc-item-wrapper"><a href="'.$inventory_url.'">';
			//title
			$sc_content .= '<div class="sc-title-wrapper">';
			$sc_content .= '<span class="sc-year">' . $year . '</span>';
			$sc_content .= '<span class="sc-make">' . $make_safe . '</span>';
			$sc_content .= '<span class="sc-model">' . $model_safe . '</span>';
			$sc_content .= '</div>';
			//image
			$sc_content .= '<div class="sc-img-wrapper">';
			$sc_content .= '<img src="'.$thumbnail.'" />';
			$sc_content .= '</div>';
			//price
			$primary_price = 0;
			if( $on_sale && $sale_price > 0 ) {
				if( $incentive_price > 0 ) {
					$primary_price = $sale_price - $incentive_price;
				} else {
					$primary_price = $sale_price;
				}
			} else {
				if( $asking_price > 0 ) {
					if( $incentive_price > 0 ) {
						$primary_price = $asking_price - $incentive_price;
					} else {
						$primary_price = $asking_price;
					}
				}
			}
			$sc_content .= '<div class="sc-price-wrapper">';
			if ( $primary_price > 0 ) {
				$sc_content .= '<div class="sc-price-value"><span class="sc-price-symbol">$</span>' .  number_format( $primary_price , 0 , '.' , ',' ) . '</div>';
			} else {
				$sc_content .= '<div class="sc-price-value-text">Call for Price</div>';
			}
			$sc_content .= '</div>';
			//wrapper -e
			$sc_content .= '</a></div>';
			
		}
	}

	$sc_content .= '</div>';


?>
