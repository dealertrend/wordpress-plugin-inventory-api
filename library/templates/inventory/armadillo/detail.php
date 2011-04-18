<?php
	# Easy to use variables.
	$sale_class = str_replace( ' ' , '%20' , $inventory->saleclass );
	setlocale(LC_MONETARY, 'en_US');
	$price = money_format( '%(#0n' , $inventory->prices->asking_price );
	$vin = $inventory->vin;
	$odometer = $inventory->odometer;
	$stock_number = $inventory->stock_number;
	$exterior_color = $inventory->exterior_color;
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

?>
<div id="detail">
	<div id="main-line">
		<?php echo '<h2>' . $year . ' ' . $make . ' ' . $model . ' ' . $trim . ' ' . $doors . 'D ' . $transmission . '</h2>'; ?>
	</div>
	<div id="column-left">
		<div id="request-form">
			<div class="form">
				<div class="header">
					Make an Offer / Get Info
				</div><!-- .header -->
				<form action="<?php echo $this->options[ 'api' ][ 'vehicle_management_system' ] . '/' . $this->options[ 'company_information' ][ 'id' ]; ?>/forms/create/<?php echo strtolower($sale_class); ?>_vehicle_inquiry" method="post"				name="vehicle-inquiry">
					<input name="required_fields" type="hidden" value="name,email,privacy" />
					<input name="subject" type="hidden" value="Vehicle Inquiry - <?php echo $headline; ?>" />
					<input name="saleclass" type="hidden" value="<?php echo $sale_class; ?>" />
					<input name="vehicle" type="hidden" value="<?php echo $year_make_model; ?>" />
					<input name="year" type="hidden" value="<?php echo $year; ?>" />
					<input name="make" type="hidden" value="<?php echo $make; ?>" />
					<input name="model_name" type="hidden" value="<?php echo $model; ?>" />
					<input name="trim" type="hidden" value="<?php echo $trim; ?>" />
					<input name="stock" type="hidden" value="<?php echo $stock; ?>" />
					<input name="vin" type="hidden" value="<?php echo $vin; ?>" />
					<input name="inventory" type="hidden" value="<?php echo $inventory->id; ?>" />
					<input name="price" type="hidden" value="<?php echo $price; ?>" />
					<input name="name" type="hidden" value="" />
					<table>
						<tr>
							<td class="required">
								<label for="vehicle-inquiry-f-name">First Name</label>
								<input maxlength="70" id="vehicle-inquiry-f-name" name="f_name" tabindex="1" type="text" />
							</td><!-- .required -->
						</tr>
						<tr>
							<td class="required">
								<label for="vehicle-inquiry-l-name">Last Name</label>
								<input maxlength="70" id="vehicle-inquiry-l-name" name="l_name" tabindex="2" type="text" />
							</td><!-- .required -->
						</tr>
							<td class="required">
								<label for="vehicle-inquiry-email">Email Address</label>
								<input maxlength="255" id="vehicle-inquiry-email" name="email" tabindex="3" type="text" />
							</td><!-- .required -->
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
							</td><!-- .required -->
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
			</div><!-- .form -->
		</div>
		<div id="helpful-links">
			<div class="header">
				Helpful Links
			</div>
			<a id="schedule" href="">Schedule Test Drive</a>
			<a id="trade" href="">Trade Evaluation</a>
			<a id="facebook" href="">Share on Facebook</a>
			<a id="approved" href="">Get Approved Now</a>
			<a id="friend" href="">Send to a Friend</a>
			<a id="calculate" href="">Calculate Payments</a>
		</div>
		<div id="carfax">
			<a href="">Carfax</a>
		</div>
		<br class="clear" />
	</div>
	<div id="column-middle">
		<div id="vehicle-information">
			<div class="header">
				Vehicle Information
			</div>
			<div><span>Color:</span> <?php echo $exterior_color; ?></div>
			<div><span>Engine:</span> <?php echo $engine; ?></div>
			<div><span>Transmission:</span> <?php echo $transmission; ?></div>
			<div><span>Mileage:</span> <?php echo $odometer; ?></div>
			<div><span>Stock Number:</span> <?php echo $stock_number; ?></div>
			<div><span>VIN:</span> <?php echo $vin; ?></div>
			<div class="price"><span>Price:</span> <?php echo $price; ?></div>
		</div>
		<div id="fuel-economy">
			<?php
				$fuel_city = !empty( $fuel_economy ) && !empty( $fuel_economy->city ) ? $fuel_economy->city : '-';
				$fuel_highway = !empty( $fuel_economy ) && !empty( $fuel_economy->highway ) ? $fuel_economy->highway : '-';
			?>
			<div id="fuel-city">
				<p>City</p>
				<p><strong><?php echo $fuel_city; ?></strong></p>
			</div>
			<div id="fuel-highway">
				<p>Highway</p>
				<p><strong><?php echo $fuel_highway; ?></strong></p>
			</div>
			<p><small>Actual mileage will vary with options, driving conditions, driving habits and vehicle's condition.</small></p>
		</div>
		<div id="icons">
			<?php echo $icons; ?>
		</div>
		<div id="tabs">
		</div>
		<br class="clear" />
	</div>
	<div id="column-right">
		<div class="slideshow">
			<div class="images">
			<?php
				foreach( $inventory->photos as $photo ) {
					echo '<img src="' . str_replace( '&' , '&amp;' , $photo->medium ) . '" width="320" height="240" />';
				}
			?>
			</div><!-- .images -->
			<div class="navigation"></div><!-- .navigation -->
		</div><!-- .slideshow -->
		<br class="clear" />
	</div>
</div><!-- #detail -->
