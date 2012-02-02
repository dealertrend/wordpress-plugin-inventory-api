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
	} elseif ( $inventory[ 0 ]->pagination->total == 1 ) {
		$total_found = $inventory[ 0 ]->pagination->total * count( $inventory );
	} else {
		$total_found = $inventory[ 0 ]->pagination->total * $inventory[ 0 ]->pagination->per_page;
	}

	$query = '?' . http_build_query( $_GET );

?>

<div id="cobra-wrapper">
	<br class="cobra-clear" id="cobra-top" />
	<div id="cobra-listing">
		<div id="cobra-listing-top">
			<div id="cobra-quick-links">
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
					if( !isset( $parameters[ 'model' ] ) || strtolower( $parameters[ 'model' ] ) == 'all' ):
						$do_not_carry = remove_query_arg( 'page' , $query );
					endif;
?>
					<span>Make</span>
						<select onchange="window.location = this.value;" class="styled">
<?php
							if( isset( $parameters[ 'saleclass' ] ) ) {
								echo '<option value="' . $site_url . '/inventory/' . $parameters['saleclass'] . '/">All</option>';
							}
							foreach( $vehicle_management_system->get_makes( array_merge( array( 'saleclass' => $sale_class ) , $filters ) ) as $make ) {
								if( !empty( $wp_rewrite->rules ) ) {
									$url = $site_url . '/inventory/' . $sale_class . '/' . $make . '/';
									$url .= isset( $this->parameters[ 'vehicleclass' ] ) ? '?' . http_build_query( array( 'vehicleclass' => $this->parameters[ 'vehicleclass' ] ) ) : NULL;?>
									<option value="<?=$url?>"<?php if ($make == $parameters['make']) {echo ' selected="selected"';} ?>><?=$make?></option>
<?php
								} else {
?>
									<option value="<?=@add_query_arg( array( 'make' => $make ) , $do_not_carry )?>"<?php if ($make == $parameters['make']) {echo ' selected="selected"';} ?>><?=$make?></option>
<?php
								}
							}
?>
						</select>
						<span>Model</span>
						<select onchange="window.location = this.value;">
<?php
							if( !empty( $wp_rewrite->rules ) ) { ?>
								<option value="<?=$site_url?>/inventory/<?=$sale_class?>/">View <?=$sale_class?> Vehicles</option>
<?php
							} else {
?>
								<option value="<?=@add_query_arg( array( 'saleclass' => $sale_class ) , $do_not_carry )?>">View <?=$sale_class?> Vehicles</option>
<?php
							}
							foreach( $vehicle_management_system->get_models( array_merge( array( 'saleclass' => $sale_class , 'make' => $parameters[ 'make' ] ) , $filters ) ) as $model ) {
								if( !empty( $wp_rewrite->rules ) ) {
									$url = $site_url . '/inventory/' . $sale_class . '/' . $parameters[ 'make' ] . '/' . $model . '/'; ?>
									<option value="<?=$url?>"<?php if ($model == $parameters['model']) {echo ' selected="selected"';} ?>><?=$model?></option>
<?php
								} else {
?>
									<option value="<?=@add_query_arg( array( 'model' => $model ) , $do_not_carry )?><?php if ($model == $parameters['model']) {echo ' selected="selected"';} ?>"><?=$model?></option>
<?php
								}
						} ?>
					</select>
<?php
				endif;
?>
				<form action="<?php echo $site_url; ?>/inventory/" method="GET" id="cobra-search">
					<input id="cobra-search-box" name="search" value="<?php echo isset( $parameters[ 'search' ] ) ? $parameters[ 'search' ] : NULL; ?>" />
					<input id="cobra-search-submit" value="Go" type="submit" />
				</form>
			</div>
			<div id="cobra-total-found">Found <?php echo $total_found; ?> Exact Matches:&nbsp;</div><?php echo $breadcrumbs; ?>
			<div class="cobra-pager">
				<?php echo paginate_links( $args ); ?>
			</div>
			<div id="cobra-sorting-columns">
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
				Sort options: 
				<a class="<?php echo $sort_year_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_year ) , $do_not_carry ); ?>">Year</a> / 
				<a class="<?php echo $sort_price_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_price ) , $do_not_carry ); ?>">Price</a> / 
				<a class="<?php echo $sort_mileage_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_mileage ) , $do_not_carry ); ?>">Mileage</a>
			</div>
		</div>
		<div id="cobra-listing-content">
			<div id="cobra-listing-items">
