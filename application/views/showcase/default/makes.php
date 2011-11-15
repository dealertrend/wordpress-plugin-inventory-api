<?php
	$make_data[ $last_year ] = $vehicle_reference_system->get_makes()->please( array( 'year' => $last_year ) );
	$make_data[ $last_year ] = isset( $make_data[ $last_year ][ 'body' ] ) ? json_decode( $make_data[ $last_year ][ 'body' ] ) : NULL;
	$make_data[ $current_year ] = $vehicle_reference_system->get_makes()->please( array( 'year' => $current_year ) );
	$make_data[ $current_year ] = isset( $make_data[ $current_year ][ 'body' ] ) ? json_decode( $make_data[ $current_year ][ 'body' ] ) : NULL;
	$make_data[ $next_year ] = $vehicle_reference_system->get_makes()->please( array( 'year' => $next_year ) );
	$make_data[ $next_year ] = isset( $make_data[ $next_year ][ 'body' ] ) ? json_decode( $make_data[ $next_year ][ 'body' ] ) : NULL;

	$makes = array_merge( $make_data[ $next_year ] , $make_data[ $current_year ] , $make_data[ $last_year ] );

	$selected_makes = $this->options[ 'vehicle_reference_system' ][ 'data' ][ 'makes' ];

	$bucket = array();

	echo '<h2><a href="/showcase/">Showcase</a> &rsaquo; All Makes</h2>';
	echo '<hr />';
	echo '<div class="group makes">';
	foreach( $makes as $make ) {
		if( in_array( $make->name , $selected_makes ) ) {
			if( ! in_array( $make->id , $bucket ) ) {
				$bucket[] = $make->id;
				if( ! empty( $make->image_url ) ) { 
					echo '<div class="make">';
					echo '<a href="/showcase/' . $make->name . '/" title="' . $make->name . '"><img src="' . $make->image_url . '" /></a>';
					echo '<div class="classifications">';
					echo '</div>';
					echo '</div>';
				}
			}
		}
	}
	echo '</div>';
?>
