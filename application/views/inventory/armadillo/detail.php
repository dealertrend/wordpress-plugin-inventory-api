<?php

	$sale_class = str_replace( ' ' , '%20' , $inventory->saleclass );
	setlocale( LC_MONETARY , 'en_US' );
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

	$primary_price = $sale_price != NULL ? $sale_price : $asking_price;

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
	<form name="formvehicletestdrive" action="<?php echo $this->options[ 'vehicle_management_system' ][ 'host' ] . '/' . $this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ]; ?>/forms/create/<?php echo strtolower($sale_class); ?>_vehicle_test_drive" method="post">
		<fieldset>
			<input type="hidden" name="required_fields" value="name,email,privacy"/>
			<input type="hidden" name="saleclass" value="<?php echo strtolower($sale_class); ?>"/>
			<input type="hidden" name="return_url" value="" id="return_url_test_drive"/>
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
			<input type="checkbox" class="privacy" id="formvehicletestdrive-privacy" name="privacy" value="Yes" class="text ui-widget-content ui-corner-all" />
			<div style="display:none">
				<input type="checkbox" name="agree_sb" value="Yes" /> I am a Spam Bot?
			</div>
		</fieldset>
	</form>
</div>

<div id="armadillo-friend-form" title="Send to a Friend">
	<h3>Send to a Friend</h3>
	<p class="armadillo-validate-tips">Name, email and phone number fields are required.</p>
	<form name="formtellafriend" id="formtellafriend" action="<?php echo $this->options[ 'vehicle_management_system' ][ 'host' ] . '/' . $this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ]; ?>/forms/create/vehicle_tell_a_friend" method="post">
		<fieldset>
			<input type="hidden" name="required_fields" value="from_name,from_email,friend_name,friend_email,privacy"/>
			<input type="hidden" name="return_url" value="" id="return_url_tellafriend"/>
			<input type="hidden" name="vehicle" value="<?php echo $year_make_model; ?>"/>
			<input type="hidden" name="stock" value="<?php echo $stock_number; ?>"/>
			<input type="hidden" name="vin" value="<?php echo $vin; ?>"/>
			<label for="formtellafriend-from-name">Your First &amp; Last Name</label>
			<input type="text" maxlength="70" name="from_name" id="formtellafriend-from-name" class="text ui-widget-content ui-corner-all" />
			<label for="formtellafriend-from-email">Email Address</label>
			<input type="text" id="formtellafriend-from-email" maxlength="255" name="from_email" class="text ui-widget-content ui-corner-all" />
			<label for="formtellafriend-friend-name">Your Friend's Name</label>
			<input type="text" maxlength="70" name="friend_name" id="formtellafriend-friend-name" class="text ui-widget-content ui-corner-all" />
			<label for="formtellafriend-friend-email">Friend's Email Address</label>
			<input type="text" maxlength="255" name="friend_email" id="formtellafriend-friend-email"class="text ui-widget-content ui-corner-all" />
			<label for="formtellafriend-subject">Subject</label>
			<input type="text" maxlength="256" name="subject" id="formtellafriend-subject" value="<?php echo $company_information->name; ?> - Tell-A-Friend - <?php echo $year_make_model; ?>" class="text ui-widget-content ui-corner-all" />
			<label for="formtellafriend-comments">Comments</label>
			<textarea rows="10" name="comments" id="formtellafriend-comments" class="text ui-widget-content ui-corner-all" ></textarea>
			<label for="formtellafriend-notify" style="float:left; margin-right:10px;">Notify me when e-mail is opened</label>
			<input id="formtellafriend-notify" type="checkbox" name="notify" value="yes" />
			<label for="formtellafriend-privacy" style="float:left; margin-right:10px;">Agree to <a target="_blank" href="/privacy">Privacy Policy</a></label>
			<input type="checkbox" class="privacy" id="formtellafriend-privacy" name="privacy" value="Yes" />
			<div style="display:none">
				<input type="checkbox" name="agree_sb" value="Yes" /> I am a Spam Bot?
			</div>
		</fieldset>
	</form>
</div>

