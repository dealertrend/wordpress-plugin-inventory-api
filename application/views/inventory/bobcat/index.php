<?php

	global $wp_rewrite;

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_script( 'jquery-ui-cycle' );
	wp_enqueue_script( 'jquery-ui-dialog' );
	wp_enqueue_script( 'dealertrend-inventory-api-detail-tabs' );
	wp_enqueue_script( 'dealertrend-inventory-api-loan-calculator' );

	wp_enqueue_script(
		'dealertrend-inventory-theme-bobcat-slideshow',
		$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/bobcat/js/slideshow.js',
		array( 'jquery-cycle' ),
		$this->plugin_information[ 'Version' ],
		true
	);

	wp_enqueue_script(
		'dealertrend-inventory-theme-bobcat-tabs',
		$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/bobcat/js/tabs.js',
		array( 'jquery-ui-tabs' ),
		$this->plugin_information[ 'Version' ],
		true
	);

	$site_url = site_url();

	$company_information = $company_information[ 'data' ];

	$generic_error_message = '<h2 style="font-family:Helvetica,Arial; color:red;">Unable to display inventory. Please contact technical support.</h2><br class="clear" />';

	$check_host = $vehicle_management_system->check_host();
	status_header( '400' );
	if( $check_host[ 'status' ] != false ) {
		$check_company_id = $vehicle_management_system->check_company_id();
		if( $check_company_id[ 'status' ] != false ) {
			$check_inventory = $vehicle_management_system->check_inventory();
			if( $check_inventory[ 'status' ] != false ) {
				$inventory = $vehicle_management_system->get_inventory( $this->parameters );
				if( $inventory != false ) {
					status_header( '200' );
				}
			}
		}
	}

	get_header();
	flush();

	if( $check_host[ 'status' ] == false ) {
		echo $generic_error_message;
		echo '<p>We were unable to establish a connection to the API. Refreshing the page may resolve this.</p>';
		return false;
	}

	if( $check_company_id[ 'status' ] == false ) {
		echo $generic_error_message;
		echo '<p>We were unable to retreive the company information feed. Refreshing the page may resolve this.</p>';
		return false;
	}

	if( $check_inventory[ 'status' ] == false && $check_inventory[ 'code' ] != 200 ) {
		echo $generic_error_message;
		echo '<p>We were unable to retreive the inventory feed. Refreshing the page may resolve this.</p>';
		return false;
	}

	if( $inventory === false ) {
		echo $generic_error_message;
		echo '<p>We were able to retreive the inventory feed, but while requesting the full feed the connecion timed out. Please refresh the page.</p>';
		return false;
	}

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

	$city = $company_information->city;
	$state = $company_information->state;

	$company_name = strtoupper( $company_information->name );

	$parameters = $this->parameters;
	$query = '?' . http_build_query( $_GET );

	$breadcrumbs = '<a href="' . $site_url . '/" title="' . $company_name . ': Home Page">' . urldecode( $company_name ) . '</a>';
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
				$breadcrumbs .= '<a href=' . $crumb_trail . '> > ' . ucfirst( urldecode( $value ) ) . '</a>';
			} else {
				$crumb_trail .= '&amp;' . rawurlencode( urldecode( $key ) ) . '=' . $value;
				$breadcrumbs .= '<a href=' . $crumb_trail . '> > ' . ucfirst( urldecode( $value ) ) . '</a>';
			}
		}
	}

	$breadcrumbs = '<div class="bobcat-breadcrumbs">' . $breadcrumbs . '</div>';

	echo '<div id="dealertrend-inventory-api">';
	$type = isset( $inventory->vin ) ? 'detail' : 'list';
	include( dirname( __FILE__ ) . '/' . $type . '.php' );
	echo '</div>';

	flush();
	get_footer();
	flush();

?>
