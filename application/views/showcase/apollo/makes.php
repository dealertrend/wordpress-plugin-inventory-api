<?php

	$makes = get_make_array( $vehicle_reference_system, $years );

	$selected_makes = $this->options[ 'vehicle_reference_system' ][ 'data' ][ 'makes' ];

	$bucket = array();

	echo '<h2><a href="/showcase/">Showcase</a> &rsaquo; All Makes</h2>';
	echo '<hr />';
	echo '<div id="make-wrapper" class="group makes">';
	foreach( $makes as $make ) {
		if( in_array( $make->name , $selected_makes ) ) {
			if( ! in_array( $make->id , $bucket ) ) {
				$bucket[] = $make->id;
				$display[] = $make;
			}
		}
	}
	usort( $display , 'sort_makes' );
	foreach( $display as $make ) {
		if( ! empty( $make->image_url ) ) {
			echo '<div class="make">';
			echo '<a href="/showcase/' . $make->name . '/" title="' . $make->name . '"><img src="' . $make->image_url . '" /></a>';
			echo '<div class="classifications">';
			echo '</div>';
			echo '</div>';
		}
	}
	echo '</div>';
?>
