<?php
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
	$certified_inv = $inventory->certified;
	$fuel_economy = $inventory->fuel_economy;
	$headline = $inventory->headline;
	$body_style = $inventory->body_style;
	$drive_train = $inventory->drive_train;
	$video_url = isset( $inventory->video_url ) ? $inventory->video_url : false;
	$carfax = isset( $inventory->carfax ) ? $inventory->carfax->url : false;
	$contact_information = $inventory->contact_info;
	$greeting = isset( $contact_information->greeting ) ? $contact_information->greeting : NULL;
	$dealer_name = isset( $contact_information->dealer_name ) ? $contact_information->dealer_name : NULL;
	$phone = isset( $contact_information->phone ) ? $contact_information->phone : NULL;
	$internet_manager = isset( $contact_information->internet_manager ) ? $contact_information->internet_manager : NULL;
	$certified = $inventory->certified;
	$vehicle_class = $inventory->vehicleclass;
	$acode = $inventory->ads_acode;
	$autocheck = isset( $inventory->auto_check_url ) ? TRUE : FALSE;

	//$primary_price = $sale_price != NULL ? $sale_price : $asking_price;
	$primary_price = 0;

	$traffic_source = isset( $_COOKIE[ 'dealertrend-traffic-source' ] ) ? $_COOKIE[ 'dealertrend-traffic-source' ] : false;

	$traffic_source = $this->sanitize_inputs( $traffic_source );

	$share_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$share_dealer = str_replace( '&' , 'and' , str_replace( ' ' , '%20' , $dealer_name) );

	$ais_incentive = isset( $inventory->ais_incentive->to_s ) ? $inventory->ais_incentive->to_s : NULL;
	$incentive_price = 0;
	if( $ais_incentive != NULL ) {
		preg_match( '/\$\d*(\s)?/' , $ais_incentive , $incentive );
		$incentive_price = isset( $incentive[ 0 ] ) ? str_replace( '$' , NULL, $incentive[ 0 ] ) : 0;
	}

	// Sort Options by length
	function sort_l($a,$b){
	    return strlen($a)-strlen($b);
	}
	usort($dealer_options, 'sort_l' );

	$form_submit_url = $this->options[ 'vehicle_management_system' ][ 'host' ] . '/' . $this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] . '/forms/create/';

?>

<script>

function video_popup(url , title) {
	if (! window.focus) return true;
	var href;
	if (typeof(url) == 'string') {
		href=url;
	} else {
		href=url.href;
		window.open(href, title, 'width=640,height=480,scrollbars=no');
		return false;
	}
}
</script>



