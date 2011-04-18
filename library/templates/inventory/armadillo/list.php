<?php

# TODO: Search (do this last).
# TODO: Car Count
# TODO: Sorting Columns
# TODO: Item list
# TODO: Left Quick Links

$pager = $this->pagination( $inventory );
$args = array(
	'base' => @add_query_arg('page','%#%'),
	'current' => $pager[ 'current_page' ],
	'total' => $pager[ 'total_pages' ],
	'next_text' => __( 'Next &raquo;' ),
	'prev_text' => __( '&laquo; Previous' ),
	'show_all' => false,
	'type' => 'plain'
);
echo '<div class="pager">' . paginate_links( $args ) . '</div>';

$parameters = $this->parameters;
$sale_class = isset( $parameters[ 'saleclass' ] ) ? ucwords( $parameters[ 'saleclass' ] ) : 'All';

?>

<div id="listing">
	<div id="sidebar">
		<div id="count"><?php echo !empty( $inventory ) ? $inventory[0]->pagination->total * $inventory[0]->pagination->per_page : 0; ?> Cars Found</div>
		<div id="search-list">
			<h3>Refine Your Search</h3>
			<ul>
				<li class="collapsed">
					<span>Make</span>
					<ul>
						<?php
							foreach( $this->get_makes() as $make ) {
								if( !empty( $wp_rewrite->rules ) ) {
									echo '<li><a href="/inventory/' . $sale_class . '/' . $make . '/">' . $make . '</a></li>';
								} else {
									echo '<li><a href="' . @add_query_arg( array( 'make' => $make ) ) . '">' . $make . '</a></li>';
								}
							}
						?>
					</ul>
				</li>
				<li class="collapsed">
					<span>Body Style</span>
					<ul>
						<li><a href="<?php echo @add_query_arg( array( 'vehicleclass' => 'car' ) ); ?>">Car</a></li>
						<li><a href="<?php echo @add_query_arg( array( 'vehicleclass' => 'truck' ) ); ?>">Truck</a></li>
						<li><a href="<?php echo @add_query_arg( array( 'vehicleclass' => 'suv' ) ); ?>">SUV</a></li>
						<li><a href="<?php echo @add_query_arg( array( 'vehicleclass' => 'van' ) ); ?>">Van</a></li>
					</ul>
				</li>
				<li class="collapsed">
					<span>Search By</span>
					<ul>
						<?php #TODO: Figure out what these are supposed to link to... ?>
						<li><a href="">Year</a></li>
						<li><a href="">Make</a></li>
						<li><a href="">Model</a></li>
						<li><a href="">Price</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
	<div id="items">
		<div id="sort-by">
			<div>Sort by</div>
			<?php
				$sort = isset( $_GET[ 'sort' ] ) ? $_GET[ 'sort' ] : NULL;
				$sort_year = $sort != 'year_asc' ? 'year_asc' : 'year_desc';
				$sort_make = $sort != 'make_asc' ? 'make_asc' : 'make_desc';
				$sort_model = $sort != 'model_asc' ? 'model_asc' : 'model_desc';
				$sort_price = $sort != 'price_asc' ? 'price_asc' : 'price_desc';
			?>
			<div><a href="<?php echo @add_query_arg( array( 'sort' => $sort_year ) ); ?>">Year</a></div>
			<div><a href="<?php echo @add_query_arg( array( 'sort' => $sort_make ) ); ?>">Make</a></div>
			<div><a href="<?php echo @add_query_arg( array( 'sort' => $sort_model ) ); ?>">Model</a></div>
			<div class="last"><a href="<?php echo @add_query_arg( array( 'sort' => $sort_price ) ); ?>">Price</a></div>
		</div>
		<?php
			if( empty( $inventory ) ) {
				echo '<h2><strong>Unable to find inventory items that matched your search criteria.</strong></h2>';
			} else {
				foreach( $inventory as $inventory_item ):
					$year = $inventory_item->year;
					$make = $inventory_item->make;
					$model = urldecode( $inventory_item->model_name );
					$vin = $inventory_item->vin;
					$trim = urldecode( $inventory_item->trim );
					$body_style = urldecode( $inventory_item->body_style );
					$engine = $inventory_item->engine;
					$transmission = $inventory_item->transmission;
					$exterior_color = $inventory_item->exterior_color;
					$interior_color = $inventory_item->interior_color;
					setlocale(LC_MONETARY, 'en_US');
					$prices = $inventory_item->prices;
					$asking_price = money_format( '%(#0n', $prices->asking_price );
					$stock_number = $inventory_item->stock_number;
					$odometer = $inventory_item->odometer;
					$icons = $inventory_item->icons;
					$thumbnail = urldecode( $inventory_item->photos[ 0 ]->small );
					if( !empty( $wp_rewrite->rules ) ) {
						$inventory_url = '/inventory/' . $sale_class . '/' . $make . '/' . $model . '/' . $state . '/' . $city . '/'. $vin . '/';
					} else {
						$inventory_url = '?taxonomy=inventory&amp;saleclass=' . $sale_class . '&amp;make=' . $make . '&amp;model=' . $model . '&amp;state=' . $state . '&amp;city=' . $city . '&amp;vin='. $vin;
					}
					$generic_vehicle_title = $year . ' ' . $make . ' ' . $model;
					?>
					<div class="item" id="<?php echo $vin; ?>">
						<div class="photo">
							<a href="<?php echo $inventory_url; ?>" title="<?php echo $generic_vehicle_title; ?>">
								<img src="<?php echo $thumbnail; ?>" alt="<?php echo $generic_vehicle_title; ?>" title="<?php echo $generic_vehicle_title; ?>" />
							</a>
						</div>
						<div class="main-line">
							<a href="<?php echo $inventory_url; ?>" title="<?php echo $generic_vehicle_title; ?>" class="details">
								<span class="year"><?php echo $year; ?></span>
								<span class="make"><?php echo $make; ?></span>
								<span class="model"><?php echo $model; ?></span>
								<span class="trim"><?php echo $trim; ?></span>
								<span class="body-style"><?php echo $body_style; ?></span>
							</a>
						</div>
						<div class="details-left">
							<span class="interior-color">Int. Color: <?php echo $interior_color; ?></span>
							<span class="exterior-color">Ext. Color: <?php echo $exterior_color; ?></span>
							<span class="transmission">Trans: <?php echo $transmission; ?></span>
						</div>
						<div class="details-right">
							<span class="stock-number">Stock #: <?php echo $stock_number; ?></span>
							<span class="odometer">Mileage: <?php echo $odometer; ?></span>
							<span class="vin">VIN: <?php echo $vin; ?></span>
						</div>
						<div class="icons">
							<?php echo $icons; ?>
						</div>
						<div class="price">
							Price: <?php echo $asking_price; ?>
							<a href="<?php echo $inventory_url; ?>" title="More Information: <?php echo $generic_vehicle_title; ?>">More Information</a>
						</div>
						<br class="clear" />
					</div>
				<?php flush(); ?>
			<?php endforeach; }	?>
	</div><!-- .items -->
</div>
