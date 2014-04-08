<?php
namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

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

?>

<div id="armadillo-wrapper">
	<br class="armadillo-clear" id="armadillo-top" />
	<div id="armadillo-listing">
		<?php echo $breadcrumbs; ?>
		<div class="armadillo-pager">
			<?php echo paginate_links( $args ); ?>
		</div>
		<form action="<?php echo $inventory_base; ?>" method="GET" id="armadillo-search">
			<?php echo empty( $wp_rewrite->rules ) ? '<input type="hidden" value="inventory" name="taxonomy" />' : NULL; ?>
			<label for="search">Inventory Search:</label>
			<input id="armadillo-search-box" name="search" value="<?php echo isset( $parameters[ 'search' ] ) ? $parameters[ 'search' ] : NULL; ?>" />
		</form>

			<div id="armadillo-sorting-columns">
			<div id="armadillo-total-found"><?php echo $total_found; ?> Cars Found</div>
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

		<div id="armadillo-listing-sidebar">

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
					<li class="armadillo-expanded">
						<span>Sale Class</span>
						<ul>
							<?php switch( $sale_class_filter ) {
								case 'all':
									echo '<li><span class="no-style"><a href="' . $new . '" title="View New Inventory" class="jquery-ui-button ' . (strtolower( $sale_class ) == 'new' ? 'disabled' : NULL) . '">New</a></span></li>';
									echo '<li><span class="no-style"><a href="' . $used . '" title="View Used Inventory" class="jquery-ui-button ' . (strtolower( $sale_class ) == 'used' ? 'disabled' : NULL) . '">Used</a></span></li>';
									break;
								case 'new':
									echo '<li><span class="no-style"><a href="' . $new . '" title="View New Inventory" class="jquery-ui-button ' . (strtolower( $sale_class ) == 'new' ? 'disabled' : NULL) . '">New</a></span></li>';
									break;
								case 'used':
									echo '<li><span class="no-style"><a href="' . $used . '" title="View Used Inventory" class="jquery-ui-button ' . (strtolower( $sale_class ) == 'used' ? 'disabled' : NULL) . '">Used</a></span></li>';
									break;
								case 'certified':
									echo '<li><span class="no-style"><a href="' . $used . '" title="View Certified Used Inventory" class="jquery-ui-button ' . (strtolower( $sale_class ) == 'used' ? 'disabled' : NULL) . '">Certified</a></span></li>';
									break;
							}
							?>
						</ul>
					</li>
				</ul>
				<ul>
					<?php
						if( !isset( $parameters[ 'model' ] ) || strtolower( $parameters[ 'model' ] ) == 'all' ):
					?>
					<li class="armadillo-expanded">
						<span>Body Style</span>
						<ul>
							<li><a href="<?php echo add_query_arg( array( 'vehicleclass' => 'car' ) , $do_not_carry ); ?>" <?php echo $vehicleclass == 'car' ? 'class="active"' : NULL; ?>>Car</a></li>
							<li><a href="<?php echo add_query_arg( array( 'vehicleclass' => 'truck' ) , $do_not_carry ); ?>" <?php echo $vehicleclass == 'truck' ? 'class="active"' : NULL; ?>>Truck</a></li>
							<li><a href="<?php echo add_query_arg( array( 'vehicleclass' => 'sport_utility' ) , $do_not_carry ); ?>" <?php echo $vehicleclass == 'sport_utility' ? 'class="active"' : NULL; ?>>SUV</a></li>
							<li><a href="<?php echo add_query_arg( array( 'vehicleclass' => 'van,minivan' ) , $do_not_carry ); ?>" <?php echo $vehicleclass == 'van,minivan' ? 'class="active"' : NULL; ?>>Van</a></li>
						</ul>
					</li>
					<?php
						endif;
						if( ! isset( $parameters[ 'make' ] ) || strtolower( $parameters[ 'make' ] ) == 'all' ) {
							$vehicle_management_system->tracer = 'Obtaining a list of makes for the sidebar.';

							if ( strcasecmp( $sale_class, 'new') == 0 && !empty( $new_makes_filter ) ) {
								$makes = $new_makes_filter;
							} else {
								$makes = $vehicle_management_system->get_makes()->please( array_merge( array( 'saleclass' => $sale_class ) , $filters ) );
								$makes = json_decode( $makes[ 'body' ] );
							}

							$make_count = count ( $makes );
							if( $make_count > 1 ) {
					?>
					<li class="armadillo-expanded">
						<span>Make</span>
						<ul>
							<?php
								foreach( $makes as $make ) {
									$make_safe = str_replace( '/' , '%2F' , $make );
									if( !empty( $wp_rewrite->rules ) ) {
										$url = $site_url . '/inventory/' . $sale_class . '/' . $make_safe . '/';
										$url .= isset( $this->parameters[ 'vehicleclass' ] ) ? '?' . http_build_query( array( 'vehicleclass' => $this->parameters[ 'vehicleclass' ] ) ) : NULL;
										echo '<li><a href="' . $url . '">' . $make . '</a></li>';
									} else {
										echo '<li><a href="' . @add_query_arg( array( 'make' => $make_safe ) , $do_not_carry ) . '">' . $make . '</a></li>';
									}
								}
							?>
						</ul>
					</li>
					<?php
							} else {
								if( $make_count == 1 ) {
									$parameters[ 'make' ] = $makes[ 0 ];
								}
							}
						}
						if( ( !isset( $parameters[ 'model' ] ) || strtolower( $parameters[ 'model' ] ) == 'all' ) && isset( $parameters[ 'make' ] ) ) {
							$tmp_do_not_carry = remove_query_arg( 'make' , $do_not_carry );
							$make_url = ! empty( $wp_rewrite->rules ) ? $site_url . '/inventory/' . $sale_class . '/' : @add_query_arg( array( 'saleclass' => $sale_class ) , $tmp_do_not_carry );
							$models = $vehicle_management_system->get_models()->please( array_merge( array( 'saleclass' => $sale_class , 'make' => $parameters[ 'make' ] ) , $filters ) );
							$models = json_decode( $models[ 'body' ] );
							$model_count = count( $models );
							if( $model_count > 1 ) {
					?>
					<li class="armadillo-expanded">
						<span>Model</span>
						<ul>
							<?php
								foreach( $models as $model ) {
									$model_safe = str_replace( '/' , '%2F' , $model );
									if( !empty( $wp_rewrite->rules ) ) {
										$url = $site_url . '/inventory/' . $sale_class . '/' . $parameters[ 'make' ] . '/' . $model_safe . '/';
										echo '<li><a href="' . $url . '">' . $model . '</a></li>';
									} else {
										echo '<li><a href="' . @add_query_arg( array( 'make' => $parameters[ 'make' ] , 'model' => $mode_safe ) , $do_not_carry ) . '">' . $model . '</a></li>';
									}
								}
								echo '<li><span class="no-style"><a href="' . $make_url . '" class="jquery-ui-button" title="View ' . $sale_class . ' Vehicles">Previous</a></span></li>';
							?>
						</ul>
					</li>
					<?php
							} else {
								if( $model_count == 1 ) {
									$parameters[ 'model' ] = $models[ 0 ];
								}
							}
						}
						if( ( !isset( $parameters[ 'trim' ] ) || strtolower( $parameters[ 'trim' ] ) == 'all' ) && isset( $parameters[ 'model' ] ) ) {
							$tmp_do_not_carry = remove_query_arg( 'model' , $do_not_carry );
							$model_url = ! empty( $wp_rewrite->rules ) ? $site_url . '/inventory/' . $sale_class . '/' : @add_query_arg( array( 'saleclass' => $sale_class , 'make' => $parameters[ 'make' ] ) , $tmp_do_not_carry );
							$trims = $vehicle_management_system->get_trims()->please( array_merge( array( 'saleclass' => $sale_class , 'make' => $parameters[ 'make' ] , 'model' => $parameters[ 'model' ] ) , $filters ) );
							$trims = json_decode( $trims[ 'body' ] );
							$trim_count = count( $trims );
							if( $trim_count > 1 ) {
					?>
					<li class="armadillo-expanded">
						<span>Trim</span>
						<ul>
							<?php
								foreach( $trims as $trim ) {
									$trim_safe = $trim;
									$trim_safe = str_replace( '/' , '%2F' , $trim_safe );
									echo '<li><a href="' . @add_query_arg( array( 'make' => $parameters[ 'make' ] , 'model' => $parameters[ 'model' ] , 'trim' => $trim_safe ) , $do_not_carry ) . '">' . $trim . '</a></li>';
								}
								echo '<li><span class="no-style"><a href="' . $model_url . '" class="jquery-ui-button" title="View ' . $parameters[ 'make' ] . ' Models">Previous</a></span></li>';
							?>
						</ul>
					</li>
					<?php
							}
						};
					?>
					<li class="armadillo-expanded">
						<span>Price</span>
						<ul>
							<li><a rel="nofollow" href="<?php echo @add_query_arg( array( 'price_from' => '0', 'price_to' => '10000' ) , $do_not_carry ); ?>" <?php echo $price_from == "0" ? 'class="active"' : NULL; ?>>$0 - $10,000</a></li>
							<li><a rel="nofollow" href="<?php echo @add_query_arg( array( 'price_from' => '10001', 'price_to' => '20000' ) , $do_not_carry ); ?>" <?php echo $price_from == 10001 ? 'class="active"' : NULL; ?>>$10,001 - $20,000</a></li>
							<li><a rel="nofollow" href="<?php echo @add_query_arg( array( 'price_from' => '20001', 'price_to' => '30000' ) , $do_not_carry ); ?>" <?php echo $price_from == 20001 ? 'class="active"' : NULL; ?>>$20,001 - $30,000</a></li>
							<li><a rel="nofollow" href="<?php echo @add_query_arg( array( 'price_from' => '30001', 'price_to' => '40000' ) , $do_not_carry ); ?>" <?php echo $price_from == 30001 ? 'class="active"' : NULL; ?>>$30,001 - $40,000</a></li>
							<li><a rel="nofollow" href="<?php echo @add_query_arg( array( 'price_from' => '40001', 'price_to' => '50000' ) , $do_not_carry ); ?>" <?php echo $price_from == 40001 ? 'class="active"' : NULL; ?>>$40,001 - $50,000</a></li>
							<li><a rel="nofollow" href="<?php echo @add_query_arg( array( 'price_from' => '50001', 'price_to' => '' ) , $do_not_carry ); ?>" <?php echo $price_from == 50001 ? 'class="active"' : NULL; ?>>$50,001 - &amp; Above</a></li>
						</ul>
					</li>
					<?php
						if( !isset( $parameters[ 'certified' ] ) && ( !isset( $parameters[ 'saleclass' ] ) || strtolower( $parameters[ 'saleclass' ] ) != 'new' ) ):
					?>
					<li class="armadillo-expanded">
						<span>Other</span>
						<ul>
							<li><a rel="nofollow" href="<?php echo @add_query_arg( array( 'certified' => 'yes' ) , $do_not_carry ); ?>">Certified Pre-Owned</a></li>
						</ul>
					</li>
					<?php endif; ?>
				</ul>
				<?php endif; ?>
			</div>
		</div>
		<div id="armadillo-listing-content">

			<div id="armadillo-listing-items">
				<?php
					if( empty( $inventory ) ) {
						echo '<div class="armadillo-not-found"><h2><strong>Unable to find inventory items that matched your search criteria.</strong></h2><a onClick="history.go(-1)" title="Return to Previous Search" class="jquery-ui-button">Return to Previous Search</a></div>';
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
							$certified_inv = $inventory_item->certified;
							$thumbnail = urldecode( $inventory_item->photos[ 0 ]->small );
							$body_style = $inventory_item->body_style;
							$drive_train = $inventory_item->drive_train;
							$doors = $inventory_item->doors;
							$headline = $inventory_item->headline;
							$saleclass_item = $inventory_item->saleclass;
							$autocheck = isset( $inventory_item->auto_check_url ) ? TRUE : FALSE;
							$certified = (!empty($inventory_item->certified) ) ? $inventory_item->certified : 'false';
							if( !empty( $wp_rewrite->rules ) ) {
								$inventory_url = $site_url . '/inventory/' . $year . '/' . $make_safe . '/' . $model_safe . '/' . $state . '/' . $city . '/'. $vin . '/';
							} else {
								$inventory_url = '?taxonomy=inventory&amp;saleclass=' . $sale_class . '&amp;make=' . $make_safe . '&amp;model=' . $model_safe . '&amp;state=' . $state . '&amp;city=' . $city . '&amp;vin='. $vin;
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
									<?php
										if( strlen( trim( $headline ) ) > 0 ) {
											echo '<div class="armadillo-headline">' . $headline . '</div>';
										}
									?>
									<?php
										$ais_incentive = isset( $inventory_item->ais_incentive->to_s ) ? $inventory_item->ais_incentive->to_s : NULL;
										$incentive_price = 0;
										if( $ais_incentive != NULL ) {
											preg_match( '/\$\d*(\s)?/' , $ais_incentive , $incentive );
											$incentive_price = isset( $incentive[ 0 ] ) ? str_replace( '$' , NULL, $incentive[ 0 ] ) : 0;
										}
									?>
									<div class="armadillo-details-left">
										<?php
											if( $retail_price > 0 ) {
												echo '<div class="armadillo-msrp">MSRP: ' . '$' . number_format( $retail_price , 2 , '.' , ',' ) . '</div>';
											}
											echo $interior_color != NULL ? '<span class="armadillo-interior-color">Int. Color: ' . $interior_color . '</span>' : NULL;
											echo $exterior_color != NULL ? '<span class="armadillo-exterior-color">Ext. Color: ' . $exterior_color . '</span>' : NULL;
										?>
										<?php if( $transmission != NULL ) { ?>
										<span class="armadillo-transmission">Trans: <?php echo $transmission; ?></span>
										<?php } ?>
									</div>
									<div class="armadillo-details-right">
										<span class="armadillo-stock-number">Stock #: <?php echo $stock_number; ?></span>
										<span class="armadillo-odometer">Odometer: <?php echo $odometer; ?></span>
										<span class="armadillo-vin">VIN: <?php echo $vin; ?></span>
									</div>
									<?php
										apply_special_tags( $tags, $on_sale, $certified);
										if( !empty( $tags ) ){
											echo '<div class="armadillo-icons">';
												$tag_icons = build_tag_icons( $default_tag_names, $custom_tag_icons, $tags);
												echo $tag_icons;
											echo '</div>';
										}
									?>

									<?php
										if( $ais_incentive != NULL && isset( $company_information->api_keys ) ) {
									?>
									<div class="armadillo-ais-incentive view-available-rebates">
										<a href="http://onecar.aisrebates.com/dlr2/inline/IncentiveOutput.php?vID=<?php echo $vin; ?>&wID=<?php echo $company_information->api_keys->ais; ?>&zID=<?php echo $company_information->zip; ?>" target="_blank" title="VIEW AVAILABLE INCENTIVES AND REBATES" onclick="return loadIframe( this.href );">
											VIEW AVAILABLE INCENTIVES AND REBATES
										</a>
										<?php
										?>
									</div>
									<?php } ?>
									<div class="armadillo-price">
										<?php
											if( $on_sale && $sale_price > 0 ) {
												$now_text = 'Price: ';
												if( $use_was_now ) {
													$price_class = ( $use_price_strike_through ) ? 'armadillo-strike-through armadillo-asking-price' : 'armadillo-asking-price';
													if( $incentive_price > 0 ) {
														echo '<div class="' . $price_class . '">Was: ' . '$' . number_format( $sale_price , 2 , '.' , ',' ) . '</div>';
													} else {
														echo '<div class="' . $price_class . '">Was: ' . '$' . number_format( $asking_price , 2 , '.' , ',' ) . '</div>';
													}
													$now_text = 'Now: ';
												}
												if( $incentive_price > 0 ) {
													echo '<div class="armadillo-ais-incentive">Savings: ' . $ais_incentive . '</div>';
													echo '<div class="armadillo-sale-price">' . $now_text . '$' . number_format( $sale_price - $incentive_price , 2 , '.' , ',' ) . '</div>';
													if( $sale_expire != NULL ) {
														echo '<div class="armadillo-sale-expires">Sale Expires: ' . $sale_expire . '</div>';
													}
												} else {
													if( $ais_incentive != NULL ) {
														echo '<div class="armadillo-ais-incentive">Savings: ' . $ais_incentive . '</div>';
													}
													echo '<div class="armadillo-sale-price">' . $now_text . '$' . number_format( $sale_price , 2 , '.' , ',' ) . '</div>';
													if( $sale_expire != NULL ) {
														echo '<div class="armadillo-sale-expires">Sale Expires: ' . $sale_expire . '</div>';
													}
												}
											} else {
												if( $asking_price > 0 ) {
													if( $incentive_price > 0 ) {
														echo '<div class="armadillo-asking-price" style="font-size:12px;">Asking Price: $' . number_format( $asking_price , 2 , '.' , ',' ) . '</div>';
														echo '<div class="armadillo-ais-incentive">Savings: ' . $ais_incentive . '</div>';
														echo '<div class="armadillo-asking-price" style="font-size:16px;">Your Price: $' . number_format( $asking_price - $incentive_price , 2 , '.' , ',' ) . '</div>';
													} else {
														if( $ais_incentive != NULL ) {
															echo '<div class="armadillo-ais-incentive">Savings: ' . $ais_incentive . '</div>';
														}
														echo '<div class="armadillo-asking-price">Price: $' . number_format( $asking_price , 2 , '.' , ',' ) . '</div>';
													}
												} else {
													if( $ais_incentive != NULL ) {
														echo '<div class="armadillo-ais-incentive">Savings: ' . $ais_incentive . '</div>';
													}
													echo $default_price_text;
												}
											}
										?>
										<a href="<?php echo $inventory_url; ?>" title="More Information: <?php echo $generic_vehicle_title; ?>">More Information</a>
									</div>
									<div class="armadillo-contact-information">
										<?php
											if ( strtolower( $saleclass_item ) == 'new' && !empty( $phone_new ) ) {
												$phone_value = $phone_new;
											} elseif ( strtolower( $saleclass_item ) == 'used' && !empty( $phone_used ) ) {
												$phone_value = $phone_used;
											} else {
												$phone_value = $contact_information->phone;
											}
											echo $contact_information->company_id != $company_information->id ? $contact_information->dealer_name . ' - ' . $phone_value : NULL;
										?>
									</div>
									<?php
										if( $autocheck ){
											echo display_autocheck_image( $vin, $saleclass_item, $type );
										}
									?>
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
