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

	$vehicleclass = isset( $this->parameters[ 'vehicleclass' ] ) ? $this->parameters[ 'vehicleclass' ] : NULL;
	$certified = isset( $this->parameters[ 'certified' ] ) ? $this->parameters[ 'certified' ] : NULL;
	$price_to = isset( $this->parameters[ 'price_to' ] ) ? $this->parameters[ 'price_to' ] : NULL;
	$price_from = isset( $this->parameters[ 'price_from' ] ) ? $this->parameters[ 'price_from' ] : NULL;

	$search_error = '';

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

<div id="dolphin-wrapper">
	<div id="dolphin-listing">
		<div id="dolphin-top"> <!-- Listing Top -->
			<div class="dolphin-breadcrumb-wrapper">
				<div class="breadcrumbs"><?php echo display_breadcrumb( $parameters, $company_information, $inventory_options, $parameters[ 'saleclass' ] ); ?></div> <!-- Breadcrumbs -->
				<div class="dolphin-pager"> <!-- Pager -->
					<?php echo paginate_links( $args ); ?>
				</div>
			</div>
			<div id="mobile-show-search">Display Search Options</div>
			<div id="dolphin-search-wrapper"> <!-- Search Filter Wrapper -->
				<div id="search-top-wrapper">
					<div id="saleclass-wrapper" class="dolphin-option-wrapper search-option-top">
						<label class="dolphin-label">Sale Class</label><br/>
						<select id="dolphin-saleclass" class="dolphin-select" onchange="document.location = this.value;">
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
					<div id="vehicleclass-wrapper" class="dolphin-option-wrapper search-option-top">
						<label class="dolphin-label">Vehicle Class</label><br/>
						<select id="dolphin-vehicleclass" class="dolphin-select" onchange="document.location = this.value;">
							<option value="<?php echo remove_query_arg('vehicleclass'); ?>">All</option>
							<option value="<?php echo add_query_arg(array('vehicleclass'=>'car')); ?>" <?php echo $vehicleclass == 'car' ? 'selected' : NULL; ?>>Car</option>
							<option value="<?php echo add_query_arg(array('vehicleclass'=>'truck')); ?>" <?php echo $vehicleclass == 'truck' ? 'selected' : NULL; ?>>Truck</option>
							<option value="<?php echo add_query_arg(array('vehicleclass'=>'sport_utility')); ?>" <?php echo $vehicleclass == 'sport_utility' ? 'selected' : NULL; ?>>SUV</option>
							<option value="<?php echo add_query_arg(array('vehicleclass'=>'van,minivan')); ?>" <?php echo $vehicleclass == 'van,minivan' ? 'selected' : NULL; ?>>Van</option>
						</select>
					</div>
					<div id="price-range-wrapper" class="dolphin-option-wrapper search-option-top">
						<label class="dolphin-label" for="price-range">Price Range</label><br/>
						<select class="dolphin-select" onchange="document.location = this.value;">
							<option value="<?php echo remove_query_arg( 'price_from', 'price_to' ) ?>"><?php echo (isset($parameters['price_from']))? 'Remove Price Range' : 'Select Price Range'; ?></option>
							<option value="<?php echo add_query_arg( array('price_from'=>'1','price_to'=>10000) ); ?>" <?php echo $parameters['price_from'] == "1" ? 'selected' : ''; ?>>$1 - $10,000</option>
							<option value="<?php echo add_query_arg( array('price_from'=>'10001','price_to'=>20000) ); ?>" <?php echo $parameters['price_from'] == "10001" ? 'selected' : ''; ?>>$10,001 - $20,000</option>
							<option value="<?php echo add_query_arg( array('price_from'=>'20001','price_to'=>30000) ); ?>" <?php echo $parameters['price_from'] == "20001" ? 'selected' : ''; ?>>$20,001 - $30,000</option>
							<option value="<?php echo add_query_arg( array('price_from'=>'30001','price_to'=>40000) ); ?>" <?php echo $parameters['price_from'] == "30001" ? 'selected' : ''; ?>>$30,001 - $40,000</option>
							<option value="<?php echo add_query_arg( array('price_from'=>'40001','price_to'=>50000) ); ?>" <?php echo $parameters['price_from'] == "40001" ? 'selected' : ''; ?>>$40,001 - $50,000</option>
							<option value="<?php echo remove_query_arg( 'price_to', add_query_arg( array('price_from'=>'50001') ) ); ?>" <?php echo $parameters['price_from'] == "50001" ? 'selected' : ''; ?>>$50,001 - Above</option>
						</select>
					</div>
					<div id="text-search-wrapper" class="search-option-top">
						<label for="search">Inventory Search</label><br/>
						<input id="dolphin-search-box" class="<?php echo isset( $parameters[ 'search' ] ) ? '' : 'invalid '; ?>list-search-value" name="search" value="<?php echo isset( $parameters[ 'search' ] ) ? $parameters[ 'search' ] : 'Text Search'; ?>" />
					</div>
					<button onclick="return get_list_input_values(event);" id="dolphin-search-submit">Search</button>
				</div>
				<div id="search-mid-wrapper">
					<div id="dolphin-makes" class="dolphin-option-wrapper search-option-mid"> <!-- Makes -->
						<label class="dolphin-label">Makes</label><br/>
						<select class="dolphin-select" onchange="document.location = this.value;">
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
					<div id="dolphin-models" class="dolphin-option-wrapper search-option-mid"> <!-- Models -->
						<label class="dolphin-label">Models</label><br/>
						<select class="dolphin-select" onchange="document.location = this.value;" <?php if( !isset( $model_count ) || $model_count == 0 ) { echo 'readonly'; } ?> >
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
					<div id="dolphin-trims" class="dolphin-option-wrapper search-option-mid"> <!-- Trims -->
						<label class="dolphin-label">Trims</label><br/>
						<select class="dolphin-select" onchange="document.location = this.value;" <?php if( !isset( $trim_count ) || $trim_count == 0 ) { echo 'readonly'; } ?> >
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
					<?php if($theme_settings['display_geo']) { ?>
						<div id="list-geo-filter" class="dolphin-option-wrapper search-option-mid">
							<div class="dolphin-label"><span>Vehicle Location</span></div>
							<div id="geo-wrapper">
								<?php 
									$geo_output = build_geo_dropdown($dealer_geo, $geo_params, $theme_settings['add_geo_zip'] ,'Clear');
									echo !empty( $geo_output['dropdown'] ) ? $geo_output['dropdown'] : '';
									//echo !empty( $geo_output['back_link'] ) ? $geo_output['back_link'] : '';
								?>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>

			<div id="dolphin-search-info"> <!-- Search Info -->
				<div id="dolphin-total-found">Vehicles Found: <?php echo $vehicle_total_found; echo !empty( $geo_output['search']) ? ' in '.$geo_output['search'] : ''; echo !empty( $geo_output['back_link'] ) ? ' | '.$geo_output['back_link'].'Geo Search' : ''; ?></div>
				<div id="dolphin-sort-wrapper">
					<div id="sort-label">Sort by:</div>
					<div class="sort-value"><a class="<?php echo $sort_year_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_year ) , $do_not_carry ); ?>">Year</a></div>
					<div class="sort-value"><a class="<?php echo $sort_price_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_price ) , $do_not_carry ); ?>">Price</a></div>
					<div class="sort-value"><a class="<?php echo $sort_mileage_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_mileage ) , $do_not_carry ); ?>">Mileage</a></div>
				</div>
			</div>
		</div>
	</div>
	

	<div id="dolphin-content"> <!-- Inventory -->
		<div id="dolphin-listing-items"> <!-- Inventory Listing -->
			<?php
				if( empty( $inventory ) ) {
					echo '<div class="dolphin-not-found"><h2><strong>Unable to find inventory items that matched your search criteria.</strong></h2><a onClick="history.go(-1)" title="Return to Previous Search" class="jquery-ui-button">Return to Previous Search</a></div>';
				} else {
					foreach( $inventory as $inventory_item ):
						$vehicle = itemize_vehicle($inventory_item);
						$generic_vehicle_title = $vehicle['year'] . ' ' . $vehicle['make']['clean'] . ' ' . $vehicle['model']['clean'];
						$link_params = array( 'year' => $vehicle['year'], 'make' => $vehicle['make']['name'],  'model' => $vehicle['model']['name'], 'state' => $state, 'city' => $city, 'vin' => $vehicle['vin'] );
						$link = generate_inventory_link($url_rule,$link_params,'','',1);
						$price = get_price_display($vehicle['prices'], $company_information, $vehicle['saleclass'], $vehicle['vin'], 'dolphin', $price_text, array() );
			?>
			<div class="dolphin-item" id="<?php echo $vin; ?>"> <!-- Inventory Listing -->
				<div class="dolphin-column-left"> <!-- dolphin column left -->
					<div class="dolphin-photo"> <!-- dolphin photo -->
						<a href="<?php echo $link; ?>" title="<?php echo $generic_vehicle_title; ?>">
							<?php echo $vehicle['sold'] ? '<img class="marked-sold-overlay" src="http://assets.s3.dealertrend.com.s3.amazonaws.com/images/sold_overlay.png" />' : '' ?>
							<img class="list-image" src="<?php echo $vehicle['thumbnail']; ?>" alt="<?php echo $generic_vehicle_title; ?>" title="<?php echo $generic_vehicle_title; ?>" />
						</a>
					</div>
					 <!-- dolphin icons -->
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
				</div>
				<div class="dolphin-column-right"> <!-- dolphin column right -->
					<div class="dolphin-headline">
						<div class="dolphin-headline-details">
							<span class="dolphin-saleclass"><?php echo $vehicle['saleclass']; ?></span>
							<a href="<?php echo $link; ?>" title="<?php echo $generic_vehicle_title; ?>" >
								<span class="dolphin-year"><?php echo $vehicle['year']; ?></span>
								<span class="dolphin-make"><?php echo $vehicle['make']['clean']; ?></span>
								<span class="dolphin-model"><?php echo $vehicle['model']['clean']; ?></span>
								<span class="dolphin-trim"><?php echo $vehicle['trim']['clean']; ?></span>
							</a>
						</div>
						<?php
							if( $price['msrp_text'] > 0 && strtolower( $vehicle['saleclass'] ) == 'new' ) {
								echo '<div class="dolphin-headline-msrp" alt="'.$price['msrp_text'].'">MSRP: '.'$'.number_format( $price['msrp_text'], 0, '.', ',').'</div>';
							}
							if( strlen( trim( $vehicle['headline'] ) ) > 0 ) {
								echo '<div class="dolphin-headline-text">'.$vehicle['headline'].'</div>';
							}
						?>
					</div>
					<div class="dolphin-listing-info">
						<div class="dolphin-details-left">
							<span class="dolphin-stock-number" alt="<?php echo $vehicle['stock_number']; ?>" >Stock #: <?php echo $vehicle['stock_number']; ?></span>
							<span class="dolphin-vin" alt="<?php echo $vehicle['vin']; ?>">VIN: <?php echo $vehicle['vin']; ?></span>
							<?php
								if( $vehicle['odometer'] > 100 ){
									echo '<span class="dolphin-odometer">Odometer: '.$vehicle['odometer'].'</span>';
								}
								if( $vehicle['certified'] == 'true' ){
									echo '<span class="dolphin-certified">Certified Pre-Owned</span>';
								}
							?>
						</div>
						<div class="dolphin-details-right">
							<?php
								echo $vehicle['body_style'] != NULL ? '<span class="dolphin-body-style">Body Style: '.$vehicle['body_style'].'</span>' : NULL;
								echo $vehicle['interior_color'] != NULL ? '<span class="dolphin-interior-color">Int. Color: '.$vehicle['interior_color'].'</span>' : NULL;
								echo $vehicle['exterior_color'] != NULL ? '<span class="dolphin-exterior-color">Ext. Color: '.$vehicle['exterior_color'].'</span>' : NULL;
								echo $vehicle['transmission'] != NULL ? '<span class="dolphin-transmission">Trans: '.$vehicle['transmission'].'</span>' : NULL;
							?>
						</div>
						<div class="dolphin-price">
							<?php
								echo (!empty($price['rebate_link'])) ? $price['rebate_link'] : ( (!empty($price['ais_link'])) ? $price['ais_link'] : '' );
								echo $price['compare_text'].( empty($price['rebate_link']) ? $price['ais_text'] : '' ).$price['primary_text'].$price['expire_text'].$price['hidden_prices'];
							?>
						</div>
					</div>
					<div class="dolphin-more-info">
						<div class="dolphin-contact-information">
							<?php
								echo get_dealer_contact_info( $vehicle['contact_info'], $inventory_options, $vehicle['saleclass'] );
							?>
						</div>
						<div class="dolphin-detail-button">
							<a href="<?php echo $link; ?>" title="More Information: <?php echo $generic_vehicle_title; ?>"><?php echo ( !empty($theme_settings['list_info_button']) ) ? $theme_settings['list_info_button'] : 'More Information'; ?></a>
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
	<div id="dolphin-disclaimer">
		<?php echo !empty( $inventory ) ? '<p>' . $inventory[ 0 ]->disclaimer . '</p>' : NULL; ?>
	</div>
	<div id="dolphin-bottom">
		<div class="breadcrumbs"><?php echo display_breadcrumb( $parameters, $company_information, $inventory_options, $parameters[ 'saleclass' ] ); ?></div>
		<div class="dolphin-pager">
			<?php echo paginate_links( $args ); ?>
		</div>
		<!-- <a href="#dolphin-wrapper" title="Return to Top" class="dolphin-return-to-top">Return to Top</a> -->
	</div>
	<?php
		if ( is_active_sidebar( 'vehicle-listing-page' ) ) :
			echo '<div id="detail-widget-area">';
				dynamic_sidebar( 'vehicle-listing-page' );
			echo '</div>';
		endif;
	?>
</div>
