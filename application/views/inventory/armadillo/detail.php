<?php
namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

	$vehicle = itemize_vehicle($inventory);
	$price = get_price_display($vehicle['prices'], $company_information, $vehicle['saleclass'], $vehicle['vin'], 'armadillo', $price_text );
	$vehicle['primary_price'] = $price['primary_price'];
	$parameters['saleclass'] = $vehicle['saleclass'];

	apply_gravity_form_hooks( $vehicle );

	$traffic_source = isset( $_COOKIE[ 'dealertrend-traffic-source' ] ) ? $_COOKIE[ 'dealertrend-traffic-source' ] : FALSE;
	$traffic_source = $this->sanitize_inputs( $traffic_source );

	// GForm Buttons
	if( function_exists('gravity_form') && isset($theme_settings['gravity_form']['data']) ){
		$gform_button_display = get_gform_button_display( $theme_settings['gravity_form']['data'], $vehicle['saleclass'] );
	}
	// Loan Display
	if( $loan_settings['display_calc'] ){
		$loan_display = get_loan_calculator($loan_settings, $vehicle['primary_price'], TRUE);
	}
	// Phone Display
	$phone_display = get_dealer_contact_number( $vehicle['contact_info'], $inventory_options, $vehicle['saleclass'] );
	// Contact Display
	$contact_display = get_dealer_contact_name( $vehicle['contact_info'], $inventory_options, $vehicle['saleclass'] );
	// AutoCheck Display
	if( $vehicle['autocheck'] ){
		$ac_display = display_autocheck_image( $vehicle['vin'], $vehicle['saleclass'], $type );
	}
	// Fuel Display
	$fuel_info = get_fuel_economy_display( $vehicle['fuel_economy'], $country_code, 0, $vehicle_reference_system, $vehicle['acode'] );
	if( !empty($fuel_info) ){
		$fuel_display = $fuel_info;
	}
	// Tag Display
	if( $theme_settings['display_tags'] ){
		apply_special_tags( $vehicle['tags'], $vehicle['prices']['on_sale'], $vehicle['certified'], $vehicle['video']);
		if( !empty( $vehicle['tags'] ) ){
			$tag_display = '<div class="armadillo-detail-tags">'.build_tag_icons( $default_tag_names, $custom_tag_icons, $vehicle['tags']).'</div>';
		}
	}
	// Options/Description Display
	$detail_tabs_info = get_vehicle_detail_display( $vehicle['dealer_options'], $vehicle['description'], FALSE, array(), $theme_settings['default_info']);
	// Standard Equipment Display
	if( isset($vehicle['standard_equipment']) && !is_Empty_check($vehicle['standard_equipment']) && $inventory_options['standard_equipment'] ){
		$equipment_display = '<div id="standard-equipment"><h3>Standard Equipment</h3><div id="eq-wrapper">'.display_equipment($vehicle['standard_equipment']).'</div></div>';
	}
	// Photo Display
	$photo_display = get_photo_detail_display( $vehicle['photos'], $vehicle['video'], $theme_settings['default_image'] );
	
	function get_info_column( $style, $vehicle, $phone, $contact, $loan, $ac, $host, $ts, $c_id, $gform_id, $gform_data){
		$column = '<div id="armadillo-column-info" class="armadillo-column '.$style.'">'; // column wrapper
		$column .= '<div id="armadillo-request-form">'; // get info form wrapper
		if( function_exists('gravity_form') && !empty($gform_id) ){
			//get_form_display( $theme_settings['gravity_form']['data'], $vehicle['saleclass'] );
			$column .= '<div class="form-wrapper">';
			$column .= '<div id="info-form-id-'.$gform_id.'" class="form-display-wrap form-'.$gform_id.'" name="form-id-'.$gform_id.'">';
			$column .= do_shortcode('[gravityform id='.$gform_id.' title=true description=false]');
			$column .= '</div>';
			$column .= '</div>';
		} else {
			$column .= '<div id="request-form-header" class="armadillo-header">Make an Offer / Get Info</div>';
			$column .= '<form action="'.$host.'/'.$c_id.'/forms/create/'.strtolower($vehicle['saleclass']).'_vehicle_inquiry" method="post" name="vehicle-inquiry" id="vehicle-inquiry">';
			$column .= '<input type="hidden" name="traffic_source" value="'.$ts.'"/> <input name="required_fields" type="hidden" value="name,email,privacy" /> <input name="subject" type="hidden" value="Vehicle Inquiry - '.$vehicle['headline'].'" /> <input name="saleclass" type="hidden" value="'.$vehicle['saleclass'].'" /> <input name="vehicle" type="hidden" value="'.$vehicle['year'].' '.$vehicle['make']['clean'].' '.$vehicle['model']['clean'].'" /> <input name="year" type="hidden" value="'.$vehicle['year'].'" /> <input name="make" type="hidden" value="'.$vehicle['make']['clean'].'" /> <input name="model_name" type="hidden" value="'.$vehicle['model']['clean'].'" /> <input name="trim" type="hidden" value="'.$vehicle['trim']['clean'].'" /> <input name="stock" type="hidden" value="'.$vehicle['stock_number'].'" /> <input name="vin" type="hidden" value="'.$vehicle['vin'].'" /> <input name="inventory" type="hidden" value="'.$vehicle['id'].'" /> <input name="price" type="hidden" value="'.$vehicle['primary_price'].'" /> <input name="name" type="hidden" value="" />';
			$column .= '<table><tr><td class="required"><label for="vehicle-inquiry-f-name">First Name</label><input maxlength="70" id="vehicle-inquiry-f-name" name="f_name" tabindex="1" type="text" /></td></tr><tr><td class="required"><label for="vehicle-inquiry-l-name">Last Name</label><input maxlength="70" id="vehicle-inquiry-l-name" name="l_name" tabindex="2" type="text" /></td></tr><td class="required"><label for="vehicle-inquiry-email">Email Address</label><input maxlength="255" id="vehicle-inquiry-email" name="email" tabindex="3" type="text" /></td></tr><tr><td><label for="vehicle-inquiry-phone">Phone Number</label><input maxlength="256" name="phone" id="vehicle-inquiry-phone" tabindex="4" type="text" /></td></tr><tr><td class="required"><label for="vehicle-inquiry-comments">Questions/Comments</label><textarea name="comments" id="vehicle-inquiry-comments" rows="4" tabindex="5"></textarea></td></tr><tr><td><div style="display:none"><input class="privacy" name="privacy" id="vehicle-inquiry-privacy" type="checkbox" value="Yes" checked="checked" /><input name="agree_sb" type="checkbox" value="Yes" /> I am a Spam Bot?</div></td></tr><tr><td><input onclick="document.forms[\'vehicle-inquiry\'][\'name\'].value = document.forms[\'vehicle-inquiry\'][\'f_name\'].value + \' \'  + document.forms[\'vehicle-inquiry\'][\'l_name\'].value; document.forms[\'vehicle-inquiry\'].submit();" type="button" value="Send Inquiry" class="submit" /></td></tr></table>';
			$column .= '</form>';
		}
		$column .= '</div>'; // get info form wrapper
		$column .= '<div id="armadillo-contact-information">'; // dealer contact info
		$column .= '<div id="contact-information-header" class="armadillo-header">Contact Information</div>';
		$column .= '<div id="contact-greeting">'.$vehicle['contact_info']['greeting'].'</div>';
		$column .= '<div id="contact-dealer-name">'.$contact.'</div>';
		$column .= '<div id="contact-dealer-address">'.$vehicle['contact_info']['location'].'</div>';
		if( !empty($phone) ) {
			$column .= '<div id="contact-dealer-phone">Phone Number: <a tel="'.$phone.'">' . $phone . '</a></div>';
		}
		$column .= '</div>'; // dealer contact info
		$column .= '<div id="armadillo-helpful-links">'; // extra links
		if( !empty($loan) ){
			$column .= '<div id="armadillo-calculator">'; // calculator	
			$column .= $loan;
			$column .= '</div>';
		}
		if( !empty($gform_data) ){
			$column .= $gform_data;
		}
		if( !empty($ac) ){
			$column .= $ac;
		}
		if( $vehicle['carfax'] ) {
			$column .= '<div class="carfax-wrapper"><a href="' . $vehicle['carfax'] . '" class="armadillo-carfax" target="_blank"><img src="http://assets.s3.dealertrend.com.s3.amazonaws.com/images/carfax_192x46.jpg" /></a></div>';
		}
		$column .= '</div>'; // extra links
		$column .= '</div>'; // column wrapper
		
		return $column;
	}
	
	function get_detail_column( $style, $vehicle, $price, $fuel, $tags, $detail_tabs, $equipment ){
		$column = '<div id="armadillo-column-details" class="armadillo-column '.$style.'">'; // column wrapper
		$column .= '<div id="armadillo-vehicle-information">'; // vehicle info
		$column .= '<div id="vehicle-info-header" class="armadillo-header">Vehicle Information</div>';
		$column .= '<div id="vehicle-info-wrapper">' . ( !empty($vehicle['exterior_color']) ?'<div id="info-ext-color"><span>Exterior: </span>'.$vehicle['exterior_color'].'</div>':'' ) . ( !empty($vehicle['interior_color']) ?'<div id="info-int-color"><span>Interior: </span>'.$vehicle['interior_color'].'</div>':'' ) . ( !empty($vehicle['engine']) ?'<div id="info-engine"><span>Engine: </span>'.$vehicle['engine'].'</div>':'' ) . ( !empty($vehicle['transmission']) ?'<div id="info-transmission"><span>Transmission: </span>'.$vehicle['transmission'].'</div>':'' ) . ( !empty($vehicle['odometer']) ?'<div id="info-odometer"><span>Odometer: </span>'.$vehicle['odometer'].'</div>':'' ) . ( !empty($vehicle['stock_number']) ?'<div id="info-stock-number"><span>Stock #: </span>'.$vehicle['stock_number'].'</div>':'' ) . ( !empty($vehicle['vin']) ?'<div id="info-vin"><span>VIN: </span>'.$vehicle['vin'].'</div>':'' ).'</div>';
		$column .= '<div id="armadillo-price-wrapper" class="armadillo-price">'.( !empty($price['ais_link']) ? $price['ais_link'] : '') . $price['compare_text'].$price['ais_text'].$price['primary_text'].$price['expire_text'].$price['hidden_prices'].'</div>';
		$column .= '</div>'; // vehicle info
		if( !empty($fuel) ){
			$column .= $fuel;
		}
		if( !empty($tags) ){
			$column .= $tags;
		}
		if( !empty($detail_tabs) ){
			$column .= $detail_tabs;
		}
		if( !empty($equipment) ){
			$column .= $equipment;
		}
		$column .= '</div>'; // column wrapper
			
		return $column;
	}
	
	function get_photo_column( $style, $photo ){
		$column = '<div id="armadillo-column-photo" class="armadillo-column '.$style.'">'; // column wrapper
		$column .= $photo;
		$column .= '</div>'; // column wrapper
		return $column;
	}

