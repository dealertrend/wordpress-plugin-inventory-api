<div class="dealertrend inventory detail">
	<div class="slideshow">
		<?php
			$headline = $inventory->headline;
			$sale_class = $inventory->saleclass;
			
			echo $headline;
			# TODO: Unable to get 'show_contact_info' data. <- Contact information from Company.
			# TODO: Allow people to specify privacy policy location.
		?>
		<div class="images">
		<?php
			foreach( $inventory->photos as $photo ) {
				echo '<img src="' . $photo->medium . '" />';
			}
		?>
		</div><!-- .slideshow-images -->
		<div class="navigation"></div>
	</div><!-- .slideshow -->
	<div class="form">
		<form action="<?php echo $this->options[ 'api' ][ 'vehicle_management_system' ] . '/' . $this->options[ 'company_information' ][ 'id' ]; ?>/forms/create/<?php echo strtolower($sale_class); ?>_vehicle_inquiry" method="post" name="vehicle-inquiry">
			<input name="required_fields" type="hidden" value="name,email,privacy" />
			<input name="subject" type="hidden" value="Vehicle Inquiry - <?php echo $headline; ?>" />
			<input name="saleclass" type="hidden" value="<?php echo $sale_class; ?>" />
			<input name="vehicle" type="hidden" value="2011 Toyota 4Runner" />
			<input name="year" type="hidden" value="2011" />
			<input name="make" type="hidden" value="Toyota" />
			<input name="model_name" type="hidden" value="4Runner" />
			<input name="trim" type="hidden" value="SR5" />
			<input name="stock" type="hidden" value="111661" />
			<input name="vin" type="hidden" value="JTEBU5JR2B5047345" />
			<input name="inventory" type="hidden" value="10725019" />
			<input name="price" type="hidden" value="35409.0" />
			<input name="name" type="hidden" value="" />

			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td class="required" valign="top" width="50%">
						<label for="f_name">Your First Name <span>*</span></label>
						<input maxlength="70" name="f_name" style="width:90%" tabindex="1" type="text" />
					</td>
					<td class="required" valign="top" width="50%">
						<label fo"email">Email Address <span>*</span></label>
						<input maxlength="255" name="email" style="width:90%" tabindex="6" type="text" />
					</td>
				</tr>
				<tr>
					<td class="required" valign="top" width="50%">
						<label for="l_name">Your Last Name <span>*</span></label>
						<input maxlength="70" name="l_name" style="width:90%" tabindex="2" type="text" />
					</td>
					<td class="required" rowspan="3" valign="top" width="50%">
						<label for="comments">Comments</label>
						<textarea name="comments" rows="7" tabindex="7"></textarea>
					</td>
				</tr>
				<tr>
					<td align="left" valign="top">
						<label for="phone">Phone Number</label>
						<input maxlength="256" name="phone" style="width:90%" tabindex="3" type="text" />
					</td>
				</tr>
				<tr>
					<td valign="top">
						<label for="timetocall">Best Time To Call</label>
						<input maxlength="256" name="timetocall" style="width:90%" tabindex="5" type="text" />
					</td>
				</tr>
				<tr>
					<td align="center">
						<small>
							<a href="#" target="_blank">Privacy Policy</a>
						</small>
						<div style="display:none">
							<input class="privacy" name="privacy" type="checkbox" value="Yes" />
							<input name="agree_sb" type="checkbox" value="Yes" /> I am a Spam Bot?
						</div>
					</td>
					<td align="center">
						<input onclick="document.forms['vehicle-inquiry']['name'].value = document.forms['vehicle-inquiry']['f_name'].value + ' ' + document.forms['vehicle-inquiry']['l_name'].value; document.forms['vehicle-inquiry']['privacy'].checked = true; document.forms['vehicle-inquiry'].submit();" type="button" value="Send Inquiry" />
					</td>
				</tr>
			</table>
		</form>
	</div>
</div><!-- .dealertrend.inventory.detail  -->

<pre style="clear:both;">
<?php
  print_r($inventory);
?>
</pre>
