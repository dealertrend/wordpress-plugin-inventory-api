<table style="width:100%; margin:20px; padding:10px;">
<?php

  foreach( $inventory as $inventory_item ) {
    echo '<tr>';
    echo '<td>' . $inventory_item->year . ' ' . $inventory_item->make . ' ' . $inventory_item->model_name . '</td>';
    echo '<td> VIN: ' . $inventory_item->vin . '</td>';
    echo '</tr>';
  }

#print_r( $inventory );

?>
</table>
