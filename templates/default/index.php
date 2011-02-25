<?php
  $type = isset( $inventory->vin ) ? 'detail' : 'list';
  include( dirname( __FILE__ ) . '/' . $type . '.php' );
?>
