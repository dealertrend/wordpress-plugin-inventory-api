<?php

	function sort_models( $a , $b ) {
		if( $a->classification == $b->classification ) {
			if( $a->name > $b->name ) {
				return +1;
			} else {
				return -1;
			}
			return 0;
		}
		return ( $a->classification > $b->classification ) ? +1 : -1;
	}

	$model_data[ $last_year ] = $vehicle_reference_system->get_models()->please( array( 'make' => $make , 'year' => $last_year ) );
	$model_data[ $last_year ] = isset( $model_data[ $last_year ][ 'body' ] ) ? json_decode( $model_data[ $last_year ][ 'body' ] ) : NULL;
	$model_data[ $current_year ] = $vehicle_reference_system->get_models()->please( array( 'make' => $make , 'year' => $current_year ) );
	$model_data[ $current_year ] = isset( $model_data[ $current_year ][ 'body' ] ) ?json_decode( $model_data[ $current_year ][ 'body' ] ) : NULL;
	$model_data[ $next_year ] = $vehicle_reference_system->get_models()->please( array( 'make' => $make , 'year' => $next_year ) );
	$model_data[ $next_year ] = isset( $model_data[ $next_year ][ 'body' ] ) ? json_decode( $model_data[ $next_year ][ 'body' ] ) : NULL;

	$model_data[ $last_year ] = is_array( $model_data[ $last_year ] ) ? $model_data[ $last_year ] : array();
	$model_data[ $current_year ] = is_array( $model_data[ $current_year ] ) ? $model_data[ $current_year ] : array();
	$model_data[ $next_year ] = is_array( $model_data[ $next_year ] ) ? $model_data[ $next_year ] : array(); 

	$model_data = array_merge( $model_data[ $next_year ] , $model_data[ $current_year ] , $model_data[ $last_year ] );

	$models = $model_data;
	usort( $models , 'sort_models' );

	$selected_models = $this->options[ 'vehicle_reference_system' ][ 'data' ][ 'models' ];

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
		if( in_array( str_replace( '&', '&amp;', $model->name ), $selected_models ) ) {
			if( $model->name != $previous_name && $model->image_urls->small != NULL ) {
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
	}
	echo '</div>';
	echo '</div>';

?>
