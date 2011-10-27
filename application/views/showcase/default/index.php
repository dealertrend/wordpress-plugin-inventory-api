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
		$make_data[ $last_year ] = json_decode( $make_data[ $last_year ][ 'body' ] );
		$make_data[ $current_year ] = $vehicle_reference_system->get_makes()->please( array( 'year' => $current_year ) );
		$make_data[ $current_year ] = json_decode( $make_data[ $current_year ][ 'body' ] );
		$make_data[ $next_year ] = $vehicle_reference_system->get_makes()->please( array( 'year' => $next_year ) );
		$make_data[ $next_year ] = json_decode( $make_data[ $next_year ][ 'body' ] );

		$make_data = array_merge( $make_data[ $last_year ] , $make_data[ $current_year ] , $make_data[ $next_year ] );

		$makes = $make_data;

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
		$model_data[ $last_year ] = json_decode( $model_data[ $last_year ][ 'body' ] );
		$model_data[ $current_year ] = $vehicle_reference_system->get_models()->please( array( 'make' => $make , 'year' => $current_year ) );
		$model_data[ $current_year ] = json_decode( $model_data[ $current_year ][ 'body' ] );
		$model_data[ $next_year ] = $vehicle_reference_system->get_models()->please( array( 'make' => $make , 'year' => $next_year ) );
		$model_data[ $next_year ] = json_decode( $model_data[ $next_year ][ 'body' ] );

		$model_data[ $last_year ] = is_array( $model_data[ $last_year ] ) ? $model_data[ $last_year ] : array();
		$model_data[ $current_year ] = is_array( $model_data[ $current_year ] ) ? $model_data[ $current_year ] : array();
		$model_data[ $next_year ] = is_array( $model_data[ $next_year ] ) ? $model_data[ $next_year ] : array();

		$model_data = array_merge( $model_data[ $last_year ] , $model_data[ $current_year ] , $model_data[ $next_year ] );

		$models = $model_data;
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
		$trim_data[ $last_year ] = json_decode( $trim_data[ $last_year ][ 'body' ] );
		$trim_data[ $current_year ] = $vehicle_reference_system->get_trims()->please( array( 'make' => $make , 'model_name' => $model , 'year' => $current_year , 'api' => 2 ) );
		$trim_data[ $current_year ] = json_decode( $trim_data[ $current_year ][ 'body' ] );
		$trim_data[ $next_year ] = $vehicle_reference_system->get_trims()->please( array( 'make' => $make , 'model_name' => $model , 'year' => $next_year , 'api' => 2 ) );
		$trim_data[ $next_year ] = json_decode( $trim_data[ $next_year ][ 'body' ] );

		$trim_data[ $last_year ] = is_array( $trim_data[ $last_year ] ) ? $trim_data[ $last_year ] : array();
		$trim_data[ $current_year ] = is_array( $trim_data[ $current_year ] ) ? $trim_data[ $current_year ] : array();
		$trim_data[ $next_year ] = is_array( $trim_data[ $next_year ] ) ? $trim_data[ $next_year ] : array();

		$trim_data = array_merge( $trim_data[ $last_year ] , $trim_data[ $current_year ] , $trim_data[ $next_year ] );

		$trims = $trim_data;

		usort( $trims , 'sort_trims' );

		echo '<h2><a href="/showcase/">Showcase</a> &rsaquo; <a href="/showcase/' . $make . '">' . $make . '</a> &rsaquo; ' . $model . '</h2>';
		echo '<hr />';

print_r($trims);

		$trim = $trims[ 0 ];

		$trim->msrp = '$' . number_format( $trim->msrp , 2 , '.' , ',' );
		$fuel_economy = $vehicle_reference_system->get_fuel_economy( $trim->acode )->please();

		$fuel_economy = json_decode( $fuel_economy[ 'body' ] );
		$fuel_economy = $fuel_economy[ 0 ];
		$colors = $vehicle_reference_system->get_colors( $trim->acode )->please();
		$colors = json_decode( $colors[ 'body' ] );

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
					$options = array();
					echo '<div id="color-text">Color: ' . $colors[ 0 ]->name . '</div>';
					foreach( $colors as $color ) {
						if( ! empty( $color->file ) ) {
							if( ! in_array( $color->rgb , $colors ) ) {
								$colors[] = $color->rgb;
								( $active === false ) ? $class = NULL : $class = 'active'; $active = false;
								echo '<a id="swatch-' . $color->code .'" title="' . $color->name . '" href="#' . $color->code . '" class="swatch ' . $class . '" style=background-color:rgb(' . $color->rgb .')">' . $color->name . '</a>';
							}
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
    <div id="overview" style="overflow:hidden;">
     <div id="video" style="float:right;border:3px double #333;">
			<?php
				$videos = $vehicle_reference_system->get_videos( $trim->acode )->please();
				if( isset( $videos[ 'response' ][ 'code' ] ) && $videos[ 'response' ][ 'code' ] === 200 ) {
					$videos = json_decode( $videos[ 'body' ] );
					if( $videos != false ) {
						echo '<iframe src="http://player.dealertrend.com/player.html?t=Test%20Player&autoplay=0&v=' . $videos[ 0 ]->flash_video_url . '" height="300" width="400"></iframe>';
					}
				}
			?>
     </div>
			<?php
				$reviews = $vehicle_reference_system->get_reviews( $trim->acode )->please();
				$reviews = json_decode( $reviews[ 'body' ] );
				$options = array();
				foreach( $reviews as $review ) {
					foreach( $review->titles as $title_object ) {
						if( $title_object->title === 'LIKED_MOST' ) {
							$options[] = $title_object->id;
						}
					}
				}
				$index = mt_rand(0, count( $options ) - 1 );
				$review = $vehicle_reference_system->get_review( $options[ $index ] )->please();
				$review = json_decode( $review[ 'body' ] );
				echo '<div id="reviews" style="line-height:24px;">';
				echo '<h4 style="display:inline-block; clear:none; margin-bottom:1.2em;">What people are saying:</h4>';
				echo '<blockquote>';
				foreach( $review->content as $paragraph ) {
					echo '<p>' . $paragraph . '</p>';
				}
				echo '</blockquote>';
				echo '</div>';
			?>
     <?php $features = $vehicle_reference_system->get_features( $trim->acode )->please(); ?>
     <?php $equipment = $vehicle_reference_system->get_equipment( $trim->acode )->please(); ?>
     <?php $options = $vehicle_reference_system->get_options( $trim->acode )->please(); ?>
     <?php $photos = $vehicle_reference_system->get_photos( $trim->acode )->please( array( 'type' => 'compliant' ) ); ?>
    </div>
    <div id="trims">
    </div>
  </div>
  <p>* Although every reasonable effort has been made to insure the accuracy of the information contained on this site, absolute accuracy cannot be guaranteed. This site, and all information and materials appearing on it, are presented to the user "as is" without warranty of any kind, either express or implied, including but not limited to the implied warranties of merchantability, fitness for a particular purpose, title or non-infringement. All vehicles are subject to prior sale. Price does not include applicable tax, title, and license. Not responsible for typographical errors.</p>
</div>

<?php
	}
	echo '</div>';

	echo '<pre>' . print_r($vehicle_reference_system,true) . '</pre>';

	get_footer();
	flush();

?>
