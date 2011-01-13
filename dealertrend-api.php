<?php
/*
 * Plugin Name: DealerTrend API Plugin
 * Version: 1.0.0
 */

if ( !class_exists( 'dealertrend_api') ) {

  class dealertrend_api {
  } # End class definition.

} # Does the class exist?

if ( class_exists( 'dealertrend_api' ) and !isset( $dealertrend_api ) ) {
  $dealertrend_api = new dealertrend_api();
}

?>
