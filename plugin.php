<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

require_once( dirname( __FILE__ ) . '/application/helpers/updater.php' );
require_once( dirname( __FILE__ ) . '/application/helpers/vehicle_management_system.php' );
require_once( dirname( __FILE__ ) . '/application/helpers/dynamic_site_headers.php' );
require_once( dirname( __FILE__ ) . '/application/helpers/vehicle_reference_system.php' );
require_once( dirname( __FILE__ ) . '/application/helpers/ajax.php' );

require_once( dirname( __FILE__ ) . '/application/views/widgets/vehicle_management_system.php' );
require_once( dirname( __FILE__ ) . '/application/views/widgets/vehicle_reference_system.php' );

class Plugin {

	protected $_plugin_options = array();
	protected $_plugin_information = array();
	protected $_plugin_parameters = array();

	public $vehicle_managment_system = false;
	public $vehicle_reference_system = false;
	public $dynamic_site_headers = false;
	public $inventory_ajax = false;

	# START!
	public function execute() {
		$this->_hook_into_wordpress();
		$this->_setup_options();
		$this->_setup_routing();
		$this->_setup_objects();
		$this->_setup_templates();
	}

	#execute
	private function _hook_into_wordpress() {
		add_action( 'admin_init' , array( &$this , 'run_updater' ) );
		$this->_enable_shortcodes_within_text_widgets();
		$this->_register_sidebars();
		add_filter(
			$this->_get_hook_prefix() . 'plugin_action_links_' . $this->_get_plugin_basename(),
			array( &$this , 'add_plugin_links' )
		);
		add_action( 'admin_menu' , array( &$this , 'add_admin_menu_item' ) );
	}

	#execute > _hook_into_wordpress
	public function run_updater() {
		$updater = new Updater();
		$updater->check_for_updates();
	}

	#execute > _hook_into_wordpress
	private function _enable_shortcodes_within_text_widgets() {
		add_filter( 'widget_text' , 'do_shortcode' );
	}

	#execute > _hook_into_wordpress
	private function _register_sidebars() {
		register_sidebar(
			array(
				'name' => 'Inventory Vehicle Detail Page',
				'id' => 'vehicle-detail-page',
				'description' => 'Widgets in this area will show up on the Vehicle Detail Pagee within Inventory.',
				'before_title' => '<h1>',
				'after_title' => '</h1>',
				'before_widget' => '<div class="inventory widget">',
				'after_widget' => '</div>'
			)
		);
		register_sidebar(
			array(
				'name' => 'Showcase Trim Page',
				'id' => 'showcase-trim-page',
				'description' => 'Widgets in this area will show up on the trim page within Showcase.',
				'before_title' => '<h1>',
				'after_title' => '</h1>',
				'before_widget' => '<div class="showcase widget">',
				'after_widget' => '</div>'
			)
		);
	}

	#execute > _hook_into_wordpress
	private function _get_hook_prefix() {
		return is_network_admin() ? 'network_admin_' : '';
	}

	#execute > _hook_into_wordpress
	private function _get_plugin_basename() {
		return plugin_basename( $this->_get_master_file() );
	}

	#execute > _hook_into_wordpress > _get_plugin_basename
	private function _get_master_file() {
		return dirname( __FILE__ ) . '/loader.php';
	}

	#execute > _hook_into_wordpress
	public function add_plugin_links( $links ) {
		array_unshift( $links , '<a href="admin.php?page=' . $this->_get_slug() . '#settings">Settings</a>' );
		array_unshift( $links , '<a href="admin.php?page=' . $this->_get_slug() . '#help">Help</a>' );

		return $links;
	}

	#execute > _hook_into_wordpress > _get_slug
	protected function _get_slug() {
		return 'dealertrend_inventory_api';
	}

