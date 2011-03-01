<?php

  # TODO: Pagination
  # TODO: AIS Rebates
  # TODO: Quick Links
  # TODO: Detail Pages
  # TODO: Return to Top links
  # TODO: jQuery UI? Click on inventory image and view large in light box.
  # TODO: Get Doors variable from Orange.
  # TODO: Anchors
  # TODO: Breadcrumbs

  $company_information = wp_cache_get( 'company_information', 'dealertrend_api' );
  $state = $company_information->state;
  $city = $company_information->city;

?>

<div class="inventory-table" id="inventory-listing">

<div>
  <?php echo "Results: " . count($inventory); ?>
</div>

<?php foreach( $inventory as $inventory_item ): ?>

<?php
  $year = $inventory_item->year;
  $make = $inventory_item->make;
  $model = $inventory_item->model_name;
  $vin = $inventory_item->vin;
  $trim = $inventory_item->trim;
  $body_style = $inventory_item->body_style;
  $engine = $inventory_item->engine;
  $transmission = $inventory_item->transmission;
  $exterior_color = $inventory_item->exterior_color;
  $pricing = $inventory_item->pricing;
  $stock_number = $inventory_item->stock_number;
  $odometer = $inventory_item->odometer;
  $icons = $inventory_item->icons;
  $headline = $inventory_item->headline;
  $thumbnail = $inventory_item->photos[ 0 ]->small;
  
  $inventory_url = $year . '/' . $make . '/' . $model . '/' . $state . '/' . $city  . '/'. $vin . '/';

  $generic_vehicle_title = $year . ' ' . $make . ' ' . $model;

?>

<div class="item">
  <div class="photo">
    <a href="<?php echo $inventory_url; ?>" title="<?php echo $generic_vehicle_title; ?>">
      <img src="<?php echo $thumbnail; ?>" alt="<?php echo $generic_vehicle_title; ?>" title="<?php echo $generic_vehicle_title; ?>" />
    </a>
  </div>
  <div class="headline"><a href="<?php echo $inventory_url; ?>" title="<?php echo $headline; ?>"><?php echo $headline; ?></a></div>
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
      <span class="pricing"><?php echo $pricing; ?></span>
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
</div>

<?php endforeach;  ?>
</div>
