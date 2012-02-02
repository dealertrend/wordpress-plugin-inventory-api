<?php

	$sale_class = str_replace( ' ' , '%20' , $inventory->saleclass );
	$prices = $inventory->prices;
	$use_was_now = $prices->{ 'use_was_now?' };
	$use_price_strike_through = $prices->{ 'use_price_strike_through?' };
	$on_sale = $prices->{ 'on_sale?' };
	$sale_price = isset( $prices->sale_price ) ? $prices->sale_price : NULL;
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

<div id="cobra-schedule-form"	title="Schedule a Test Drive">
	<h3>Schedule a Test Drive</h3>
	<p class="cobra-validate-tips">Name, email and phone number fields are required.</p>
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

<div id="cobra-friend-form" title="Send to a Friend">
	<h3>Send to a Friend</h3>
	<p class="cobra-validate-tips">Name, email and phone number fields are required.</p>
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

<div id="cobra-wrapper">
	<div id="cobra-detail">
		<div class="cobra-detail-top">
			<div class="cobra-detail-top-wrapper">
				<div class="cobra-detail-year"><?php echo $year ?></div>
				<div class="cobra-detail-vehicle"><?php echo $make . ' ' . $model ?></div>
				<div class="cobra-detail-price">
					<?php if ($sale_class == 'New'):?>
					<span>MSRP</span>
					<?php endif; ?>
					<div class="cobra-price">
					<?php
					if( $on_sale && $sale_price > 0 ) {
						$now_text = 'Price: ';
						if( $use_was_now ) {
							$price_class = ( $use_price_strike_through ) ? 'cobra-strike-through cobra-asking-price' : 'cobra-asking-price';
							echo '<div class="' . $price_class . '">Was: ' . money_format( '%(#0n' , $asking_price ) . '</div>';
$now_text = 'Now: ';
						}
						echo '<div class="cobra-sale-price">' . $now_text . money_format( '%(#0n' , $sale_price ) . '</div>';
					} else {
						if( $asking_price > 0 ) {
							echo '<div class="cobra-asking-price"><span>' . substr( money_format( '%(#0n' , $asking_price ) , 1 , 1) . '</span>' . substr( money_format( '%(#0.0n' , $asking_price ) , 2) . '</div>';
						} else {
							echo '<div>' . $default_price_text . '</div>';
						}
					}
					?>
					</div>
				</div>
				<div class="cobra-detail-cpo">
					<?php if ($inventory->certified) { ?>
					<img src="<?php echo plugin_dir_url(__FILE__); ?>images/<?php echo $make; ?>-cpo.png" />
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="cobra-column-left">
			<div class="cobra-vehicle-information">
				<div class="cobra-header">
					Vehicle Information
				</div>
				<div><span>Trim Level:</span> <?php echo $trim; ?></div>
				<div><span>Stock Number:</span> <?php echo $stock_number; ?></div>
				<div><span>VIN:</span> <?php echo $vin; ?></div>
				<div><span>Mileage:</span> <?php echo $odometer; ?></div>
				<div><span>Exterior Color:</span> <?php echo $exterior_color; ?></div>
				<div><span>Engine:</span> <?php echo $engine; ?></div>
				<div><span>Transmission:</span> <?php echo $transmission; ?></div>
				<div><span>Drivetrain:</span> <?php echo $drivetrain; ?></div>
			</div>
			<div class="cobra-detail-make-offer">
				<a href="#cobra-make-offer"></a>
			</div>
			<div class="cobra-sticker">
				<?php if ($sale_class == 'New'):?>
				<a href="http://fordlabels.webview.biz/webviewhybrid/WindowSticker.aspx?vin=<?php echo $vin; ?>" target="_blank">
					<img src="<?php echo bloginfo('wpurl'); ?>/wp-content/plugins/dealertrend-inventory-api/application/views/inventory/cobra/images/btn-detail-window-sticker.png" />
				</a>
				<?php endif; ?>
			</div>
			<?php
				$fuel_city = !empty( $fuel_economy ) && !empty( $fuel_economy->city ) ? $fuel_economy->city : false;
				$fuel_highway = !empty( $fuel_economy ) && !empty( $fuel_economy->highway ) ? $fuel_economy->highway : false;
				if( $fuel_city != false && $fuel_highway != false ) {
			?>
			<div class="cobra-fuel-economy">
				<div id="cobra-fuel-city">
					<p>City</p>
					<p><strong><?php echo $fuel_city; ?></strong></p>
				</div>
				<div id="cobra-fuel-highway">
					<p>Hwy</p>
					<p><strong><?php echo $fuel_highway; ?></strong></p>
				</div>
				<p><small>Actual mileage will vary with options, driving conditions, driving habits and vehicle's condition.</small></p>
			</div>
			<?php } ?>
			<div class="cobra-detail-buttons">
				<a id="cobra-calculate" href="">Payment Calculator</a>
			</div>
			<!--<div class="cobra-request-form">
				<div class="cobra-form">
					<div class="cobra-header">
						Request a Quote
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
						<div>						
							<input maxlength="70" id="vehicle-inquiry-f-name" name="f_name" tabindex="1" type="text" value="First Name" />
							<input maxlength="70" id="vehicle-inquiry-l-name" name="l_name" tabindex="2" type="text" value="Last Name" />
							<input maxlength="255" id="vehicle-inquiry-email" name="email" tabindex="3" type="text" value="Email" />
							<input maxlength="256" name="phone" id="vehicle-inquiry-phone" tabindex="4" type="text" value="Phone" />
							<textarea name="comments" id="vehicle-inquiry-comments" rows="4" tabindex="5">Comments</textarea>
							<div style="display:none">
								<input class="privacy" name="privacy" id="vehicle-inquiry-privacy" type="checkbox" value="Yes" />
								<input name="agree_sb" type="checkbox" value="Yes" /> I am a Spam Bot?
							</div>
							<input onclick="document.forms['vehicle-inquiry']['name'].value = document.forms['vehicle-inquiry']['f_name'].value + ' ' + document.forms['vehicle-inquiry']['l_name'].value; document.forms['vehicle-inquiry']['privacy'].checked = true; document.forms['vehicle-inquiry'].submit();" type="button" value="Send Inquiry" class="submit" />
						</div>
					</form>
				</div>
			</div>-->
			<br class="cobra-clear" />
		</div>
		<div class="cobra-column-right">
			<div class="cobra-slideshow">
				<div class="cobra-images">
				<?php
					foreach( $inventory->photos as $photo ) {
						echo '<img src="' . str_replace( '&' , '&amp;' , $photo->large ) . '" />';
					}
				?>
				</div>
				<?php
					if( $video_url ) {
						echo '<a onClick="return video_popup(this, \'' . $year_make_model . '\')" href="' . $video_url . '" class="cobra-video-button">Watch Video for this Vehicle</a>';
					}
					if( count( $inventory->photos > 1 ) ) {
						echo '<div class="cobra-navigation"></div>';
					}
				?>
			</div>
			<?php if (count($dealer_options) > 0){ ?>
			<div id="cobra-inventory-tabs">
				<ul>
					<li><a href="#cobra-dealer">Equipment / Features</a></li>
				</ul>
				<div id="cobra-dealer">
					<ul>
					<?php
						foreach( $dealer_options as $option ) {
							echo '<li>' . $option . '</li>';
						}
					?>
					</ul>
				</div>
			</div>
			<?php } ?>
			<br class="cobra-clear" />
		</div>
		<div class="cobra-contact-information">
				<?php echo '<p>CALL US TODAY AT 800-241-5712</p>'; ?>
		</div>
	</div>
	<div style="display: none;">
		<div id="cobra-make-offer">
			<h1>Make us an Offer</h1>
			<div class="required">*Required information</div>
			<?php echo do_shortcode('[contact-form-7 id="200" title="Make Offer Form"]'); ?>
		</div>
	</div>
</div>
<div id="cobra-calculate-form" title="Calculate Payments">
	<h3>Loan Calculator</h3>
	<form id="loan-calculator" name="loan-calculator" action="#" method="post">
		<table style="width:100%">
			<tr>
				<td colspan="1">
					<label for="loan-calculator-price">Vehicle Price</label>
					<input type="text" style="width:90%" name="price" id="loan-calculator-price" value="<?php echo trim( money_format( '%(#0n' , $primary_price ) ); ?>" />
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
