<?php
/**
 * Plugin Name: DealerTrend Inventory API
 * Plugin URI: https://github.com/dealertrend/wordpress-plugin-inventory-api
 * Author: DealerTrend, Inc.
 * Author URI: http://www.dealertrend.com
 * Description: Provides access to the Vehicle Management System and Vehicle Reference System provided by <a href="http://www.dealertrend.com" target="_blank" title="DealerTrend, Inc: Shift Everything">DealerTrend, Inc.</a>
 * Version: 3.5.1
 * License: GPLv2 or later
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

/** Load the helpers so we can interface with the APIs. */
require_once( dirname( __FILE__ ) . '/application/helpers/http_api_wrapper.php' );
require_once( dirname( __FILE__ ) . '/application/helpers/vehicle_management_system.php' );
require_once( dirname( __FILE__ ) . '/application/helpers/vehicle_reference_system.php' );
require_once( dirname( __FILE__ ) . '/application/helpers/dynamic_site_headers.php' );
require_once( dirname( __FILE__ ) . '/application/helpers/dealertrend_plugin_updater.php' );

/** Widgets */
require_once( dirname( __FILE__ ) . '/application/views/widgets/vehicle_management_system.php' );
require_once( dirname( __FILE__ ) . '/application/views/widgets/vehicle_reference_system.php' );

/**
 * This is the primary class for the plugin.
 *
 * It uses standard WordPress hooks and helpers for the different APIs it interfaces with.
 * It also utilizes several custom helpers to incorporate the API itself and some extended functionality of the WordPress core.
 *
 * @package Wordpress
 * @since 3.0.0
 */
class dealertrend_inventory_api {

	/**
	 * Public plugin information derived from the file header, as well as some custom keys.
	 *
	 * @package Wordpress
	 * @since 3.0.0
	 * @access public
	 * @var array
	 */
	public $plugin_information = array();

	/**
	 * Default options. These values are initially set when the plugin is creates a new instance.
	 *
	 * This is also the array that ends up storing changed variables wich are later saved.
	 *
	 * @package Wordpress
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
		),
		'debug' => array(
			'logging' => false
		),
		'jquery' => array(
			'ui' => array(
				'theme' => 'black-tie'
			)
		)
	);

	/**
	 * Public variable for all the parameters the plugin is currently working with.
	 *
	 * @package Wordpress
	 * @since 3.0.0
	 * @access public
	 * @var array
	 */
	public $parameters = array();

	/**
	 * Sets up object properties and ties into the WordPress procedural hooks. PHP 5 style constructor.
	 *
	 * @package Wordpress
	 * @since 3.0.0
	 * @return void
	 */
	function __construct() {
		$this->load_plugin_information();
		$this->check_for_updates();
		$this->load_options();
		$this->load_widgets();
		$this->load_admin_assets();
		$this->setup_routing();
		$this->queue_templates();
	}

	/**
	 * Retreives standard WordPress file headers from this plugin's header.
	 *
	 * Adds some custom keys that are plugin related data that are used frequently.
	 *
	 * @package Wordpress
	 * @since 3.0.0
	 * @return array
	 */
	function load_plugin_information() {
		$data = array();

		$file_headers = array (
			'Name' => 'Plugin Name',
			'PluginURI' => 'Plugin URI',
			'Version' => 'Version',
			'Description' => 'Description',
			'Author' => 'Author',
			'AuthorURI' => 'Author URI'
		);

		$data = get_file_data( __FILE__ , $file_headers , 'plugin' );

		$plugin_file = pathinfo( __FILE__ );
		$data[ 'PluginURL' ] = WP_PLUGIN_URL . '/' . basename( $plugin_file[ 'dirname' ] );
		$data[ 'PluginBaseName' ] = plugin_basename( __FILE__ );

		$this->plugin_information = $data;
	}

	/**
	 * Queues the updater to check for updates anytime WordPress is checking for an update.
	 *
	 * @package Wordpress
	 * @since 3.2
	 * @return void
	 *
	 */
	function check_for_updates() {
		add_action( 'core_version_check_locale' , array( &$this , 'instantiate_updater' ) );
	}

	/**
	 * Uses a helper to check for plugin updates. This looks up tags via GitHub and then if a new version is avilable, allows us to do an auto-install.
	 *
	 * @package Wordpress
	 * @since 3.0.1
	 * @return void;
	 */
	function instantiate_updater() {
		$update_handler = new dealetrend_plugin_updater( $this->plugin_information );
		$version_comparison = $update_handler->check_for_updates();
		$update_handler->display_update_notice( $version_comparison );
	}

