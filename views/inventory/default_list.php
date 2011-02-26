<div id="inventory-listing">
<?php

  foreach( $inventory as $inventory_item ) {
    echo '<div class="row">';
    echo '<div class="cell photo"><img src="' . $inventory_item->photos[ 0 ]->small . '" /></div>';
    echo '<div class="cell headline">' . $inventory_item->headline . '</div>';
    echo '<div class="cell left">';
    echo '<div>';
    echo '<span class="year">' . $inventory_item->year . '</span>';
    echo '<span class="make">' . $inventory_item->make . '</span>';
    echo '<span class="model">' . $inventory_item->model_name . '</span>';
    echo '<span class="trim">' . $inventory_item->trim . '</span>';
    echo '<span class="doors">' . '[DOOR DATA]' . '</span>';
    echo '<span class="body-style">' . $inventory_item->body_style . '</span>';
    echo '</div>';
    echo '<div>';
    echo '<span class="engine">' . $inventory_item->engine . '</span>';
    echo '<span class="transmission"> / ' . $inventory_item->transmission . '</span>';
    echo '</div>';
    echo '<div>';
    echo '<span class="exterior-color">Color: ' . $inventory_item->exterior_color . '</span>';
    echo '</div>';
    echo '<div>';
    echo '<span class="pricing">' . $inventory_item->pricing . '</span>';
    echo '</div>';
    echo '</div><!-- .cell -->';
    echo '<div class="cell middle">';
    echo '<span class="vin">VIN: ' . $inventory_item->vin . '</span>';
    echo '<span class="stock-number">Stock Number: ' . $inventory_item->stock_number . '</span>';
    echo '<span class="odometer">Odometer: ' . $inventory_item->odometer . '</span>';
    echo '</div>';
    echo '<div class="cell right">';
    echo '<span class="icons">' . $inventory_item->icons . '</span>';
    echo '</div>';
    echo '</div><!-- .row -->';
  }

?>
</div>
