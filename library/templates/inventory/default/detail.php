<?php
	# TODO: Unable to get 'show_contact_info' data. <- Contact information from Company.
	# TODO: Allow people to specify privacy policy location.
	# TODO: Get Dealer Notes data.
	# TODO: Doors

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
	</div>
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
						<td class="required">
							<label for="f_name">Your First Name</label>
							<input maxlength="70" name="f_name" style="width:90%" tabindex="1" type="text" />
						</td><!-- .required -->
						<td class="required">
							<label for="email">Email Address</label>
							<input maxlength="255" name="email" style="width:97%" tabindex="6" type="text" />
						</td><!-- .required -->
					</tr>
					<tr>
						<td class="required">
							<label for="l_name">Your Last Name</label>
							<input maxlength="70" name="l_name" style="width:90%" tabindex="2" type="text" />
						</td><!-- .required -->
						<td class="required" rowspan="3">
							<label for="comments">Comments</label>
							<textarea name="comments" rows="7" style="width:97%" tabindex="7"></textarea>
						</td><!-- .required -->
					</tr>
					<tr>
						<td>
							<label for="phone">Phone Number</label>
							<input maxlength="256" name="phone" style="width:90%" tabindex="3" type="text" />
						</td>
					</tr>
					<tr>
						<td>
							<label for="timetocall">Best Time To Call</label>
							<input maxlength="256" name="timetocall" style="width:90%" tabindex="5" type="text" />
						</td>
					</tr>
					<tr>
						<td>
							<small>
								<a href="/privacy" target="_blank">Privacy Policy</a>
							</small>
							<div style="display:none">
								<input class="privacy" name="privacy" type="checkbox" value="Yes" />
								<input name="agree_sb" type="checkbox" value="Yes" /> I am a Spam Bot?
							</div>
						</td>
						<td>
							<input onclick="document.forms['vehicle-inquiry']['name'].value = document.forms['vehicle-inquiry']['f_name'].value + ' ' + document.forms['vehicle-inquiry']['l_name'].value; document.forms['vehicle-inquiry']['privacy'].checked = true; document.forms['vehicle-inquiry'].submit();" type="button" value="Send Inquiry" class="submit" />
						</td>
					</tr>
				</table>
			</form>
		</div><!-- .form -->
	</div><!-- right-column -->
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
			<p>Orange doesn't want to share this with me! :(</p>
		</div><!-- #dealer-notes -->
		<div id="dealer-options">
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
				Information on optional equipment is currently not available.
			<?php endif ?>
			<br class="clear" />
		</div><!-- #dealer-options -->
		<div id="dealer-equipment">
			<?php if( !is_null( $equipment ) ): ?>
			<ul>
    		<?php
					$previous = null;
					foreach( $equipment as $item ) {
						echo ( is_null( $previous ) ) ? '<strong>' . $item->group . '</strong>' : NULL;
						$previous = ( is_null( $previous ) ) ? $item->group : $previous;
						echo ( $previous != $item->group ) ? '</ul><ul><strong>' . $previous . '</strong>' : NULL;
						$previous = ( $previous != $item->group ) ? $item->group : $previous;
						echo '<li>' . $item->name . '</li>';
					}
    		?>
			</ul>
		<?php else: ?>
			<p>Information on dealer equipment is currently not available.</p>
		<?php endif; ?>
    <br class="clear" />
	</div>
	<div id="test-drive">
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
    		<input type="hidden" name="inventory" value="<?php echo $id; ?>"/>
    		<input type="hidden" name="price" value="<?php echo $price; ?>"/>
    		<table width="100%" cellpadding="0" cellspacing="0" border="0">
      		<tr>
        		<td class="required" valign="bottom" width="50%">
          		<label for="name">Your First &amp; Last Name</label>
          		<input type="text" maxlength="70" name="name" style="width:90%"/>
        		</td>
        		<td class="required" valign="bottom" width="50%">
          		<label for="email">Email Address</label>
          		<input type="text" maxlength="255" name="email" style="width:90%"/>
        		</td>
      		</tr>
      		<tr>
        		<td align="left" valign="bottom">
          		<label for="phone">Phone Number</label>
          		<input type="text" maxlength="256" name="phone" style="width:90%"/>
        		</td>
        		<td valign="bottom">
          		<label for="timetocall">Best Time To Call</label>
          		<input type="text" maxlength="256" name="timetocall" style="width:90%"/>
        		</td>
      		</tr>
      		<tr>
        		<td valign="bottom" colspan="2">
          		<label for="subject">Subject</label>
          		<input type="text" maxlength="256" style="width:100%" name="subject" value="Vehicle Test Drive - <?php echo $year . ' ' . $make . ' ' . $model; ?>"/>
        		</td>
      		</tr>
      		<tr>
        		<td colspan="2" align="left" valign="bottom" class="required">
          		<label for="comments">Comments</label>
          		<textarea style="width:100%" rows="10" name="comments"></textarea>
        		</td>
      		</tr>
      		<tr>
        		<td class="required">
           		<input type="checkbox" class="privacy" id="privacy" name="privacy" value="Yes" /> <label for="privacy">Agree to <a target="_blank" href="/privacy">Privacy Policy</a></label>
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
	</div>
	<div id="tell-a-friend">
	</div>
	<div id="loan-calculator">
	</div>
</div>

	<pre style="clear:both; display:none;">
		<?php print_r($inventory); ?>
	</pre>

	<br class="clear" />

</div><!-- .dealertrend.inventory.detail  -->
