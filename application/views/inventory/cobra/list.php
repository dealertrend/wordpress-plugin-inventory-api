<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

	$on_page = isset( $inventory[ 0 ]->pagination->on_page ) ? $inventory[ 0 ]->pagination->on_page : 0;
	$page_total = isset( $inventory[ 0 ]->pagination->total ) ? $inventory[ 0 ]->pagination->total : 0;

	$args = array(
		'base' => add_query_arg( 'page' , '%#%' ),
		'current' => $on_page,
		'total' => $page_total,
		'next_text' => __( 'Next &raquo;' ),
		'prev_text' => __( '< Previous' ),
		'show_all' => false,
		'type' => 'plain'
	);

	$vehicle_management_system->tracer = 'Calculating how many items were returned with the given parameters.';
	$vehicle_total_found = $vehicle_management_system->get_inventory()->please( array_merge( $this->parameters , array( 'per_page' => 1 , 'photo_view' => 1 , 'make_filters' =>  $inventory_options['make_filter'] ) ) );
	$vehicle_total_found = ( isset($vehicle_total_found[ 'body' ]) ) ? json_decode( $vehicle_total_found[ 'body' ] ) : NULL;
	$vehicle_total_found = is_array( $vehicle_total_found ) && count( $vehicle_total_found ) > 0 ? $vehicle_total_found[ 0 ]->pagination->total : 0;

	$do_not_carry = remove_query_arg( 'page' , $query );
	$tmp_do_not_carry = remove_query_arg( 'certified' , $do_not_carry );

	$filters = array(
		'vehicleclass' => isset( $this->parameters[ 'vehicleclass' ] ) ? $this->parameters[ 'vehicleclass' ] : NULL,
		'price_to' => isset( $this->parameters[ 'price_to' ] ) ? $this->parameters[ 'price_to' ] : NULL,
		'price_from' => isset( $this->parameters[ 'price_from' ] ) ? $this->parameters[ 'price_from' ] : NULL,
		'certified' => isset( $this->parameters[ 'certified' ] ) ? $this->parameters[ 'certified' ] : NULL
	);

	$vehicle_management_system->tracer = 'Obtaining a list of makes.';
	if ( strcasecmp( $param_saleclass, 'new') == 0 && !empty( $inventory_options['make_filter'] ) ) { //Get Makes
		$makes = $new_makes_filter;
	} else {
		$makes = $vehicle_management_system->get_makes()->please( array_merge( array( 'saleclass' => $param_saleclass ) , $filters ) );
		$makes = json_decode( $makes[ 'body' ] );
	}
	$make_count = count ( $makes );

	if( isset( $parameters[ 'make' ] ) && $parameters[ 'make' ] != 'all' ) { //Get Models
		$vehicle_management_system->tracer = 'Obtaining a list of models.';
		$tmp_do_not_carry = remove_query_arg( 'make' , $do_not_carry );
		$models = $vehicle_management_system->get_models()->please( array_merge( array( 'saleclass' => $param_saleclass , 'make' => $parameters[ 'make' ] ) , $filters ) );
		$models = json_decode( $models[ 'body' ] );
		$model_count = count( $models );
		if( !in_array( rawurldecode($parameters[ 'model' ]), $models ) && !empty($parameters[ 'model' ]) ){
			$search_error = 'The current model('.$parameters[ 'model' ].') could not be found with current search parameters. Reset search or adjust search parameters. ';
		}
		$model = isset( $parameters[ 'model' ] ) ? $parameters[ 'model' ] : 'all';
		$parameters[ 'model' ] = $model;
		$model_text = 'All Models';
	} else {
		$model_text = 'Select a Make';
	}

	if( isset( $parameters[ 'model' ] ) && $parameters[ 'model' ] != 'all' ) { //Get Trims
		$vehicle_management_system->tracer = 'Obtaining a list of trims.';
		$tmp_do_not_carry = remove_query_arg( array( 'make' , 'model' ) , $do_not_carry );
		$trims = $vehicle_management_system->get_trims()->please( array_merge( array( 'saleclass' => $param_saleclass , 'make' => $parameters[ 'make' ] , 'model' => $parameters[ 'model' ] ) , $filters ) );
		$trims = json_decode( $trims[ 'body' ] );
		$trim_count = count( $trims );
		if( !in_array( rawurldecode($parameters[ 'trim' ]), $trims ) && !empty( $parameters[ 'trim' ] ) ){
			$search_error = 'The current trim('.$parameters[ 'trim' ].') could not be found with current search parameters. Reset search or adjust search parameters. ';
		}
		$trim = isset( $parameters[ 'trim' ] ) ? $parameters[ 'trim' ]  : 'all';
		$parameters[ 'trim' ] = $trim;
		$trim_text = 'All Trims';
	} else {
		$trim_text = 'Select a Model';
	}

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

	$shown_makes = array();

	?>
	<div id="cobra-wrapper">
		<div id="cobra-listing">
			<div class="breadcrumbs"><?php echo display_breadcrumb( $this->parameters, $company_information, $inventory_options[company_override] ); ?></div>
			<div id="cobra-quick-links">
				<div id="cobra-quick-selects">
					<div class="quick-link-wrap">
						<label class="cobra-label">Makes:</label>
						<select onchange="cobra_filter_select('make');" class="cobra-select cobra-select-makes">
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
					<div class="quick-link-wrap">
						<label class="cobra-label">Models:</label>
						<select onchange="cobra_filter_select('model');" class="cobra-select cobra-select-models"<?php if( ! isset( $model_count ) || $model_count == 0 ) { echo 'readonly'; } ?>>
							<option value=""><?php echo $model_text; ?></option>
							<?php
								if( $model_count > 0 ) {
									if( $model_count == 1 ) {
										$parameters[ 'model' ] = rawurlencode( $models[ 0 ] );
									}
									foreach( $models as $model ) {
										$model_safe = str_replace( '/' , '%252' , $model );
										$value = '<option value="' . $model_safe . '"';
										if( rawurlencode( strtolower( $model_safe ) ) == strtolower( $parameters[ 'model' ] ) ) {
											$value .= ' selected="selected" ';
										}
										$value .= '>' . $model . '</option>';
										echo $value;
									}
								}
							?>
						</select>
					</div>
					<div class="quick-link-wrap">
						<label class="cobra-label">Trims:</label>
						<select onchange="cobra_filter_select('trim');" class="cobra-select cobra-select-trims"<?php if( ! isset( $trim_count ) || $trim_count == 0 ) { echo 'readonly'; } ?>>
							<option value=""><?php echo $trim_text; ?></option>
							<?php
								if( isset( $trim_count ) && $trim_count != 0 ) {
									if( $trim_count == 1 ) {
										$parameters[ 'trim' ] = $trims[ 0 ];
									}
									foreach( $trims as $trim ) {
										$trim_safe = str_replace( '/' , '%252' , $trim );
							
										$value = '<option value="' . $trim_safe . '"';
										if( rawurlencode( strtolower( $trim_safe ) ) == strtolower( $parameters[ 'trim' ] ) ) {
											$value .= ' selected="selected" ';
										}
										$value .= '>' . $trim . '</option>';
										echo $value;
									}
								}
							?>
						</select>
					</div>
				</div>
				<div id="cobra-quick-search">
					<input id="cobra-search-box" name="search" value="<?php echo isset( $parameters[ 'search' ] ) ? $parameters[ 'search' ] : NULL; ?>" />
					<div id="cobra-search-submit">GO</div>
				</div>
			</div>
			<div id="cobra-search-wrapper" > <!-- Search Wrapper -->
				<form action="#" method="POST" id="cobra-search"> <!-- Vehicle Search -->
					<input type="hidden" id="hidden-rewrite" value="<?php if ( isset($rules['^(inventory)']) ) { echo 'true'; } ?>" name="h_taxonomy" />
					<input type="hidden" id="hidden-saleclass" value="<?php echo ucwords( strtolower( $param_saleclass ) ) ?>" name="h_saleclass" />
					<div id="cobra-search-advance" style="display: none;">
						<div class="cobra-advance-peram">
							<label class="cobra-label" for="price-range">Price Range -</label><span>(From: 1000 To: 10000)</span><br>
							<label class="cobra-label" for="price_from">From:</label>
							<input id="cobra-price-from" name="price_from" value="<?php echo isset( $parameters[ 'price_from' ] ) ? $parameters[ 'price_from' ] : NULL; ?>" />
							<label class="cobra-label" for="price_to">To:</label>
							<input id="cobra-price-to" name="price_to" value="<?php echo isset( $parameters[ 'price_to' ] ) ? $parameters[ 'price_to' ] : NULL; ?>" />
						</div>
						<div class="cobra-advance-peram">
							<label class="cobra-label" for="year-range">Year -</label><span>(From: 2010 To: 2013)</span><br>
							<label class="cobra-label" for="year_from">From:</label>
							<input id="cobra-year-from" name="year_from" value="<?php echo isset( $parameters[ 'year_from' ] ) ? $parameters[ 'year_from' ] : NULL; ?>" />
							<label class="cobra-label" for="year_to">To:</label>
							<input id="cobra-year-to" name="year_to" value="<?php echo isset( $parameters[ 'year_to' ] ) ? $parameters[ 'year_to' ] : NULL; ?>" />
						</div>
						<div class="cobra-advance-peram">
							<label class="cobra-label" for="mileage-range">Odometer -</label><span>(From: 20000 To: 50000)</span><br>
							<label class="cobra-label" for="mileage_from">From:</label>
							<input id="cobra-mileage-from" name="mileage_from" value="<?php echo isset( $parameters[ 'mileage_from' ] ) ? $parameters[ 'mileage_from' ] : NULL; ?>" />
							<label class="cobra-label" for="mileage_to">To:</label>
							<input id="cobra-mileage-to" name="mileage_to" value="<?php echo isset( $parameters[ 'mileage_to' ] ) ? $parameters[ 'mileage_to' ] : NULL; ?>" />
						</div>
						<hr class="cobra-hr">
						<div class="cobra-advance-peram">
							<label class="cobra-label">Body Style: </label>
							<select id="cobra-vehicleclass" class="cobra-select">
								<option value="">All</option>
								<option value="car" <?php echo $vehicleclass == 'car' ? 'selected' : NULL; ?>>Car</option>
								<option value="truck" <?php echo $vehicleclass == 'truck' ? 'selected' : NULL; ?>>Truck</option>
								<option value="sport_utility" <?php echo $vehicleclass == 'sport_utility' ? 'selected' : NULL; ?>>SUV</option>
								<option value="van,minivan" <?php echo $vehicleclass == 'van,minivan' ? 'selected' : NULL; ?>>Van</option>
							</select>
						</div>
						<div class="cobra-advance-peram">
							<label class="cobra-label">Sale Class: </label>
							<select id="cobra-saleclass" class="cobra-select">
								<?php
									switch( $inventory_options['saleclass_filter'] ) {
										case 'all':
											echo '<option value="New" ' . (strtolower( $param_saleclass ) == 'new' ? 'selected' : NULL) . ' >New Vehicles</option>';
											echo '<option value="Used" ' . (strtolower( $param_saleclass ) == 'used' && empty( $filters['certified'] ) ? 'selected' : NULL) . ' >Pre-Owned Vehicles</option>';
											echo '<option value="Certified" ' . (strtolower( $param_saleclass ) == 'used' && !empty( $filters['certified'] ) ? 'selected' : NULL) . ' >Certified Pre-Owned</option>';
											break;
										case 'new':
											echo '<option value="New" selected >New Vehicles</option>';
											break;
										case 'used':
											echo '<option value="Used" ' . (strtolower( $param_saleclass ) == 'used' && empty( $filters['certified'] ) ? 'selected' : NULL) . ' >Pre-Owned Vehicles</option>';
											echo '<option value="Certified" ' . (strtolower( $param_saleclass ) == 'used' && !empty( $filters['certified'] ) ? 'selected' : NULL) . ' >Certified Pre-Owned</option>';
											break;
										case 'certified':
											echo '<option value="Certified" selected >Certified Pre-Owned</option>';
											break;
									}
								?>
							</select>
						</div>
						<div class="cobra-advance-peram">
							<div class="reset-search"><a href="<?php echo !empty($param_saleclass) ? '/inventory/' .$param_saleclass. '/' : '/inventory/'; ?>">Reset Search</a></div>
						</div>
					</div>
					<input id="search-form-submit" style="display: none;" type="submit" value="go" />
				</form>
				<div id="cobra-advance-show" name="hidden">
					Advanced Search
				</div>
			</div>
			<div id="cobra-total-found">Found <?php echo $vehicle_total_found; ?> Exact Matches</div>

			<div id="cobra-pager"><?php echo paginate_links( $args ); ?></div>

			<div id="cobra-sorting">Sort options:
				<a class="<?php echo $sort_year_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_year ) , $do_not_carry ); ?>">Year</a> /
				<a class="<?php echo $sort_price_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_price ) , $do_not_carry ); ?>">Price</a> /
				<a class="<?php echo $sort_mileage_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_mileage ) , $do_not_carry ); ?>">Mileage</a>
			</div>

			<div id="cobra-inventory-wrapper">

				<?php
					if( empty( $inventory ) ) {
						echo '<div class="not-found"><h2><strong>Unable to find inventory items that matched your search criteria.</strong></h2></div>';
					} else {
						foreach( $inventory as $inventory_item ) {
							$vehicle = itemize_vehicle($inventory_item);

							if( isset($rules['^(inventory)']) ) {
								$inventory_url = '/inventory/' . $vehicle['year'] . '/' . $vehicle['make']['clean'] . '/' . $vehicle['model']['clean'] . '/' . $state . '/' . $city . '/'. $vehicle['vin'] . '/';
							} else {
								$inventory_url = '?taxonomy=inventory&amp;year=' . $vehicle['year'] . '&amp;make=' . $vehicle['make']['clean'] . '&amp;model=' . $vehicle['model']['clean'] . '&amp;state=' . $state . '&amp;city=' . $city . '&amp;vin='. $vehicle['vin'];
							}

							$generic_vehicle_title = $vehicle['year'] . ' ' . $vehicle['make']['clean'] . ' ' . $vehicle['model']['clean'];

							echo'
							<div class="cobra-vehicle saleclass-'.strtolower($vehicle['saleclass']).'" id="' . $vehicle['vin'] . '">
								<div class="cobra-vehicle-left">
									<div class="photo">
										<a href="'.$inventory_url.'" title="' . $generic_vehicle_title . '">
											<img src="' . $vehicle['thumbnail'] . '" alt="' . $generic_vehicle_title . '" title="' . $generic_vehicle_title . '" />
										</a>
									</div>
								</div>
								<div class="cobra-vehicle-right">
									<div class="cobra-vehicle-top">
										'.( $vehicle['headline'] ? '<div class="cobra-list-headline">'.$vehicle['headline'].'</div>' : '' ).'
									</div>
									<div class="cobra-vehicle-inner-wrap">
										<div class="cobra-vehicle-inner-left">
											<div class="cobra-vehicle-title">
												<a href="' . $inventory_url . '" title="' . $generic_vehicle_title . '" class="title-details">
													<div>
														<span class="title-year">' . $vehicle['year'] . '</span>
														<span class="title-make">' . $vehicle['make']['name'] . '</span>
														<span class="title-model">' . $vehicle['model']['name'] . '</span>
													</div>
													<span class="title-trim">' . $vehicle['trim']['name'] . '&nbsp;</span>
												</a>
											</div>
											<div class="cobra-vehicle-identifier">
												Stock #: <span class="vehicle-stock">' . $vehicle['stock_number'] . '</span> - VIN: <span class="vehicle-vin">' . $vehicle['vin'] . '</span>
											</div>
											<div class="cobra-vehicle-extras">
												' . ( !empty($vehicle['exterior_color']) ? '<span class="exterior-color"> Exterior: '.$vehicle['exterior_color'].'</span>' : '')
												. ( !empty($vehicle['interior_color']) ? '<span class="interior-color"> Interior: '.$vehicle['interior_color'].'</span>' : '') . '
											</div>
										</div>
										<div class="cobra-vehicle-inner-right">
							';
										$price = get_price_display($vehicle['prices'], $company_information, $vehicle['vin'], 'cobra' );
							echo'
											<div class="cobra-price-wrap">
												' . ( !empty($price['msrp_text']) && strtolower($vehicle['saleclass']) == 'new' ? $price['msrp_text'] : '') . '
												'.$price['primary_text'].$price['ais_text'].$price['compare_text'].$price['expire_text'].$price['hidden_prices'].'
												'. ( !empty($price['ais_link']) ? $price['ais_link'] : '') .'
											</div>
											<div class="cobra-more-info"><div class="cobra-more-info-button"><a href="'.$inventory_url.'">More Info</a></div></div>

										</div>
									</div>
									<div class="cobra-vehicle-bottom">
										<div class="cobra-vehicle-dealer-info">
											<span class="vehicle-dealer-name-">'.$vehicle['contact_info']['dealer'].'</span> - <span class="vehicle-dealer-phone">'.$vehicle['contact_info']['phone'].'</span>
										</div>
							';
								if( $theme_settings['display_tags'] ){
									apply_special_tags( $vehicle['tags'], $vehicle['prices']['on_sale'], $vehicle['certified'], $vehicle['video']);
									if( !empty( $vehicle['tags'] ) ){
										echo '<div class="cobra-listing-tags">';
											$tag_icons = build_tag_icons( $default_tag_names, $custom_tag_icons, $vehicle['tags']);
											echo $tag_icons;
										echo '</div>';
									}
								}

								if( $vehicle['autocheck'] ){
									echo display_autocheck_image( $vehicle['vin'], $vehicle['saleclass'], $type );
								}
							echo'
									</div>
								</div>
							</div>
							';
						}
					}
				?>
			</div>
			<div id="cobra-pager"><?php echo paginate_links( $args ); ?></div>
		</div>
	</div>

