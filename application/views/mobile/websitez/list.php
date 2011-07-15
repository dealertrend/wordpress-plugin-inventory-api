<?php

	global $wp_rewrite;

	$parameters = $this->parameters;

	$args = array(
		'base' => @add_query_arg('page','%#%'),
		'current' => $inventory[ 0 ]->pagination->on_page,
		'total' => $inventory[ 0 ]->pagination->total,
		'next_text' => __( 'Next &raquo;' ),
		'prev_text' => __( '&laquo; Previous' ),
		'show_all' => false,
		'type' => 'plain'
	);

	$sale_class = isset( $parameters[ 'saleclass' ] ) ? ucwords( $parameters[ 'saleclass' ] ) : 'All';
	$quick_links = $quick_links_end = null;

	if( !isset( $parameters[ 'make' ] ) || $parameters[ 'make' ] == 'All' ) {
		$makes = $vehicle_management_system->get_makes( array( 'saleclass' => $sale_class ) );
		foreach( $makes as $make ) {
			if( !empty( $wp_rewrite->rules ) ) {
				$quick_links .= '<option value="/inventory/'. $sale_class  . '/' . $make . '/">' . $make . '</option>';
			} else {
				$quick_links .= '<option value="?taxonomy=inventory&amp;saleclass='. $sale_class  . '&amp;make=' . $make . '">' . $make . '</option>';
			}
		}
	} elseif( !isset( $parameters[ 'model' ] ) || $parameters[ 'model' ] == 'All' ) {
		$models = $vehicle_management_system->get_models(  array( 'saleclass' => $sale_class , 'make' => $parameters[ 'make'] ) );
		$quick_links_end .= !empty( $wp_rewrite->rules ) ? '<option value="/inventory/'.$sale_class.'">View All Makes</option>' : '<option value-"'.@add_query_arg( array( 'make' => 'All' , 'model' => 'All' , 'trim' => 'All' ) ).'">View All Makes</option>';
		if(is_array($models)):
			foreach( $models as $model ) {
				if( !empty( $wp_rewrite->rules ) ) {
					$quick_links .= '<option value="/inventory/'. $sale_class  . '/' . $parameters[ 'make'] . '/' . $model . '/">' . $model . '</option>';
				} else {
					$quick_links .= '<option value="?taxonomy=inventory&amp;saleclass='. $sale_class  . '&amp;make=' . $parameters[ 'make'] . '&amp;model=' . $model . '">' . $model . '</option>';
				}
			}
		endif;
	} elseif( !isset( $parameters[ 'trim' ] ) || $parameters[ 'trim' ] == 'All' ) {
		$trims = $vehicle_management_system->get_trims(  array( 'saleclass' => $sale_class , 'make' => $parameters[ 'make'] , 'model' => $parameters[ 'model'] ) );
		$quick_links_end .= !empty( $wp_rewrite->rules ) ? '<option value="/inventory/' . $sale_class . '/All/">View All Makes</option>' : '<option value="' . @add_query_arg( array( 'make' => 'All' , 'model' => 'All' , 'trim' => 'All' ) ) . '">View All Makes</option>';
		$quick_links_end .= !empty( $wp_rewrite->rules ) ? '<option value="/inventory/' . $sale_class . '/' . $parameters[ 'make'] . '/All/">View All Models</option>' : '<option value="' . @add_query_arg( array( 'model' => 'All' , 'trim' => 'All' ) ) . '">View All Models</option>';
		foreach( $trims as $trim ) {
			$quick_links .= '<option value="' . @add_query_arg( array( 'trim' => $trim ) ) . '">' . $trim . '</option>';
		}
	} else {
		$quick_links_end .= !empty( $wp_rewrite->rules ) ? '<option value="/inventory/' . $sale_class . '/All/">View All Makes</option>' : '<option value="' . @add_query_arg( array( 'make' => 'All' , 'model' => 'All' , 'trim' => 'All' ) ) . '">View All Makes</option>';
		$quick_links_end .= !empty( $wp_rewrite->rules ) ? '<option value="/inventory/' . $sale_class . '/' . $parameters[ 'make'] . '/All/">View All Models</option>' : '<option value="' . @add_query_arg( array( 'model' => 'All' , 'trim' => 'All' ) ) . '">View All Models</option>';
		$quick_links_end .= '<option value="' . @add_query_arg( array( 'trim' => 'All' ) ) . '">View All Trims</option>';
	}

?>

