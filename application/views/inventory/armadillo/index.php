<?php

	global $wp_rewrite;

	$site_url = site_url();

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui' );
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_script( 'jquery-ui-cycle' );
	wp_enqueue_script( 'jquery-ui-dialog' );

	wp_enqueue_script(
		'dealertrend-inventory-theme-armadillo-slideshow',
		$this->plugin_information[ 'PluginURL' ] . '/app/views/inventory/armadillo/js/slideshow.js',
		array( 'jquery-cycle' , 'dealertrend-inventory-api' ),
		$this->plugin_information[ 'Version' ],
		true
	);

	wp_enqueue_script(
		'dealertrend-inventory-theme-armadillo-detail-buttons',
		$this->plugin_information[ 'PluginURL' ] . '/app/views/inventory/armadillo/js/detail-buttons.js',
		array( 'jquery-ui-dialog' , 'dealertrend-inventory-api' ),
		$this->plugin_information[ 'Version' ],
		true
	);

	wp_enqueue_script(
		'dealertrend-inventory-theme-armadillo-sidebar',
		$this->plugin_information[ 'PluginURL' ] . '/app/views/inventory/armadillo/js/sidebar.js',
		array( 'jquery' , 'jquery-ui-core' , 'dealertrend-inventory-api' ),
		$this->plugin_information[ 'Version' ],
		true
	);

	wp_enqueue_script(
		'dealertrend-inventory-theme-armadillo-tabs',
		$this->plugin_information[ 'PluginURL' ] . '/app/views/inventory/armadillo/js/tabs.js',
		array( 'jquery-ui-tabs' , 'dealertrend-inventory-api' ),
		$this->plugin_information[ 'Version' ],
		true
	);

	flush();
	get_header();
	flush();

	$company_information = $company_information[ 'data' ];

	echo "\n" . '<!--' . "\n";
	echo '##################################################' . "\n";
	echo print_r( $this , true ) . "\n";
	echo "[ Inventory Plugin Information ]\n";
	echo print_r( $company_information , true ) . "\n";
	echo print_r( $vehicle_management_system , true ) . "\n";
	echo print_r( $dynamic_site_headers , true ) . "\n";
	echo '##################################################' . "\n";
	echo '-->' . "\n";

	$check_host = $vehicle_management_system->check_host();
	if( $check_host[ 'status' ] == false ) {
		echo '<h2 style="font-family:Helvetica,Arial; color:red;">Unable to display inventory. Please contact technical support.</h2><br class="clear" />';
		echo '<p>Unable to connect to API.</p>';
		return false;
	}

	$check_company_id = $vehicle_management_system->check_company_id();
	if( $check_company_id[ 'status' ] == false ) {
		echo '<h2 style="font-family:Helvetica,Arial; color:red;">Unable to display inventory. Please contact technical support.</h2><br class="clear" />';
		echo '<p>Unable to validate company information.</p>';
		return false;
	}

	$check_inventory = $vehicle_management_system->check_inventory();

	if( $check_inventory[ 'status' ] == false && $check_inventory[ 'code' ] != 200 ) {
		echo '<h2 style="font-family:Helvetica,Arial; color:red;">Unable to display inventory. Please contact technical support.</h2><br class="clear" />';
		echo '<p>Unable to retrieve inventory.</p>';
		return false;
	}

	$inventory = $vehicle_management_system->get_inventory( $this->parameters );

	if( $inventory === false ) {
		echo '<h2 style="font-family:Helvetica,Arial; color:red;">Unable to display inventory. Please contact technical support.</h2><br class="clear" />';
		echo '<p>The inventory feed timed out while trying to display. Please refresh the page. If the feed refuses to return data, then the given parameters may be invalid.</p>';
		return false;
	}

	$state = $company_information->state;
	$city = $company_information->city;

	$company_name = strtoupper( $company_information->name );

	$parameters = $this->parameters;

	$breadcrumbs = '<a href="' . $site_url . '/" title="' . $company_name . ': Home Page"><span>&gt;</span>' . urldecode( $company_name ) . '</a>';
	$do_not_show = array( 'page' , 'per_page' , 'trim' , 'body_style' , 'vehicleclass' , 'sort' , 'city' , 'state' , 'search', 'price_from' , 'price_to' , 'certified' );
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

	if( count( $parameters > 1 ) ) {
		$crumb_trail = $site_url . '/inventory/';
		if( !empty( $wp_rewrite->rules ) ) {
			foreach( $parameters as $key => $value ) {
				if( !in_array( $key ,$do_not_show ) ) {
					$crumb_trail .= rawurlencode( urldecode( $value ) ) . '/';
					$breadcrumbs .= '<a href=' . $crumb_trail . '><span>&gt;</span>' . ucfirst( urldecode( $value ) ) . '</a>';
				}
			}
		} else {
			$crumb_trail = '?taxonomy=inventory';
			foreach( $parameters as $key => $value ) {
				if( !in_array( $key ,$do_not_show ) ) {
					$crumb_trail .= '&amp;' . rawurlencode( urldecode( $key ) ) . '=' . $value;
					$breadcrumbs .= '<a href=' . $crumb_trail . '><span>&gt;</span>' . ucfirst( urldecode( $value ) ) . '</a>';
				}
			}
		}
	}

	$breadcrumbs = '<div class="armadillo-breadcrumbs">' . $breadcrumbs . '</div>';

	$type = isset( $inventory->vin ) ? 'detail' : 'list';
	include( dirname( __FILE__ ) . '/' . $type . '.php' );

	flush();
	get_footer();
	flush();

?>
