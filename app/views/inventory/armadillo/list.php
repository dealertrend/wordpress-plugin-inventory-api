<?php

	global $wp_rewrite;

	$parameters = $this->parameters;

	$on_page = isset( $inventory[ 0 ]->pagination->on_page ) ? $inventory[ 0 ]->pagination->on_page : 0;
	$total = isset( $inventory[ 0 ]->pagination->total ) ? $inventory[ 0 ]->pagination->total : 0;

	$args = array(
		'base' => @add_query_arg( 'page' , '%#%' ),
		'current' => $on_page,
		'total' => $total,
		'next_text' => __( 'Next &raquo;' ),
		'prev_text' => __( '&laquo; Previous' ),
		'show_all' => false,
		'type' => 'plain'
	); 

	$vehicle_class = isset( $parameters[ 'vehiclesclass' ] ) ? ucwords( $parameters[ 'vehicleclass' ] ) : 'All';

	if( empty( $inventory ) ) {
		$total_found = 0;
	} elseif ( $inventory[0]->pagination->total == 1 ) {
		$total_found = $inventory[0]->pagination->total * count( $inventory );
	} else {
		$total_found = $inventory[0]->pagination->total * $inventory[0]->pagination->per_page;
	}

?>

<div class="dealertrend inventory wrapper">
	<br class="clear" id="top" />
	<div class="listing wrapper">
		<?php echo $breadcrumbs; ?>
		<div class="pager">
			<?php echo paginate_links( $args ); ?>
		</div>
		<div class="sidebar">
			<div class="total-found"><?php echo $total_found; ?> Cars Found</div>
			<div class="quick-links">
				<?php if( !isset( $parameters[ 'trim' ] ) || strtolower( $parameters[ 'trim' ] ) == 'all' ): ?>
				<h3>Refine Your Search</h3>
				<ul>
					<?php	if( !isset( $parameters[ 'model' ] ) || strtolower( $parameters[ 'model' ] ) == 'all' ): ?>
					<li class="expanded">
						<span>Body Style</span>
						<ul>
							<li><a href="<?php echo @add_query_arg( array( 'vehicleclass' => 'car' , 'page' => 1 ) ); ?>">Car</a></li>
							<li><a href="<?php echo @add_query_arg( array( 'vehicleclass' => 'truck' , 'page' => 1 ) ); ?>">Truck</a></li>
							<li><a href="<?php echo @add_query_arg( array( 'vehicleclass' => 'sport_utility' , 'page' => 1 ) ); ?>">SUV</a></li>
							<li><a href="<?php echo @add_query_arg( array( 'vehicleclass' => 'van' , 'page' => 1 ) ); ?>">Van</a></li>
						</ul>
					</li>
					<?php
						endif;
						if( !isset( $parameters[ 'make' ] ) || strtolower( $parameters[ 'make' ] ) == 'all' ):
					?>
					<li class="expanded">
						<span>Make</span>
						<ul>
							<?php
								foreach( $vehicle_management_system->get_makes( array( 'saleclass' => $sale_class , 'vehicleclass' => $vehicle_class ) ) as $make ) {
									if( !empty( $wp_rewrite->rules ) ) {
										$url = '/inventory/' . $sale_class . '/' . $make . '/';
										$url .= isset( $this->parameters[ 'vehicleclass' ] ) ? '?' . http_build_query( array( 'vehicleclass' => $this->parameters[ 'vehicleclass' ] ) ) : NULL;
										echo '<li><a href="' . $url . '">' . $make . '</a></li>';
									} else {
										echo '<li><a href="' . @add_query_arg( array( 'make' => $make , 'page' => 1 ) ) . '">' . $make . '</a></li>';
									}
								}
							?>
						</ul>
					</li>
					<?php elseif( !isset( $parameters[ 'model' ] ) || strtolower( $parameters[ 'model' ] ) == 'all' ): ?>
					<li class="expanded">
						<span>Model</span>
						<ul>
							<?php
								foreach( $vehicle_management_system->get_models( array( 'saleclass' => $sale_class , 'make' => $parameters[ 'make' ] ) ) as $model ) {
									if( !empty( $wp_rewrite->rules ) ) {
										$url = '/inventory/' . $sale_class . '/' . $parameters[ 'make' ] . '/' . $model . '/';
										echo '<li><a href="' . $url . '">' . $model . '</a></li>';
									} else {
										echo '<li><a href="' . @add_query_arg( array( 'model' => $model , 'page' => 1 ) ) . '">' . $model . '</a></li>';
									}
								}
							?>
						</ul>
					</li>
					<?php elseif( !isset( $parameters[ 'trim' ] ) || strtolower( $parameters[ 'trim' ] ) == 'all' ): ?>
					<li class="expanded">
						<span>Trim</span>
						<ul>
							<?php
								foreach( $vehicle_management_system->get_trims( array( 'saleclass' => $sale_class , 'make' => $parameters[ 'make' ] , 'model' => $parameters[ 'model' ] ) ) as $trim ) {
									echo '<li><a href="' . @add_query_arg( array( 'trim' => $trim , 'page' => 1 ) ) . '">' . $trim . '</a></li>';
								}
							?>
						</ul>
					</li>
					<?php endif; ?>
				</ul>
				<?php endif; ?>
			</div>
		</div>
		<div class="content">
			<div class="sort">
				<div class="column">Sort by</div>
				<?php
					$sort = isset( $_GET[ 'sort' ] ) ? $_GET[ 'sort' ] : NULL;
					switch( $sort ) {
						case 'year_asc': $sort_year_class = 'asc'; break;
						case 'year_desc': $sort_year_class = 'desc'; break;
						case 'price_asc': $sort_price_class = 'asc'; break;
						case 'price_desc': $sort_price_class = 'desc'; break;
						case 'mileage_asc': $sort_mileage_class = 'asc'; break;
						case 'mileage_desc': $sort_mileage_class = 'desc'; break;
						default: $sort_year_class = $sort_price_class = $sort_mileage_class = null; break;
					}
					$sort_year = $sort != 'year_asc' ? 'year_asc' : 'year_desc';
					$sort_mileage = $sort != 'mileage_asc' ? 'mileage_asc' : 'mileage_desc';
					$sort_price = $sort != 'price_asc' ? 'price_asc' : 'price_desc';
				?>
				<div><a class="<?php echo $sort_year_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_year , 'page' => 1 ) ); ?>">Year</a></div>
				<div><a class="<?php echo $sort_price_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_price , 'page' => 1 ) ); ?>">Price</a></div>
				<div class="last"><a class="<?php echo $sort_mileage_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_mileage , 'page' => 1 ) ); ?>">Mileage</a></div>
			</div>
			<div class="items">
				<?php
					if( empty( $inventory ) ) {
						echo '<div class="not-found"><h2><strong>Unable to find inventory items that matched your search criteria.</strong></h2></div>';
					} else {
						foreach( $inventory as $inventory_item ):
							setlocale(LC_MONETARY, 'en_US');
							$prices = $inventory_item->prices;
							$use_was_now = $prices->{ 'use_was_now?' };
							$use_price_strike_through = $prices->{ 'use_price_strike_through?' };
							$on_sale = $prices->{ 'on_sale?' };
							$sale_price = isset( $prices->sale_price ) ? $prices->sale_price : NULL;
							$retail_price = $prices->retail_price;
							$default_price_text = $prices->default_price_text;
							$asking_price = $prices->asking_price;
							$year = $inventory_item->year;
							$make = $inventory_item->make;
							$model = urldecode( $inventory_item->model_name );
							$vin = $inventory_item->vin;
							$trim = urldecode( $inventory_item->trim );
							$engine = $inventory_item->engine;
							$transmission = $inventory_item->transmission;
							$exterior_color = $inventory_item->exterior_color;
							$interior_color = $inventory_item->interior_color;
							$stock_number = $inventory_item->stock_number;
							$odometer = $inventory_item->odometer;
							$icons = $inventory_item->icons;
							$thumbnail = urldecode( $inventory_item->photos[ 0 ]->small );
							$body_style = $inventory_item->body_style;
							$drive_train = $inventory_item->drive_train;
							$doors = $inventory_item->doors;
							$headline = $inventory_item->headline;
							if( !empty( $wp_rewrite->rules ) ) {
								$inventory_url = '/inventory/' . $year . '/' . $make . '/' . $model . '/' . $state . '/' . $city . '/'. $vin . '/';
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
								<div class="main-line">
									<a href="<?php echo $inventory_url; ?>" title="<?php echo $generic_vehicle_title; ?>" class="details">
										<span class="year"><?php echo $year; ?></span>
										<span class="make"><?php echo $make; ?></span>
										<span class="model"><?php echo $model; ?></span>
										<span class="trim"><?php echo $trim; ?></span>
										<span class="drive-train"><?php echo $drive_train; ?></span>
										<span class="body-style"><?php echo $body_style; ?></span>
									</a>
								</div>
								<div class="headline">
									<?php echo $headline; ?>
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
									<?php
										if( $on_sale ) {
											$now_text = 'Price: ';
											if( $use_was_now ) {
												$price_class = ( $use_price_strike_through ) ? 'strike-through asking-price' : 'asking-price';
												echo '<div class="' . $price_class . '">Was: ' . money_format( '%(#0n' , $asking_price ) . '</div>';
												$now_text = 'Now: ';
											}
											echo '<div class="sale-price">' . $now_text . money_format( '%(#0n' , $sale_price ) . '</div>';
										} else {
											if( $asking_price > 0 ) {
												echo '<div class="asking-price">Price: ' . money_format( '%(#0n' , $asking_price ) . '</div>';
											} else {
												echo $default_price_text;
											}
										}
									?>
									<a href="<?php echo $inventory_url; ?>" title="More Information: <?php echo $generic_vehicle_title; ?>">More Information</a>
								</div>
								<br class="clear" />
							</div>
						<?php
						flush();
						endforeach;
					}
				?>
			</div>
		</div>
		<div class="disclaimer">
			<?php echo !empty( $inventory ) ? '<p>' . $inventory[ 0 ]->disclaimer . '</p>' : NULL; ?>
		</div>
	</div>
	<?php echo $breadcrumbs; ?>
	<div class="pager">
		<?php echo paginate_links( $args ); ?>
	</div>
	<a href="#top" title="Return to Top" class="return-to-top">Return to Top</a>
</div>
