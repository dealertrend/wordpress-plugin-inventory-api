<?php

	global $wp_rewrite;

	$site_url = site_url();

	setlocale( LC_MONETARY , 'en_US.UTF-8' );

	$company_information = json_decode( $company_information[ 'body' ] );

  $vehicle_management_system->tracer = 'Obtaining requested inventory.';
  $inventory_information = $vehicle_management_system->get_inventory()->please( $this->parameters );
  $inventory = isset( $inventory_information[ 'body' ] ) ? json_decode( $inventory_information[ 'body' ] ) : false;

	$generic_error_message = '<h2 style="font-family:Helvetica,Arial; color:red;">Unable to display inventory. Please contact technical support.</h2><br class="clear" />';

	$type = isset( $inventory->vin ) ? 'detail' : 'list';

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );

	switch( $type ) {
		case 'detail':
			wp_enqueue_script( 'jquery-ui-cycle' );
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

	$breadcrumbs = '<div class="websitez-breadcrumbs">' . $breadcrumbs . '</div>';

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
	$type = isset( $inventory->vin ) ? 'detail' : 'list';
	include( dirname( __FILE__ ) . '/' . $type . '.php' );
	echo '</div>';

	flush();

	$name = isset( $company_information->name ) ? $company_information->name : NULL;
	$street = isset( $company_information->street ) ? $company_information->street : NULL;
	$city = isset( $company_information->city ) ? $company_information->city : NULL;
	$state = isset( $company_information->state ) ? $company_information->state : NULL;
	$zip = isset( $company_information->zip ) ? $company_information->zip : NULL;
	$phone = isset( $company_information->phone ) ? $company_information->phone : NULL;

	?>

	<div class="websitez-container dealertrend-mobile inventory">
		<div class="post">
			<div class="post-wrapper">
				<div class="company-contact">
				</div>
				<div class="company-details">
					<p><strong><?php echo $name; ?></strong></p>
					<p><?php echo $street; ?></p>
					<p><?php echo $city . ', ' . $state . ' ' . $zip; ?></p>
					<p><strong><a href="tel:1<?php echo str_replace( array( "(" , ")" , " " , "-" ) , "" , $phone ); ?>">Call: <?php echo $phone; ?></a></strong></p>
				</div>
				<div style="clear: both;"></div>
			</div>
		</div>
	</div>

	<?php

	get_footer();
	flush();

?>