	/**
	 * Load the plugins options from the database into the current scope.
	 *
	 * If there are no settings, it will instantiate them with the plugin defaults.
	 *
	 * @package Wordpress
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

	/**
	 * Load the widgets! (Must read this description in Frau Farbissina's voice)
	 *
	 * The widgets should only load if they have the settings they need to work.
	 *
	 * @package Wordpress
	 * @since 3.0.0
	 * @return void
	 */
	function load_widgets() {
		if( $this->options[ 'vehicle_management_system' ][ 'host' ] && $this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] ) {
			add_action( 'widgets_init' , create_function( '' , 'return register_widget( "vehicle_management_system_widget" );' ) );
		}

		if( $this->options[ 'vehicle_reference_system' ][ 'host' ] ) {
			add_action( 'widgets_init' , create_function( '' , 'return register_widget( "vehicle_reference_system_widget" );' ) );
		}
	}

	/**
	 * Load the stylesheets and scripts for the administration area.
	 *
	 * @package Wordpress
	 * @since 3.0.0
	 * @return void
	 */
	function load_admin_assets() {
		add_action( 'admin_menu' , array( &$this , 'admin_styles' ) );
		add_action( 'admin_menu' , array( &$this , 'admin_scripts' ) );
	}

	/**
	 * Take the quasinecessary steps to get our plugin to assert dominance within a specific taxonomy and URL structure.
	 *
	 * @package Wordpress
	 * @since 3.0.0
	 * @return void
	 */
	function setup_routing() {
		add_action( 'rewrite_rules_array' , array( &$this , 'add_rewrite_rules' ) , 1 );
		add_action( 'init' , array( &$this , 'flush_rewrite_rules' ) , 1 );
		add_action( 'init' , array( &$this , 'create_taxonomy' ) );
	}

	/**
	 * When loading the standard content WordPress would try to serve, under a specific instance we want to inject our own content.
	 *
	 * @package Wordpress
	 * @since 3.0.0
	 * @return void
	 */
	function queue_templates() {
		add_action( 'template_redirect' , array( &$this , 'show_inventory_theme' ) );
	}

	/**
	 * Saves the current options to the database then load them back into the plugin.
	 *
	 * @package WordPress
	 * @since 3.0.0
	 * @return void
	 */
	function save_options() {
		update_option( 'dealertrend_inventory_api' , $this->options );
		$this->load_options();
	}

	/**
	 * A persistent implementation of a funciton to add a few access points to the plugin's options page.
	 *
	 * It also checks to see if we are on the options page for the plugin, if so, load the CSS for it.
	 *
	 * @package WordPress
	 * @since 3.0.0
	 * @return void
	 */
	function admin_styles() {
		add_filter( 'plugin_action_links_' . $this->plugin_information[ 'PluginBaseName' ] , array( $this , 'add_plugin_links' ) );
		add_menu_page(
			'Dealertrend API',
			'Dealertrend API',
			'manage_options',
			'dealertrend_inventory_api',
			array( &$this , 'create_options_page' ),
			$this->plugin_information[ 'PluginURL' ] . '/application/views/options/img/icon-dealertrend.png'
		);

		if( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'dealertrend_inventory_api' ) {
			wp_enqueue_style( 'jquery-ui-' . $this->options[ 'jquery' ][ 'ui' ][ 'theme' ] , $this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui/1.8.11/themes/' . $this->options[ 'jquery' ][ 'ui' ][ 'theme' ] . '/jquery-ui.css' , false , '1.8.11' );
			wp_enqueue_style( 'dealertrend-inventory-api-admin' , $this->plugin_information[ 'PluginURL' ] . '/application/views/options/css/dealertrend-inventory-api.css' , false , $this->plugin_information[ 'Version' ] );
		}
	}

	/**
	 * Add shortcut links to the settings page for our plugin.
	 *
	 * @package WordPress
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
	 * @package WordPress
	 * @since 3.0.0
	 * @return void
	 */
	function create_options_page() {
		include( dirname( __FILE__ ) . '/application/views/options/page.php' );
	}

	/**
	 * Add the Javascript for the options page.
	 *
	 * @package WordPress
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
			$this->plugin_information[ 'PluginURL' ] . '/application/views/options/js/dealertrend-inventory-api-admin.js' , array( 'jquery' , 'jquery-ui-core' , 'jquery-ui-tabs' , 'jquery-ui-dialog' ),
			$this->plugin_information[ 'Version' ],
			true
		);
	}

	/**
	 * Add our custom rewrite rules to the WordPress Rewrite Object.
	 *
	 * @package WordPress
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
	 * @package WordPress
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
	 * @package WordPress
	 * @since 3.0.0
	 * @return void
	 */
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
				$this->fix_bad_wordpress_assumption();

				add_action( 'wp_print_styles' , array( &$this , 'inventory_styles' ) , 1 );
				add_action( 'wp_print_scripts', array( &$this , 'inventory_scripts' ) , 1 );

				$current_theme = $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'name' ];

				$vehicle_management_system = new vehicle_management_system(
					$this->options[ 'vehicle_management_system' ][ 'host' ],
					$this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ]
				);

				$company_information = $vehicle_management_system->get_company_information();

				if( $company_information[ 'data' ] != false ) {
					$seo_hack = array( 'city' => $company_information[ 'data' ]->city , 'state' => $company_information[ 'data' ]->state );
					$dynamic_site_headers = new dynamic_site_headers(
						$this->options[ 'vehicle_management_system' ][ 'host' ],
						$this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ],
						$this->parameters + $seo_hack
					);
				}

				$theme_path = dirname( __FILE__ ) . '/application/views/inventory/' . $current_theme;
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

				$this->stop_wordpress();

			break;
		}
	}

	/**
	 * There are instances where we need to stop the execution of WordPress.
	 *
	 * One such instance is when we are overriding the content output.
	 *
	 * @package Wordpress
	 * @since 3.0.0
	 * @return void
	 */
	function stop_wordpress() {
		exit;
	}

	/**
	 * There's an issue where WordPress is labeling our taxonomy as a home page. Which it is not.
	 *
	 * @package Wordpress
	 * @since 3.0.0
	 * @return void
	 */
	function fix_bad_wordpress_assumption() {
		$wp_query->is_home = false;
	}

	/**
	 * Take the current parameters, including the URL structure and query strings and assemble them into an array.
	 *
	 * @package Wordpress
	 * @since 3.0.0
	 * @return array The processed parameters.
	 */
	function get_parameters() {
		global $wp;
		global $wp_rewrite;

		$permalink_parameters = !empty( $wp_rewrite->permalink_structure ) ? explode( '/' , $wp->request ) : array();
		$server_parameters = isset( $_GET ) ? array_map( array( &$this , 'sanitize_inputs' ) , $_GET ) : NULL;
		$server_parameters[ 'per_page' ] = $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'per_page' ];
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
		wp_enqueue_style(
			'dealertrend-inventory-api',
			$this->plugin_information[ 'PluginURL' ] . '/application/views/inventory/' . $current_theme . '/dealertrend-inventory-api.css',
			false,
			$this->plugin_information[ 'Version' ]
		);
		
		wp_enqueue_style( 'jquery-ui-' . $this->options[ 'jquery' ][ 'ui' ][ 'theme' ] , $this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui/1.8.11/themes/' . $this->options[ 'jquery' ][ 'ui' ][ 'theme' ] . '/jquery-ui.css' , false , '1.8.11' );
	}

	/**
	 * Load the Javascript for inventory themes.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	function inventory_scripts() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-cycle' , $this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-cycle/2.72/js/jquery.cycle.all.js' , array( 'jquery' ) , '2.72' , true );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_register_style(
			'dealertrend-inventory-api-detail-tabs',
			$this->plugin_information[ 'PluginURL' ] . '/application/assets/inventory/detail-tabs.js',
			false,
			$this->plugin_information[ 'Version' ]
		);
		wp_register_style(
			'dealertrend-inventory-api-loan-calculator',
			$this->plugin_information[ 'PluginURL' ] . '/application/assets/inventory/loan-calculator.js',
			false,
			$this->plugin_information[ 'Version' ]
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
		$directories = scandir( dirname( __FILE__ ) . '/application/views/' . $type . '/' );
		$ignore = array( '.' , '..' );
		foreach( $directories as $key => $value ) {
			if( in_array( $value , $ignore ) ) {
				unset( $directories[ $key ] );
			}
		}

		return array_values( $directories );
	}

}

if ( class_exists( 'dealertrend_inventory_api' ) and !isset( $dealertrend_inventory_api ) ) {
	$dealertrend_inventory_api = new dealertrend_inventory_api();
}

?>
