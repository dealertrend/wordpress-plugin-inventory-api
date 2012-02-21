<?php

	namespace Wordpress\Plugins\Dealertrend\Inventory\Api;
print_me( __FILE__ );

	global $wp_rewrite;

	$vehicle_management_system->tracer = 'Obtaining requested inventory.';
	$inventory_information = $vehicle_management_system->get_inventory()->please( $this->parameters );

	$inventory = json_decode( $inventory_information[ 'body' ] );

	$site_url = site_url();
	$generic_error_message = '<h2 style="font-family:Helvetica,Arial; color:red;">Unable to display inventory. Please contact technical support.</h2><br class="clear" />';
	$type = isset( $inventory->vin ) ? 'detail' : 'list';

	$default_scripts = array(
		'jquery',
		'jquery-ui-core',
		'jquery-ui-tabs'
	);

	foreach( $default_scripts as $key => $value ) {
		wp_enqueue_script( $value );
	}

	switch( $type ) {
		case 'detail':
			wp_enqueue_script( 'jquery-cycle' , $this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-cycle/2.72/js/jquery.cycle.all.js' , array( 'jquery' ) , '2.72' , true );
			wp_enqueue_script(
				'dealertrend-inventory-api-loan-calculator',
				$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/bobcat/js/loan-calculator.js',
				'jquery',
				$this->plugin_information[ 'Version' ]
			);
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
		break;
	}

	get_header();
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
	$city = trim( $company_information->seo->city );
	$state = trim( $company_information->seo->state );
	$company_name = trim( strtoupper( $company_information->name ) );

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
	include( dirname( __FILE__ ) . '/' . $type . '.php' );
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
	get_footer();
	flush();

?>