<div id="armadillo-calculate-form" title="Calculate Payments">
	<h3>Loan Calculator</h3>
	<form id="loan-calculator" name="loan-calculator" action="#" method="post">
		<table style="width:100%">
			<tr>
				<td colspan="1">
					<label for="loan-calculator-price">Vehicle Price</label>
					<input type="text" style="width:90%" name="price" id="loan-calculator-price" value="<?php echo money_format( '%(#0n' , $primary_price ); ?>" />
				</td>
				<td colspan="1">
					<label for="loan-calculator-interest-rate">Interest Rate</label>
					<input type="text" style="width:90%" name="interest-rate" id="loan-calculator-interest-rate" value="7.35%" />
				</td>
				<td colspan="1">
					<label for="loan-calculator-term">Term (months)</label>
					<input type="text" style="width:90%" name="term" id="loan-calculator-term" value="72" />
				</td>
			</tr>
			<tr>
				<td colspan="1">
					<label for="loan-calculator-trade-in-value">Trade in Value</label>
					<input type="text" style="width:90%" name="trade-in-value" id="loan-calculator-trade-in-value" value="$3,000" />
				</td>
				<td colspan="1">
					<label for="loan-calculator-down-payment">Down Payment</label>
					<input type="text" style="width:90%" name="down-payment" id="loan-calculator-down-payment" value="$5,000" />
				</td>
				<td colspan="1">
					<label for="loan-calculator-sales-tax">Sales Tax</label>
					<input type="text" style="width:90%" name="sales-tax" id="loan-calculator-sales-tax" value="7.375%" />
				</td>
			</tr>
			<tr>
				<td colspan="1">
					<div>Bi-Monthly Cost</div>
					<div id="loan-calculator-bi-monthly-cost"></div>
				</td>
				<td colspan="1">
					<div>Monthly Cost</div>
					<div id="loan-calculator-monthly-cost"></div>
				</td>
				<td colspan="1">
					<div>Total Cost <small>(including taxes)</small></div>
					<div id="loan-calculator-total-cost"></div>
				</td>
			</tr>
			<tr>
				<td colspan="3"><br /><button>Calculate</button></td>
			</tr>
		</table>
	</form>
</div>

