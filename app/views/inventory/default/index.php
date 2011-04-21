<?php

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
		echo '<p>Unable to retreive inventory.</p>';
		return false;
	}

	$company_information = $vehicle_management_system->get_company_information();
	$state = $company_information->state;
	$city = $company_information->city;
	$company_name = strtoupper( $company_information->name );

	$breadcrumbs = '<a href="/" title="' . $company_name . ': Home Page">' . urldecode( $company_name ) . '</a>';
	$do_not_show = array( 'page' , 'per_page', 'trim', 'body_style', 'vehicleclass', 'sort', 'city', 'state' );

	if( count( $this->parameters > 1 ) ) {
		$crumb_trail = null;
		if( !empty( $wp_rewrite->rules ) ) {
			$breadcrumbs .= ' > <a href="/inventory/" title="' . $company_name . ': Inventory">INVENTORY</a>';
						$crumb_trail = '/inventory';
						foreach( $this->parameters as $key => $value ) {
				if( !in_array( $key ,$do_not_show ) && $key != 'taxonomy' ) {
					$crumb_trail .= '/' . $value;
					$breadcrumbs .= ' > <a href=' . $crumb_trail . '>' . strtoupper( urldecode( $value ) ) . '</a>';
				}
			}
		} else {
			$breadcrumbs .= ' > <a href="?taxonomy=inventory" title="' . $company_name . ': Inventory">INVENTORY</a>';
			$crumb_trail = '?taxonomy=inventory';
			foreach( $this->parameters as $key => $value ) {
				if( !in_array( $key ,$do_not_show ) && $key != 'taxonomy' ) {
					$crumb_trail .= '&amp;' . $key . '=' . $value;
					$breadcrumbs .= ' > <a href=' . $crumb_trail . '>' . strtoupper( urldecode( $value ) ) . '</a>';
				}
			}
		}
	}
	$breadcrumbs = '<div class="breadcrumbs">' . $breadcrumbs . '</div>';

	$inventory = $vehicle_management_system->get_inventory( $this->parameters );

?>

<br class="clear" id="top" />

<?php
	$type = isset( $inventory->vin ) ? 'detail' : 'list';
	include( dirname( __FILE__ ) . '/' . $type . '.php' );
	echo $breadcrumbs;
	echo '<a href="#top" title="Return to Top" class="return-to-top">Return to Top</a>';
?>

<?php
	flush();
	get_footer();
	flush();
?>
