<?php

	global $wp_rewrite;

	$parameters = $this->parameters;

	$on_page = isset( $inventory[ 0 ]->pagination->on_page ) ? $inventory[ 0 ]->pagination->on_page : 0;
	$total = isset( $inventory[ 0 ]->pagination->total ) ? $inventory[ 0 ]->pagination->total : 0;

	$args = array(
		'base' => add_query_arg( 'page' , '%#%' ),
		'current' => $on_page,
		'total' => $total,
		'next_text' => __( 'Next &raquo;' ),
		'prev_text' => __( '< Previous' ),
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

	$query = '?' . http_build_query( $_GET );

?>

<div id="armadillo-wrapper">
	<br class="armadillo-clear" id="armadillo-top" />
	<div id="armadillo-listing">
		<?php echo $breadcrumbs; ?>
		<div class="armadillo-pager">
			<?php echo paginate_links( $args ); ?>
		</div>
		<form action="/inventory/" method="GET" id="armadillo-search">
			<label for="search">Inventory Search:</label>
			<input id="armadillo-search-box" name="search" value="<?php echo isset( $parameters[ 'search' ] ) ? $parameters[ 'search' ] : NULL; ?>" />
		</form>
		<div id="armadillo-listing-sidebar">
			<div id="armadillo-total-found"><?php echo $total_found; ?> Cars Found</div>
			<div id="armadillo-quick-links">
				<?php
					$vehicleclass = isset( $this->parameters[ 'vehicleclass' ] ) ? $this->parameters[ 'vehicleclass' ] : NULL;
					$price_to = isset( $this->parameters[ 'price_to' ] ) ? $this->parameters[ 'price_to' ] : NULL;
					$price_from = isset( $this->parameters[ 'price_from' ] ) ? $this->parameters[ 'price_from' ] : NULL;
					$certified = isset( $this->parameters[ 'certified' ] ) ? $this->parameters[ 'certified' ] : NULL;
					$filters = array(
						'vehicleclass' => $vehicleclass,
						'price_to' => $price_to,
						'price_from' => $price_from,
						'certified' => $certified
					);
					if( !isset( $parameters[ 'trim' ] ) || strtolower( $parameters[ 'trim' ] ) == 'all' ):
				?>
				<h3>Refine Your Search</h3>
				<ul>
					<?php
						if( !isset( $parameters[ 'model' ] ) || strtolower( $parameters[ 'model' ] ) == 'all' ):
							$do_not_carry = remove_query_arg( 'page' , $query );
					?>
					<li class="armadillo-expanded">
						<span>Body Style</span>
						<ul>
							<li><a href="<?php echo add_query_arg( array( 'vehicleclass' => 'car' ) , $do_not_carry ); ?>">Car</a></li>
							<li><a href="<?php echo add_query_arg( array( 'vehicleclass' => 'truck' ) , $do_not_carry ); ?>">Truck</a></li>
							<li><a href="<?php echo add_query_arg( array( 'vehicleclass' => 'sport_utility' ) , $do_not_carry ); ?>">SUV</a></li>
							<li><a href="<?php echo add_query_arg( array( 'vehicleclass' => 'van' ) , $do_not_carry ); ?>">Van</a></li>
						</ul>
					</li>
					<?php
						endif;
						if( !isset( $parameters[ 'make' ] ) || strtolower( $parameters[ 'make' ] ) == 'all' ):
					?>
					<li class="armadillo-expanded">
						<span>Make</span>
						<ul>
							<?php
								if( isset( $parameters[ 'saleclass' ] ) ) {
									echo '<li class="small"><a href="/inventory/">View All Vehicles</a></li>';
								}
								foreach( $vehicle_management_system->get_makes( array_merge( array( 'saleclass' => $sale_class ) , $filters ) ) as $make ) {
									if( !empty( $wp_rewrite->rules ) ) {
										$url = '/inventory/' . $sale_class . '/' . $make . '/';
										$url .= isset( $this->parameters[ 'vehicleclass' ] ) ? '?' . http_build_query( array( 'vehicleclass' => $this->parameters[ 'vehicleclass' ] ) ) : NULL;
										echo '<li><a href="' . $url . '">' . $make . '</a></li>';
									} else {
										echo '<li><a href="' . @add_query_arg( array( 'make' => $make ) , $do_not_carry ) . '">' . $make . '</a></li>';
									}
								}
							?>
						</ul>
					</li>
					<?php elseif( !isset( $parameters[ 'model' ] ) || strtolower( $parameters[ 'model' ] ) == 'all' ): ?>
					<li class="armadillo-expanded">
						<span>Model</span>
						<ul>
							<?php
								if( !empty( $wp_rewrite->rules ) ) {
									echo '<li class="armadillo-small"><a href="/inventory/' . $sale_class . '/">View ' . $sale_class . ' Vehicles</a></li>';
								} else {
									echo '<li class="armadillo-small"><a href="' . @add_query_arg( array( 'saleclass' => $sale_class ) , $do_not_carry ) . '">View ' . $sale_class. ' Vehicles</a></li>';
								}
								foreach( $vehicle_management_system->get_models( array_merge( array( 'saleclass' => $sale_class , 'make' => $parameters[ 'make' ] ) , $filters ) ) as $model ) {
									if( !empty( $wp_rewrite->rules ) ) {
										$url = '/inventory/' . $sale_class . '/' . $parameters[ 'make' ] . '/' . $model . '/';
										echo '<li><a href="' . $url . '">' . $model . '</a></li>';
									} else {
										echo '<li><a href="' . @add_query_arg( array( 'model' => $model ) , $do_not_carry ) . '">' . $model . '</a></li>';
									}
								}
							?>
						</ul>
					</li>
					<?php elseif( !isset( $parameters[ 'trim' ] ) || strtolower( $parameters[ 'trim' ] ) == 'all' ): ?>
					<li class="armadillo-expanded">
						<span>Trim</span>
						<ul>
							<?php
								if( !empty( $wp_rewrite->rules ) ) {
									echo '<li class="armadillo-small"><a href="/inventory/' . $sale_class . '/' . $parameters[ 'make' ] . '/">View All ' . $parameters[ 'make' ] . ' Models</a></li>';
									echo '<li class="armadillo-small"><a href="/inventory/' . $sale_class . '/">View ' . $sale_class . ' Vehicles</a></li>';
								} else {
									echo '<li class="armadillo-small"><a href="' . @add_query_arg( array( 'make' => $parameters[ 'make' ] ) , $do_not_carry ) . '">< View All ' . $parameters[ 'make' ] . '</a></li>';
									echo '<li class="armadillo-small"><a href="' . @add_query_arg( array( 'saleclass' => $sale_class ) , $do_not_carry ) . '">View ' . $sale_class. ' Vehicles</a></li>';
								}
								foreach( $vehicle_management_system->get_trims( array_merge( array( 'saleclass' => $sale_class , 'make' => $parameters[ 'make' ] , 'model' => $parameters[ 'model' ] ) , $filters ) ) as $trim ) {
									echo '<li><a href="' . @add_query_arg( array( 'trim' => $trim ) , $do_not_carry ) . '">' . $trim . '</a></li>';
								}
							?>
						</ul>
					</li>
					<?php
						endif;
					?>
					<li class="armadillo-expanded">
						<span>Price</span>
						<ul>
							<li><a href="<?php echo @add_query_arg( array( 'price_from' => '0', 'price_to' => '10000' ) , $do_not_carry ); ?>">$0 - $10,000</a></li>
							<li><a href="<?php echo @add_query_arg( array( 'price_from' => '10001', 'price_to' => '20000' ) , $do_not_carry ); ?>">$10,001 - $20,000</a></li>
							<li><a href="<?php echo @add_query_arg( array( 'price_from' => '20001', 'price_to' => '30000' ) , $do_not_carry ); ?>">$20,001 - $30,000</a></li>
							<li><a href="<?php echo @add_query_arg( array( 'price_from' => '30001', 'price_to' => '40000' ) , $do_not_carry ); ?>">$30,001 - $40,000</a></li>
							<li><a href="<?php echo @add_query_arg( array( 'price_from' => '40001', 'price_to' => '50000' ) , $do_not_carry ); ?>">$40,001 - $50,000</a></li>
							<li><a href="<?php echo @add_query_arg( array( 'price_from' => '50001', 'price_to' => '' ) , $do_not_carry ); ?>">$50,001 - &amp; Above</a></li>
						</ul>
					</li>
					<?php
						if( !isset( $parameters[ 'certified' ] ) && ( !isset( $parameters[ 'saleclass' ] ) || strtolower( $parameters[ 'saleclass' ] ) != 'new' ) ):
					?>
					<li class="armadillo-expanded">
						<span>Other</span>
						<ul>
							<li><a href="<?php echo @add_query_arg( array( 'certified' => 'yes' ) , $do_not_carry ); ?>">Certified Pre-Owned</a></li>
						</ul>
					</li>
					<?php endif; ?>
				</ul>
				<?php endif; ?>
			</div>
		</div>
		<div id="armadillo-listing-content">
			<div id="armadillo-sorting-columns">
				<div>Sort by</div>
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
				<div><a class="<?php echo $sort_year_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_year ) , $do_not_carry ); ?>">Year</a></div>
				<div><a class="<?php echo $sort_price_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_price ) , $do_not_carry ); ?>">Price</a></div>
				<div class="last"><a class="<?php echo $sort_mileage_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_mileage ) , $do_not_carry ); ?>">Mileage</a></div>
			</div>
			<div id="armadillo-listing-items">
				<?php
					if( empty( $inventory ) ) {
						echo '<div class="armadillo-not-found"><h2><strong>Unable to find inventory items that matched your search criteria.</strong></h2></div>';
					} else {
						foreach( $inventory as $inventory_item ):
							setlocale( LC_MONETARY , 'en_US' );
							$prices = $inventory_item->prices;
							$use_was_now = $prices->{ 'use_was_now?' };
							$use_price_strike_through = $prices->{ 'use_price_strike_through?' };
							$on_sale = $prices->{ 'on_sale?' };
							$sale_price = isset( $prices->sale_price ) ? $prices->sale_price : NULL;
							$retail_price = $prices->retail_price;
							$default_price_text = $prices->default_price_text;
							$asking_price = $prices->asking_price;
							$year = $inventory_item->year;
							$make = urldecode( $inventory_item->make );
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
							$contact_information = $inventory_item->contact_info;
							$generic_vehicle_title = $year . ' ' . $make . ' ' . $model; ?>
							<div class="armadillo-item" id="<?php echo $vin; ?>">
								<div class="armadillo-column-left">
									<div class="armadillo-photo">
										<a href="<?php echo $inventory_url; ?>" title="<?php echo $generic_vehicle_title; ?>">
											<img src="<?php echo $thumbnail; ?>" alt="<?php echo $generic_vehicle_title; ?>" title="<?php echo $generic_vehicle_title; ?>" />
										</a>
									</div>
								</div>
								<div class="armadillo-column-right">
									<div class="armadillo-main-line">
										<a href="<?php echo $inventory_url; ?>" title="<?php echo $generic_vehicle_title; ?>" class="details">
											<span class="armadillo-year"><?php echo $year; ?></span>
											<span class="armadillo-make"><?php echo $make; ?></span>
											<span class="armadillo-model"><?php echo $model; ?></span>
											<span class="armadillo-trim"><?php echo $trim; ?></span>
											<span class="armadillo-drive-train"><?php echo $drive_train; ?></span>
											<span class="armadillo-body-style"><?php echo $body_style; ?></span>
										</a>
									</div>
									<div class="armadillo-headline">
										<?php echo $headline; ?>
									</div>
									<div class="armadillo-details-left">
										<span class="armadillo-interior-color">Int. Color: <?php echo $interior_color; ?></span>
										<span class="armadillo-exterior-color">Ext. Color: <?php echo $exterior_color; ?></span>
										<span class="armadillo-transmission">Trans: <?php echo $transmission; ?></span>
									</div>
									<div class="armadillo-details-right">
										<span class="armadillo-stock-number">Stock #: <?php echo $stock_number; ?></span>
										<span class="armadillo-odometer">Mileage: <?php echo $odometer; ?></span>
										<span class="armadillo-vin">VIN: <?php echo $vin; ?></span>
									</div>
									<div class="armadillo-icons">
										<?php echo $icons; ?>
									</div>
									<div class="armadillo-price">
										<?php
											if( $on_sale ) {
												$now_text = 'Price: ';
												if( $use_was_now ) {
													$price_class = ( $use_price_strike_through ) ? 'armadillo-strike-through armadillo-asking-price' : 'armadillo-asking-price';
													echo '<div class="' . $price_class . '">Was: ' . money_format( '%(#0n' , $asking_price ) . '</div>';
													$now_text = 'Now: ';
												}
												echo '<div class="armadillo-sale-price">' . $now_text . money_format( '%(#0n' , $sale_price ) . '</div>';
											} else {
												if( $asking_price > 0 ) {
													echo '<div class="armadillo-asking-price">Price: ' . money_format( '%(#0n' , $asking_price ) . '</div>';
												} else {
													echo $default_price_text;
												}
											}
										?>
										<a href="<?php echo $inventory_url; ?>" title="More Information: <?php echo $generic_vehicle_title; ?>">More Information</a>
									</div>
									<div class="armadillo-contact-information">
										<?php echo $contact_information->company_id != $company_information->id ? $contact_information->dealer_name . ' - ' . $contact_information->phone : NULL; ?>
									</div>
									<br class="armadillo-clear" />
								</div>
								<br class="armadillo-clear" />
							</div>
					<?php
						flush();
						endforeach;
					}
				?>
				<br class="armadillo-clear" />
			</div>
		</div>
		<div id="armadillo-disclaimer">
			<?php echo !empty( $inventory ) ? '<p>' . $inventory[ 0 ]->disclaimer . '</p>' : NULL; ?>
		</div>
	</div>
	<?php echo $breadcrumbs; ?>
	<div class="armadillo-pager">
		<?php echo paginate_links( $args ); ?>
	</div>
	<a href="#armadillo-top" title="Return to Top" class="armadillo-return-to-top">Return to Top</a>
	<br class="armadillo-clear" />
</div>
<br class="armadillo-clear" />