?>


<div id="armadillo-wrapper">
	<div id="armadillo-detail" class="armadillo-saleclass-<?php echo $vehicle['saleclass']; ?>">
		<div class="breadcrumb-wrapper">
			<a id="friendly-print" onclick="window.open('?print_page','popup','width=800,height=900,scrollbars=yes,resizable=no,toolbar=no,directories=no,location=no,menubar=yes,status=no,left=0,top=0'); return false">Print</a>
			<div class="breadcrumbs"><?php echo display_breadcrumb( $parameters, $company_information, $inventory_options, $vehicle['saleclass'] ); ?></div>
		</div>
		<div class="armadillo-main-line">
			<h2><?php echo $vehicle['year'] . ' ' . $vehicle['make']['clean'] . ' ' . $vehicle['model']['clean'] . ' ' . $vehicle['trim']['clean'] . ' ' . $vehicle['drive_train'] ?></h2>
			<p><?php echo $vehicle['headline']; ?></p>
		</div>
		<div id="detail-content-wrapper">
			<?php
				switch ($theme_settings['detail_format']) {
					case 0:
						$theme_style = 'column-float-right';
						break;
					case 1:
						$theme_style = 'column-float-left';
						break;
				}
				$info_column = get_info_column($theme_style, $vehicle, $phone_display, $contact_display, $loan_display, $ac_display, $temp_host, $traffic_source, $company_information->id, $theme_settings['detail_gform_id'],$gform_button_display);
				$detail_column = get_detail_column($theme_style, $vehicle, $price, $fuel_display, $tag_display, $detail_tabs_info, $equipment_display);
				$photo_column = get_photo_column($theme_style, $photo_display);
				
				echo $photo_column . $detail_column . $info_column;
			?>
		</div>
		<?php
			if( $theme_settings['display_similar'] ){
				echo '<div id="detail-similar-wrapper">';
				echo get_similar_vehicles( $vehicle_management_system, $vehicle['vin'], $vehicle['saleclass'], $vehicle['vehicle_class'], $price['primary_price'], $vehicle['make']['name'], $inventory_options['make_filter'], array( 'city' => $city, 'state' => $state) );
				echo '</div>';
			}
		?>
		<div class="armadillo-disclaimer">
			<p><?php echo $vehicle['disclaimer']; ?></p>
		</div>
	</div>
</div>
<?php
	if ( is_active_sidebar( 'vehicle-detail-page' ) ) :
		echo '<div id="sidebar-widget-area" class="sidebar">';
		dynamic_sidebar( 'vehicle-detail-page' );
		echo '</div>';
	endif;
?>
