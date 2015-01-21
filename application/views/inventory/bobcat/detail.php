<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

	$vehicle = itemize_vehicle($inventory);
	$price = get_price_display($vehicle['prices'], $company_information, $vehicle['saleclass'], $vehicle['vin'], 'bobcat', $price_text );
	$vehicle['primary_price'] = $price['primary_price'];

	apply_gravity_form_hooks( $vehicle );

?>

	<div id="bobcat-wrapper">
		<div id="bobcat-detail" class="saleclass-<?php echo strtolower($vehicle['saleclass']); ?>">
			<div id="bobcat-title">
				<span id="title-year"><?php echo $vehicle['year']; ?></span>
				<span id="title-make"><?php echo $vehicle['make']['name']; ?></span>
				<span id="title-model"><?php echo $vehicle['model']['name']; ?></span>
				<span id="title-trim"><?php echo $vehicle['trim']['name']; ?></span>
			</div>
			<div class="breadcrumbs">
				<?php echo display_breadcrumb( $parameters, $company_information, $inventory_options, $vehicle['saleclass'] ); ?>
				<a id="friendly-print" onclick="window.open('?print_page','popup','width=550,height=800,scrollbars=yes,resizable=no,toolbar=no,directories=no,location=no,menubar=yes,status=no,left=0,top=0'); return false">Print Page</a>
			</div>
			<div id="vehicle-dealer-info">
				<span class="vehicle-dealer-name"><?php echo get_dealer_contact_name( $vehicle['contact_info'], $inventory_options, $vehicle['saleclass'] ); ?></span> - 
				<span class="vehicle-dealer-phone"><?php echo get_dealer_contact_number( $vehicle['contact_info'], $inventory_options, $vehicle['saleclass'] ); ?></span>
				<span class="vehicle-dealer-location"><?php echo $vehicle['contact_info']['location']; ?></span>
				<span style="display: none;" class="vehicle-dealer-id"><?php echo $vehicle['contact_info']['dealer_id']; ?></span>
			</div>
			<div id="bobcat-content-wrapper">
				<div id="content-inner-left">
					<?php
						echo get_photo_detail_display( $vehicle['photos'], $vehicle['video'], $theme_settings['default_image'] );
					?>
				</div>

				<div id="content-inner-right">
					<div id="bobcat-price-wrap">
						<div id="price-main">
							<?php echo $price['primary_text']; ?>
						</div>
						<div id="price-extra">
						<?php echo
							( !empty($price['msrp_text']) && strtolower($vehicle['saleclass']) == 'new' ? $price['msrp_text'] : '') . '
							'.$price['ais_text'].$price['compare_text'].$price['expire_text'].$price['hidden_prices'].'
							'. ( !empty($price['ais_link']) ? $price['ais_link'] : '')
						?>
						</div>
					</div>
					<?php echo $vehicle['headline'] ? '<div id="bobcat-detail-headline">'.$vehicle['headline'].'</div>' : ''; ?>
					<div id="bobcat-vehicle-info">
						<?php
							echo'
								<div class="info-divider">Stock Number: <span id="info-stock-number" class="info-value">'.$vehicle['stock_number'].'</span></div>
								<div class="info-divider">VIN: <span id="info-vin" class="info-value">'.$vehicle['vin'].'</span></div>
								<div class="info-divider">Condition: <span id="info-saleclass" class="info-value">'.$vehicle['saleclass'].'</span></div>
								'.
								( $vehicle['certified'] != 'false' ? '<div class="info-divider">Certified: <span id="info-certified" class="info-value">Yes</span></div>' : '' ).
								( !empty($vehicle['odometer']) ? '<div class="info-divider">Mileage: <span id="info-mileage" class="info-value">'.$vehicle['odometer'].'</span></div>' : '' ).
								( !empty($vehicle['exterior_color']) ? '<div class="info-divider">Exterior: <span id="info-exterior" class="info-value">'.$vehicle['exterior_color'].'</span></div>' : '' ).
								( !empty($vehicle['interior_color']) ? '<div class="info-divider">Interior: <span id="info-interior" class="info-value">'.$vehicle['interior_color'].'</span></div>' : '' ).
								( !empty($vehicle['engine']) ? '<div class="info-divider">Engine: <span id="info-engine" class="info-value">'.$vehicle['engine'].'</span></div>' : '' ).
								( !empty($vehicle['transmission']) ? '<div class="info-divider">Transmission: <span id="info-transmission" class="info-value">'.$vehicle['transmission'].'</span></div>' : '' ).
								( !empty($vehicle['drivetrain']) ? '<div class="info-divider">Drivetrain: <span id="info-drivetrain" class="info-value">'.$vehicle['drivetrain'].'</span></div>' : '' ).
								( !empty($vehicle['doors']) ? '<div class="info-divider">Doors: <span id="info-doors" class="info-value">'.$vehicle['doors'].'</span></div>' : '' ).
								( !empty($vehicle['body_style']) ? '<div class="info-divider">Body: <span id="info-body" class="info-value">'.$vehicle['body_style'].'</span></div>' : '' )

							;
						?>
					</div>
					<?php
						echo get_fuel_economy_display( $vehicle['fuel_economy'], $country_code, 1, $vehicle_reference_system, $vehicle['acode'] );

						if( $theme_settings['display_tags'] ){
							apply_special_tags( $vehicle['tags'], $vehicle['prices']['on_sale'], $vehicle['certified'], $vehicle['video']);
							if( !empty( $vehicle['tags'] ) ){
								echo '<div id="bobcat-detail-tags">';
									$tag_icons = build_tag_icons( $default_tag_names, $custom_tag_icons, $vehicle['tags']);
									echo $tag_icons;
								echo '</div>';
							}
						}

						if( $vehicle['autocheck'] ){
							echo display_autocheck_image( $vehicle['vin'], $vehicle['saleclass'], $type );
						}

						if( $vehicle['carfax'] ) {
		 					echo '<div class="carfax-wrapper"><a href="' . $vehicle['carfax'] . '" class="bobcat-carfax" target="_blank"><img src="http://assets.s3.dealertrend.com.s3.amazonaws.com/images/carfax_192x46.jpg" /></a></div>';
			 			}

					?>
				</div>
				<div id="content-inner-bottom">
					<?php

						if( $theme_settings['display_similar'] ) {
							$similar_output =  get_similar_vehicles( $vehicle_management_system, $vehicle['vin'], $vehicle['saleclass'], $vehicle['vehicle_class'], $price['primary_price'], $vehicle['make']['name'], $inventory_options['make_filter'], array( 'city' => $city, 'state' => $state) );
						}

						$tab_buttons = array(
							0 => array('options', 'Vehicle Options'),
							1 => array('description', 'Description'),
							2 => array('equipment', 'Standard Equipment'),
							3 => array('loan', 'Loan Calculator'),
							4 => array('similar', 'Similar'),
							5 => array('form', '')
						);

						$tab_data = array(
							'options' => array( (count($vehicle['dealer_options']) > 0 ? 1 : 0), $vehicle['dealer_options'] ),
							'description' => array( (strlen($vehicle['description']) > 0 ? 1 : 0), $vehicle['description'] ),
							'equipment' => array( ($inventory_options['standard_equipment'] && !is_Empty_check($vehicle['standard_equipment']) ? 1 : 0 ), $vehicle['standard_equipment'] ),
							'loan' => array( $loan_settings['display_calc'], $loan_settings ),
							'similar' => array($theme_settings['display_similar'], $similar_output),
							'form' => array( (function_exists(gravity_form) && isset($theme_settings['gravity_form']['data'])?1:0), $theme_settings['gravity_form']['data'] ),
							'values' => array('saleclass' => $vehicle['saleclass'], 'price' => $price['primary_price'] )
						);

						build_tab_display( $tab_buttons, $tab_data, $theme_settings['default_info'] );

					?>
				</div>
				<div id="bobcat-disclaimer">
					<?php echo $vehicle['disclaimer']; ?>
				</div>
			</div>
			<?php
				if ( is_active_sidebar( 'vehicle-detail-page' ) ) :
					echo '<div id="detail-widget-area">';
						dynamic_sidebar( 'vehicle-detail-page' );
					echo '</div>';
				endif;
			?>
		</div>
	</div>

