<?php

	global $wp_rewrite;

	function sort_models( $a , $b ) {
		if( $a->classification === $b->classification ) {
			if( $a->name > $b->name ) {
				return +1;
			} else {
				return -1;
			}
			return 0;
		}
		return ( $a->classification > $b->classification ) ? +1 : -1;
	}

	// Sort by newest year, and cheapest msrp.
	function sort_trims( $a , $b ) {
		if( $a->year === $b->year ) {
			if( $a->msrp > $b->msrp ) {
				return +1;
			} else {
				return -1;
			}
			return 0;
		}
		return ( $a->year < $b->year ) ? +1 : -1;
	}

	$site_url = site_url();

	wp_enqueue_style( 'jquery-ui-' . $this->options[ 'jquery' ][ 'ui' ][ 'theme' ] , $this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui/1.8.11/themes/' . $this->options[ 'jquery' ][ 'ui' ][ 'theme' ] . '/jquer    y-ui.css' , false , '1.8.11' );
	wp_enqueue_style( 'dealertrend-showcase' , $this->plugin_information[ 'PluginURL' ] . '/application/views/showcase/default/css/showcase.css' , false );
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_script( 'dealertrend-showcase', $this->plugin_information[ 'PluginURL' ] . '/application/views/showcase/default/js/showcase.js', array( 'jquery-ui-tabs' ) );

	get_header();
	flush();

	$parameters = $this->parameters;
	$query = '?' . http_build_query( $_GET );

	$make = isset( $parameters[ 'make' ] ) ? urldecode( $parameters[ 'make' ] ) : false;
	$model = isset( $parameters[ 'model' ] ) ? urldecode( $parameters[ 'model' ] ) : false;
	$trim = isset( $parameters[ 'trim' ] ) ? urldecode( $parameters[ 'trim' ] ) : false;
	$acode = isset( $parameters[ 'acode' ] ) ? urldecode( $parameters[ 'acode' ] ) : false;

	flush();
?>

<?php

	$current_year = date( 'Y' );
	$last_year = $current_year - 1;
	$next_year = $current_year + 1;

	echo '<div id="showcase">';
	if( $make === false ) {

		$make_data[ $last_year ] = $vehicle_reference_system->get_makes()->please( array( 'year' => $last_year ) );
		$make_data[ $current_year ] = $vehicle_reference_system->get_makes()->please( array( 'year' => $current_year ) );
		$make_data[ $next_year ] = $vehicle_reference_system->get_makes()->please( array( 'year' => $next_year ) );

		$make_data[ 'data' ] = array_merge( $make_data[ $last_year ][ 'data' ] , $make_data[ $current_year ][ 'data' ] , $make_data[ $next_year ][ 'data' ] );

		$makes = $make_data[ 'data' ];

		echo '<h2><a href="/showcase/">Showcase</a> &rsaquo; All Makes</h2>';
		echo '<hr />';
		echo '<div class="group makes">';
		foreach( $makes as $make ) {
			if( ! empty( $make->image_url ) ) {
				echo '<div class="make">';
				echo '<a href="/showcase/' . $make->name . '/" title="' . $make->name . '"><img src="' . $make->image_url . '" /></a>';
				echo '<div class="classifications">';
				echo '<a href="/showcase/' . $make->name . '/?vehicleclass=all" title="All ' . $make->name . ' Models">All</a> | ';
				echo '<a href="/showcase/' . $make->name . '/?vehicleclass=all" title="' . $make->name . ' Cars">Cars</a> | ';
				echo '<a href="/showcase/' . $make->name . '/?vehicleclass=all" title="' . $make->name . ' Trucks">Trucks</a> | ';
				echo '<a href="/showcase/' . $make->name . '/?vehicleclass=all" title="' . $make->name . ' SUV\'s">SUV\'s</a> | ';
				echo '<a href="/showcase/' . $make->name . '/?vehicleclass=all" title="' . $make->name . ' Vans">Vans</a>';
				echo '</div>';
				echo '</div>';
			}
		}
		echo '</div>';
	} elseif( $model === false ) {

		$model_data[ $last_year ] = $vehicle_reference_system->get_models()->please( array( 'make' => $make , 'year' => $last_year ) );
		$model_data[ $current_year ] = $vehicle_reference_system->get_models()->please( array( 'make' => $make , 'year' => $current_year ) );
		$model_data[ $next_year ] = $vehicle_reference_system->get_models()->please( array( 'make' => $make , 'year' => $next_year ) );

		$model_data[ $last_year ][ 'data' ] = is_array( $model_data[ $last_year ][ 'data' ] ) ? $model_data[ $last_year ][ 'data' ] : array();
		$model_data[ $current_year ][ 'data' ] = is_array( $model_data[ $current_year ][ 'data' ] ) ? $model_data[ $current_year ][ 'data' ] : array();
		$model_data[ $next_year ][ 'data' ] = is_array( $model_data[ $next_year ][ 'data' ] ) ? $model_data[ $next_year ][ 'data' ] : array();

		$model_data[ 'data' ] = array_merge( $model_data[ $last_year ][ 'data' ] , $model_data[ $current_year ][ 'data' ] , $model_data[ $next_year ][ 'data' ] );

		$models = $model_data[ 'data' ];
		usort( $models , 'sort_models' );

		$classifications = array(
			'T' => 'Trucks',
			'M' => 'Medium Duty Vehicles',
			'C' => 'Cars',
			'S' => 'Sport Utility Vehicles',
			'V' => 'Vans',
			'H' => 'Chassis'
		);

		echo '<h2><a href="/showcase/">Showcase</a> &rsaquo; ' . $make . '</h2>';
		echo '<hr />';
		$previous_classification = $models[0]->classification;
		$previous_name = null;
		echo '<div class="group models">';
		echo '<h3>&raquo; ' . $classifications[ $models[0]->classification ] . '</h3>';
		echo '<div class="items">';
		foreach( $models as $model ) {
			if( $model->name != $previous_name ) {
				$previous_name = $model->name;
				if( $model->classification != $previous_classification) {
					$previous_classification = $model->classification;
					echo '</div>';
					echo '<h3>&raquo; ' . $classifications[ $model->classification ] . '</h3>';
					echo '<div class="items">';
				}
				echo '<div class="model">';
				echo '<a href="/showcase/' . $make . '/' . $model->name . '" title="' . $make . ' ' . $model->name .'">';
				echo '<img src="' . preg_replace( '/IMG=(.*)\.\w{3,4}/i' , 'IMG=\1.png' , $model->image_urls->small ) . '" />';
				echo '<div class="name">' . $model->name . '</div>';
				echo '</a>';
				echo '</div>';
			}
		}
		echo '</div>';
		echo '</div>';

	} elseif( $trim === false ) {

		$trim_data[ $last_year ] = $vehicle_reference_system->get_trims()->please( array( 'make' => $make , 'model_name' => $model , 'year' => $last_year , 'api' => 2 ) );
		$trim_data[ $current_year ] = $vehicle_reference_system->get_trims()->please( array( 'make' => $make , 'model_name' => $model , 'year' => $current_year , 'api' => 2 ) );
		$trim_data[ $next_year ] = $vehicle_reference_system->get_trims()->please( array( 'make' => $make , 'model_name' => $model , 'year' => $next_year , 'api' => 2 ) );

		$trim_data[ $last_year ][ 'data' ] = is_array( $trim_data[ $last_year ][ 'data' ] ) ? $trim_data[ $last_year ][ 'data' ] : array();
		$trim_data[ $current_year ][ 'data' ] = is_array( $trim_data[ $current_year ][ 'data' ] ) ? $trim_data[ $current_year ][ 'data' ] : array();
		$trim_data[ $next_year ][ 'data' ] = is_array( $trim_data[ $next_year ][ 'data' ] ) ? $trim_data[ $next_year ][ 'data' ] : array();

		$trim_data[ 'data' ] = array_merge( $trim_data[ $last_year ][ 'data' ] , $trim_data[ $current_year ][ 'data' ] , $trim_data[ $next_year ][ 'data' ] );

		$trims = $trim_data[ 'data' ];

		usort( $trims , 'sort_trims' );

		echo '<h2><a href="/showcase/">Showcase</a> &rsaquo; <a href="/showcase/' . $make . '">' . $make . '</a> &rsaquo; ' . $model . '</h2>';
		echo '<hr />';

		$trim = $trims[ 0 ];

		$trim->msrp = '$' . number_format( $trim->msrp , 2 , '.' , ',' );
		$fuel_economy = $vehicle_reference_system->get_fuel_economy( $trim->acode )->please();
		$fuel_economy = $fuel_economy[ 'data' ][ 0 ];
		$colors = $vehicle_reference_system->get_colors( $trim->acode )->please();
		$colors = $colors[ 'data' ];

		function make_transparent( $url ) {
			return preg_replace( '/IMG=(.*)\.\w{3,4}/i', 'IMG=\1.png' , $url );
		}

?>

<div id="trim">
  <div id="visuals">
    <div>
      <h3><?php echo "$trim->year $make $model"; ?></h3>
    </div>
    <div id="left-side">
      <div id="spotlight" class="ui-widget-header ui-corner-top">
				<?php
						$active = true;
						foreach( $colors as $color ) {
							if( isset( $color->image_urls ) ) {
								( $active === false ) ? $class = NULL : $class = 'active'; $active = false;
								echo '<img id="' . $color->code . '" src="' . make_transparent( $color->image_urls->medium ) . '" class="' . $class . '" />';
							}
						}
					?>
      </div>
    </div>
    <div id="right-side">
      <div id="pricing"><span>Starting at:</span>
        <div id="msrp"><?php echo $trim->msrp; ?></div>
      </div>
      <div id="fuel">
        <div id="city"><div class="label">CITY:</div><div class="number"><?php echo $fuel_economy->city_mpg; ?></div></div>
        <div id="icon"><img src="http://static.dealer.com/v8/tools/automotive/showroom/v4/images/white/mpg.gif" /></div>
        <div id="hwy"><div class="label">HWY:</div><div class="number"><?php echo $fuel_economy->highway_mpg; ?></div></div>
      </div>
      <div id="disclaimer">Actual rating will vary with options, driving conditions, habits and vehicle condition.</div>
    </div>
    <div>
      <div id="swatches">
				<?php
          $active = true;
					foreach( $colors as $color ) {
							if( ! empty( $color->file ) ) {
								( $active === false ) ? $class = NULL : $class = 'active'; $active = false;
								echo '<a id="swatch-' . $color->code .'" title="' . $color->name . '" href="#' . $color->code . '" class="swatch ' . $class . '" style=background-color:rgb(' . $color->rgb .')">' . $color->name . '</a>';
							}
					}
				?>
      </div>
    </div>
  </div>
  <div id="showcase-tabs">
    <ul>
      <li><a href="#overview">Overview</a></li>
      <li><a href="#trims">Trims</a></li>
    </ul>
    <div id="overview">
     <?php $videos = $vehicle_reference_system->get_videos( $trim->acode )->please(); ?>
     <?php $reviews = $vehicle_reference_system->get_reviews( $trim->acode )->please(); ?>
     <?php $features = $vehicle_reference_system->get_features( $trim->acode )->please(); ?>
     <?php $equipment = $vehicle_reference_system->get_equipment( $trim->acode )->please(); ?>
     <?php $options = $vehicle_reference_system->get_options( $trim->acode )->please(); ?>
     <?php $photos = $vehicle_reference_system->get_photos( $trim->acode )->please( array( 'type' => 'compliant' ) ); ?>
    </div>
    <div id="trims">
    </div>
  </div>
</div>

<?php
	}
	echo '</div>';

	echo '<pre>' . print_r($vehicle_reference_system,true) . '</pre>';

	get_footer();
	flush();

?>
