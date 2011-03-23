<?php
/*
 * Plugin Name: DealerTrend API Plugin
 * Description: Access to the Vehicle Management System and Vehicle Refrence System provided by <a href="http://www.dealertrend.com" target="_blank" title="DealerTrend, Inc: Shift Everything">DealerTrend, Inc.</a>
 * Version: 3.0.0
 */

# TODO: Allow for custom permalink structures.
# TODO: Integrate showcase.
# TODO: AIS Rebates

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
		public $report = array();
		public $parameters = array();
		public $notices = array();
	
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

			$this->report[ 'inventory_download_time' ] = 0;
			$this->report[ 'company_information_download_time' ] = 0;

      add_action( 'admin_menu' , array( &$this , 'admin_styles' ) );
      add_action( 'admin_menu' , array( &$this , 'admin_scripts' ) );

      # Provide easy acess to the settings page.
      add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ) , array( $this , 'add_plugin_links' ) );

      # Do we have options? If not, set the defaults - otherwise, load the existing options.
      if( !get_option( 'dealertrend_api_options' ) ) {
      	update_option( 'dealertrend_api_options' , $this->options );
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
      # Root: /inventory/
      # Sale Class / Year: /inventory/(used|new|all|####)/
      # Make: /inventory/(used|new|all|####)/make/
      # Model: /inventory/(used|new|all|####)/make/model/
      # State: /inventory/(used|new|all|####)/make/model/state/
      # City: /inventory/(used|new|all|####)/make/model/state/city/
      # VIN: /inventory/(used|new|all|####)/make/model/state/city/vin/

      return $new_rule + $existing_rules;

    } # End add_rewrite_rule()

    function show_template() {

      global $wp;
      global $wp_query;
      global $wp_rewrite;

      $taxonomy = ( isset( $wp_query->query_vars[ 'taxonomy' ] ) ) ? $wp_query->query_vars[ 'taxonomy' ] : NULL;

      if( $taxonomy == 'inventory' ) {

	      add_action( 'wp_print_styles' , array( &$this , 'front_styles' ) );
				add_action( 'wp_print_scripts', array( &$this , 'front_scripts' ) );

				flush();
        get_header();
				flush();

        $this->get_company_information();

        $permalink_parameters = !empty( $wp_rewrite->permalink_structure ) ? explode( '/' , $wp->request ) : array();

        $server_parameters = isset( $_GET ) ? $_GET : NULL;

        $parameters = array();

        foreach( $permalink_parameters as $key => $value ) {

          switch( $key ) {
            case 0: $index = 'taxonomy'; break;
            case 1:
              if( is_numeric( $value ) ) {
                $index = 'year'; 
              } else {
                $index = 'saleclass';
              }
            break;
            case 2: $index = 'make'; break;
            case 3: $index = 'model'; break;
            case 4: $index = 'state'; break;
            case 5: $index = 'city'; break;
            case 6: $index = 'vin'; break;
          }

          $parameters[ $index ] = $value;

        }

        # Year hack.
        # Because we have a parameter called "year" and WordPress tries to use it to pull post archives.
        if( isset( $server_parameters[ 'vehicle_year' ] ) ) {
          $server_parameters[ 'year' ] = $server_parameters[ 'vehicle_year' ];
          unset( $server_parameters[ 'vehicle_year' ] );
        }

        $this->parameters = array_merge( $parameters , $server_parameters );

        $inventory = $this->get_inventory( $this->parameters );
				
				$start_inventory_display_timer = timer_stop();
        $this->display_inventory( $inventory );
				$stop_inventory_display_timer = timer_stop();
				$this->report[ 'inventory_display_time' ] = $stop_inventory_display_timer - $start_inventory_display_timer; + $this->report[ 'inventory_download_time' ];

				#echo "<pre>";
				#print_r($this);
				#print_r($inventory);
				#print_r( wp_cache_get( $this->options[ 'api' ][ 'vehicle_management_system' ] . '/api/companies/' . $this->options[ 'company_information' ][ 'id' ] , 'dealertrend_api' ) );
				#echo "</pre>";

				flush();
        get_footer();
				flush();

				error_log( 'DealerTrend API Report: ' . print_r( $this->report , true ) , 3 , dirname( __FILE__ ) . '/reporting.log' );

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
  
			$plugin_file = pathinfo(__FILE__);

      # Use those headers and parse our plugins meta data.
      $this->plugin_meta_data = get_file_data( __FILE__ , $default_headers , 'plugin' );
      $this->plugin_meta_data[ 'BaseURL' ] = WP_PLUGIN_URL . '/' . basename( $plugin_file[ 'dirname' ] );
			$this->plugin_meta_data[ 'UninstallPath' ] = basename( $plugin_file[ 'dirname' ] ) . '/' . $plugin_file[ 'basename' ];

    } # End load_plugin_meta_data()

    # Load hooks that are needed for the admin screen.
    function admin_styles() {

      # Add a shortcut on the plugin management page, so that people can quickly get to the settings page of our plugin.
      add_menu_page( 'Dealertrend API Settings' , 'Dealertrend API' , 'manage_options' , 'dealertrend_api' , array( &$this , 'create_options_page' ) , 'http://wp.s3.dealertrend.com/shared/icon-dealertrend.png' );
      
      # Load up the CSS for the adminstration screen.
      wp_register_style( 'dealertrend-api-admin' , $this->plugin_meta_data[ 'BaseURL' ] . '/library/wp-admin/css/dealertrend-api-options.css' , false , $this->plugin_meta_data[ 'Version' ] );
      wp_enqueue_style( 'dealertrend-api-admin' );

			wp_register_Style( 'jquery-ui-black-tie' , 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/black-tie/jquery-ui.css', false , '1.8.1' );
			wp_enqueue_style( 'jquery-ui-black-tie' );

    } # End admin_styles()

		function admin_scripts() {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-tabs' );
			wp_enqueue_script( 'jquery-ui-dialog' );
			wp_enqueue_script( 'dealertrend-api-options', $this->plugin_meta_data[ 'BaseURL' ] . '/library/wp-admin/js/dealertrend-api-admin-init.js' , array( 'jquery' , 'jquery-ui-core' , 'jquery-ui-tabs' , 'jquery-ui-dialog' ) , $this->plugin_meta_data[ 'Version' ] , true );
		} # End admin_scripts()

    function front_styles() {

      $template_name = $this->options[ 'template' ];
      wp_register_style( 'dealertrend-api-inventory' , $this->plugin_meta_data[ 'BaseURL' ] . '/library/templates/inventory/' . $template_name . '/style.css' , false , $this->plugin_meta_data[ 'Version' ] );
      wp_enqueue_style( 'dealertrend-api-inventory' );

			wp_register_Style( 'jquery-ui-black-tie' , 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/black-tie/jquery-ui.css', false , '1.8.1' );
			wp_enqueue_style( 'jquery-ui-black-tie' );

    } # End front_styles()

		function front_scripts() {

			# We'll be using jQuery quite a bit hopefully.
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-tabs' );
			wp_enqueue_script( 'jquery-cycle', 'http://cloud.github.com/downloads/malsup/cycle/jquery.cycle.all.2.72.js' , array( 'jquery' ) , '2.72' , true );
			wp_enqueue_script( 'dealertrend-api-inventory', $this->plugin_meta_data[ 'BaseURL' ] . '/library/templates/inventory/js/dealertrend-api-init.js' , array( 'jquery' , 'jquery-ui-core' , 'jquery-ui-tabs' , 'jquery-cycle' ) , $this->plugin_meta_data[ 'Version' ] , true );

		} # End front_scripts()

    # Add a shortcut to the settings page and the readme file.
    function add_plugin_links( $links ) {

      $settings_link = '<a href="admin.php?page=dealertrend_api#settings">Settings</a>'; 
      $readme_link = '<a href="admin.php?page=dealertrend_api#help">Documentation</a>'; 

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

      include( dirname( __FILE__ ) . '/library/wp-admin/options.php' );

    } # End create_options_page()

		# Just include the subdomain, domain and tld.
		# subdomain.mydomain.com
    function get_remote_file( $location , $option_key = NULL ) {

      $data = NULL;

      $response = wp_remote_get( 'http://' . $location , array( 'timeout' => 10 ) );

      if( is_wp_error( $response ) ) {

        $this->errors[ $option_key ] = $response->errors;
        $error_string = $response->errors[ 'http_request_failed' ][ 0 ];
        error_log( get_bloginfo( 'url' ) . ': WARNING: ' . $error_string, 0 );
        error_log( get_bloginfo( 'url' ) . ': REQUEST: ' . $location , 0 );
        $this->status[ $option_key ] = false;
				return false;

      } else {

        if( !isset( $response ) )
          return false;

				$status_header = isset( $response[ 'headers' ][ 'status' ] ) ? $response[ 'headers' ][ 'status' ] : false;

        # The API isn't happy with our parameters or we were given a bad URL.
				if( $status_header == '404 Not Found' ) {
					$this->errors[ $option_key ][ 'http_request_failed' ][ 0 ] = '404 Not Found';
					return false;
				}

        if( $status_header != '200 OK' )
          return false;

        # We accessed the API.
        $this->status[ $option_key ] = true;

        $data = ( trim( $response[ 'body' ] ) != '[]' ) ? $response[ 'body' ] : false;

      	return $data;

      }

    } # End get_remote_file()

    function get_inventory( $parameters = array() ) {

			$start_inventory_timer = timer_stop();

      if( !isset( $parameters['vin'] )  ) {
        $parameters['photo_view'] = isset( $parameters['photo_view'] ) ? $parameters['photo_view'] : '1';
      }

      $parameter_string = http_build_query( $parameters , '' , '&' );

      # Don't continue if we don't have the required company information.
      if( !$this->status[ 'company_information' ] )
        return false;

      # Check to see if the data is cached.
      $data_array = wp_cache_get( $this->options[ 'api' ][ 'vehicle_management_system' ] . '/' . $this->options[ 'company_information' ][ 'id' ] . '/inventory/vehicles.json?' . $parameter_string , 'dealertrend_api' );

      # If it's not cached, then let's pull a new one from Orange.
      if ( $data_array == false ) {
				$this->report[ 'inventory_cached' ] = false;

        # Get the file, store it's status in the given option key.
        $data_json = $this->get_remote_file(
          $this->options[ 'api' ][ 'vehicle_management_system' ] . '/' . $this->options[ 'company_information' ][ 'id' ] . '/inventory/vehicles.json?' . $parameter_string,
          'inventory_json_request'
        );

        # If we get a 200 back AND it's not empty.
        if( $this->status[ 'inventory_json_request' ] && $data_json ) {
          $data_array = json_decode( $data_json );
					# Give the cache name a useful name. Something that shows the company and the parameters as well as what system it's pulling from.
          wp_cache_add( $this->options[ 'api' ][ 'vehicle_management_system' ] . '/' . $this->options[ 'company_information' ][ 'id' ] . '/inventory/vehicles.json?' . $parameter_string , $data_array , 'dealertrend_api' , 0 );
          $this->status[ 'inventory_json' ] = true;
        }

      } else {
				$this->report[ 'inventory_cached' ] = true;
				$this->status[ 'inventory_json' ] = true;
			}   

			$stop_inventory_timer = timer_stop();
			$this->report[ 'inventory_download_time' ] = $stop_inventory_timer - $start_inventory_timer;

      return $data_array;

    } # End get_inventory()

    function get_company_information() {

			$start_company_information_timer = timer_stop();

      # Don't continue if we don't have the required API information.
      if( !$this->options[ 'api' ][ 'vehicle_management_system' ] )
        return false;

      # Check to see if the data is cached.
      $data_array = wp_cache_get( $this->options[ 'api' ][ 'vehicle_management_system' ] . '/api/companies/' . $this->options[ 'company_information' ][ 'id' ] , 'dealertrend_api' );

      # If it's not cached, then let's pull a new one from Orange.
      if ( $data_array == false ) {
				$this->report[ 'company_information_cached' ] = false;

        # Get the file, store it's status in the given option key.
        $data_json = $this->get_remote_file(
          $this->options[ 'api' ][ 'vehicle_management_system' ] . '/api/companies/' . $this->options[ 'company_information' ][ 'id' ],
          'company_information_request'
        );

        # If the result is false, then we were unable to retreive the file.
        if( !$this->status[ 'company_information_request' ] ) {
          $this->notices[ 'admin' ][] = '<span class="warning">Warning!</span> <strong>Unable to retrieve company information:</strong> ' . $this->errors[ 'company_information_request' ][ 'http_request_failed' ][ 0 ];
        }

        if( $this->status[ 'company_information_request' ] && $data_json ) {
          $data_array = json_decode( $data_json );
					# Give the cache name a useful name. Something that shows the company and the parameters as well as what system it's pulling from.
          wp_cache_add( $this->options[ 'api' ][ 'vehicle_management_system' ] . '/api/companies/' . $this->options[ 'company_information' ][ 'id' ] , $data_array , 'dealertrend_api' , 0 );
          $this->status[ 'company_information' ] = true;
        }

      } else {
					# We have cached data.
          $this->status[ 'company_information' ] = true;
					$this->report[ 'company_information_cached' ] = true;
			}

			$stop_company_information_timer = timer_stop();

			$this->report[ 'company_information_download_time' ] = $stop_company_information_timer - $start_company_information_timer;

      return $data_array;

    } # End get_company_information()

    function display_inventory( $inventory ) {

      global $wp_rewrite;

      $template_name = $this->options[ 'template' ];
      $template_base_path = dirname( __FILE__ ) . '/library/templates/inventory/' . $template_name;

			$start_template_timer = timer_stop();

      if( $handle = opendir( $template_base_path ) ) {
 
        while( false !== ( $file = readdir( $handle ) ) ) {
          if( $file == 'index.php' ) {
            include( $template_base_path . '/index.php' );
          }
        }

        closedir( $handle );
 
      }

			$stop_template_timer = timer_stop();

			$this->report[ 'template_render_time' ] = $stop_template_timer - $start_template_timer;

    }

  } # End class definition.

} # Does the class exist?

if ( class_exists( 'dealertrend_api' ) and !isset( $dealertrend_api ) ) {
  $dealertrend_api = new dealertrend_api();
}

?>
