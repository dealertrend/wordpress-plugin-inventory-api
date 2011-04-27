<?php

	global $wp_rewrite;

	wp_enqueue_script(
		'dealertrend-inventory-theme-armadillo-slideshow',
		$this->meta_information[ 'PluginURL' ] . '/app/views/inventory/armadillo/js/slideshow.js',
		array( 'jquery' , 'jquery-ui-core' , 'jquery-ui-tabs' , 'jquery-cycle', 'dealertrend-inventory-api' ),
		$this->meta_information[ 'Version' ],
		true
	);

	wp_enqueue_script(
		'dealertrend-inventory-theme-armadillo-sidebar',
		$this->meta_information[ 'PluginURL' ] . '/app/views/inventory/armadillo/js/sidebar.js',
		array( 'jquery' , 'jquery-ui-core' , 'jquery-ui-tabs' , 'jquery-cycle', 'dealertrend-inventory-api' ),
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

	if( $check_inventory[ 'status' ] == false ) {
		echo '<h2 style="font-family:Helvetica,Arial; color:red;">Unable to display inventory. Please contact technical support.</h2><br class="clear" />';
		echo '<p>Unable to retrieve inventory.</p>';
		return false;
	}

	$inventory = $vehicle_management_system->get_inventory( $this->parameters );

	if( $inventory == false ) {
		echo '<h2 style="font-family:Helvetica,Arial; color:red;">Unable to display inventory. Please contact technical support.</h2><br class="clear" />';
		echo '<p>Inventory query timed out.</p>';
		return false;
	}

	$company_information = $company_information[ 'data' ];
	$state = $company_information->state;
	$city = $company_information->city;

	$company_name = strtoupper( $company_information->name );

	$breadcrumbs = '<a href="/" title="' . $company_name . ': Home Page"><span>&gt;</span>' . urldecode( $company_name ) . '</a>';
	$do_not_show = array( 'page' , 'per_page', 'trim', 'body_style', 'vehicleclass', 'sort', 'city', 'state' );
	$sale_class = isset( $parameters[ 'saleclass' ] ) ? ucwords( $parameters[ 'saleclass' ] ) : 'All';

	$parameters = $this->parameters;

	unset( $parameters[ 'taxonomy' ] );

	if( !isset( $parameters[ 'saleclass' ] ) ){
		if( isset( $inventory->saleclass ) ) {
			$substitute = $inventory->saleclass;
		} else {
			$substitute = isset( $inventory[ 0 ]->saleclass ) ? $inventory[ 0 ]->saleclass : NULL;
		}
		array_shift( $parameters );
		$substitute_array = array( 'saleclass' => $substitute );
		$parameters = $substitute_array + $parameters;
	}

	if( count( $parameters > 1 ) ) {
		$crumb_trail = '/inventory/';
		if( !empty( $wp_rewrite->rules ) ) {
			foreach( $parameters as $key => $value ) {
				if( !in_array( $key ,$do_not_show ) ) {
					$crumb_trail .= $value . '/';
					$breadcrumbs .= '<a href=' . $crumb_trail . '><span>&gt;</span>' . ucfirst( urldecode( $value ) ) . '</a>';
				}
			}
		} else {
			$crumb_trail = '?taxonomy=inventory';
			foreach( $parameters as $key => $value ) {
				if( !in_array( $key ,$do_not_show ) ) {
					$crumb_trail .= '&amp;' . $key . '=' . $value;
					$breadcrumbs .= '<a href=' . $crumb_trail . '><span>&gt;</span>' . ucfirst( urldecode( $value ) ) . '</a>';
				}
			}
		}
	}

	$breadcrumbs = '<div class="breadcrumbs">' . $breadcrumbs . '</div>';

	$type = isset( $inventory->vin ) ? 'detail' : 'list';
	include( dirname( __FILE__ ) . '/' . $type . '.php' );

	flush();
	get_footer();
	flush();

?>
