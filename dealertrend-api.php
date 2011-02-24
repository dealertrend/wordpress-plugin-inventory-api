<?php
/*
 * Plugin Name: DealerTrend API Plugin
 * Description: Access to the Vehicle Management System and Vehicle Refrence System provided by <a href="http://www.dealertrend.com" target="_blank" title="DealerTrend, Inc: Shift Everything">DealerTrend, Inc.</a>
 * Version: 3.0.0
 */

# TODO: Create inventory template for displaying and navigating the inventory.
# TODO: Decide if the plugin should delete any saved settings if the plugin is deactivated.
# TODO: Determine if their's a hook for "uninstall" or "delete" in relation to a plugin.
# TODO: Allow for custom permalink structures.
# TODO: Integrate showcase.

# Sanity check.
if ( !class_exists( 'dealertrend_api' ) ) {

  class dealertrend_api {

    # This will store all the WordPress meta information related to this plugin file.
    public $plugin_meta_data = array();

    # These are various default states for the plugin.
    public $status = array(
      'inventory_json' => false,
      'inventory_json_request' => false,
      'company_information' => false,
      'company_information_request' => false
    );
    
    public $errors = array();

    # Default options:
    # These values are initially set when the plugin is activated on a new site instance.
    # If there are existing options for the site - it will use them instead.
    public $options = array(
      'company_information' =>
        array(
          'id' => 0
        ),
      'api' =>
        array(
          'vehicle_management_system' => NULL,
          'vehicle_reference_system' => NULL
        ),
      'template' => 'default'
    );

    # PHP 4 Constructor
    function dealertrend_api() {

      # Retrieve information about the plugin - the WordPress way.
      $this->load_plugin_meta_data();

      add_action( 'admin_menu' , array( &$this , 'initialize_admin_hooks' ) );
      add_action( 'wp_print_styles' , array( &$this , 'initialize_front_hooks' ) );

      # Provide easy acess to the settings page.
      add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ) , array( $this , 'add_plugin_links' ) );

      # Do we have options? If not, set the defaults - otherwise, load the existing options.
      if( !get_option( 'dealertrend_api_options' ) ) {

        update_option( 'dealertrend_api_options' , $this->options );

        $this->notices[ 'admin' ][] = '<span class="success">Sucess!</span> ' . $this->plugin_meta_data[ 'Name' ] . ' ' . $this->plugin_meta_data[ 'Version' ] . ' Has Been Installed!';
        add_action( 'admin_notices' , array( &$this , 'display_admin_notices' ) );

      } else {

        $this->load_options();

      }

      add_action( 'rewrite_rules_array' , array( &$this , 'add_rewrite_rule' ) , 1 );
      add_action( 'init' , array( &$this , 'create_taxonomy' ) );
      add_action( 'init' , array( &$this , 'flush_rewrite_rules' ) , 1 );
      add_action( 'template_redirect' , array( &$this , 'show_template' ) );

    } # End PHP 4 Constructor

    function create_taxonomy() {

      $labels = array(
        'name' => _x( 'Inventory' , 'taxonomy general name' ),
        'menu_name' => __( 'Inventory' ),
      );

      register_taxonomy(
        'inventory',
        array( 'page' ),
        array(
          'hierarchical' => true,
          'labels' => $labels,
          'show_ui' => false,
          'query_var' => true,
          'rewrite' => array( 'slug' => 'inventory' ),
        )
      );

    } # End create_taxonomy()

    function flush_rewrite_rules() {

      global $wp_rewrite;

      return $wp_rewrite->flush_rules();

    } # End flush_rewrite_rules()

    function add_rewrite_rule( $existing_rules ) {

      $new_rule = array();

      $new_rule[ '^(inventory)' ] = 'index.php?taxonomy=inventory';

      return $new_rule + $existing_rules;

    } # End add_rewrite_rule()

    function show_template() {

      global $wp_query;

      $taxonomy = ( isset( $wp_query->query_vars[ 'taxonomy' ] ) ) ? $wp_query->query_vars[ 'taxonomy' ] : NULL;

      if( $taxonomy == 'inventory' ) {

        get_header();

        $this->get_company_information();
        $inventory = $this->get_inventory();

        $permalink_parameters = !empty( $wp_rewrite->permalink_structure ) ? explode( '/' , $_SERVER[ 'REQUEST_URI' ] ) : array();
        $server_parameters = isset( $_GET ) ? $_GET : NULL;

        array_shift( $permalink_parameters );
        array_pop( $permalink_parameters );

        # Defaults:
        #
        # /inventory/
        #
        # /inventory/(used|new|all)
        # /inventory/(used|new|all)/
        # /inventory/(used|new|all)/classification/
        # /inventory/(used|new|all)/classification/make/
        # /inventory/(used|new|all)/classification/make/model/
        # /inventory/(used|new|all)/classification/make/model/city/
        # /inventory/(used|new|all)/classification/make/model/city/state/
        #
        # /inventory/####/
        # /inventory/####/make/
        # /inventory/####/make/model/
        # /inventory/####/make/model/city/
        # /inventory/####/make/model/city/state/

        # /inventory/####/make/model/vin/
        # /inventory/####/make/model/vin/city/
        # /inventory/####/make/model/vin/city/state/
        #
        # Questions:
        #   -> VIN format?
        #   -> Showcase format?

        # Post Meeting:
        # 
        # /inventory/(used|new|all|####)/
        # /inventory/(used|new|all|####)/make/
        # /inventory/(used|new|all|####)/make/model/
        # /inventory/(used|new|all|####)/make/model/state/
        # /inventory/(used|new|all|####)/make/model/state/city/
        # /inventory/(used|new|all|####)/make/model/state/city/vin/

        $this->display_inventory( $inventory );

        get_footer();

        exit;

      }

    } # End get_parameters()

    # Do we have any active notices our object needs to output?
    function display_admin_notices() {

      if( !is_admin() )
        return false;

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

      # Add a shortcut on the plugin management page, so that people can quickly get to the settings page of our plugin.
      add_menu_page( 'Dealertrend API Settings' , 'Dealertrend API' , 'manage_options' , 'dealertrend_api' , array( &$this , 'create_options_page' ) , 'http://wp.s3.dealertrend.com/shared/icon-dealertrend.png' );
      
      # Load up the CSS for the adminstration screen.
      wp_register_style( 'dealertrend_api_admin' , $this->plugin_meta_data[ 'BaseURL' ] . '/css/admin.css' );
      wp_enqueue_style( 'dealertrend_api_admin' );

    } # End initialize_admin_hooks()

    function initialize_front_hooks() {

      $template_name = $this->options[ 'template' ];
      wp_register_style( 'dealertrend_api_inventory' , $this->plugin_meta_data[ 'BaseURL' ] . '/templates/'. $template_name .'/style.css' );
      wp_enqueue_style( 'dealertrend_api_inventory' );

    } # End initialize_front_hooks()

    # Add a shortcut to the settings page and the readme file.
    function add_plugin_links( $links ) {

      $settings_link = '<a href="admin.php?page=dealertrend_api">Settings</a>'; 
      $readme_link = '<a href="' . $this->plugin_meta_data[ 'BaseURL' ] . '/readme.html">Documentation</a>'; 

      array_unshift( $links , $settings_link );
      array_unshift( $links , $readme_link );

      return $links;

    } # End add_plugin_settings_link()

    # Go through the current local object variable and give them to the application for saving. Then load the options back from the application.
    function save_options() {

      update_option( 'dealertrend_api_options' , $this->options );

      $this->notices[ 'admin' ][] = 'Settings Saved';

      $this->load_options();

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

    function get_remote_file( $location , $option_key = NULL ) {

      $data = NULL;

      $response = wp_remote_get( $location );

      if( is_wp_error( $response ) ) {

        $this->errors[ $option_key] = $response->errors;
        $this->status[ $option_key ] = false;

      } else {

        # We accessed the API.
        $this->status[ $option_key ] = true;

        if( !isset( $response ) )
          return false;

        # The API isn't happy with our parameters.
        if( $response[ 'headers' ][ 'status' ] != '200 OK' )
          return false;

        $data = ( trim( $response[ 'body' ] ) != '[]' ) ? $response[ 'body' ] : false;

      }

      return $data;

    } # End get_remote_file()

    function get_inventory( $parameters = array() ) {

      $parameters['photo_view'] = isset( $parameters['photo_view'] ) ? $parameters['photo_view'] : '0';

      $parameter_string = http_build_query( $parameters , '' , ';' );

      # Don't continue if we don't have the required company information.
      if( !$this->status[ 'company_information' ] )
        return false;

      # Check to see if the data is cached.
      $data_array = wp_cache_get( 'inventory_json', 'dealertrend_api' );

      # If it's not cached, then let's pull a new one from Orange.
      if ( $data_array == false ) {

        # Get the file, store it's status in the given option key.
        $data_json = $this->get_remote_file(
          $this->options[ 'api' ][ 'vehicle_management_system' ] . '/' . $this->options[ 'company_information' ][ 'id' ] . '/inventory/vehicles.json?' . $parameter_string,
          'inventory_json_request'
        );

        # If we get a 200 back AND it's not empty.
        if( $this->status[ 'inventory_json_request' ] && $data_json ) {
          $data_array = json_decode( $data_json );
          wp_cache_add( 'inventory_json' , $data_array , 'dealertrend_api' , 0 );
          $this->status[ 'inventory_json' ] = true;
        }

      }   

      return $data_array;

    } # End get_inventory()

    function get_company_information() {

      # Don't continue if we don't have the required API information.
      if( !$this->options[ 'api' ][ 'vehicle_management_system' ] )
        return false;

      # Check to see if the data is cached.
      $data_array = wp_cache_get( 'company_information', 'dealertrend_api' );

      # If it's not cached, then let's pull a new one from Orange.
      if ( $data_array == false ) {

        # Get the file, store it's status in the given option key.
        $data_json = $this->get_remote_file(
          $this->options[ 'api' ][ 'vehicle_management_system' ] . '/api/companies/' . $this->options[ 'company_information' ][ 'id' ],
          'company_information_request'
        );

        # If the result is false, then we were unable to retreive the file.
        if( !$this->status[ 'company_information_request' ] ) {
          $this->notices[ 'admin' ][] = '<span class="warning">Warning!</span> <strong>Unable to connect to provided address:</strong> ' . $this->errors[ 'company_information_request' ][ 'http_request_failed' ][ 0 ];
          $this->display_admin_notices();
        }

        if( $this->status[ 'company_information_request' ] && $data_json ) {
          $data_array = json_decode( $data_json );
          wp_cache_add( 'company_information' , $data_array , 'dealertrend_api' , 0 );
          $this->status[ 'company_information' ] = true;
        }

      }

      return $data_array;

    } # End get_company_information()

    function get_template( $template_name ) {


    } # End get_templates()

    function display_inventory( $inventory ) {

      global $wp_rewrite;

      $template_base_path = dirname( __FILE__ ) . '/templates';
      $template_name = $this->options[ 'template' ];

      if( $handle = opendir( $template_base_path ) ) {
 
        while( false !== ( $file = readdir( $handle ) ) ) {
          if( $file == $template_name ) {
            include $template_base_path . '/' . $template_name . '/index.php';
          }
        }

        closedir( $handle );
 
      }

    }

  } # End class definition.

} # Does the class exist?

if ( class_exists( 'dealertrend_api' ) and !isset( $dealertrend_api ) ) {
  $dealertrend_api = new dealertrend_api();
}

?>
