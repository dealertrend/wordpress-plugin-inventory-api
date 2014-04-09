<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

	$sale_class = str_replace( ' ' , '%20' , $inventory->saleclass );
	$prices = $inventory->prices;
	$use_was_now = $prices->{ 'use_was_now?' };
	$use_price_strike_through = $prices->{ 'use_price_strike_through?' };
	$on_sale = $prices->{ 'on_sale?' };
	$sale_price = isset( $prices->sale_price ) ? $prices->sale_price : NULL;
	$sale_expire = isset( $prices->sale_expire ) ? $prices->sale_expire : NULL;
	$retail_price = $prices->retail_price;
	$default_price_text = $prices->default_price_text;
	$asking_price = $prices->asking_price;
	$vin = $inventory->vin;
	$odometer = empty( $inventory->odometer ) || $inventory->odometer <= 0 ? 'N/A' : $inventory->odometer;
	$stock_number = $inventory->stock_number;
	$exterior_color = empty( $inventory->exterior_color ) ? '' : $inventory->exterior_color;
	$interior_color = empty( $inventory->interior_color ) ? '' : $inventory->interior_color;
	$engine = $inventory->engine;
	$transmission = $inventory->transmission;
	$drivetrain = $inventory->drive_train;
	$dealer_options = $inventory->dealer_options;
	$standard_equipment = $inventory->standard_equipment;
	$year = $inventory->year;
	$make = urldecode( $inventory->make );
	$model = urldecode( $inventory->model_name );
	$trim = urldecode( $inventory->trim );
	$year_make_model = $year . ' ' . $make . ' ' . $model;
	$description = $inventory->description;
	$doors = $inventory->doors;
	$icons = $inventory->icons;
	$tags = $inventory->tags;
	$fuel_economy = $inventory->fuel_economy;
	$headline = $inventory->headline;
	$body_style = $inventory->body_style;
	$drive_train = $inventory->drive_train;
	$vehicle_location = $inventory->vehicle_location;
	$video_url = isset( $inventory->video_url ) ? $inventory->video_url : false;
	$carfax = isset( $inventory->carfax ) ? $inventory->carfax->url : false;
	$contact_information = $inventory->contact_info;
	$greeting = isset( $contact_information->greeting ) ? $contact_information->greeting : NULL;
	$dealer_name = isset( $contact_information->dealer_name ) ? $contact_information->dealer_name : NULL;
	$phone = isset( $contact_information->phone ) ? $contact_information->phone : NULL;
	$internet_manager = isset( $contact_information->internet_manager ) ? $contact_information->internet_manager : NULL;
	$certified = (!empty($inventory->certified) ) ? $inventory->certified : 'false';
	$vehicle_class = $inventory->vehicleclass;
	$acode = $inventory->ads_acode;
	$primary_price = 0;
	$autocheck = isset( $inventory->auto_check_url ) ? TRUE : FALSE;

	$traffic_source = isset( $_COOKIE[ 'dealertrend-traffic-source' ] ) ? $_COOKIE[ 'dealertrend-traffic-source' ] : false;
	$traffic_source = $this->sanitize_inputs( $traffic_source );

	usort($dealer_options, 'sort_length' );

	$form_subject = $year . ' ' . $make . ' ' . $model . ' ' . $stock_number;
	$form_submit_url = $temp_host . '/' . $this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] . '/forms/create/';

?>

