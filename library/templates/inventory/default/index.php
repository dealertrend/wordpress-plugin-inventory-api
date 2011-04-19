<?php
	if( !$this->status[ 'inventory_json_request' ] ) {
		echo '<h2 style="font-family:Helvetica,Arial; color:red;">Unable to display inventory. Please contact technical support.</h2><br class="clear" />';
		echo '<p>Unable to connect to API.</p>';
		return false;
	}
?>

<br class="clear" id="inventory-top" />
<?php
	$company_information = wp_cache_get( $this->options[ 'api' ][ 'vehicle_management_system' ] . '/api/companies/' . $this->options[ 'company_information' ][ 'id' ] , 'dealertrend_api' );
	$state = $company_information->state;
	$city = $company_information->city;
?>

<?php
	$company_name = strtoupper( $company_information->name );
	$breadcrumbs = '<a href="/" title="' . $company_name . ': Home Page">' . urldecode( $company_name ) . '</a>';
	$do_not_show = array( 'page' , 'per_page' );
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
?>

<?php flush(); ?>

<?php
		$type = isset( $inventory->vin ) ? 'detail' : 'list';
		include( dirname( __FILE__ ) . '/' . $type . '.php' );
		echo $breadcrumbs;
		echo '<a href="#inventory-top" title="Return to Top" class="return-to-top">Return to Top</a>';
?>

<?php flush(); ?>
