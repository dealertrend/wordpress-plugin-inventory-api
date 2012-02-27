<?php

require_once( dirname( __FILE__ ) . '/plugin.php' );
$dealertrend_inventory_api = new Wordpress\Plugins\Dealertrend\Inventory\Api\Plugin();
$dealertrend_inventory_api->execute();

?>
