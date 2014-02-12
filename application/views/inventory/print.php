<?php

	$vehicle_management_system->tracer = 'Obtaining requested inventory print page';
	$inventory_information = $vehicle_management_system->get_inventory()->please( array_merge( $this->parameters , array( 'photo_view' => 1 ) ) );
	$inventory = isset( $inventory_information[ 'body' ] ) ? json_decode( $inventory_information[ 'body' ] ) : false;

	$company_information = json_decode( $company_information[ 'body' ] );

	$type = isset( $inventory->vin ) ? 'detail' : 'list';

	$vehicle = itemize_vehicle($inventory);

	if ( $type == 'detail' ) {
?>
	<style>
		#print-wrapper{
			display: block;
			overflow: hidden;
			clear: both;
			float: none;
			max-width: 500px;
		}
		.print-header{
			display: block;
			overflow: hidden;
			clear: both;
			float: none;
			width: 100%;
			text-align: center;
		}
		.print-header-name, .print-header-phone{
			display: block;
			float: left;
			width: 50%;
		}

		.print-vehicle-information{
			width: 60%;
			margin: 5% auto;
		}
		.print-vehicle-information div{
			border-bottom: 2px solid #000;
		}
		.print-vehicle-information span{
		   margin: 0 5% 0 0;
		}
		.print-image{
			text-align: center;
			margin: 5% 0;
		}
		.print-price{
			text-align: center;
		}
		.print-description{
			font-size: 12px;
			text-align: center;
			margin: 0 0 5%;
		}
		.print-disclaimer{
			font-size: 10px;
			text-align: center;
			display: block;
			width: 100%;
		}
	</style>

	<div id="print-wrapper">
		<div id="detail-print">
			<div class="print-header">
				<div class="print-headline">
					<h2><?php echo $vehicle['year'] . ' ' . $vehicle['make']['clean'] . ' ' . $vehicle['model']['clean'] . ' ' . $vehicle['trim']['clean']; ?></h2>
				</div>
				<div class="print-header-name"><?php echo $vehicle['contact_info']['dealer'] ?></div>
				<div class="print-header-phone"><?php echo $vehicle['contact_info']['phone'] ?></div>
			</div>
			<div class="print-top">
				<?php if( count( $vehicle['photos'] ) ){ ?>
				<div class="print-image">
				<?php
					foreach( array_slice($vehicle['photos'],0,1) as $photo ) {
						echo '<img src="' . str_replace( '&' , '&amp;' , $photo->medium ) . '" width="300" alt="" title="' . $vehicle['contact_info']['dealer'] . '" />';
					}
				}
				?>
				</div>

				<div class="print-price">
					<?php $price = get_price_display($vehicle['prices'], $company_information, $vehicle['vin'], 'price' );
						echo
							( !empty($price['msrp_text']) && strtolower($vehicle['saleclass']) == 'new' ? $price['msrp_text'] : '') . '
							'.$price['primary_text'].$price['ais_text'].$price['compare_text'].$price['expire_text']
						;
					?>
				</div>

				<div class="print-vehicle-information">
					<div><span>Stock Number:</span> <?php echo $vehicle['stock_number']; ?></div>
					<div><span>VIN:</span> <?php echo $vehicle['vin']; ?></div>
					<div><span>Odometer:</span> <?php echo $vehicle['odometer']; ?></div>
					<div><span>Exterior:</span> <?php echo $vehicle['exterior_color']; ?></div>
					<div><span>Interior:</span> <?php echo $vehicle['interior_color']; ?></div>
					<div><span>Engine:</span> <?php echo $vehicle['engine']; ?></div>
					<div><span>Transmission:</span> <?php echo $vehicle['transmission']; ?></div>
				</div>
			</div>
			<div class="print-bottom">
				<?php if( $vehicle['description'] ){ ?>
					<div class="print-description">
						<?php echo $vehicle['description'];?>
					</div>
				<?php } ?>

				<div class="print-disclaimer">
					<?php echo $inventory->disclaimer; ?>
				</div>
			</div>

		</div>
	</div>

<?php

	} else {

		get_header();
		echo '<h2>Not able to display vehicle print page.</h2>';
		get_footer();

	}

?>