	#execute > _hook_into_wordpress
	public function add_admin_menu_item() {
		$this->_setup_options_page();

		add_menu_page(
			'Dealertrend API',
			'Dealertrend API',
			'manage_options',
			$this->_get_slug(),
			array( &$this , 'create_options_page' ),
			$this->_get_plugin_url() . '/application/views/options/img/icon-dealertrend.png'
		);
	}

	#execute > _hook_into_wordpress > add_admin_menu_item
	private function _setup_options_page() {
		add_action( 'admin_init' , array( &$this , 'register_options_page_styles' ) );
		add_action( 'admin_init' , array( &$this , 'enqueue_options_page_styles' ) );
		add_action( 'admin_init' , array( &$this , 'register_options_page_scripts' ) );
		add_action( 'admin_init' , array( &$this , 'enqueue_options_page_scripts' ) );
	}

	#execute > _hook_into_wordpress > add_admin_menu_item > _setup_options_page
	public function register_options_page_styles() {
		$this->_register_jquery_ui_theme( $this->_get_options_page_jquery_ui_theme() , array( 'colors' ) );
		$this->_register_jquery_ui_multiselect_style(
			'jquery-ui-multiselect',
			'jquery.multiselect.css',
			array( $this->_get_options_page_jquery_ui_theme() )
		);
		$this->_register_jquery_ui_multiselect_style(
			'jquery-ui-multiselect-filter',
			'jquery.multiselect.filter.css',
			array( 'jquery-ui-multiselect' )
		);
		wp_register_style(
			$this->_get_options_page_key(),
			$this->_get_plugin_url() . '/application/views/options/css/dealertrend-inventory-api.css',
			array(
				$this->_get_options_page_jquery_ui_theme(),
				'jquery-ui-multiselect',
				'jquery-ui-multiselect-filter'
			)
		);
	}

	#execute > _hook_into_wordpress > add_admin_menu_item > _setup_options_page > register_options_page_styles
	private function _register_jquery_ui_theme( $theme_name , $dependencies = false ) {
		wp_register_style(
			$theme_name,
			$this->_get_jquery_ui_theme_path( $theme_name ) . '/jquery-ui.css',
			$dependencies,
			$this->_get_jquery_ui_version()
		);
	}

	#execute > _hook_into_wordpress > add_admin_menu_item > _setup_options_page > register_options_page_styles > _register_jquery_ui_theme
	private function _get_jquery_ui_theme_path( $theme_folder ) {
		return
		$this->_get_plugin_url() .
		'/application/assets/jquery-ui/' .
		$this->_get_jquery_ui_version() . '/themes/' . $theme_folder;
	}

	#execute > _hook_into_wordpress > add_admin_menu_item > _setup_options_page > register_options_page_styles > _register_jquery_ui_theme > _get_jquery_ui_theme_path
	private function _get_jquery_ui_version() {
		return '1.8.11';
	}

	#execute > _hook_into_wordpress > add_admin_menu_item > _setup_options_page > register_options_page_styles > _register_jquery_ui_theme
	private function _get_options_page_jquery_ui_theme() {
		return $this->_plugin_options[ 'jquery' ][ 'ui' ][ 'options-page-theme' ];
	}

	#execute > _hook_into_wordpress > add_admin_menu_item > _setup_options_page > register_options_page_styles
	private function _register_jquery_ui_multiselect_style( $style_name , $file , $dependencies ) {
		wp_register_style(
			$style_name,
			$this->_get_plugin_url() .
				'/application/assets/jquery-ui-multiselect-widget/' .
				$this->_get_jquery_ui_multiselect_version() . '/css/' . $file,
			$dependencies,
			$this->_get_jquery_ui_multiselect_version()
		);
	}

	#execute > _hook_into_wordpress > add_admin_menu_item > _setup_options_page > register_options_page_styles > _register_jquery_ui_multiselect_style
	private function _get_jquery_ui_multiselect_version() {
		return '1.10';
	}

