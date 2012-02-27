<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

global $wp_rewrite;

wp_enqueue_script(
	'dealertrend-inventory-theme-cobra-select-box',
	$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/cobra/js/jquery.selectBox.js',
	array( 'jquery' ),
	$this->plugin_information[ 'Version' ],
	true
);

echo '
<script type="text/javascript">
(function ($) {
    $(document).ready(function() {
        $("#cobra #quick-links select").selectBox();
    });
}(jQuery));
</script>
';

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

$vehicle_class = isset( $parameters[ 'vehiclesclass' ] ) ? ucwords( $parameters[ 'vehicleclass' ] ) : 'All';

$vehicle_management_system->tracer = 'Calculating how many items were returned with the given parameters.';
$total_found = $vehicle_management_system->get_inventory()->please( array_merge( $this->parameters , array( 'per_page' => 1 , 'photo_view' => 1 ) ) );
$total_found = json_decode( $total_found[ 'body' ] );
$total_found = is_array( $total_found ) && count( $total_found ) > 0 ? $total_found[ 0 ]->pagination->total : 0;

$do_not_carry = remove_query_arg( 'page' , $query );
$tmp_do_not_carry = remove_query_arg( 'certified' , $do_not_carry );

$new = ! empty( $wp_rewrite->rules ) ? '/inventory/new/' : add_query_arg( array( 'saleclass' => 'new' ) , $tmp_do_not_carry );
$used = ! empty( $wp_rewrite->rules ) ? '/inventory/used/' : add_query_arg( array( 'saleclass' => 'used' ) );

$vehicleclass = isset( $this->parameters[ 'vehicleclass' ] ) ? $this->parameters[ 'vehicleclass' ] : NULL;
$price_to = isset( $this->parameters[ 'price_to' ] ) ? $this->parameters[ 'price_to' ] : NULL;
$price_from = isset( $this->parameters[ 'price_from' ] ) ? $this->parameters[ 'price_from' ] : NULL;
$certified = isset( $this->parameters[ 'certified' ] ) ? $this->parameters[ 'certified' ] : NULL;
$filters = array(
	'vehicleclass' => $vehicleclass,
	'price_to' => $price_to,
	'price_from' => $price_from,
	'certified' => $certified
);
$vehicle_management_system->tracer = 'Obtaining a list of makes for the quick-links.';
$makes = $vehicle_management_system->get_makes()->please( array_merge( array( 'saleclass' => $sale_class ) , $filters ) );
$makes = json_decode( $makes[ 'body' ] );
$make_count = count ( $makes );

$sort = isset( $_GET[ 'sort' ] ) ? $_GET[ 'sort' ] : NULL;
switch( $sort ) {
	case 'year_asc': $sort_year_class = 'asc'; break;
	case 'year_desc': $sort_year_class = 'desc'; break;
	case 'price_asc': $sort_price_class = 'asc'; break;
	case 'price_desc': $sort_price_class = 'desc'; break;
	case 'mileage_asc': $sort_mileage_class = 'asc'; break;
	case 'mileage_desc': $sort_mileage_class = 'desc'; break;
	default: $sort_year_class = $sort_price_class = $sort_mileage_class = null; break;
}
$sort_year = $sort != 'year_asc' ? 'year_asc' : 'year_desc';
$sort_mileage = $sort != 'mileage_asc' ? 'mileage_asc' : 'mileage_desc';
$sort_price = $sort != 'price_asc' ? 'price_asc' : 'price_desc';

$shown_makes = array();

$make = isset( $parameters[ 'make' ] ) ? $parameters[ 'make' ] : 'all';
$parameters[ 'make' ] = $make;

