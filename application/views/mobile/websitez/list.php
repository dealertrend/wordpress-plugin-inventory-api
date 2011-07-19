<?php

	global $wp_rewrite;

	$on_page = isset( $inventory[ 0 ]->pagination->on_page ) ? $inventory[ 0 ]->pagination->on_page : 0;
	$total = isset( $inventory[ 0 ]->pagination->total ) ? $inventory[ 0 ]->pagination->total : 0;

	$args = array(
		'base' => add_query_arg( 'page' , '%#%' ),
		'current' => $on_page,
		'total' => $total,
		'next_text' => __( 'Next &raquo;' ),
		'prev_text' => __( '< Previous' ),
		'show_all' => false,
		'type' => 'plain'
	);

	$quick_links = null;

?>

<div class="websitez-container dealertrend-mobile inventory">
	<div class="post nav">
		<div class="select">
			<p><select name='' onchange='window.location=this.value'><option value='/inventory/All/'>View All Cars</option><option value='/inventory/New/' <?php if($sale_class == "New") echo "selected";?>>View New Cars</option><option value='/inventory/Used/' <?php if($sale_class == "Used") echo "selected";?>>View Used Cars</option></select></p>
		<?php
			//Car Makes
			if( !isset( $parameters[ 'make' ] ) || $parameters[ 'make' ] == 'All' ):
				$makes = $vehicle_management_system->get_makes( array( 'saleclass' => $sale_class ) );
				if(count($makes) > 0):
					$quick_links = !empty( $wp_rewrite->rules ) ? '<option value="/inventory/'.$sale_class.'">View All Makes</option>' : '<option value-"'.@add_query_arg( array( 'make' => 'All' , 'model' => 'All' , 'trim' => 'All' ) ).'">View All Makes</option>';
					foreach( $makes as $make ):
						if( !empty( $wp_rewrite->rules ) ):
							$quick_links .= '<option value="/inventory/'. $sale_class . '/' . $make . '/">' . $make . '</option>';
						else:
							$quick_links .= '<option value="?taxonomy=inventory&amp;saleclass='. $sale_class . '&amp;make=' . $make . '">' . $make . '</option>';
						endif;
					endforeach;
				endif;
			else:
				$quick_links = !empty( $wp_rewrite->rules ) ? '<option value="/inventory/'.$sale_class.'">View All Makes</option>' : '<option value-"'.@add_query_arg( array( 'make' => 'All' , 'model' => 'All' , 'trim' => 'All' ) ).'">View All Makes</option>';
				$quick_links .= '<option value="/inventory/'. $sale_class . '/' . $parameters[ 'make' ] . '/" selected>' . $parameters[ 'make' ] . '</option>';
			endif;

			echo !empty($quick_links) ? "<p><select name='' onchange='window.location=this.value'>".$quick_links."</select></p>" : NULL;

			//Car Models
			if( !isset( $parameters[ 'model' ] ) || $parameters[ 'model' ] == 'All' ):
				$models = $vehicle_management_system->get_models( array( 'saleclass' => $sale_class , 'make' => $parameters[ 'make'] ) );
				$quick_links = !empty( $wp_rewrite->rules ) ? '<option value="/inventory/'.$sale_class.'">View All Models</option>' : '<option value-"'.@add_query_arg( array( 'make' => $parameters['make'] , 'model' => 'All' , 'trim' => 'All' ) ).'">View All Models</option>';
				if(is_array($models) && count($models) > 0):
					foreach( $models as $model ):
						if( !empty( $wp_rewrite->rules ) ):
							$quick_links .= '<option value="/inventory/'. $sale_class . '/' . $parameters[ 'make'] . '/' . $model . '/">' . $model . '</option>';
						else:
							$quick_links .= '<option value="?taxonomy=inventory&amp;saleclass='. $sale_class . '&amp;make=' . $parameters[ 'make'] . '&amp;model=' . $model . '">' . $model . '</option>';
						endif;
					endforeach;
				endif;
			else:
				$quick_links = !empty( $wp_rewrite->rules ) ? '<option value="/inventory/'.$sale_class.'/'.$parameters['make'].'/">View All Models</option>' : '<option value-"'.@add_query_arg( array( 'make' => $parameters['model'] , 'model' => 'All' , 'trim' => 'All' ) ).'">View All Models</option>';
				$quick_links .= '<option value="/inventory/'. $sale_class . '/' . $parameters[ 'make' ] . '/' . $parameters['model']. '/" selected>' . $parameters[ 'model' ] . '</option>';
			endif;

			echo !empty($quick_links) ? "<p><select name='' onchange='window.location=this.value'>".$quick_links."</select></p>" : NULL;

			//Car Trims
			if( !isset( $parameters[ 'trim' ] ) || $parameters[ 'trim' ] == 'All' ):
				$trims = $vehicle_management_system->get_trims( array( 'saleclass' => $sale_class , 'make' => $parameters[ 'make'] , 'model' => $parameters[ 'model'] ) );
				$quick_links = !empty( $wp_rewrite->rules ) ? '<option value="/inventory/' . $sale_class . '/' . $parameters[ 'make'] . '/' . $parameters['model'] . '/All/">View All Trims</option>' : '<option value="' . @add_query_arg( array( 'make' => $parameters['make'], 'model' => $parameters['model'] , 'trim' => 'All' ) ) . '">View All Trims</option>';
				if(count($trims) > 0):
					foreach( $trims as $trim ):
						$quick_links .= '<option value="' . @add_query_arg( array( 'trim' => $trim ) ) . '">' . $trim . '</option>';
					endforeach;
				endif;
			else:
				$quick_links = !empty( $wp_rewrite->rules ) ? '<option value="/inventory/'.$sale_class.'/' . $parameters['make'] . '/' . $parameters['model'] . '/">View All Trims</option>' : '<option value-"'.@add_query_arg( array( 'make' => 'All' , 'model' => 'All' , 'trim' => 'All' ) ).'">View All Trims</option>';
				$quick_links .= '<option value="/inventory/'. $sale_class . '/' . $parameters[ 'make' ] . '/' . $parameters['model']. '/' . $parameters['trim'] . '/" selected>' . $parameters[ 'trim' ] . '</option>';
			endif;

			echo !empty($quick_links) ? "<p><select name='' onchange='window.location=this.value'>".$quick_links."</select></p>" : NULL;
		?>
		</div>
		<div class="paginate">
			<p><?php echo paginate_links( $args ); ?></p>
		</div>
	</div>
	<div class="listing">
		<?php
			if( empty( $inventory ) ) {
				echo '<div class="websitez-not-found"><h2><strong>Unable to find inventory items that matched your search criteria.</strong></h2><a onClick="history.go(-1)" title="Return to Previous Search" class="jquery-ui-button">Return to Previous Search</a></div>';
			} else {
				foreach( $inventory as $inventory_item ):
					$prices = $inventory_item->prices;
					$use_was_now = $prices->{ 'use_was_now?' };
					$use_price_strike_through = $prices->{ 'use_price_strike_through?' };
					$on_sale = $prices->{ 'on_sale?' };
					$sale_price = isset( $prices->sale_price ) ? $prices->sale_price : NULL;
					$sale_expire = isset( $prices->sale_expire ) ? $prices->sale_expire : NULL;
					$retail_price = $prices->retail_price;
					$default_price_text = $prices->default_price_text;
					$asking_price = $prices->asking_price;
					$year = $inventory_item->year;
					$make = urldecode( $inventory_item->make );
					$model = urldecode( $inventory_item->model_name );
					$vin = $inventory_item->vin;
					$trim = urldecode( $inventory_item->trim );
					$engine = $inventory_item->engine;
					$transmission = $inventory_item->transmission;
					$exterior_color = $inventory_item->exterior_color;
					$interior_color = $inventory_item->interior_color;
					$stock_number = $inventory_item->stock_number;
					$odometer = $inventory_item->odometer;
					$icons = $inventory_item->icons;
					$thumbnail = urldecode( $inventory_item->photos[ 0 ]->small );
					$body_style = $inventory_item->body_style;
					$drive_train = $inventory_item->drive_train;
					$doors = $inventory_item->doors;
					$headline = "$year $make $model";
					$custom_headline = $inventory_item->headline;
					if( !empty( $wp_rewrite->rules ) ) {
						$inventory_url = $site_url . '/inventory/' . $year . '/' . $make . '/' . $model . '/' . $state . '/' . $city . '/'. $vin . '/';
					} else {
						$inventory_url = '?taxonomy=inventory&amp;saleclass=' . $sale_class . '&amp;make=' . $make . '&amp;model=' . $model . '&amp;state=' . $state . '&amp;city=' . $city . '&amp;vin='. $vin;
					}
					$contact_information = $inventory_item->contact_info;
					$generic_vehicle_title = $year . ' ' . $make . ' ' . $model;
			?>
				<a href="<?php echo $inventory_url; ?>" title="<?php echo $generic_vehicle_title; ?>"><div class="post item" id="<?php echo $vin; ?>">
					<div class="post-wrapper">
						<div class="photo">
							<img src="<?php echo $thumbnail; ?>" alt="<?php echo $generic_vehicle_title; ?>" title="<?php echo $generic_vehicle_title; ?>"/>
						</div>
						<div class="details">
							<p class="headline"><?php echo $headline; ?></p>
							<p class="engine"><?php echo $engine; ?> - <?php echo $transmission; ?></p>
							<p class="exterior-color"><?php echo $exterior_color; ?></p>
							<p class="odometer"><?php echo number_format($odometer); ?> miles</p>
						</div>
							<div class="clear"></div>
							<?php if(strlen($custom_headline) > 0): ?>
								<div class="bottom-column">
									<p class="custom-headline"><?php echo $custom_headline; ?></p>
								</div>
							<?php endif; ?>
						<div class="bottom-column">
							<p class="icons"><?php echo $icons; ?></p>
						</div>
						<div class="pricing">
							<?php
								$ais_incentive = isset( $inventory_item->ais_incentive->to_s ) ? $inventory_item->ais_incentive->to_s : NULL;
								$incentive_price = 0;
								if( $ais_incentive != NULL ) {
									preg_match( '/\$\d*\s/' , $ais_incentive , $incentive );
									$incentive_price = isset( $incentive[ 0 ] ) ? str_replace( '$' , NULL, $incentive[ 0 ] ) : 0;
								}
								if( $on_sale && $sale_price > 0 ) {
									$now_text = 'Price: ';
									if( $use_was_now ) {
										$price_class = ( $use_price_strike_through ) ? 'websitez-strike-through websitez-asking-price' : 'websitez-asking-price';
										if( $incentive_price > 0 ) {
											echo '<div class="' . $price_class . '">Was: ' . money_format( '%(#0n' , $sale_price ) . '</div>';
										} else {
											echo '<div class="' . $price_class . '">Was: ' . money_format( '%(#0n' , $asking_price ) . '</div>';
										}
										$now_text = 'Now: ';
									}
									if( $incentive_price > 0 ) {
										echo '<div class="websitez-ais-incentive">Savings: ' . $ais_incentive . '</div>';
										echo '<div class="websitez-sale-price">' . $now_text . money_format( '%(#0n' , $sale_price - $incentive_price ) . '</div>';
										if( $sale_expire != NULL ) {
											echo '<div class="websitez-sale-expires">Sale Expires: ' . $sale_expire . '</div>';
										}
									} else {
										if( $ais_incentive != NULL ) {
											echo '<div class="websitez-ais-incentive">Savings: ' . $ais_incentive . '</div>';
										}
										echo '<div class="websitez-sale-price">' . $now_text . money_format( '%(#0n' , $sale_price ) . '</div>';
										if( $sale_expire != NULL ) {
											echo '<div class="websitez-sale-expires">Sale Expires: ' . $sale_expire . '</div>';
										}
									}
								} else {
									if( $asking_price > 0 ) {
										if( $incentive_price > 0 ) {
											echo '<div class="websitez-asking-price" style="font-size:12px;">Retail Price: ' . money_format( '%(#0n' , $asking_price ) . '</div>';
											echo '<div class="websitez-ais-incentive">Savings: ' . $ais_incentive . '</div>';
											echo '<div class="websitez-asking-price" style="font-size:16px;">Your Price: ' . money_format( '%(#0n' , $asking_price - $incentive_price ) . '</div>';
										} else {
											if( $ais_incentive != NULL ) {
												echo '<div class="websitez-ais-incentive">Savings: ' . $ais_incentive . '</div>';
											}
											echo '<div class="websitez-asking-price">Price: ' . money_format( '%(#0n' , $asking_price ) . '</div>';
										}
									} else {
										if( $ais_incentive != NULL ) {
											echo '<div class="websitez-ais-incentive">Savings: ' . $ais_incentive . '</div>';
										}
										echo $default_price_text;
									}
								}
							?>
							</div>
							<div style="clear: both;"></div>
						</div>
					</div>
				</a>
			<?php
				flush();
				endforeach;
			}
			?>
	</div>
	<div class="post nav">
		<div class="select">
			<div class="paginate">
				<p><?php echo paginate_links( $args ); ?></p>
			</div>
		</div>
	</div>
</div>
