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
	$contact_information = $inventory->contact_info;
	$greeting = isset( $contact_information->greeting ) ? $contact_information->greeting : NULL;
	$dealer_name = isset( $contact_information->dealer_name ) ? $contact_information->dealer_name : NULL;
	$phone = isset( $contact_information->phone ) ? $contact_information->phone : NULL;
	$carfax = isset( $inventory->carfax ) ? $inventory->carfax->url : false;

	$primary_price = $sale_price != NULL ? $sale_price : $asking_price;

?>
<div id="cobra">
	<div id="cobra-detail-page">
		<div class="top">
			<div class="wrapper">
				<div class="year"><?php echo $year ?></div>
				<div class="vehicle"><?php echo $make . ' ' . $model ?></div>
				<div class="price">
					<div class="price">
					<?php if ($sale_class == 'New' && ! $on_sale):?>
					<span>MSRP</span>
					<?php endif; ?>
					<?php
					if( $on_sale && $sale_price > 0 ) {
						$now_text = 'Price: ';
						if( $use_was_now ) {
							$price_class = ( $use_price_strike_through ) ? 'strike-through asking-price' : 'asking-price';
							echo '<div class="' . $price_class . '">Was: $' . number_format( $asking_price , 0 , '.' , ',' ) . '</div>';
$now_text = 'Now: ';
						}
						echo '<div class="sale-price">' . $now_text . '$' . number_format( $sale_price , 0 , '.' , ','  ) . '</div>';
					} else {
						if( $asking_price > 0 ) {
							echo '<div class="asking-price"><span>$' . number_format( $asking_price , 0 , '.' , ','  ) . '</span></div>';
						} else {
							echo '<div>' . $default_price_text . '</div>';
						}
					}
					?>
					</div>
				</div>
			</div>
		</div>
		<div class="left">
			<div class="vehicle-information">
				<div class="header">
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
			<?php
				$fuel_city = !empty( $fuel_economy ) && !empty( $fuel_economy->city ) ? $fuel_economy->city : false;
				$fuel_highway = !empty( $fuel_economy ) && !empty( $fuel_economy->highway ) ? $fuel_economy->highway : false;
				if( $fuel_city != false && $fuel_highway != false ) {
			?>
			<div class="fuel-economy">
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
			<div class="detail-buttons">
				<a id="calculate" href="">Payment Calculator</a>
			</div>
			<br class="clear" />
			<?php
				if( $carfax ) { 
 					echo '<a href="' . $carfax . '" class="cobra-carfax" target="_blank">Carfax</a>';
     		}   
   		?>
			<br class="clear" />
		</div>
		<div class="right">
			<div class="slideshow">
				<div class="images">
				<?php
					foreach( $inventory->photos as $photo ) {
						echo '<img src="' . str_replace( '&' , '&amp;' , $photo->large ) . '" />';
					}
				?>
				</div>
				<div id="cobra-mobile-nav">
					<a id="cobra-prev" href="#">< Prev</a>
					<a id="cobra-pause" href="#">Pause</a>
					<a id="cobra-next" href="#">Next ></a>
				</div>
				<?php
					if( count( $inventory->photos > 1 ) ) {
						echo '<div class="navigation"></div>';
					}
				?>
			</div>
			<?php if( count( $dealer_options ) > 0 ) { ?>
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
			<br class="clear" />
		</div>
	</div>
	<br class="clear" />
	<?php
		if ( is_active_sidebar( 'vehicle-detail-page' ) ) :
			echo '<div id="sidebar-widget-area" class="sidebar">';
			dynamic_sidebar( 'vehicle-detail-page' );
			echo '</div>';
		endif;
	?>
	<br class="clear" />
</div>
<div id="calculate-form" title="Calculate Payments">
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
