<?php

print_me( __FILE__ );
	global $wp_rewrite;

	$args = array(
		'base' => @add_query_arg('page','%#%'),
		'current' => $inventory[ 0 ]->pagination->on_page,
		'total' => $inventory[ 0 ]->pagination->total,
		'next_text' => __( 'Next &raquo;' ),
		'prev_text' => __( '&laquo; Previous' ),
		'show_all' => false,
		'type' => 'plain'
	);

	$sale_class = isset( $parameters[ 'saleclass' ] ) ? ucwords( $parameters[ 'saleclass' ] ) : 'All';
	$quick_links = $quick_links_end = null;

	if( !isset( $parameters[ 'make' ] ) || $parameters[ 'make' ] == 'All' ) {
		$makes = $vehicle_management_system->get_makes()->please( array( 'saleclass' => $sale_class ) );
		$makes = json_decode( $makes[ 'body' ] );
		foreach( $makes as $make ) {
			if( !empty( $wp_rewrite->rules ) ) {
				$quick_links .= '<a href="' . $site_url . '/inventory/'. $sale_class  . '/' . $make . '/">' . $make . '</a>';
			} else {
				$quick_links .= '<a href="?taxonomy=inventory&amp;saleclass='. $sale_class  . '&amp;make=' . $make . '">' . $make . '</a>';
			}
		}
	} elseif( !isset( $parameters[ 'model' ] ) || $parameters[ 'model' ] == 'All' ) {
		$models = $vehicle_management_system->get_models()->please( array( 'saleclass' => $sale_class , 'make' => $parameters[ 'make'] ) );
		$models = json_decode( $models[ 'body' ] );
		$quick_links_end .= !empty( $wp_rewrite->rules ) ? '<a href="' . $site_url . '/inventory/' . $sale_class . '/All/">View All Makes</a>' : '<a href="' . @add_query_arg( array( 'make' => 'All' , 'model' => 'All' , 'trim' => 'All' ) ) . '">View All Makes</a>';
		foreach( $models as $model ) {
			if( !empty( $wp_rewrite->rules ) ) {
				$quick_links .= '<a href="' . $site_url . '/inventory/'. $sale_class  . '/' . $parameters[ 'make'] . '/' . $model . '/">' . $model . '</a>';
			} else {
				$quick_links .= '<a href="?taxonomy=inventory&amp;saleclass='. $sale_class  . '&amp;make=' . $parameters[ 'make'] . '&amp;model=' . $model . '">' . $model . '</a>';
			}
		}
	} elseif( !isset( $parameters[ 'trim' ] ) || $parameters[ 'trim' ] == 'All' ) {
		$trims = $vehicle_management_system->get_trims()->please( array( 'saleclass' => $sale_class , 'make' => $parameters[ 'make'] , 'model' => $parameters[ 'model'] ) );
		$trims = json_decode( $trims[ 'body' ] );
		$quick_links_end .= !empty( $wp_rewrite->rules ) ? '<a href="' . $site_url . '/inventory/' . $sale_class . '/All/">View All Makes</a>' : '<a href="' . @add_query_arg( array( 'make' => 'All' , 'model' => 'All' , 'trim' => 'All' ) ) . '">View All Makes</a>';
		$quick_links_end .= !empty( $wp_rewrite->rules ) ? '<a href="' . $site_url . '/inventory/' . $sale_class . '/' . $parameters[ 'make'] . '/All/">View All Models</a>' : '<a href="' . @add_query_arg( array( 'model' => 'All' , 'trim' => 'All' ) ) . '">View All Models</a>';
		foreach( $trims as $trim ) {
			$quick_links .= '<a href="' . @add_query_arg( array( 'trim' => $trim ) ) . '">' . $trim . '</a>';
		}
	} else {
		$quick_links_end .= !empty( $wp_rewrite->rules ) ? '<a href="' . $site_url . '/inventory/' . $sale_class . '/All/">View All Makes</a>' : '<a href="' . @add_query_arg( array( 'make' => 'All' , 'model' => 'All' , 'trim' => 'All' ) ) . '">View All Makes</a>';
		$quick_links_end .= !empty( $wp_rewrite->rules ) ? '<a href="' . $site_url . '/inventory/' . $sale_class . '/' . $parameters[ 'make'] . '/All/">View All Models</a>' : '<a href="' . @add_query_arg( array( 'model' => 'All' , 'trim' => 'All' ) ) . '">View All Models</a>';
		$quick_links_end .= '<a href="' . @add_query_arg( array( 'trim' => 'All' ) ) . '">View All Trims</a>';
	}

