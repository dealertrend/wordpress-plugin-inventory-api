<?php
/**
 * Plugin Name: DealerTrend Inventory API
 * Plugin URI: https://github.com/dealertrend/wordpress-plugin-inventory-api
 * Author: DealerTrend, Inc.
 * Author URI: http://www.dealertrend.com
 * Description: Provides access to the Vehicle Management System provided by <a href="http://www.dealertrend.com" target="_blank" title="DealerTrend, Inc: Shift Everything">DealerTrend, Inc.</a>
 * Version: 3.1.3
 */

if ( !class_exists( 'dealertrend_inventory_api' ) ) {
	return false;
}

/** Load the helpers so we can interface with the APIs. */
require_once( dirname( __FILE__ ) . '/app/helpers/http_api_wrapper.php' );
require_once( dirname( __FILE__ ) . '/app/helpers/vehicle_management_system.php' );
require_once( dirname( __FILE__ ) . '/app/helpers/vehicle_reference_system.php' );
require_once( dirname( __FILE__ ) . '/app/helpers/inventory_seo_headers.php' );
require_once( dirname( __FILE__ ) . '/app/helpers/dealertrend_plugin_updater.php' );

/** Widgets */
require_once( dirname( __FILE__ ) . '/app/views/widgets/vms.php' );
require_once( dirname( __FILE__ ) . '/app/views/widgets/vrs.php' );

/**
 * This is the primary class for the plugin.
 *
 * It uses standard WordPress hooks and helpers for the different APIs it interfaces with.
 * It also utilizes several custom helpers to incorporate the API itself and some extended functionality of the WordPress core.
 *
 * @since 3.0.0
 */
class dealertrend_inventory_api {

	/**
	 * Public plugin information derived from the file header, as well as some custom keys.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var array
	 */
	public $meta_information = array();

	/**
	 * Default options. These values are initially set when the plugin is creates a new instance.
	 *
	 * This is also the array that ends up storing changed variables wich are later saved.
	 *
	 * @since 3.0.0
	 * @access private
	 * @var array
	 */
	public $options = array(
		'vehicle_management_system' => array(
			'company_information' => array(
				'id' => 0
			),
			'host' => NULL,
			'theme' => array(
				'name' => 'armadillo',
				'per_page' => 10
			)
		),
		'vehicle_reference_system' => array(
			'host' => NULL
		)
	);

	/**
	 * Public variable for all the parameters the plugin is currently working with.
	 *
	 * @since 3.0.0
	 * @access public
	 * @var array
	 */
	public $parameters = array();

	/**
	 * Sets up object properties and ties into the WordPress procedural hooks. PHP 5 style constructor.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	function __construct() {
		$this->meta_information = $this->get_meta_information();
		# Need to call the updater after the required objects have been instantiated.
		add_action( 'admin_init' , array( &$this , 'updater' ) );
		$this->load_options();
		$this->load_widgets();
		# Only load the admin CSS/JS on the admin screen.
		add_action( 'admin_menu' , array( &$this , 'admin_styles' ) );
		add_action( 'admin_menu' , array( &$this , 'admin_scripts' ) );
		# Dealing with the rewrite object: {@link http://codex.wordpress.org/Function_Reference/WP_Rewrite}
		add_action( 'rewrite_rules_array' , array( &$this , 'add_rewrite_rules' ) , 1 );
		add_action( 'init' , array( &$this , 'flush_rewrite_rules' ) , 1 );
		add_action( 'init' , array( &$this , 'create_taxonomy' ) );
		add_action( 'template_redirect' , array( &$this , 'show_inventory_theme' ) );
	}

	/**
	 * Uses a helper to check for plugin updates. This looks up tags via GitHub and then if a new version is avilable, allows us to do an auto-install.
	 *
	 * @since 3.0.1
	 * @return void;
	 */
	function updater() {
		$updater = new dealetrend_plugin_updater( $this->meta_information );
		$version_check = $updater->check_for_updates();
		$updater->display_update_notice( $version_check );
	}

