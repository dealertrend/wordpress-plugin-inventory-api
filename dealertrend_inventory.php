<?php
/**
 * Plugin Name: DealerTrend Inventory API
 * Plugin URI: https://github.com/dealertrend/wordpress-plugin-inventory-api
 * Author: DealerTrend, Inc.
 * Author URI: http://www.dealertrend.com
 * Description: Provides access to the Vehicle Management System and Vehicle Reference System provided by <a href="http://www.dealertrend.com" target="_blank" title="DealerTrend, Inc: Shift Everything">DealerTrend, Inc.</a>
 * Version: 3.26.1
 * License: GPLv2 or later
 */

require_once( dirname( __FILE__ ) . '/application/helpers/check_requirements.php' );

$helper = new Dealertrend_Inventory_Api_Requirements();
if( $helper->has_been_checked() === false ) {
	$helper->set_master_file( __FILE__ );
	if( $helper->check_requirements() === false ) {
		return false;
	}
}

require_once( dirname( __FILE__ ) . '/plugin.php' );
$dealertrend_inventory_api = new Wordpress\Plugins\Dealertrend\Inventory\Api\Plugin();
$dealertrend_inventory_api->plugin_slug = plugin_basename(__FILE__);
$dealertrend_inventory_api->execute();

?>
