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
		'type' => 'plain',
		'mid_size' => 1
	);

	$vehicle_class = isset( $parameters[ 'vehiclesclass' ] ) ? ucwords( $parameters[ 'vehicleclass' ] ) : 'All';

	$vehicle_management_system->tracer = 'Calculating how many items were returned with the given parameters.';

	if ( strcasecmp( $this->parameters['saleclass'], 'new') == 0 && !empty( $new_makes_filter ) ) {
		$total_found = $vehicle_management_system->get_inventory()->please( array_merge( $this->parameters , array( 'per_page' => 1 , 'photo_view' => 1 , 'make_filters' =>  $new_makes_filter ) ) );
	} else {
		$total_found = $vehicle_management_system->get_inventory()->please( array_merge( $this->parameters , array( 'per_page' => 1 , 'photo_view' => 1 ) ) );
	}
	$total_found = json_decode( $total_found[ 'body' ] );
	$total_found = is_array( $total_found ) && count( $total_found ) > 0 ? $total_found[ 0 ]->pagination->total : 0;

	$do_not_carry = remove_query_arg( 'page' , $query );

	$tmp_do_not_carry = remove_query_arg( 'certified' , $do_not_carry );
	$new = ! empty( $wp_rewrite->rules ) ? '/inventory/New/' : add_query_arg( array( 'saleclass' => 'new' ) , $tmp_do_not_carry );
	$used = ! empty( $wp_rewrite->rules ) ? '/inventory/Used/' : add_query_arg( array( 'saleclass' => 'used' ) );

	$vehicleclass = isset( $this->parameters[ 'vehicleclass' ] ) ? $this->parameters[ 'vehicleclass' ] : NULL;
	$certified = isset( $this->parameters[ 'certified' ] ) ? $this->parameters[ 'certified' ] : NULL;
	$price_to = isset( $this->parameters[ 'price_to' ] ) ? $this->parameters[ 'price_to' ] : NULL;
	$price_from = isset( $this->parameters[ 'price_from' ] ) ? $this->parameters[ 'price_from' ] : NULL;

	$filters = array(
		'vehicleclass' => $vehicleclass,
		'price_to' => $price_to,
		'price_from' => $price_from,
		'certified' => $certified
	);

	$vehicle_management_system->tracer = 'Obtaining a list of makes.';
	if ( strcasecmp( $sale_class, 'new') == 0 && !empty( $new_makes_filter ) ) { //Get Makes
		$makes = $new_makes_filter;
	} else {
		$makes = $vehicle_management_system->get_makes()->please( array_merge( array( 'saleclass' => $sale_class ) , $filters ) );
		$makes = json_decode( $makes[ 'body' ] );
	}
	$make_count = count ( $makes );

	if( isset( $parameters[ 'make' ] ) && $parameters[ 'make' ] != 'all' ) { //Get Models
		$tmp_do_not_carry = remove_query_arg( 'make' , $do_not_carry );
		$models = $vehicle_management_system->get_models()->please( array_merge( array( 'saleclass' => $sale_class , 'make' => $parameters[ 'make' ] ) , $filters ) );
		$models = json_decode( $models[ 'body' ] );
		$model_count = count( $models );

		$model = isset( $parameters[ 'model' ] ) ? $parameters[ 'model' ] : 'all';
		$parameters[ 'model' ] = $model;
		$model_text = 'All Models';
	} else {
		$model_text = 'Select a Make';
	}

	if( isset( $parameters[ 'model' ] ) && $parameters[ 'model' ] != 'all' ) { //Get Trims
		$tmp_do_not_carry = remove_query_arg( array( 'make' , 'model' ) , $do_not_carry );
		$trims = $vehicle_management_system->get_trims()->please( array_merge( array( 'saleclass' => $sale_class , 'make' => $parameters[ 'make' ] , 'model' => $parameters[ 'model' ] ) , $filters ) );
		$trims = json_decode( $trims[ 'body' ] );
		$trim_count = count( $trims );

		$trim = isset( $parameters[ 'trim' ] ) ? $parameters[ 'trim' ]  : 'all';
		$parameters[ 'trim' ] = $trim;
		$trim_text = 'All Trims';
	} else {
		$trim_text = 'Select a Model';
	}

	if( !empty( $wp_rewrite->rules ) ) { //
		$clean_url = true;
	} else {
		$clean_url = false;
	}

