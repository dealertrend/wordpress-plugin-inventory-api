<?php

	namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

	global $wp_rewrite;

	$sale_class_filter = $this->options[ 'vehicle_management_system' ][ 'saleclass' ];
	$new_makes_filter = $this->options[ 'vehicle_management_system' ][ 'data' ][ 'makes_new' ];
	$remove_responsive = $this->options[ 'vehicle_management_system' ][ 'inv_responsive' ];
	$phone_new = $this->options[ 'vehicle_management_system' ][ 'custom_contact' ][ 'phone_new' ];
	$phone_used = $this->options[ 'vehicle_management_system' ][ 'custom_contact' ][ 'phone_used' ];
	$name_new = $this->options[ 'vehicle_management_system' ][ 'custom_contact' ][ 'name_new' ];
	$name_used = $this->options[ 'vehicle_management_system' ][ 'custom_contact' ][ 'name_used' ];
	$show_standard_eq = $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'show_standard_eq' ];

	if ( substr($this->options[ 'vehicle_management_system' ][ 'host' ], 0, 7) == 'http://' ){
		$temp_host = $this->options[ 'vehicle_management_system' ][ 'host' ];
	} else {
		$temp_host = 'http://'.$this->options[ 'vehicle_management_system' ][ 'host' ];
	}

	$vehicle_management_system->tracer = 'Obtaining requested inventory.';

	if ( strcasecmp( $this->parameters['saleclass'], 'new') == 0 && !empty( $new_makes_filter ) ) { // New Make Filter
		$inventory_information = $vehicle_management_system->get_inventory()->please( array_merge( $this->parameters , array( 'photo_view' => 1 , 'make_filters' =>  $new_makes_filter ) ) );
	} else {
		$inventory_information = $vehicle_management_system->get_inventory()->please( array_merge( $this->parameters , array( 'photo_view' => 1 ) ) );
	}

	$inventory = isset( $inventory_information[ 'body' ] ) ? json_decode( $inventory_information[ 'body' ] ) : false;

	$site_url = site_url();
	$generic_error_message = '<h2 style="font-family:Helvetica,Arial; color:red;">Unable to display inventory. Please contact technical support.</h2><br class="clear" />';
	$type = isset( $inventory->vin ) ? 'detail' : 'list';
	$default_scripts = array(
		'jquery',
		'jquery-ui-core'
	);

	if ( isset($_GET['print_page']) && $type == 'detail' ) {
		$setprintpage = "detail_print";
	}

	foreach( $default_scripts as $key => $value ) {
		wp_enqueue_script( $value );
	}

	wp_enqueue_script(
		'dealertrend-inventory-theme-armadillo-js',
		$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/armadillo/js/armadillo.js',
		array( 'jquery-ui-core' , 'jquery-ui-button' , 'jquery-ui-dialog' ),
		$this->plugin_information[ 'Version' ],
		true
	);

	if ( empty( $remove_responsive ) ) {
		//Responsive Style Sheet
		wp_enqueue_style(
			'dealertrend-inventory-responsive-armadillo',
			$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/armadillo/css/armadillo-responsive.css',
			false,
			$this->plugin_information[ 'Version' ]
		);
	}

	if ( !isset($setprintpage) ) {
		switch( $type ) {
			case 'list':

				break;
			case 'detail':
				wp_enqueue_script( 'jquery-ui-tabs' );
				wp_enqueue_script( 'jquery-cycle' , $this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-cycle/2.72/js/jquery.cycle.all.js' , array( 'jquery' ) , '2.72' , true );
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
	} else {
		wp_enqueue_style(
			'dealertrend-detail-print' ,
			$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/armadillo/css/dealertrend-inventory-print.css' ,
			false ,
			'0.5'
		);
	}

	if ( !isset($setprintpage) ){
		get_header();
	}
	flush();

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

	$company_information = json_decode( $company_information[ 'body' ] );
	$city = $company_information->seo->city;
	$state = $company_information->seo->state;
	$company_name = strtoupper( $company_information->name );
	$company_name_override = $this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'name_override' ];

	$parameters = $this->parameters;
	$query = '?' . http_build_query( $_GET );

	$breadcrumbs = '<a href="' . $site_url . '/" title="' . $company_name . ': Home Page"><span>&gt;</span>' . ( !empty($company_name_override) ? $company_name_override: urldecode( $company_name ) ) . '</a>';
	$put_in_trail = array(
		'saleclass',
		'make',
		'model',
		'trim',
		'vin'
	);

	$sale_class = isset( $parameters[ 'saleclass' ] ) ? ucwords( $parameters[ 'saleclass' ] ) : 'All';

	unset( $parameters[ 'taxonomy' ] );
	if( !isset( $parameters[ 'saleclass' ] ) ){
		if( isset( $inventory->saleclass ) ) {
			$substitute = $inventory->saleclass;
			array_shift( $parameters );
			$substitute_array = array( 'saleclass' => $substitute );
			$parameters = $substitute_array + $parameters;
		}
	}

	// Moves saleclass to top of array. Needed for Breadcrumbs
	if( isset( $parameters[ 'saleclass' ] ) ){
		$array_jump = array( 'saleclass' => $parameters[ 'saleclass' ] );
		unset( $parameters[ 'saleclass' ] );
		$parameters = $array_jump + $parameters;
	}

	$inventory_base = ! empty( $wp_rewrite->rules ) ? $site_url . '/inventory/' : $site_url . '?taxonomy=inventory';
	$crumb_trail = $inventory_base;

	foreach( $parameters as $key => $value ) {
		if( in_array( $key , $put_in_trail ) ) {
			if( ! empty( $wp_rewrite->rules ) ) {
				if ( $key == 'trim' ) {
					$crumb_trail .= '?trim=' . rawurlencode( urldecode( $value ) ) ;
				} else {
					$crumb_trail .= rawurlencode( urldecode( $value ) ) . '/';
				}
				if( $key == 'city' || $key == 'state' | $key == 'vin' ) {
					$breadcrumbs .= '<a href=""><span>&gt;</span>' . ucfirst( urldecode( $value ) ) . '</a>';
				} else {
					$breadcrumbs .= '<a href=' . $crumb_trail . '><span>&gt;</span>' . ucfirst( urldecode( $value ) ) . '</a>';
				}
			} else {
				if( $key == 'city' || $key == 'state' | $key == 'vin' ) {
					$breadcrumbs .= '<a href=""><span>&gt;</span>' . ucfirst( urldecode( $value ) ) . '</a>';
				} else {
					$breadcrumbs .= '<a href=' . $crumb_trail . '><span>&gt;</span>' . ucfirst( urldecode( $value ) ) . '</a>';
				}
			}
		}
	}

	$breadcrumbs = '<div class="armadillo-breadcrumbs">' . $breadcrumbs . '</div>';

	$default_tag_names = get_default_tag_names();
	$custom_tag_icons = $this->options[ 'vehicle_management_system' ][ 'tags' ][ 'data' ];

	echo '<div id="dealertrend-inventory-api">';
	if ( !isset($setprintpage) ){
		include( dirname( __FILE__ ) . '/' . $type . '.php' );
	} else {
		include( dirname( __FILE__ ) . '/' . $setprintpage . '.php' );
	}
	echo '</div>';

	echo "\n" . '<!--' . "\n";
	echo '##################################################' . "\n";
	echo print_r( $this , true ) . "\n";
	echo print_r( $company_information , true ) . "\n";
	echo print_r( $vehicle_management_system , true ) . "\n";
	if( isset( $dynamic_site_headers ) ) {
		echo print_r( $dynamic_site_headers , true ) . "\n";
	}
	echo '##################################################' . "\n";
	echo '-->' . "\n";

	flush();
	if ( !isset($setprintpage) ){
		get_footer();
	}
	flush();

?>
