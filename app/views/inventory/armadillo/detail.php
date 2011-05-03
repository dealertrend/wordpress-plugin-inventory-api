<?php

	$sale_class = str_replace( ' ' , '%20' , $inventory->saleclass );
	setlocale( LC_MONETARY , 'en_US' );
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
	$make = $inventory->make;
	$model = $inventory->model_name;
	$trim = $inventory->trim;
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
<p class="armadillo-validate-tips">All form fields are required.</p>
	<form name="formvehicletestdrive" action="<?php echo $this->options[ 'vehicle_management_system' ][ 'host' ] . '/' . $this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ]; ?>/forms/create/<?php echo strtolower($sale_class); ?>_vehicle_test_drive" method="post">
		
	</form>

	<form>
	<fieldset>
		<label for="name">Name</label>
		<input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" />
		<label for="email">Email</label>
		<input type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all" />
		<label for="password">Password</label>
		<input type="password" name="password" id="password" value="" class="text ui-widget-content ui-corner-all" />
	</fieldset>
	</form>

</div>

<div id="armadillo-friend-form" title="Send to a Friend">
friend
</div>

<div id="armadillo-calculate-form" title="Calculate Payments">
calculate
</div>

<div id="armadillo-inventory-wrapper">
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
					<form action="<?php echo $this->options[ 'vehicle_management_system' ][ 'host' ] . '/' . $this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ]; ?>/forms/create/<?php echo strtolower( $sale_class ); ?>_vehicle_inquiry" method="post" name="vehicle-inquiry">
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
						<input name="price" type="hidden" value="<?php echo $price; ?>" />
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
					echo '<p>' . $contact_information->greeting . ' <strong>' . $contact_information->dealer_name . '</strong></p>';
					echo '<p>Phone Number: <strong>' . $contact_information->phone . '</strong></p>';
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
					echo '<a href="' . $carfax . '" class="armadillo-carfax">Carfax</a>';
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
					if( $on_sale ) {
						$now_text = '<span>Price:</span> ';
						if( $use_was_now ) {
							$price_class = ( $use_price_strike_through ) ? 'armadillo-strike-through armadillo-asking-price' : 'armadillo-asking-price';
							echo '<div class="' . $price_class . '"><span>Was:</span> ' . money_format( '%(#0n' , $asking_price ) . '</div>';
							$now_text = '<span>Now:</span> ';
						}
						echo '<div class="armadillo-sale-price">' . $now_text . money_format( '%(#0n' , $sale_price ) . '</div>';
					} else {
						if( $asking_price > 0 ) {
							echo '<div class="armadillo-asking-price"><span>Price:</span> ' . money_format( '%(#0n' , $asking_price ) . '</div>';
						} else {
							echo $default_price_text;
						}
					}
				?>
				</div>
			</div>
			<div class="armadillo-fuel-economy">
				<?php
					$fuel_city = !empty( $fuel_economy ) && !empty( $fuel_economy->city ) ? $fuel_economy->city : '-';
					$fuel_highway = !empty( $fuel_economy ) && !empty( $fuel_economy->highway ) ? $fuel_economy->highway : '-';
				?>
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
			<div class="armadillo-icons">
				<?php echo $icons; ?>
			</div>
			<div id="armadillo-inventory-tabs">
				<ul>
					<li><a href="#armadillo-options">Vehicle Details</a></li>
					<li><a href="#armadillo-description">Comments</a></li>
				</ul>
				<div id="armadillo-description">
					<?php echo '<p>' . $description . '</p>'; ?>
				</div>
				<div id="armadillo-options">
					<ul>
					<?php
						foreach( $dealer_options as $option ) {
							echo '<li>' . $option . '</li>';
						}
					?>
					</ul>
				</div>
			</div>
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
				<div class="armadillo-images">
				<?php
					foreach( $inventory->photos as $photo ) {
						echo '<img src="' . str_replace( '&' , '&amp;' , $photo->medium ) . '" width="320" height="240" />';
					}
				?>
				</div>
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