	/**
	 * Retreives standard WordPress file headers from this plugin's header.
	 *
	 * Adds some custom keys that are plugin related data that are used frequently.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	function get_meta_information() {
		$data = array();

		$file_headers = array (
			'Name' => 'Plugin Name',
			'PluginURI' => 'Plugin URI',
			'Version' => 'Version',
			'Description' => 'Description',
			'Author' => 'Author',
			'AuthorURI' => 'Author URI'
		);

		# The WordPress way of getting file headers: {@link http://phpdoc.wordpress.org/trunk/WordPress/_wp-includes---functions.php.html#functionget_file_data)
		$data = get_file_data( __FILE__ , $file_headers , 'plugin' );

		$plugin_file = pathinfo( __FILE__ );

		$data[ 'PluginURL' ] = WP_PLUGIN_URL . '/' . basename( $plugin_file[ 'dirname' ] );
		$data[ 'PluginBaseName' ] = plugin_basename( __FILE__ );

		return $data;
	}

	/**
	 * Load the plugins options from the database into the current scope.
	 *
	 * If there are no settings, it will instantiate them with the plugin defaults.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	function load_options() {
		if( !get_option( 'dealertrend_inventory_api' ) ) {
			update_option( 'dealertrend_inventory_api' , $this->options );
		} else {
			foreach( get_option( 'dealertrend_inventory_api' ) as $option_group => $option_values ) {
				$this->options[ $option_group ] = $option_values;
			}
		}
	}

	function load_widgets() {
		if( $this->options[ 'vehicle_management_system' ][ 'host' ] && $this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] ) {
			add_action( 'widgets_init' , create_function( '' , 'return register_widget("VehicleManagementSystemWidget");' ) );
		}

		if( $this->options[ 'vehicle_reference_system' ][ 'host' ] ) {
			add_action( 'widgets_init' , create_function( '' , 'return register_widget("VehicleReferenceSystemWidget");' ) );
		}

#		$check_host = $vehicle_reference_system->check_host();
#		if( $check_host[ 'status' ] == false ) {
#			echo '<p>Unable to connect to API.</p>';
#			return false;
#		}
	}

	/**
	 * Saves the current options to the database.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	function save_options() {
		update_option( 'dealertrend_inventory_api' , $this->options );
		$this->load_options();
	}

	/**
	 * A persistent implementation of a funciton to add a  few access points to the plugin's options page.
	 *
	 * It also checks to see if we are on the options page for the plugin, if so, load the CSS for it.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	function admin_styles() {
		# Provide easy acess to the settings page by adding links to our plugin's entry in the plugin management page.
		add_filter( 'plugin_action_links_' . $this->meta_information[ 'PluginBaseName' ] , array( $this , 'add_plugin_links' ) );
		# Add a new menu item in the WordPress admin menu so people can get to the plugin settings from the sidebar.
		add_menu_page(
			'Dealertrend API',
			'Dealertrend API',
			'manage_options',
			'dealertrend_inventory_api',
			array( &$this , 'create_options_page' ),
			'http://wp.s3.dealertrend.com/shared/icon-dealertrend.png'
		);

		if( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'dealertrend_inventory_api' ) {
			# Load up the CSS for the adminstration screen.
			wp_register_style( 'dealertrend-inventory-api-admin' , $this->meta_information[ 'PluginURL' ] . '/app/views/wp-admin/css/dealertrend-inventory-api.css' , false , $this->meta_information[ 'Version' ] );
			wp_enqueue_style( 'dealertrend-inventory-api-admin' );
			wp_register_Style( 'jquery-ui-black-tie' , 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/black-tie/jquery-ui.css' , false , '1.8.1' );
			wp_enqueue_style( 'jquery-ui-black-tie' );
		}

	}

	/**
	 * Add shortcut links to the settings page for our plugin.
	 *
	 * @since 3.0.0
	 * @return array The array of links to be added in the plugin management page.
	 */
	function add_plugin_links( $links ) {
		$settings_link = '<a href="admin.php?page=dealertrend_inventory_api#settings">Settings</a>';
		$readme_link = '<a href="admin.php?page=dealertrend_inventory_api#help">Documentation</a>';
		array_unshift( $links , $settings_link );
		array_unshift( $links , $readme_link );

		return $links;
	}

