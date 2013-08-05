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
	wp_enqueue_style( 'dealertrend-showcase' , $this->plugin_information[ 'PluginURL' ] . '/application/views/showcase/default/css/showcase.css' , false );
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_script( 'jquery-ui-dialog' );
	wp_enqueue_script( 'jquery-ui-button' );
	wp_enqueue_script( 'dealertrend-showcase', $this->plugin_information[ 'PluginURL' ] . '/application/views/showcase/default/js/showcase.js', array( 'jquery-ui-tabs' ) );
	wp_enqueue_style( 'showcase-mobile' , $this->plugin_information[ 'PluginURL' ] . '/application/views/showcase/default/css/mobile.css' , array( 'dealertrend-showcase' ), $this->plugin_information[ 'Version' ], 'only screen and (max-width: 480px)' );

	$ajax_nonce = wp_create_nonce( 'ajax!' );
	$parameters = $this->parameters;
	$query = '?' . http_build_query( $_GET );

	$make = isset( $parameters[ 'make' ] ) ? urldecode( $parameters[ 'make' ] ) : false;
	$model = isset( $parameters[ 'model' ] ) ? urldecode( $parameters[ 'model' ] ) : false;
	$trim = isset( $parameters[ 'trim' ] ) ? urldecode( $parameters[ 'trim' ] ) : false;

	$type = ( $make == false ) ? 'makes' : ( ( $model == false ) ? 'models' : 'trims' );

	if(
		( isset( $makes ) && $makes != false && ! in_array( $make , $this->options[ 'vehicle_reference_system' ][ 'data' ][ 'makes' ] ) ) ||
		( isset( $models ) && $models != false && ! in_array( $model , $this->options[ 'vehicle_reference_system' ][ 'data' ][ 'models' ] ) )
	) {
		status_header( 400 );
		$type = false;
	}
	$year_filter = $this->options[ 'vehicle_reference_system' ][ 'data' ][ 'year-filter' ];

	get_header();

	$current_year = date( 'Y' );
	$last_year = $current_year - 1;
	$next_year = $current_year + 1;

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
