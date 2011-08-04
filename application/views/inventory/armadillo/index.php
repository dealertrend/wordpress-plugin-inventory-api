<?php

	global $wp_rewrite;

	$site_url = site_url();

	setlocale( LC_MONETARY , 'en_US.UTF-8' );

	$company_information = $company_information[ 'data' ];

	$generic_error_message = '<h2 style="font-family:Helvetica,Arial; color:red;">Unable to display inventory. Please contact technical support.</h2><br class="clear" />';

	include_once( ABSPATH . 'wp-content/plugins/' . dirname( $this->plugin_information[ 'PluginBaseName' ] ) . '/application/assets/inventory/php/partials/check_headers.php' );

	$type = isset( $inventory->vin ) ? 'detail' : 'list';

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-button' );
	wp_enqueue_script( 'jquery-ui-dialog' );
	wp_enqueue_script(
		'dealertrend-inventory-theme-armadillo-misc-ui',
		$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/armadillo/js/misc-ui.js',
		array( 'jquery-ui-core' , 'jquery-ui-button' , 'jquery-ui-dialog' ),
		$this->plugin_information[ 'Version' ],
		true
	);

	switch( $type ) {
		case 'list':
			wp_enqueue_script(
				'dealertrend-inventory-theme-armadillo-sidebar',
				$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/armadillo/js/sidebar.js',
				array( 'jquery' , 'jquery-ui-core' ),
				$this->plugin_information[ 'Version' ],
				true
			);
		break;
		case 'detail':
			wp_enqueue_script( 'jquery-ui-tabs' );
			wp_enqueue_script( 'jquery-cycle' , $this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-cycle/2.72/js/jquery.cycle.all.js' , array( 'jquery' ) , '2.72' , true );
			wp_enqueue_script(
				'dealertrend-inventory-api-loan-calculator',
				$this->plugin_information[ 'PluginURL' ] . '/application/assets/inventory/js/loan-calculator.js',
				'jquery',
				$this->plugin_information[ 'Version' ]
			);
			wp_enqueue_script(
				'dealertrend-inventory-theme-armadillo-slideshow',
				$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/armadillo/js/slideshow.js',
				array( 'jquery-cycle' ),
				$this->plugin_information[ 'Version' ],
				true
			);
			wp_enqueue_script(
				'dealertrend-inventory-theme-armadillo-detail-buttons',
				$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/armadillo/js/detail-buttons.js',
				array( 'jquery-ui-dialog' ),
				$this->plugin_information[ 'Version' ],
				true
			);
			wp_enqueue_script(
				'dealertrend-inventory-theme-armadillo-tabs',
				$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/armadillo/js/tabs.js',
				array( 'jquery-ui-tabs' ),
				$this->plugin_information[ 'Version' ],
				true
			);
		break;
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
				$breadcrumbs .= '<a href=' . $crumb_trail . '><span>&gt;</span>' . ucfirst( urldecode( $value ) ) . '</a>';
			} else {
				$crumb_trail .= '&amp;' . rawurlencode( urldecode( $key ) ) . '=' . $value;
				$breadcrumbs .= '<a href=' . $crumb_trail . '><span>&gt;</span>' . ucfirst( urldecode( $value ) ) . '</a>';
			}
		}
	}

	$breadcrumbs = '<div class="armadillo-breadcrumbs">' . $breadcrumbs . '</div>';

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

	echo '<div id="dealertrend-inventory-api">';
	include( dirname( __FILE__ ) . '/' . $type . '.php' );
	echo '</div>';

	flush();
	get_footer();
	flush();

?>
