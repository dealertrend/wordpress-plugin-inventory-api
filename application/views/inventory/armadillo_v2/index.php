<?php

	namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

	global $wp_rewrite;

	$vehicle_management_system->tracer = 'Obtaining requested inventory.';
	$inventory_information = $vehicle_management_system->get_inventory()->please( array_merge( $this->parameters , array( 'photo_view' => 1  ) ) );

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
		'dealertrend-inventory-theme-armadillo-misc-ui',
		$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/armadillo_v2/js/misc-ui.js',
		array( 'jquery-ui-core' , 'jquery-ui-button' , 'jquery-ui-dialog' ),
		$this->plugin_information[ 'Version' ],
		true
	);
	
//Responsive Style Sheets
	//Mid
	wp_enqueue_style(
		'dealertrend-inventory-responsive-mid',
		$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/armadillo_v2/css/responsive-mid.css',
		false,
		$this->plugin_information[ 'Version' ],
		'only screen and (min-width: 650px) and (max-width: 1066px), not (min-device-width: 481px) and (max-device-width: 1024px)'
	);
	//Small
	wp_enqueue_style(
		'dealertrend-inventory-responsive-small',
		$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/armadillo_v2/css/responsive-small.css',
		false,
		$this->plugin_information[ 'Version' ],
		'only screen and (max-width: 649px), not (min-device-width: 481px) and (max-device-width: 1024px), not (max-device-width 480px)'
	);
	//Phone
	wp_enqueue_style(
		'dealertrend-inventory-responsive-phone',
		$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/armadillo_v2/css/responsive-phone.css',
		false,
		$this->plugin_information[ 'Version' ],
		'only screen and (max-device-width: 480px)'
	);
	if ( !isset($setprintpage) ) {
		switch( $type ) {
			case 'list':
				wp_enqueue_script(
					'dealertrend-inventory-theme-armadillo-sidebar',
					$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/armadillo_v2/js/sidebar.js',
					array( 'jquery' , 'jquery-ui-core' ),
					$this->plugin_information[ 'Version' ],
					true
				);
				wp_enqueue_script(
					'dealertrend-inventory-responsive-menu',
					$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/armadillo_v2/js/responsive-menu.js',
					array( 'jquery' ),
					$this->plugin_information[ 'Version' ]
				);
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
				wp_enqueue_script(
					'dealertrend-inventory-theme-armadillo-slideshow',
					$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/armadillo_v2/js/slideshow.js',
					array( 'jquery-cycle' ),
					$this->plugin_information[ 'Version' ],
					true
				);
				wp_enqueue_script(
					'dealertrend-inventory-theme-armadillo-detail-buttons',
					$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/armadillo_v2/js/detail-buttons.js',
					array( 'jquery-ui-dialog' ),
					$this->plugin_information[ 'Version' ],
					true
				);
				wp_enqueue_script(
					'dealertrend-inventory-theme-armadillo-tabs',
					$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/armadillo_v2/js/tabs.js',
					array( 'jquery-ui-tabs' ),
					$this->plugin_information[ 'Version' ],
					true
				);
				break;
		}
	} else {
		wp_enqueue_style(
			'dealertrend-detail-print' ,
			$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/armadillo_v2/dealertrend-inventory-print.css' ,
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

	$parameters = $this->parameters;
	$query = '?' . http_build_query( $_GET );

	$breadcrumbs = '<a href="' . $site_url . '/" title="' . $company_name . ': Home Page"><span>&gt;</span>' . urldecode( $company_name ) . '</a>';
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

	$inventory_base = ! empty( $wp_rewrite->rules ) ? $site_url . '/inventory/' : $site_url . '?taxonomy=inventory';
	$crumb_trail = $inventory_base;

	foreach( $parameters as $key => $value ) {
		if( in_array( $key , $put_in_trail ) ) {
			if( ! empty( $wp_rewrite->rules ) ) {
				$crumb_trail .= rawurlencode( urldecode( $value ) ) . '/';
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
