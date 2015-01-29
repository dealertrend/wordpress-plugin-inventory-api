<?php
namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

	$vehicle_management_system->tracer = 'Calculating how many items were returned with the given parameters.';
	$vehicle_total_found = $vehicle_management_system->get_inventory()->please( array_merge( $parameters , array( 'per_page' => 1 , 'photo_view' => 1 , 'make_filters' =>  $inventory_options['make_filter'] ) ) );
	$vehicle_total_found = ( isset($vehicle_total_found[ 'body' ]) ) ? json_decode( $vehicle_total_found[ 'body' ] ) : NULL;
	$vehicle_total_found = is_array( $vehicle_total_found ) && count( $vehicle_total_found ) > 0 ? $vehicle_total_found[ 0 ]->pagination->total : 0;

	$do_not_carry = remove_query_arg( 'page' , $query );
	$tmp_do_not_carry = remove_query_arg( 'certified' , $do_not_carry );

	$new_link = ( isset($rules['^(inventory)']) ) ? '/inventory/New/' : add_query_arg( array('saleclass' => 'New'), $tmp_do_not_carry );
	$used_link = ( isset($rules['^(inventory)']) ) ? '/inventory/Used/' : add_query_arg( array('saleclass' => 'Used') );
	$cert_link = ( isset($rules['^(inventory)']) ) ? add_query_arg('certified', 'yes', '/inventory/Used/') : add_query_arg( array('saleclass' => 'Used', 'certified' => 'yes') );

	//echo generate_inventory_link( $url_rule, $parameters );

	$filters = array(
		'vehicleclass' => isset( $parameters[ 'vehicleclass' ] ) ? $parameters[ 'vehicleclass' ] : NULL,
		'price_to' => isset( $parameters[ 'price_to' ] ) ? $parameters[ 'price_to' ] : NULL,
		'price_from' => isset( $parameters[ 'price_from' ] ) ? $parameters[ 'price_from' ] : NULL,
		'certified' => isset( $parameters[ 'certified' ] ) ? $parameters[ 'certified' ] : NULL,
		'search' => isset( $parameters[ 'search' ] ) ? $parameters[ 'search' ] : NULL,
		'year_to' => isset( $parameters[ 'year_to' ] ) ? $parameters[ 'year_to' ] : NULL,
		'year_from' => isset( $parameters[ 'year_from' ] ) ? $parameters[ 'year_from' ] : NULL,
		'mileage_to' => isset( $parameters[ 'mileage_to' ] ) ? $parameters[ 'mileage_to' ] : NULL,
		'mileage_from' => isset( $parameters[ 'mileage_from' ] ) ? $parameters[ 'mileage_from' ] : NULL
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
		case 'make_asc': $sort_make_class = 'asc'; break;
		case 'make_desc': $sort_make_class = 'desc'; break;
		default: $sort_year_class = $sort_price_class = $sort_mileage_class = null; break;
	}
	$sort_year = $sort != 'year_asc' ? 'year_asc' : 'year_desc';
	$sort_mileage = $sort != 'mileage_asc' ? 'mileage_asc' : 'mileage_desc';
	$sort_price = $sort != 'price_asc' ? 'price_asc' : 'price_desc';
	$sort_make = $sort != 'make_asc' ? 'make_asc' : 'make_desc';

	$traffic_source = isset( $_COOKIE[ 'dealertrend-traffic-source' ] ) ? $_COOKIE[ 'dealertrend-traffic-source' ] : false;
	$traffic_source = $this->sanitize_inputs( $traffic_source );