<div id="eagle-wrapper">
	<div id="eagle-detail">
		<div id="eagle-top"> <!-- Eagle Top -->
			<?php echo $breadcrumbs; ?>
			<div id="eagle-top-info">
				<div id="eagle-headline-wrap">
					<div id="eagle-main-headline">
						<h2>
							<span class="eagle-saleclass" style="display: none;"><?php echo $sale_class; ?></span>
							<span class="eagle-year"><?php echo $year; ?></span>
							<span class="eagle-make"><?php echo $make; ?></span>
							<span class="eagle-model"><?php echo $model; ?></span>
							<span class="eagle-trim"><?php echo $trim; ?></span>
							<span class="eagle-drive-train"><?php echo $drive_train; ?></span>
							<span class="eagle-transmission"><?php echo $transmission; ?></span>
							<span class="eagle-body-style"><?php echo $body_style; ?></span>
						</h2>
					</div>
					<div id="eagle-sub-headline">
						<?php
							if( empty( $custom_settings[ 'remove_sub_headline_d' ] ) ){
						?>
						<h3>
							<span class="eagle-make"><?php echo $make; ?></span>
							<span class="eagle-model"><?php echo $model; ?></span>
							<span class="eagle-trim"><?php echo $trim; ?></span>
							<span class="eagle-city"><?php echo $company_information->city; ?></span>,
							<span class="eagle-state"><?php echo $company_information->state; ?></span>

						</h3>
						<?php
							}
						?>

						<?php
							if( !empty( $custom_settings[ 'display_vehicle_location_sub_headline_d' ] ) ){
						?>
						<h3>
							<span class="eagle-sh-vehicle-location"><?php echo $vehicle_location; ?></span>
						</h3>
						<?php
							}
						?>
					</div>
				</div>
				<div id="eagle-top-price">
					<div class="eagle-price">
						<?php
							// AIS Info
							$ais_incentive = isset( $inventory->ais_incentive->to_s ) ? $inventory->ais_incentive->to_s : NULL;
							$incentive_price = 0;
							if( $ais_incentive != NULL ) {
								preg_match( '/\$\d*(\s)?/' , $ais_incentive , $incentive );
								$incentive_price = isset( $incentive[ 0 ] ) ? str_replace( '$' , NULL, $incentive[ 0 ] ) : 0;
							}
							if( $on_sale && $sale_price > 0 ) {
								$now_text = 'No Stress Price: ';
								$eagle_price_text = ''; //Used to put the Was/Compare At price after the Now/Sale Price value
								if( $use_was_now ) {
									$price_class = ( $use_price_strike_through ) ? 'eagle-strike-through eagle-asking-price' : 'eagle-asking-price';
									if( $incentive_price > 0 ) {
										echo '<div class="' . $price_class . ' eagle-ais">Compare At: ' . '<span>$' . number_format( $sale_price , 0 , '.' , ',' ) . '</span></div>';
										$primary_price = $sale_price;
									} else {
										echo '<div class="' . $price_class . '"><span>Compare At:</span> ' . '<span>$' . number_format( $asking_price , 0 , '.' , ',' ) . '</span></div>';
										$primary_price = $asking_price;
									}
									$now_text = 'No Stress Price: ';
								}
								if( $incentive_price > 0 ) {
									$eagle_price_text .= '<div class="eagle-ais-incentive-l-text">' . $ais_incentive . '</div>';
									echo '<div class="eagle-sale-price eagle-ais eagle-main-price">' . $now_text . '<br><span>$' . number_format( $sale_price - $incentive_price , 0 , '.' , ',' ) . '</span></div>';
									$primary_price = $sale_price - $incentive_price;
									if( $sale_expire != NULL ) {
										$eagle_price_text .= '<div class="eagle-sale-expires">Sale Expires: ' . $sale_expire . '</div>';
									}
								} else {
									if( $ais_incentive != NULL ) {
										$eagle_price_text .= '<div class="eagle-ais-incentive-l-text">' . $ais_incentive . '</div>';
									}
									echo '<div class="eagle-sale-price eagle-main-price">' . $now_text . '<br><span>$' . number_format( $sale_price , 0 , '.' , ',' ) . '</span></div>';
									if( $sale_expire != NULL ) {
										$eagle_price_text .= '<div class="eagle-sale-expires">Sale Expires: ' . $sale_expire . '</div>';
									}
									$primary_price = $sale_price;
								}
								echo $eagle_price_text;
							} else {
								if( $asking_price > 0 ) {
									if( $incentive_price > 0 ) {
										echo '<div class="eagle-asking-price eagle-ais">Compare At: <span>$' . number_format( $asking_price , 0 , '.' , ',' ) . '</span> </div>';
										echo '<div class="eagle-your-price eagle-ais eagle-main-price">No Stress Price:<br><span>$' . number_format( $asking_price - $incentive_price , 0 , '.' , ',' ) . '</span></div>';
										echo '<div class="eagle-ais-incentive-l-text">' . $ais_incentive . '</div>';
										$primary_price = $asking_price - $incentive_price;
									} else {
										echo '<div class="eagle-asking-price eagle-main-price">No Stress Price:<br><span>$' . number_format( $asking_price , 0 , '.' , ',' ) . '</span></div>';
										if( $ais_incentive != NULL ) {
											echo '<div class="eagle-ais-incentive-l-text">' . $ais_incentive . '</div>';
										}
										$primary_price = $asking_price;
									}
								} else {
									echo '<div class="eagle-no-price eagle-main-price">' . $default_price_text . '</div>';
									if( $ais_incentive != NULL ) {
										echo '<div class="eagle-ais-incentive-l-text">' . $ais_incentive . '</div>';
									}
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
							echo ( strtolower( $sale_class ) == 'new' ) ? '<div class="hidden-msrp" alt="msrp">'.$retail_price.'</div>' : '';
							echo ( $incentive_price > 0 ) ? '<div class="hidden-rebate" alt="rebate">'.$incentive_price.'</div>' : '';
							echo '<div class="hidden-sale" alt="sale">'.$sale_price.'</div>';
							echo '<div class="hidden-asking" alt="asking">'.$asking_price.'</div>';
							echo '<div class="hidden-main" alt="main">'.$primary_price.'</div>';
							echo ( $sale_price > 0 && ($asking_price - $sale_price) != 0 ) ? '<div class="hidden-discount" alt="discount">'. ($asking_price - $sale_price) .'</div>' : '';
							echo '</div>';
						?>
					</div>
					<div id="eagle-get-price">
						<div class="eagle-get-price-button eagle-show-form" name="Send Me The ePrice">GET YOUR ePRICE</div>
					</div>
					<?php
						if( !empty( $custom_settings[ 'display_tags' ] ) ){
							apply_special_tags( $tags, $on_sale, $certified, $video_url);
							if( !empty( $tags ) ){
								echo '<div class="eagle-detail-tags">';
									$tag_icons = build_tag_icons( $default_tag_names, $custom_tag_icons, $tags);
									echo $tag_icons;
								echo '</div>';
							}
						}
					?>
				</div>
				<?php
					if( !empty($custom_settings['display_headlines']) ){
						if ( !empty( $headline ) ){
							$eagle_value = '<div id="eagle-custom-headline">' . $headline . '</div>';
							echo $eagle_value;
						}
					}
				?>
			</div>
		</div>
		<div id="eagle-content"> <!-- Eagle Content -->
			<div id="eagle-content-top"> <!-- Eagle Content Top -->
				<div id="eagle-content-headline">
					<span class="eagle-year"><?php echo $year; ?></span>
					<span class="eagle-make"><?php echo $make; ?></span>
					<span class="eagle-model"><?php echo $model; ?></span>
					<span class="eagle-text">Photos:</span>
					<a id="friendly-print" onclick="window.open('?print_page','popup','width=800,height=900,scrollbars=yes,resizable=no,toolbar=no,directories=no,location=no,menubar=yes,status=no,left=0,top=0'); return false">Print</a>
				</div>
			</div>
			<div id="eagle-content-center"> <!-- Eagle Content Center -->
				<div id="eagle-content-detail-left"> <!-- Eagle Content Detail Left -->
					<div id="eagle-image-wrapper"> <!-- Eagle Image Wrapper -->
						<?php
							if( count( $inventory->photos ) ) {
								$value = '<div id="eagle-images">';

								foreach( $inventory->photos as $photo ) {
									$value .= '<a class="lightbox" rel="slides" href="' . str_replace( '&' , '&amp;' , $photo->large ) . '" title="' . $company_name . '">';
									$value .= '<img src="' . str_replace( '&' , '&amp;' , $photo->medium ) . '" alt="" />';
									$value .= '</a>';
								}

								$value .= '</div>';
							}
							echo $value;

							if( count( $inventory->photos > 1 ) ) {
								$value = '';
								$value .= '<div id="eagle-nav-wrapper">';
								$value .= '<div id="eagle-nav-images"></div>';
								$value .= '</div>';

								echo $value;
							}
						?>
						<div id="eagle-photo-extra">
							<div id="eagle-photo-enlarge">Click Image to Enlarge</div>
							<?php
								if( $video_url ) {
									$value = '<div id="eagle-video-button">';
									$value .= '<a onClick="return video_popup(this, \'' . $year_make_model . '\')" href="' . $video_url . '">Play Video</a>';
									$value .= '</div>';
									echo $value;
								}
							?>
						</div>
					</div>
					<div id="eagle-vehicle-information"> <!-- Eagle Vehicle Information -->
						<div class="eagle-vehicle-info-divider">
							<h4 class="eagle-divider-headline">
								<span class="eagle-year"><?php echo $year; ?></span>
								<span class="eagle-make"><?php echo $make; ?></span>
								<span class="eagle-model"><?php echo $model; ?></span>
								<span class="eagle-divider-text">Vehicle Details:</span>
							</h4>
							<div class="eagle-divider-content">
								<div id="eagle-stock-vin-wrapper">
									<span class="eagle-text-bold">Stock #:</span><span id="eagle-stock-number" class="eagle-text-space"><?php echo $stock_number; ?></span>
									<span class="eagle-text-bold">Vin #:</span><span id="eagle-vin-number" class="eagle-text-space"><?php echo $vin; ?></span>
								</div>
								<div id="eagle-vehicle-details-wrapper">
									<div id="eagle-vehicle-detail-left">
										<?php
											$vehicle_info = '';

											if ( !empty( $sale_class ) ) {
												$vehicle_info .= '<div class="eagle-vehicle-overview-wrap vehicle-saleclass">';
												$vehicle_info .= '<div class="eagle-vehicle-overview-left">Condition:</div>';
												$vehicle_info .= '<div class="eagle-vehicle-overview-right vehicle-saleclass-value">' . $sale_class . '</div>';
												$vehicle_info .= '</div>';

											}

											if ( $certified == 'true') {
												$vehicle_info .= '<div class="eagle-vehicle-overview-wrap vehicle-certified">';
												$vehicle_info .= '<div class="eagle-vehicle-overview-left">Certified:</div>';
												$vehicle_info .= '<div class="eagle-vehicle-overview-right vehicle-certified-value">Yes</div>';
												$vehicle_info .= '</div>';

											}

											if ( !empty( $odometer ) ) {
												$vehicle_info .= '<div class="eagle-vehicle-overview-wrap vehicle-odometer">';
												$vehicle_info .= '<div class="eagle-vehicle-overview-left">Mileage:</div>';
												$vehicle_info .= '<div class="eagle-vehicle-overview-right vehicle-odometer-value">' . $odometer . '</div>';
												$vehicle_info .= '</div>';
											}

											if ( !empty( $engine ) ) {
												$vehicle_info .= '<div class="eagle-vehicle-overview-wrap vehicle-engine">';
												$vehicle_info .= '<div class="eagle-vehicle-overview-left">Engine:</div>';
												$vehicle_info .= '<div class="eagle-vehicle-overview-right vehicle-engine-value">' . $engine . '</div>';
												$vehicle_info .= '</div>';
											}

											if ( !empty( $transmission ) ) {
												$vehicle_info .= '<div class="eagle-vehicle-overview-wrap vehicle-transmission">';
												$vehicle_info .= '<div class="eagle-vehicle-overview-left">Transmission:</div>';
												$vehicle_info .= '<div class="eagle-vehicle-overview-right vehicle-transmission-value">' . $transmission . '</div>';
												$vehicle_info .= '</div>';
											}

											if ( !empty( $drive_train ) ) {
												$vehicle_info .= '<div class="eagle-vehicle-overview-wrap vehicle-drivetrain">';
												$vehicle_info .= '<div class="eagle-vehicle-overview-left">Drivetrain:</div>';
												$vehicle_info .= '<div class="eagle-vehicle-overview-right vehicle-drivetrain-value">' . $drive_train . '</div>';
												$vehicle_info .= '</div>';
											}

											if ( !empty( $exterior_color ) ) {
												$vehicle_info .= '<div class="eagle-vehicle-overview-wrap vehicle-exterior">';
												$vehicle_info .= '<div class="eagle-vehicle-overview-left">Exterior Color:</div>';
												$vehicle_info .= '<div class="eagle-vehicle-overview-right vehicle-exterior-value">' . $exterior_color . '</div>';
												$vehicle_info .= '</div>';
											}

											if ( !empty( $interior_color ) ) {
												$vehicle_info .= '<div class="eagle-vehicle-overview-wrap vehicle-interior">';
												$vehicle_info .= '<div class="eagle-vehicle-overview-left">Interior color:</div>';
												$vehicle_info .= '<div class="eagle-vehicle-overview-right vehicle-interior-value">' . $interior_color . '</div>';
												$vehicle_info .= '</div>';
											}

											if ( !empty( $carfax) ) {
												$vehicle_info .= '<div class="eagle-vehicle-overview-wrap">';
												$vehicle_info .= '<a href="' . $carfax . '" class="eagle-detail-carfax" target="_blank">Carfax</a>';
												$vehicle_info .= '</div>';
											}

											echo $vehicle_info;

											if( $autocheck ){
												echo display_autocheck_image( $vin, $sale_class, $type );
											}

										?>
									</div>
									<div id="eagle-vehicle-detail-right">
										<?php
											$fuel_city = !empty( $fuel_economy ) && !empty( $fuel_economy->city ) ? $fuel_economy->city : false;
											$fuel_highway = !empty( $fuel_economy ) && !empty( $fuel_economy->highway ) ? $fuel_economy->highway : false;
											if( $company_information->country_code == 'CA' ) {
												$fuel_economy = $vehicle_reference_system->get_fuel_economy( $acode )->please();
												if( isset( $fuel_economy[ 'body' ] ) ) {
													$fuel_economy = json_decode( $fuel_economy[ 'body' ] );
													$fuel_economy = $fuel_economy[ 0 ];
												} else {
													$fuel_economy = false;
												}
												if( $fuel_economy != false ){
													$fuel_city = $fuel_economy->city_lp_100km;
													$fuel_highway = $fuel_economy->highway_lp_100km;
												}
											}

											if( $fuel_city != false && $fuel_highway != false ) {
												$fuel_text = '<div id="eagle-fuel-headline">Fuel Economy:</div>';
												$fuel_text .= '<div id="eagle-fuel-economy">';
												$fuel_text .= '<div id="eagle-fuel-details">';
												$fuel_text .= '<div id="eagle-fuel-city">';
												$fuel_text .= '<div class="eagle-fuel-text">City</div>';
												$fuel_text .= '<div class="eagle-fuel-number"><strong>' . $fuel_city . '</strong></div>';
												$fuel_text .= '</div>';
												$fuel_text .= '<div id="eagle-fuel-img"><img src="http://assets.s3.dealertrend.com.s3.amazonaws.com/images/mpg_s_39x52.png" /></div>';
												$fuel_text .= '<div id="eagle-fuel-highway">';
												$fuel_text .= '<div class="eagle-fuel-text">Hwy</div>';
												$fuel_text .= '<div class="eagle-fuel-number"><strong>' . $fuel_highway . '</strong></div>';
												$fuel_text .= '</div>';
												$fuel_text .= '</div>';
												$fuel_text .= '<div id="eagle-fuel-disclaimer"><small>Actual mileage will vary with options, driving conditions, driving habits and vehicle&#39;s condition.</small></div>';
												$fuel_text .= '</div>';

												echo $fuel_text;
											}
				
										?>
									</div>
								</div>
							</div>
						</div>

						<div class="eagle-vehicle-info-divider" id="eagle-content-description-wrapper">
							<h4 class="eagle-divider-headline">
								<span class="eagle-divider-text">Vehicle Comments:</span>
							</h4>
							<div class="eagle-divider-content">
								<?php
									$eagle_value = '<div id="eagle-content-description"><p>';
										if ( !empty( $description ) ) {
											$eagle_value .= $description;
										}
									$eagle_value .= '</p></div>';
									echo $eagle_value;
								?>
							</div>
						</div>

						<div class="eagle-vehicle-info-divider" id="eagle-content-features-wrapper">
							<h4 class="eagle-divider-headline">
								<span class="eagle-divider-text">Vehicle Specifications and Features:</span>
							</h4>
							<div class="eagle-divider-content">
								<?php
									$eagle_value = '<div id="eagle-content-features"><ul>';
									foreach( $dealer_options as $option ) {
										$eagle_value .= '<li>' . $option . '</li>';
									}
									$eagle_value .= '</ul></div>';
									echo $eagle_value;
								?>
							</div>
						</div>

						<?php
							if( isset($standard_equipment) && !is_Empty_check($standard_equipment) && $show_standard_eq ){
						?>
							<div class="eagle-vehicle-info-divider" id="eagle-content-equipment-wrapper">
								<h4 class="eagle-divider-headline">
									<span class="eagle-divider-text">Vehicle Standard Equipment:</span>
								</h4>
								<div class="eagle-divider-content">
									<?php
										echo display_equipment( $standard_equipment );
									?>
								</div>
							</div>
						<?php
							}
						?>
					</div>
				</div>

				<div id="eagle-content-detail-right"> <!-- Eagle Content Detail Right -->
					<div id="eagle-contact-information">
						<?php
							if ( strtolower( $sale_class ) == 'new' && !empty( $phone_new ) ) {
								$phone_value = $phone_new;
							} elseif ( strtolower( $sale_class ) == 'used' && !empty( $phone_used ) ) {
								$phone_value = $phone_used;
							} else {
								$phone_value = $phone;
							}
							$contact_info_value = '';

							if( !empty($custom_settings['display_dealer_name_sidebar_d']) ){
								$contact_info_value .= '<div id="eagle-contact-name">' . $dealer_name . '</div>';
							}
							if( !empty($custom_settings['display_vehicle_location_sidebar_d']) ){
								$contact_info_value .= '<div id="eagle-contact-vehicle-location">' . $vehicle_location . '</div>';
							}
							$contact_info_value .= '<div id="eagle-contact-phone">' . $phone_value . '</div>';
							$contact_info_value .= '<div id="eagle-contact-message">' . $greeting . '</div>';

							echo $contact_info_value;
						?>

					</div>
					<div class="eagle-content-sidebar-wrapper">
						<div class="eagle-forms">
							<div class="eagle-form-headers active-form" name="form-info">
								Tell Us How We Can Help:
							</div>
							<div class="eagle-form-headers-sub" name="form-info-sub">
								(Check All That Apply)
							</div>
							<div id="eagle-form-info" class="eagle-form" name="active" style="display: block;">
								<form action="#" method="post" name="vehicle-inquiry" id="vehicle-inquiry">
									<input type="hidden" name="traffic_source" value="<?php echo $traffic_source; ?>"/>
									<input name="required_fields" type="hidden" value="name,email,privacy" />
									<input name="subject" type="hidden" value="Vehicle Inquiry - <?php echo $form_subject; ?>" />
									<input name="saleclass" type="hidden" value="<?php echo $sale_class; ?>" />
									<input name="vehicle" type="hidden" value="<?php echo $year_make_model; ?>" />
									<input name="year" type="hidden" value="<?php echo $year; ?>" />
									<input name="make" type="hidden" value="<?php echo $make; ?>" />
									<input name="model_name" type="hidden" value="<?php echo $model; ?>" />
									<input name="trim" type="hidden" value="<?php echo $trim; ?>" />
									<input name="stock" type="hidden" value="<?php echo $stock_number; ?>" />
									<input name="vin" type="hidden" value="<?php echo $vin; ?>" />
									<input name="inventory" type="hidden" value="<?php echo $inventory->id; ?>" />
									<input name="price" type="hidden" value="<?php echo $primary_price; ?>" />
									<input name="name" type="hidden" id="vehicle-inquiry-name" value="" />
									<input name="comments" type="hidden" id="vehicle-inquiry-comments" value="" />
									<div class="eagle-form-table">
										<div class="eagle-form-top-checkboxes">
											<div class="eagle-checkbox-wrapper">
												<div class="eagle-checkbox-left">
													<input class="eagle-checkbox" name="eagle-checkbox-general-questions" id="vehicle-inquiry-checkbox-1" tabindex="4" type="checkbox"  />
												</div>
												<div class="eagle-checkbox-right">
													<label for="vehicle-inquiry-checkbox-1">General Questions</label>
												</div>
											</div>
											<div class="eagle-checkbox-wrapper">
												<div class="eagle-checkbox-left">
													<input class="eagle-checkbox" name="eagle-checkbox-email-coupon" id="vehicle-inquiry-checkbox-2" tabindex="5" type="checkbox"  />
												</div>
												<div class="eagle-checkbox-right">
													<label for="vehicle-inquiry-checkbox-2">Email Me a Coupon</label>
												</div>
											</div>
											<div class="eagle-checkbox-wrapper">
												<div class="eagle-checkbox-left">
													<input class="eagle-checkbox" name="eagle-checkbox-call-asap" id="vehicle-inquiry-checkbox-3" tabindex="6" type="checkbox"  />
												</div>
												<div class="eagle-checkbox-right">
													<label for="vehicle-inquiry-checkbox-3">Call Me ASAP</label>
												</div>
											</div>
											<div class="eagle-checkbox-wrapper">
												<div class="eagle-checkbox-left">
													<input class="eagle-checkbox" name="eagle-checkbox-price-drop" id="vehicle-inquiry-checkbox-4" tabindex="7" type="checkbox"  />
												</div>
												<div class="eagle-checkbox-right">
													<label for="vehicle-inquiry-checkbox-4">Email Me When Price Drops</label>
												</div>
											</div>
											<div class="eagle-checkbox-wrapper">
												<div class="eagle-checkbox-left">
													<input class="eagle-checkbox" name="eagle-checkbox-request-video" id="vehicle-inquiry-checkbox-5" tabindex="8" type="checkbox"  />
												</div>
												<div class="eagle-checkbox-right">
													<label for="vehicle-inquiry-checkbox-5">Request Walk-Through Video</label>
												</div>
											</div>
											<div class="eagle-checkbox-wrapper">
												<div class="eagle-checkbox-left">
													<input class="eagle-checkbox" name="eagle-checkbox-send-eprice" id="vehicle-inquiry-checkbox-6" tabindex="9" type="checkbox"  />
												</div>
												<div class="eagle-checkbox-right">
													<label for="vehicle-inquiry-checkbox-6">Send Me The ePrice</label>
												</div>
											</div>
										</div>
										<div class="eagle-form-full">
											<div class="required">
												<input maxlength="70" id="vehicle-inquiry-f-name" name="f_name" tabindex="10" type="text" alt="empty" value="First Name*" />
											</div>
										</div>
										<div class="eagle-form-full">
											<div class="required">
												<input maxlength="70" id="vehicle-inquiry-l-name" name="l_name" tabindex="11" type="text" alt="empty" value="Last Name*" />
											</div>
										</div>

										<div class="eagle-form-full">
											<div class="required">
												<input maxlength="255" id="vehicle-inquiry-email" name="email" tabindex="12" type="text" alt="empty" value="Email Address*"/>
											</div>
										</div>
										<div class="eagle-form-full">
											<div>
												<input maxlength="256" name="phone" id="vehicle-inquiry-phone" tabindex="13" type="text" alt="empty" value="Phone Number"/>
											</div>
										</div>
										<div class="eagle-form-full">
											<div>
												<textarea name="vehicle-inquiry-form-comments" id="vehicle-inquiry-form-comments" rows="4" tabindex="14" alt="empty">Comments</textarea>
											</div>
										</div>
										<div class="eagle-form-full">
											<div style="display:none">
												<input name="agree_sb" type="checkbox" value="Yes" /> I am a Spam Bot?
											</div>
											<div style="display:none">
												<label for="vehicle-inquiry-privacy" style="float:left; margin-right:10px;">Agree to <a target="_blank" href="/privacy">Privacy Policy</a></label>
												<input class="privacy" name="privacy" id="vehicle-inquiry-privacy" tabindex="15" type="checkbox" checked />
											</div>
										</div>
										<div class="eagle-form-button">
											<div>
												<input onclick="return eagle_process_forms(<?php echo '&#39;' . $form_submit_url . strtolower( $sale_class ) . '_vehicle_inquiry&#39;'; ?> , '0' )" type="submit" value="Send Inquiry" class="submit" tabindex="16" />
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
					</div>

					<?php //Eagle Similar Vehicles

						if ( !empty( $vehicle_class ) && !empty( $inventory ) ) {
							$price_from = $primary_price - 3000;
							$price_to = $primary_price + 1000;
							if ( $price_from < 0 ) {
								$price_from = 0;
							}
							if ( $primary_price > 0) {
								$sim_array = array( 'search_sim' => 1 , 'photo_view' => 1 , 'saleclass' => $sale_class , 'vehicleclass' => $vehicle_class , 'per_page' => 4 , 'price_from' => $price_from, 'price_to' => $price_to);
							} else {
								$sim_array = array( 'search_sim' => 1 , 'photo_view' => 1 , 'saleclass' => $sale_class , 'vehicleclass' => $vehicle_class , 'per_page' => 4 , 'make' => $make );
							}

							if ( strcasecmp( $sale_class, 'new') == 0 && !empty( $new_makes_filter ) ) {
								$sim_array = array_merge( $sim_array, array( 'make_filters' =>  $new_makes_filter ) );
							}
							$vehicle_management_system->tracer = 'Obtaining Similar Vehicles - 1';
							$inventory_sims_info = $vehicle_management_system->get_inventory()->please( $sim_array );
							$inventory_sims = isset( $inventory_sims_info[ 'body' ] ) ? json_decode( $inventory_sims_info[ 'body' ] ) : false;

							if ( !empty( $inventory_sims ) && count($inventory_sims) > 1 ) {
								include( dirname( __FILE__ ) . '/detail_similar.php' );
							} else if ( isset( $sim_array['price_from'] ) ) {
								unset( $sim_array['price_from'] );
								unset( $sim_array['price_to'] );
								$sim_array = $sim_array + array( 'make' => $make );
								$vehicle_management_system->tracer = 'Obtaining Similar Vehicles - 2';
								$inventory_sims_info = $vehicle_management_system->get_inventory()->please( $sim_array );
								$inventory_sims = isset( $inventory_sims_info[ 'body' ] ) ? json_decode( $inventory_sims_info[ 'body' ] ) : false;
								if ( !empty( $inventory_sims ) && count($inventory_sims) > 1 ) {
									include( dirname( __FILE__ ) . '/detail_similar.php' );
								}
							}

						}

					?>

				</div>
			</div>
			<div id="eagle-content-bottom"> <!-- Eagle Content Bottom -->
			</div>
		</div>
		<div id="eagle-bottom"> <!-- Eagle Bottom -->
			<div id="eagle-disclaimer">
				<?php echo '<p>' . $inventory->disclaimer . '</p>'; ?>
			</div>
		</div>

		<?php
			if ( is_active_sidebar( 'vehicle-detail-page' ) ) :
				echo '<div id="eagle-widget-area" class="sidebar">';
					dynamic_sidebar( 'vehicle-detail-page' );
				echo '</div>';
			endif;
		?>

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
					<input name="saleclass" type="hidden" value="<?php echo $sale_class; ?>" />
					<input name="vehicle" type="hidden" value="<?php echo $year_make_model; ?>" />
					<input name="year" type="hidden" value="<?php echo $year; ?>" />
					<input name="make" type="hidden" value="<?php echo $make; ?>" />
					<input name="model_name" type="hidden" value="<?php echo $model; ?>" />
					<input name="trim" type="hidden" value="<?php echo $trim; ?>" />
					<input name="stock" type="hidden" value="<?php echo $stock_number; ?>" />
					<input name="vin" type="hidden" value="<?php echo $vin; ?>" />
					<input name="inventory" type="hidden" value="<?php echo $inventory->id; ?>" />
					<input name="price" type="hidden" value="<?php echo $primary_price; ?>" />
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
								<input onclick="return eagle_process_forms(<?php echo '&#39;' . $form_submit_url . strtolower( $sale_class ) . '_vehicle_inquiry&#39;'; ?> , '3' )" type="submit" value="Send Inquiry" class="submit" tabindex="25" />
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

	</div>
</div>



