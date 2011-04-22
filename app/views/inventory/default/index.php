<?php

	global $wp_rewrite;

	wp_enqueue_script(
		'dealertrend-inventory-theme-default',
		$this->meta_information[ 'PluginURL' ] . '/app/views/inventory/default/js/slideshow.js',
		array( 'jquery' , 'jquery-ui-core' , 'jquery-ui-tabs' , 'jquery-cycle', 'dealertrend-inventory-api' ),
		$this->meta_information[ 'Version' ],
		true
	);

	flush();
	get_header();
	flush();

	if( $vehicle_management_system->check_host() == false ) {
		echo '<h2 style="font-family:Helvetica,Arial; color:red;">Unable to display inventory. Please contact technical support.</h2><br class="clear" />';
		echo '<p>Unable to connect to API.</p>';
		return false;
	}

	if( $vehicle_management_system->check_company_id() == false ) {
		echo '<h2 style="font-family:Helvetica,Arial; color:red;">Unable to display inventory. Please contact technical support.</h2><br class="clear" />';
		echo '<p>Unable to validate company information.</p>';
		return false;
	}

	if( $vehicle_management_system->check_inventory() == false ) {
		echo '<h2 style="font-family:Helvetica,Arial; color:red;">Unable to display inventory. Please contact technical support.</h2><br class="clear" />';
		echo '<p>Unable to retrieve inventory.</p>';
		return false;
	}

	$company_information = $vehicle_management_system->get_company_information();
	$state = $company_information->state;
	$city = $company_information->city;
	$company_name = strtoupper( $company_information->name );

	$breadcrumbs = '<a href="/" title="' . $company_name . ': Home Page">' . urldecode( $company_name ) . '</a>';
	$do_not_show = array( 'page' , 'per_page', 'trim', 'body_style', 'vehicleclass', 'sort', 'city', 'state' );

	if( count( $this->parameters > 1 ) ) {
		$crumb_trail = '/inventory/';
		if( !empty( $wp_rewrite->rules ) ) {
			foreach( $this->parameters as $key => $value ) {
				if( !in_array( $key ,$do_not_show ) && $key != 'taxonomy' ) {
					$crumb_trail .= $value . '/';
					$breadcrumbs .= ' > <a href=' . $crumb_trail . '>' . ucfirst( urldecode( $value ) ) . '</a>';
				} 
			} 
		} else {
			$crumb_trail = '?taxonomy=inventory';
			foreach( $this->parameters as $key => $value ) {
				if( !in_array( $key ,$do_not_show ) && $key != 'taxonomy' ) {
					$crumb_trail .= '&amp;' . $key . '=' . $value;
					$breadcrumbs .= ' > <a href=' . $crumb_trail . '>' . ucfirst( urldecode( $value ) ) . '</a>';
				} 
			} 
		} 
	}

	$breadcrumbs = '<div class="breadcrumbs">' . $breadcrumbs . '</div>';

	$inventory = $vehicle_management_system->get_inventory( $this->parameters );

	$type = isset( $inventory->vin ) ? 'detail' : 'list';
	include( dirname( __FILE__ ) . '/' . $type . '.php' );

	flush();
	get_footer();
	flush();

?>