<div id="armadillo-wrapper">
	<div id="armadillo-detail">
		<?php echo $breadcrumbs; ?>
		<div class="armadillo-main-line">
			<h2><?php echo $year . ' ' . $make . ' ' . $model . ' ' . $trim . ' ' . $drive_train . ' ' . $body_style . ' ' . $transmission; ?></h2>
			<p><?php echo $headline; ?></p>
		</div>
		<div class="armadillo-column-left">
			<div class="armadillo-request-form">
				<div class="armadillo-form">
					<div class="armadillo-header">
						Make an Offer / Get Info
					</div>
					<form action="<?php echo $this->options[ 'vehicle_management_system' ][ 'host' ] . '/' . $this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ]; ?>/forms/create/<?php echo strtolower( $sale_class ); ?>_vehicle_inquiry" method="post" name="vehicle-inquiry" id="vehicle-inquiry">
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
										<input class="privacy" name="privacy" id="vehicle-inquiry-privacy" type="checkbox" value="Yes" />
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
					echo '<p>Phone Number: <strong>' . $phone . '</strong></p>';
				?>
			</div>
			<div class="armadillo-helpful-links">
				<div class="armadillo-header">
					Helpful Links
				</div>
				<a id="armadillo-schedule" href="#armadillo-schedule-form">Schedule Test Drive</a>
				<a id="armadillo-facebook" href="http://www.addthis.com/bookmark.php?pub=dealertrend&amp;v=250&amp;source=tbx-250&amp;s=facebook&url=&amp;title=&amp;content=" target="_blank">Share on Facebook</a>
				<a id="armadillo-friend" href="#armadillo-friend-form">Send to a Friend</a>
				<a id="armadillo-calculate" href="#armadillo-calculate-form">Calculate Payments</a>
			</div>
			<?php
				if( $carfax ) {
					echo '<a href="' . $carfax . '" class="armadillo-carfax" target="_blank">Carfax</a>';
				}
			?>
			<br class="armadillo-clear" />
		</div>
		<div class="armadillo-column-middle">
			<div class="armadillo-vehicle-information">
				<div class="armadillo-header">
					Vehicle Information
				</div>
				<div><span>Color:</span> <?php echo $exterior_color; ?></div>
				<div><span>Engine:</span> <?php echo $engine; ?></div>
				<div><span>Transmission:</span> <?php echo $transmission; ?></div>
				<div><span>Mileage:</span> <?php echo $odometer; ?></div>
				<div><span>Stock Number:</span> <?php echo $stock_number; ?></div>
				<div><span>VIN:</span> <?php echo $vin; ?></div>
				<div class="armadillo-price">
				<?php
					$ais_incentive = isset( $inventory->ais_incentive->to_s ) ? $inventory->ais_incentive->to_s : NULL;
					$incentive_price = 0;
					if( $ais_incentive != NULL ) {
						preg_match( '/\$\d*\s/' , $ais_incentive , $incentive );
						$incentive_price = isset( $incentive[ 0 ] ) ? str_replace( '$' , NULL, $incentive[ 0 ] ) : 0;
					}
					if( $on_sale && $sale_price > 0 ) {
						$now_text = 'Price: ';
						if( $use_was_now ) {
							$price_class = ( $use_price_strike_through ) ? 'armadillo-strike-through armadillo-asking-price' : 'armadillo-asking-price';
							if( $incentive_price > 0 ) {
								echo '<div class="armadillo-sale-price">Was: ' . money_format( '%(#0n' , $sale_price ) . '</div>';
								if( $sale_expire != NULL ) {
									echo '<div class="armadillo-sale-expires">Sale Expires: ' . $sale_expire  . '</div>';
								}
							} else {
								echo '<div class="' . $price_class . '">Was: ' . money_format( '%(#0n' , $asking_price ) . '</div>';
							}
							$now_text = 'Now: ';
						}
						if( $incentive_price > 0 ) {
							echo '<div class="armadillo-ais-incentive">Savings: ' . $ais_incentive . '</div>';
							echo '<div class="armadillo-sale-price">' . $now_text . money_format( '%(#0n' , $sale_price - $incentive_price ) . '</div>';
						} else {
							if( $ais_incentive != NULL ) {
								echo '<div class="armadillo-ais-incentive">Savings: ' . $ais_incentive . '</div>';
							}
							echo '<div class="armadillo-sale-price">' . $now_text . money_format( '%(#0n' , $sale_price ) . '</div>';
							if( $sale_expire != NULL ) {
								echo '<div class="armadillo-sale-expires">Sale Expires: ' . $sale_expire  . '</div>';
							}
						}
					} else {
						if( $asking_price > 0 ) {
							if( $incentive_price > 0 ) {
								echo '<div class="armadillo-asking-price">Retail Price: ' . money_format( '%(#0n' , $asking_price ) . '</div>';
								echo '<div class="armadillo-ais-incentive">Savings: ' . $ais_incentive . '</div>';
								echo '<div class="armadillo-asking-price">Your Price: ' . money_format( '%(#0n' , $asking_price - $incentive_price ) . '</div>';
							} else {
								if( $ais_incentive != NULL ) {
									echo '<div class="armadillo-ais-incentive">Savings: ' . $ais_incentive . '</div>';
								}
								echo '<div class="armadillo-asking-price">Price: ' . money_format( '%(#0n' , $asking_price ) . '</div>';
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
				if( $fuel_city != false && $fuel_highway != false ) {
			?>
			<div class="armadillo-fuel-economy">
				<div id="armadillo-fuel-city">
					<p>City</p>
					<p><strong><?php echo $fuel_city; ?></strong></p>
				</div>
				<div id="armadillo-fuel-highway">
					<p>Highway</p>
					<p><strong><?php echo $fuel_highway; ?></strong></p>
				</div>
				<p><small>Actual mileage will vary with options, driving conditions, driving habits and vehicle's condition.</small></p>
			</div>
			<?php } ?>
			<div class="armadillo-icons">
				<?php echo $icons; ?>
			</div>
			<?php
				if( ! empty( $dealer_options ) && ! empty( $description ) ) {
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
			<div class="armadillo-slideshow">
				<?php if( count( $inventory->photos ) ): ?>
				<div class="armadillo-images">
				<?php
					foreach( $inventory->photos as $photo ) {
						echo '<img src="' . str_replace( '&' , '&amp;' , $photo->medium ) . '" width="320" height="240" />';
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
		<div class="armadillo-disclaimer">
			<p><?php echo $inventory->disclaimer; ?></p>
		</div>
	</div>
</div>