echo '
<div id="cobra">
	<br id="top" class="clear" />
	<div id="listing">
		<div id="quick-links">';
			echo '
			<span>Make: </span>
			<select onchange="window.location = this.value;" class="styled">
				<option value="' . $site_url . '/inventory/' . $sale_class . '/">All Makes</option>';
				foreach( $makes as $make ) {
					$make_safe = str_replace( '/' , '%252' , $make );
					$make_safe = ucwords( strtolower( $make_safe ) );
					if( ! in_array( $make_safe , $shown_makes ) ) {
						$shown_makes[] = $make_safe;
						if( !empty( $wp_rewrite->rules ) ) {
							$url = $site_url . '/inventory/' . $sale_class . '/' . $make_safe . '/';
							$url .= isset( $this->parameters[ 'vehicleclass' ] ) ? '?' . http_build_query( array( 'vehicleclass' => $this->parameters[ 'vehicleclass' ] ) ) : NULL;
							echo '<option value="' . $url . '"';
							if( rawurlencode( strtolower( $make_safe ) ) == strtolower( $parameters[ 'make' ] ) ) {
								echo ' selected="selected" ';
							}
						} else {
							echo '<option value="?taxonomy=inventory&make=' . $make_safe . '"';
							if( rawurlencode( strtolower( $make_safe ) ) == strtolower( $parameters[ 'make' ] ) ) {
								echo ' selected="selected" ';
							}
						}
						echo '>' . $make . '</option>';
					}
				}
			echo '
			</select>
			';

			if( isset( $parameters[ 'make' ] ) && $parameters[ 'make' ] != 'all' ) {
				$tmp_do_not_carry = remove_query_arg( 'make' , $do_not_carry );
				$models = $vehicle_management_system->get_models()->please( array_merge( array( 'saleclass' => $sale_class , 'make' => $parameters[ 'make' ] ) , $filters ) );
				$models = json_decode( $models[ 'body' ] );
				$model_count = count( $models );
			}

			$model = isset( $parameters[ 'model' ] ) ? $parameters[ 'model' ] : 'all';
			$parameters[ 'model' ] = $model;
			echo '
			<span>Models: </span>
			<select onchange="window.location = this.value;" class="styled"';
			if( ! isset( $model_count ) || $model_count == 0 ) {
				echo ' disabled="disabled" ';
			}
			echo '>';
			if( !empty( $wp_rewrite->rules ) ) {
			echo '<option value="' . $site_url . '/inventory/' . $sale_class . '/' . $make . ' "/>View All Models</option>';
			} else {
			echo '<option value="' . $site_url . '?taxonomy=inventory&saleclass=' . $sale_class . '&' . $make . ' "/>View All Models</option>';
			}
				if( $model_count > 0 ) {
					if( $model_count == 1 ) {
						$parameters[ 'model' ] = rawurlencode( $models[ 0 ] );
					}
					foreach( $models as $model ) {
						$model_safe = str_replace( '/' , '%252' , $model );
						$model_safe = ucwords( strtolower( $model_safe ) );
						if( !empty( $wp_rewrite->rules ) ) {
							$url = $site_url . '/inventory/' . $sale_class . '/' . $parameters[ 'make' ] . '/' . $model_safe . '/';
							$url .= isset( $this->parameters[ 'vehicleclass' ] ) ? '?' . http_build_query( array( 'vehicleclass' => $this->parameters[ 'vehicleclass' ] ) ) : NULL;
							echo '<option value="' . $url . '"';
							if( rawurlencode( strtolower( $model_safe ) ) == strtolower( $parameters[ 'model' ] ) ) {
								echo ' selected="selected" ';
							}
							echo '>' . $model . '</option>';
						} else {
							echo '<option value="' . @add_query_arg( array( 'model' => $model_safe ) , $do_not_carry ) . '"';
							if( rawurlencode( strtolower( $model_safe ) ) == strtolower( $parameters[ 'model' ] ) ) {
								echo ' selected="selected" ';
							}
						}
						echo '>' . ucwords( strtolower( $model ) ) . '</option>';
					}
				}
			echo '
			</select>';

			if( isset( $parameters[ 'model' ] ) && $parameters[ 'model' ] != 'all' ) {
				$tmp_do_not_carry = remove_query_arg( array( 'make' , 'model' ) , $do_not_carry );
				$trims = $vehicle_management_system->get_trims()->please( array_merge( array( 'saleclass' => $sale_class , 'make' => $parameters[ 'make' ] , 'model' => $parameters[ 'model' ] ) , $filters ) );
				$trims = json_decode( $trims[ 'body' ] );
				$trim_count = count( $trims );
			}

			if( isset( $trim_count ) && $trim_count != 0 ) {
				$trim = isset( $parameters[ 'trim' ] ) ? $parameters[ 'trim' ]  : 'all';
				$parameters[ 'trim' ] = $trim;
				echo '
				<span>Trims: </span>
				<select onchange="window.location = this.value;" class="styled"';
				if( ! isset( $trim_count ) || $trim_count == 0 ) {
					echo ' disabled="disabled" ';
				}
				echo '>
					<option value="' . $site_url . '/inventory/' . $sale_class . '/' . $parameters[ 'make' ] . '/' . $parameters[ 'model' ] . '/">View All Trims</option>';
					if( $trim_count == 1 ) {
						$parameters[ 'trim' ] = $trims[ 0 ];
					}
						foreach( $trims as $trim ) {
							$trim_safe = str_replace( '/' , '%252' , $trim );
							$trim_safe = ucwords( strtolower( $trim_safe ) );
							if( !empty( $wp_rewrite->rules ) ) {
								$url = $site_url . '/inventory/' . $sale_class . '/' . $make . '/' . $model . '/' . $trim_safe . '/';
								$url .= isset( $this->parameters[ 'vehicleclass' ] ) ? '?' . http_build_query( array( 'vehicleclass' => $this->parameters[ 'vehicleclass' ] ) ) : NULL;
								echo '<option value="' . $url . '"';
								if( rawurlencode( strtolower( $trim_safe ) ) == rawurlencode( strtolower( $parameters[ 'trim' ] ) ) ) {
									echo ' selected="selected" ';
								}
								echo '>' . $trim . '</option>';
							} else {
								echo '<option value="' . @add_query_arg( array( 'trim' => $trim_safe ) , $do_not_carry ) . '"';
								if( rawurlencode( strtolower( $trim_safe ) ) == rawurlencode( strtolower( $parameters[ 'trim' ] ) ) ) {
									echo ' selected="selected" ';
								}
							}
							echo '>' . ucwords( strtolower( $trim ) ) . '</option>';
						}
				echo '
				</select>';
			}

			echo '
			<form action="" method="GET" id="search">
				<input id="search-box" name="search" value="';
				echo isset( $parameters[ 'search' ] ) ? $parameters[ 'search' ] : NULL;
				echo '" />
				<input id="search-submit" value="Go" type="submit" />
			</form>';
