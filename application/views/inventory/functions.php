<?php

	function get_inventory_options( $data ){
		$options = array();

		!empty($data[ 'saleclass' ]) ? $options['saleclass_filter'] = $data[ 'saleclass' ] : '';
		!empty($data[ 'data' ][ 'makes_new' ]) ? $options['make_filter'] = $data[ 'data' ][ 'makes_new' ] : $options['make_filter'] = array();
		!empty($data[ 'inv_responsive' ]) ? $options['disable_responsive'] = $data[ 'inv_responsive' ]: '';
		!empty($data[ 'custom_contact' ][ 'phone_new' ]) ? $options['phone']['new'] = $data[ 'custom_contact' ][ 'phone_new' ] : '';
		!empty($data[ 'custom_contact' ][ 'phone_used' ]) ? $options['phone']['used'] = $data[ 'custom_contact' ][ 'phone_used' ] : '';
		!empty($data[ 'custom_contact' ][ 'name_new' ]) ? $options['contact_name']['new'] = $data[ 'custom_contact' ][ 'name_new' ] : '';
		!empty($data[ 'custom_contact' ][ 'name_used' ]) ? $options['contact_name']['used'] = $data[ 'custom_contact' ][ 'name_used' ] : '';
		!empty($data[ 'company_information' ][ 'name_override' ]) ? $options['company_override']['name'] = $data[ 'company_information' ][ 'name_override' ] : '';
		!empty($data[ 'theme' ][ 'show_standard_eq' ]) ? $options['standard_equipment'] = $data[ 'theme' ][ 'show_standard_eq' ] : '';

		return $options;
	}

	function get_custom_theme_settings( $data, $theme ){

		if( !empty( $data[ $theme ] ) ){
			return $data[ $theme ];
		} else {
			return array();
		}
	}

	function display_breadcrumb( $parameters, $company, $override, $sc = '' ){

		$company_name = ( !empty( $override['name'] ) ) ? $override['name'] : $company->name;

		$breadcrumb = '<a href="' . site_url() . '/" title="' . $company_name . ': Home Page">' . ucwords( strtolower( urldecode( $company_name ) ) ) . '</a>';

		$put_in_trail = array('saleclass','make','model','trim','vin');

		unset($parameters['taxonomy']);

		// Moves saleclass to top of array. Needed for Breadcrumbs
		if( isset( $parameters[ 'saleclass' ] ) ){
			$array_jump = array( 'saleclass' => $parameters[ 'saleclass' ] );
			unset( $parameters[ 'saleclass' ] );
			$parameters = $array_jump + $parameters;
		} else if( !empty($sc) ){
			$array_jump = array( 'saleclass' => $sc );
			$parameters = $array_jump + $parameters;
		}

		$rules = get_option( 'rewrite_rules' );
		$crumb_trail = isset($rules['^(inventory)']) ? '/inventory/' : '?taxonomy=inventory';

		foreach( $parameters as $key => $value ) {
			if( in_array( $key , $put_in_trail ) ) {
				if( isset($rules['^(inventory)']) ) {
					$crumb_trail .= rawurlencode( urldecode( $value ) ) . '/';
					$breadcrumb .= ' <span>&gt;</span> <a href=' .( $key != 'vin' ? $crumb_trail : ''). '>' . ucfirst( urldecode( $value ) ) . '</a>';
				} else {
					$crumb_trail .= '&amp;' . rawurlencode( urldecode( $key ) ) . '=' . $value;
					$breadcrumb .= ' <span>&gt;</span> <a href=' .( $key != 'vin' ? $crumb_trail : ''). '>' . ucfirst( urldecode( $value ) ) . '</a>';
				}
			}
		}

		return $breadcrumb;
	}

	function itemize_vehicle( $vehicle ){

		$data = array();

		$prices = $vehicle->prices;
		$data['prices']['was_now'] = $prices->{ 'use_was_now?' };
		$data['prices']['strike_through'] = $prices->{ 'use_price_strike_through?' };
		$data['prices']['on_sale'] = $prices->{ 'on_sale?' };
		$data['prices']['sale_price'] = isset( $prices->sale_price ) ? $prices->sale_price : NULL;
		$data['prices']['sale_expire'] = isset( $prices->sale_expire ) ? $prices->sale_expire : NULL;
		$data['prices']['retail_price'] = $prices->retail_price;
		$data['prices']['default_text'] = $prices->default_price_text;
		$data['prices']['asking_price'] = $prices->asking_price;
		$data['prices']['ais']['text'] = isset( $vehicle->ais_incentive->to_s ) ? $vehicle->ais_incentive->to_s : NULL;
		preg_match( '/\$\d*(\s)?/' , $data['prices']['ais']['text'] , $incentive );
		$data['prices']['ais']['value'] = isset( $incentive[ 0 ] ) ? str_replace( '$' , NULL, $incentive[ 0 ] ) : 0;

		$data['year'] = $vehicle->year;
		$data['make']['name'] = urldecode( $vehicle->make );
		$data['make']['clean'] = str_replace( '/' , '%2F' ,  $data['make']['name'] );
		$data['model']['name'] = urldecode( $vehicle->model_name );
		$data['model']['clean'] = str_replace( '/' , '%2F' ,  $data['model']['name'] );
		$data['trim']['name'] = urldecode( $vehicle->trim );
		$data['trim']['clean'] = str_replace( '/' , '%2F' ,  $data['trim']['name'] );
		$data['stock_number'] = $vehicle->stock_number;
		$data['vin'] = $vehicle->vin;
		$data['engine'] = $vehicle->engine;
		$data['transmission'] = $vehicle->transmission;
		$data['exterior_color'] = $vehicle->exterior_color;
		$data['interior_color'] = $vehicle->interior_color;
		$data['odometer'] = $vehicle->odometer;
		$data['icons'] = $vehicle->icons;
		$data['tags'] = $vehicle->tags;
		$data['thumbnail'] = urldecode( $vehicle->photos[ 0 ]->small );
		$data['photos'] = $vehicle->photos;
		$data['body_style'] = $vehicle->body_style;
		$data['vehicle_class'] = $vehicle->vehicleclass;
		$data['drive_train'] = $vehicle->drive_train;
		$data['doors'] = $vehicle->doors;
		$data['headline'] = $vehicle->headline;
		$data['saleclass'] = $vehicle->saleclass;
		$data['certified'] = (!empty($vehicle->certified) ) ? $vehicle->certified : 'false';
		$data['autocheck'] = isset( $vehicle->auto_check_url ) ? TRUE : FALSE;
		$data['disclaimer'] = $vehicle->disclaimer;
		$data['carfax'] = isset($vehicle->carfax) ? $vehicle->carfax->url : NULL;
		$data['video'] = isset($vehicle->video_url) ? $vehicle->video_url : NULL;
		$data['dealer_options'] = $vehicle->dealer_options;
		$data['standard_equipment'] = $vehicle->standard_equipment;
		$data['description'] = $vehicle->description;
		$data['fuel_economy'] = $vehicle->fuel_economy;
		$data['acode'] = isset($vehicle->ads_acode) && !empty($vehicle->ads_acode) ? $vehicle->ads_acode : NULL;

		$contact = $vehicle->contact_info;
		$data['contact_info']['dealer_id'] = $contact->company_id;
		$data['contact_info']['dealer'] = $contact->dealer_name;
		$data['contact_info']['greeting'] = $contact->greeting;
		$data['contact_info']['manager'] = $contact->internet_manager;
		$data['contact_info']['phone'] = $contact->phone;
		$data['contact_info']['location'] = $vehicle->vehicle_location;

		return $data;
	}

	function get_price_display( $price, $company, $vin, $theme = 'custom' ){

		$data = array();
		$data['primary_price'] = 0;

		$data['ais_link'] = '';
		$data['ais_text'] = ( !empty($price['ais']['text']) ) ? '<div class="'.$theme.'-ais-incentive-l-text">' . $price['ais']['text'] . '</div>' : '';
		$data['expire_text'] = ( !empty($price['sale_expire']) ) ? '<div class="'.$theme.'-sale-expires">Sale Expires: ' . $price['sale_expire'] . '</div>' : '';
		$data['msrp_text'] = ( !empty($price['retail_price']) ) ? '<div class="'.$theme.'-msrp">MSRP: $'.number_format($price['retail_price'] , 0 , '.' , ',' ).'</div>' : '<div class="'.$theme.'-msrp"></div>';

		if( $price['on_sale'] && $price['sale_price'] > 0 ) {
			$now_text = 'Price: ';
			if( $price['was_now'] ) {
				$price_class = ( $price['strike_through'] ) ? $theme.'-strike-through '.$theme.'-asking-price' : $theme.'-asking-price';
				if( $price['ais']['value'] > 0 ) {
					$data['compare_text'] = '<div class="'.$price_class.' '.$theme.'-ais"><span>Compare At:</span> $'.number_format($price['sale_price'] , 0 , '.' , ',' ).'</div>';
				} else {
					$data['compare_text'] = '<div class="'.$price_class.'"><span>Compare At:</span> $'.number_format($price['asking_price'] , 0 , '.' , ',' ).'</div>';
				}
				$now_text = 'Sale Price: ';
			}
			if( $price['ais']['value'] > 0 ) {
				$data['primary_text'] = '<div class="'.$theme.'-sale-price '.$theme.'-ais '.$theme.'-main-price">'.$now_text.'<span>$'.number_format($price['sale_price'] - $price['ais']['value'] , 0 , '.' , ',' ) .'</span></div>';
				$data['primary_price'] = $price['sale_price'] - $price['ais']['value'];
			} else {
				$data['primary_text'] = '<div class="'.$theme.'-sale-price '.$theme.'-main-price">'.$now_text.'<span>$' . number_format( $price['sale_price'] , 0 , '.' , ',' ) .'</span></div>';
				$data['primary_price'] = $price['sale_price'];
			}
		} else {
			if( $price['asking_price'] > 0 ) {
				if( $price['ais']['value'] > 0 ) {
					$data['primary_text'] = '<div class="'.$theme.'-your-price '.$theme.'-ais '.$theme.'-main-price">Sale Price: <span>$' . number_format( $price['asking_price'] - $price['ais']['value'] , 0 , '.' , ',' ) . '</span></div>';
					$data['compare_text'] = '<div class="'.$theme.'-asking-price '.$theme.'-ais"><span>Compare At:</span> $'.number_format( $price['asking_price'] , 0 , '.' , ',' ) .'</div>';
					$data['primary_price'] = $price['asking_price'] - $price['ais']['value'];
				} else {
					$data['primary_text'] = '<div class="'.$theme.'-asking-price '.$theme.'-main-price">Price: <span> $'.number_format( $price['asking_price'] , 0 , '.' , ',' ) .'</span></div>';
					$data['primary_price'] = $price['asking_price'];
				}
			} else {
				$data['primary_text'] = '<div class="'.$theme.'-no-price '.$theme.'-main-price">'.$price['default_text'].'</div>';
			}
		}

		if( !empty($price['ais']['text']) && isset( $company->api_keys ) && !empty($vin) ) {
			$data['ais_link'] = '<div class="'.$theme.'-ais-link view-available-rebates"><a href="http://onecar.aisrebates.com/dlr2/inline/IncentiveOutput.php?vID='. $vin . '&wID=' . $company->api_keys->ais . '&zID=' . $company->zip . '" target="_blank" title="VIEW AVAILABLE INCENTIVES" onclick="return loadIframe( this.href );">VIEW INCENTIVES</a></div>';
		}

		//Clean Price Values
		$data['hidden_prices'] = '<div class="hidden-vehicle-prices" style="display: none;"><div class="hidden-msrp" alt="msrp">'.$price['retail_price'].'</div>'. (( $price['ais']['value'] > 0 ) ? '<div class="hidden-rebate" alt="rebate">'.$price['ais']['value'].'</div>' : '') . '<div class="hidden-sale" alt="sale">'.$price['sale_price'].'</div><div class="hidden-asking" alt="asking">'.$price['asking_price'].'</div><div class="hidden-main" alt="main">'.$data['primary_price'].'</div>'. (( $price['sale_price'] > 0 && ($price['asking_price'] - $price['sale_price']) != 0 ) ? '<div class="hidden-discount" alt="discount">'. ($price['asking_price'] - $price['sale_price']) .'</div>' : '') . '</div>';

		return $data;
	}

	function get_photo_detail_display( $photos, $video, $active = 1 ){

		$class = ( $active == 1 ) ? array( 'active', '' ) : array( '', 'active');
		$content_video = '';

		if( $video ){
			$buttons = '<div class="tabs-button tabs-button-img-photo '.$class[1].'" name="img-photo">Photos</div><div class="tabs-button tabs-button-img-video '.$class[0].'" name="img-video">Video</div>';
			$content_video = '<div class="tabs-content tabs-content-img-video '.$class[0].'"><div id="inventory-video-wrapper">';
				$type = wp_check_filetype( $video, wp_get_mime_types() );
				if( empty( $type['ext'] ) ){
					//currently only works for youtube videos
					preg_match('/(?<=v(\=|\/))([-a-zA-Z0-9_]+)|(?<=youtu\.be\/)([-a-zA-Z0-9_]+)/', $video, $matches);
					$content_video .= '<iframe id="inventory-video-iframe" src="http://www.youtube.com/embed/'.$matches[0].'?feature=player_detailpage" frameborder="0" allowfullscreen></iframe>';
				} else {
					$content_video .= wp_video_shortcode( array( 'src' => $video ) );
				}
			$content_video .= '</div></div>';
		} else {
			$buttons = '<div class="tabs-button tabs-button-img-photo active" name="img-photo">Photos</div>';
			$class[1] = 'active';
		}

		$content_photos = '<div class="tabs-content tabs-content-img-photo '.$class[1].'"><div id="vehicle-images">';
			foreach( $photos as $photo ) {
				$content_photos .= '<a class="lightbox" rel="slides" href="'.str_replace('&', '&amp;', $photo->large).'"><img src="'.str_replace('&', '&amp;', $photo->large).'" /></a>';
			}
		$content_photos .= '</div><div id="vehicle-thumbnails"></div></div>';

		$display = '<div id="photo-tabs-wrapper">';

		$display .= '<div id="photo-tabs-buttons">';
		$display .= $buttons;
		$display .= '</div>';

		$display .= '<div id="photo-tabs-content">';
		$display .= $content_photos;
		$display .= $content_video;
		$display .= '</div>';

		$display .= '</div>';

		return $display;
	}

	function get_fuel_economy_display( $fuel, $country, $img_id = 0, $vrs = NULL, $acode = NULL ){

		$city = !empty( $fuel ) && !empty( $fuel->city ) ? $fuel->city : 0;
		$highway = !empty( $fuel ) && !empty( $fuel->highway ) ? $fuel->highway : 0;

		if( $country == 'CA' ) {
			$fuel_ca = $vrs->get_fuel_economy( $acode )->please();
			if( isset( $fuel_ca[ 'body' ] ) ) {
				$fuel_ca = json_decode( $fuel_ca[ 'body' ] );
				$fuel_ca = $fuel_ca[ 0 ];
			} else {
				$fuel_ca = NULL;
			}
			if( !empty($fuel_ca) ){
				$city = !empty($fuel_ca->city_lp_100km) ? $fuel_ca->city_lp_100km : 0;
				$highway = !empty($fuel_ca->highway_lp_100km) ? $fuel_ca->highway_lp_100km : 0;
			}
		}

		switch($img_id){
			case 0:
				$img_name = 'mpg_39x52.png';
				break;
			case 1:
				$img_name = 'mpg_s_39x52.png';
				break;
			case 2:
				$img_name = 'mpg_green_44x65.png';
				break;
		}

		$img_link = '<img src="http://assets.s3.dealertrend.com.s3.amazonaws.com/images/'.$img_name.'" name="fuel-economy" />';
		$fuel_text = '';

		if( !empty($city) && !empty($highway) ) {
			$fuel_text = '<div id="fuel-economy-wrapper"><div id="fuel-city"><span id="city-text">City</span><span id="city-value">' . $city . '</span></div><div id="fuel-image">'.$img_link.'</div><div id="fuel-highway"><span id="highway-text">Highway</span><span id="highway-value">' . $highway . '</span></div><p id="fuel-disclaimer">Actual mileage will vary with options, driving conditions, driving habits and vehicle&#39;s condition.</p></div>';
		}

		return $fuel_text;

	}

	function get_form_button_display( $forms, $saleclass = "All" ){
		$ids = array();
		switch( strtolower($saleclass) ){
			case 'new':
				$sc = 1;
				break;
			case 'used':
				$sc = 2;
				break;
			default:
				$sc = 0;
		}
		echo '<div id="form-button-wrapper">';

		foreach( $forms as $key => $form ){
			if( !empty($form['button']) && ($form['saleclass'] == 0 || $form['saleclass'] == $sc) ){
				$ids[] = $form['id'];
				echo '<div id="form-button-'.$form['id'].'" class="form-button form-'.$form['id'].'" name="form-id-'.$form['id'].'">';
				echo $form['title'];
				echo '</div>';
			}
		}

		if( !empty($ids) ){
			echo '<div id="form-data">';
			foreach( $ids as $id ){
				echo '<div id="form-id-'.$id.'" class="form-wrap">';
				echo gravity_form($id, true);
				echo '</div>';
			}
			echo '</div>';
		}

		echo '</div>';

	}

	function get_form_display( $forms, $saleclass = "All" ){

		switch( strtolower($saleclass) ){
			case 'new':
				$sc = 1;
				break;
			case 'used':
				$sc = 2;
				break;
			default:
				$sc = 0;
		}

		foreach( $forms as $key => $form ){
			if( empty($form['button']) && ($form['saleclass'] == 0 || $form['saleclass'] == $sc) ){
				echo '<div class="form-wrapper">';
					echo '<div id="form-id-'.$form['id'].'" class="form-display-wrap form-'.$form['id'].'" name="form-id-'.$form['id'].'">';
					echo gravity_form($form['id'], true);
					echo '</div>';
				echo '</div>';
			}
		}

	}

	function get_loan_calculator( $loan_data, $price ){
		$display = '';

		if( is_numeric($price) && $price != 0 ){
			$display = '<div id="loan-calculator-wrapper">';

			$display .= '<div id="loan-calculator-button">Loan Calculator</div>';

			$display .= '<div id="loan-calculator-data">';
			$display .= '<div id="loan-calculator">';
			$display .= '<div class="loan-value"><label>Vehicle Price</label><input type="text" id="loan-calculator-price" value="$'.( is_numeric($price) ? number_format( $price , 0 , '.' , ',' ) : '0' ).'" /></div>';
			$display .= '<div class="loan-value"><label>Interest Rate</label><input type="text" id="loan-calculator-interest-rate" value="'.$loan_data['default_interest'].'%" /></div>';
			$display .= '<div class="loan-value"><label>Trade-in Value</label><input type="text" id="loan-calculator-trade-in-value" value="$'.number_format( $loan_data['default_trade'] , 0 , '.' , ',' ).'" /></div>';
			$display .= '<div class="loan-value"><label>Term (months)</label><input type="text" id="loan-calculator-term" value="'.$loan_data['default_term'].'" /></div>';
			$display .= '<div class="loan-value"><label>Down Payment</label><input type="text" id="loan-calculator-down-payment" value="$'.number_format( $loan_data['default_down'] , 0 , '.' , ',' ).'" /></div>';
			$display .= '<div class="loan-value"><label>Sales Tax</label><input type="text" id="loan-calculator-sales-tax" value="'.$loan_data['default_tax'].'%" /></div>';

			$display .= '<hr>';

			$display .= ($loan_data['display_bi_monthly']) ? '<div class="loan-payment"><label>Bi Monthly Cost</label><div id="loan-calculator-bi-monthly-cost"></div></div>' :'';
			$display .= ($loan_data['display_monthly']) ? '<div class="loan-payment"><label>Monthly Cost</label><div id="loan-calculator-monthly-cost"></div></div>' :'';

			$display .= '<div class="loan-total"><label>Total Cost<br><small>(including taxes)</small></label><div id="loan-calculator-total-cost"></div></div>';

			$display .= '<div id="loan-calculator-submit"><button>Calculate</button></div>';

			$display .= '</div></div>';

			$display .= '</div>';
		}

		return $display;
	}

	function get_vehicle_detail_display( $options, $description, $show_equipment = FALSE, $equipment = array(), $active = 0 ){

		switch ($active){
			case 0:
				$class = count($options) > 0 ? array( 'active', '', '') : '';
				break;
			case 1:
				$class = strlen($description) > 0 ? array( '', 'active', '') : '';
				break;
			case 2:
				$class = $show_equipment && !is_Empty_check($equipment) ? array( '', '', 'active') : '';
				break;
		}

		if( empty($class) ){
			if( count($options) > 0 ){
				$class = array( 'active', '', '');
			} else if( strlen($description) > 0 ){
				$class = array( '', 'active', '');
			} else if( $show_equipment && !is_Empty_check($equipment) ){
				$class = array( '', '', 'active');
			}
		}

		$buttons = '';
		$content = '';

		if( count($options) > 0 ){
			$buttons .= '<div class="tabs-button tabs-button-details-options '.$class[0].'" name="details-options">Vehicle Options</div>';
			$content .= '<div class="tabs-content tabs-content-details-options '.$class[0].'"><div id="vehicle-detail-options"><ul>';
				foreach( $options as $option ){
					$content .= '<li>' . $option . '</li>';
				}
			$content .= '</ul></div></div>';
		}

		if( strlen($description) > 0 ){
			$buttons .= '<div class="tabs-button tabs-button-details-desc '.$class[1].'" name="details-desc">Description</div>';
			$content .= '<div class="tabs-content tabs-content-details-desc '.$class[1].'"><div id="vehicle-detail-desc">';
			$content .= $description;
			$content .= '</div></div>';
		}

		if( $show_equipment && !is_Empty_check($equipment) ){
			$buttons .= '<div class="tabs-button tabs-button-details-equipment '.$class[2].'" name="details-equipment">Standard Equipment</div>';
			$content .= '<div class="tabs-content tabs-content-details-equipment '.$class[2].'"><div id="vehicle-detail-equipment">';
			$content .= display_equipment($equipment);
			$content .= '</div></div>';
		}

		$display = '<div id="vehicle-details-wrapper" >';

		$display .= '<div id="vehicle-details-tabs-buttons">';
		$display .= $buttons;
		$display .= '</div>';

		$display .= '<div id="vehicle-details-tabs-content">';
		$display .= $content;
		$display .= '</div>';

		$display .= '</div>';

		return $display;

	}

	function get_default_tag_names(){
		$icons = array(
			'on-sale',
			'certified',
			'special',
			'cherry-deal',
			'custom-wheels',
			'gas-saver',
			'good-buy',
			'hybrid',
			'local-trade-in',
			'low-miles',
			'moon-roof',
			'navagation',
			'one-owner',
			'priced-to-go',
			'rare',
			'sale-pending',
			'under-blue-book',
			'wont-last',
			'video'
		);

		return $icons;
	}

	function apply_special_tags( &$tags, $on_sale = false, $certified = 'false', $video = NULL ){

		if( !empty($on_sale) ){
			$tags[] = 'on-sale';
		}

		if( $certified != 'false' ){
			$tags[] = 'certified';
		}

		if( !empty($video) ){
			$tags[] = 'video';
		}

	}

	function build_tag_icons( $default, $custom, $tags ){

		$icons = '';
		$temp = array();

		if( !empty($custom) ){
			usort( $custom, 'sort_custom_tags' );

			//Get Custom Icons
			foreach( $custom as $value ){
				if( in_array($value['name'], $tags) ){
					$icons .= '<img title="'.$value['name'].'" class="icon-custom icon-emb-'.$value['name'].'" src="'.$value['url'].'" />';
					$temp[] = $value['name'];
				}
			}
		}

		//Get Defaults
		foreach( $default as $value ){
			if( !in_array($value, $temp) ){
				if( in_array($value, $tags) ){
					$icons .= '<img title="'.$value.'" class="icon-default icon-emb-'.$value.'" src="http://assets.s3.dealertrend.com.s3.amazonaws.com/images/tag_icons/'.$value.'.png" />';
				}
			}
		}

		return $icons;

	}

	function sort_custom_tags( $a , $b ){
		return ( $a->order > $b->order ) ? +1 : -1;
	}

	function display_autocheck_image( $vin, $sc = 'new', $page = 'list' ){

		$autocheck = '';
		if( strtolower($sc) == 'used' ){
			$autocheck = '<div class="autocheck-'.$page.'" >';
			$onclick_string = "'".site_url() . "/inventory/autocheck/".$vin."/','popup', 'width=960,height=800,scrollbars=yes,resizable=yes,toolbar=no,directories=no,location=no,menubar=no,status=no,left=0,top=0'";
			$autocheck .= '<a onclick="window.open('.$onclick_string.'); return false"  ><img src="http://assets.s3.dealertrend.com.s3.amazonaws.com/images/autocheck_'.$page.'.png" /></a>';
			$autocheck .= '</div>';
		}

		return $autocheck;
	}

	function get_proxy_site_page( $url ){
		$options = array(
		    CURLOPT_RETURNTRANSFER => true,     // return web page
		    CURLOPT_HEADER         => true,     // return headers
		    CURLOPT_FOLLOWLOCATION => true,     // follow redirects
		    CURLOPT_ENCODING       => "",       // handle all encodings
		    CURLOPT_AUTOREFERER    => true,     // set referer on redirect
		    CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
		    CURLOPT_TIMEOUT        => 120,      // timeout on response
		    CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
		);

		$ch = curl_init( $url );
		curl_setopt_array( $ch, $options );
		$remoteSite = curl_exec( $ch );
		$header  = curl_getinfo( $ch );
		curl_close( $ch );

		$header['content'] = $remoteSite;
		return $header;
	}

	function sort_equipment( $a , $b ) {
		return ( $a->group > $b->group ) ? +1 : -1;
	}

	function display_equipment( $equipment ){

		$equipment_groups = array();
		$equipment_data = array();
		$eq = '';

		usort( $equipment , 'sort_equipment' );

		foreach( $equipment as $item ) {
			$equipment_data[ $item->group ][] = $item;
			if( ! in_array( $item->group , $equipment_groups ) ) {
				$equipment_groups[] = $item->group;
			}
		}

		foreach( $equipment_groups as $group ) {
			$eq .= '<div class="equipment_group">';
			$eq .= '<h4>' . $group . '</h4>';
			$eq .= '<ul class="equipment_list">';
			foreach( $equipment_data[ $group ] as $data ) {
				$eq .= '<li>' . $data->name;
				$eq .= ! empty( $data->data ) ? ': ' . $data->data : NULL;
				$eq .= '</li>';
			}
			$eq .= '</ul>';
			$eq .= '</div>';
		}

		return $eq;
	}

	function get_similar_vehicles( $vms, $detail_vin, $sale_class, $vehicle_class, $price, $make, $make_filter, $location ){

		if ( !empty( $vehicle_class ) ) {
			$price_from = $price - 3000;
			$price_to = $price + 1000;
			if ( $price_from < 0 ) {
				$price_from = 0;
			}

			if ( $primary_price > 0) {
				$sim_array = array( 'search_sim' => 1 , 'photo_view' => 1 , 'saleclass' => $sale_class , 'vehicleclass' => $vehicle_class , 'per_page' => 4 , 'price_from' => $price_from, 'price_to' => $price_to);
			} else {
				$sim_array = array( 'search_sim' => 1 , 'photo_view' => 1 , 'saleclass' => $sale_class , 'vehicleclass' => $vehicle_class , 'per_page' => 4 , 'make' => $make );
			}

			if ( strcasecmp( $sale_class, 'new') == 0 && !empty( $make_filter ) ) {
				$sim_array = array_merge( $sim_array, array( 'make_filters' =>  $make_filter ) );
			}

			$vms->tracer = 'Obtaining Similar Vehicles - 1';
			$inventory_sims_info = $vms->get_inventory()->please( $sim_array );
			$inventory_sims = isset( $inventory_sims_info[ 'body' ] ) ? json_decode( $inventory_sims_info[ 'body' ] ) : false;

			if ( !empty( $inventory_sims ) && count($inventory_sims) > 1 ) {
				include( dirname( __FILE__ ) . '/similar_vehicles.php' );
			} else if ( isset( $sim_array['price_from'] ) ) {
				unset( $sim_array['price_from'] );
				unset( $sim_array['price_to'] );
				$sim_array = $sim_array + array( 'make' => $make );
				$vms->tracer = 'Obtaining Similar Vehicles - 2';
				$inventory_sims_info = $vms->get_inventory()->please( $sim_array );
				$inventory_sims = isset( $inventory_sims_info[ 'body' ] ) ? json_decode( $inventory_sims_info[ 'body' ] ) : false;
				if ( !empty( $inventory_sims ) && count($inventory_sims) > 1 ) {
					include( dirname( __FILE__ ) . '/similar_vehicles.php' );
				}
			}
		}
	}

	function is_Empty_check($obj){
		if( empty($obj) ){
			return true;
		} else if( is_numeric( $obj ) ){
			return false;
		}else if( is_string($obj) ){
			return !strlen(trim($obj));
		}else if( is_object($obj) ){
			return is_Empty_check((array)$obj);
		}
		// It's an array!
		foreach($obj as $element)
			if (is_Empty_check($element)) continue; // so far so good.
			else return false;

		// all good.
		return true;
	}
?>
