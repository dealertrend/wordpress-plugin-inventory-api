<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

require_once( dirname( __FILE__ ) . '/application/helpers/http_request.php' );
require_once( dirname( __FILE__ ) . '/application/helpers/ajax.php' );
require_once( dirname( __FILE__ ) . '/application/helpers/vehicle_management_system.php' );
require_once( dirname( __FILE__ ) . '/application/helpers/vehicle_reference_system.php' );
require_once( dirname( __FILE__ ) . '/application/helpers/dynamic_site_headers.php' );
require_once( dirname( __FILE__ ) . '/application/helpers/updater.php' );

require_once( dirname( __FILE__ ) . '/application/views/widgets/vehicle_management_system.php' );
require_once( dirname( __FILE__ ) . '/application/views/widgets/vehicle_reference_system.php' );

class Plugin {

	public $options = array(
		'vehicle_management_system' => array(
			'company_information' => array(
				'id' => 0
			),
			'host' => '',
			'mobile_theme' => array(
				'name' => 'websitez',
				'per_page' => '10'
			),
			'theme' => array(
				'name' => 'armadillo',
				'per_page' => 10
			),
			'saleclass' => 'all'
		),
		'vehicle_reference_system' => array(
			'host' => '',
			'data' => array(
				'makes' => array(),
				'models' => array()
			),
			'theme' => 'default'
		),
		'jquery' => array(
			'ui' => array(
				'admin-theme' => 'dealertrend',
				'inventory-theme' => 'smoothness',
				'showcase-theme' => 'smoothness'
			)
		)
	);

	public $plugin_information = array();
	public $parameters = array();
	public $taxonomy = null;
	public $is_mobile = false;

	public function execute() {
		$this->load_plugin_information();
		$this->queue_registrations();
		$this->check_for_updates();
		$this->load_options();
		$this->load_widgets();
		$this->load_admin_assets();
		$this->setup_routing();
		$this->queue_templates();
	}

	private function get_master_file() {
		return dirname( __FILE__ ) . '/loader.php';
	}

	private function get_plugin_basename() {
		return plugin_basename( $this->get_master_file() );
	}

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

		$data = get_file_data( $this->get_master_file() , $file_headers , 'plugin' );

		$plugin_file = pathinfo( $this->get_master_file() );
		$data[ 'PluginURL' ] = plugins_url( '' , $this->get_master_file() );
		$data[ 'PluginBaseName' ] = $this->get_plugin_basename();

