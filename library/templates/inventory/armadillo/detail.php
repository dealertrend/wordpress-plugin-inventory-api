<?php
	# Easy to use variables.
	$sale_class = str_replace( ' ' , '%20' , $inventory->saleclass );
	setlocale(LC_MONETARY, 'en_US');
	$price = money_format( '%(#0n' , $inventory->prices->asking_price );
	$vin = $inventory->vin;
	$odometer = $inventory->odometer;
	$stock = $inventory->stock_number;
	$exterior_color = $inventory->exterior_color;
	$engine = $inventory->engine;
	$transmission = $inventory->transmission;
	$drivetrain = $inventory->drive_train;
	$dealer_options = $inventory->dealer_options;
	$standard_equipment = $inventory->standard_equipment;
	$year = $inventory->year;
	$make = $inventory->make;
	$model = $inventory->model_name;
	$trim = $inventory->trim;
	$year_make_model = $year . ' ' . $make . ' ' . $model;
	$description = $inventory->description;
	$doors = $inventory->doors;

?>
<div id="detail">
	<div id="header">
		<?php echo '<h2>' . $year . ' ' . $make . ' ' . $model . ' ' . $trim . ' ' . $doors . 'D ' . $transmission . '</h2>'; ?>
	</div>
</div><!--#detail -->
