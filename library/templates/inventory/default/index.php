<?php
	if(!$inventory) {
		echo '<h2 style="font-family:Helvetica,Arial; color:red;">Unable to display inventory. Please contact technical support.</h2><br class="clear" />';
		return false;
	}
?>

<br class="clear" id="inventory-top" />
<?php
	$company_information = wp_cache_get( $this->options[ 'api' ][ 'vehicle_management_system' ] . '/api/companies/' . $this->options[ 'company_information' ][ 'id' ] , 'dealertrend_api' );
	$state = $company_information->state;
	$city = $company_information->city;
?>

<div class="breadcrumbs">
	<?php
		$company_name = strtoupper( $company_information->name );
		$breadcrumbs = '<a href="/" title="' . $company_name . ': Home Page">' . urldecode( $company_name ) . '</a>';
		$do_not_show = array( 'page' , 'per_page' );
		if( count( $this->parameters > 1 ) ) {
			$crumb_trail = null;
			if( !empty( $wp_rewrite->rules ) ) {
				foreach( $this->parameters as $key => $value ) {
					if( !in_array( $key ,$do_not_show ) ) {
						$crumb_trail .= '/' . $value;
						$breadcrumbs .= ' > <a href=' . $crumb_trail . '>' . strtoupper( urldecode( $value ) ) . '</a>';
					}
				}
			} else {
				$crumb_trail = '?taxonomy=inventory';
				foreach( $this->parameters as $key => $value ) {
					if( !in_array( $key ,$do_not_show ) && $key != 'taxonomy' ) {
						$crumb_trail .= '&amp;' . $key . '=' . $value;
						$breadcrumbs .= ' > <a href=' . $crumb_trail . '>' . strtoupper( urldecode( $value ) ) . '</a>';
					}
				}
			}
		}
		echo $breadcrumbs;
	?>
</div>

<?php flush(); ?>

<?php
	$type = isset( $inventory->vin ) ? 'detail' : 'list';
	include( dirname( __FILE__ ) . '/' . $type . '.php' );
?>

<?php flush(); ?>

<div class="breadcrumbs">
<?php
	echo $breadcrumbs;
?>
</div>
<?php echo '<a href="#inventory-top" title="Return to Top" class="return-to-top">Return to Top</a>'; ?>
