<?php

	namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

	global $wp_rewrite;

	$site_url = site_url();

	wp_enqueue_style(
		'jquery-ui-' . $this->options[ 'jquery' ][ 'ui' ][ 'showcase-theme' ],
		$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui/1.8.11/themes/' . $this->options[ 'jquery' ][ 'ui' ][ 'showcase-theme' ] . '/jquery-ui.css',
		false,
		'1.8.11'
	);
	wp_enqueue_style( 'dealertrend-showcase' , $this->plugin_information[ 'PluginURL' ] . '/application/views/showcase/apollo/css/showcase.css' , false );
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_script( 'jquery-ui-dialog' );
	wp_enqueue_script( 'jquery-ui-button' );
	wp_enqueue_script( 'dealertrend-showcase', $this->plugin_information[ 'PluginURL' ] . '/application/views/showcase/apollo/js/showcase.js', array( 'jquery-ui-tabs' ) );
	wp_enqueue_style( 'showcase-responsive' , $this->plugin_information[ 'PluginURL' ] . '/application/views/showcase/apollo/css/responsive.css' , array( 'dealertrend-showcase' ), $this->plugin_information[ 'Version' ] );

	//$ajax_nonce = wp_create_nonce( 'ajax!' );
	$parameters = $this->parameters;
	$query = '?' . http_build_query( $_GET );
	$classification_param = $_GET['filter'];

	$make = isset( $parameters[ 'make' ] ) ? urldecode( $parameters[ 'make' ] ) : false;
	$model = isset( $parameters[ 'model' ] ) ? urldecode( $parameters[ 'model' ] ) : false;
	$trim = isset( $parameters[ 'trim' ] ) ? urldecode( $parameters[ 'trim' ] ) : false;

	$type = ( $make == false ) ? 'makes' : ( ( $model == false ) ? 'models' : ( ( $trim == false ) ? 'trims' : 'trim_details' ) );

	if( ( isset( $makes ) && $makes != false && ! in_array( $make , $this->options[ 'vehicle_reference_system' ][ 'data' ][ 'makes' ] ) ) ) {
		status_header( 400 );
		$type = false;
	}

	$year_filter = $this->options[ 'vehicle_reference_system' ][ 'data' ][ 'year_filter' ];
	$form_id = $this->options[ 'alt_settings' ][ 'gravity_forms' ]['showcase'];
	$display_vms = $this->options[ 'vehicle_reference_system' ][ 'theme_settings' ][ 'apollo' ][ 'display_vms' ];
	$display_vms_count = $this->options[ 'vehicle_reference_system' ][ 'theme_settings' ][ 'apollo' ][ 'display_vms_count' ];
	$custom_message = $this->options[ 'vehicle_reference_system' ][ 'theme_settings' ][ 'apollo' ][ 'custom_message' ][ 'data' ];
	$display_videos = $this->options[ 'vehicle_reference_system' ][ 'theme_settings' ][ 'apollo' ][ 'display_videos' ];
	$custom_videos = $this->options[ 'vehicle_reference_system' ][ 'theme_settings' ][ 'apollo' ][ 'custom_videos' ][ 'data' ];

	get_header();

	$company_information = json_decode( $company_information[ 'body' ] );
	$city = $company_information->seo->city;
	$state = $company_information->seo->state;

	include( dirname( __FILE__ ) . '/' . 'functions.php' );

	$years = get_valid_years( $year_filter );

	if( $type ) {
		echo '<div id="showcase">';
		include( dirname( __FILE__ ) . '/' . $type . '.php' );
		echo '</div>';
	}

	echo "\n" . '<!--' . "\n";
	echo '##################################################' . "\n";
	echo print_r( $this , true ) . "\n";
	echo print_r( $vehicle_reference_system , true ) . "\n";
	echo '##################################################' . "\n";
	echo '-->' . "\n";

	flush();
	get_footer();
	flush();

?>
