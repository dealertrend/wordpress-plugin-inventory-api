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
	$exterior_color = empty( $inventory->exterior_color ) ? 'N/A' : $inventory->exterior_color;
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
	$acode = $inventory->ads_acode;

	$primary_price = $sale_price != NULL ? $sale_price : $asking_price;

	$traffic_source = isset( $_COOKIE[ 'dealertrend-traffic-source' ] ) ? $_COOKIE[ 'dealertrend-traffic-source' ] : false;

	$traffic_source = $this->sanitize_inputs( $traffic_source );

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

<div id="armadillo-schedule-form"	title="Schedule a Test Drive">
	<h3>Schedule a Test Drive</h3>
	<p class="armadillo-validate-tips">Name, email and phone number fields are required.</p>
	<form name="formvehicletestdrive" id="formvehicletestdrive" action="<?php echo $this->options[ 'vehicle_management_system' ][ 'host' ] . '/' . $this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ]; ?>/forms/create/<?php echo strtolower($sale_class); ?>_vehicle_test_drive" method="post">
		<fieldset>
			<input type="hidden" name="traffic_source" value="<?php echo $traffic_source; ?>"/>
			<input type="hidden" name="required_fields" value="name,email,privacy"/>
			<input type="hidden" name="saleclass" value="<?php echo strtolower($sale_class); ?>"/>
			<input type="hidden" name="return_url" value="<?php echo $site_url; ?>" id="return_url_test_drive"/>
			<input type="hidden" name="vehicle" value="<?php echo $year . ' ' . $make . ' ' . $model; ?>"/>
			<input type="hidden" name="year" value="<?php echo $year; ?>"/>
			<input type="hidden" name="make" value="<?php echo $make; ?>"/>
			<input type="hidden" name="model_name" value="<?php echo $model; ?>"/>
			<input type="hidden" name="trim" value="<?php echo $trim; ?>"/>
			<input type="hidden" name="stock" value="<?php echo $stock_number; ?>"/>
			<input type="hidden" name="vin" value="<?php echo $vin; ?>"/>
			<input type="hidden" name="inventory" value="<?php echo $inventory->id; ?>"/>
			<input type="hidden" name="price" value="<?php echo $primary_price; ?>"/>
			<label for="formvehicletestdrive-name">Your First &amp; Last Name</label>
			<input type="text" maxlength="70" name="name" id="formvehicletestdrive-name" class="text ui-widget-content ui-corner-all" />
			<label for="formvehicletestdrive-email">Email Address</label>
			<input type="text" maxlength="255" name="email" id="formvehicletestdrive-email" class="text ui-widget-content ui-corner-all" />
			<label for="formvehicletestdrive-phone">Phone Number</label>
			<input type="text" maxlength="256" name="phone" id="formvehicletestdrive-phone" class="text ui-widget-content ui-corner-all" />
			<label for="formvehicletestdrive-timetocall">Best Time To Call</label>
			<input type="text" maxlength="256" name="timetocall" id="formvehicletestdrive-timetocall" class="text ui-widget-content ui-corner-all" />
			<label for="formvehicletestdrive-subject">Subject</label>
			<input type="text" maxlength="256" name="subject" id="formvehicletestdrive-subject" value="Vehicle Test Drive - <?php echo $year_make_model; ?>" class="text ui-widget-content ui-corner-all" />
			<label for="formvehicletestdrive-comments">Comments</label>
			<textarea rows="10" cols="35" name="comments" id="formvehicletestdrive-comments" class="text ui-widget-content ui-corner-all" ></textarea>
			<label for="formvehicletestdrive-privacy" style="float:left; margin-right:10px;">Agree to <a target="_blank" href="/privacy">Privacy Policy</a></label>
			<input type="checkbox" class="privacy" id="formvehicletestdrive-privacy" name="privacy" value="Yes" checked="checked" />
			<div style="display:none">
				<input type="checkbox" name="agree_sb" value="Yes" /> I am a Spam Bot?
			</div>
		</fieldset>
	</form>