<div id="dolphin-wrapper" class="dolphin-detail-page"> <!-- 2nd Wrapper -->
	<div id="dolphin-top"> <!-- Detail Top -->
		<div id="dolphin-taxonomy"> <!-- SEO/Taxonomy -->
			<?php echo $breadcrumbs; ?> <!-- Breadcrumbs -->
			<div class="dolphin-print"> <!-- Print -->
				<a id="dolphin-previous-page" href="javascript:history.go(-1)">&#60;&#60; Previous Page</a>
				<a id="friendly-print" onclick="window.open('?print_page','popup','width=800,height=900,scrollbars=yes,resizable=no,toolbar=no,directories=no,location=no,menubar=yes,status=no,left=0,top=0'); return false">Print</a>
			</div>
		</div>
		<div id="dolphin-headline"> <!-- Headline -->
			<h2>
				<span class="dolphin-saleclass"><?php echo $sale_class; ?></span>
				<span class="dolphin-year"><?php echo $year; ?></span>
				<span class="dolphin-make"><?php echo $make; ?></span>
				<span class="dolphin-model"><?php echo $model; ?></span>
				<span class="dolphin-trim"><?php echo $trim; ?></span>
				<span class="dolphin-drive-train"><?php echo $drive_train; ?></span>
				<span class="dolphin-body-style"><?php echo $body_style; ?></span>

			</h2>
			<?php
				if ( !empty( $headline ) ){
					$value = '<div id="dolphin-headline-text">' . $headline . '</div>';
					echo $value;
				}
			?>

		</div>
	</div> <!-- Detail Top End-->
	<div id="dolphin-detail"> <!-- Detail Body -->
		<div id="dolphin-contact-information"> <!-- Contact Information -->
			<?php
				$value = '<div id="dolphin-contact-details">';
				$value .= '<span id="dolphin-contact-greeting">' . $greeting . ' </span><strong id="dolphin-contact-dealer">' . $dealer_name . '</strong>';

				if ( strtolower( $sale_class ) == 'new' && !empty( $phone_new ) ) {
					$phone_value = $phone_new;
				} elseif ( strtolower( $sale_class ) == 'used' && !empty( $phone_used ) ) {
					$phone_value = $phone_used;
				} else {
					$phone_value = $phone;
				}
				if( $phone_value != NULL ) {
					$value .= '<span id="dolphin-contact-phone">: <strong>' . $phone_value . '</strong></span>';
				}
				$value .= '</div>';
				if( $internet_manager != NULL ) {
					//$value .= '<div id="dolphin-contact-manager">Internet Manager: <strong>' . $internet_manager . '</strong></div>';
				}
				echo $value;
			?>
		</div> <!-- Contact Information End -->
		<div id="dolphin-detail-share"> <!-- Share -->
			<div id="dolphin-share-text">Share:</div>
			<ul id="dolphin-share-buttons">
				<li id="dolphin-facebook"><a target="_blank" href="http://www.facebook.com/sharer.php?u=<?php echo $share_link; ?>" >facebook</a></li>
				<li id="dolphin-google"><a target="_blank" href="https://plus.google.com/share?url=<?php echo $share_link; ?>" >google plus</a></li>
				<li id="dolphin-twitter"><a target="_blank" href="http://twitter.com/share?text=Found%20at%20<?php echo $share_dealer; ?>&url=<?php echo $share_link; ?> ">twitter</a></li>
			</ul>
		</div> <!-- Share End-->
		<div id="dolphin-detail-information"> <!-- Detail Information -->
			<div id="dolphin-column-left"> <!-- Column Left -->
				<div id="dolphin-slideshow-text">
					Click Image to Enlarge
				</div>
				<div id="dolphin-slideshow">
					<?php
						if( count( $inventory->photos ) ) {
							$value = '<div id="dolphin-images">';

							foreach( $inventory->photos as $photo ) {
								$value .= '<a class="lightbox" rel="slides" href="' . str_replace( '&' , '&amp;' , $photo->large ) . '" title="' . $company_name . '">';
								$value .= '<img src="' . str_replace( '&' , '&amp;' , $photo->medium ) . '" alt="" />';
								$value .= '</a>';
							}

							$value .= '</div>';
						}
						echo $value;
						if( $video_url ) {
							$value = '<div id="dolphin-video-button">';
							$value .= '<a onClick="return video_popup(this, \'' . $year_make_model . '\')" href="' . $video_url . '">Play Video</a>';
							$value .= '</div>';
							echo $value;
						}
						if( count( $inventory->photos ) > 1 ) {
							$value = '';
							if( count( $inventory->photos ) > 3 ) {
								$value .= '<div id="dolphin-nav-button" name="hidden">Show All</div>';
							} else {
								$value .= '<div id="dolphin-nav-button" name="hidden"></div>';
							}
							$value .= '<div id="dolphin-nav-wrapper">';
							$value .= '<div id="dolphin-nav-images"></div>';
							$value .= '</div>';

							echo $value;
						}
					?>
				</div>
				<?php
					if( !empty( $tags ) ){
						echo '<div class="dolphin-icons">';
							apply_special_tags( $tags, $on_sale, $certified_inv);
							$tag_icons = build_tag_icons( $default_tag_names, $custom_tag_icons, $tags);
							echo $tag_icons;
						echo '</div>';
					}
				?>

				<?php
					if( $carfax ) {
						echo '<div id="dolphin-carfax"><a href="' . $carfax . '" target="_blank"><img src="http://assets.s3.dealertrend.com.s3.amazonaws.com/images/carfax_192x46.jpg" /></a></div>';
					}

					if( $autocheck ){
						echo display_autocheck_image( $vin, $sale_class, $type );
					}

				?>
			</div> <!-- Column Left End -->
			<div id="dolphin-column-middle"> <!-- Column Middle -->
				<div id="dolphin-price">
					<?php
						$asking_text = '';
						$sale_text = '';
						$ais_text = '';
						$sale_expires_text = '';

						if( $on_sale && $sale_price > 0 ) {
							$now_text = 'Price: ';
							if( $use_was_now ) {
								$price_class = ( $use_price_strike_through ) ? 'dolphin-strike-through dolphin-asking-price' : 'dolphin-asking-price';
								if( $incentive_price > 0 ) {
									$asking_text = '<div class="' . $price_class . ' dolphin-ais">Was: ' . '$' . number_format( $sale_price , 0 , '.' , ',' ) . '</div>';
									$primary_price = $sale_price;
								} else {
									$asking_text = '<div class="' . $price_class . '">Was: ' . '$' . number_format( $asking_price , 0 , '.' , ',' ) . '</div>';
									$primary_price = $asking_price;
								}
								$now_text = 'Now: ';
							}
							if( $incentive_price > 0 ) {
								$ais_text = '<div class="dolphin-ais-incentive-l-text">' . $ais_incentive . '</div>';
								$sale_text = '<div class="dolphin-sale-price dolphin-ais">' . $now_text . '$' . number_format( $sale_price - $incentive_price , 0 , '.' , ',' ) . '</div>';
								$primary_price = $sale_price - $incentive_price;
								if( $sale_expire != NULL ) {
									$sale_expires_text = '<div class="dolphin-sale-expires">Sale Expires: ' . $sale_expire . '</div>';
								}
							} else {
								if( $ais_incentive != NULL ) {
									$ais_text = '<div class="dolphin-ais-incentive-l-text">' . $ais_incentive . '</div>';
								}
								$sale_text = '<div class="dolphin-sale-price">' . $now_text . '$' . number_format( $sale_price , 0 , '.' , ',' ) . '</div>';
								if( $sale_expire != NULL ) {
									$sale_expires_text = '<div class="dolphin-sale-expires">Sale Expires: ' . $sale_expire . '</div>';
								}
								$primary_price = $sale_price;
							}
						} else {
							if( $asking_price > 0 ) {
								if( $incentive_price > 0 ) {
									$asking_text = '<div class="dolphin-asking-price dolphin-ais">Asking Price: $' . number_format( $asking_price , 0 , '.' , ',' ) . '</div>';
									$ais_text = '<div class="dolphin-ais-incentive-l-text">' . $ais_incentive . '</div>';
									$sale_text = '<div class="dolphin-your-price dolphin-ais">Your Price: $' . number_format( $asking_price - $incentive_price , 0 , '.' , ',' ) . '</div>';
									$primary_price = $asking_price - $incentive_price;
								} else {
									if( $ais_incentive != NULL ) {
										$ais_text = '<div class="dolphin-ais-incentive-l-text">' . $ais_incentive . '</div>';
									}
									$sale_text = '<div class="dolphin-asking-price">Price: $' . number_format( $asking_price , 0 , '.' , ',' ) . '</div>';
									$primary_price = $asking_price;
								}
							} else {
								if( $ais_incentive != NULL ) {
									$ais_text = '<div class="dolphin-ais-incentive-l-text">' . $ais_incentive . '</div>';
								}
								$sale_text = '<div class="dolphin-no-price">' . $default_price_text . '</div>';
							}
						}

						if ( !empty( $sale_text ) ){
							echo $sale_text;
						} else {
							echo $asking_text;
						}

						if( $ais_incentive != NULL && isset( $company_information->api_keys ) ) {
							$value_ais = '<div class="dolphin-ais-incentive-s-text view-available-rebates">';
							$value_ais .= '<a href="http://onecar.aisrebates.com/dlr2/inline/IncentiveOutput.php?vID='. $vin . '&wID=' . $company_information->api_keys->ais . '&zID=' . $company_information->zip . '" target="_blank" title="VIEW AVAILABLE INCENTIVES AND REBATES" onclick="return loadIframe( this.href );">VIEW AIS</a>';
							$value_ais .= '</div>';
							echo $value_ais;
						}
					?>
				</div>
				<?php
					if ( !empty( $sale_expires_text ) ) {
						echo $sale_expires_text;
					}
				?>
				<div id="dolphin-vehicle-overview">
					<?php
						$vehicle_info = '';

						if ( !empty( $ais_text ) ) {
							$vehicle_info .= '<div class="dolphin-vehicle-overview-wrap">';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-left">AIS Rebate:</div>';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-right">' . $ais_text . '</div>';
							$vehicle_info .= '</div>';
						}

						if ( !empty( $sale_text ) && $sale_price > 0 && $incentive_price > 0 ) {
							$vehicle_info .= '<div class="dolphin-vehicle-overview-wrap">';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-left">Price Was:</div>';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-right">$' . number_format( $sale_price , 0 , '.' , ',' ) . '</div>';
							$vehicle_info .= '</div>';
						} else if (  !empty( $asking_text ) && $asking_price > 0 ){
							$vehicle_info .= '<div class="dolphin-vehicle-overview-wrap">';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-left">Price Was:</div>';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-right">$' . number_format( $asking_price , 0 , '.' , ',' ) . '</div>';
							$vehicle_info .= '</div>';
						}

						if ( $retail_price > 0 && strtolower( $sale_class ) == 'new' ) {
							$vehicle_info .= '<div class="dolphin-vehicle-overview-wrap">';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-left">MSRP:</div>';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-right">$' . number_format( $retail_price , 0 , '.' , ',' ) . '</div>';
							$vehicle_info .= '</div>';
						}

						if ( !empty( $vehicle_info ) ) { $vehicle_info .= '<br>'; }

						if ( $certified == 'true') {
							$vehicle_info .= '<div class="dolphin-vehicle-overview-wrap">';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-left">Certified:</div>';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-right">Yes</div>';
							$vehicle_info .= '</div>';

						}

						if ( !empty( $body_style ) ) {
							$vehicle_info .= '<div class="dolphin-vehicle-overview-wrap">';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-left">Body Style:</div>';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-right">' . $body_style . '</div>';
							$vehicle_info .= '</div>';
						}

						if ( !empty( $exterior_color ) ) {
							$vehicle_info .= '<div class="dolphin-vehicle-overview-wrap">';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-left">Ext. Color:</div>';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-right">' . $exterior_color . '</div>';
							$vehicle_info .= '</div>';
						}

						if ( !empty( $interior_color ) ) {
							$vehicle_info .= '<div class="dolphin-vehicle-overview-wrap">';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-left">Int. color:</div>';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-right">' . $interior_color . '</div>';
							$vehicle_info .= '</div>';
						}

						if ( !empty( $engine ) ) {
							$vehicle_info .= '<div class="dolphin-vehicle-overview-wrap">';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-left">Engine:</div>';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-right">' . $engine . '</div>';
							$vehicle_info .= '</div>';
						}

						if ( !empty( $transmission ) ) {
							$vehicle_info .= '<div class="dolphin-vehicle-overview-wrap">';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-left">Transmission:</div>';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-right">' . $transmission . '</div>';
							$vehicle_info .= '</div>';
						}

						if ( !empty( $drive_train ) ) {
							$vehicle_info .= '<div class="dolphin-vehicle-overview-wrap">';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-left">Drivetrain:</div>';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-right">' . $drive_train . '</div>';
							$vehicle_info .= '</div>';
						}

						if ( !empty( $odometer ) ) {
							$vehicle_info .= '<div class="dolphin-vehicle-overview-wrap">';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-left">Odometer:</div>';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-right">' . $odometer . '</div>';
							$vehicle_info .= '</div>';
						}

						if ( !empty( $vehicle_info ) ) { $vehicle_info .= '<br>'; }

						if ( !empty( $vin ) ) {
							$vehicle_info .= '<div class="dolphin-vehicle-overview-wrap">';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-left">Vin:</div>';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-right">' . $vin . '</div>';
							$vehicle_info .= '</div>';
						}

						if ( !empty( $stock_number ) ) {
							$vehicle_info .= '<div class="dolphin-vehicle-overview-wrap">';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-left">Stock #:</div>';
							$vehicle_info .= '<div class="dolphin-vehicle-overview-right">' . $stock_number . '</div>';
							$vehicle_info .= '</div>';
						}

						echo $vehicle_info;

					?>
				</div>
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
						$fuel_text = '<div id="dolphin-fuel-economy">';
						$fuel_text .= '<div id="dolphin-fuel-headline">Est. Fuel Economy</div>';
						$fuel_text .= '<div id="dolphin-fuel-details">';
						$fuel_text .= '<div id="dolphin-fuel-city">';
						$fuel_text .= '<div class="dolphin-fuel-text">City</div>';
						$fuel_text .= '<div class="dolphin-fuel-number"><strong>' . $fuel_city . '</strong></div>';
						$fuel_text .= '</div>';
						$fuel_text .= '<div id="dolphin-fuel-img"><img src="http://assets.s3.dealertrend.com.s3.amazonaws.com/images/mpg_s_39x52.png" /></div>';
						$fuel_text .= '<div id="dolphin-fuel-highway">';
						$fuel_text .= '<div class="dolphin-fuel-text">Hwy</div>';
						$fuel_text .= '<div class="dolphin-fuel-number"><strong>' . $fuel_highway . '</strong></div>';
						$fuel_text .= '</div>';
						$fuel_text .= '</div>';
						$fuel_text .= '<div id="dolphin-fuel-disclaimer"><small>Actual mileage will vary with options, driving conditions, driving habits and vehicle&#39;s condition.</small></div>';
						$fuel_text .= '</div>';

						echo $fuel_text;
					}
				
				?>
			</div> <!-- Column Middle End -->
			<div id="dolphin-column-right"> <!-- Column Right -->
				<div class="dolphin-forms">
					<div class="dolphin-form-headers active-form" name="form-info">
						Request Information
					</div>
					<div id="dolphin-form-info" class="dolphin-form" name="active" style="display: block;">
						<form action="#" method="post" name="vehicle-inquiry" id="vehicle-inquiry">
							<input type="hidden" name="traffic_source" value="<?php echo $traffic_source; ?>"/>
							<input name="required_fields" type="hidden" value="name,email,privacy" />
							<input name="subject" type="hidden" value="Vehicle Inquiry - <?php echo $headline; ?>" />
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
							<div class="dolphin-form-table">
								<div class="dolphin-form-one-half">
									<div class="required">
										<label for="vehicle-inquiry-f-name">First Name*</label>
										<input maxlength="70" id="vehicle-inquiry-f-name" name="f_name" tabindex="10" type="text" />
									</div>
									<div class="required">
										<label for="vehicle-inquiry-l-name">Last Name*</label>
										<input maxlength="70" id="vehicle-inquiry-l-name" name="l_name" tabindex="11" type="text" />
									</div>
								</div>
								<div class="dolphin-form-one-half">
									<div class="required">
										<label for="vehicle-inquiry-email">Email Address*</label>
										<input maxlength="255" id="vehicle-inquiry-email" name="email" tabindex="12" type="text" />
									</div>
									<div>
										<label for="vehicle-inquiry-phone">Phone Number</label>
										<input maxlength="256" name="phone" id="vehicle-inquiry-phone" tabindex="13" type="text" />
									</div>
								</div>
								<div class="dolphin-form-full">
									<div class="required">
										<label for="vehicle-inquiry-comments">Questions/Comments</label>
										<textarea name="comments" id="vehicle-inquiry-comments" rows="4" tabindex="14"></textarea>
									</div>
								</div>
								<div class="dolphin-form-full">
									<div style="display:none">

										<input name="agree_sb" type="checkbox" value="Yes" /> I am a Spam Bot?
									</div>
									<div>
										<label for="vehicle-inquiry-privacy" style="float:left; margin-right:10px;">Agree to <a target="_blank" href="/privacy">Privacy Policy</a></label>
										<input class="privacy" name="privacy" id="vehicle-inquiry-privacy" tabindex="15" type="checkbox" checked />
									</div>
									<div>
										<input onclick="dolphin_detail_forms(<?php echo '&#39;' . $form_submit_url . strtolower( $sale_class ) . '_vehicle_inquiry&#39;'; ?> , '0' )" type="submit" value="Send Inquiry" class="submit" tabindex="16" />
									</div>
								</div>
								<div class="dolphin-form-full">
									<div class="form-error" style="display: none;">
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>

				<div class="dolphin-forms">
					<div class="dolphin-form-headers" name="form-test-drive">
						Schedule a Test Drive
					</div>
					<div id="dolphin-form-test-drive" class="dolphin-form" name="hidden" style="display: none;">
						<form name="vehicle-testdrive" id="vehicle-testdrive" action="#" method="post">
							<input type="hidden" name="traffic_source" value="<?php echo $traffic_source; ?>"/>
							<input type="hidden" name="required_fields" value="name,email,privacy"/>
							<input type="hidden" name="saleclass" value="<?php echo strtolower($sale_class); ?>"/>
							<input type="hidden" name="return_url" value="<?php echo $share_link; ?>" id="return_url_test_drive"/>
							<input type="hidden" name="vehicle" value="<?php echo $year . ' ' . $make . ' ' . $model; ?>"/>
							<input type="hidden" name="year" value="<?php echo $year; ?>"/>
							<input type="hidden" name="make" value="<?php echo $make; ?>"/>
							<input type="hidden" name="model_name" value="<?php echo $model; ?>"/>
							<input type="hidden" name="trim" value="<?php echo $trim; ?>"/>
							<input type="hidden" name="stock" value="<?php echo $stock_number; ?>"/>
							<input type="hidden" name="vin" value="<?php echo $vin; ?>"/>
							<input type="hidden" name="inventory" value="<?php echo $inventory->id; ?>"/>
							<input type="hidden" name="price" value="<?php echo $primary_price; ?>"/>
							<input type="hidden" name="name" value="" id="vehicle-testdrive-name" />
							<input type="hidden" name="timetocall" value="" id="vehicle-testdrive-timetocall"/>
							<input type="hidden" name="subject" value="Vehicle Test Drive - <?php echo $year_make_model; ?>" />
							<div class="dolphin-form-table">
								<div class="dolphin-form-one-half">
									<div class="required">
										<label for="vehicle-testdrive-f-name">First Name*</label>
										<input maxlength="70" id="vehicle-testdrive-f-name" name="f_name" tabindex="20" type="text" />
									</div>
									<div class="required">
										<label for="vehicle-testdrive-l-name">Last Name*</label>
										<input maxlength="70" id="vehicle-testdrive-l-name" name="l_name" tabindex="21" type="text" />
									</div>
								</div>
								<div class="dolphin-form-one-half">
									<div class="required">
										<label for="vehicle-testdrive-email">Email Address*</label>
										<input type="text" maxlength="255" name="email" id="vehicle-testdrive-email" tabindex="22" />
									</div>
									<div>
										<label for="vehicle-testdrive-phone">Phone Number</label>
										<input type="text" maxlength="256" name="phone" id="vehicle-testdrive-phone" tabindex="23" />
									</div>
								</div>
								<div class="dolphin-form-full">
									<label class="form-single-label">Best Time To Contact</label>
								</div>
								<div class="dolphin-form-one-half">
									<div>
										<label for="vehicle-testdrive-date">Date</label>
										<input type="text" maxlength="256" name="test_date" id="vehicle-testdrive-date" tabindex="24" />
									</div>
									<div>
										<label for="vehicle-testdrive-time">Time</label>
										<input type="text" maxlength="256" name="test_time" id="vehicle-testdrive-time" tabindex="25" />
									</div>
								</div>
								<div class="dolphin-form-full">
									<div>
										<label for="vehicle-testdrive-comments">Comments</label>
										<textarea rows="10" cols="35" name="comments" id="vehicle-testdrive-comments" tabindex="26" ></textarea>
									</div>
								</div>
								<div class="dolphin-form-full">
									<div style="display:none">
										<input type="checkbox" name="agree_sb" value="Yes" /> I am a Spam Bot?
									</div>
									<div>
										<label for="vehicle-testdrive-privacy" style="float:left; margin-right:10px;">Agree to <a target="_blank" href="/privacy">Privacy Policy</a></label>
										<input type="checkbox" class="privacy" id="vehicle-testdrive-privacy" tabindex="27" name="privacy" checked />
									</div>
								</div>
								<div class="dolphin-form-full">
									<div>
										<input onclick="dolphin_detail_forms(<?php echo '&#39;' . $form_submit_url . strtolower( $sale_class ) . '_vehicle_test_drive&#39;'; ?> , '1' )" type="submit" value="Test Drive" class="submit" tabindex="28" />
									</div>
								</div>
								<div class="dolphin-form-full">
									<div class="form-error" style="display: none;">
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>


				<div class="dolphin-forms">
					<a href="mailto:?Subject=<?php echo str_replace( ' ', '%20', $company_information->name ) . '%20-%20Tell-A-Friend%20-%20' . str_replace( ' ', '%20', $year_make_model); ?>" target="_top"><div class="dolphin-form-headers" name="form-friend">
						Tell A Friend
					</div></a>
				</div>


				<div class="dolphin-forms">
					<div class="dolphin-form-headers" name="form-calculate">
						Calculate Payment
					</div>
					<div id="dolphin-form-calculate" class="dolphin-form" name="hidden" style="display: none;">
						<form id="loan-calculator" name="loan-calculator" action="#" method="post">
							<div class="dolphin-form-table">
								<div class="dolphin-form-one-half">
									<div>
										<label for="loan-calculator-price">Vehicle Price</label>
										<input type="text" name="price" id="loan-calculator-price" value="$<?php echo trim( number_format( $primary_price , 0 , '.' , ',' ) ); ?>" tabindex="40" />
									</div>
									<div>
										<label for="loan-calculator-interest-rate">Interest Rate</label>
										<input type="text" name="interest-rate" id="loan-calculator-interest-rate" value="7.35%" tabindex="41"/>
									</div>
								</div>
								<div class="dolphin-form-one-half">
									<div>
										<label for="loan-calculator-term">Term (months)</label>
										<input type="text" name="term" id="loan-calculator-term" value="72" tabindex="42" />
									</div>
									<div>
										<label for="loan-calculator-trade-in-value">Trade in Value</label>
										<input type="text" name="trade-in-value" id="loan-calculator-trade-in-value" value="$3,000" tabindex="43" />
									</div>
								</div>
								<div class="dolphin-form-one-half">
									<div>
										<label for="loan-calculator-down-payment">Down Payment</label>
										<input type="text" name="down-payment" id="loan-calculator-down-payment" value="$5,000" tabindex="44" />
									</div>
									<div>
										<label for="loan-calculator-sales-tax">Sales Tax</label>
										<input type="text" name="sales-tax" id="loan-calculator-sales-tax" value="7.375%" tabindex="45" />
									</div>
								</div>
								<div class="dolphin-form-full">
									<div>
										<label for="loan-calculator-bi-monthly-cost">Bi-Monthly Cost</label>
										<div id="loan-calculator-bi-monthly-cost" class="loan-calculated-value"></div>
									</div>
								</div>
								<div class="dolphin-form-full">
									<div>
										<label for="loan-calculator-monthly-cost">Monthly Cost</label>
										<div id="loan-calculator-monthly-cost" class="loan-calculated-value"></div>
									</div>
								</div>
								<div class="dolphin-form-full">
									<div>
										<label for="loan-calculator-total-cost">Total Cost <small>(including taxes)</small></label>
										<div id="loan-calculator-total-cost" class="loan-calculated-value"></div>
									</div>
								</div>
								<div class="dolphin-form-full">
									<div>
										<input onclick="" type="button" value="Calculate" class="submit" tabindex="46" />
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div> <!-- Column Right End -->
			<div id="dolphin-detail-specs"> <!-- Detail Specs -->
				<div id="dolphin-detail-tabs">
					<ul>
						<?php
							echo ( isset($dealer_options) ) ? '<li class="dolphin-detail-tab active-tab" name="features">Equipment / Features</li>' : '';
							echo ( isset($standard_equipment) && $show_standard_eq ) ? '<li class="dolphin-detail-tab" name="standard">Standard Equipment</li>' : '';
							echo ( $description ) ? '<li class="dolphin-detail-tab" name="description">Dealer Comments</li>' : '';
						?>
					</ul>
					<div id="dolphin-detail-features" class="detail-tab-info">
						<ul>
						<?php
							if( isset($dealer_options) ){
								foreach( $dealer_options as $option ) {
									echo '<li>' . $option . '</li>';
								}
							}
						?>
						</ul>
					</div>
					<div id="dolphin-detail-description" class="detail-tab-info">
						<p><?php echo $description; ?></p>
					</div>
					<?php
						if( isset($standard_equipment) && $show_standard_eq ){
							echo '<div id="dolphin-detail-standard" class="detail-tab-info">';
							echo display_equipment( $standard_equipment );
							echo '</div>';
						}
					?>
				</div>
			</div>  <!-- Detail Specs End -->

			<?php //Dolphin Similar Vehicles

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

			<div id="dolphin-disclaimer">
				<?php echo '<p>' . $inventory->disclaimer . '</p>'; ?>
			</div>

			<?php
				if ( is_active_sidebar( 'vehicle-detail-page' ) ) :
					echo '<div id="dolphin-widget-area" class="sidebar">';
						dynamic_sidebar( 'vehicle-detail-page' );
					echo '</div>';
				endif;
			?>
		</div> <!-- Detail Information End -->
	</div> <!-- Detail Body End-->
</div>  <!-- 2nd Wrapper End -->
