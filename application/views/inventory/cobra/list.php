<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

	$vehicle_management_system->tracer = 'Calculating how many items were returned with the given parameters.';
	$vehicle_total_found = $vehicle_management_system->get_inventory()->please( array_merge( $parameters , array( 'per_page' => 1 , 'photo_view' => 1 , 'make_filters' =>  $inventory_options['make_filter'] ) ) );
	$vehicle_total_found = ( isset($vehicle_total_found[ 'body' ]) ) ? json_decode( $vehicle_total_found[ 'body' ] ) : NULL;
	$vehicle_total_found = is_array( $vehicle_total_found ) && count( $vehicle_total_found ) > 0 ? $vehicle_total_found[ 0 ]->pagination->total : 0;

	$do_not_carry = remove_query_arg( 'page' , $query );
	$tmp_do_not_carry = remove_query_arg( 'certified' , $do_not_carry );

	$all_link = ( isset($rules['^(inventory)']) ) ? '/inventory/' : add_query_arg( array('taxonomy' => 'inventory'), $tmp_do_not_carry );	
	$new_link = ( isset($rules['^(inventory)']) ) ? '/inventory/New/' : add_query_arg( array('saleclass' => 'New'), $tmp_do_not_carry );
	$used_link = ( isset($rules['^(inventory)']) ) ? '/inventory/Used/' : add_query_arg( array('saleclass' => 'Used') );
	$cert_link = ( isset($rules['^(inventory)']) ) ? add_query_arg('certified', 'yes', '/inventory/Used/') : add_query_arg( array('saleclass' => 'Used', 'certified' => 'yes') );

	$filters = array(
		'vehicleclass' => isset( $this->parameters[ 'vehicleclass' ] ) ? $this->parameters[ 'vehicleclass' ] : NULL,
		'price_to' => isset( $this->parameters[ 'price_to' ] ) ? $this->parameters[ 'price_to' ] : NULL,
		'price_from' => isset( $this->parameters[ 'price_from' ] ) ? $this->parameters[ 'price_from' ] : NULL,
		'certified' => isset( $this->parameters[ 'certified' ] ) ? $this->parameters[ 'certified' ] : NULL
	);

	$vehicle_management_system->tracer = 'Obtaining a list of makes.';
	if( empty($geo_params) || (count($dealer_geo) == 1 && $geo_params['key'] == 'state') ){
		if ( strcasecmp( $parameters[ 'saleclass' ], 'new') == 0 && !empty( $inventory_options['make_filter'] ) ) { //Get Makes
			$makes = $inventory_options['make_filter'];
		} else {
			$makes = $vehicle_management_system->get_makes()->please( array_merge( array( 'saleclass' => $parameters[ 'saleclass' ] ) , $filters ) );
			$makes = json_decode( $makes[ 'body' ] );
		}
	} else {
		$geo_makes = $vehicle_management_system->get_geo_dealer_mmt('makes',$parameters['dealer_id'], array_merge( array( 'saleclass' => $parameters[ 'saleclass' ] ) , $filters));
		natcasesort($geo_makes);
		$makes = $geo_makes;
	}
	$make_count = count($makes);

	if( isset( $parameters[ 'make' ] ) && $parameters[ 'make' ] != 'all' ) { //Get Models
		if( empty($geo_params) || (count($dealer_geo) == 1 && $geo_params['key'] == 'state') ){
			$vehicle_management_system->tracer = 'Obtaining a list of models.';
			$tmp_do_not_carry = remove_query_arg( 'make' , $do_not_carry );
			$models = $vehicle_management_system->get_models()->please( array_merge( array('saleclass'=>$parameters[ 'saleclass' ],'make'=>$parameters[ 'make' ]),$filters));
			$models = json_decode( $models[ 'body' ] );
			if( !in_array( rawurldecode($parameters[ 'model' ]), $models ) && !empty($parameters[ 'model' ]) ){
				$search_error = 'The current model('.$parameters[ 'model' ].') could not be found with current search parameters. Reset search or adjust search parameters. ';
			}
		} else {
			$geo_models = $vehicle_management_system->get_geo_dealer_mmt('models',$parameters['dealer_id'], array_merge( array('saleclass'=>$parameters[ 'saleclass' ],'make'=>$parameters[ 'make' ]),$filters));
			natcasesort($geo_models);
			$models = $geo_models;
		}
		$model_count = count($models);
		$model = isset( $parameters[ 'model' ] ) ? $parameters[ 'model' ] : 'all';
		$parameters[ 'model' ] = $model;
		$model_text = 'All Models';
	} else {
		$model_text = 'Select a Make';
	}

	if( isset( $parameters[ 'model' ] ) && $parameters[ 'model' ] != 'all' ) { //Get Trims
		if( empty($geo_params) || (count($dealer_geo) == 1 && $geo_params['key'] == 'state') ){
			$vehicle_management_system->tracer = 'Obtaining a list of trims.';
			$tmp_do_not_carry = remove_query_arg( array( 'make' , 'model' ) , $do_not_carry );
			$trims = $vehicle_management_system->get_trims()->please( array_merge( array( 'saleclass' => $parameters[ 'saleclass' ] , 'make' => $parameters[ 'make' ] , 'model' => $parameters[ 'model' ] ) , $filters ) );
			$trims = json_decode( $trims[ 'body' ] );
			if( !in_array( rawurldecode($parameters[ 'trim' ]), $trims ) && !empty( $parameters[ 'trim' ] ) ){
				$search_error = 'The current trim('.$parameters[ 'trim' ].') could not be found with current search parameters. Reset search or adjust search parameters. ';
			}
		} else {
			$geo_trims = $vehicle_management_system->get_geo_dealer_mmt('trims',$parameters['dealer_id'], array_merge( array('saleclass'=>$parameters[ 'saleclass' ],'make'=>$parameters[ 'make' ],'model'=>$parameters[ 'model' ]),$filters));
			natcasesort($geo_trims);
			$trims = $geo_trims;
		}
		$trim_count = count($trims);
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
			<div class="breadcrumbs"><?php echo display_breadcrumb( $parameters, $company_information, $inventory_options ); ?></div>
			<div id="cobra-quick-links">
				<div id="cobra-quick-selects">
					<div class="quick-link-wrap">
						<label class="cobra-label">Makes:</label>
						<select onchange="document.location = this.value;" class="cobra-select cobra-select-makes">
							<option value="<?php echo generate_inventory_link($url_rule,$parameters,'',array('make','model','trim')); ?>">All Makes</option>
							<?php
							$shown_makes = array();
							foreach( $makes as $make ) {
								$make_safe = str_replace( '/' , '%2F' , $make );
								$make_safe = ucwords( strtolower( $make_safe ) );
								if( ! in_array( $make_safe , $shown_makes ) ) {
									$shown_makes[] = $make_safe;
									$link = generate_inventory_link($url_rule,$parameters,array('make'=>$make_safe),array('model','trim'));
									echo '<option value="'.$link.'" '.(rawurlencode(strtolower($make_safe)) == strtolower($parameters['make']) ? 'selected': '').' >'.$make.'</option>';
								}
							}
							?>
						</select>
					</div>
					<div class="quick-link-wrap">
						<label class="cobra-label">Models:</label>
						<select onchange="document.location = this.value;" class="cobra-select cobra-select-models"<?php if( ! isset( $model_count ) || $model_count == 0 ) { echo 'readonly'; } ?>>
							<option value="<?php echo generate_inventory_link($url_rule,$parameters,'',array('model','trim')); ?>"><?php echo $model_text; ?></option>
							<?php
							if( $model_count > 0 ) {
								if( $model_count == 1 ) {
									$parameters[ 'model' ] = rawurlencode( $models[ 0 ] );
								}

								foreach( $models as $model ) {
									$model_safe = str_replace( '/' , '%2F' , $model );
									$link = generate_inventory_link($url_rule,$parameters,array('model'=>$model_safe),array('trim'));
									echo '<option value="'.$link.'" '.(rawurlencode(strtolower($model_safe)) == strtolower($parameters['model']) ? 'selected': '').' >'.$model.'</option>';
								}
							}
							?>
						</select>
					</div>
					<div class="quick-link-wrap">
						<label class="cobra-label">Trims:</label>
						<select onchange="document.location = this.value;" class="cobra-select cobra-select-trims"<?php if( ! isset( $trim_count ) || $trim_count == 0 ) { echo 'readonly'; } ?>>
							<option value="<?php echo remove_query_arg( array('trim') ); ?>"><?php echo $trim_text; ?></option>
							<?php
							if( isset( $trim_count ) && $trim_count != 0 ) {
								if( $trim_count == 1 ) {
									$parameters['trim'] = $trims[ 0 ];
								}
								foreach( $trims as $trim ) {
									$trim_safe = str_replace( '/' , '%2F' , $trim );
									echo '<option value="'.add_query_arg(array('trim' => urlencode($trim_safe))).'" '.(rawurlencode(strtolower($trim_safe)) == strtolower($parameters['trim']) ? 'selected': '').'> '.$trim.'</option>';
								}
							}
							?>
						</select>
					</div>
				</div>
				<div id="cobra-quick-search">
					<input id="cobra-search-box" class="list-search-value" name="search" value="<?php echo isset( $parameters[ 'search' ] ) ? $parameters[ 'search' ] : NULL; ?>" />
					<button onclick="return get_list_input_values(event);" id="cobra-search-submit">GO</button>
				</div>
			</div>
			<div id="cobra-search-wrapper" > <!-- Search Wrapper -->
					<div id="cobra-search-advance" style="display: none;">
						<div class="cobra-advance-param">
							<label class="cobra-label" for="price-range">Price Range -</label><span>(From: 1000 To: 10000)</span><br>
							<label class="cobra-label" for="price_from">From:</label>
							<input id="cobra-price-from" class="list-search-value" name="price_from" value="<?php echo isset( $parameters[ 'price_from' ] ) ? $parameters[ 'price_from' ] : NULL; ?>" />
							<label class="cobra-label" for="price_to">To:</label>
							<input id="cobra-price-to" class="list-search-value" name="price_to" value="<?php echo isset( $parameters[ 'price_to' ] ) ? $parameters[ 'price_to' ] : NULL; ?>" />
						</div>
						<div class="cobra-advance-param">
							<label class="cobra-label" for="year-range">Year -</label><span>(From: 2010 To: 2013)</span><br>
							<label class="cobra-label" for="year_from">From:</label>
							<input id="cobra-year-from" class="list-search-value" name="year_from" value="<?php echo isset( $parameters[ 'year_from' ] ) ? $parameters[ 'year_from' ] : NULL; ?>" />
							<label class="cobra-label" for="year_to">To:</label>
							<input id="cobra-year-to" class="list-search-value" name="year_to" value="<?php echo isset( $parameters[ 'year_to' ] ) ? $parameters[ 'year_to' ] : NULL; ?>" />
						</div>
						<div class="cobra-advance-param">
							<label class="cobra-label" for="mileage-range">Odometer -</label><span>(From: 20000 To: 50000)</span><br>
							<label class="cobra-label" for="mileage_from">From:</label>
							<input id="cobra-mileage-from" class="list-search-value" name="mileage_from" value="<?php echo isset( $parameters[ 'mileage_from' ] ) ? $parameters[ 'mileage_from' ] : NULL; ?>" />
							<label class="cobra-label" for="mileage_to">To:</label>
							<input id="cobra-mileage-to" class="list-search-value" name="mileage_to" value="<?php echo isset( $parameters[ 'mileage_to' ] ) ? $parameters[ 'mileage_to' ] : NULL; ?>" />
						</div>
						<hr class="cobra-hr">
						<div class="cobra-advance-param">
							<label class="cobra-label">Vehicle Class: </label>
							<select id="cobra-vehicleclass" class="cobra-select" onchange="document.location = this.value;">
							<option value="<?php echo remove_query_arg('vehicleclass'); ?>">All</option>
							<option value="<?php echo add_query_arg(array('vehicleclass'=>'car')); ?>" <?php echo $vehicleclass == 'car' ? 'selected' : NULL; ?>>Car</option>
							<option value="<?php echo add_query_arg(array('vehicleclass'=>'truck')); ?>" <?php echo $vehicleclass == 'truck' ? 'selected' : NULL; ?>>Truck</option>
							<option value="<?php echo add_query_arg(array('vehicleclass'=>'sport_utility')); ?>" <?php echo $vehicleclass == 'sport_utility' ? 'selected' : NULL; ?>>SUV</option>
							<option value="<?php echo add_query_arg(array('vehicleclass'=>'van,minivan')); ?>" <?php echo $vehicleclass == 'van,minivan' ? 'selected' : NULL; ?>>Van</option>
							</select>
						</div>
						<div class="cobra-advance-param">
							<label class="cobra-label">Sale Class: </label>
							<select id="cobra-saleclass" class="cobra-select" onchange="document.location = this.value;">
								<?php
								switch( $inventory_options['saleclass_filter'] ) {
									case 'all':
										echo '<option value="'.$all_link.'" '.(strtolower( $parameters[ 'saleclass' ] ) == 'all' ? 'selected' : NULL) .' >All Vehicles</option>';
										echo '<option value="'.$new_link.'" '.(strtolower( $parameters[ 'saleclass' ] ) == 'new' ? 'selected' : NULL) .' >New Vehicles</option>';
										echo '<option value="'.$used_link.'" '.(strtolower( $parameters[ 'saleclass' ] ) == 'used' && empty( $certified ) ? 'selected' : NULL) . ' >Pre-Owned Vehicles</option>';
										echo '<option value="'.$cert_link.'" '.(strtolower( $parameters[ 'saleclass' ] ) == 'used' && !empty( $certified ) ? 'selected' : NULL) . ' >Certified Pre-Owned</option>';
										break;
									case 'new':
										echo '<option value="'.$new_link.'" selected >New Vehicles</option>';
										break;
									case 'used':
										echo '<option value="'.$used_link.'" ' . (strtolower( $parameters[ 'saleclass' ] ) == 'used' && empty( $certified ) ? 'selected' : NULL) . ' >Pre-Owned Vehicles</option>';
										echo '<option value="'.$cert_link.'" ' . (strtolower( $parameters[ 'saleclass' ] ) == 'used' && !empty( $certified ) ? 'selected' : NULL) . ' >Certified Pre-Owned</option>';
										break;
									case 'certified':
										echo '<option value="'.$cert_link.'" selected >Certified Pre-Owned</option>';
										break;
								}
								?>
							</select>
						</div>
						<div class="cobra-advance-param">
							<div class="reset-search"><a href="<?php echo !empty($parameters[ 'saleclass' ]) ? '/inventory/' .$parameters[ 'saleclass' ]. '/' : '/inventory/'; ?>">Reset Search</a></div>
						</div>
						<?php if($theme_settings['display_geo']) { ?>
							<div id="geo-wrapper" class="cobra-advance-param">
								<label class="cobra-label">Vehicle Location: </label>
								<?php 
									$geo_output = build_geo_dropdown($dealer_geo, $geo_params, $theme_settings['add_geo_zip']);
									//echo !empty( $geo_output['search'] ) ? $geo_output['search'] : ''; 
									echo !empty( $geo_output['dropdown'] ) ? $geo_output['dropdown'] : '';
									//echo !empty( $geo_output['back_link'] ) ? $geo_output['back_link'] : '';
								?>
							</div>
						<?php } ?>
					</div>
				<div id="cobra-advance-show" name="hidden">
					Advanced Search
				</div>
			</div>
			<div id="cobra-total-found">Found <?php echo $vehicle_total_found; ?> Exact Matches <?php echo !empty( $geo_output['search']) ? '<br/>in '.$geo_output['search'] : ''; ?></div>

			<div class="cobra-pager"><?php echo paginate_links( $args ); ?></div>

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
											'.($vehicle['sold'] ? '<img class="marked-sold-overlay" src="http://assets.s3.dealertrend.com.s3.amazonaws.com/images/sold_overlay.png" />' : '').'
											<img class="list-image" src="' . $vehicle['thumbnail'] . '" alt="' . $generic_vehicle_title . '" title="' . $generic_vehicle_title . '" />
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
												Stock #: <span class="vehicle-stock">' . $vehicle['stock_number'] . '</span> | VIN: <span class="vehicle-vin">' . $vehicle['vin'] . '</span>
											</div>
											<div class="cobra-vehicle-extras">
												' . ( !empty($vehicle['exterior_color']) ? '<span class="exterior-color"> Exterior: '.$vehicle['exterior_color'].'</span>' : '')
												. ( !empty($vehicle['interior_color']) ? '<span class="interior-color"> Interior: '.$vehicle['interior_color'].'</span>' : '') . '
											</div>
										</div>
										<div class="cobra-vehicle-inner-right">
							';
										$price = get_price_display($vehicle['prices'], $company_information, $vehicle['saleclass'], $vehicle['vin'], 'cobra', $price_text );
							echo'
											<div class="cobra-price-wrap">
												' . ( !empty($price['msrp_text']) && strtolower($vehicle['saleclass']) == 'new' ? $price['msrp_text'] : '') . '
												'.$price['primary_text'].$price['ais_text'].$price['compare_text'].$price['expire_text'].$price['hidden_prices'].'
												'. ( !empty($price['ais_link']) ? $price['ais_link'] : '') .'
											</div>
											<div class="cobra-more-info"><div class="cobra-more-info-button"><a href="'.$inventory_url.'">More Info</a></div></div>

										</div>
									</div>
									<div class="cobra-vehicle-bottom">'.get_dealer_contact_info( $vehicle['contact_info'], $inventory_options, $vehicle['saleclass'] );
								
								if( $theme_settings['display_tags'] ){
									apply_special_tags( $vehicle['tags'], $vehicle['prices']['on_sale'], $vehicle['certified'], $vehicle['video']);
									if( !empty( $vehicle['tags'] ) ){
										echo '<div class="cobra-listing-tags">';
											$tag_icons = build_tag_icons( $default_tag_names, $custom_tag_icons, $vehicle['tags'], $vehicle['vin']);
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
			<div class="cobra-pager"><?php echo paginate_links( $args ); ?></div>
			<?php
			if ( is_active_sidebar( 'vehicle-listing-page' ) ) :
				echo '<div id="list-widget-area">';
					dynamic_sidebar( 'vehicle-listing-page' );
				echo '</div>';
			endif;
			?>
		</div>
	</div>

