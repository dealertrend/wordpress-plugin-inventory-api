<?php

	# Easy to use variables.
	$headline = $inventory->headline;
	$sale_class = str_replace( ' ' , '%20' , $inventory->saleclass );
	$price = $inventory->pricing;
	$vin = $inventory->vin;
	$odometer = $inventory->odometer;
	$stock = $inventory->stock_number;
	$exterior_color = $inventory->exterior_color;
	$engine = $inventory->engine;
	$transmission = $inventory->transmission;
	$drivetrain = $inventory->drive_train;
	$dealer_options = $inventory->dealer_options;
	$equipment = $inventory->standard_equipment;
	$year = $inventory->year;
	$make = $inventory->make;
	$model = $inventory->model_name;
	$trim = $inventory->trim;
	$year_make_model = $year . ' ' . $make . ' ' . $model;
?>
<div class="dealertrend inventory detail">
	<div class="headline">
		<h2><?php echo $headline; ?></h2>
	</div><!-- .headline -->
	<div class="left-column">
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
	</div><!-- .left-column -->
	<?php flush(); ?>
	<div class="right-column">
		<div class="details">
			<div class="header">
				<strong>Vehicle Information</strong>
			</div><!-- .header -->
			<div class="row"><strong>Price:</strong> <?php echo $price; ?></div>
			<div class="row"><strong>Stock:</strong> <?php echo $stock; ?></div>
			<div class="row"><strong>VIN:</strong> <?php echo $vin; ?></div>
			<div class="row"><strong>Odomter:</strong> <?php echo $odometer; ?></div>
			<div class="row"><strong>Exterior Color:</strong> <?php echo $exterior_color; ?></div>
			<div class="row"><strong>Engine:</strong> <?php echo $engine; ?></div>
			<div class="row"><strong>Transmission:</strong> <?php echo $transmission; ?></div>
			<div class="row"><strong>Drivetrain:</strong> <?php echo $drivetrain; ?></div>
		</div><!-- .details -->
		<?php flush(); ?>
		<div class="form">
			<div class="header">
				<strong>Vehicle Inquirey</strong>
			</div><!-- .header -->
			<form action="<?php echo $this->options[ 'api' ][ 'vehicle_management_system' ] . '/' . $this->options[ 'company_information' ][ 'id' ]; ?>/forms/create/<?php echo strtolower($sale_class); ?>_vehicle_inquiry" method="post" name="vehicle-inquiry">
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
				<input name="inventory" type="hidden" value="<?php # TODO: What is this? 10725019 ... I can't submit forms without this... ?>" />
				<input name="price" type="hidden" value="<?php echo $price; ?>" />
				<input name="name" type="hidden" value="" />
				<table>
					<tr>
						<td class="required" colspan="1">
							<label for="vehicle-inquiry-f-name">Your First Name</label>
							<input maxlength="70" id="vehicle-inquiry-f-name" name="f_name" style="width:90%" tabindex="1" type="text" />
						</td><!-- .required -->
						<td class="required" colspan="1">
							<label for="vehicle-inquiry-email">Email Address</label>
							<input maxlength="255" id="vehicle-inquiry-email" name="email" style="width:97%" tabindex="6" type="text" />
						</td><!-- .required -->
					</tr>
					<tr>
						<td class="required" colspan="1">
							<label for="vehicle-inquiry-l-name">Your Last Name</label>
							<input maxlength="70" id="vehicle-inquiry-l-name" name="l_name" style="width:90%" tabindex="2" type="text" />
						</td><!-- .required -->
						<td colspan="1">
							<label for="vehicle-inquiry-timetocall">Best Time To Call</label>
							<input maxlength="256" name="timetocall" id="vehicle-inquiry-timetocall" style="width:97%" tabindex="5" type="text" />
						</td>
					</tr>
					<tr>
						<td colspan="1">
							<label for="vehicle-inquiry-phone">Phone Number</label>
							<input maxlength="256" name="phone" id="vehicle-inquiry-phone" style="width:90%" tabindex="3" type="text" />
						</td>
						<td class="required" rowspan="1">
							<label for="vehicle-inquiry-comments">Comments</label>
							<textarea name="comments" id="vehicle-inquiry-comments" rows="7" style="width:97%" tabindex="7"></textarea>
						</td><!-- .required -->
					</tr>
					<tr>
						<td colspan="1">&nbsp;</td>
						<td colspan="1">
							<input onclick="document.forms['vehicle-inquiry']['name'].value = document.forms['vehicle-inquiry']['f_name'].value + ' ' + document.forms['vehicle-inquiry']['l_name'].value; document.forms['vehicle-inquiry']['privacy'].checked = true; document.forms['vehicle-inquiry'].submit();" type="button" value="Send Inquiry" class="submit" />
							<small style="float:right;">
								<label for="vehicle-inquiry-privacy"><a href="/privacy" target="_blank">Privacy Policy</a></label>
							</small>
							<div style="display:none">
								<input class="privacy" name="privacy" id="vehicle-inquiry-privacy" type="checkbox" value="Yes" />
								<input name="agree_sb" type="checkbox" value="Yes" /> I am a Spam Bot?
							</div>
						</td>
					</tr>
				</table>
			</form>
		</div><!-- .form -->
	</div><!-- right-column -->
	<?php flush(); ?>
	<div id="inventory-tabs" style="clear:both;">
		<ul>
			<li><a href="#dealer-notes">Dealer Notes</a></li>
			<li><a href="#dealer-options">Optional Equipment</a></li>
			<li><a href="#dealer-equipment">Equipment</a></li>
			<li><a href="#test-drive">Test Drive</a></li>
			<li><a href="#trade-in">Trade In</a></li>
			<li><a href="#tell-a-friend">Tell a Friend</a></li>
			<li><a href="#loan-calculator">Loan Calculator</a></li>
		</ul>
		<div id="dealer-notes">
			<h3>Dealer Notes</h3>
			<p>Orange doesn't want to share this with me! :( <?php # TODO: FIX ME! ?></p>
		</div><!-- #dealer-notes -->
		<div id="dealer-options">
			<h3>Optional Equipment</h3>
			<?php if( !is_null( $dealer_options ) ): ?>
				<ul>
					<?php
						$counter = 0;
						$split = count( $dealer_options ) / 2;
						foreach( $dealer_options as $option ) {
							echo ( $counter > $split ) ? '</ul><ul>' : NULL;
							$counter = ( $counter <= $split ) ? $counter + 1 : 0;
							echo '<li>' . $option . '</li>';
						}
					?>
				</ul>
			<?php else: ?>
				<p>This information is currently not available.</p>
			<?php endif ?>
			<br class="clear" />
		</div><!-- #dealer-options -->
		<div id="dealer-equipment">
			<h3>Standard Equipment</h3>
			<?php if( !is_null( $equipment ) ): ?>
			<ul>
    		<?php
					$previous = null;
					foreach( $equipment as $item ) {
						echo ( is_null( $previous ) ) ? '<li class="no-list"><strong>' . $item->group . '</strong></li>' : NULL;
						$previous = ( is_null( $previous ) ) ? $item->group : $previous;
						echo ( $previous != $item->group ) ? '</ul><ul><li class="no-list"><strong>' . $previous . '</strong></li>' : NULL;
						$previous = ( $previous != $item->group ) ? $item->group : $previous;
						echo '<li>' . $item->name . '</li>';
					}
    		?>
			</ul>
			<?php else: ?>
				<p>This information is currently not available.</p>
			<?php endif; ?>
    	<br class="clear" />
		</div><!-- #dealer-equipment -->
		<div id="test-drive">
			<h3>Test Drive</h3>
			<div class="form">
	  		<form name="formvehicletestdrive" action="<?php echo $this->options[ 'api' ][ 'vehicle_management_system' ] . '/' . $this->options[ 'company_information' ][ 'id' ]; ?>/forms/create/<?php echo strtolower($sale_class); ?>_vehicle_test_drive" method="post">
	    		<input type="hidden" name="required_fields" value="name,email,privacy"/>
	    		<input type="hidden" name="saleclass" value="<?php echo strtolower($sale_class); ?>"/>
	    		<input type="hidden" name="return_url" value="" id="return_url_test_drive"/>
	    		<input type="hidden" name="vehicle" value="<?php echo $year . ' ' . $make . ' ' . $model; ?>"/>
	    		<input type="hidden" name="year" value="<?php echo $year; ?>"/>
	    		<input type="hidden" name="make" value="<?php echo $make; ?>"/>
	    		<input type="hidden" name="model_name" value="<?php echo $model; ?>"/>
	    		<input type="hidden" name="trim" value="<?php echo $trim; ?>"/>
	    		<input type="hidden" name="stock" value="<?php echo $stock; ?>"/>
	    		<input type="hidden" name="vin" value="<?php echo $vin; ?>"/>
	    		<input type="hidden" name="inventory" value="<?php #TODO: FIX THIS ?>"/>
	    		<input type="hidden" name="price" value="<?php echo $price; ?>"/>
	    		<table style="width:100%">
	      		<tr>
	        		<td class="required" style="width:50%;" colspan="1">
	          		<label for="formvehicletestdrive-name">Your First &amp; Last Name</label>
	          		<input type="text" maxlength="70" name="name" id="formvehicletestdrive-name" style="width:95%"/>
	        		</td>
	        		<td class="required" style="width:50%;" colspan="1">
	          		<label for="formvehicletestdrive-email">Email Address</label>
	          		<input type="text" maxlength="255" name="email" id="formvehicletestdrive-email" style="width:98%"/>
	        		</td>
	      		</tr>
	      		<tr>
	        		<td colspan="1">
  	        		<label for="formvehicletestdrive-phone">Phone Number</label>
    	      		<input type="text" maxlength="256" name="phone" id="formvehicletestdrive-phone" style="width:95%"/>
      	  		</td>
        			<td colspan="1">
          			<label for="formvehicletestdrive-timetocall">Best Time To Call</label>
          			<input type="text" maxlength="256" name="timetocall" id="formvehicletestdrive-timetocall" style="width:98%"/>
	        		</td>
  	    		</tr>
    	  		<tr>
      	  		<td colspan="2">
        	  		<label for="formvehicletestdrive-subject">Subject</label>
          			<input type="text" maxlength="256" style="width:99%" name="subject" id="formvehicletestdrive-subject" value="Vehicle Test Drive - <?php echo $year_make_model; ?>"/>
        			</td>
	      		</tr>
	      		<tr>
  	      		<td colspan="2" class="required">
    	      		<label for="formvehicletestdrive-comments">Comments</label>
      	    		<textarea style="width:99%" rows="10" name="comments" id="formvehicletestdrive-comments"></textarea>
        			</td>
	      		</tr>
	      		<tr>
  	      		<td class="required" colspan="2">
    	       		<input type="checkbox" class="privacy" id="formvehicletestdrive-privacy" name="privacy" value="Yes" /> <label for="formvehicletestdrive-privacy">Agree to <a target="_blank" href="/privacy">Privacy Policy</a></label>
								<div style="display:none">
									<input type="checkbox" name="agree_sb" value="Yes" /> I am a Spam Bot?
								</div>
	        		</td>
	      		</tr>
	    		</table>
	    		<div class="buttons" style="float:right">
	      		<button type="submit">Send Inquiry</button>
	    		</div>
	  		</form>
			</div>
		</div>
		<div id="trade-in">
			<h3>Trade In</h3>
			<div class="form">
		 		<form name="formvehicletradein" action="<?php echo $this->options[ 'api' ][ 'vehicle_management_system' ] . '/' . $this->options[ 'company_information' ][ 'id' ]; ?>/forms/create/<?php echo strtolower($sale_class); ?>_vehicle_trade_in" method="post">
	      	<input type="hidden" name="required_fields" value="name,email,privacy"/>
	      	<input type="hidden" name="saleclass" value="strtolower($sale_class);"/>
  	    	<input type="hidden" name="return_url" value="" id="return_url_trade_in"/>
    	  	<input type="hidden" name="vehicle" value="<?php echo $year_make_model; ?>"/>
      		<input type="hidden" name="year" value="<?php echo $year; ?>"/>
      		<input type="hidden" name="make" value="<?php echo $make; ?>"/>
	      	<input type="hidden" name="model_name" value="<?php echo $model; ?>"/>
	      	<input type="hidden" name="trim" value="<?php echo $trim; ?>"/>
	      	<input type="hidden" name="stock" value="<?php echo $stock; ?>"/>
	      	<input type="hidden" name="vin" value="<?php echo $vin; ?>"/>
	      	<input type="hidden" name="inventory" value="<?php # TODO: FIX THIS ?>"/>
	      	<input type="hidden" name="price" value="<?php echo $price; ?>"/>
	    		<table style="width:100%">
    	  		<tr>
  	      		<td class="required" colspan="1" style="width:50%;">
      	    		<label for="formvehicletradein-name">Your First &amp; Last Name</label>
        	  		<input type="text" maxlength="70" name="name" id="formvehicletradein-name" style="width:95%"/>
        			</td>
	        		<td class="required" colspan="1" style="width:50%;">
  	        		<label for="formvehicletradein-email">Email Address</label>
    	      		<input type="text" maxlength="255" name="email" id="formvehicletradein-email" style="width:98%"/>
      	  		</td>
      			</tr>
	      		<tr>
  	      		<td colspan="1">
    	      		<label for="formvehicletradein-phone">Phone Number</label>
      	    		<input type="text" maxlength="256" name="phone" id="formvehicletradein-phone" style="width:95%"/>
        			</td>
        			<td colspan="1">
          			<label for="formvehicletradein-timetocall">Best Time To Call</label>
          			<input type="text" maxlength="256" name="timetocall" id="formvehicletradein-timetocall" style="width:98%"/>
	        		</td>
  	    		</tr>
    	  		<tr>
      	  		<td colspan="1" class="required">
        	  		<label for="formvehicletradein-vin">VIN</label>
          			<input type="text" size="20" maxlength="256" id="formvehicletradein-vin" name="vin" onKeyUp="update_vehicle(this.value);" onChange="update_vehicle(this.value)" style="width:95%;" />
        			</td>
        			<td class="required" colspan="1">
	          		<label for="formvehicletradein-vehicle-desc">Vehicle Year / Make / Model</label>
  	        		<input type="text" maxlength=256 name="vehicle_desc" id="formvehicletradein-vehicle-desc" style="width:98%" />
    	    		</td>
      			</tr>
      			<tr>
        			<td colspan="1">
          			<label for="formvehicletradein-engine">Engine</label>
          			<select name="engine" id="formvehicletradein-engine">
            			<option value="4 Cyl">4 Cyl</option>
	            		<option value="6 Cyl">6 Cyl</option>
  	          		<option value="V6">V6</option>
    	        		<option value="V8">V8</option>
      	      		<option value="Other">Other</option>
        	  		</select>
	
  	        		<label for="formvehicletradein-transmission">Transmission</label>
    	      		<select name="transmission" id="formvehicletradein-transmission">
      	      		<option value="Manual">Manual</option>
        	    		<option value="Automatic">Automatic</option>
          			</select>
	        		</td>
    	   			<td colspan="1">
  	        		<label for="formvehicletradein-mileage">Odometer</label>
      	    		<input type="text" maxlength=256 name="mileage" id="formvehicletradein-mileage" style="width:98%" />
	        		</td>
  	    		</tr>
    	  		<tr>
      	  		<td colspan="2">
        	  		<label for="formvehicletradein-tradein">Description and Comments</label>
          			<textarea name="tradein" rows="5" id="formvehicletradein-tradein" style='width:99%'></textarea>
        			</td>
	      		</tr>
  	    		<tr>
    	    		<td colspan="2">
      	    		<label>Are you still making payments on this trade-in?</label>
        	  		<input type="radio" name="payoff" value="Yes"> Yes
          			<input type="radio" name="payoff" value="No"> No
        			</td>
	      		</tr>
  	    		<tr>
    	    		<td colspan="1">
      	    		<label for="formvehicletradein-payoff-holder">Who is your loan financed by?</label>
        	  		<input type="text" maxlength=256 name="payoff_holder" id="formvehicletradein-payoff-holder" style="width:95%" />
        			</td>
        			<td colspan="1">
          			<label for="formvehicletradein-payoff-amt">If financing, provide loan-payoff amount</label>
	          		<input type="text" maxlength=256 name="payoff_amt" id="formvehicletradein-payoff-amt" style="width:98%" />
  	      		</td>
    	  		</tr>
      			<tr>
        			<td colspan="2">
          			<label for="formvehicletradein-subject">Subject</label>
          			<input type="text" id="formvehicletradein-subject" maxlength="256" style="width:99%" name="subject" value="Vehicle Trade In - <?php echo $year_make_model; ?>"/>
	        		</td>
  	    		</tr>
    	  		<tr>
      	  		<td colspan="2" class="required">
        	  		<label for="formvehicletradein-comments">Comments</label>
          			<textarea style="width:99%" rows="10" name="comments" id="formvehicletradein-comments"></textarea>
        			</td>
	      		</tr>
  	    		<tr>
    	    		<td class="required" colspan="2">
      	    		<input type="checkbox" class="privacy" name="privacy" id="formvehicletradein-privacy" value="Yes" /><label for="formvehicletradein-privacy">Agree to <a target="_blank" href="/privacy">Privacy Policy</a></label>
        	  		<div style="display:none">
          	  		<input type="checkbox" name="agree_sb" value="Yes" /> I am a Spam Bot?
          			</div>
        			</td>
	      		</tr>
  	  		</table>
    			<div class="buttons" style="float:right">
      			<button type="submit">Send Inquiry</button>
    			</div>
	  		</form>
			</div><!-- .form -->
			<script id="loader" type="text/javascript"></script>
			<script type="text/javascript">
				function update_vehicle(vin) {
    			if (vin == null || vin.length < 11) return false;
    			dealertrend('#loader').attr('src', '<?php echo $this->options[ 'api' ][ 'vehicle_management_system' ] . '/' . $this->options[ 'company_information' ][ 'id' ]; ?>/widget/check_vin.js?vin=' + vin);
  			}
			</script>
		</div><!-- #trade-in -->
		<div id="tell-a-friend">
			<h3>Tell a Friend</h3>
			<div class="form">
  			<form name="formtellafriend" action="<?php echo $this->options[ 'api' ][ 'vehicle_management_system' ] . '/' . $this->options[ 'company_information' ][ 'id' ]; ?>/forms/create/vehicle_tell_a_friend" method="post">
      		<input type="hidden" name="required_fields" value="from_name,from_email,friend_name,friend_email,privacy"/>
	      	<input type="hidden" name="return_url" value="" id="return_url_tellafriend"/>
  	    	<input type="hidden" name="vehicle" value="<?php echo $year_make_model; ?>"/>
    	  	<input type="hidden" name="stock" value="<?php echo $stock; ?>"/>
      		<input type="hidden" name="vin" value="<?php echo $vin; ?>"/>
    			<table style="width:100%">
      			<tr>
	        		<td class="required" style="width:50%;" colspan="1">
  	      			<label for="formtellafriend-from-name">Your First &amp; Last Name</label>
    	      		<input type="text" style="width:90%" maxlength="70" name="from_name" id="formtellafriend-from-name"/>
      	  		</td>
        			<td class="required" colspan="1">
          			<label for="formtellafriend-from-email">Email Address</label>
          			<input type="text" style="width:97%" id="formtellafriend-from-email" maxlength="255" name="from_email"/>
	       			</td>
  	    		</tr>
    	  		<tr>
      	  		<td class="required" style="width:50%;" colspan="1">
        	  		<label for="formtellafriend-friend-name">Your Friend's Name</label>
          			<input type="text" style="width:90%" maxlength="70" name="friend_name" id="formtellafriend-friend-name" />
	        		</td>
  	      		<td class="required" colspan="1">
    	      		<label for="formtellafriend-friend-email">Friend's Email Address</label>
      	    		<input type="text" style="width:90%" maxlength="255" name="friend_email" id="formtellafriend-friend-email" />
        			</td>
	      		</tr>
  	    		<tr>
    	    		<td colspan="2">
      	    		<label for="formtellafriend-subject">Subject</label>
        	  		<input type="text" style="width:97%" maxlength="256" name="subject" id="formtellafriend-subject" value="<?php echo $company_information->name; ?> - Tell-A-Friend - <?php echo $year_make_model; ?>"/>
        			</td>
	      		</tr>
  	    		<tr>
    	    		<td colspan="2" class="required">
      	    		<label for="formtellafriend-comments">Comments</label>
        	  		<textarea style="width:97%" rows="10" name="comments" id="formtellafriend-comments"></textarea>
        			</td>
	      		</tr>
  	    		<tr>
    	    		<td colspan="2">
								<div>
        	    		<input id="formtellafriend-notify" type="checkbox" name="notify" value="yes" /><label for="formtellafriend-notify">Request Notification of Receipt when email is opened?</label>
								</div>
          			<div class="required">
            			<input type="checkbox" name="privacy" id="formtellafriend-privacy" value="Yes" /><label for="formtellafriend-privacy">Agree to <a target="_blank" href="/privacy">Privacy Policy</a></label>
	          		</div>
  	        		<div style="display:none">
    	        		<input type="checkbox" name="agree_sb" value="Yes" /> I am a Spam Bot?
      	    		</div>
        			</td>
      			</tr>
	    		</table>
  	  		<div class="buttons" style="float:right">
    	  		<button type="submit">Send to a Friend</button>
    			</div>
	  		</form>
			</div><!-- .form -->
		</div><!-- #tell-a-friend -->
		<div id="loan-calculator">
			<?php
				$html_free_price = str_replace( '<span class=\'asking\'>' , null , $price );
				$html_free_price = str_replace( '</span>' , null , $html_free_price );
			?>
			<h3>Loan Calculator</h3>
			<div class="form">
				<form name="loan-calculator" action="#" method="post">
					<table style="width:100%">
						<tr>
							<td colspan="1">
								<label for="loan-calculator-price">Vehicle Price</label>
								<input type="text" style="width:90%" name="price" id="loan-calculator-price" value="<?php echo $html_free_price; ?>" />
							</td>
							<td colspan="1">
								<label for="loan-calculator-interest-rate">Interest Rate</label>
								<input type="text" style="width:90%" name="interest-rate" id="loan-calculator-interest-rate" value="7.35%" />
							</td>
							<td colspan="1">
								<label for="loan-calculator-term">Term (months)</label>
								<input type="text" style="width:90%" name="term" id="loan-calculator-term" value="48" />
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
							<td colspan="3"><button>Calculate</button></td>
						</tr>
					</table>
				</form>
			</div><!-- .form-->
		</div><!-- #loan-calculator -->
	</div><!-- #inventory-tabs -->
</div><!-- .dealertrend.inventory.detail  -->
