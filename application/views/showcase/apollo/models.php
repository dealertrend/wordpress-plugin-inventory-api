<?php

	$models = get_model_array( $vehicle_reference_system, $years, $make, $year_filter, $this->options[ 'vehicle_reference_system' ][ 'data' ] );

	usort( $models , 'sort_models' );

	$classifications = array(
		'T' => 'Truck', 
		'M' => 'MDV',
		'C' => 'Car',
		'S' => 'SUV',
		'V' => 'Van',
		'H' => 'Chassis'
	);

	$search_array = array();
	foreach( $models as $model ) {
		if( !in_array( $classifications[ $model['class'] ], $search_array  ) ){
			$search_array[] = $classifications[ $model['class'] ];
		}
	}
	echo '<div id="showcase-models">';
	echo '<h2><a href="/showcase/">Showcase</a> &rsaquo; ' . $make . '</h2>';
	echo '<hr />';
	echo '<div id="showcase-search-filter">';
	echo '<h3>Search Filter</h3>';
	echo '<div id="filter-wrapper">';
	foreach( $search_array as $filter ) {
		( $filter == $classification_param ) ? $checked = 'checked="checked"' : $checked = '';
		echo '<div class="filter-item">';
		echo '<input type="checkbox" name="' . $filter . '" '.$checked.' >';
		echo '<label value="' . $filter . '">' . $filter . '</label>';
		echo '</div>';
	}
	echo '<div class="filter-item filter-clear"><label class="clear-button" value="clear"><span class="clear-button-x">X</span><br>Clear</label></div>';
	echo '</div>';
	echo '</div>';
	echo '<hr />';
	$previous_name = null;
	echo '<div id="model-wrapper" class="group models">';
	foreach( $models as $model ) {
			if( $model['name'] != $previous_name && $model['img'] != NULL ) {
				$previous_name = $model['name'];
				$trims = $vehicle_reference_system->get_trims()->please( array( 'make' => $make , 'model_name' => $model['name'], 'year' => $model['year'] ) );
				$trims = isset( $trims[ 'body' ] ) ? json_decode( $trims[ 'body' ] ) : NULL;
				if( !empty($trims) ){
					$starting_value = $trims[0]->msrp;
					$trim_img = $trims[0]->images->small;
					$model_year = $model['year'];
				}
				echo '<div class="active model ' . $classifications[ $model['class'] ] . '">';
				echo '<a href="/showcase/' . $make . '/' . $model['name'] . '/" title="' . $make . ' ' . $model['name'] .'">';
				echo '<div class="name"><span class="year">' . $model_year . '</span><br><span class="model-name">' . $model['name'] . '</span></div>';
				if( !empty( $starting_value ) ) {
					echo '<div class="price"><span class="price-tag">Starting At</span><br><span class="price-value">$' . number_format( $starting_value , 0 , '.' , ',' ) . '</span></div>';
				}
				if( !empty( $trim_img ) ){
					echo '<img src="' . preg_replace( '/IMG=(.*)\.\w{3,4}/i' , 'IMG=\1.png' , $trim_img ) . '" />';
				} else {
					echo '<img src="' . preg_replace( '/IMG=(.*)\.\w{3,4}/i' , 'IMG=\1.png' , $model['img'] ) . '" />';
				}
				echo '</a>';
				echo '</div>';
			}
	}
	echo '</div>';
	echo '</div>';
	echo '</div>';

?>
