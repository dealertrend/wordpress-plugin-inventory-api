<?php

	global $wp_rewrite;

	wp_enqueue_script(
		'dealertrend-inventory-theme-mobile-websitez',
		$this->plugin_information[ 'PluginURL' ] . '/application/views/mobile/websitez/js/slideshow.js',
		array( 'jquery' , 'jquery-ui-core' , 'jquery-ui-tabs' , 'jquery-cycle' ),
		$this->plugin_information[ 'Version' ],
		true
	);

	flush();
	get_header();
	flush();

	if( $vehicle_management_system->check_host() == false ) {
		echo '<div class="websitez-container"><div class="post"><div class="post-wrapper">';
		echo '<h2 style="font-family:Helvetica,Arial; color:red;">Unable to display inventory. Please contact technical support.</h2><br class="clear" />';
		echo '<p>Unable to connect to API.</p>';
		echo '</div></div></div>';
		return false;
	}

	if( $vehicle_management_system->check_company_id() == false ) {
		echo '<div class="websitez-container"><div class="post"><div class="post-wrapper">';
		echo '<h2 style="font-family:Helvetica,Arial; color:red;">Unable to display inventory. Please contact technical support.</h2><br class="clear" />';
		echo '<p>Unable to validate company information.</p>';
		echo '</div></div></div>';
		return false;
	}

	if( $vehicle_management_system->check_inventory() == false ) {
		echo '<div class="websitez-container"><div class="post"><div class="post-wrapper">';
		echo '<h2 style="font-family:Helvetica,Arial; color:red;">Unable to display inventory. Please contact technical support.</h2><br class="clear" />';
		echo '<p>Unable to retrieve inventory.</p>';
		echo '</div></div></div>';
		return false;
	}
	
	$company_information = $vehicle_management_system->get_company_information();
	$state = $company_information['data']->state;
	$city = $company_information['data']->city;
	$company_name = strtoupper( $company_information['data']->name );

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
	?>
	<div class="websitez-container dealertrend-mobile inventory">
		<div class="post">
			<div class="post-wrapper">
				<div class="company-contact">
					<p><a href="tel:1<?php echo str_replace(array("(",")"," ","-"),"",$company_information['data']->phone);?>">Click To Call<br /><?php echo $company_information['data']->phone; ?></a></p>
				</div>
				<div class="company-details">
					<p><strong><?php echo $company_information['data']->name; ?></strong></p>
					<p><?php echo $company_information['data']->street; ?></p>
					<p><?php echo $company_information['data']->city.", ".$company_information['data']->state." ".$company_information['data']->zip; ?></p>
				</div>
				<div style="clear: both;"></div>
			</div>
		</div>
	</div>
	<?php
	get_footer();
	flush();

?>
<link rel="stylesheet" id="dealertrend-inventory-api-css" href="<?php echo $this->plugin_information[ 'PluginURL' ]; ?>/application/views/mobile/websitez/dealertrend-inventory-api.css" type="text/css" media="all">