	#execute > _hook_into_wordpress > add_admin_menu_item > _setup_options_page > register_options_page_styles
	private function _get_options_page_key() {
		return 'dealertrend-inventory-api-options-page';
	}

	#execute > _hook_into_wordpress > add_admin_menu_item > _setup_options_page > register_options_page_styles
	private function _get_plugin_url() {
		return plugins_url( '' , $this->_get_master_file() );
	}

	#execute > _hook_into_wordpress > add_admin_menu_item > _setup_options_page
	public function enqueue_options_page_styles() {
		if( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == $this->_get_slug() ) {
			wp_enqueue_style( $this->_get_options_page_key() );
		}
	}

	#execute > _hook_into_wordpress > add_admin_menu_item > _setup_options_page
	public function register_options_page_scripts() {
echo 'test';
		$this->_register_jquery_ui_multiselect_script(
			'jquery-ui-multiselect',
			'jquery.multiselect.min.js',
			array( 'jquery' )
		);
		$this->_register_jquery_ui_multiselect_script(
			'jquery-ui-multiselect-filter',
			'jquery.multiselect.filter.min.js',
			array( 'jquery' , 'jquery-ui-multiselect' )
		);
		wp_register_script(
			$this->_get_options_page_key(),
			$this->_get_plugin_url() . '/application/views/options/js/dealertrend-inventory-api.js',
			array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-tabs',
				'jquery-ui-dialog',
				'jquery-ui-multiselect',
				'jquery-ui-multiselect-filter'
			),
			true
		);
	}

  private function _register_jquery_ui_multiselect_script( $script_name , $file , $dependencies ) { 
      wp_register_script(
        $script_name,
        $this->_get_plugin_url() .
          '/application/assets/jquery-ui-multiselect-widget/' .
          $this->_get_jquery_ui_multiselect_version() . '/js/' . $file,
        $dependencies,
        $this->_get_jquery_ui_multiselect_version(),
        true
      );  
  }

	#execute > _hook_into_wordpress > add_admin_menu_item > _setup_options_page
	public function enqueue_options_page_scripts() {
		if( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == $this->_get_slug() ) {
			wp_enqueue_script( $this->_get_options_page_key() );
		}
	}

  public function create_options_page() {
    require_once( dirname( __FILE__ ) . '/application/views/options/page.php' );
    $options_page = new Options_Page(); 
    $options_page->display();
  }

	#execute > _hook_into_wordpress
	protected function _setup_options() {
		if( ! $this->_get_plugin_options() ) {
			$this->_load_default_options();
			$this->_save_options();
		}

		$this->_load_options();
	}

	#execute > _hook_into_wordpress > _setup_options
	protected function _get_plugin_options() {
		if( empty( $this->_plugin_options ) ) {
			$this->_plugin_options = get_option( $this->_get_slug() );
		}
		return $this->_plugin_options;
	}

	#execute > _hook_into_wordpress > _setup_options
	private function _load_default_options() {
		$this->_set_default_vms_options();
		$this->_set_default_vrs_options();
		$this->_set_default_jquery_options();
	}

	#execute > _hook_into_wordpress > _setup_options > _load_default_options
	private function _set_default_vms_options() {
		$this->_plugin_options[ 'vehicle_management_system' ] = array(
			'mobile_theme' => array(
				'name' => 'websitez',
				'per_page' => '10'
			),
			'theme' => array(
				'name' => 'armadillo',
				'per_page' => 10
			)
		);
	}

	#execute > _hook_into_wordpress > _setup_options > _load_default_options
	private function _set_default_vrs_options() {
		$this->_plugin_options[ 'vehicle_reference_system' ] = array(
			'theme' => array(
				'name' => 'default'
			)
		);
	}

	#execute > _hook_into_wordpress > _setup_options > _load_default_options
	private function _set_default_jquery_options() {
		$this->_plugin_options[ 'jquery' ] = array(
			'ui' => array(
				'options-page-theme' => 'dealertrend',
				'inventory-theme' => 'smoothness',
				'showcase-theme' => 'smoothness'
			)
		);
	}

	#execute > _hook_into_wordpress > _setup_options
	protected function _save_options() {
		update_option( $this->_get_slug() , $this->_plugin_options );
		$this->_load_options();
	}

	#execute > _hook_into_wordpress > _setup_options > _save_options
	private function _load_options() {
		foreach( $this->_get_plugin_options() as $option_group => $option_values ) {
			$this->_plugin_options[ $option_group ] = $option_values;
		}
	}

	#execute > _hook_into_wordpress
	private function _setup_routing() {
		add_action( 'rewrite_rules_array' , array( &$this , 'add_rewrite_rules' ) , 1 );
		add_action( 'init' , array( &$this , 'flush_rewrite_rules' ) , 1 );
		add_action( 'init' , array( &$this , 'create_taxonomies' ) );
	}

	#execute > _hook_into_wordpress > _setup_routing
	public function add_rewrite_rules( $rules ) {
		$this->_set_vms_rewrite_rule( $rules );
		$this->_set_vrs_rewrite_rule( $rules );
		$this->_set_ajax_rewrite_rule( $rules );
		return $rules;
	}

	#execute > _hook_into_wordpress > _setup_routing > add_rewrite_rules
	private function _set_vms_rewrite_rule( &$rules ) {
		if( $this->_get_vms_host() != null && $this->_get_vms_company_id() != 0 ) {
			$rules[ '^(inventory)' ] = 'index.php?taxonomy=inventory';
		}
	}

	#execute > _hook_into_wordpress > _setup_routing > add_rewrite_rules > _set_vms_rewrite_rule
	protected function _get_vms_host() {
		return
		isset( $this->_plugin_options[ 'vehicle_management_system' ][ 'host' ] ) ?
		$this->_plugin_options[ 'vehicle_management_system' ][ 'host' ] :
		null;
	}

	#execute > _hook_into_wordpress > _setup_routing > add_rewrite_rules > _set_vms_rewrite_rule
	protected function _get_vms_company_id() {
		return
		isset( $this->_plugin_options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] ) ?
		$this->_plugin_options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] :
		false;
	}

	#execute > _hook_into_wordpress > _setup_routing > add_rewrite_rules
	private function _set_vrs_rewrite_rule( &$rules ) {
		if( $this->_get_vrs_host() ) {
			$rules[ '^(showcase)' ] = 'index.php?taxonomy=showcase';
		}
	}

	#execute > _hook_into_wordpress > _setup_routing > add_rewrite_rules > _set_vrs_rewrite_rule
	protected function _get_vrs_host() {
		return
		isset( $this->_plugin_options[ 'vehicle_reference_system' ][ 'host' ] ) ?
		$this->_plugin_options[ 'vehicle_reference_system' ][ 'host' ] :
		null;
	}

	#execute > _hook_into_wordpress > _setup_routing > add_rewrite_rules
	private function _set_ajax_rewrite_rule( &$rules ) {
		$this->_array_unshift_assoc( $rules , '^(dealertrend-ajax)' , 'index.php?taxonomy=dealertrend-ajax' );
	}

	#execute > _hook_into_wordpress > _setup_routing > add_rewrite_rules > _set_ajax_rewrite_rule
	private function _array_unshift_assoc( &$array , $key , $value ) {
		$array = array_reverse( $array , true );
		$array[ $key ] = $value;
		$array = array_reverse( $array , true );
		return count( $array );
	}

	#execute > _hook_into_wordpress > _setup_routing
	public function flush_rewrite_rules() {
		global $wp_rewrite;
		return $wp_rewrite->flush_rules();
	}

	#execute > _hook_into_wordpress > _setup_routing
	public function create_taxonomies() {
		$this->_register_vms_taxonomy();
		$this->_register_vrs_taxonomy();
		$this->_register_ajax_taxonomy();
	}

	#execute > _hook_into_wordpress > _setup_routing > create_taxonomies
	private function _register_vms_taxonomy() {
		if( $this->_get_vms_host() == '' || $this->_get_vms_company_id() == 0 ) {
			return false;
		}
		$labels = array(
			'name' => _x( 'Inventory' , 'taxonomy general name' ),
			'menu_name' => __( 'Inventory' )
		);
		$this->_register_generic_taxonomy( $labels , 'inventory' );
	}

	#execute > _hook_into_wordpress > _setup_routing > create_taxonomies > _register_vms_taxonomy
	private function _register_generic_taxonomy( $labels , $slug ) {
		register_taxonomy(
			'showcase',
			array( 'page' ),
			array(
				'hierarchical' => false,
				'labels' => $labels,
				'show_ui' => false,
				'query_var' => true,
				'rewrite' => array( 'slug' => $slug )
			)
		);
	}

	#execute > _hook_into_wordpress > _setup_routing > create_taxonomies
	private function _register_vrs_taxonomy() {
		if( $this->_get_vrs_host() == null ) {
			return false;
		}
		$labels = array(
			'name' => _x( 'Showcase' , 'taxonomy general name' ),
			'menu_name' => __( 'Showcase' )
		);
		$this->_register_generic_taxonomy( $labels , 'showcase' );
	}

	#execute > _hook_into_wordpress > _setup_routing > create_taxonomies
	private function _register_ajax_taxonomy() {
		$labels = array( 'name' => _x( 'DealerTrend AJAX' , 'taxonomy general name' ) );
		$this->_register_generic_taxonomy( $labels , 'dealertrend-ajax' );
	}

	#execute > _hook_into_wordpress
	private function _setup_objects() {
		if( $this->_get_vms_host() && $this->_get_vms_company_id() ) {
			$this->vehicle_managment_system = new Vehicle_Management_System();
			$this->dynamic_site_headers = new Dynamic_Site_Headers();
		}
		if( $this->_get_vrs_host() ) {
			$this->vehicle_reference_system = new Vehicle_Reference_System();
		}
		$this->inventory_ajax = new Ajax();
	}

	#execute > _hook_into_wordpress
	private function _setup_templates() {
		add_action( 'template_redirect' , array( &$this , 'show_theme' ) , 100 );
	}

	#execute > _hook_into_wordpress > _setup_templates
	public function show_theme() {
		switch( $this->_get_taxonomy() ) {
			case 'inventory':
				$this->_show_vms_theme();
			break;
			case 'showcase':
				$this->_show_vrs_theme();
			break;
			case 'dealertrend-ajax':
				$this->_show_ajax();
			break;
		}
	}

	#execute > _hook_into_wordpress > _setup_templates > show_theme
	protected function _get_taxonomy() {
		global $wp_query;
		return isset( $wp_query->query_vars[ 'taxonomy' ] ) ? $wp_query->query_vars[ 'taxonomy' ] : NULL;
	}

	#execute > _hook_into_wordpress > _setup_templates > show_theme
	private function _show_vms_theme() {
		if( $this->_get_vms_host() && $this->_get_vms_company_id() != 0 ) {
			$this->_fix_bad_wordpress_assumption();
			// add thing here to make widget logic work
			// http://codex.wordpress.org/Conditional_Tags
			// http://wordpress.org/extend/plugins/widget-logic/
			$this->_get_dynamic_site_headers();
			$this->_load_theme( $this->_get_vms_theme_path() );
			$this->_stop_wordpress();
		}
	}

	#execute > _hook_into_wordpress > _setup_templates > show_theme > _show_vms_theme
	private function _fix_bad_wordpress_assumption() {
		global $wp_query;
		$wp_query->is_home = false;
	}

	#execute > _hook_into_wordpress > _setup_templates > show_theme > _show_vms_theme
	private function _get_dynamic_site_headers() {
		$this->dynamic_site_headers->set_host( $this->_get_vms_host() );
	}

	#execute > _hook_into_wordpress > _setup_templates > show_theme > _show_vms_theme
	private function _load_theme( $theme_path ) {
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
	}

	#execute > _hook_into_wordpress > _setup_templates > show_theme > _show_vms_theme
	private function _get_vms_theme_path() {
		return dirname( __FILE__ ) . '/application/views/' . $this->_get_vms_theme_folder() . '/' . $this->_get_vms_theme_name();
	}

	#execute > _hook_into_wordpress > _setup_templates > show_theme > _show_vms_theme > _get_vms_theme_path
	private function _get_vms_theme_folder() {
		return ! $this->_is_mobile() ? 'inventory' : 'mobile';
	}

	#execute > _hook_into_wordpress > _setup_templates > show_theme > _show_vms_theme > _get_vms_theme_path > _get_vms_theme_folder
	private function _is_mobile() {
		global $wp_query;
		return isset( $wp_query->query_vars[ 'is_mobile' ] ) ? $wp_query->query_vars[ 'is_mobile' ] : false;
	}

	#execute > _hook_into_wordpress > _setup_templates > show_theme > _show_vms_theme > _get_vms_theme_path
	private function _get_vms_theme_name() {
		return
		! $this->_is_mobile() ?
		$this->_plugin_options[ 'vehicle_management_system' ][ 'theme' ][ 'name' ] :
		$this->_plugin_options[ 'vehicle_management_system' ][ 'mobile_theme' ][ 'name' ];
	}

	#execute > _hook_into_wordpress > _setup_templates > show_theme > _show_vms_theme
	private function _stop_wordpress( $taxonomy ) {
		do_action( 'shutdown' );
		wp_cache_close();
		exit;
	}

	#execute > _hook_into_wordpress > _setup_templates > show_theme
	private function _show_vrs_theme() {
		if( $this->_get_vrs_host() ) {
			$this->_fix_bad_wordpress_assumption();
			$this->_get_dynamic_site_headers();
			$this->_load_theme( $this->_get_vrs_theme_path() );
			$this->_stop_wordpress();
		}
	}

	#execute > _hook_into_wordpress > _setup_templates > show_theme > _show_vrs_theme
	private function _get_vrs_theme_path() {
		return dirname( __FILE__ ) . '/application/views/' . $this->_get_vrs_theme_folder() . '/' . $this->_get_vrs_theme_name();
	}

	#execute > _hook_into_wordpress > _setup_templates > show_theme > _show_vrs_theme > _get_vrs_theme_path
	private function _get_vrs_theme_folder() {
		return 'default';
	}

	#execute > _hook_into_wordpress > _setup_templates > show_theme > _show_vrs_theme > _get_vrs_theme_path
	private function _get_vrs_theme_name() {
		return $this->_plugin_options[ 'vehicle_reference_system' ][ 'theme' ][ 'name' ];
	}

	#execute > _hook_into_wordpress > _setup_templates > show_theme
	private function _show_ajax() {
		$this->fix_bad_wordpress_assumption();
		$this->inventory_ajax->execute();
		$this->stop_wordpress();
	}

#########

  protected function _get_plugin_information() {
    if( empty( $this->_plugin_information ) ) {
      $this->_plugin_information = $this->_load_plugin_header();
    }

    return $this->_plugin_information;
  }

  private function _load_plugin_header() {
    return get_file_data( $this->_get_master_file() , $this->_get_default_file_headers() , 'plugin' );
  }

  private function _get_default_file_headers() {
    return array ( 
      'Name' => 'Plugin Name',
      'PluginURI' => 'Plugin URI',
      'Version' => 'Version',
      'Description' => 'Description',
      'Author' => 'Author',
      'AuthorURI' => 'Author URI'
    );
  }

}

?>