	/**
	 * Load the plugin options page.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	function create_options_page() {
		include( dirname( __FILE__ ) . '/app/views/wp-admin/options.php' );
	}

	/**
	 * Add the Javascript for the options page.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	function admin_scripts() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script(
			'dealertrend-inventory-api-admin' ,
			$this->meta_information[ 'PluginURL' ] . '/app/views/wp-admin/js/dealertrend-inventory-api-admin.js' , array( 'jquery' , 'jquery-ui-core' , 'jquery-ui-tabs' , 'jquery-ui-dialog' ),
			$this->meta_information[ 'Version' ],
			true
		);
	}

	/**
	 * Add our custom rewrite rules to the WordPress Rewrite Object.
	 *
	 * @since 3.0.0
	 * @return array The old rewrite rules with our prefixed to them.
	 */
	function add_rewrite_rules( $existing_rules ) {
		$new_rules = array();
		$new_rules[ '^(inventory)' ] = 'index.php?taxonomy=inventory';

		return $new_rules + $existing_rules;
	}

	/**
	 * Remove rewrite rules and then recreate rewrite rules.
	 *
	 * @since 3.0.0
	 * @return bool False on failure.
	 */
	function flush_rewrite_rules() {
		global $wp_rewrite;

		return $wp_rewrite->flush_rules();
	}

