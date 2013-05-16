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

	$primary_price = $sale_price != NULL ? $sale_price : $asking_price;

	$traffic_source = isset( $_COOKIE[ 'dealertrend-traffic-source' ] ) ? $_COOKIE[ 'dealertrend-traffic-source' ] : false;

	$traffic_source = $this->sanitize_inputs( $traffic_source );

?>
<link id="dealertrend-inventory-print-css" href="<?php echo $this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/armadillo/dealertrend-inventory-print.css' ?>" type="text/css" rel="stylesheet"/>

<div id="armadillo-wrapper">
	<div id="armadillo-detail-print">
<!-- Print Header Start -->
		<div class="armadillo-print-header">
			<div class="print-header-name"><?php echo $dealer_name ?></div>
			<div class="print-header-greeting"><?php echo $greeting ?></div>
			<div class="print-header-phone"><?php echo $phone ?></div>
		</div>
<!-- Print Header End -->
		<div class="armadillo-print-top">
<!-- Print Top Left Start -->
		<div class="armadillo-print-top-left">
			<div class="armadillo-main-line">
				<h2><?php echo $year . ' ' . $make . ' ' . $model . ' ' . $trim . ' ' . $drive_train . ' ' . $body_style . ' ' . $transmission; ?></h2>
				<!-- <p><?php echo $headline; ?></p> -->
			</div>
			<div class="armadillo-vehicle-information">
				<div class="vehicle-info-left-column">
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

				</div>
				<div class="vehicle-info-right-column">
					<div><span>Stock Number:</span> <?php echo $stock_number; ?></div>
					<div><span>Odometer:</span> <?php echo $odometer; ?></div>
					<div><span>VIN:</span> <?php echo $vin; ?></div>
				</div>
			</div>
			<div class="armadillo-price">
				<?php
					if( $on_sale && $sale_price > 0 ) {
						$now_text = 'Price: ';
						if( $use_was_now ) {
							$price_class = ( $use_price_strike_through ) ? 'armadillo-strike-through armadillo-asking-price' : 'armadillo-asking-price';
							if( $incentive_price > 0 ) {
								echo '<div class="armadillo-sale-price">Was: $' . number_format( $sale_price , 2 , '.' , ',' ) . '</div>';
								//if( $sale_expire != NULL ) {
								//	echo '<div class="armadillo-sale-expires">Sale Expires: ' . $sale_expire  . '</div>';
								//}
							} else {
								echo '<div class="' . $price_class . '">Was: $' . number_format( $asking_price , 2 , '.' , ',' ) . '</div>';
							}
							$now_text = 'Now: ';
						}
						if( $incentive_price > 0 ) {
							//echo '<div class="armadillo-ais-incentive">Savings: ' . $ais_incentive . '</div>';
							echo '<div class="armadillo-sale-price">' . $now_text . '$' . number_format( $sale_price - $incentive_price , 2 , '.' , ',' ) . '</div>';
						} else {
							//if( $ais_incentive != NULL ) {
							//	echo '<div class="armadillo-ais-incentive">Savings: ' . $ais_incentive . '</div>';
							//}
							echo '<div class="armadillo-sale-price">' . $now_text . '$' . number_format( $sale_price , 2 , '.' , ',' ) . '</div>';
							if( $sale_expire != NULL ) {
								echo '<div class="armadillo-sale-expires">Sale Expires: ' . $sale_expire  . '</div>';
							}
						}
					} else {
						if( $asking_price > 0 ) {
							if( $incentive_price > 0 ) {
								echo '<div class="armadillo-asking-price">Asking Price: ' . '$' . number_format( $asking_price , 2 , '.' , ',' ) . '</div>';
								//echo '<div class="armadillo-ais-incentive">Savings: ' . $ais_incentive . '</div>';
								echo '<div class="armadillo-asking-price">Your Price: ' . '$' . number_format( $asking_price - $incentive_price , 2 , '.' , ',' ) . '</div>';
							} else {
								//if( $ais_incentive != NULL ) {
								//	echo '<div class="armadillo-ais-incentive">Savings: ' . $ais_incentive . '</div>';
								//}
								echo '<div class="armadillo-asking-price">Price: ' . '$' . number_format( $asking_price , 2 , '.' , ',' ) . '</div>';
							}
						} else {
							//if( $ais_incentive != NULL ) {
							//	echo '<div class="armadillo-ais-incentive">Savings: ' . $ais_incentive . '</div>';
							//}
							echo $default_price_text;
						}
					}
					if( $ais_incentive != NULL ) {
										//echo '<div style="float:none; " class="armadillo-ais-incentive view-available-rebates"><a href="http://onecar.aisrebates.com/dlr2/inline/IncentiveOutput.php?vID='. $vin . '&wID=' . $company_information->api_keys->ais . '&zID=' . $company_information->zip . '" target="_blank" title="VIEW AVAILABLE INCENTIVES AND REBATES" style="display:block; width:280px !important;" onClick="return loadIframe( this.href );">
										//	VIEW AVAILABLE INCENTIVES AND REBATES
										//</a></div>';
					}
				?>
			</div>