echo '
		</div>
	</div>
	<div id="total-found">Found ' . $total_found . ' Exact Matches:&nbsp;</div>' . $breadcrumbs . '
	<div class="pager">
		' . paginate_links( $args ) . '
	</div>

	<div id="sorting-columns">
		Sort options:
		<a class="' . $sort_year_class . '" href="' . @add_query_arg( array( 'sort' => $sort_year ) , $do_not_carry ) . '">Year</a> /
		<a class="' . $sort_price_class . '" href="' . @add_query_arg( array( 'sort' => $sort_price ) , $do_not_carry ) . '">Price</a> /
		<a class="' . $sort_mileage_class . '" href="' . @add_query_arg( array( 'sort' => $sort_mileage ) , $do_not_carry ) . '">Mileage</a>
	</div>

	<div id="content">
		<div id="items">';
			if( empty( $inventory ) ) {
				echo '<div class="not-found"><h2><strong>Unable to find inventory items that matched your search criteria.</strong></h2></div>';
			} else {
					foreach( $inventory as $inventory_item ) {
						$sale_class = $inventory_item->saleclass;
						$prices = $inventory_item->prices;
						$use_was_now = $prices->{ 'use_was_now?' };
						$use_price_strike_through = $prices->{ 'use_price_strike_through?' };
						$on_sale = $prices->{ 'on_sale?' };
						$sale_price = isset( $prices->sale_price ) ? $prices->sale_price : NULL;
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
						$headline = $inventory_item->headline;
						
						if( ! empty( $wp_rewrite->rules ) ) {
							$inventory_url = $site_url . '/inventory/' . $year . '/' . $make . '/' . $model . '/' . $state . '/' . $city . '/'. $vin . '/';
						} else {
							$inventory_url = '?taxonomy=inventory&amp;saleclass=' . $sale_class . '&amp;make=' . $make . '&amp;model=' . $model . '&amp;state=' . $state . '&amp;city=' . $city . '&amp;vin='. $vin;
						}
						$contact_information = $inventory_item->contact_info;
						$generic_vehicle_title = $year . ' ' . $make . ' ' . $model;

					echo '
					<div class="item" id="' . $vin . '">
						<div class="photo">
							<a href="' . $inventory_url . '" title="' . $generic_vehicle_title . '">
								<img src="' . $thumbnail . '" alt="' . $generic_vehicle_title . '" title="' . $generic_vehicle_title . '" />
							</a>
							<a class="info" href="' . $inventory_url . '" title="More Information: ' . $generic_vehicle_title . '">Click here to view details</a>
						</div>
						<div class="left">
							<div class="main">
								<a href="' . $inventory_url . '" title="' . $generic_vehicle_title . '" class="details">
									<div style="overflow:hidden; width:260px; height:24px; line-height:24px; margin-top:-5px;">
										<span class="year">' . $year . '</span>
										<span class="make">' . $make . '</span>
										<span class="model">' . $model . '</span>
									</div>
									<span class="trim">' . $trim . '&nbsp;</span>
								</a>
							</div>
							<div class="stock-n-vin">
								Stock #: ' . $stock_number . ' - VIN: ' . $vin . '
							</div>
						</div>
						<div class="right">
							<div class="price">
								<span class="msrp">';
									if( strtolower( $sale_class ) == 'new' && ! $on_sale ) { echo 'MSRP'; }
								echo '
									&nbsp;
								</span>';
								if( $on_sale && $sale_price > 0 ) {
									$now_text = 'Price: ';
									if( $use_was_now ) {
										$price_class = ( $use_price_strike_through ) ? 'strike-through asking-price' : 'asking-price';
										echo '<div class="' . $price_class . ' was"><span>Was: ' . '$</span><span>' . number_format( $asking_price , 0 , '.' , ',' ) . '</span></div>';
										$now_text = '';
									}
									echo '<div class="sale-price"><span>' . $now_text . '$</span><span>' . number_format( $sale_price , 0 , '.' , ',' ) . '</span></div>';
								} else {
									if( $asking_price > 0 ) {
										echo '<div class="asking-price"><span>$</span>' . number_format( $asking_price , 0 , '.' , ',' ) . '</div>';
									} else {
										echo '<div>' . $default_price_text . '</div>';
									}
								}
								echo '
							</div>
							<a class="info-button" href="' . $inventory_url . '"></a>
						</div>
						<div class="info">';
						if( ! empty( $exterior_color ) ) {
							echo '<span class="exterior-color">Ext. Color: ' . $exterior_color . '</span>';
						}
						if( ! empty( $interior_color ) ) {
							echo '<span class="interior-color">Int. Color: ' . $interior_color . '</span>';
						}
						if( ! empty( $engine ) ) {
							echo '<span class="engine">Engine: ' . $engine . '</span>';
						}
						echo '
						</div>
						<div class="cerfitied-pre-owned">';
							if ( $inventory_item->certified ) {
								// this is broken...we don't have all the images for all the makes...
								//echo '<img src="' . plugin_dir_url( __FILE__ ) . 'images/' . $make . '-cpo-white.png" />';
							}
						echo '
						</div>
					</div>
					<br class="clear" />';
				}
			}
			echo '
			<div class="pager">
				' . paginate_links( $args ) . '
			</div>
			<br class="clear" />
		</div>
		<div id="cobra-disclaimer">
			<p>' . $inventory[ 0 ]->disclaimer . '</p>
		</div>
		<br class="clear" />
	</div>

</div>';

?>
<div style="display: none;">
	<div id="cobra-make-offer">
		<h1>Make us an Offer</h1>
		<div class="required">*Required information</div>
		<?php echo do_shortcode('[contact-form-7 id="200" title="Make Offer Form"]'); ?>
	</div>
</div>