?>

<div id="dolphin-wrapper" class="dolphin-list-page"> <!-- 2nd Wrapper -->
	<div id="dolphin-top"> <!-- Listing Top -->
		<div id="dolphin-taxonomy"> <!-- SEO/Taxonomy -->
			<?php echo $breadcrumbs; ?> <!-- Breadcrumbs -->
			<div class="dolphin-pager"> <!-- Pager -->
				<?php echo paginate_links( $args ); ?>
			</div>
		</div>
		<div id="dolphin-filter-wrapper"> <!-- Search Filter Wrapper -->
			<div id="dolphin-filter-text"> <!-- Search Filter Text -->
				Filter
			</div>
			<div id="dolphin-filters"> <!-- Search Filter -->
				<div id="dolphin-makes"> <!-- Makes -->
					<label class="dolphin-label">Makes:</label>
					<select onchange="dolphin_filter_select('make');" class="dolphin-select">
						<option value="">All Makes</option>
						<?php
							$shown_makes = array();
							foreach( $makes as $make ) {
								$make_safe = str_replace( '/' , '%252' , $make );
								$make_safe = ucwords( strtolower( $make_safe ) );
								if( ! in_array( $make_safe , $shown_makes ) ) {
									$shown_makes[] = $make_safe;

									$value = '<option value="' . $make_safe . '"';
									if( rawurlencode( strtolower( $make_safe ) ) == strtolower( $parameters[ 'make' ] ) ) {
										$value .= ' selected="selected" ';
									}
									$value .= '>' . $make . '</option>';
									echo $value;
								}
							}
						?>
					</select>
				</div>
				<div id="dolphin-models"> <!-- Models -->
					<label class="dolphin-label">Models:</label>
					<select onchange="dolphin_filter_select('model');" class="dolphin-select"<?php if( ! isset( $model_count ) || $model_count == 0 ) { echo 'readonly'; } ?>>
						<option value=""/><?php echo $model_text; ?></option>
						<?php
							if( $model_count > 0 ) {
								if( $model_count == 1 ) {
									$parameters[ 'model' ] = rawurlencode( $models[ 0 ] );
								}
								foreach( $models as $model ) {
									$model_safe = str_replace( '/' , '%252' , $model );
									$model_safe = ucwords( strtolower( $model_safe ) );

									$value = '<option value="' . $model_safe . '"';
									if( rawurlencode( strtolower( $model_safe ) ) == strtolower( $parameters[ 'model' ] ) ) {
										$value .= ' selected="selected" ';
									}
									$value .= '>' . ucwords( strtolower( $model ) ) . '</option>';
									echo $value;
								}
							}
						?>
					</select>
				</div>
				<div id="dolphin-trims"> <!-- Trims -->
					<label class="dolphin-label">Trims:</label>
					<select onchange="dolphin_filter_select('trim');" class="dolphin-select"<?php if( ! isset( $trim_count ) || $trim_count == 0 ) { echo 'readonly'; } ?>>
						<option value=""><?php echo $trim_text; ?></option>
						<?php
							if( isset( $trim_count ) && $trim_count != 0 ) {
								if( $trim_count == 1 ) {
									$parameters[ 'trim' ] = $trims[ 0 ];
								}
								foreach( $trims as $trim ) {
									$trim_safe = str_replace( '/' , '%252' , $trim );
									$trim_safe = ucwords( strtolower( $trim_safe ) );

									$value = '<option value="' . $trim_safe . '"';
									if( rawurlencode( strtolower( $trim_safe ) ) == strtolower( $parameters[ 'trim' ] ) ) {
										$value .= ' selected="selected" ';
									}
									$value .= '>' . ucwords( strtolower( $trim ) ) . '</option>';
									echo $value;
								}
							}
						?>
					</select>
				</div>
			</div>
		</div>
		<div id="dolphin-search-wrapper" > <!-- Search Wrapper -->
			<form action="#" method="POST" id="dolphin-search"> <!-- Vehicle Search -->
				<input type="hidden" id="hidden-rewrite" value="<?php if ( !empty( $wp_rewrite->rules ) ) { echo 'true'; } ?>" name="h_taxonomy" />
				<input type="hidden" id="hidden-saleclass" value="<?php echo ucwords( strtolower( $sale_class ) ) ?>" name="h_saleclass" />
				<div id="dolphin-search-advance" style="display: none;">
					<div class="dolphin-advance-peram">
						<label class="dolphin-label" for="price-range">Price Range -</label><span>(From: 1000 To: 10000)</span><br>
						<label class="dolphin-label" for="price_from">From:</label>
						<input id="dolphin-price-from" name="price_from" value="<?php echo isset( $parameters[ 'price_from' ] ) ? $parameters[ 'price_from' ] : NULL; ?>" />
						<label class="dolphin-label" for="price_to">To:</label>
						<input id="dolphin-price-to" name="price_to" value="<?php echo isset( $parameters[ 'price_to' ] ) ? $parameters[ 'price_to' ] : NULL; ?>" />
					</div>
					<div class="dolphin-advance-peram">
						<label class="dolphin-label" for="year-range">Year -</label><span>(From: 2010 To: 2013)</span><br>
						<label class="dolphin-label" for="year_from">From:</label>
						<input id="dolphin-year-from" name="year_from" value="<?php echo isset( $parameters[ 'year_from' ] ) ? $parameters[ 'year_from' ] : NULL; ?>" />
						<label class="dolphin-label" for="year_to">To:</label>
						<input id="dolphin-year-to" name="year_to" value="<?php echo isset( $parameters[ 'year_to' ] ) ? $parameters[ 'year_to' ] : NULL; ?>" />
					</div>
					<div class="dolphin-advance-peram">
						<label class="dolphin-label" for="mileage-range">Odometer -</label><span>(From: 20000 To: 50000)</span><br>
						<label class="dolphin-label" for="mileage_from">From:</label>
						<input id="dolphin-mileage-from" name="mileage_from" value="<?php echo isset( $parameters[ 'mileage_from' ] ) ? $parameters[ 'mileage_from' ] : NULL; ?>" />
						<label class="dolphin-label" for="mileage_to">To:</label>
						<input id="dolphin-mileage-to" name="mileage_to" value="<?php echo isset( $parameters[ 'mileage_to' ] ) ? $parameters[ 'mileage_to' ] : NULL; ?>" />
					</div>
					<div class="dolphin-advance-peram">
						<label class="dolphin-label">Body Style: </label>
						<select id="dolphin-vehicleclass" class="dolphin-select">
							<option value="">All</option>
							<option value="car" <?php echo $vehicleclass == 'car' ? 'selected' : NULL; ?>>Car</option>
							<option value="truck" <?php echo $vehicleclass == 'truck' ? 'selected' : NULL; ?>>Truck</option>
							<option value="sport_utility" <?php echo $vehicleclass == 'sport_utility' ? 'selected' : NULL; ?>>SUV</option>
							<option value="van,minivan" <?php echo $vehicleclass == 'van,minivan' ? 'selected' : NULL; ?>>Van</option>
						</select>
					</div>
				</div>
				<div id="dolphin-search-top">
					<div id="dolphin-search-text"> <!-- Search Filter Text -->
						Search
					</div>
					<div id="dolphin-search-mid">
						<select id="dolphin-saleclass" class="dolphin-select">
							<?php
								switch( $sale_class_filter ) {
									case 'all':
										echo '<option value="New" ' . (strtolower( $sale_class ) == 'new' ? 'selected' : NULL) . ' >New Vehicles</option>';
										echo '<option value="Used" ' . (strtolower( $sale_class ) == 'used' && empty( $certified ) ? 'selected' : NULL) . ' >Pre-Owned Vehicles</option>';
										echo '<option value="Certified" ' . (strtolower( $sale_class ) == 'used' && !empty( $certified ) ? 'selected' : NULL) . ' >Certified Pre-Owned</option>';
										break;
									case 'new':
										echo '<option value="New" selected >New Vehicles</option>';
										break;
									case 'used':
										echo '<option value="Used" ' . (strtolower( $sale_class ) == 'used' && empty( $certified ) ? 'selected' : NULL) . ' >Pre-Owned Vehicles</option>';
										echo '<option value="Certified" ' . (strtolower( $sale_class ) == 'used' && !empty( $certified ) ? 'selected' : NULL) . ' >Certified Pre-Owned</option>';
										break;
									case 'certified':
										echo '<option value="Certified" selected >Certified Pre-Owned</option>';
										break;
								}
							?>
						</select>
						<button id="dolphin-search-submit">GO :</button>
					</div>
					<input id="dolphin-search-box" name="search" value="<?php echo isset( $parameters[ 'search' ] ) ? $parameters[ 'search' ] : NULL; ?>" />
				</div>
				<div id="dolphin-apply-search">
					<input type="checkbox" name="apply_search" <?php if ( !empty( $apply_search ) ) { echo 'checked'; } ?> />
					<span>Apply search parameters to filters.</span>
				</div>
			</form>
			<div id="dolphin-advance-show" name="hidden">
				Advance>>
			</div>
		</div>
		<div id="dolphin-search-info"> <!-- Search Info -->
			<div id="dolphin-total-found">
				Vehicles Found: <?php echo $total_found; ?>
			</div>
			<div id="dolphin-sort">
			</div>
		</div>
	</div>

	<div id="dolphin-listing"> <!-- Inventory -->
		<div id="dolphin-listing-items"> <!-- Inventory Listing -->
			<?php
				if( empty( $inventory ) ) {
					echo '<div class="dolphin-not-found"><h2><strong>Unable to find inventory items that matched your search criteria.</strong></h2><a onClick="history.go(-1)" title="Return to Previous Search" class="jquery-ui-button">Return to Previous Search</a></div>';
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
						$make_safe = str_replace( '/' , '%252' ,  $make );
						$model = urldecode( $inventory_item->model_name );
						$model_safe = str_replace( '/' , '%252' ,  $model );
						$vin = $inventory_item->vin;
						$trim = urldecode( $inventory_item->trim );
						$trim_safe = str_replace( '/' , '%252' ,  $trim );
						$engine = $inventory_item->engine;
						$transmission = $inventory_item->transmission;
						$exterior_color = $inventory_item->exterior_color;
						$interior_color = $inventory_item->interior_color;
						$stock_number = $inventory_item->stock_number;
						$odometer = $inventory_item->odometer;
						$icons = $inventory_item->icons;
						$tags = $inventory_item->tags;
						$certified_inv = $inventory_item->certified;
						$thumbnail = urldecode( $inventory_item->photos[ 0 ]->small );
						$body_style = $inventory_item->body_style;
						$drive_train = $inventory_item->drive_train;
						$doors = $inventory_item->doors;
						$headline = $inventory_item->headline;
						$saleclass = $inventory_item->saleclass;
						$certified = $inventory_item->certified;
						if( !empty( $wp_rewrite->rules ) ) {
							$inventory_url = $site_url . '/inventory/' . $year . '/' . $make_safe . '/' . $model_safe . '/' . $state . '/' . $city . '/'. $vin . '/';
						} else {
							$inventory_url = '?taxonomy=inventory&amp;saleclass=' . $sale_class . '&amp;make=' . $make_safe . '&amp;model=' . $model_safe . '&amp;state=' . $state . '&amp;city=' . $city . '&amp;vin='. $vin;
						}
						$contact_information = $inventory_item->contact_info;
						$generic_vehicle_title = $year . ' ' . $make . ' ' . $model;

						// AIS Info
						$ais_incentive = isset( $inventory_item->ais_incentive->to_s ) ? $inventory_item->ais_incentive->to_s : NULL;
						$incentive_price = 0;
						if( $ais_incentive != NULL ) {
							preg_match( '/\$\d*(\s)?/' , $ais_incentive , $incentive );
							$incentive_price = isset( $incentive[ 0 ] ) ? str_replace( '$' , NULL, $incentive[ 0 ] ) : 0;
						}
			?>
			<div class="dolphin-item" id="<?php echo $vin; ?>"> <!-- Inventory Listing -->
				<div class="dolphin-column-left"> <!-- dolphin column left -->
					<div class="dolphin-photo"> <!-- dolphin photo -->
						<a href="<?php echo $inventory_url; ?>" title="<?php echo $generic_vehicle_title; ?>">
							<img src="<?php echo $thumbnail; ?>" alt="<?php echo $generic_vehicle_title; ?>" title="<?php echo $generic_vehicle_title; ?>" />
						</a>
					</div>
					 <!-- dolphin icons -->
					<?php
						if( !empty( $tags ) ){
							echo '<div class="dolphin-icons">';
								apply_special_tags( $tags, $on_sale, $certified_inv);
								$tag_icons = build_tag_icons( $default_tag_names, $custom_tag_icons, $tags);
								echo $tag_icons;
							echo '</div>';
						}
					?>
				</div>
				<div class="dolphin-column-right"> <!-- dolphin column right -->
					<div class="dolphin-headline">
						<div class="dolphin-headline-details">
							<span class="dolphin-saleclass"><?php echo $saleclass; ?></span>
							<a href="<?php echo $inventory_url; ?>" title="<?php echo $generic_vehicle_title; ?>" >
								<span class="dolphin-year"><?php echo $year; ?></span>
								<span class="dolphin-make"><?php echo $make; ?></span>
								<span class="dolphin-model"><?php echo $model; ?></span>
								<span class="dolphin-trim"><?php echo $trim; ?></span>
							</a>
						</div>
						<?php
							if( $retail_price > 0 && strtolower( $saleclass ) == 'new' ) {
								echo '<div class="dolphin-headline-msrp" alt="' . $retail_price . '">MSRP: ' . '$' . number_format( $retail_price , 0 , '.' , ',' ) . '</div>';
							}

							if( strlen( trim( $headline ) ) > 0 ) {
								echo '<div class="dolphin-headline-text">' . $headline . '</div>';
							}
						?>
					</div>
					<div class="dolphin-listing-info">
						<div class="dolphin-details-left">
							<span class="dolphin-stock-number" alt="<?php echo $stock_number; ?>" >Stock #: <?php echo $stock_number; ?></span>
							<span class="dolphin-vin" alt="<?php echo $vin; ?>">VIN: <?php echo $vin; ?></span>
							<?php
								if ( $odometer > 100 ) {
									echo '<span class="dolphin-odometer">Odometer: ' . $odometer . '</span>';
								}
								if ( $certified == 'true') {
									echo '<span class="dolphin-certified">Certified Pre-Owned</span>';
								}
							?>
						</div>
						<div class="dolphin-details-right">
							<?php
								echo $body_style != NULL ? '<span class="dolphin-body-style">Body Style: ' . $body_style . '</span>' : NULL;
								echo $interior_color != NULL ? '<span class="dolphin-interior-color">Int. Color: ' . $interior_color . '</span>' : NULL;
								echo $exterior_color != NULL ? '<span class="dolphin-exterior-color">Ext. Color: ' . $exterior_color . '</span>' : NULL;
								echo $transmission != NULL ? '<span class="dolphin-transmission">Trans: ' . $transmission . '</span>' : NULL;
							?>
						</div>
						<div class="dolphin-price">
							<?php
								if( $on_sale && $sale_price > 0 ) {
									$now_text = 'Price: ';
									if( $use_was_now ) {
										$price_class = ( $use_price_strike_through ) ? 'dolphin-strike-through dolphin-asking-price' : 'dolphin-asking-price';
										if( $incentive_price > 0 ) {
											echo '<div class="' . $price_class . ' dolphin-ais">Was: ' . '$' . number_format( $sale_price , 0 , '.' , ',' ) . '</div>';
										} else {
											echo '<div class="' . $price_class . '">Was: ' . '$' . number_format( $asking_price , 0 , '.' , ',' ) . '</div>';
										}
										$now_text = 'Now: ';
									}
									if( $incentive_price > 0 ) {
										echo '<div class="dolphin-ais-incentive-l-text">' . $ais_incentive . '</div>';
										echo '<div class="dolphin-sale-price dolphin-ais dolphin-main-price">' . $now_text . '$' . number_format( $sale_price - $incentive_price , 0 , '.' , ',' ) . '</div>';
										if( $sale_expire != NULL ) {
											echo '<div class="dolphin-sale-expires">Sale Expires: ' . $sale_expire . '</div>';
										}
									} else {
										if( $ais_incentive != NULL ) {
											echo '<div class="dolphin-ais-incentive-l-text">' . $ais_incentive . '</div>';
										}
										echo '<div class="dolphin-sale-price dolphin-main-price">' . $now_text . '$' . number_format( $sale_price , 0 , '.' , ',' ) . '</div>';
										if( $sale_expire != NULL ) {
											echo '<div class="dolphin-sale-expires">Sale Expires: ' . $sale_expire . '</div>';
										}
									}
								} else {
									if( $asking_price > 0 ) {
										if( $incentive_price > 0 ) {
											echo '<div class="dolphin-asking-price dolphin-ais">Asking Price: $' . number_format( $asking_price , 0 , '.' , ',' ) . '</div>';
											echo '<div class="dolphin-ais-incentive-l-text">' . $ais_incentive . '</div>';
											echo '<div class="dolphin-your-price dolphin-ais dolphin-main-price">Your Price: $' . number_format( $asking_price - $incentive_price , 0 , '.' , ',' ) . '</div>';
										} else {
											if( $ais_incentive != NULL ) {
												echo '<div class="dolphin-ais-incentive-l-text">' . $ais_incentive . '</div>';
											}
											echo '<div class="dolphin-asking-price dolphin-main-price">Price: $' . number_format( $asking_price , 0 , '.' , ',' ) . '</div>';
										}
									} else {
										if( $ais_incentive != NULL ) {
											echo '<div class="dolphin-ais-incentive-l-text">' . $ais_incentive . '</div>';
										}
										echo '<div class="dolphin-no-price dolphin-main-price">' . $default_price_text . '</div>';
									}
								}

								if( $ais_incentive != NULL && isset( $company_information->api_keys ) ) {
									$value_ais = '<div class="dolphin-ais-incentive-s-text view-available-rebates">';
									$value_ais .= '<a href="http://onecar.aisrebates.com/dlr2/inline/IncentiveOutput.php?vID='. $vin . '&wID=' . $company_information->api_keys->ais . '&zID=' . $company_information->zip . '" target="_blank" title="VIEW AVAILABLE INCENTIVES AND REBATES" onclick="return loadIframe( this.href );">VIEW AIS</a>';
									$value_ais .= '</div>';
									echo $value_ais;
								}
							?>
						</div>
					</div>
					<div class="dolphin-more-info">
						<div class="dolphin-contact-information">
							<?php
								if ( strtolower( $saleclass ) == 'new' && !empty( $phone_new ) ) {
									$phone_value = $phone_new;
								} elseif ( strtolower( $saleclass ) == 'used' && !empty( $phone_used ) ) {
									$phone_value = $phone_used;
								} else {
									$phone_value = $contact_information->phone;
								}
								echo $contact_information->company_id != $company_information->id ? $contact_information->dealer_name . ' - ' . $phone_value : NULL;
							?>
						</div>
						<div class="dolphin-detail-button">
							<a href="<?php echo $inventory_url; ?>" title="More Information: <?php echo $generic_vehicle_title; ?>">More Information</a>
						</div>
					</div>
				</div>
			</div>
			<?php
				flush();
				endforeach;
				}
			?>
		</div>
	</div>
	<div id="dolphin-disclaimer">
		<?php echo !empty( $inventory ) ? '<p>' . $inventory[ 0 ]->disclaimer . '</p>' : NULL; ?>
	</div>
	<div id="dolphin-bottom">
		<?php echo $breadcrumbs; ?>
		<div class="dolphin-pager">
			<?php echo paginate_links( $args ); ?>
		</div>
		<a href="#dolphin-wrapper" title="Return to Top" class="dolphin-return-to-top">Return to Top</a>
	</div>
</div>