?>
<div id="eagle-wrapper">
	<div id="eagle-listing">
		<div id="eagle-top"> <!-- Eagle Top -->
			<div class="eagle-breadcrumbs">
			<?php echo display_breadcrumb( $parameters, $company_information, $inventory_options ); ?>
			</div>
			<div id="eagle-top-search">
				<div class="eagle-search">
					<form action="#" method="GET" id="eagle-search">
						<input id="eagle-search-box" name="search" value="<?php echo isset( $parameters[ 'search' ] ) ? $parameters[ 'search' ] : NULL; ?>" />
						<button id="eagle-search-submit">SEARCH</button>
					</form>
				</div>
				<div class="eagle-pager">
					<span>Page:</span><?php echo paginate_links( $args ); ?>
				</div>
			</div>
		</div>

		<div id="eagle-content">  <!-- Eagle Content -->
			<div id="eagle-content-top"> <!-- Eagle Content Top -->
				<div id="eagle-total-found"><span><?php echo $vehicle_total_found; ?></span> Vehicles Found</div>
				<div id="eagle-sorting-columns">
					<div>Sort by: </div>
					<div class="eagle-sorting-divider"><a class="<?php echo $sort_year_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_year ) , $do_not_carry ); ?>">Year</a></div>
					<div class="eagle-sorting-divider"><a class="<?php echo $sort_price_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_price ) , $do_not_carry ); ?>">Price</a></div>
					<div class="eagle-sorting-divider"><a class="<?php echo $sort_mileage_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_mileage ) , $do_not_carry ); ?>">Mileage</a></div>
					<div class="eagle-sorting-divider"><a class="<?php echo $sort_make_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_make ) , $do_not_carry ); ?>">Make</a></div>
				</div>
			</div>
			<div id="eagle-content-center"> <!-- Eagle Content Center -->
				<div id="eagle-mobile-search-wrap" class="inactive"><div id="eagle-mobile-search-img"></div><div id="eagle-mobile-search-text">Search</div></div>
				<div id="eagle-content-left"> <!-- Eagle Content Left -->
					<div id="eagle-content-left-wrapper">
						<div class="eagle-sidebar sidebar-new-used">
							<h3>Search New and Used:</h3>
							<div class="eagle-sidebar-content content-new-used">
								<h4 class="" name="condition">Condition</h4>
								<ul>
								<?php switch( $inventory_options['saleclass_filter'] ) {
									case 'all':
										echo '<li><a href="'.$new_link.'" title="View New Inventory" class="'.(strtolower($parameters[ 'saleclass' ]) == 'new'?'disabled': NULL).'">New</a></li>';
										echo '<li><a href="'.$used_link.'" title="View Used Inventory" class="'.(strtolower($parameters[ 'saleclass' ]) == 'used'?'disabled': NULL).'">Used</a></li>';
										echo '<li><a href="'.$cert_link.'" title="View Certified Used Inventory" class="'.(strtolower($parameters[ 'saleclass' ]) == 'used'?'disabled':NULL).'">Certified</a></li>';
										break;
									case 'new':
										echo '<li><a href="'.$new_link.'" title="View New Inventory" class="'.(strtolower($parameters[ 'saleclass' ]) == 'new'?'disabled':NULL).'">New</a></li>';
										break;
									case 'used':
										echo '<li><a href="'.$used_link.'" title="View Used Inventory" class="'.(strtolower($parameters[ 'saleclass' ]) == 'used'?'disabled':NULL).'">Used</a></li>';
										echo '<li><a href="'.$cert_link.'" title="View Certified Used Inventory" class="'.(strtolower($parameters[ 'saleclass' ]) == 'used'?'disabled':NULL).'">Certified</a></li>';
										break;
									case 'certified':
										echo '<li><a href="'.$cert_link.'" title="View Certified Used Inventory" class="'.(strtolower($parameters[ 'saleclass' ]) == 'used'?'disabled': NULL).'">Certified</a></li>';
										break;
								}
								?>
								</ul>
							</div>
						</div>
						<div class="eagle-sidebar sidebar-refine-search">
							<h3>Refine Your Search By:</h3>
							<?php if($theme_settings['display_geo']) { ?>
									
									<div id="list-geo-filter" class="eagle-sidebar-content content-geo">
										<h4>Vehicle Location</h4>
										<div id="geo-wrapper">
											<?php 
												$geo_output = build_geo_dropdown($dealer_geo, $geo_params, $theme_settings['add_geo_zip']);
												echo !empty( $geo_output['search'] ) ? $geo_output['search'] : ''; 
												echo !empty( $geo_output['dropdown'] ) ? $geo_output['dropdown'] : '';
												echo !empty( $geo_output['back_link'] ) ? $geo_output['back_link'] : '';
											?>
										</div>
									</div>	
							<?php } ?>
							
							<div class="eagle-sidebar-content content-bodystyle">
								<h4 class="" name="styles">Vehicle Class</h4>
								<ul>
									<li><a href="<?php echo generate_inventory_link($url_rule,$parameters,array('vehicleclass'=>'car')); ?>" <?php echo $parameters[ 'vehicleclass' ] == 'car' ? 'class="active"' : NULL; ?>>Car</a></li>
									<li><a href="<?php echo generate_inventory_link($url_rule,$parameters,array('vehicleclass'=>'truck')); ?>" <?php echo $parameters[ 'vehicleclass' ] == 'truck' ? 'class="active"' : NULL; ?>>Truck</a></li>
									<li><a href="<?php echo generate_inventory_link($url_rule,$parameters,array('vehicleclass'=>'sport_utility')); ?>" <?php echo $parameters[ 'vehicleclass' ] == 'sport_utility' ? 'class="active"' : NULL; ?>>SUV</a></li>
									<li><a href="<?php echo generate_inventory_link($url_rule,$parameters,array('vehicleclass'=>'van,minivan')); ?>" <?php echo $parameters[ 'vehicleclass' ] == 'van,minivan' ? 'class="active"' : NULL; ?>>Van</a></li>
								</ul>
							</div>

							<div class="eagle-sidebar-content content-make-model-trim">
								<?php
									if ( $trim_count != 0 ) {
										$sidebar_content = '<h4 class="" name="vehicles">Trims</h4>';
										$sidebar_content .= '<ul>';
										foreach( $trims as $trim ) {
											$trim_safe = str_replace( '/' , '%2F' , $trim );
											$link = generate_inventory_link($url_rule,$parameters,array('trim'=>$trim_safe));
											$sidebar_content .= '<li><a href="'.$link.'">'.$trim.'</a></li>';
										}
										$back_link = generate_inventory_link($url_rule,$parameters,'',array('model','trim'));
										$sidebar_content .= '<li><span class="no-style"><a href="'.$back_link.'" class="eagle-filter-prev" title="View '.$parameters['make'].' Models">&#60; View '. $parameters[ 'make' ].'</a></span></li>';
										$sidebar_content .= '</ul>';

									} else if ( $model_count != 0) {
										$sidebar_content = '<h4 class="" name="vehicles">Models</h4>';
										$sidebar_content .= '<ul>';
										foreach( $models as $model ) {
											$model_safe = str_replace( '/' , '%2F' , $model );
											$link = generate_inventory_link($url_rule,$parameters,array('model'=>$model_safe));
											$sidebar_content .= '<li><a href="'.$link.'">'.$model.'</a></li>';
										}
										$back_link = generate_inventory_link($url_rule,$parameters,'',array('make','model'));
										$sidebar_content .= '<li><span class="no-style"><a href="'.$back_link.'" class="eagle-filter-prev" title="View '.$parameters[ 'saleclass' ].' Vehicles">&#60; All '.$parameters[ 'saleclass' ].' Vehicles</a></span></li>';
										$sidebar_content .= '</ul>';
									} else if ( $make_count != 0) {
										$sidebar_content = '<h4 class="" name="vehicles">Makes</h4>';
										$sidebar_content .= '<ul>';
											foreach( $makes as $make ) {
												$make_safe = str_replace( '/' , '%2F' , $make );
												$link = generate_inventory_link($url_rule,$parameters,array('make'=>$make_safe));
												$sidebar_content .= '<li><a href="'.$link.'">'.$make.'</a></li>';
											}
										$sidebar_content .= '</ul>';
									} else {
										$sidebar_content = '<h3>No Makes Found</h3>';
									}
									echo $sidebar_content;
								?>
							</div>
							<div class="eagle-sidebar-content content-price-range">
								<h4 class="" name="price">Price Range</h4>
								<ul>
									<li><a rel="nofollow" href="<?php echo generate_inventory_link($url_rule,$parameters,array('price_from'=>'1', 'price_to'=>'10000'));?>" <?php echo $parameters['price_from'] == "1" ? 'class="active"' : NULL; ?>>$1 &#38; $10,000</a></li>
									<li><a rel="nofollow" href="<?php echo generate_inventory_link($url_rule,$parameters,array('price_from'=>'10001', 'price_to'=>'20000'));?>" <?php echo $parameters['price_from'] == 10001 ? 'class="active"' : NULL; ?>>$10,001 &#38; $20,000</a></li>
									<li><a rel="nofollow" href="<?php echo generate_inventory_link($url_rule,$parameters,array('price_from'=>'20001', 'price_to'=>'30000')); ?>" <?php echo $parameters['price_from'] == 20001 ? 'class="active"' : NULL; ?>>$20,001 &#38; $30,000</a></li>
									<li><a rel="nofollow" href="<?php echo generate_inventory_link($url_rule,$parameters,array('price_from'=>'30001', 'price_to'=>'40000')); ?>" <?php echo $parameters['price_from'] == 30001 ? 'class="active"' : NULL; ?>>$30,001 &#38; $40,000</a></li>
									<li><a rel="nofollow" href="<?php echo generate_inventory_link($url_rule,$parameters,array('price_from'=>'40001', 'price_to'=>'50000')); ?>" <?php echo $parameters['price_from'] == 40001 ? 'class="active"' : NULL; ?>>$40,001 &#38; $50,000</a></li>
									<li><a rel="nofollow" href="<?php echo generate_inventory_link($url_rule,$parameters,array('price_from'=>'50001')); ?>" <?php echo $parameters['price_from'] == 50001 ? 'class="active"' : NULL; ?>>$50,001 &#38; &amp; Above</a></li>
								</ul>
							</div>
						</div>
					</div>
				</div>

				<div id="eagle-content-right"> <!-- Eagle Content Right -->
					<div id="eagle-vehicle-listings">
						<?php
							if( empty( $inventory ) ) {
								echo '<div class="eagle-not-found"><h2><strong>Unable to find inventory items that matched your search criteria.</strong></h2><a onClick="history.go(-1)" title="Return to Previous Search" class="jquery-ui-button">Return to Previous Search</a></div>';
							} else {
								foreach( $inventory as $inventory_item ):
									$vehicle = itemize_vehicle($inventory_item);
									$form_subject = $year . ' ' . $make . ' ' . $model . ' ' . $stock_number;
									$form_submit_url = $temp_host . '/' . $this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] . '/forms/create/';
									$link_params = array( 'year' => $vehicle['year'], 'make' => $vehicle['make']['name'],  'model' => $vehicle['model']['name'], 'state' => $state, 'city' => $city, 'vin' => $vehicle['vin'] );
									//$link = get_inventory_link( $rules['^(inventory)'], $link_params, 1);
									$link = generate_inventory_link($url_rule,$link_params,'','',1);
									$contact_information = $inventory_item->contact_info;
									$generic_vehicle_title = $vehicle['year'].' '.$vehicle['make']['name'].' '.$vehicle['model']['name'];

									?>
									<div class="eagle-vehicle" id="<?php echo $vehicle['vin']; ?>">
										<div class="eagle-listing-top"> <!-- Eagle Listing Top -->
											<?php
												if( !empty($custom_settings['display_headlines']) ){
													echo '<div class="eagle-listing-vehicle-headline">' . $vehicle['headline'] . '</div>';
												}
											?>
											<div class="eagle-column-left">
												<div class="eagle-main-line">
													<a href="<?php echo $link; ?>" title="<?php echo $generic_vehicle_title; ?>" class="details">
														<span class="eagle-year"><?php echo $vehicle['year']; ?></span>
														<span class="eagle-make"><?php echo $vehicle['make']['name']; ?></span>
														<span class="eagle-model"><?php echo $vehicle['model']['name']; ?></span>
														<span class="eagle-trim"><?php echo $vehicle['trim']['name']; ?></span>
														<span class="eagle-drive-train"><?php echo $vehicle['drive_train']; ?></span>
														<span class="eagle-body-style"><?php echo $vehicle['body_style']; ?></span>
														<span class="eagle-saleclass" style="display: none;"><?php echo $vehicle['saleclass']; ?></span>
													</a>
												</div>
												<div class="eagle-photo">
													<a href="<?php echo $link; ?>" title="<?php echo $generic_vehicle_title; ?>">
														<?php echo $vehicle['sold'] ? '<img class="marked-sold-overlay" src="http://assets.s3.dealertrend.com.s3.amazonaws.com/images/sold_overlay.png" />' : '' ?>
														<img class="list-image" src="<?php echo $vehicle['thumbnail']; ?>" alt="<?php echo $generic_vehicle_title; ?>" title="<?php echo $generic_vehicle_title; ?>" />
													</a>
												</div>
												<div class="eagle-listing-info">
													<?php
														if( $vehicle['prices']['retail_price'] > 0 && strtolower( $vehicle['saleclass'] ) == 'new' ) {
															echo '<div class="eagle-msrp" alt="'.$vehicle['prices']['retail_price'].'"><span>MSRP:</span> '.'$'.number_format( $vehicle['prices']['retail_price'] , 0 , '.' , ',' ).'</div>';
														}
														if ( $vehicle['odometer'] > 100 ) {
															echo '<div class="eagle-odometer"><span>Mileage:</span> ' . $vehicle['odometer'] . '</div>';
														}
														echo $vehicle['exterior_color'] != NULL ? '<div class="eagle-exterior-color"><span>Exterior:</span> '.$vehicle['exterior_color']. '</div>' : NULL;
														echo $vehicle['interior_color'] != NULL ? '<div class="eagle-interior-color"><span>Interior:</span> '.$vehicle['interior_color']. '</div>' : NULL;
														echo $vehicle['transmission'] != NULL ? '<div class="eagle-transmission"><span>Transmission:</span> '.$vehicle['transmission']. '</div>' : NULL;
													?>
												</div>
											</div>
											<div class="eagle-column-right">
												<div class="eagle-price">
													<?php
													$price = get_price_display($vehicle['prices'], $company_information, $vehicle['saleclass'], $vehicle['vin'], 'eagle', $price_text );
													echo (!empty($price['ais_link'])) ? $price['ais_link'] : '';
													echo $price['compare_text'].$price['ais_text'].$price['primary_text'].$price['expire_text'].$price['hidden_prices'];
													?>
												</div>
												<div class="eagle-detail-button eagle-show-form" name="<?php echo ( !empty($theme_settings['list_info_button']) ) ? $theme_settings['list_info_button'] : 'Get Your ePrice'; ?>">
													<a><?php echo ( !empty($theme_settings['list_info_button']) ) ? $theme_settings['list_info_button'] : 'GET YOUR ePrice'; ?></a>
												</div>
												<div class="eagle-hidden-form-values" style="display: none;">
													<span class="eagle-hidden-form-value eagle-form-price"><?php echo $price['primary_price']; ?></span>
													<span class="eagle-hidden-form-value eagle-form-year"><?php echo $vehicle['year']; ?></span>
													<span class="eagle-hidden-form-value eagle-form-make"><?php echo $vehicle['make']['name']; ?></span>
													<span class="eagle-hidden-form-value eagle-form-model"><?php echo $vehicle['model']['name']; ?></span>
													<span class="eagle-hidden-form-value eagle-form-trim"><?php echo $vehicle['trim']['name']; ?></span>
													<span class="eagle-hidden-form-value eagle-form-saleclass"><?php echo $vehicle['saleclass']; ?></span>
													<span class="eagle-hidden-form-value eagle-form-stock"><?php echo $vehicle['stock_number']; ?></span>
													<span class="eagle-hidden-form-value eagle-form-vin"><?php echo $vehicle['vin']; ?></span>
													<span class="eagle-hidden-form-value eagle-form-vehicle"><?php echo $generic_vehicle_title; ?></span>
													<span class="eagle-hidden-form-value eagle-form-inventory"><?php echo $vehicle['id']; ?></span>
													<span class="eagle-hidden-form-value eagle-form-subject-post"><?php echo $form_subject; ?></span>
													<span class="eagle-hidden-form-value eagle-form-url"><?php echo '&#39;' . $form_submit_url . strtolower( $vehicle['saleclass'] ) . '_vehicle_inquiry&#39;'; ?></span>
												</div>

												<?php
													if( $vehicle['autocheck'] ){
														echo display_autocheck_image( $vehicle['vin'], $vehicle['saleclass'], $type );
													}
												?>
											</div>
										</div>
										<div class="eagle-listing-bottom"> <!-- Eagle Listing Bottom -->
											<?php
												if( !empty( $theme_settings[ 'display_tags' ] ) ){
													apply_special_tags( $vehicle['tags'], $vehicle['on_sale'], $vehicle['certified'], $vehicle['video']);
													if( !empty( $vehicle['tags'] ) ){
														echo '<div class="eagle-listing-tags">';
															$tag_icons = build_tag_icons( $default_tag_names, $custom_tag_icons, $vehicle['tags']);
															echo $tag_icons;
														echo '</div>';
													}
												}
											?>
											<div class="eagle-listing-buttons">
												<div class="eagle-listing-button eagle-show-form eagle-confirm-button" name="Confirm Availability">Confirm Availability</div>
												<div class="eagle-listing-button eagle-show-form eagle-question-button" name="Ask a Question">Ask a Question</div>
												<div class="eagle-listing-button eagle-details-button"><a href="<?php echo $link; ?>">View More Details</a></div>
												<div class="eagle-hidden-form-values" style="display: none;">
													<span class="eagle-hidden-form-value eagle-form-price"><?php echo $price['primary_price']; ?></span>
													<span class="eagle-hidden-form-value eagle-form-year"><?php echo $vehicle['year']; ?></span>
													<span class="eagle-hidden-form-value eagle-form-make"><?php echo $vehicle['make']['name']; ?></span>
													<span class="eagle-hidden-form-value eagle-form-model"><?php echo $vehicle['model']['name']; ?></span>
													<span class="eagle-hidden-form-value eagle-form-trim"><?php echo $vehicle['trim']['name']; ?></span>
													<span class="eagle-hidden-form-value eagle-form-saleclass"><?php echo $vehicle['saleclass']; ?></span>
													<span class="eagle-hidden-form-value eagle-form-stock"><?php echo $vehicle['stock_number']; ?></span>
													<span class="eagle-hidden-form-value eagle-form-vin"><?php echo $vehicle['vin']; ?></span>
													<span class="eagle-hidden-form-value eagle-form-vehicle"><?php echo $generic_vehicle_title; ?></span>
													<span class="eagle-hidden-form-value eagle-form-inventory"><?php echo $vehicle['id']; ?></span>
													<span class="eagle-hidden-form-value eagle-form-subject-post"><?php echo $form_subject; ?></span>
													<span class="eagle-hidden-form-value eagle-form-url"><?php echo '&#39;' . $form_submit_url . strtolower( $vehicle['saleclass'] ) . '_vehicle_inquiry&#39;'; ?></span>

												</div>
											</div>
											<div class="eagle-stock-number" alt="<?php echo $vehicle['stock_number']; ?>">
												Stock # <?php echo $vehicle['stock_number']; ?>
											</div>
											<div class="eagle-mobile-bottom">
												<?php echo $price['primary_text']; ?>
												<div class="eagle-detail-button">
													<a href="<?php echo $link; ?>"><?php echo ( !empty($theme_settings['list_info_button']) ) ? $theme_settings['list_info_button'] : 'GET YOUR ePrice'; ?></a>
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
			</div>
			<div id="eagle-content-bottom"> <!-- Eagle Content Bottom -->
				<div class="eagle-content-bottom-wrapper">
					<div class="eagle-pager">
						<span>Page:</span>
						<?php echo paginate_links( $args ); ?>
					</div>
				</div>
			</div>
		</div>

		<div id="eagle-bottom">  <!-- Eagle Bottom -->
			<div class="eagle-disclaimer">
				<?php echo !empty( $inventory ) ? '<p>' . $inventory[0]->disclaimer . '</p>' : NULL; ?>
			</div>
		</div>

		<div class="eagle-forms eagle-hidden-form" style="display: none;">
			<div class="eagle-form-headers active-form" name="form-info" tabindex="19">
			</div>
			<div class="eagle-form-headers-sub" name="form-info-sub">
			</div>
			<div id="eagle-form-info" class="eagle-form" name="active" style="display: block;">
				<form action="#" method="post" name="vehicle-inquiry" id="vehicle-inquiry-hidden">
					<input type="hidden" name="traffic_source" value="<?php echo $traffic_source; ?>"/>
					<input name="required_fields" type="hidden" value="name,email,privacy" />
					<input name="subject" type="hidden" id="vehicle-inquiry-subject-hidden" value="" />
					<input name="saleclass" type="hidden" id="vehicle-inquiry-saleclass-hidden" value="" />
					<input name="vehicle" type="hidden" id="vehicle-inquiry-vehicle-hidden" value="" />
					<input name="year" type="hidden" id="vehicle-inquiry-year-hidden" value="" />
					<input name="make" type="hidden" id="vehicle-inquiry-make-hidden" value="" />
					<input name="model_name" type="hidden" id="vehicle-inquiry-model-hidden" value="" />
					<input name="trim" type="hidden" id="vehicle-inquiry-trim-hidden" value="" />
					<input name="stock" type="hidden" id="vehicle-inquiry-stock-hidden" value="" />
					<input name="vin" type="hidden" id="vehicle-inquiry-vin-hidden" value="" />
					<input name="inventory" type="hidden" id="vehicle-inquiry-inventory-hidden" value="" />
					<input name="price" type="hidden" id="vehicle-inquiry-price-hidden" value="" />
					<input name="name" type="hidden" id="vehicle-inquiry-name-hidden" value="" />
					<input name="subject-pre" type="hidden" id="vehicle-inquiry-subpre-hidden" value="" />
					<input name="subject-post" type="hidden" id="vehicle-inquiry-subpost-hidden" value="<?php echo $form_subject; ?>" />
					<div class="eagle-form-table">
						<div class="eagle-form-full">
							<div class="required">
								<input maxlength="70" id="vehicle-inquiry-f-name-hidden" name="f_name" tabindex="20" type="text" alt="empty" value="First Name*" />
							</div>
						</div>
						<div class="eagle-form-full">
							<div class="required">
								<input maxlength="70" id="vehicle-inquiry-l-name-hidden" name="l_name" tabindex="21" type="text" alt="empty" value="Last Name*" />
							</div>
						</div>

						<div class="eagle-form-full">
							<div class="required">
								<input maxlength="255" id="vehicle-inquiry-email-hidden" name="email" tabindex="22" type="text" alt="empty" value="Email Address*"/>
							</div>
						</div>
						<div class="eagle-form-full">
							<div>
								<input maxlength="256" name="phone" id="vehicle-inquiry-phone-hidden" tabindex="23" type="text" alt="empty" value="Phone Number"/>
							</div>
						</div>
						<div class="eagle-form-full">
							<div>
								<textarea name="comments" id="vehicle-inquiry-form-comments-hidden" rows="4" tabindex="24" alt="empty">Comments</textarea>
							</div>
						</div>
						<div class="eagle-form-full">
							<div style="display:none">
								<input name="agree_sb" type="checkbox" value="Yes" /> I am a Spam Bot?
							</div>
							<div style="display:none">
								<label for="vehicle-inquiry-privacy" style="float:left; margin-right:10px;">Agree to <a target="_blank" href="/privacy">Privacy Policy</a></label>
								<input class="privacy" name="privacy" id="vehicle-inquiry-privacy-hidden" type="checkbox" checked />
							</div>
						</div>
						<div class="eagle-form-button">
							<div>
								<input onclick="" type="submit" value="Send Inquiry" class="submit" tabindex="25" />
							</div>
						</div>
						<div class="eagle-form-full">
							<div class="form-error" style="display: none;">
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		<?php
			if ( is_active_sidebar( 'vehicle-listing-page' ) ) :
				echo '<div id="detail-widget-area">';
					dynamic_sidebar( 'vehicle-listing-page' );
				echo '</div>';
			endif;
		?>
	</div>
</div>