<div class="websitez-container dealertrend-mobile inventory">
	<div class="post nav">
		<div class="select">
		<?php
			//Car Makes
			if( !isset( $parameters[ 'make' ] ) || $parameters[ 'make' ] == 'All' ):
				$makes = $vehicle_management_system->get_makes( array( 'saleclass' => $sale_class ) );
				if(count($makes) > 0):
					$quick_links = !empty( $wp_rewrite->rules ) ? '<option value="/inventory/'.$sale_class.'">View All Makes</option>' : '<option value-"'.@add_query_arg( array( 'make' => 'All' , 'model' => 'All' , 'trim' => 'All' ) ).'">View All Makes</option>';
					foreach( $makes as $make ):
						if( !empty( $wp_rewrite->rules ) ):
							$quick_links .= '<option value="/inventory/'. $sale_class  . '/' . $make . '/">' . $make . '</option>';
						else:
							$quick_links .= '<option value="?taxonomy=inventory&amp;saleclass='. $sale_class  . '&amp;make=' . $make . '">' . $make . '</option>';
						endif;
					endforeach;
				endif;
			else:
				$quick_links = !empty( $wp_rewrite->rules ) ? '<option value="/inventory/'.$sale_class.'">View All Makes</option>' : '<option value-"'.@add_query_arg( array( 'make' => 'All' , 'model' => 'All' , 'trim' => 'All' ) ).'">View All Makes</option>';
				$quick_links .= '<option value="/inventory/'. $sale_class  . '/' . $parameters[ 'make' ] . '/" selected>' . $parameters[ 'make' ] . '</option>';
			endif;
			
			echo !empty($quick_links) ? "<p><select name='' onchange='window.location=this.value'>".$quick_links."</select></p>" : NULL;
			
			//Car Models
			if( !isset( $parameters[ 'model' ] ) || $parameters[ 'model' ] == 'All' ):
				$models = $vehicle_management_system->get_models(  array( 'saleclass' => $sale_class , 'make' => $parameters[ 'make'] ) );
				$quick_links = !empty( $wp_rewrite->rules ) ? '<option value="/inventory/'.$sale_class.'">View All Models</option>' : '<option value-"'.@add_query_arg( array( 'make' => $parameters['make'] , 'model' => 'All' , 'trim' => 'All' ) ).'">View All Models</option>';
				if(is_array($models) && count($models) > 0):
					foreach( $models as $model ):
						if( !empty( $wp_rewrite->rules ) ):
							$quick_links .= '<option value="/inventory/'. $sale_class  . '/' . $parameters[ 'make'] . '/' . $model . '/">' . $model . '</option>';
						else:
							$quick_links .= '<option value="?taxonomy=inventory&amp;saleclass='. $sale_class  . '&amp;make=' . $parameters[ 'make'] . '&amp;model=' . $model . '">' . $model . '</option>';
						endif;
					endforeach;
				endif;
			else:
				$quick_links = !empty( $wp_rewrite->rules ) ? '<option value="/inventory/'.$sale_class.'/'.$parameters['make'].'/">View All Models</option>' : '<option value-"'.@add_query_arg( array( 'make' => $parameters['model'] , 'model' => 'All' , 'trim' => 'All' ) ).'">View All Models</option>';
				$quick_links .= '<option value="/inventory/'. $sale_class  . '/' . $parameters[ 'make' ] . '/' . $parameters['model']. '/" selected>' . $parameters[ 'model' ] . '</option>';
			endif;
			
			echo !empty($quick_links) ? "<p><select name='' onchange='window.location=this.value'>".$quick_links."</select></p>" : NULL;
			
			//Car Trims
			if( !isset( $parameters[ 'trim' ] ) || $parameters[ 'trim' ] == 'All' ):
				$trims = $vehicle_management_system->get_trims(  array( 'saleclass' => $sale_class , 'make' => $parameters[ 'make'] , 'model' => $parameters[ 'model'] ) );
				//$quick_links = !empty( $wp_rewrite->rules ) ? '<option value="/inventory/' . $sale_class . '/All/">View All Trims</option>' : '<option value="' . @add_query_arg( array( 'make' => 'All' , 'model' => 'All' , 'trim' => 'All' ) ) . '">View All Trims</option>';
				$quick_links = !empty( $wp_rewrite->rules ) ? '<option value="/inventory/' . $sale_class . '/' . $parameters[ 'make'] . '/' . $parameters['model'] . '/All/">View All Trims</option>' : '<option value="' . @add_query_arg( array( 'make' => $parameters['make'], 'model' => $parameters['model'] , 'trim' => 'All' ) ) . '">View All Trims</option>';
				if(count($trims) > 0):
					foreach( $trims as $trim ):
						$quick_links .= '<option value="' . @add_query_arg( array( 'trim' => $trim ) ) . '">' . $trim . '</option>';
					endforeach;
				endif;
			else:
				$quick_links = !empty( $wp_rewrite->rules ) ? '<option value="/inventory/'.$sale_class.'/' . $parameters['make'] . '/' . $parameters['model'] . '/">View All Trims</option>' : '<option value-"'.@add_query_arg( array( 'make' => 'All' , 'model' => 'All' , 'trim' => 'All' ) ).'">View All Trims</option>';
				$quick_links .= '<option value="/inventory/'. $sale_class  . '/' . $parameters[ 'make' ] . '/' . $parameters['model']. '/' . $parameters['trim'] . '/" selected>' . $parameters[ 'trim' ] . '</option>';
			endif;
			
			echo !empty($quick_links) ? "<p><select name='' onchange='window.location=this.value'>".$quick_links."</select></p>" : NULL;
			
			//echo !empty($quick_links) ? "<p><select name='' onchange='window.location=this.value'>".$quick_links."</select></p>" : NULL;
			//echo !empty($quick_links_end) ? "<p><select name='' onchange=''>".$quick_links_end."</select></p>" : NULL;
		?>
		</div>
		<div class="paginate">
			<p><?php echo paginate_links( $args ); ?></p>
		</div>
	</div>
	<div class="listing">
		<?php
			if( empty( $inventory ) ) {
				echo '<div class="post"><div class="post-wrapper"><h2><strong>Unable to find inventory items that matched your search criteria.</strong></h2></div></div>';
			} else {
				foreach( $inventory as $inventory_item ):
					$year = $inventory_item->year;
					$make = $inventory_item->make;
					$model = urldecode( $inventory_item->model_name );
					$vin = $inventory_item->vin;
					$trim = urldecode( $inventory_item->trim );
					$body_style = urldecode( $inventory_item->body_style );
					$engine = $inventory_item->engine;
					$transmission = $inventory_item->transmission;
					$exterior_color = $inventory_item->exterior_color;
					setlocale(LC_MONETARY, 'en_US');
					$prices = $inventory_item->prices;
					$use_was_now = $prices->{ 'use_was_now?' };
					$use_price_strike_through = $prices->{ 'use_price_strike_through?' };
					$on_sale = $prices->{ 'on_sale?' };
					$sale_price = isset( $prices->sale_price ) ? $prices->sale_price : NULL;
					$retail_price = $prices->retail_price;
					$default_price_text = $prices->default_price_text;
					$asking_price = $prices->asking_price;
					$stock_number = $inventory_item->stock_number;
					$odometer = $inventory_item->odometer;
					$icons = $inventory_item->icons;
					$headline = $inventory_item->headline;
					if(strlen($headline) == 0)
						$headline = $year." ".$make." ".$model;
					$thumbnail = urldecode( $inventory_item->photos[ 0 ]->small );
					$doors = $inventory_item->doors . 'D';
					if( !empty( $wp_rewrite->rules ) ) {
						$inventory_url = '/inventory/' . $sale_class . '/' . $make . '/' . $model . '/' . $state . '/' . $city . '/'. $vin . '/';
					} else {
						$inventory_url = '?taxonomy=inventory&amp;saleclass=' . $sale_class . '&amp;make=' . $make . '&amp;model=' . $model . '&amp;state=' . $state . '&amp;city=' . $city . '&amp;vin='. $vin;
					}
					$generic_vehicle_title = $year . ' ' . $make . ' ' . $model; ?>
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
							<div class="bottom-column">
								<p class="icons"><?php echo $icons; ?></p>
							</div>
							<div class="pricing">
								<?php
								if( $on_sale && $sale_price > 0 ) {
									$now_text = 'Price: ';
									if( $use_was_now ) {
										$price_class = ( $use_price_strike_through ) ? 'armadillo-strike-through armadillo-asking-price' : 'armadillo-asking-price';
										echo '<div class="' . $price_class . '">Was: ' . money_format( '%(#0n' , $asking_price ) . '</div>';
										$now_text = 'Now: ';
									}
									echo '<div class="armadillo-sale-price">' . $now_text . money_format( '%(#0n' , $sale_price ) . '</div>';
								} else {
									if( $asking_price > 0 ) {
										echo '<div class="armadillo-asking-price">Price: ' . money_format( '%(#0n' , $asking_price ) . '</div>';
									} else {
										echo $default_price_text;
									}
								}
								?>
							</div>
							<div style="clear: both;"></div>
						</div>
					</div></a>
					<?php
						flush();
				 endforeach;
				} ?>
		</div>
	</div>
</div>
<link rel="stylesheet" id="dealertrend-inventory-api-css" href="<?php echo $this->plugin_information[ 'PluginURL' ]; ?>/application/views/mobile/websitez/dealertrend-inventory-api.css" type="text/css" media="all">