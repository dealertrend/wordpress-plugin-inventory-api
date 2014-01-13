<?php

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
			'wont-last'
		);

		return $icons;
	}

	function apply_special_tags( &$tags, $on_sale = false, $certified = 'false' ){

		if( !empty($on_sale) ){
			$tags[] = 'on-sale';
		}

		if( $certified != 'false' ){
			$tags[] = 'certified';
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

	function get_custom_theme_settings( $data, $theme ){

		if( !empty( $data[ $theme ] ) ){
			return $data[ $theme ];
		} else {
			return array();
		}

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
?>