<?php
				if( empty( $inventory ) ) {
					echo '<div class="cobra-not-found"><h2><strong>Unable to find inventory items that matched your search criteria.</strong></h2></div>';
				} else {
					foreach( $inventory as $inventory_item ):
						$sale_class = $inventory_item->saleclass;
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
							$inventory_url = $site_url . '/inventory/' . $year . '/' . $make . '/' . $model . '/' . $state . '/' . $city . '/'. $vin . '/';
						} else {
							$inventory_url = '?taxonomy=inventory&amp;saleclass=' . $sale_class . '&amp;make=' . $make . '&amp;model=' . $model . '&amp;state=' . $state . '&amp;city=' . $city . '&amp;vin='. $vin;
						}
						$contact_information = $inventory_item->contact_info;
						$generic_vehicle_title = $year . ' ' . $make . ' ' . $model;
?>
						<div class="cobra-item" id="<?php echo $vin; ?>">
							<div class="cobra-listing-photo">
								<a href="<?php echo $inventory_url; ?>" title="<?php echo $generic_vehicle_title; ?>">
									<img src="<?php echo $thumbnail; ?>" alt="<?php echo $generic_vehicle_title; ?>" title="<?php echo $generic_vehicle_title; ?>" />
								</a>
								<a class="cobra-listing-moreinfo" href="<?php echo $inventory_url; ?>" title="More Information: <?php echo $generic_vehicle_title; ?>">Click here to view details</a>
							</div>
							<div class="cobra-listing-left">
								<div class="cobra-main-line">
									<a href="<?php echo $inventory_url; ?>" title="<?php echo $generic_vehicle_title; ?>" class="details">
										<span class="cobra-year"><?php echo $year; ?></span>
										<span class="cobra-make"><?php echo $make; ?></span>
										<span class="cobra-model"><?php echo $model; ?></span><br />
										<span class="cobra-trim"><?php echo $trim; ?></span>
									</a>
								</div>
								<div class="cobra-stock-vin">
									Stock #: <?php echo $stock_number; ?> - VIN: <?php echo $vin; ?>
								</div>
							</div>
							<div class="cobra-listing-right">
								<div class="cobra-listing-price">
									<span class="listing-msrp"><?php if ($sale_class == 'New'):?>MSRP<?php endif; ?></span>
									<div class="cobra-price">
<?php
										if( $on_sale && $sale_price > 0 ) {
											$now_text = 'Price: ';
											if( $use_was_now ) {
												$price_class = ( $use_price_strike_through ) ? 'cobra-strike-through cobra-asking-price' : 'cobra-asking-price';
												echo '<div class="' . $price_class . '">Was: ' . money_format( '%(#0n' , $asking_price ) . '</div>';
												$now_text = 'Now: ';
											}
											echo '<div class="cobra-sale-price">' . $now_text . money_format( '%(#0n' , $sale_price ) . '</div>';
										} else {
											if( $asking_price > 0 ) {
												echo '<div class="cobra-asking-price"><span>' . substr( money_format( '%(#0n' , $asking_price ) , 1 , 1) . '</span>' . substr( money_format( '%(#0.0n' , $asking_price ) , 2) . '</div>';
											} else {
												echo '<div>' . $default_price_text . '</div>';
											}
										}
?>
									</div>
								</div>
								<a class="cobra-listing-moreinfo-btn" href="<?php echo $inventory_url; ?>"></a>
							</div>
							<div class="cobra-listing-info">
								<span class="cobra-exterior-color">Ext. Color: <?php echo $exterior_color; ?></span>
								<span class="cobra-interior-color">Int. Color: <?php echo $interior_color; ?></span>
								<span class="cobra-engine">Engine: <?php echo $engine; ?></span>
							</div>
							<div class="cobra-listing-cpo">
<?php
								if ($inventory_item->certified) {
?>
								<img src="<?php echo plugin_dir_url(__FILE__); ?>images/<?php echo $make; ?>-cpo-white.png" />
<?php
								}
?>
							</div>
							<!--<div class="cobra-icons">
<?php
								if (strpos($icons,'assets0')) {
									$iconLink = 'assets0';
								} else {
									$iconLink = 'assets3';
								}
								echo str_replace('http://'.$iconLink.'.dealertrend.com/images/themes/vehicle_inventory/silver_surfer/icons/',get_bloginfo('wpurl').'/wp-content/plugins/dealertrend-inventory-api/application/views/inventory/cobra/images/icons/',$icons);
?>
							</div>-->
							<br class="cobra-clear" />
					</div>
<?php
					endforeach;
				}
?>
					<div class="cobra-pager-bottom">
				<?php echo paginate_links( $args ); ?>
			</div>
				</div>
				<br class="cobra-clear" />
			</div>
		</div>
		<div id="cobra-disclaimer">
			<?php echo !empty( $inventory ) ? '<p>' . $inventory[ 0 ]->disclaimer . '</p>' : NULL; ?>
		</div>
	</div>
	<br class="cobra-clear" />
</div>
<br class="cobra-clear" />
<div style="display: none;">
	<div id="cobra-make-offer">
		<h1>Make us an Offer</h1>
		<div class="required">*Required information</div>
		<?php echo do_shortcode('[contact-form-7 id="200" title="Make Offer Form"]'); ?>
	</div>
</div>
