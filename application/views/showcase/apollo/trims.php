<?php

	$trim_year = date( 'Y' );
	$vms_variables = array( 'make' => $make, 'model' => $model );

	$trims = get_trim_data( $vehicle_reference_system, $years, $make, $model, $year_filter, $this->options[ 'vehicle_reference_system' ][ 'data' ], $trim_year );

	$trims_array = array();
	$variations_array = array();

	build_array_trims( $trims, $trims_array );
	build_variation_array_trims( $trims, $variations_array );

	echo '<div id="showcase-trims">';
	echo '<h2><a class="red-link" href="/showcase/">Showcase</a> &rsaquo; <a class="red-link" href="/showcase/'. $make .'/">' . $make . '</a> &rsaquo; ' . $model . '</h2>';
	echo '<hr />';
	//Trim Left - Trims
	echo '<div id="showcase-trims-left">';
	echo '<div class="trim-headline">Select Trim to View Options</div>';
		foreach( $trims_array as $trim ) {
			echo '<div class="trim-item"><a href="/showcase/' . $make . '/' . $model . '/' . urlencode( $trim['name'] ) . '/">';
			echo '<div class="img-wrapper">';
			echo '<img src="' . preg_replace( '/IMG=(.*)\.\w{3,4}/i' , 'IMG=\1.png' , $trim['image'] ) . '" />';
			echo '</div>';
			echo '<div class="content-wrapper">';
			echo '<div class="line-one"><span class="trim-year">' . $trim_year . '</span> <span class="trim-model">' . $model . '</span></div>';
			echo '<div class="line-two"><span class="trim-name">' . str_ireplace('base', '', $trim['name'] ) . '</span></div>';
			echo !empty( $trim['msrp'] ) ? '<div class="line-three trim-msrp"><span class="msrp-text">Starting At</span><br><span class="msrp-symbol">$</span><span class="msrp-value">' . number_format( $trim['msrp'] , 0 , '.' , ',' ) . '</span></div>' : '';
			echo '<div class="line-four trim-variations">';
			foreach( $variations_array[ $trim['name'] ] as $var ){
				echo '<div class="trim-variation">';
				echo '<span class="var-drive-train">' . $var['drive_train'] . '</span>';
				echo '<span class="var-cab-type">' . $var['cab_type'] . '</span>';
				echo '</div>';
			}
			echo '</div>';
			echo '</div>';
			echo '</a></div>';
		}
	echo '</div>';
	//Trim Right
	echo '<div id="showcase-trims-right">';

		$inventory_count = get_similar_vehicles_count( $vehicle_management_system, $vms_variables );
		$count_message = 'Currently found ' . $inventory_count . ' In-stock'; //Default
		$form_message = 'Inquire More Info'; //Default
		get_custom_message( $count_message, $form_message, $custom_message, $inventory_count, $make, $model );

		echo '<div class="showcase-count-message">';
		echo $count_message;
		echo '</div>';

		if( !empty( $inventory_count ) ){
			echo '<div class="showcase-similar-link">';
			echo '<a href="/inventory/New/' . $make . '/' . $model .'/" title="Listings for ' . $make . ' ' . $model. '" >View All In-Stock</a>';
			echo '</div>';
		}

		if( function_exists(gravity_form) ){
			echo '<div id="showcase-form-wrapper">';
			echo '<div class="trim-headline">' . $form_message . '</div>';
			echo '<div id="showcase-form">';
				gravity_form($form_id, false);
			echo '</div>';
			echo '</div>';
		}

	echo '</div>';
	//Trim Bottom
	echo '<div id="showcase-trims-bottom">';
	if( !empty( $display_vms ) ) {
			$inventory = get_similar_vehicles_showcase( $vehicle_management_system, $vms_variables );
			if( !empty( $inventory ) ){
				$added_param = '';
				echo '<div class="trim-headline">' . $model . '&#39;s In-Stock</div>';
				include( dirname( __FILE__ ) . '/vms_inventory.php' );
			}
	}
	echo '</div>';

	echo '</div>';

?>
