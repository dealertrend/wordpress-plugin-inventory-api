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

	function apply_special_tags( &$tags, $on_sale = false, $certified = false ){

		if( !empty($on_sale) ){
			$tags[] = 'on-sale';
		}

		if( !empty($certified) ){
			$tags[] = 'certified';
		}

		return $tags;

	}

	function build_tag_icons( $default, $custom, $tags ){

		$icons = '';
		$temp = array();

		usort( $custom, 'sort_custom_tags' );

		//Get Custom Icons
		foreach( $custom as $value ){
			if( in_array($value['name'], $tags) ){
				$icons .= '<img title="'.$value['name'].'" class="icon-custom icon-emb-'.$value['name'].'" src="'.$value['url'].'" />';
				$temp[] = $value['name'];
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
?>
