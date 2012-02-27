<?php
/**
 * Plugin Name: DealerTrend Inventory API
 * Plugin URI: https://github.com/dealertrend/wordpress-plugin-inventory-api
 * Author: DealerTrend, Inc.
 * Author URI: http://www.dealertrend.com
 * Description: Provides access to the Vehicle Management System and Vehicle Reference System provided by <a href="http://www.dealertrend.com" target="_blank" title="DealerTrend, Inc: Shift Everything">DealerTrend, Inc.</a>
 * Version: 3.16.1
 * License: GPLv2 or later

   Copyright (C) 2012  DealerTrend, Inc.

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.

 */

require_once( dirname( __FILE__ ) . '/application/helpers/check_requirements.php' );

$helper = new Dealertrend_Inventory_Api_Requirements();
if( $helper->has_been_checked() === false ) {
	$helper->set_master_file( __FILE__ );
	if( $helper->check_requirements() === false ) {
		return false;
	}
}

include_once( dirname( __FILE__ ) . '/start.php' );

?>
