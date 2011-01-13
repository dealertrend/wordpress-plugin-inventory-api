<?php
/*
 * Plugin Name: DealerTrend API Plugin
 * Version: 1.0.0
 */

if ( !class_exists( 'dealertrend_api') ) {

  class dealertrend_api {

    public $plugin_meta_data = array();

    # Primary Orange API URL.
    public $orange_api = array(
      'production' => 'http://api.dealertrend.com',
      'beta' => 'http://api.beta.dealertrend.com'
    );

    # Primary ADS (Auto Data Solutions) API URL
    public $ads_api = array(
      'production' => 'http://ads.dealertrend.com',
      'beta' => 'http://ads.beta.dealertrend.com'
    );

    # PHP 4 Constructor
    function dealertrend_api() {

      # Retrieve information about the plugin - the WordPress way.
      $this->load_plugin_meta_data();

    }

    function load_plugin_meta_data() {
    
      # Specify what headers we are looking for
      $default_headers = array (
        'Name' => 'Plugin Name',
        'PluginURI' => 'Plugin URI',
        'Version' => 'Version',
        'Description' => 'Description',
        'Author' => 'Author',
      );
  
      # Use those headers and parse our plugins meta data.
      $this->plugin_meta_data = get_file_data( __FILE__ , $default_headers , 'plugin' );
      $this->plugin_meta_data[ 'BaseURL' ] = WP_PLUGIN_URL . '/' . basename( dirname( __FILE__ ) );

    } 

  } # End class definition.

} # Does the class exist?

if ( class_exists( 'dealertrend_api' ) and !isset( $dealertrend_api ) ) {
  $dealertrend_api = new dealertrend_api();
}

?>
