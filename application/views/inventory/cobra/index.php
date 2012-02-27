<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

global $wp_rewrite;

$vehicle_management_system->tracer = 'Obtaining requested inventory.';
$inventory_information = $vehicle_management_system->get_inventory()->please( array_merge( $this->parameters , array( 'photo_view' => 1	) ) );
$inventory = isset( $inventory_information[ 'body' ] ) ? json_decode( $inventory_information[ 'body' ] ) : false;

$site_url = site_url();
$generic_error_message = '<h2 style="font-family:Helvetica,Arial; color:red;">Unable to display inventory. Please contact technical support.</h2><br class="clear" />';
$type = isset( $inventory->vin ) ? 'detail' : 'list';

wp_enqueue_script( 'jquery' );
wp_enqueue_script( 'jquery-ui-core' );
wp_enqueue_script( 'jquery-ui-tabs' );
wp_enqueue_script( 'jquery-ui-button' );
wp_enqueue_script( 'jquery-ui-dialog' );

switch( $type ) {
	case 'detail':
		wp_enqueue_script( 'jquery-cycle' , $this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-cycle/2.72/js/jquery.cycle.all.js' , array( 'jquery' ) , '2.72' , true );
		wp_enqueue_script( 'jquery-lightbox' , $this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-lightbox/1.0/js/jquery.lightbox.js' , array( 'jquery' ) , '0.5' , true );
		wp_enqueue_style( 'jquery-lightbox' , $this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-lightbox/1.0/css/jquery.lightbox.css' , false , '0.5' );
		wp_enqueue_style( 'jquery-jscrollpane' , $this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/cobra/css/jquery.jscrollpane.css' , false );
		wp_enqueue_script(
			'dealertrend-inventory-api-loan-calculator',
			$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/cobra/js/loan-calculator.js',
			'jquery',
			$this->plugin_information[ 'Version' ]
		);
		wp_enqueue_script(
			'dealertrend-inventory-theme-cobra-slideshow',
			$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/cobra/js/slideshow.js',
			array( 'jquery-cycle' ),
			$this->plugin_information[ 'Version' ],
			true
		);
		wp_enqueue_script(
			'dealertrend-inventory-theme-cobra-jscrollpane-box',
			$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/cobra/js/jquery.jscrollpane.min.js',
			array( 'jquery' ),
			$this->plugin_information[ 'Version' ],
			true
		);
		wp_enqueue_script(
			'dealertrend-inventory-theme-cobra-jquery-mousewheel',
			$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/cobra/js/jquery.mousewheel.js',
			array( 'jquery' , 'dealertrend-inventory-theme-cobra-jscrollpane-box' ),
			$this->plugin_information[ 'Version' ],
			true
		);
		wp_enqueue_script(
			'dealertrend-inventory-theme-cobra-detail-buttons',
			$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/cobra/js/detail-buttons.js',
			array( 'jquery-ui-dialog' , 'jquery-ui-tabs' , 'dealertrend-inventory-theme-cobra-jscrollpane-box' , 'dealertrend-inventory-theme-cobra-jquery-mousewheel' ),
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
$company_name = strtoupper( trim( $company_information->name ) );

$parameters = $this->parameters;
$query = '?' . http_build_query( $_GET );

$breadcrumbs = '<a href="' . $site_url . '/" title="' . $company_name . ': Home Page">' . ucwords( strtolower( urldecode( $company_name ) ) ) . '</a>';
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
			$breadcrumbs .= ' <span>&gt;</span> <a href=' . $crumb_trail . '>' . ucfirst( urldecode( $value ) ) . '</a>';
		} else {
			$crumb_trail .= '&amp;' . rawurlencode( urldecode( $key ) ) . '=' . $value;
			$breadcrumbs .= ' <span>&gt;</span> <a href=' . $crumb_trail . '>' . ucfirst( urldecode( $value ) ) . '</a>';
		}
	}
}

$breadcrumbs = '<div class="cobra-breadcrumbs">' . $breadcrumbs . '</div>';

echo '<div id="dealertrend-inventory-api">';
include( dirname( __FILE__ ) . '/' . $type . '.php' );
echo '</div>';

flush();
get_footer();

?>
