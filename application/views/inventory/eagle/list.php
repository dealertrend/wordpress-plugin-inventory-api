<?php
namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

	global $wp_rewrite;

	$on_page = isset( $inventory[ 0 ]->pagination->on_page ) ? $inventory[ 0 ]->pagination->on_page : 0;
	$total = isset( $inventory[ 0 ]->pagination->total ) ? $inventory[ 0 ]->pagination->total : 0;

	$args = array(
		'base' => add_query_arg( 'page' , '%#%' ),
		'current' => $on_page,
		'total' => $total,
		'next_text' => __( 'Next >' ),
		'prev_text' => __( '< Prev' ),
		'show_all' => false,
		'type' => 'plain'
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
	$make_count = count( $makes );
	$model_count = 0;
	$trim_count = 0;

	if( isset( $parameters[ 'make' ] ) && $parameters[ 'make' ] != 'all' ) { //Get Models
		$tmp_do_not_carry = remove_query_arg( 'make' , $do_not_carry );
		$vehicle_management_system->tracer = 'Obtaining a list of models.';
		$models = $vehicle_management_system->get_models()->please( array_merge( array( 'saleclass' => $sale_class , 'make' => $parameters[ 'make' ] ) , $filters ) );
		$models = json_decode( $models[ 'body' ] );
		$model_count = count( $models );

		$model = isset( $parameters[ 'model' ] ) ? $parameters[ 'model' ] : 'all';
		$parameters[ 'model' ] = $model;
		$model_text = 'All Models';
	}

	if( isset( $parameters[ 'model' ] ) && $parameters[ 'model' ] != 'all' ) { //Get Trims
		$tmp_do_not_carry = remove_query_arg( array( 'make' , 'model' ) , $do_not_carry );
		$vehicle_management_system->tracer = 'Obtaining a list of trims.';
		$trims = $vehicle_management_system->get_trims()->please( array_merge( array( 'saleclass' => $sale_class , 'make' => $parameters[ 'make' ] , 'model' => $parameters[ 'model' ] ) , $filters ) );
		$trims = json_decode( $trims[ 'body' ] );
		$trim_count = count( $trims );

		$trim = isset( $parameters[ 'trim' ] ) ? $parameters[ 'trim' ]  : 'all';
		$parameters[ 'trim' ] = $trim;
		$trim_text = 'All Trims';
	}

	$traffic_source = isset( $_COOKIE[ 'dealertrend-traffic-source' ] ) ? $_COOKIE[ 'dealertrend-traffic-source' ] : false;
	$traffic_source = $this->sanitize_inputs( $traffic_source );

?>
<div id="eagle-wrapper">
	<div id="eagle-listing">
		<div id="eagle-top"> <!-- Eagle Top -->
			<?php echo $breadcrumbs; ?>
			<div id="eagle-top-search">
				<div class="eagle-search">
					<form action="<?php echo $inventory_base; ?>" method="GET" id="eagle-search">
						<?php echo empty( $wp_rewrite->rules ) ? '<input type="hidden" value="inventory" name="taxonomy" />' : NULL; ?>
						<input id="eagle-search-box" name="search" value="<?php echo isset( $parameters[ 'search' ] ) ? $parameters[ 'search' ] : NULL; ?>" />
						<button id="eagle-search-submit">SEARCH</button>
					</form>
				</div>
				<div class="eagle-pager">
					<span>Page:</span>
					<?php echo paginate_links( $args ); ?>
				</div>
			</div>
		</div>

		<div id="eagle-content">  <!-- Eagle Content -->
			<div id="eagle-content-top"> <!-- Eagle Content Top -->
				<div id="eagle-total-found"><span><?php echo $total_found; ?></span> Vehicles Found</div>
				<div id="eagle-sorting-columns">
					<div>Sort by: </div>
					<?php
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
					?>
					<div class="eagle-sorting-divider"><a class="<?php echo $sort_year_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_year ) , $do_not_carry ); ?>">Year</a></div>
					<div class="eagle-sorting-divider"><a class="<?php echo $sort_price_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_price ) , $do_not_carry ); ?>">Price</a></div>
					<div class="eagle-sorting-divider"><a class="<?php echo $sort_mileage_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_mileage ) , $do_not_carry ); ?>">Mileage</a></div>
					<div><a class="<?php echo $sort_make_class; ?>" href="<?php echo @add_query_arg( array( 'sort' => $sort_make ) , $do_not_carry ); ?>">Make</a></div>
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
									<?php
										switch( $sale_class_filter ) {
											case 'all':
												echo '<li><a href="' . $new . '" title="View New Inventory">New Vehicles</a></li>';
												echo '<li><a href="' . $used . '" title="View Used Inventory">Used Vehicles</a></li>';
												break;
											case 'new':
												echo '<li><a href="' . $new . '" title="View New Inventory">New Vehicles</a></li>';
												break;
											case 'used':
												echo '<li><a href="' . $used . '" title="View Used Inventory">Used Vehicles</a></li>';
												break;
											case 'certified':
												echo '<li><a href="' . $used . '" title="View Certified Used Inventory">Certified Vehicles</a></li>';
												break;
										}
									?>
								</ul>
							</div>
						</div>
						<div class="eagle-sidebar sidebar-refine-search">
							<h3>Refine Your Search By:</h3>
							<div class="eagle-sidebar-content content-bodystyle">
								<h4 class="" name="styles">Body Styles</h4>
								<ul>
									<li><a href="<?php echo add_query_arg( array( 'vehicleclass' => 'car' ) , $do_not_carry ); ?>" <?php echo $vehicleclass == 'car' ? 'class="active"' : NULL; ?>>Car</a></li>
									<li><a href="<?php echo add_query_arg( array( 'vehicleclass' => 'truck' ) , $do_not_carry ); ?>" <?php echo $vehicleclass == 'truck' ? 'class="active"' : NULL; ?>>Truck</a></li>
									<li><a href="<?php echo add_query_arg( array( 'vehicleclass' => 'sport_utility' ) , $do_not_carry ); ?>" <?php echo $vehicleclass == 'sport_utility' ? 'class="active"' : NULL; ?>>SUV</a></li>
									<li><a href="<?php echo add_query_arg( array( 'vehicleclass' => 'van,minivan' ) , $do_not_carry ); ?>" <?php echo $vehicleclass == 'van,minivan' ? 'class="active"' : NULL; ?>>Van</a></li>
								</ul>
							</div>

							<div class="eagle-sidebar-content content-make-model-trim">
								<?php
									if ( $trim_count != 0 ) {
										$sidebar_content = '<h4 class="" name="vehicles">Trims</h4>';
										$sidebar_content .= '<ul>';
										foreach( $trims as $trim ) {
											$trim_safe = str_replace( '/' , '%2F' , $trim );
											if( !empty( $wp_rewrite->rules ) ) {
												$url = $site_url . '/inventory/' . $sale_class . '/' . $parameters[ 'make' ] . '/' . $parameters[ 'model' ] . '/?trim=' . $trim_safe;
												$sidebar_content .= '<li><a href="' . $url . '">' . $trim . '</a></li>';
											} else {
												$sidebar_content .= '<li><a href="' . @add_query_arg( array( 'make' => $parameters[ 'make' ] , 'model' => $parameters[ 'model' ] , 'trim' => $trim_safe ) , $do_not_carry ) . '">' . $trim . '</a></li>';
											}
										}
										$sidebar_content .= '<li><span class="no-style"><a href="/inventory/' . $sale_class . '/' . $parameters[ 'make' ] . '/" class="eagle-filter-prev" title="View ' . $parameters[ 'make' ] . ' Models">&#60; View ' . $parameters[ 'make' ] . '</a></span></li>';
										$sidebar_content .= '</ul>';

									} else if ( $model_count != 0) {
										$sidebar_content = '<h4 class="" name="vehicles">Models</h4>';
										$sidebar_content .= '<ul>';
										foreach( $models as $model ) {
											$model_safe = str_replace( '/' , '%2F' , $model );
											if( !empty( $wp_rewrite->rules ) ) {
												$url = $site_url . '/inventory/' . $sale_class . '/' . $parameters[ 'make' ] . '/' . $model_safe . '/';
												$sidebar_content .= '<li><a href="' . $url . '">' . $model . '</a></li>';
											} else {
												$sidebar_content .= '<li><a href="' . @add_query_arg( array( 'make' => $parameters[ 'make' ] , 'model' => $mode_safe ) , $do_not_carry ) . '">' . $model . '</a></li>';
											}
										}
										$sidebar_content .= '<li><span class="no-style"><a href="/inventory/' . $sale_class . '/" class="eagle-filter-prev" title="View ' . $sale_class . ' Vehicles">&#60; All ' . $sale_class . ' Vehicles</a></span></li>';
										$sidebar_content .= '</ul>';
									} else if ( $make_count != 0) {
										$sidebar_content = '<h4 class="" name="vehicles">Makes</h4>';
										$sidebar_content .= '<ul>';
											foreach( $makes as $make ) {
												$make_safe = str_replace( '/' , '%2F' , $make );
												if( !empty( $wp_rewrite->rules ) ) {
													$url = $site_url . '/inventory/' . $sale_class . '/' . $make_safe . '/';
													$url .= isset( $this->parameters[ 'vehicleclass' ] ) ? '?' . http_build_query( array( 'vehicleclass' => $this->parameters[ 'vehicleclass' ] ) ) : NULL;
													$sidebar_content .='<li><a href="' . $url . '">' . $make . '</a></li>';
												} else {
													$sidebar_content .= '<li><a href="' . @add_query_arg( array( 'make' => $make_safe ) , $do_not_carry ) . '">' . $make . '</a></li>';
												}
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
									<li><a rel="nofollow" href="<?php echo @add_query_arg( array( 'price_from' => '0', 'price_to' => '10000' ) , $do_not_carry ); ?>" <?php echo $price_from == "0" ? 'class="active"' : NULL; ?>>$10,000 &#38; Under</a></li>
									<li><a rel="nofollow" href="<?php echo @add_query_arg( array( 'price_from' => '10001', 'price_to' => '20000' ) , $do_not_carry ); ?>" <?php echo $price_from == 10001 ? 'class="active"' : NULL; ?>>$10,001 - $20,000</a></li>
									<li><a rel="nofollow" href="<?php echo @add_query_arg( array( 'price_from' => '20001', 'price_to' => '30000' ) , $do_not_carry ); ?>" <?php echo $price_from == 20001 ? 'class="active"' : NULL; ?>>$20,001 - $30,000</a></li>
									<li><a rel="nofollow" href="<?php echo @add_query_arg( array( 'price_from' => '30001', 'price_to' => '40000' ) , $do_not_carry ); ?>" <?php echo $price_from == 30001 ? 'class="active"' : NULL; ?>>$30,001 - $40,000</a></li>
									<li><a rel="nofollow" href="<?php echo @add_query_arg( array( 'price_from' => '40001', 'price_to' => '50000' ) , $do_not_carry ); ?>" <?php echo $price_from == 40001 ? 'class="active"' : NULL; ?>>$40,001 - $50,000</a></li>
									<li><a rel="nofollow" href="<?php echo @add_query_arg( array( 'price_from' => '50001', 'price_to' => '' ) , $do_not_carry ); ?>" <?php echo $price_from == 50001 ? 'class="active"' : NULL; ?>>$50,001 &#38; Over</a></li>
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
									$make_safe = str_replace( '/' , '%2F' ,  $make );
									$model = urldecode( $inventory_item->model_name );
									$model_safe = str_replace( '/' , '%2F' ,  $model );
									$vin = $inventory_item->vin;
									$trim = urldecode( $inventory_item->trim );
									$trim_safe = str_replace( '/' , '%2F' ,  $trim );
									$engine = $inventory_item->engine;
									$transmission = $inventory_item->transmission;
									$exterior_color = $inventory_item->exterior_color;
									$interior_color = $inventory_item->interior_color;
									$stock_number = $inventory_item->stock_number;
									$odometer = $inventory_item->odometer;
									$icons = $inventory_item->icons;
									$tags = $inventory_item->tags;
									$thumbnail = urldecode( $inventory_item->photos[ 0 ]->small );
									$body_style = $inventory_item->body_style;
									$drive_train = $inventory_item->drive_train;
									$doors = $inventory_item->doors;
									$headline = $inventory_item->headline;
									$saleclass = $inventory_item->saleclass;
									$certified = (!empty($inventory_item->certified) ) ? $inventory_item->certified : 'false';
									$autocheck = isset( $inventory_item->auto_check_url ) ? TRUE : FALSE;
									$video_url = isset( $inventory_item->video_url ) ? $inventory_item->video_url : false;
									$sold = isset($inventory_item->sold_on) ? TRUE : FALSE;

									$form_subject = $year . ' ' . $make . ' ' . $model . ' ' . $stock_number;
									$form_submit_url = $temp_host . '/' . $this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] . '/forms/create/';
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
									<div class="eagle-vehicle" id="<?php echo $vin; ?>">
										<div class="eagle-listing-top"> <!-- Eagle Listing Top -->
											<?php
												if( !empty($custom_settings['display_headlines']) ){
													echo '<div class="eagle-listing-vehicle-headline">' . $headline . '</div>';
												}
											?>
											<div class="eagle-column-left">
												<div class="eagle-main-line">
													<a href="<?php echo $inventory_url; ?>" title="<?php echo $generic_vehicle_title; ?>" class="details">
														<span class="eagle-year"><?php echo $year; ?></span>
														<span class="eagle-make"><?php echo $make; ?></span>
														<span class="eagle-model"><?php echo $model; ?></span>
														<span class="eagle-trim"><?php echo $trim; ?></span>
														<span class="eagle-drive-train"><?php echo $drive_train; ?></span>
														<span class="eagle-body-style"><?php echo $body_style; ?></span>
														<span class="eagle-saleclass" style="display: none;"><?php echo $saleclass; ?></span>
													</a>
												</div>
												<div class="eagle-photo">
													<a href="<?php echo $inventory_url; ?>" title="<?php echo $generic_vehicle_title; ?>">
														<?php echo $sold ? '<img class="marked-sold-overlay" src="http://assets.s3.dealertrend.com.s3.amazonaws.com/images/sold_overlay.png" />' : '' ?>
														<img class="list-image" src="<?php echo $thumbnail; ?>" alt="<?php echo $generic_vehicle_title; ?>" title="<?php echo $generic_vehicle_title; ?>" />
													</a>
												</div>
												<div class="eagle-listing-info">
													<?php
														if( $retail_price > 0 && strtolower( $saleclass ) == 'new' ) {
															echo '<div class="eagle-msrp" alt="' . $retail_price . '"><span>MSRP:</span> ' . '$' . number_format( $retail_price , 0 , '.' , ',' ) . '</div>';
														}
														if ( $odometer > 100 ) {
															echo '<div class="eagle-odometer"><span>Mileage:</span> ' . $odometer . '</div>';
														}
														echo $exterior_color != NULL ? '<div class="eagle-exterior-color"><span>Exterior:</span> ' . $exterior_color . '</div>' : NULL;
														echo $interior_color != NULL ? '<div class="eagle-interior-color"><span>Interior:</span> ' . $interior_color . '</div>' : NULL;
														echo $transmission != NULL ? '<div class="eagle-transmission"><span>Transmission:</span> ' . $transmission . '</div>' : NULL;
													?>
												</div>
											</div>
											<div class="eagle-column-right">
												<div class="eagle-price">
													<?php
														$primary_price = 0;
														if( $on_sale && $sale_price > 0 ) {
															$now_text = 'Price: ';
															$eagle_price_text = ''; //Used to put the Was/Compare At price after the Now/Sale Price value
															if( $use_was_now ) {
																$price_class = ( $use_price_strike_through ) ? 'eagle-strike-through eagle-asking-price' : 'eagle-asking-price';
																if( $incentive_price > 0 ) {
																	$eagle_price_text = '<div class="' . $price_class . ' eagle-ais"><span>Compare At:</span> ' . '$' . number_format( $sale_price , 0 , '.' , ',' ) . '</div>';
																} else {
																	$eagle_price_text = '<div class="' . $price_class . '"><span>Compare At:</span> ' . '$' . number_format( $asking_price , 0 , '.' , ',' ) . '</div>';
																}
																$now_text = 'Sale Price: ';
															}
															if( $incentive_price > 0 ) {
																$eagle_price_text .= '<div class="eagle-ais-incentive-l-text">' . $ais_incentive . '</div>';
																echo '<div class="eagle-sale-price eagle-ais eagle-main-price">' . $now_text . '<span>$' . number_format( $sale_price - $incentive_price , 0 , '.' , ',' ) . '</span></div>';
																if( $sale_expire != NULL ) {
																	$eagle_price_text .= '<div class="eagle-sale-expires">Sale Expires: ' . $sale_expire . '</div>';
																}
																$primary_price = $sale_price - $incentive_price;
															} else {
																if( $ais_incentive != NULL ) {
																	$eagle_price_text .= '<div class="eagle-ais-incentive-l-text">' . $ais_incentive . '</div>';
																}
																echo '<div class="eagle-sale-price eagle-main-price">' . $now_text . '<span>$' . number_format( $sale_price , 0 , '.' , ',' ) . '</span></div>';
																if( $sale_expire != NULL ) {
																	$eagle_price_text .= '<div class="eagle-sale-expires">Sale Expires: ' . $sale_expire . '</div>';
																}
																$primary_price = $sale_price;
															}
															echo $eagle_price_text;
														} else {
															if( $asking_price > 0 ) {
																if( $incentive_price > 0 ) {
																	echo '<div class="eagle-your-price eagle-ais eagle-main-price">Sale Price: <span>$' . number_format( $asking_price - $incentive_price , 0 , '.' , ',' ) . '</span></div>';
																	echo '<div class="eagle-asking-price eagle-ais"><span>Compare At:</span> $' . number_format( $asking_price , 0 , '.' , ',' ) . '</div>';
																	echo '<div class="eagle-ais-incentive-l-text">' . $ais_incentive . '</div>';
																	$primary_price = $asking_price - $incentive_price;
																} else {
																	if( $ais_incentive != NULL ) {
																		echo '<div class="eagle-ais-incentive-l-text">' . $ais_incentive . '</div>';
																	}
																	echo '<div class="eagle-asking-price eagle-main-price">Price: <span>$' . number_format( $asking_price , 0 , '.' , ',' ) . '</span></div>';
																	$primary_price = $asking_price;
																}
															} else {
																if( $ais_incentive != NULL ) {
																	echo '<div class="eagle-ais-incentive-l-text">' . $ais_incentive . '</div>';
																}
																echo '<div class="eagle-no-price eagle-main-price">' . $default_price_text . '</div>';
															}
														}

														if( $ais_incentive != NULL && isset( $company_information->api_keys ) ) {
															$value_ais = '<div class="eagle-ais-incentive-s-text view-available-rebates">';
															$value_ais .= '<a href="http://onecar.aisrebates.com/dlr2/inline/IncentiveOutput.php?vID='. $vin . '&wID=' . $company_information->api_keys->ais . '&zID=' . $company_information->zip . '" target="_blank" title="VIEW AVAILABLE INCENTIVES AND REBATES" onclick="return loadIframe( this.href );">VIEW AIS</a>';
															$value_ais .= '</div>';
															echo $value_ais;
														}

														//Clean Price Values
														echo '<div class="hidden-vehicle-prices">';
														echo ( strtolower( $saleclass ) == 'new' ) ? '<div class="hidden-msrp" alt="msrp">'.$retail_price.'</div>' : '';
														echo ( $incentive_price > 0 ) ? '<div class="hidden-rebate" alt="rebate">'.$incentive_price.'</div>' : '';
														echo '<div class="hidden-sale" alt="sale">'.$sale_price.'</div>';
														echo '<div class="hidden-asking" alt="asking">'.$asking_price.'</div>';
														echo '<div class="hidden-main" alt="main">'.$primary_price.'</div>';
														echo ( $sale_price > 0 && ($asking_price - $sale_price) != 0 ) ? '<div class="hidden-discount" alt="discount">'. ($asking_price - $sale_price) .'</div>' : '';
														echo '</div>';
													?>
												</div>
												<div class="eagle-detail-button eagle-show-form" name="Get Your ePrice">
													<a>GET YOUR ePRICE</a>
												</div>
												<div class="eagle-hidden-form-values" style="display: none;">
													<span class="eagle-hidden-form-value eagle-form-price"><?php echo $primary_price; ?></span>
													<span class="eagle-hidden-form-value eagle-form-year"><?php echo $year; ?></span>
													<span class="eagle-hidden-form-value eagle-form-make"><?php echo $make; ?></span>
													<span class="eagle-hidden-form-value eagle-form-model"><?php echo $model; ?></span>
													<span class="eagle-hidden-form-value eagle-form-trim"><?php echo $trim; ?></span>
													<span class="eagle-hidden-form-value eagle-form-saleclass"><?php echo $saleclass; ?></span>
													<span class="eagle-hidden-form-value eagle-form-stock"><?php echo $stock_number; ?></span>
													<span class="eagle-hidden-form-value eagle-form-vin"><?php echo $vin; ?></span>
													<span class="eagle-hidden-form-value eagle-form-vehicle"><?php echo $generic_vehicle_title; ?></span>
													<span class="eagle-hidden-form-value eagle-form-inventory"><?php echo $inventory_item->id; ?></span>
													<span class="eagle-hidden-form-value eagle-form-subject-post"><?php echo $form_subject; ?></span>
													<span class="eagle-hidden-form-value eagle-form-url"><?php echo '&#39;' . $form_submit_url . strtolower( $saleclass ) . '_vehicle_inquiry&#39;'; ?></span>
												</div>

												<?php
													if( $autocheck ){
														echo display_autocheck_image( $vin, $saleclass, $type );
													}
												?>
											</div>
										</div>
										<div class="eagle-listing-bottom"> <!-- Eagle Listing Bottom -->
											<?php
												if( !empty( $custom_settings[ 'display_tags' ] ) ){
													apply_special_tags( $tags, $on_sale, $certified, $video_url);
													if( !empty( $tags ) ){
														echo '<div class="eagle-listing-tags">';
															$tag_icons = build_tag_icons( $default_tag_names, $custom_tag_icons, $tags);
															echo $tag_icons;
														echo '</div>';
													}
												}
											?>
											<div class="eagle-listing-buttons">
												<div class="eagle-listing-button eagle-show-form eagle-confirm-button" name="Confirm Availability">Confirm Availability</div>
												<div class="eagle-listing-button eagle-show-form eagle-question-button" name="Ask a Question">Ask a Question</div>
												<div class="eagle-listing-button eagle-details-button"><a href="<?php echo $inventory_url; ?>">View More Details</a></div>
												<div class="eagle-hidden-form-values" style="display: none;">
													<span class="eagle-hidden-form-value eagle-form-price"><?php echo $primary_price; ?></span>
													<span class="eagle-hidden-form-value eagle-form-year"><?php echo $year; ?></span>
													<span class="eagle-hidden-form-value eagle-form-make"><?php echo $make; ?></span>
													<span class="eagle-hidden-form-value eagle-form-model"><?php echo $model; ?></span>
													<span class="eagle-hidden-form-value eagle-form-trim"><?php echo $trim; ?></span>
													<span class="eagle-hidden-form-value eagle-form-saleclass"><?php echo $saleclass; ?></span>
													<span class="eagle-hidden-form-value eagle-form-stock"><?php echo $stock_number; ?></span>
													<span class="eagle-hidden-form-value eagle-form-vin"><?php echo $vin; ?></span>
													<span class="eagle-hidden-form-value eagle-form-vehicle"><?php echo $generic_vehicle_title; ?></span>
													<span class="eagle-hidden-form-value eagle-form-inventory"><?php echo $inventory_item->id; ?></span>
													<span class="eagle-hidden-form-value eagle-form-subject-post"><?php echo $form_subject; ?></span>
													<span class="eagle-hidden-form-value eagle-form-url"><?php echo '&#39;' . $form_submit_url . strtolower( $saleclass ) . '_vehicle_inquiry&#39;'; ?></span>

												</div>
											</div>
											<div class="eagle-stock-number" alt="<?php echo $stock_number; ?>">
												Stock # <?php echo $stock_number; ?>
											</div>
											<div class="eagle-mobile-bottom">
												<div class="eagle-main-price"><?php if (!empty($primary_price)){ echo 'Price: <span>$' . number_format( $primary_price , 0 , '.' , ',' ) . '</span>';} else {echo $default_price_text;}  ?></div>
												<div class="eagle-detail-button">
													<a href="<?php echo $inventory_url; ?>">GET YOUR ePRICE</a>
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
				<?php echo !empty( $inventory ) ? '<p>' . $inventory[ 0 ]->disclaimer . '</p>' : NULL; ?>
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
