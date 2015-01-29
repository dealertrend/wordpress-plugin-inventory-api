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
		default: $sort_year_class = $sort_price_class = $sort_mileage_class = null; break;
	}
	$sort_year = $sort != 'year_asc' ? 'year_asc' : 'year_desc';
	$sort_mileage = $sort != 'mileage_asc' ? 'mileage_asc' : 'mileage_desc';
	$sort_price = $sort != 'price_asc' ? 'price_asc' : 'price_desc';

?>

	<div id="armadillo-wrapper">
		<div id="armadillo-listing">
			<div class="breadcrumb-wrapper">
				<div class="breadcrumbs"><?php echo display_breadcrumb( $parameters, $company_information, $inventory_options ); ?></div>
				<div class="armadillo-pager"><?php echo paginate_links( $args ); ?></div>
			</div>
			<form onsubmit="return list_search_field(event, this.value);" name="list_search_form" id="armadillo-search">
				<label for="search">Inventory Search:</label>
				<input id="armadillo-search-box" name="search" value="<?php echo isset( $parameters[ 'search' ] ) ? $parameters[ 'search' ] : NULL; ?>" />
			</form>

			<div id="armadillo-list-top-wrapper">
				<div id="armadillo-total-found" class="color-one"><?php echo $vehicle_total_found; ?> Cars Found</div>
				<div id="armadillo-sort-wrapper">
					<div id="sort-label">Sort by:</div>
					<div class="sort-value"><a class="<?php echo $sort_year_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_year ) , $do_not_carry ); ?>">Year</a></div>
					<div class="sort-value"><a class="<?php echo $sort_price_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_price ) , $do_not_carry ); ?>">Price</a></div>
					<div class="sort-value"><a class="<?php echo $sort_mileage_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_mileage ) , $do_not_carry ); ?>">Mileage</a></div>
				</div>
			</div>

			<div id="armadillo-list-sidebar">
				<h3 id="list-sidebar-label-mobile" class="">Refine Your Search</h3>
				<h3 id="list-sidebar-label" class="color-one">Refine Your Search</h3>
				<ul id="list-saleclass-filter">
					<li class="armadillo-expanded">
						<div class="list-sidebar-label"><span>Sale Class</span></div>
						<ul>
							<?php switch( $inventory_options['saleclass_filter'] ) {
								case 'all':
									echo '<li><span class="no-style"><a href="' . $new_link . '" title="View New Inventory" class="list-button ' . (strtolower( $parameters[ 'saleclass' ] ) == 'new' ? 'disabled' : NULL) . '">New</a></span></li>';
									echo '<li><span class="no-style"><a href="' . $used_link . '" title="View Used Inventory" class="list-button ' . (strtolower( $parameters[ 'saleclass' ] ) == 'used' ? 'disabled' : NULL) . '">Used</a></span></li>';
									echo '<li><span class="no-style"><a href="' . $cert_link . '" title="View Certified Used Inventory" class="list-button ' . (strtolower( $parameters[ 'saleclass' ] ) == 'used' ? 'disabled' : NULL) . '">Certified</a></span></li>';
									break;
								case 'new':
									echo '<li><span class="no-style"><a href="' . $new_link . '" title="View New Inventory" class="list-button ' . (strtolower( $parameters[ 'saleclass' ] ) == 'new' ? 'disabled' : NULL) . '">New</a></span></li>';
									break;
								case 'used':
									echo '<li><span class="no-style"><a href="' . $used_link . '" title="View Used Inventory" class="list-button ' . (strtolower( $parameters[ 'saleclass' ] ) == 'used' ? 'disabled' : NULL) . '">Used</a></span></li>';
									echo '<li><span class="no-style"><a href="' . $cert_link . '" title="View Certified Used Inventory" class="list-button ' . (strtolower( $parameters[ 'saleclass' ] ) == 'used' ? 'disabled' : NULL) . '">Certified</a></span></li>';
									break;
								case 'certified':
									echo '<li><span class="no-style"><a href="' . $cert_link . '" title="View Certified Used Inventory" class="list-button ' . (strtolower( $parameters[ 'saleclass' ] ) == 'used' ? 'disabled' : NULL) . '">Certified</a></span></li>';
									break;
							}
							?>
						</ul>
					</li>
				</ul>
				<?php if($theme_settings['display_geo']) { ?>
					<ul id="list-geo-filter">
						<li class="armadillo-expanded">
							<div class="list-sidebar-label"><span>Vehicle Location</span></div>
							<div id="geo-wrapper">
								<?php
									$geo_output = build_geo_dropdown($dealer_geo, $geo_params, $theme_settings['add_geo_zip'] );
									echo !empty( $geo_output['search'] ) ? $geo_output['search'] : ''; 
									echo !empty( $geo_output['dropdown'] ) ? $geo_output['dropdown'] : '';
									echo !empty( $geo_output['back_link'] ) ? $geo_output['back_link'] : '';
								?>
							</div>
						</li>
					</ul>
				<?php } ?>
				<ul id="list-vehicleclass-filter">
					<li class="armadillo-expanded">
						<div class="list-sidebar-label"><span>Vehicle Class</span></div>
						<ul>
							<li><a href="<?php echo generate_inventory_link($url_rule,$parameters,array('vehicleclass'=>'car')); ?>" <?php echo $parameters[ 'vehicleclass' ] == 'car' ? 'class="active"' : NULL; ?>>Car</a></li>
							<li><a href="<?php echo generate_inventory_link($url_rule,$parameters,array('vehicleclass'=>'truck')); ?>" <?php echo $parameters[ 'vehicleclass' ] == 'truck' ? 'class="active"' : NULL; ?>>Truck</a></li>
							<li><a href="<?php echo generate_inventory_link($url_rule,$parameters,array('vehicleclass'=>'sport_utility')); ?>" <?php echo $parameters[ 'vehicleclass' ] == 'sport_utility' ? 'class="active"' : NULL; ?>>SUV</a></li>
							<li><a href="<?php echo generate_inventory_link($url_rule,$parameters,array('vehicleclass'=>'van,minivan')); ?>" <?php echo $parameters[ 'vehicleclass' ] == 'van,minivan' ? 'class="active"' : NULL; ?>>Van</a></li>
						</ul>
					</li>
				</ul>

				<?php //Make | Model | Trim Fitler
					if ( $trim_count != 0 ) {
						$sidebar_content = '<ul id="list-trim-filter"><li class="armadillo-expanded"><div class="list-sidebar-label"><span>Trims</span></div><ul>';
						foreach( $trims as $trim ) {
							$trim_safe = str_replace( '/' , '%2F' , $trim );
							$link = generate_inventory_link($url_rule,$parameters,array('trim'=>$trim_safe));
							$sidebar_content .= '<li><a href="'.$link.'">'.$trim.'</a></li>';
						}
						$back_link = generate_inventory_link($url_rule,$parameters,'',array('model','trim'));
						$sidebar_content .= '<li><span class="no-style"><a href="'.$back_link.'" class="armadillo-filter-prev" title="View '.$parameters['make'].' Models">&#60; View '. $parameters[ 'make' ].'</a></span></li>';
						$sidebar_content .= '</ul></li></ul>';

					} else if ( $model_count != 0) {
						$sidebar_content = '<ul id="list-model-filter"><li class="armadillo-expanded"><div class="list-sidebar-label"><span>Models</span></div><ul>';
						foreach( $models as $model ) {
							$model_safe = str_replace( '/' , '%2F' , $model );
							$link = generate_inventory_link($url_rule,$parameters,array('model'=>$model_safe));
							$sidebar_content .= '<li><a href="'.$link.'">'.$model.'</a></li>';
						}
						$back_link = generate_inventory_link($url_rule,$parameters,'',array('make', 'model'));
						$sidebar_content .= '<li><span class="no-style"><a href="'.$back_link.'" class="armadillo-filter-prev" title="View ' . $parameters[ 'saleclass' ] . ' Vehicles">&#60; All ' . $parameters[ 'saleclass' ] . ' Vehicles</a></span></li>';
						$sidebar_content .= '</ul></li></ul>';
					} else if ( $make_count != 0) {
						$sidebar_content = '<ul id="list-make-filter"><li class="armadillo-expanded"><div class="list-sidebar-label"><span>Makes</span></div><ul>';
							foreach( $makes as $make ) {
								$make_safe = str_replace( '/' , '%2F' , $make );
								$link = generate_inventory_link($url_rule,$parameters,array('make'=>$make_safe));
								$sidebar_content .= '<li><a href="'.$link.'">'.$make.'</a></li>';
							}
						$sidebar_content .= '</ul></li></ul>';
					} else {
						$sidebar_content = '<ul id="list-filter-error"><li><div class="list-sidebar-label"><span>No Makes Found</span></div></li><ul>';
					}
					echo $sidebar_content;
				?>
				<ul id="list-price-filter">
					<li class="armadillo-expanded">
						<div class="list-sidebar-label"><span>Price Range</span></div>
						<ul>
							<li><a rel="nofollow" href="<?php echo generate_inventory_link($url_rule,$parameters,array('price_from'=>'1', 'price_to'=>'10000'));?>" <?php echo $parameters['price_from'] == "1" ? 'class="active"' : NULL; ?>>$1 - $10,000</a></li>
							<li><a rel="nofollow" href="<?php echo generate_inventory_link($url_rule,$parameters,array('price_from'=>'10001', 'price_to'=>'20000'));?>" <?php echo $parameters['price_from'] == 10001 ? 'class="active"' : NULL; ?>>$10,001 - $20,000</a></li>
							<li><a rel="nofollow" href="<?php echo generate_inventory_link($url_rule,$parameters,array('price_from'=>'20001', 'price_to'=>'30000')); ?>" <?php echo $parameters['price_from'] == 20001 ? 'class="active"' : NULL; ?>>$20,001 - $30,000</a></li>
							<li><a rel="nofollow" href="<?php echo generate_inventory_link($url_rule,$parameters,array('price_from'=>'30001', 'price_to'=>'40000')); ?>" <?php echo $parameters['price_from'] == 30001 ? 'class="active"' : NULL; ?>>$30,001 - $40,000</a></li>
							<li><a rel="nofollow" href="<?php echo generate_inventory_link($url_rule,$parameters,array('price_from'=>'40001', 'price_to'=>'50000')); ?>" <?php echo $parameters['price_from'] == 40001 ? 'class="active"' : NULL; ?>>$40,001 - $50,000</a></li>
							<li><a rel="nofollow" href="<?php echo generate_inventory_link($url_rule,$parameters,array('price_from'=>'50001')); ?>" <?php echo $parameters['price_from'] == 50001 ? 'class="active"' : NULL; ?>>$50,001 - &amp; Above</a></li>
						</ul>
					</li>
				</ul>
			</div>
		
		<div id="armadillo-listing-content">
			<div id="armadillo-listing-items">
				<?php
					if( empty( $inventory ) ) {
						echo '<div class="armadillo-not-found"><h2><strong>Unable to find inventory items that matched your search criteria.</strong></h2><a onClick="history.go(-1)" title="Return to Previous Search" class="">Return to Previous Search</a></div>';
					} else {
						foreach( $inventory as $inventory_item ):
							$vehicle = itemize_vehicle($inventory_item);
							$generic_vehicle_title = $vehicle['year'] . ' ' . $vehicle['make']['clean'] . ' ' . $vehicle['model']['clean'];
							$link_params = array( 'year' => $vehicle['year'], 'make' => $vehicle['make']['name'],  'model' => $vehicle['model']['name'], 'state' => $state, 'city' => $city, 'vin' => $vehicle['vin'] );
							$link = generate_inventory_link($url_rule,$link_params,'','',1);
							?>
							<div id="<?php echo $vehicle['vin'];?>" class="armadillo-vehicle saleclass-<?php echo strtolower($vehicle['saleclass']);?>">
								<div class="armadillo-content-wrapper">
									<div class="armadillo-column-left">
										<div class="armadillo-photo">
											<a class="vehicle-link" href="<?php echo $link;?>">
												<?php echo $vehicle['sold'] ? '<img class="marked-sold-overlay" src="http://assets.s3.dealertrend.com.s3.amazonaws.com/images/sold_overlay.png" />' : '' ?>
												<img class="list-image" src="<?php echo $vehicle['thumbnail']; ?>" alt="<?php echo $generic_vehicle_title; ?>" title="<?php echo $generic_vehicle_title; ?>" />
											</a>
										</div>
									</div>
									<div class="armadillo-column-right">
										<div class="armadillo-main-line" title="<?php echo $generic_vehicle_title; ?>">
											<a class="vehicle-link" href="<?php echo $link;?>">
												<span class="armadillo-year"><?php echo $vehicle['year']; ?></span>
												<span class="armadillo-make"><?php echo $vehicle['make']['name']; ?></span>
												<span class="armadillo-model"><?php echo $vehicle['model']['name']; ?></span>
												<span class="armadillo-trim"><?php echo $vehicle['trim']['name']; ?></span>
												<span class="armadillo-drive-train"><?php echo $vehicle['drive_train']; ?></span>
												<span class="armadillo-body-style"><?php echo $vehicle['body_style']; ?></span>
											</a>
										</div>
										<?php
											if( strlen( trim( $vehicle['headline'] ) ) > 0 ) {
												echo '<div class="armadillo-headline">' . $vehicle['headline'] . '</div>';
											}
											$price = get_price_display($vehicle['prices'], $company_information, $vehicle['saleclass'], $vehicle['vin'], 'armadillo', $price_text, array() );
										?>
										<div class="armadillo-details-left">
											<?php
												echo ( !empty($price['msrp_text']) && strtolower($vehicle['saleclass']) == 'new' ) ? $price['msrp_text'] : '';
												echo $vehicle['interior_color'] != NULL ? '<span class="armadillo-interior-color">Interior: '.$vehicle['interior_color'].'</span>' : '';
												echo $vehicle['exterior_color'] != NULL ? '<span class="armadillo-exterior-color">Exterior: '.$vehicle['exterior_color'].'</span>' : '';
												echo $vehicle['transmission'] != NULL ? '<span class="armadillo-transmission">Transmission: '.$vehicle['transmission'].'</span>' : '';
											?>
										</div>
										<div class="armadillo-details-right">
											<span class="armadillo-stock-number">Stock #: <?php echo $vehicle['stock_number']; ?></span>
											<span class="armadillo-odometer">Odometer: <?php echo $vehicle['odometer']; ?></span>
											<span class="armadillo-vin">VIN: <?php echo $vehicle['vin']; ?></span>
										</div>
										<?php
											if( $theme_settings['display_tags'] ){
												apply_special_tags( $vehicle['tags'], $vehicle['prices']['on_sale'], $vehicle['certified'], $vehicle['video']);
												if( !empty( $vehicle['tags'] ) ){
													echo '<div class="armadillo-listing-tags">';
														$tag_icons = build_tag_icons( $default_tag_names, $custom_tag_icons, $vehicle['tags']);
														echo $tag_icons;
													echo '</div>';
												}
											}
										?>
										<div class="armadillo-details-bottom">
											<div class="armadillo-price-wrapper">
											<?php
												echo (!empty($price['rebate_link'])) ? $price['rebate_link'] : ( (!empty($price['ais_link'])) ? $price['ais_link'] : '' );
												echo $price['compare_text'].( empty($price['rebate_link']) ? $price['ais_text'] : '' ).$price['primary_text'].$price['expire_text'].$price['hidden_prices'];
											?>
											</div>
											<div class="list-detail-button" title="More Information: <?php echo $generic_vehicle_title; ?>">
												<a class="vehicle-link" href="<?php echo $link;?>"><?php echo ( !empty($theme_settings['list_info_button']) ) ? $theme_settings['list_info_button'] : 'More Information'; ?></a>
											</div>
										</div>
										<div class="armadillo-contact-information">
											<?php
												echo get_dealer_contact_info( $vehicle['contact_info'], $inventory_options, $vehicle['saleclass'] );
											?>
										</div>
										<?php
											if( $vehicle['autocheck'] ){
												echo display_autocheck_image( $vehicle['vin'], $vehicle['saleclass'], $type );
											}
										?>
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
		<div id="armadillo-disclaimer">
			<?php echo !empty( $inventory ) ? '<p>' . $inventory[ 0 ]->disclaimer . '</p>' : NULL; ?>
		</div>
	</div>
	<div class="breadcrumb-wrapper">
		<div class="breadcrumbs"><?php echo display_breadcrumb( $parameters, $company_information, $inventory_options ); ?></div>
		<div class="armadillo-pager"><?php echo paginate_links( $args ); ?></div>
	</div>
	<a href="#armadillo-top" title="Return to Top" class="armadillo-return-to-top">Return to Top</a>
	<?php
		if ( is_active_sidebar( 'vehicle-listing-page' ) ) :
			echo '<div id="detail-widget-area">';
				dynamic_sidebar( 'vehicle-listing-page' );
			echo '</div>';
		endif;
	?>
</div>

