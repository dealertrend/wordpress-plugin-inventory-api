<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

	$vehicle = itemize_vehicle($inventory);
	$price = get_price_display($vehicle['prices'], $company_information, $vehicle['saleclass'], $vehicle['vin'], 'armadillo', $price_text );
	$vehicle['primary_price'] = $price['primary_price'];
	$parameters['saleclass'] = $vehicle['saleclass'];
	
	apply_gravity_form_hooks( $vehicle );

	$traffic_source = isset( $_COOKIE[ 'dealertrend-traffic-source' ] ) ? $_COOKIE[ 'dealertrend-traffic-source' ] : false;
	$traffic_source = $this->sanitize_inputs( $traffic_source );

	usort($vehicle['dealer_options'], 'sort_length' );

	$form_subject = $vehicle['year'] . ' ' . $vehicle['make']['name'] . ' ' . $vehicle['model']['name'] . ' ' . $vehicle['stock_number'];
	$form_submit_url = $temp_host . '/' . $this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] . '/forms/create/';

?>

<div id="eagle-wrapper">
	<div id="eagle-detail">
		<div id="eagle-top"> <!-- Eagle Top -->
			<div class="eagle-breadcrumbs">
			<?php echo display_breadcrumb( $parameters, $company_information, $inventory_options, $vehicle['saleclass'] ); ?>
			</div>
			<div id="eagle-top-info">
				<div id="eagle-headline-wrap">
					<div id="eagle-main-headline">
						<h2>
							<span class="eagle-saleclass" style="display: none;"><?php echo $vehicle['saleclass']; ?></span>
							<span class="eagle-year"><?php echo $vehicle['year']; ?></span>
							<span class="eagle-make"><?php echo $vehicle['make']['name']; ?></span>
							<span class="eagle-model"><?php echo $vehicle['model']['name']; ?></span>
							<span class="eagle-trim"><?php echo $vehicle['trim']['name']; ?></span>
							<span class="eagle-drive-train"><?php echo $vehicle['drive_train']; ?></span>
							<span class="eagle-transmission"><?php echo $vehicle['transmission']; ?></span>
							<span class="eagle-body-style"><?php echo $vehicle['body_style']; ?></span>
						</h2>
					</div>
					<div id="eagle-sub-headline">
						<?php
							if( empty( $custom_settings[ 'remove_sub_headline_d' ] ) ){
						?>
						<h3>
							<span class="eagle-make"><?php echo $vehicle['make']['name']; ?></span>
							<span class="eagle-model"><?php echo $vehicle['model']['name']; ?></span>
							<span class="eagle-trim"><?php echo $vehicle['trim']['name']; ?></span>
							<span class="eagle-city"><?php echo $company_information->city; ?></span>,
							<span class="eagle-state"><?php echo $company_information->state; ?></span>

						</h3>
						<?php
							}
						?>

						<?php
							if( !empty( $custom_settings[ 'display_vehicle_location_sub_headline_d' ] ) ){
						?>
						<h3>
							<span class="eagle-sh-vehicle-location"><?php echo $vehicle['contact_info']['location']; ?></span>
						</h3>
						<?php
							}
						?>
					</div>
				</div>
				<div id="eagle-top-price">
					<div class="eagle-price">
						<?php
						$price = get_price_display($vehicle['prices'], $company_information, $vehicle['saleclass'], $vehicle['vin'], 'eagle', $price_text );
						echo (!empty($price['ais_link'])) ? $price['ais_link'] : '';
						echo $price['compare_text'].$price['ais_text'].$price['primary_text'].$price['expire_text'].$price['hidden_prices'];
						?>
						
					</div>
					<div id="eagle-get-price">
						<div class="eagle-get-price-button eagle-show-form" name="<?php echo ( !empty($theme_settings['list_info_button']) ) ? $theme_settings['list_info_button'] : 'Get Your ePrice'; ?>"><?php echo ( !empty($theme_settings['list_info_button']) ) ? $theme_settings['list_info_button'] : 'GET YOUR ePrice'; ?></div>
					</div>
					<?php
						if( !empty( $theme_settings[ 'display_tags' ] ) ){
							apply_special_tags( $vehicle['tags'], $vehicle['on_sale'], $vehicle['certified'], $vehicle['video']);
							if( !empty( $vehicle['tags'] ) ){
								echo '<div class="eagle-detail-tags">';
									$tag_icons = build_tag_icons( $default_tag_names, $custom_tag_icons, $vehicle['tags'], $vehicle['vin']);
									echo $tag_icons;
								echo '</div>';
							}
						}
					?>
				</div>
				<?php
					if( !empty($custom_settings['display_headlines']) ){
						if ( !empty( $vehicle['headline'] ) ){
							$eagle_value = '<div id="eagle-custom-headline">'.$vehicle['headline'].'</div>';
							echo $eagle_value;
						}
					}
				?>
			</div>
		</div>
		<div id="eagle-content"> <!-- Eagle Content -->
			<div id="eagle-content-top"> <!-- Eagle Content Top -->
				<div id="eagle-content-headline">
					<span class="eagle-year"><?php echo $vehicle['year']; ?></span>
					<span class="eagle-make"><?php echo $vehicle['make']['name']; ?></span>
					<span class="eagle-model"><?php echo $vehicle['model']['name']; ?></span>
					<span class="eagle-text">Photos:</span>
					<a id="friendly-print" onclick="window.open('?print_page','popup','width=800,height=900,scrollbars=yes,resizable=no,toolbar=no,directories=no,location=no,menubar=yes,status=no,left=0,top=0'); return false">Print</a>
				</div>
			</div>
			<div id="eagle-content-center"> <!-- Eagle Content Center -->
				<div id="eagle-content-detail-left"> <!-- Eagle Content Detail Left -->
					<div id="eagle-image-wrapper"> <!-- Eagle Image Wrapper -->
						<?php
							echo get_photo_detail_display( $vehicle['photos'], $vehicle['video'], $theme_settings['default_image'] );
						?>
					</div>
					<div id="eagle-vehicle-information"> <!-- Eagle Vehicle Information -->
						<div class="eagle-vehicle-info-divider">
							<h4 class="eagle-divider-headline">
								<span class="eagle-year"><?php echo $vehicle['year']; ?></span>
								<span class="eagle-make"><?php echo $vehicle['make']['name']; ?></span>
								<span class="eagle-model"><?php echo $vehicle['model']['name']; ?></span>
								<span class="eagle-divider-text">Vehicle Details:</span>
							</h4>
							<div class="eagle-divider-content">
								<div id="eagle-stock-vin-wrapper">
									<span class="eagle-text-bold">Stock #:</span><span id="eagle-stock-number" class="eagle-text-space"><?php echo $vehicle['stock_number']; ?></span>
									<span class="eagle-text-bold">Vin #:</span><span id="eagle-vin-number" class="eagle-text-space"><?php echo $vehicle['vin']; ?></span>
								</div>
								<div id="eagle-vehicle-details-wrapper">
									<div id="eagle-vehicle-detail-left">
										<?php
											$vehicle_info = '';

											if ( !empty( $vehicle['saleclass'] ) ) {
												$vehicle_info .= '<div class="eagle-vehicle-overview-wrap vehicle-saleclass">';
												$vehicle_info .= '<div class="eagle-vehicle-overview-left">Condition:</div>';
												$vehicle_info .= '<div class="eagle-vehicle-overview-right vehicle-saleclass-value">' . $vehicle['saleclass'] . '</div>';
												$vehicle_info .= '</div>';

											}

											if ( $vehicle['certified'] == 'true') {
												$vehicle_info .= '<div class="eagle-vehicle-overview-wrap vehicle-certified">';
												$vehicle_info .= '<div class="eagle-vehicle-overview-left">Certified:</div>';
												$vehicle_info .= '<div class="eagle-vehicle-overview-right vehicle-certified-value">Yes</div>';
												$vehicle_info .= '</div>';

											}

											if ( !empty( $vehicle['odometer'] ) ) {
												$vehicle_info .= '<div class="eagle-vehicle-overview-wrap vehicle-odometer">';
												$vehicle_info .= '<div class="eagle-vehicle-overview-left">Mileage:</div>';
												$vehicle_info .= '<div class="eagle-vehicle-overview-right vehicle-odometer-value">' . $vehicle['odometer'] . '</div>';
												$vehicle_info .= '</div>';
											}

											if ( !empty( $vehicle['engine'] ) ) {
												$vehicle_info .= '<div class="eagle-vehicle-overview-wrap vehicle-engine">';
												$vehicle_info .= '<div class="eagle-vehicle-overview-left">Engine:</div>';
												$vehicle_info .= '<div class="eagle-vehicle-overview-right vehicle-engine-value">' . $vehicle['engine'] . '</div>';
												$vehicle_info .= '</div>';
											}

											if ( !empty( $vehicle['transmission'] ) ) {
												$vehicle_info .= '<div class="eagle-vehicle-overview-wrap vehicle-transmission">';
												$vehicle_info .= '<div class="eagle-vehicle-overview-left">Transmission:</div>';
												$vehicle_info .= '<div class="eagle-vehicle-overview-right vehicle-transmission-value">' . $vehicle['transmission'] . '</div>';
												$vehicle_info .= '</div>';
											}

											if ( !empty( $vehicle['drive_train'] ) ) {
												$vehicle_info .= '<div class="eagle-vehicle-overview-wrap vehicle-drivetrain">';
												$vehicle_info .= '<div class="eagle-vehicle-overview-left">Drivetrain:</div>';
												$vehicle_info .= '<div class="eagle-vehicle-overview-right vehicle-drivetrain-value">' . $vehicle['drive_train'] . '</div>';
												$vehicle_info .= '</div>';
											}

											if ( !empty( $vehicle['exterior_color'] ) ) {
												$vehicle_info .= '<div class="eagle-vehicle-overview-wrap vehicle-exterior">';
												$vehicle_info .= '<div class="eagle-vehicle-overview-left">Exterior Color:</div>';
												$vehicle_info .= '<div class="eagle-vehicle-overview-right vehicle-exterior-value">' . $vehicle['exterior_color'] . '</div>';
												$vehicle_info .= '</div>';
											}

											if ( !empty( $vehicle['interior_color'] ) ) {
												$vehicle_info .= '<div class="eagle-vehicle-overview-wrap vehicle-interior">';
												$vehicle_info .= '<div class="eagle-vehicle-overview-left">Interior color:</div>';
												$vehicle_info .= '<div class="eagle-vehicle-overview-right vehicle-interior-value">' . $vehicle['interior_color'] . '</div>';
												$vehicle_info .= '</div>';
											}

											if ( !empty( $vehicle['carfax']) ) {
												$vehicle_info .= '<div class="eagle-vehicle-overview-wrap">';
												$vehicle_info .= '<a href="' . $vehicle['carfax'] . '" class="eagle-detail-carfax" target="_blank">Carfax</a>';
												$vehicle_info .= '</div>';
											}

											echo $vehicle_info;

											if( $vehicle['autocheck'] ){
												echo display_autocheck_image( $vehicle['vin'], $vehicle['saleclass'], $type );
											}

										?>
									</div>
									<div id="eagle-vehicle-detail-right">
										<?php
											$fuel_text = '<div id="eagle-fuel-headline">Fuel Economy:</div>';
											$fuel_text .= '<div id="eagle-fuel-economy">';
											$fuel_text .= get_fuel_economy_display( $vehicle['fuel_economy'], $country_code, 0, $vehicle_reference_system, $vehicle['acode'] );
											$fuel_text .= '</div>';
											echo $fuel_text;
										?>
									</div>
								</div>
							</div>
						</div>

						<div class="eagle-vehicle-info-divider" id="eagle-content-description-wrapper">
							<h4 class="eagle-divider-headline">
								<span class="eagle-divider-text">Vehicle Comments:</span>
							</h4>
							<div class="eagle-divider-content">
								<?php
									$eagle_value = '<div id="eagle-content-description"><p>';
										if ( !empty( $vehicle['description'] ) ) {
											$eagle_value .= $vehicle['description'];
										}
									$eagle_value .= '</p></div>';
									echo $eagle_value;
								?>
							</div>
						</div>

						<div class="eagle-vehicle-info-divider" id="eagle-content-features-wrapper">
							<h4 class="eagle-divider-headline">
								<span class="eagle-divider-text">Vehicle Specifications and Features:</span>
							</h4>
							<div class="eagle-divider-content">
								<?php
									$eagle_value = '<div id="eagle-content-features"><ul>';
									foreach( $vehicle['dealer_options'] as $option ) {
										$eagle_value .= '<li>' . $option . '</li>';
									}
									$eagle_value .= '</ul></div>';
									echo $eagle_value;
								?>
							</div>
						</div>

						<?php
							if( isset($vehicle['standard_equipment']) && !is_Empty_check($vehicle['standard_equipment']) && $inventory_options['standard_equipment'] ){
						?>
							<div class="eagle-vehicle-info-divider" id="eagle-content-equipment-wrapper">
								<h4 class="eagle-divider-headline">
									<span class="eagle-divider-text">Vehicle Standard Equipment:</span>
								</h4>
								<div class="eagle-divider-content">
									<?php
										echo display_equipment( $vehicle['standard_equipment'] );
									?>
								</div>
							</div>
						<?php
							}
						?>
					</div>
				</div>

				<div id="eagle-content-detail-right"> <!-- Eagle Content Detail Right -->
					<div id="eagle-contact-information">
						<?php
							if( !empty($theme_settings['display_dealer_name_sidebar_d']) ){
								$contact_info_value .= '<div id="eagle-contact-name">' . get_dealer_contact_name( $vehicle['contact_info'], $inventory_options, $vehicle['saleclass'] ) . '</div>';
							}
							if( !empty($theme_settings['display_vehicle_location_sidebar_d']) ){
								$contact_info_value .= '<div id="eagle-contact-vehicle-location">' . $vehicle['contact_info']['location'] . '</div>';
							}
							$contact_info_value .= '<div id="eagle-contact-phone">'.get_dealer_contact_number( $vehicle['contact_info'], $inventory_options, $vehicle['saleclass'] ) . '</div>';
							$contact_info_value .= '<div id="eagle-contact-message">' . $vehicle['contact_info']['greeting'] . '</div>';

							echo $contact_info_value;
						?>

					</div>
					<div class="eagle-content-sidebar-wrapper">
						<div class="eagle-forms">
							<?php
								if( function_exists('gravity_form') && !empty($theme_settings['detail_gform_id']) ){
									$form = '<div class="form-wrapper"><div id="info-form-id-'.$theme_settings['detail_gform_id'].'" class="eagle-form form-display-wrap form-'.$theme_settings['detail_gform_id'].'" name="form-id-'.$theme_settings['detail_gform_id'].'">';
									$form .= do_shortcode('[gravityform id='.$theme_settings['detail_gform_id'].' title=true description=false]');
									$form .= '</div></div>';
									echo $form;			
								} else {
							?>
							
							<div class="eagle-form-headers active-form" name="form-info">
								Tell Us How We Can Help:
							</div>
							<div class="eagle-form-headers-sub" name="form-info-sub">
								(Check All That Apply)
							</div>
							<div id="eagle-form-info" class="eagle-form" name="active" style="display: block;">
								<form action="#" method="post" name="vehicle-inquiry" id="vehicle-inquiry">
									<input type="hidden" name="traffic_source" value="<?php echo $traffic_source; ?>"/>
									<input name="required_fields" type="hidden" value="name,email,privacy" />
									<input name="subject" type="hidden" value="Vehicle Inquiry - <?php echo $form_subject; ?>" />
									<input name="saleclass" type="hidden" value="<?php echo $vehicle['saleclass']; ?>" />
									<input name="vehicle" type="hidden" value="<?php echo $vehicle['year'].' '.$vehicle['make']['name'].' '.$vehicle['model']['name']; ?>" />
									<input name="year" type="hidden" value="<?php echo $vehicle['year']; ?>" />
									<input name="make" type="hidden" value="<?php echo $vehicle['make']['name']; ?>" />
									<input name="model_name" type="hidden" value="<?php echo $vehicle['model']['name']; ?>" />
									<input name="trim" type="hidden" value="<?php echo $vehicle['trim']['name']; ?>" />
									<input name="stock" type="hidden" value="<?php echo $vehicle['stock_number']; ?>" />
									<input name="vin" type="hidden" value="<?php echo $vehicle['vin']; ?>" />
									<input name="inventory" type="hidden" value="<?php echo $vehicle['id']; ?>" />
									<input name="price" type="hidden" value="<?php echo $vehicle['primary_price']; ?>" />
									<input name="name" type="hidden" id="vehicle-inquiry-name" value="" />
									<input name="comments" type="hidden" id="vehicle-inquiry-comments" value="" />
									<div class="eagle-form-table">
										<div class="eagle-form-top-checkboxes">
											<div class="eagle-checkbox-wrapper">
												<div class="eagle-checkbox-left">
													<input class="eagle-checkbox" name="eagle-checkbox-general-questions" id="vehicle-inquiry-checkbox-1" tabindex="4" type="checkbox"  />
												</div>
												<div class="eagle-checkbox-right">
													<label for="vehicle-inquiry-checkbox-1">General Questions</label>
												</div>
											</div>
											<div class="eagle-checkbox-wrapper">
												<div class="eagle-checkbox-left">
													<input class="eagle-checkbox" name="eagle-checkbox-email-coupon" id="vehicle-inquiry-checkbox-2" tabindex="5" type="checkbox"  />
												</div>
												<div class="eagle-checkbox-right">
													<label for="vehicle-inquiry-checkbox-2">Email Me a Coupon</label>
												</div>
											</div>
											<div class="eagle-checkbox-wrapper">
												<div class="eagle-checkbox-left">
													<input class="eagle-checkbox" name="eagle-checkbox-call-asap" id="vehicle-inquiry-checkbox-3" tabindex="6" type="checkbox"  />
												</div>
												<div class="eagle-checkbox-right">
													<label for="vehicle-inquiry-checkbox-3">Call Me ASAP</label>
												</div>
											</div>
											<div class="eagle-checkbox-wrapper">
												<div class="eagle-checkbox-left">
													<input class="eagle-checkbox" name="eagle-checkbox-price-drop" id="vehicle-inquiry-checkbox-4" tabindex="7" type="checkbox"  />
												</div>
												<div class="eagle-checkbox-right">
													<label for="vehicle-inquiry-checkbox-4">Email Me When Price Drops</label>
												</div>
											</div>
											<div class="eagle-checkbox-wrapper">
												<div class="eagle-checkbox-left">
													<input class="eagle-checkbox" name="eagle-checkbox-request-video" id="vehicle-inquiry-checkbox-5" tabindex="8" type="checkbox"  />
												</div>
												<div class="eagle-checkbox-right">
													<label for="vehicle-inquiry-checkbox-5">Request Walk-Through Video</label>
												</div>
											</div>
											<div class="eagle-checkbox-wrapper">
												<div class="eagle-checkbox-left">
													<input class="eagle-checkbox" name="eagle-checkbox-send-eprice" id="vehicle-inquiry-checkbox-6" tabindex="9" type="checkbox"  />
												</div>
												<div class="eagle-checkbox-right">
													<label for="vehicle-inquiry-checkbox-6">Send Me The ePrice</label>
												</div>
											</div>
										</div>
										<div class="eagle-form-full">
											<div class="required">
												<input maxlength="70" id="vehicle-inquiry-f-name" name="f_name" tabindex="10" type="text" alt="empty" value="First Name*" />
											</div>
										</div>
										<div class="eagle-form-full">
											<div class="required">
												<input maxlength="70" id="vehicle-inquiry-l-name" name="l_name" tabindex="11" type="text" alt="empty" value="Last Name*" />
											</div>
										</div>

										<div class="eagle-form-full">
											<div class="required">
												<input maxlength="255" id="vehicle-inquiry-email" name="email" tabindex="12" type="text" alt="empty" value="Email Address*"/>
											</div>
										</div>
										<div class="eagle-form-full">
											<div>
												<input maxlength="256" name="phone" id="vehicle-inquiry-phone" tabindex="13" type="text" alt="empty" value="Phone Number"/>
											</div>
										</div>
										<div class="eagle-form-full">
											<div>
												<textarea name="vehicle-inquiry-form-comments" id="vehicle-inquiry-form-comments" rows="4" tabindex="14" alt="empty">Comments</textarea>
											</div>
										</div>
										<div class="eagle-form-full">
											<div style="display:none">
												<input name="agree_sb" type="checkbox" value="Yes" /> I am a Spam Bot?
											</div>
											<div style="display:none">
												<label for="vehicle-inquiry-privacy" style="float:left; margin-right:10px;">Agree to <a target="_blank" href="/privacy">Privacy Policy</a></label>
												<input class="privacy" name="privacy" id="vehicle-inquiry-privacy" tabindex="15" type="checkbox" checked />
											</div>
										</div>
										<div class="eagle-form-button">
											<div>
												<input onclick="return eagle_process_forms(<?php echo '&#39;' . $form_submit_url . strtolower( $vehicle['saleclass'] ) . '_vehicle_inquiry&#39;'; ?> , '0' )" type="submit" value="Send Inquiry" class="submit" tabindex="16" />
											</div>
										</div>
										<div class="eagle-form-full">
											<div class="form-error" style="display: none;">
											</div>
										</div>
									</div>
								</form>
							</div>
							<?php } ?>
						</div>
						<?php
							if( $loan_settings['display_calc'] ){
								echo get_loan_calculator($loan_settings, $vehicle['primary_price'], TRUE);
							}
							// GForm Buttons
							if( function_exists('gravity_form') && isset($theme_settings['gravity_form']['data']) ){
								echo get_gform_button_display( $theme_settings['gravity_form']['data'], $vehicle['saleclass'] );
							}
							
							//Eagle Similar Vehicles
							if( $theme_settings['display_similar'] ){
								echo '<div id="detail-similar-wrapper">';
								echo get_similar_vehicles( $vehicle_management_system, $vehicle['vin'], $vehicle['saleclass'], $vehicle['vehicle_class'], $price['primary_price'], $vehicle['make']['name'], $inventory_options['make_filter'], array( 'city' => $city, 'state' => $state) );
								echo '</div>';
							}
						?>
					</div>
				</div>
			</div>
			<div id="eagle-content-bottom"> <!-- Eagle Content Bottom -->
			</div>
		</div>
		<div id="eagle-bottom"> <!-- Eagle Bottom -->
			<div id="eagle-disclaimer">
				<?php echo '<p>' . $inventory->disclaimer . '</p>'; ?>
			</div>
		</div>

		<?php
			if ( is_active_sidebar( 'vehicle-detail-page' ) ) :
				echo '<div id="eagle-widget-area" class="sidebar">';
					dynamic_sidebar( 'vehicle-detail-page' );
				echo '</div>';
			endif;
		?>

		<div class="eagle-forms eagle-hidden-form" style="display: none;">
			<div class="eagle-form-headers active-form" name="form-info" tabindex="19">
			</div>
			<div class="eagle-form-headers-sub" name="form-info-sub">
			</div>
			<div id="eagle-form-info" class="eagle-form" name="active" style="display: block;">
				<form action="#" method="post" name="vehicle-inquiry" id="vehicle-inquiry-hidden">
					<input type="hidden" name="traffic_source" value="<?php echo $traffic_source; ?>"/>
					<input name="required_fields" type="hidden" value="name,email,privacy" />
					<input name="subject" type="hidden" id="vehicle-inquiry-subject-hidden" value="" />
					<input name="saleclass" type="hidden" value="<?php echo $vehicle['saleclass']; ?>" />
					<input name="vehicle" type="hidden" value="<?php echo $vehicle['year'].' '.$vehicle['make']['name'].' '.$vehicle['model']['name']; ?>" />
					<input name="year" type="hidden" value="<?php echo $vehicle['year']; ?>" />
					<input name="make" type="hidden" value="<?php echo $vehicle['make']['name']; ?>" />
					<input name="model_name" type="hidden" value="<?php echo $vehicle['model']['name']; ?>" />
					<input name="trim" type="hidden" value="<?php echo $vehicle['trim']['name']; ?>" />
					<input name="stock" type="hidden" value="<?php echo $vehicle['stock_number']; ?>" />
					<input name="vin" type="hidden" value="<?php echo $vehicle['vin']; ?>" />
					<input name="inventory" type="hidden" value="<?php echo $vehicle['id']; ?>" />
					<input name="price" type="hidden" value="<?php echo $vehicle['primary_price']; ?>" />
					<input name="name" type="hidden" id="vehicle-inquiry-name-hidden" value="" />
					<input name="subject-pre" type="hidden" id="vehicle-inquiry-subpre-hidden" value="" />
					<input name="subject-post" type="hidden" id="vehicle-inquiry-subpost-hidden" value="<?php echo $form_subject; ?>" />
					<div class="eagle-form-table">
						<div class="eagle-form-full">
							<div class="required">
								<input maxlength="70" id="vehicle-inquiry-f-name-hidden" name="f_name" tabindex="20" type="text" alt="empty" value="First Name*" />
							</div>
						</div>
						<div class="eagle-form-full">
							<div class="required">
								<input maxlength="70" id="vehicle-inquiry-l-name-hidden" name="l_name" tabindex="21" type="text" alt="empty" value="Last Name*" />
							</div>
						</div>

						<div class="eagle-form-full">
							<div class="required">
								<input maxlength="255" id="vehicle-inquiry-email-hidden" name="email" tabindex="22" type="text" alt="empty" value="Email Address*"/>
							</div>
						</div>
						<div class="eagle-form-full">
							<div>
								<input maxlength="256" name="phone" id="vehicle-inquiry-phone-hidden" tabindex="23" type="text" alt="empty" value="Phone Number"/>
							</div>
						</div>
						<div class="eagle-form-full">
							<div>
								<textarea name="comments" id="vehicle-inquiry-form-comments-hidden" rows="4" tabindex="24" alt="empty">Comments</textarea>
							</div>
						</div>
						<div class="eagle-form-full">
							<div style="display:none">
								<input name="agree_sb" type="checkbox" value="Yes" /> I am a Spam Bot?
							</div>
							<div style="display:none">
								<label for="vehicle-inquiry-privacy" style="float:left; margin-right:10px;">Agree to <a target="_blank" href="/privacy">Privacy Policy</a></label>
								<input class="privacy" name="privacy" id="vehicle-inquiry-privacy-hidden" type="checkbox" checked />
							</div>
						</div>
						<div class="eagle-form-button">
							<div>
								<input onclick="return eagle_process_forms(<?php echo '&#39;' . $form_submit_url . strtolower( $vehicle['saleclass'] ) . '_vehicle_inquiry&#39;'; ?> , '3' )" type="submit" value="Send Inquiry" class="submit" tabindex="25" />
							</div>
						</div>
						<div class="eagle-form-full">
							<div class="form-error" style="display: none;">
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

	</div>
</div>



