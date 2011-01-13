<?php
/*
 * Plugin Name: DealerTrend API Plugin
 * Version: 1.0.0
 */

# Sanity check.
if ( !class_exists( 'dealertrend_api' ) ) {

  class dealertrend_api {

    # This will store all the WordPress meta information related to this plugin file.
    public $plugin_meta_data = array();

    # Primary Orange API URLs.
    public $orange_api = array(
      'production' => 'http://api.dealertrend.com',
      'beta' => 'http://api.beta.dealertrend.com'
    );

    # Primary ADS (Auto Data Solutions) API URLs
    public $ads_api = array(
      'production' => 'http://ads.dealertrend.com',
      'beta' => 'http://ads.beta.dealertrend.com'
    );

    # Default options:
    # These values are initially set when the plugin is activated on a new site instance.
    # If there are existing options for the site - it will use them instead.
    # TODO: Decide if the plugin should delete any saved settings if the plugin is deactivated.
    # TODO: Determine if their's a hook for "uninstall" or "delete" in relation to a plugin.
    public $options = array(
      'company_information' =>
        array(
          'id' => 0,
          'country' => 'US'
        )
    );

    # PHP 4 Constructor
    function dealertrend_api() {

      # Retrieve information about the plugin - the WordPress way.
      $this->load_plugin_meta_data();

      add_action( 'admin_menu' , array( &$this , 'initialize_admin_hooks' ) );

      # Provide easy acess to the settings page.
      add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ) , array( $this , 'add_plugin_settings_link' ) );

      # Do we have options? If not, set the defaults - otherwise, load the existing options.
      if( !get_option( 'dealertrend_api_options' ) ) {
        $this->notices[ 'admin' ][] = $this->plugin_data[ 'Name' ] . ' ' . $this->plugin_data[ 'Version' ] . ' Has Been Initialized';
        update_option( 'dealertrend_api_options' , $this->options );
      } else {
        $this->load_options();
      }

    } # End PHP 4 Constructor

    # Do we have any active notices our object needs to output?
    function display_admin_notices() {

      foreach( $this->notices[ 'admin' ] as $admin_notice ) {
        echo '<div id="message" class="updated"><p>' . $admin_notice . '</p></div>';
      }

    } # End display_admin_notices()

    # Load meta information related to this plugin file. Using current WordPress methods.
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

    } # End load_plugin_meta_data()

    # Load hooks that are needed for the admin screen.
    function initialize_admin_hooks() {

      add_menu_page( 'Dealertrend API Settings' , 'Dealertrend API' , 'manage_options' , 'dealertrend_api' , array( &$this , 'create_options_page' ) , 'http://wp.s3.dealertrend.com/shared/icon-dealertrend.png' );

    } # End initialize_admin_hooks()

    # Add a shortcut to the settings page.
    function add_plugin_settings_link( $links ) {

      $settings_link = '<a href="admin.php?page=dealertrend_api">Settings</a>'; 
      array_unshift( $links , $settings_link );
      return $links;

    } # End add_plugin_settings_link()

    # Go through the current local object variable and give them to the application for saving. Then load the options back from the application.
    function save_options() {

      update_option( 'dealertrend_api_options' , $this->options );

      $this->notices[ 'admin' ][] = 'Settings Saved';

      $this->load_options();

      add_action( 'admin_notices' , array( &$this , 'display_admin_notices' ) );
      do_action( 'admin_notices' );

    } # End save_options()

    # Load the options from the application into our local object.
    function load_options() {

      foreach( get_option( 'dealertrend_api_options' ) as $option_group => $option_values ) { 

          $this->options[ $option_group ] = $option_values;

      }   

    } # End load_options()

    # Load our options page.
    function create_options_page() {

      include( dirname( __FILE__ ) . '/admin/options.php' );

    } # End create_options_page()

  } # End class definition.

} # Does the class exist?

if ( class_exists( 'dealertrend_api' ) and !isset( $dealertrend_api ) ) {
  $dealertrend_api = new dealertrend_api();
}

?>
