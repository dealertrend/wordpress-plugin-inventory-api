<?php

	global $wp_rewrite;

	$site_url = site_url();

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui' );
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_script( 'jquery-ui-cycle' );

	wp_enqueue_script(
		'dealertrend-inventory-theme-bobcat-slideshow',
		$this->meta_information[ 'PluginURL' ] . '/app/views/inventory/bobcat/js/slideshow.js',
		array( 'jquery-cycle', 'dealertrend-inventory-api' ),
		$this->meta_information[ 'Version' ],
		true
	);

	flush();
	get_header();
	flush();

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

	$company_information = $vehicle_management_system->get_company_information();
	$company_information = $company_information[ 'data' ];
	$state = $company_information->state;
	$city = $company_information->city;

	$company_name = strtoupper( $company_information->name );

	$parameters = $this->parameters;

	$breadcrumbs = '<a href="' . $site_url . '/" title="' . $company_name . ': Home Page">' . urldecode( $company_name ) . '</a>';
	$do_not_show = array( 'page' , 'per_page' , 'trim' , 'body_style' , 'vehicleclass' , 'sort' , 'city' , 'state' , 'search', 'price_from' , 'price_to' , 'certified' );

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
					$breadcrumbs .= '<a href=' . $crumb_trail . '> > ' . ucfirst( urldecode( $value ) ) . '</a>';
				}
			}
		} else {
			$crumb_trail = '?taxonomy=inventory';
			foreach( $parameters as $key => $value ) {
				if( !in_array( $key ,$do_not_show ) ) {
					$crumb_trail .= '&amp;' . rawurlencode( urldecode( $key ) ) . '=' . $value;
					$breadcrumbs .= '<a href=' . $crumb_trail . '> > ' . ucfirst( urldecode( $value ) ) . '</a>';
				}
			}
		}
	}

	$breadcrumbs = '<div class="bobcat-breadcrumbs">' . $breadcrumbs . '</div>';

	$inventory = $vehicle_management_system->get_inventory( $this->parameters );

	$type = isset( $inventory->vin ) ? 'detail' : 'list';
	include( dirname( __FILE__ ) . '/' . $type . '.php' );

	flush();
	get_footer();
	flush();

?>