</div>

<div id="armadillo-wrapper">
	<div id="armadillo-detail">
		<a id="friendly-print" onclick="window.open('?print_page','popup','width=800,height=900,scrollbars=yes,resizable=no,toolbar=no,directories=no,location=no,menubar=yes,status=no,left=0,top=0'); return false">Print</a>
		<?php echo $breadcrumbs; ?>
		<div class="armadillo-main-line">
			<h2><?php echo $year . ' ' . $make . ' ' . $model . ' ' . $trim . ' ' . $drive_train . ' ' . $body_style . ' ' . $transmission; ?></h2>
			<p><?php echo $headline; ?></p>
		</div>
		<div class="armadillo-column-left">
			<div class="armadillo-slideshow">
				<?php if( count( $inventory->photos ) ): ?>
				<div class="armadillo-images">
				<?php
					foreach( $inventory->photos as $photo ) {
						echo '<a class="lightbox" rel="slides" href="' . str_replace( '&' , '&amp;' , $photo->large ) . '" title="' . $company_name . '">';
						echo '<img src="' . str_replace( '&' , '&amp;' , $photo->medium ) . '" width="300" alt="" />';
						echo '</a>';
					}
				?>
				</div>
				<?php endif; ?>
				<?php
					if( $video_url ) {
						echo '<a onClick="return video_popup(this, \'' . $year_make_model . '\')" href="' . $video_url . '" class="armadillo-video-button">Watch Video for this Vehicle</a>';
					}
					if( count( $inventory->photos > 1 ) ) {
						echo '<div class="armadillo-navigation"></div>';
					}
				?>
			</div>
			<br class="armadillo-clear" />
		</div>
		<div class="armadillo-column-middle">
			<div class="armadillo-vehicle-information">
				<div class="armadillo-header">
					Vehicle Information
				</div>
				<?php
					$ais_incentive = isset( $inventory->ais_incentive->to_s ) ? $inventory->ais_incentive->to_s : NULL;
					$incentive_price = 0;
					if( $ais_incentive != NULL ) {
						preg_match( '/\$\d*(\s)?/' , $ais_incentive , $incentive );
						$incentive_price = isset( $incentive[ 0 ] ) ? str_replace( '$' , NULL, $incentive[ 0 ] ) : 0;
					}
					if( $retail_price > 0 ) {
						echo '<div class="armadillo-msrp"><span>MSRP:</span> $' . number_format( $retail_price , 2 , '.' , ',' ) . '</div>';
					}
				?>
				<div><span>Color:</span> <?php echo $exterior_color; ?></div>
				<div><span>Engine:</span> <?php echo $engine; ?></div>
				<div><span>Transmission:</span> <?php echo $transmission; ?></div>
				<div><span>Odometer:</span> <?php echo $odometer; ?></div>
				<div><span>Stock Number:</span> <?php echo $stock_number; ?></div>
				<div><span>VIN:</span> <?php echo $vin; ?></div>
				<div class="armadillo-price">
				<?php
					if( $on_sale && $sale_price > 0 ) {
						$now_text = 'Price: ';
						if( $use_was_now ) {
							$price_class = ( $use_price_strike_through ) ? 'armadillo-strike-through armadillo-asking-price' : 'armadillo-asking-price';
							if( $incentive_price > 0 ) {
								echo '<div class="armadillo-sale-price">Was: $' . number_format( $sale_price , 2 , '.' , ',' ) . '</div>';
								if( $sale_expire != NULL ) {
									echo '<div class="armadillo-sale-expires">Sale Expires: ' . $sale_expire  . '</div>';
								}
							} else {
								echo '<div class="' . $price_class . '">Was: $' . number_format( $asking_price , 2 , '.' , ',' ) . '</div>';
							}
							$now_text = 'Now: ';
						}
						if( $incentive_price > 0 ) {
							echo '<div class="armadillo-ais-incentive">Savings: ' . $ais_incentive . '</div>';
							echo '<div class="armadillo-sale-price">' . $now_text . '$' . number_format( $sale_price - $incentive_price , 2 , '.' , ',' ) . '</div>';
						} else {
							if( $ais_incentive != NULL ) {
								echo '<div class="armadillo-ais-incentive">Savings: ' . $ais_incentive . '</div>';
							}
							echo '<div class="armadillo-sale-price">' . $now_text . '$' . number_format( $sale_price , 2 , '.' , ',' ) . '</div>';
							if( $sale_expire != NULL ) {
								echo '<div class="armadillo-sale-expires">Sale Expires: ' . $sale_expire  . '</div>';
							}
						}
					} else {
						if( $asking_price > 0 ) {
							if( $incentive_price > 0 ) {
								echo '<div class="armadillo-asking-price">Asking Price: ' . '$' . number_format( $asking_price , 2 , '.' , ',' ) . '</div>';
								echo '<div class="armadillo-ais-incentive">Savings: ' . $ais_incentive . '</div>';
								echo '<div class="armadillo-asking-price">Your Price: ' . '$' . number_format( $asking_price - $incentive_price , 2 , '.' , ',' ) . '</div>';
							} else {
								if( $ais_incentive != NULL ) {
									echo '<div class="armadillo-ais-incentive">Savings: ' . $ais_incentive . '</div>';
								}
								echo '<div class="armadillo-asking-price">Price: ' . '$' . number_format( $asking_price , 2 , '.' , ',' ) . '</div>';
							}
						} else {
							if( $ais_incentive != NULL ) {
								echo '<div class="armadillo-ais-incentive">Savings: ' . $ais_incentive . '</div>';
							}
							echo $default_price_text;
						}
					}
					if( $ais_incentive != NULL ) {
										echo '<div style="float:none; " class="armadillo-ais-incentive view-available-rebates"><a href="http://onecar.aisrebates.com/dlr2/inline/IncentiveOutput.php?vID='. $vin . '&wID=' . $company_information->api_keys->ais . '&zID=' . $company_information->zip . '" target="_blank" title="VIEW AVAILABLE INCENTIVES AND REBATES" style="display:block; width:280px !important;" onClick="return loadIframe( this.href );">
											VIEW AVAILABLE INCENTIVES AND REBATES
										</a></div>';
					}
				?>
				</div>
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
					$fuel_text = '<div class="armadillo-fuel-economy">';
					$fuel_text .= '<div id="armadillo-fuel-city">';
					$fuel_text .= '<p>City</p>';
					$fuel_text .= '<p><strong>' . $fuel_city . '</strong></p>';
					$fuel_text .= '</div>';
					$fuel_text .= '<div id="armadillo-fuel-highway">';
					$fuel_text .= '<p>Highway</p>';
					$fuel_text .= '<p><strong>' . $fuel_highway . '</strong></p>';
					$fuel_text .= '</div>';
					$fuel_text .= '<p><small>Actual mileage will vary with options, driving conditions, driving habits and vehicle&#39;s condition.</small></p>';
					$fuel_text .= '</div>';

					echo $fuel_text;
				}
				
			?>
			<?php
				if( !empty( $tags ) ){
					echo '<div class="armadillo-icons">';
						apply_special_tags( $tags, $on_sale, $certified_inv);
						$tag_icons = build_tag_icons( $default_tag_names, $custom_tag_icons, $tags);
						echo $tag_icons;
					echo '</div>';
				}
			?>
			<?php
				if( ! empty( $dealer_options ) || ! empty( $description ) ) {
			?>
				<div id="armadillo-inventory-tabs">
					<ul>
						<?php
							echo ! empty( $dealer_options ) ? '<li><a href="#armadillo-options">Vehicle Details</a></li>' : NULL;
							echo ! empty( $description ) ? '<li><a href="#armadillo-description">Comments</a></li>' : NULL;
						?>
					</ul>
					<?php
						echo ! empty( $description ) ? '<div id="armadillo-description"><p>' . $description . '</p></div>' : NULL;
						if( ! empty( $dealer_options ) ) {
					?>
					<div id="armadillo-options">
						<ul>
						<?php
							foreach( $dealer_options as $option ) {
								echo '<li>' . $option . '</li>';
							}
						?>
						</ul>
					</div>
					<?php } ?>
				</div>
			<?php } ?>
			<div id="armadillo-tabs">
				<div id="armadillo-description">
				</div>
				<div id="armadillo-equipment">
				</div>
			</div>
			<br class="armadillo-clear" />
		</div>
		<div class="armadillo-column-right">
			<div class="armadillo-request-form">
				<div class="armadillo-form">
					<div class="armadillo-header">
						Make an Offer / Get Info
					</div>
					<form action="<?php echo $this->options[ 'vehicle_management_system' ][ 'host' ] . '/' . $this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ]; ?>/forms/create/<?php echo strtolower( $sale_class ); ?>_vehicle_inquiry" method="post" name="vehicle-inquiry" id="vehicle-inquiry">
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
						<input name="name" type="hidden" value="" />
						<table>
							<tr>
								<td class="required">
									<label for="vehicle-inquiry-f-name">First Name</label>
									<input maxlength="70" id="vehicle-inquiry-f-name" name="f_name" tabindex="1" type="text" />
								</td>
							</tr>
							<tr>
								<td class="required">
									<label for="vehicle-inquiry-l-name">Last Name</label>
									<input maxlength="70" id="vehicle-inquiry-l-name" name="l_name" tabindex="2" type="text" />
								</td>
							</tr>
								<td class="required">
									<label for="vehicle-inquiry-email">Email Address</label>
									<input maxlength="255" id="vehicle-inquiry-email" name="email" tabindex="3" type="text" />
								</td>
							</tr>
							<tr>
								<td>
									<label for="vehicle-inquiry-phone">Phone Number</label>
									<input maxlength="256" name="phone" id="vehicle-inquiry-phone" tabindex="4" type="text" />
								</td>
							</tr>
							<tr>
								<td class="required">
									<label for="vehicle-inquiry-comments">Questions/Comments</label>
									<textarea name="comments" id="vehicle-inquiry-comments" rows="4" tabindex="5"></textarea>
								</td>
							</tr>
							<tr>
							<td>
									<div style="display:none">
										<input class="privacy" name="privacy" id="vehicle-inquiry-privacy" type="checkbox" value="Yes" checked="checked" />
										<input name="agree_sb" type="checkbox" value="Yes" /> I am a Spam Bot?
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<input onclick="document.forms['vehicle-inquiry']['name'].value = document.forms['vehicle-inquiry']['f_name'].value + ' ' + document.forms['vehicle-inquiry']['l_name'].value; document.forms['vehicle-inquiry']['privacy'].checked = true; document.forms['vehicle-inquiry'].submit();" type="button" value="Send Inquiry" class="submit" />
								</td>
							</tr>
						</table>
					</form>
				</div>
			</div>
			<div class="armadillo-contact-information">
				<div class="armadillo-header">Contact Information</div>
				<?php
					echo '<p>' . $greeting . ' <strong>' . $dealer_name . '</strong></p>';
					if ( strtolower( $sale_class ) == 'new' && !empty( $name_new ) ) {
						$name_value = $name_new;
					} elseif ( strtolower( $sale_class ) == 'used' && !empty( $name_used ) ) {
						$name_value = $name_used;
					} else {
						$name_value = $internet_manager;
					}
					if( $name_value != NULL ) {
						echo '<p><span id="armadillo-internet-manager-label">Internet Manager:</span> <strong>' . $name_value . '</strong></p>';
					}

					if ( strtolower( $sale_class ) == 'new' && !empty( $phone_new ) ) {
						$phone_value = $phone_new;
					} elseif ( strtolower( $sale_class ) == 'used' && !empty( $phone_used ) ) {
						$phone_value = $phone_used;
					} else {
						$phone_value = $phone;
					}
					if( $phone_value != NULL ) {
						echo '<p>Phone Number: <strong>' . $phone_value . '</strong></p>';
					}
				?>
			</div>
			<div class="armadillo-helpful-links">
				<div class="armadillo-header">
					Helpful Links
				</div>
				<a id="armadillo-schedule" href="#armadillo-schedule-form">Schedule Test Drive</a>
				<a id="armadillo-facebook" href="http://www.addthis.com/bookmark.php?pub=dealertrend&amp;v=250&amp;source=tbx-250&amp;s=facebook&url=&amp;title=&amp;content=" target="_blank">Share on Facebook</a>
				<a id="armadillo-friend" href="mailto:?Subject=<?php echo str_replace( ' ', '%20', $company_information->name ) . '%20-%20Tell-A-Friend%20-%20' . str_replace( ' ', '%20', $year_make_model); ?>">Send to a Friend</a>
				<a id="armadillo-calculate" class="hide-form">Calculate Payments</a>

				<div id="armadillo-calculate-form" title="Calculate Payments" style="width: 84%; padding: 2% 8%;">
					<div id="calculate-close-form">X</div>
					<h3 style="text-align: center;">Loan Calculator</h3>
					<form id="loan-calculator" name="loan-calculator" action="#" method="post">
						<table style="width:100%">
							<tr>
								<td colspan="1">
									<label for="loan-calculator-price">Vehicle Price</label>
									<input type="text" style="width:75%" name="price" id="loan-calculator-price" value="$<?php echo trim( number_format( $primary_price , 2 , '.' , ',' ) ); ?>" />
								</td>
								<td colspan="1">
									<label for="loan-calculator-interest-rate">Interest Rate</label>
									<input type="text" style="width:75%" name="interest-rate" id="loan-calculator-interest-rate" value="7.35%" />
								</td>
							</tr>
							<tr>
								<td colspan="1">
									<label for="loan-calculator-trade-in-value">Trade in Value</label>
									<input type="text" style="width:75%" name="trade-in-value" id="loan-calculator-trade-in-value" value="$3,000" />
								</td>
								<td colspan="1">
									<label for="loan-calculator-term">Term (months)</label>
									<input type="text" style="width:75%" name="term" id="loan-calculator-term" value="72" />
								</td>
							</tr>
							<tr>
								<td colspan="1">
									<label for="loan-calculator-down-payment">Down Payment</label>
									<input type="text" style="width:75%" name="down-payment" id="loan-calculator-down-payment" value="$5,000" />
								</td>
								<td colspan="1">
									<label for="loan-calculator-sales-tax">Sales Tax</label>
									<input type="text" style="width:75%" name="sales-tax" id="loan-calculator-sales-tax" value="7.375%" />
								</td>
							</tr>
							<tr>
								<td colspan="1">
									<div class="calc-label">Bi-Monthly Cost</div>
									<div class="calc-value" id="loan-calculator-bi-monthly-cost"></div>
								</td>
								<td colspan="1">
									<div class="calc-label">Monthly Cost</div>
									<div class="calc-value" id="loan-calculator-monthly-cost"></div>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<div class="calc-label-center">Total Cost <br><small>(including taxes)</small></div>
									<div class="calc-label-center calc-value" id="loan-calculator-total-cost"></div>
								</td>
							</tr>
							<tr>
								<td colspan="2"><br /><button>Calculate</button></td>
							</tr>
						</table>
					</form>
				</div>

			</div>
			<?php
				if( $carfax ) {
					echo '<a href="' . $carfax . '" class="armadillo-carfax" target="_blank">Carfax</a>';
				}
			?>
			<br class="armadillo-clear" />
		</div>
		<div class="armadillo-disclaimer">
			<p><?php echo $inventory->disclaimer; ?></p>
		</div>
	</div>
</div>
<?php
	if ( is_active_sidebar( 'vehicle-detail-page' ) ) :
		echo '<div id="sidebar-widget-area" class="sidebar">';
		dynamic_sidebar( 'vehicle-detail-page' );
		echo '</div>';
	endif;
?>
