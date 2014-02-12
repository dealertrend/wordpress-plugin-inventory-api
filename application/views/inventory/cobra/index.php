<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

	$parameters = $this->parameters;
	$inventory_options = get_inventory_options( $this->options[ 'vehicle_management_system' ] );
	$theme_settings = get_custom_theme_settings( $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ], 'Cobra');
	$loan_settings = get_custom_theme_settings( $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'custom_settings' ], 'loan');
	$rules = get_option( 'rewrite_rules' );

	$company_information = json_decode( $company_information[ 'body' ] );
	$city = $company_information->seo->city;
	$state = $company_information->seo->state;
	$company_name = strtoupper( $company_information->name );
	$country_code =	$company_information->country_code;

	$vehicle_management_system->tracer = 'Obtaining requested inventory.';
	$inventory_information = $vehicle_management_system->get_inventory()->please( array_merge( $this->parameters , array( 'photo_view' => 1 , 'make_filters' =>  $inventory_options['make_filter'] ) ) );
	$inventory = isset( $inventory_information[ 'body' ] ) ? json_decode( $inventory_information[ 'body' ] ) : false;

	$site_url = site_url();
	$type = isset( $inventory->vin ) ? 'detail' : 'list';
	$param_saleclass = isset( $parameters[ 'saleclass' ] ) ? ucwords( $parameters[ 'saleclass' ] ) : 'All';
	$default_tag_names = get_default_tag_names();
	$custom_tag_icons = $this->options[ 'vehicle_management_system' ][ 'tags' ][ 'data' ];

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_script( 'jquery-ui-button' );
	wp_enqueue_script( 'jquery-ui-dialog' );

	if ( ! $inventory_options['disable_responsive'] ) {
		wp_enqueue_style( 
			'cobra-responsive' ,
			$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/cobra/css/cobra-responsive.css' ,
			false,
			$this->plugin_information[ 'Version' ]
		);
	}

	wp_enqueue_script(
		'cobra-theme-js',
		$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/cobra/js/cobra.js',
		array( 'jquery' ),
		$this->plugin_information[ 'Version' ],
		true
	);

	switch( $type ) {
		case 'detail':
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

?>
