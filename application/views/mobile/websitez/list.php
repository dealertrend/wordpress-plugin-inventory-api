<?php

	global $wp_rewrite;

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
	} elseif ( $inventory[ 0 ]->pagination->total == 1 ) {
		$total_found = $inventory[ 0 ]->pagination->total * count( $inventory );
	} else {
		$total_found = $inventory[ 0 ]->pagination->total * $inventory[ 0 ]->pagination->per_page;
	}

	$do_not_carry = remove_query_arg( 'page' , $query );

	$tmp_do_not_carry = remove_query_arg( 'certified' , $do_not_carry );
	$new = ! empty( $wp_rewrite->rules ) ? '/inventory/new/' : add_query_arg( array( 'saleclass' => 'new' ) , $tmp_do_not_carry );
	$used = ! empty( $wp_rewrite->rules ) ? '/inventory/used/' : add_query_arg( array( 'saleclass' => 'used' ) );

?>

<div class="websitez-container dealertrend-mobile inventory">
	<div class="post nav">
		<div class="select">
			<select>
			<option>PUT STUFF HERE</option>
			</select>
		</div>
		<div class="paginate">
			<p><?php echo paginate_links( $args ); ?></p>
		</div>
	</div>
	<div class="listing">
		<?php
			if( empty( $inventory ) ) {
				echo '<div class="websitez-not-found"><h2><strong>Unable to find inventory items that matched your search criteria.</strong></h2><a onClick="history.go(-1)" title="Return to Previous Search" class="jquery-ui-button">Return to Previous Search</a></div>';
			} else {
				foreach( $inventory as $inventory_item ):
					$prices = $inventory_item->prices;
					$use_was_now = $prices->{ 'use_was_now?' };
					$use_price_strike_through = $prices->{ 'use_price_strike_through?' };
					$on_sale = $prices->{ 'on_sale?' };
					$sale_price = isset( $prices->sale_price ) ? $prices->sale_price : NULL;
					$sale_expire = isset( $prices->sale_expire ) ? $prices->sale_expire : NULL;
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
						$inventory_url = $site_url . '/inventory/' . $year . '/' . $make . '/' . $model . '/' . $state . '/' . $city . '/'. $vin . '/';
					} else {
						$inventory_url = '?taxonomy=inventory&amp;saleclass=' . $sale_class . '&amp;make=' . $make . '&amp;model=' . $model . '&amp;state=' . $state . '&amp;city=' . $city . '&amp;vin='. $vin;
					}
					$contact_information = $inventory_item->contact_info;
					$generic_vehicle_title = $year . ' ' . $make . ' ' . $model;
			?>
				<a href="<?php echo $inventory_url; ?>" title="<?php echo $generic_vehicle_title; ?>"><div class="post item" id="<?php echo $vin; ?>">
					<div class="post-wrapper">
						<div class="photo">
							<img src="<?php echo $thumbnail; ?>" alt="<?php echo $generic_vehicle_title; ?>" title="<?php echo $generic_vehicle_title; ?>"/>
						</div>
						<div class="details">
							<p class="headline"><?php echo $headline; ?></p>
							<p class="engine"><?php echo $engine; ?> - <?php echo $transmission; ?></p>
							<p class="exterior-color"><?php echo $exterior_color; ?></p>
							<p class="odometer"><?php echo number_format($odometer); ?> miles</p>
						</div>
						<div class="bottom-column">
							<p class="icons"><?php echo $icons; ?></p>
						</div>
						<div class="pricing">
							<?php
								$ais_incentive = isset( $inventory_item->ais_incentive->to_s ) ? $inventory_item->ais_incentive->to_s : NULL;
								$incentive_price = 0;
								if( $ais_incentive != NULL ) {
									preg_match( '/\$\d*\s/' , $ais_incentive , $incentive );
									$incentive_price = isset( $incentive[ 0 ] ) ? str_replace( '$' , NULL, $incentive[ 0 ] ) : 0;
								}
								if( $on_sale && $sale_price > 0 ) {
									$now_text = 'Price: ';
									if( $use_was_now ) {
										$price_class = ( $use_price_strike_through ) ? 'websitez-strike-through websitez-asking-price' : 'websitez-asking-price';
										if( $incentive_price > 0 ) {
											echo '<div class="' . $price_class . '">Was: ' . money_format( '%(#0n' , $sale_price ) . '</div>';
										} else {
											echo '<div class="' . $price_class . '">Was: ' . money_format( '%(#0n' , $asking_price ) . '</div>';
										}
										$now_text = 'Now: ';
									}
									if( $incentive_price > 0 ) {
										echo '<div class="websitez-ais-incentive">Savings: ' . $ais_incentive . '</div>';
										echo '<div class="websitez-sale-price">' . $now_text . money_format( '%(#0n' , $sale_price - $incentive_price ) . '</div>';
										if( $sale_expire != NULL ) {
											echo '<div class="websitez-sale-expires">Sale Expires: ' . $sale_expire . '</div>';
										}
									} else {
										if( $ais_incentive != NULL ) {
											echo '<div class="websitez-ais-incentive">Savings: ' . $ais_incentive . '</div>';
										}
										echo '<div class="websitez-sale-price">' . $now_text . money_format( '%(#0n' , $sale_price ) . '</div>';
										if( $sale_expire != NULL ) {
											echo '<div class="websitez-sale-expires">Sale Expires: ' . $sale_expire . '</div>';
										}
									}
								} else {
									if( $asking_price > 0 ) {
										if( $incentive_price > 0 ) {
											echo '<div class="websitez-asking-price" style="font-size:12px;">Retail Price: ' . money_format( '%(#0n' , $asking_price ) . '</div>';
											echo '<div class="websitez-ais-incentive">Savings: ' . $ais_incentive . '</div>';
											echo '<div class="websitez-asking-price" style="font-size:16px;">Your Price: ' . money_format( '%(#0n' , $asking_price - $incentive_price ) . '</div>';
										} else {
											if( $ais_incentive != NULL ) {
												echo '<div class="websitez-ais-incentive">Savings: ' . $ais_incentive . '</div>';
											}
											echo '<div class="websitez-asking-price">Price: ' . money_format( '%(#0n' , $asking_price ) . '</div>';
										}
									} else {
										if( $ais_incentive != NULL ) {
											echo '<div class="websitez-ais-incentive">Savings: ' . $ais_incentive . '</div>';
										}
										echo $default_price_text;
									}
								}
							?>
							</div>
						</div>
					</div>
				</a>
			<?php
				endforeach;
			}
			?>
	</div>
</div>
