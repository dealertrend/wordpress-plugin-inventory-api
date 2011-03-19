<br class="clear" id="inventory-top" />
<?php
  $company_information = wp_cache_get( $this->options[ 'api' ][ 'vehicle_management_system' ] . '/api/companies/' . $this->options[ 'company_information' ][ 'id' ] , 'dealertrend_api' );
  $state = $company_information->state;
  $city = $company_information->city;
?>

<div class="breadcrumbs">
	<?php
		$company_name = strtoupper( $company_information->name );
		$breadcrumbs = '<a href="/" title="' . $company_name . ': Home Page">' . $company_name . '</a>'; 
		if( count( $this->parameters > 1 ) ) {
			$crumb_trail = null;
			foreach( $this->parameters  as $parameter ) {
				$crumb_trail .= '/' . $parameter;
				$breadcrumbs .= ' > <a href=' . $crumb_trail . '>' . strtoupper( $parameter ) . '</a>';
			}
		}
		echo $breadcrumbs;
	?>
</div>

<?php
  $type = isset( $inventory->vin ) ? 'detail' : 'list';
  include( dirname( __FILE__ ) . '/' . $type . '.php' );
?>

<div class="breadcrumbs">
<?php
	echo $breadcrumbs;
	echo '<a href="#inventory-top" title="Return to Top" class="return-to-top">Return to Top</a>';
?>
</div>
