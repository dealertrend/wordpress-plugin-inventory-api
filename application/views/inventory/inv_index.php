<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

	$parameters = $this->parameters;
	$parameters[ 'saleclass' ] = isset( $parameters[ 'saleclass' ] ) ? ucwords( $parameters[ 'saleclass' ] ) : 'All';
	$inventory_options = get_inventory_options( $this->options[ 'vehicle_management_system' ] );
	$theme_settings = get_custom_theme_settings( $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ], ucfirst($current_theme) );
	$loan_settings = get_custom_theme_settings( $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ], 'loan');
	$price_text = get_custom_theme_settings( $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ], 'price');
	$rules = get_option( 'rewrite_rules' );
	$url_rule = ( isset($rules['^(inventory)']) ) ? TRUE : FALSE;
	//$cro_system = new cro_system($this->options['cro_system']['access_id']);
	
	if( $theme_settings['display_geo'] ){
		$dealer_geo = $vehicle_management_system->get_automall_geo_data();
		decode_geo_query( $dealer_geo, $parameters, $geo_params );
	}
	
	if ( substr($this->options[ 'vehicle_management_system' ][ 'host' ], 0, 7) == 'http://' ){
		$temp_host = $this->options[ 'vehicle_management_system' ][ 'host' ];
	} else {
		$temp_host = 'http://'.$this->options[ 'vehicle_management_system' ][ 'host' ];
	}

	$company_information = json_decode( $company_information[ 'body' ] );
	$company_zip = $company_information->zip;
	$city = $company_information->seo->city;
	$state = $company_information->seo->state;
	$company_name = strtoupper( $company_information->name );
	$country_code =	$company_information->country_code;

	$vehicle_management_system->tracer = 'Obtaining requested inventory.';
	$inventory_information = $vehicle_management_system->get_inventory()->please( array_merge( $parameters , array( 'photo_view' => 1 , 'make_filters' =>  $inventory_options['make_filter'] ) ) );
	$inventory = isset( $inventory_information[ 'body' ] ) ? json_decode( $inventory_information[ 'body' ] ) : false;

	$site_url = site_url();
	$type = isset( $inventory->vin ) ? 'detail' : 'list';
	
	$default_tag_names = get_default_tag_names();
	$custom_tag_icons = $this->options[ 'vehicle_management_system' ][ 'tags' ][ 'data' ];

	$default_scripts = array(
		'jquery',
		'jquery-ui-core',
		'jquery-ui-tabs',
		'jquery-ui-dialog',
		'jquery-ui-slider'
	);

	foreach( $default_scripts as $key => $value ) {
		wp_enqueue_script( $value );
	}

	if ( ! $inventory_options['disable_responsive'] ) {
		wp_enqueue_style(
			$current_theme.'-responsive' ,
			$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/'.$current_theme.'/css/'.$current_theme.'-responsive.css' ,
			false ,
			'1.0'
		);
	}

	wp_enqueue_script(
		$current_theme.'-theme-js',
		$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/'.$current_theme.'/js/'.$current_theme.'.js',
		array( 'jquery' ),
		$this->plugin_information[ 'Version' ],
		true
	);

	switch( $type ) {
		case 'detail':
			wp_enqueue_script( 'jquery-cycle2' , $this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-cycle/cycle2.js' , array( 'jquery' ) , '2.1.5' , true );
			wp_enqueue_script( 'jquery-lightbox' , $this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-lightbox/1.0/js/jquery.lightbox.js' , array( 'jquery' ) , '0.5' , true );
			wp_enqueue_style( 'jquery-lightbox' , $this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-lightbox/1.0/css/jquery.lightbox.css' , false , '0.5' );
			wp_enqueue_script(
				'dealertrend-inventory-api-loan-calculator',
				$this->plugin_information[ 'PluginURL' ] . '/application/assets/inventory/js/loan-calculator.js',
				'jquery',
				$this->plugin_information[ 'Version' ]
			);
		break;
	}
	
	get_header();
	flush();

	$generic_error_message = '<h2 style="font-family:Helvetica,Arial; color:red;">Unable to display inventory. Please contact technical support.</h2><br class="clear" />';

	switch( $status ) {
		case 200:
		case 404:
			break;
		case 503:
			echo $generic_error_message;
			echo '<p>We were unable to establish a connection to the API. Refreshing the page may resolve this.</p>';
		default:
			get_footer();
			return false;
			break;
	}

	echo "\n" . '<!--' . "\n";
	echo '########' . "\n";
	//echo print_r( $this , true ) . "\n";
	//echo print_r( $company_information , true ) . "\n";
	//echo print_r( $vehicle_management_system , true ) . "\n";
	if( isset( $dynamic_site_headers ) ) {
		//echo print_r( $dynamic_site_headers , true ) . "\n";
		echo '[SEO Helper] => ' . $dynamic_site_headers->request_stack[0];
	}
	echo "\n" . '########' . "\n";
	echo '-->' . "\n";

	echo '<div id="dealertrend-inventory-api">';
		include( $theme_path . '/' . $type . '.php' );
	echo '</div>';

	flush();
	get_footer();

?>