	/**
	 * Allows us to create our own taxonomy, see: {@link http://codex.wordpress.org/Taxonomies#What_is_a_taxonomy.3F Taxonomies: What is a taxonomy?}
	 *
	 * @since 3.0.0
	 * @return void
	 */
	function create_taxonomy() {
		$labels = array(
			# Localization: {@link http://phpdoc.wordpress.org/trunk/WordPress/i18n/_wp-includes---l10n.php.html#function_x}
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
				'rewrite' => array( 'slug' => 'inventory' )
			)
		);
	}

	/**
	 * Runs before the determination of the template file to be used to display the requested page, so that the plugin can override the template file choice.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	function show_inventory_theme() {
		global $wp_query;

		$this->parameters = $this->get_parameters();

		$taxonomy = ( isset( $wp_query->query_vars[ 'taxonomy' ] ) ) ? $wp_query->query_vars[ 'taxonomy' ] : NULL;

		switch( $taxonomy ) {

			case 'inventory':
				# There's an issue where WordPress is labeling our taxonomy as a home page. Which it is not.
				$wp_query->is_home = false;
				add_action( 'wp_print_styles' , array( &$this , 'inventory_styles' ) , 1 );
				add_action( 'wp_print_scripts', array( &$this , 'inventory_scripts' ) , 1 );

				$current_theme = $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'name' ];

				# Instantiate our object for the VMS.
				$vehicle_management_system = new vehicle_management_system(
					$this->options[ 'vehicle_management_system' ][ 'host' ],
					$this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ]
				);

				$company_information = $vehicle_management_system->get_company_information();

				# Because we don't carry the city and state in the parameter array, we need to construct it here and amend it to the array when we instantiate the object.
				$seo_hack = array( 'city' => $company_information[ 'data' ]->city , 'state' => $company_information[ 'data' ]->state );
				# Instantiate our object for dynamic title and meta information.
				$inventory_seo_headers = new inventory_seo_headers(
					$this->options[ 'vehicle_management_system' ][ 'host' ],
					$this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ],
					$this->parameters + $seo_hack
				);

				$theme_path = dirname( __FILE__ ) . '/app/views/inventory/' . $current_theme;
				if( $handle = opendir( $theme_path ) ) {
					while( false !== ( $file = readdir( $handle ) ) ) {
						if( $file == 'index.php' ) {
							include_once( $theme_path . '/index.php' );
						}
					}
					closedir( $handle );
				} else {
					echo __FUNCTION__ . ' Could not open directory at: ' . $theme_path;
					return false;
				}

				# Our ovveride should not continue, lest we end up with duplicated outputs.
				exit;

			break;
		}
	}

	/**
	 * Take the current parameters, including the URL structure and query strings and assemble them into an array.
	 *
	 * @since 3.0.0
	 * @return array The processed parameters.
	 */
	function get_parameters() {
		global $wp;
		global $wp_rewrite;
		$permalink_parameters = !empty( $wp_rewrite->permalink_structure ) ? explode( '/' , $wp->request ) : array();
		# Sanitize potential user inputs.
		$server_parameters = isset( $_GET ) ? array_map( array( &$this , 'sanitize_inputs' ) , $_GET ) : NULL;
		$server_parameters[ 'per_page' ] = $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'per_page' ];
		$parameters = array();
		# Assert control over the expected parameters.
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
				default: return; break;
			}
			$parameters[ $index ] = $value;
		}

		return array_merge( $parameters , $server_parameters );
	}

	/**
	 * Never trust the user.
	 *
	 * Recursive funciton intended to tranverse both scalar and non-scalar values to sanitize them usins kses.
	 *
	 * @since 3.0.0
	 * @return mixed The sanitized given values.
	 */
	function sanitize_inputs( $input ) {
		if( is_array( $input ) ) {
			foreach( $input as $key => $value ) {
				$input[ $key ] = is_scalar( $value ) ? wp_kses_data( $value , false , 'http' ) : array_map( array( &$this , 'sanitize_inputs' ) , $value );
			}
		} else {
			$input = wp_kses_data( $input , false , 'http' );
		}

		return $input;
	}

	/**
	 * Load the CSS styles for inventory themes.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	function inventory_styles() {
		$current_theme = $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'name' ];
		wp_register_style(
			'dealertrend-inventory-api',
			$this->meta_information[ 'PluginURL' ] . '/app/views/inventory/' . $current_theme . '/dealertrend-inventory-api.css',
			false,
			$this->meta_information[ 'Version' ]
		);
		wp_enqueue_style( 'dealertrend-inventory-api' );
		
		wp_register_Style( 'jquery-ui-black-tie' , 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/black-tie/jquery-ui.css' , false , '1.8.1' );
		wp_enqueue_style( 'jquery-ui-black-tie' ); 
	}

	/**
	 * Load the Javascript for inventory themes.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	function inventory_scripts() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'jquery-cycle' , 'http://cloud.github.com/downloads/malsup/cycle/jquery.cycle.all.2.72.js' , array( 'jquery' ) , '2.72' , true );
		wp_enqueue_script(
			'dealertrend-inventory-api',
			$this->meta_information[ 'PluginURL' ] . '/app/views/shared/js/dealertrend-api-init-inventory.js',
			array( 'jquery' , 'jquery-ui-core' , 'jquery-ui-tabs' , 'jquery-cycle' ),
			$this->meta_information[ 'Version' ],
			true
		);
	}

	/** 
	 * Get a list of a specific type of theme. Example: 'inventory' or 'showcase'.
	 *
	 * @since 3.0.0
	 * @param string $type The name of the folder to look in for themes.
	 * @return array The collected list of folders available to choose form.
	 */
	function get_themes( $type ) { 
		$directories = scandir( dirname( __FILE__ ) . '/app/views/' . $type . '/' );
		$ignore = array( '.' , '..' , 'shared' );
		foreach( $directories as $key => $value ) {
			if( in_array( $value , $ignore ) ) {
				unset( $directories[ $key ] );
			}
		}

		return array_values( $directories );
	}

}

# Instantiate the object so it can be used in other places.
if ( class_exists( 'dealertrend_inventory_api' ) and !isset( $dealertrend_inventory_api ) ) {
	$dealertrend_inventory_api = new dealertrend_inventory_api();
}

?>