?>

<div class="dealertrend inventory">
	<br class="clear" id="top" />
	<div class="quick-links">
		<?php
			echo !empty( $quick_links ) ? $quick_links . '<br />' : NULL;
			echo !empty( $quick_links_end ) ? $quick_links_end : NULL;
		?>
	</div>
	<div class="listing wrapper">
		<?php echo $breadcrumbs; ?>
		<div class="pager">
			<?php echo paginate_links( $args ); ?>
		</div>
		<?php
			if( empty( $inventory ) ) {
				echo '<h2><strong>Unable to find inventory items that matched your search criteria.</strong></h2>';
			} else {
				foreach( $inventory as $inventory_item ):
					$year = $inventory_item->year;
					$make = urldecode( $inventory_item->make );
					$model = urldecode( $inventory_item->model_name );
					$vin = $inventory_item->vin;
					$trim = urldecode( $inventory_item->trim );
					$body_style = urldecode( $inventory_item->body_style );
					$engine = $inventory_item->engine;
					$transmission = $inventory_item->transmission;
					$exterior_color = $inventory_item->exterior_color;
					$prices = $inventory_item->prices;
					$asking_price = number_format( $prices->asking_price , 2 , '.' , ',' );
					$stock_number = $inventory_item->stock_number;
					$odometer = $inventory_item->odometer;
					$icons = $inventory_item->icons;
					$headline = $inventory_item->headline;
					$thumbnail = urldecode( $inventory_item->photos[ 0 ]->small );
					$doors = $inventory_item->doors . 'D';
					if( !empty( $wp_rewrite->rules ) ) {
						$inventory_url = $site_url . '/inventory/' . $sale_class . '/' . $make . '/' . $model . '/' . $state . '/' . $city . '/'. $vin . '/';
					} else {
						$inventory_url = '?taxonomy=inventory&amp;saleclass=' . $sale_class . '&amp;make=' . $make . '&amp;model=' . $model . '&amp;state=' . $state . '&amp;city=' . $city . '&amp;vin='. $vin;
					}
					$generic_vehicle_title = $year . ' ' . $make . ' ' . $model; ?>
					<div class="item" id="<?php echo $vin; ?>">
						<div class="photo">
							<a href="<?php echo $inventory_url; ?>" title="<?php echo $generic_vehicle_title; ?>">
								<img src="<?php echo $thumbnail; ?>" alt="<?php echo $generic_vehicle_title; ?>" title="<?php echo $generic_vehicle_title; ?>" />
							</a>
						</div>
						<div class="headline"><a href="<?php echo $inventory_url; ?>" title="<?php echo $headline; ?>"><?php echo $headline; ?>&nbsp;</a></div>
						<div class="left-column">
							<a href="<?php echo $inventory_url; ?>" title="<?php echo $generic_vehicle_title; ?>" class="details">
								<span class="year"><?php echo $year; ?></span>
								<span class="make"><?php echo $make; ?></span>
								<span class="model"><?php echo $model; ?></span>
								<span class="trim"><?php echo $trim; ?></span>
								<span class="doors"><?php echo $doors; ?></span>
								<span class="body-style"><?php echo $body_style; ?></span>
								<span class="engine"><?php echo $engine; ?></span>
								<span class="transmission"><?php echo $transmission; ?></span>
								<span class="exterior-color">Color: <?php echo $exterior_color; ?></span>
								<span class="pricing">$<?php echo $asking_price; ?></span>
							</a>
						</div>
						<div class="middle-column">
							<span class="vin">VIN: <?php echo $vin; ?></span>
							<span class="stock-number">Stock Number: <?php echo $stock_number; ?></span>
							<span class="odometer">Odometer: <?php echo $odometer; ?></span>
						</div>
						<div class="right-column">
							<span class="icons"><?php echo $icons; ?></span>
						</div>
						<div class="call-to-action">
							<a href="<?php echo $inventory_url; ?>" title="More Information: <?php echo $generic_vehicle_title; ?>">Click Here for More Details</a>
						</div>
						<br class="clear" />
					</div>
					<?php
						flush();
				 endforeach;
				} ?>
		</div>
		<a href="#top" title="Return to Top" class="return-to-top">Return to Top</a>
	</div>
</div>