<!--
			<div class="armadillo-contact-information">
				<div class="armadillo-header">Contact Information</div>
				<?php
					echo '<p>' . $greeting . ' <strong>' . $dealer_name . '</strong></p>';
					if( $internet_manager != NULL ) {
						echo '<p><span id="armadillo-internet-manager-label">Internet Manager:</span> <strong>' . $internet_manager . '</strong></p>';
					}
					if( $phone != NULL ) {
						echo '<p>Phone Number: <strong>' . $phone . '</strong></p>';
					}
				?>
			</div>
-->

			<br class="armadillo-clear" />
		</div>
<!-- Print Top Left End -->

<!-- Print Top Right Start -->
		<div class="armadillo-print-top-right">
			<div class="armadillo-slideshow">
				<?php if( count( $inventory->photos ) ): ?>
				<div class="armadillo-images">
				<?php
					foreach( array_slice($inventory->photos,0,1) as $photo ) {
						echo '<a class="lightbox" rel="slides" href="' . str_replace( '&' , '&amp;' , $photo->large ) . '" title="' . $company_name . '">';
						echo '<img src="' . str_replace( '&' , '&amp;' , $photo->medium ) . '" width="300" alt="" />';
						echo '</a>';
					}
				?>
				</div>
				<?php endif; ?>
			</div>
		</div>
<!-- Print Top Right End -->
		</div>
		<br class="armadillo-clear" />
<!-- Print Bottom Left Start -->
		<div class="armadillo-print-bottom-left">
			<?php
				if( ! empty( $dealer_options ) ) {
			?>
			<div id="armadillo-print-options">
				<?php
					echo ! empty( $dealer_options ) ? '<div id="print-options-header">Vehicle Details</div>' : NULL;
					if( ! empty( $dealer_options ) ) {
				?>
					<div id="print-options-details">
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
		</div>
<!-- Print Bottom Left End -->

<!-- Print Bottom Right Start -->
		<div class="armadillo-print-bottom-right">
			<?php
				if( ! empty( $description ) ) {
			?>
			<div id="armadillo-print-description">
				<?php
					echo ! empty( $description ) ? '<div id="print-desc-header">Vehicle Notes</div>' : NULL;
					echo ! empty( $description ) ? '<div id="print-desc-details"><p>' . $description . '</p></div>' : NULL;
				?>
			</div>
			<?php } ?>
		</div>
<!-- Print Bottom Right End -->
		<br class="armadillo-clear" />
<!-- Print Disclaimer -->
		<div class="armadillo-print-disclaimer">
			<p><?php echo $inventory->disclaimer; ?></p>
		</div>
	</div>
</div>
<script type="text/javascript">
//Sets the two detail columns to the same height
left_column = document.getElementById('print-options-details');
right_column = document.getElementById('print-desc-details');

if ( left_column.offsetHeight >= right_column.offsetHeight ){
	right_column.style.height = left_column.offsetHeight + 'px';
} else {
	left_column.style.height = right_column.offsetHeight + 'px';
}


</script>
