<?php

$pager = $this->pagination( $inventory );

$parameters = $this->parameters;
$args = array(
	'base' => @add_query_arg('page','%#%'),
	'current' => $pager[ 'current_page' ],
	'total' => $pager[ 'total_pages' ],
	'next_text' => __( 'Next &raquo;' ),
	'prev_text' => __( '&laquo; Previous' ),
	'show_all' => true,
	'type' => 'plain'
);

?>

<div class="pager">
	<?php echo paginate_links( $args ); ?>
</div>
<div class="dealertrend inventory listing">
<?php foreach( $inventory as $inventory_item ): ?>

<?php
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
	$asking_price = money_format( '%(#0n', $prices->asking_price );
	$stock_number = $inventory_item->stock_number;
	$odometer = $inventory_item->odometer;
	$icons = $inventory_item->icons;
	$headline = $inventory_item->headline;
	$thumbnail = urldecode( $inventory_item->photos[ 0 ]->small );

	if( !empty( $wp_rewrite->rules ) ) {
		$inventory_url = '/inventory/' . $year . '/' . $make . '/' . $model . '/' . $state . '/' . $city . '/'. $vin . '/';
	} else {
		$inventory_url = '?taxonomy=inventory&amp;vehicle_year=' . $year . '&amp;make=' . $make . '&amp;model=' . $model . '&amp;state=' . $state . '&amp;city=' . $city . '&amp;vin='. $vin;
	}

	$generic_vehicle_title = $year . ' ' . $make . ' ' . $model;

?>

<div class="item" id="<?php echo $vin; ?>">
	<div class="photo">
		<a href="<?php echo $inventory_url; ?>" title="<?php echo $generic_vehicle_title; ?>">
			<img src="<?php echo $thumbnail; ?>" alt="<?php echo $generic_vehicle_title; ?>" title="<?php echo $generic_vehicle_title; ?>" />
		</a>
	</div>
	<div class="headline"><a href="<?php echo $inventory_url; ?>" title="<?php echo $headline; ?>"><?php echo $headline; ?>&nbsp;</a></div>
	<div class="left-column">
		<a href="<?php echo $inventory_url; ?>" title="<?php echo $generic_vehicle_title; ?>" class="details">
			<span class="year"><?php echo $year; ?></span>
			<span class="make"><?php echo $make; ?></span>
			<span class="model"><?php echo $model; ?></span>
			<span class="trim"><?php echo $trim; ?></span>
			<span class="doors">[N/A]</span>
			<span class="body-style"><?php echo $body_style; ?></span>
			<span class="engine"><?php echo $engine; ?></span>
			<span class="transmission"><?php echo $transmission; ?></span>
			<span class="exterior-color">Color: <?php echo $exterior_color; ?></span>
			<span class="pricing"><?php echo $asking_price; ?></span>
		</a>
	</div>
	<div class="middle-column">
		<span class="vin">VIN: <?php echo $vin; ?></span>
		<span class="stock-number">Stock Number: <?php echo $stock_number; ?></span>
		<span class="odometer">Odometer: <?php echo $odometer; ?></span>
	</div>
	<div class="right-column">
		<span class="icons"><?php echo $icons; ?></span>
	</div>
	<div class="call-to-action">
		<a href="<?php echo $inventory_url; ?>" title="More Information: <?php echo $generic_vehicle_title; ?>">Click Here for More Details</a>
	</div>
	<br class="clear" />
</div>
<?php flush(); ?>
<?php endforeach;	?>
</div>