		$this->plugin_information = $data;
	}

	function queue_registrations() {
		add_action( 'init' , array( &$this , 'register_assets' ) );
	}

	function register_assets() {
			$admin_jquery_ui_theme = $this->options[ 'jquery' ][ 'ui' ][ 'admin-theme' ];
			$inventory_jquery_ui_theme = $this->options[ 'jquery' ][ 'ui' ][ 'inventory-theme' ];
			$showcase_jquery_ui_theme = $this->options[ 'jquery' ][ 'ui' ][ 'showcase-theme' ];
			wp_register_style(
				$admin_jquery_ui_theme,
				$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui/1.8.11/themes/' . $admin_jquery_ui_theme . '/jquery-ui.css',
				array( 'colors' ),
				'1.8.11'
			);
			wp_register_style(
				$inventory_jquery_ui_theme,
				$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui/1.8.11/themes/' . $inventory_jquery_ui_theme . '/jquery-ui.css',
				false,
				'1.8.11'
			);
			wp_register_style(
				$showcase_jquery_ui_theme,
				$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui/1.8.11/themes/' . $showcase_jquery_ui_theme . '/jquery-ui.css',
				false,
				'1.8.11'
			);
			wp_register_style(
				'dealertrend-inventory-api-admin',
				$this->plugin_information[ 'PluginURL' ] . '/application/views/options/css/dealertrend-inventory-api.css',
				array( $admin_jquery_ui_theme , 'jquery-ui-multiselect' , 'jquery-ui-multiselect-filter' ),
				$this->plugin_information[ 'Version' ]
			);
			wp_register_style(
				'jquery-ui-multiselect',
				$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui-multiselect-widget/1.14/css/jquery.multiselect.css',
				array( $admin_jquery_ui_theme ),
				'1.14'
			);
			wp_register_style(
				'jquery-ui-multiselect-filter',
				$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui-multiselect-widget/1.14/css/jquery.multiselect.filter.css',
				array( 'jquery-ui-multiselect' ),
				'1.14'
			);

			wp_register_script(
				'jquery-ui-multiselect',
				$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui-multiselect-widget/1.14/js/jquery.multiselect.min.js',
				array( 'jquery' ),
				'1.14',
				true
			);
			wp_register_script(
				'jquery-ui-multiselect-filter',
				$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui-multiselect-widget/1.14/js/jquery.multiselect.filter.min.js',
				array( 'jquery' , 'jquery-ui-multiselect' ),
				'1.14',
				true
			);
			wp_register_script(
				'dealertrend-inventory-api-admin' ,
				$this->plugin_information[ 'PluginURL' ] . '/application/views/options/js/dealertrend-inventory-api-admin.js',
				array( 'jquery' , 'jquery-ui-core' , 'jquery-ui-tabs' , 'jquery-ui-dialog' , 'jquery-ui-multiselect' , 'jquery-ui-multiselect-filter' ),
				$this->plugin_information[ 'Version' ],
				true
			);
			wp_register_script(
				'jquery-cookie',
				$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-cookie/1.0/js/jquery.cookie.js',
				array( 'jquery' ),
				'1.0',
				true
			);
			wp_register_script(
				'dealertrend_inventory_api_traffic_source',
				$this->plugin_information[ 'PluginURL' ] . '/application/assets/inventory/js/traffic-sources.js',
				array( 'jquery-cookie' ),
				$this->plugin_information[ 'Version' ],
				true
			);
		}

	function check_for_updates() {
		add_action( 'admin_init' , array( &$this , 'instantiate_updater' ) );
	}

	function instantiate_updater() {
		$update_handler = new Updater( $this->plugin_information );
		$version_comparison = $update_handler->check_for_updates();
		$update_handler->display_update_notice( $version_comparison );
	}

	function load_options() {
		$loaded_options = get_option( 'dealertrend_inventory_api' ) ;
		if( !$loaded_options ) {
			update_option( 'dealertrend_inventory_api' , $this->options );
		} else {
			if( $this->validate_options( &$loaded_options , &$this->options ) ) {
				update_option( 'dealertrend_inventory_api' , $loaded_options );
			}
			foreach( $loaded_options as $option_group => $option_values ) {
				$this->options[ $option_group ] = $option_values;
			}
		}
	}

	function validate_options( $options , $defaults , $modified = false ) {
		foreach( $defaults as $key => $value ) {
			if( is_array( $value ) ) {
				$this->validate_options( &$options[ $key ] , &$value , &$modified );
			} elseif( !isset( $options[ $key ] ) || $options[ $key ] == NULL ) {
				$options[ $key ] = $defaults[ $key ];
				$modified = true;
			}
		}

		return $modified;
	}

	function load_widgets() {
		if( $this->options[ 'vehicle_management_system' ][ 'host' ] && $this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] ) {
			add_action( 'widgets_init' , create_function( '' , 'return register_widget( "vehicle_management_system_widget" );' ) );
		}

		if( $this->options[ 'vehicle_reference_system' ][ 'host' ] ) {
			add_action( 'widgets_init' , create_function( '' , 'return register_widget( "vehicle_reference_system_widget" );' ) );
		}
	}

	function load_admin_assets() {
		add_action( 'admin_menu' , array( &$this , 'admin_styles' ) );
		add_action( 'admin_menu' , array( &$this , 'admin_scripts' ) );
	}

	function setup_routing() {
		add_action( 'rewrite_rules_array' , array( &$this , 'add_rewrite_rules' ) , 1 );
		add_action( 'init' , array( &$this , 'flush_rewrite_rules' ) , 1 );
		add_action( 'init' , array( &$this , 'create_taxonomies' ) );
	}

	function queue_templates() {
		add_action( 'template_redirect' , array( &$this , 'show_theme' ) , 100 );
	}

	function save_options() {
		update_option( 'dealertrend_inventory_api' , $this->options );
		$this->load_options();
	}

	function admin_styles() {
		$network_admin = is_network_admin();
		$prefix = $network_admin ? 'network_admin_' : '';
		add_filter( $prefix . 'plugin_action_links_' . $this->get_plugin_basename() , array( $this , 'add_plugin_links' ) );
		add_menu_page(
			'Dealertrend API',
			'Dealertrend API',
			'manage_options',
			'dealertrend_inventory_api',
			array( &$this , 'create_options_page' ),
			$this->plugin_information[ 'PluginURL' ] . '/application/views/options/img/icon-dealertrend.png'
		);

		if( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'dealertrend_inventory_api' ) {
			wp_enqueue_style( 'dealertrend-inventory-api-admin' );
		}
	}

	function add_plugin_links( $links ) {
		$settings_link = '<a href="admin.php?page=dealertrend_inventory_api#settings">Settings</a>';
		$readme_link = '<a href="admin.php?page=dealertrend_inventory_api#help">Help</a>';
		array_unshift( $links , $settings_link );
		array_unshift( $links , $readme_link );

		return $links;
	}

	function create_options_page() {
		include( dirname( __FILE__ ) . '/application/views/options/page.php' );
	}

	function admin_scripts() {
		wp_enqueue_script( 'dealertrend-inventory-api-admin' );
	}

	function add_rewrite_rules( $existing_rules ) {
		$new_rules = array();
		if( $this->options[ 'vehicle_management_system' ][ 'host' ] && $this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] ) {
			$new_rules[ '^(inventory)' ] = 'index.php?taxonomy=inventory';
		}
		if( $this->options[ 'vehicle_reference_system' ][ 'host' ] && count( $this->options[ 'vehicle_reference_system' ][ 'data' ][ 'makes' ] ) > 0 ) {
			$new_rules[ '^(showcase)' ] = 'index.php?taxonomy=showcase';
		}
		$new_rules[ '^(dealertrend-ajax)' ] = 'index.php?taxonomy=dealertrend-ajax';

		return $new_rules + $existing_rules;
	}

	function flush_rewrite_rules() {
		global $wp_rewrite;

		return $wp_rewrite->flush_rules();
	}

	function create_taxonomies() {
		if( $this->options[ 'vehicle_management_system' ][ 'host' ] && $this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] ) {
			add_filter( 'widget_text' , 'do_shortcode' );
			register_sidebar(array(
				'name' => 'Inventory Vehicle Detail Page',
				'id' => 'vehicle-detail-page',
				'description' => 'Widgets in this area will show up on the Vehicle Detail Pagee within Inventory.',
				'before_title' => '<h1>',
				'after_title' => '</h1>',
				'before_widget' => '<div class="inventory widget">',
				'after_widget' => '</div>'
			));
			$labels = array(
				'name' => _x( 'Inventory' , 'taxonomy general name' ),
				'menu_name' => __( 'Inventory' )
			);
			register_taxonomy(
				'inventory',
				array( 'page' ),
				array(
					'hierarchical' => false,
					'labels' => $labels,
					'show_ui' => false,
					'query_var' => true,
					'rewrite' => array( 'slug' => 'inventory' )
				)
			);
		}
		if( $this->options[ 'vehicle_reference_system' ][ 'host' ] ) {
			add_filter( 'widget_text' , 'do_shortcode' );
			register_sidebar(array(
				'name' => 'Showcase Trim Page',
				'id' => 'showcase-trim-page',
				'description' => 'Widgets in this area will show up on the trim page within Showcase.',
				'before_title' => '<h1>',
				'after_title' => '</h1>',
				'before_widget' => '<div class="showcase widget">',
				'after_widget' => '</div>'
			));
			$labels = array(
				'name' => _x( 'Showcase' , 'taxonomy general name' ),
				'menu_name' => __( 'Showcase' )
			);
			register_taxonomy(
				'showcase',
				array( 'page' ),
				array(
					'hierarchical' => false,
					'labels' => $labels,
					'show_ui' => false,
					'query_var' => true,
					'rewrite' => array( 'slug' => 'showcase' )
				)
			);
		}
		$labels = array(
			'name' => _x( 'DealerTrend AJAX' , 'taxonomy general name' )
		);
		register_taxonomy(
			'showcase',
			array( 'page' ),
			array(
				'hierarchical' => false,
				'labels' => $labels,
				'show_ui' => false,
				'query_var' => true,
				'rewrite' => array( 'slug' => 'dealertrend-ajax' )
			)
		);
	}

	function show_theme() {
		global $wp_query;

		$this->check_mobile();

		$this->taxonomy = ( isset( $wp_query->query_vars[ 'taxonomy' ] ) ) ? $wp_query->query_vars[ 'taxonomy' ] : NULL;

		$this->parameters = $this->get_parameters();

		wp_enqueue_script( 'dealertrend_inventory_api_traffic_source' );

		switch( $this->taxonomy ) {

			case 'inventory':
				if( $this->options[ 'vehicle_management_system' ][ 'host' ] ) {
					$this->fix_bad_wordpress_assumption();

					$current_theme = $this->is_mobile != true ? $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'name' ] : $this->options[ 'vehicle_management_system' ][ 'mobile_theme' ][ 'name' ];
					$theme_folder = $this->is_mobile != true ? 'inventory' : 'mobile';
					$theme_path = dirname( __FILE__ ) . '/application/views/' . $theme_folder . '/' . $current_theme;

					add_action( 'wp_print_styles' , array( &$this , 'inventory_styles' ) , 1 );

					$vehicle_management_system = new vehicle_management_system(
						$this->options[ 'vehicle_management_system' ][ 'host' ],
						$this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ]
					);

					$status = $vehicle_management_system->set_headers( $this->parameters );

					$vehicle_management_system->tracer = 'Getting company information for use in other API requests.';
					$company_information = $vehicle_management_system->get_company_information()->please();

					if( isset( $company_information[ 'response' ][ 'code' ] ) && $company_information[ 'response' ][ 'code' ] == 200 ) {
						$data = json_decode( $company_information[ 'body' ] );
						$seo_hack = array( 'city' => $data->seo->city , 'state' => $data->seo->state , 'country_code' => $data->country_code );
						$dynamic_site_headers = new dynamic_site_headers(
							$this->options[ 'vehicle_management_system' ][ 'host' ],
							$this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ],
							(array) $this->parameters + (array) $seo_hack
						);
					}

					if( $handle = opendir( $theme_path ) ) {
						while( false != ( $file = readdir( $handle ) ) ) {
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
				}
			break;
			case 'showcase':
				if( $this->options[ 'vehicle_reference_system' ][ 'host' ] ) {
					$this->fix_bad_wordpress_assumption();

					$vehicle_management_system = new vehicle_management_system(
						$this->options[ 'vehicle_management_system' ][ 'host' ],
						$this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ]
					);
					$vehicle_management_system->tracer = 'Getting company information for use in other API requests.';
					$company_information = $vehicle_management_system->get_company_information()->please();

					$country_code = isset( $data->country_code ) ? $data->country_code : 'US';
					if( isset( $company_information[ 'body' ] ) ) {
						$data = json_decode( $company_information[ 'body' ] );
						if( isset( $company_information[ 'response' ][ 'code' ] ) && $company_information[ 'response' ][ 'code' ] == 200 ) {
							$seo_hack = array( 'city' => $data->seo->city , 'state' => $data->seo->state );
							$dynamic_site_headers = new dynamic_site_headers(
								$this->options[ 'vehicle_management_system' ][ 'host' ],
								$this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ],
								(array) $this->parameters + (array) $seo_hack
							);
						}
						$country_code = $data->country_code;
					}

					$current_theme = $this->options[ 'vehicle_reference_system' ][ 'theme' ];
					$theme_folder = 'showcase';
					$theme_path = dirname( __FILE__ ) . '/application/views/' . $theme_folder . '/' . $current_theme;

					$vehicle_reference_system = new vehicle_reference_system(
						$this->options[ 'vehicle_reference_system' ][ 'host' ],
						$country_code
					);

					if( $handle = opendir( $theme_path ) ) {
						while( false != ( $file = readdir( $handle ) ) ) {
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
				}
			break;
			case 'dealertrend-ajax':
				$this->fix_bad_wordpress_assumption();
				$ajax = new ajax( $this->parameters , $this );
				$this->stop_wordpress();
			break;
		}
	}

	function check_mobile() {
		global $wp_query;
		$this->is_mobile = isset( $wp_query->query_vars[ 'is_mobile' ] ) ? $wp_query->query_vars[ 'is_mobile' ] : false;
	}

	function stop_wordpress() {
		exit;
	}

	function fix_bad_wordpress_assumption() {
		global $wp_query;
		$wp_query->is_home = false;
	}

	function get_parameters() {
		global $wp;
		global $wp_rewrite;

		$permalink_parameters = !empty( $wp_rewrite->permalink_structure ) ? explode( '/' , $wp->request ) : array();
		$server_parameters = isset( $_GET ) ? array_map( array( &$this , 'sanitize_inputs' ) , $_GET ) : NULL;
		$parameters = array();

		switch( $this->taxonomy ) {
			case 'inventory';
				$server_parameters[ 'per_page' ] = $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'per_page' ];

				switch( $this->options[ 'vehicle_management_system' ][ 'saleclass' ] ) {
					case 'new':
						$server_parameters[ 'saleclass' ] = 'new';
						break;
					case 'used':
						$server_parameters[ 'saleclass' ] = 'used';
						break;
				}

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
			break;
			case 'showcase':
				foreach( $permalink_parameters as $key => $value ) {
					switch( $key ) {
						case 0: $index = 'taxonomy'; break;
						case 1: $index = 'make'; break;
						case 2: $index = 'model'; break;
						case 3: $index = 'trim'; break;
						default: return; break;
					}
					$parameters[ $index ] = $value;
				}
			break;
			case 'dealertrend-ajax':
				foreach( $permalink_parameters as $key => $value ) {
					switch( $key ) {
						case 0: $index = 'taxonomy'; break;
						case 1: $index = 'meta-taxonomy'; break;
						case ( $key == 2 && $parameters[ 'meta-taxonomy' ] == 'inventory' ): $index = 'year'; break;
						case ( $key == 3 && $parameters[ 'meta-taxonomy' ] == 'inventory' ): $index = 'make'; break;
						case ( $key == 4 && $parameters[ 'meta-taxonomy' ] == 'inventory' ): $index = 'model'; break;
						case ( $key == 5 && $parameters[ 'meta-taxonomy' ] == 'inventory' ): $index = 'state'; break;
						case ( $key == 6 && $parameters[ 'meta-taxonomy' ] == 'inventory' ): $index = 'city'; break;
						case ( $key == 7 && $parameters[ 'meta-taxonomy' ] == 'inventory' ): $index = 'vin'; break;
						case ( $key == 2 && $parameters[ 'meta-taxonomy' ] == 'showcase' ): $index = 'make'; break;
						case ( $key == 3 && $parameters[ 'meta-taxonomy' ] == 'showcase' ): $index = 'model'; break;
						case ( $key == 4 && $parameters[ 'meta-taxonomy' ] == 'showcase' ): $index = 'trim'; break;
						default: return; break;
					}
					$parameters[ $index ] = $value;
				}
			break;
		}
		return array_merge( $parameters , $server_parameters );
	}

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

	function inventory_styles() {
		$current_theme = $this->is_mobile != true ? $this->options[ 'vehicle_management_system' ][ 'theme' ][ 'name' ] : $this->options[ 'vehicle_management_system' ][ 'mobile_theme' ][ 'name' ];
		$theme_folder = $this->is_mobile != true ? 'inventory' : 'mobile';
		$style_path = $this->plugin_information[ 'PluginURL' ] . '/application/views/' . $theme_folder . '/' . $current_theme . '/dealertrend-inventory-api.css';
		wp_enqueue_style(
			'dealertrend-inventory-api',
			$style_path,
			array( $this->options[ 'jquery' ][ 'ui' ][ 'inventory-theme' ] ),
			$this->plugin_information[ 'Version' ]
		);
	}

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

?>
