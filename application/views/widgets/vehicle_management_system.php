<?php

class vehicle_management_system_widget extends WP_Widget {

	public $options = array();

	const limit = 20;

	public $sale_class = array(
		'all',
		'new',
		'used'
	);

	public $tags = array(
		NULL,
		'certified',
		'special',
		'gas-saver',
		'cherry-deal',
		'good-buy',
		'low-miles',
		'one-owner',
		'sale-pending',
		'custom-wheels',
		'hybrid',
		'local-trade-in',
		'moon-roof',
		'navigation',
		'priced-to-go',
		'rare',
		'under-blue-book',
		'wont-last'
	);

	public $plugin_information = array();

	public $jquery_theme = NULL;

	function __construct() {

		parent::__construct( false , $name = 'Vehicle Management System' , array( 'description' => 'A customizable widget to display inventory items in widget areas throughout your site. Feeds provided by DealerTrend, Inc.' ) );
		$this->plugin_information[ 'PluginURL' ] = plugins_url( '' , __FILE__ ) . '/dealertrend-inventory-api';
		$this->plugin_information[ 'WidgetURL' ] = plugins_url( '' , __FILE__ ) . '/' . str_replace( basename( __FILE__ ) , '' , plugin_basename( __FILE__ ) );
		$plugin_options = get_option( 'dealertrend_inventory_api' );
		$this->jquery_theme = $plugin_options[ 'jquery' ][ 'ui' ][ 'theme' ];

		if( !is_admin() ) {
			if( is_active_widget( false, $this->id , $this->id_base , true ) ) {
				add_action( 'wp_print_styles' , array( &$this , 'vms_styles' ) , 1 );
				add_action( 'wp_print_scripts', array( &$this , 'vms_scripts' ) , 1 );
			}
		}
		$this->load_options();

	}

	function widget( $args , $instance ) {
		global $wp_rewrite;

		extract( $args );

		$title = apply_filters( 'widget_title' , $instance[ 'title' ] );
		$layout = isset( $instance[ 'layout' ] ) ? $instance[ 'layout' ] : 'small';
		$float = isset( $instance[ 'float' ] ) ? 'float: ' . $instance[ 'float' ] . ';' : NULL;
		$carousel = isset( $instance[ 'carousel' ] ) && $instance[ 'carousel' ] != false ? 'carousel' : false;

		$vehicle_management_system = new vehicle_management_system(
			$this->options[ 'vehicle_management_system' ][ 'host' ],
			$this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ]
		);

		$check_host = $vehicle_management_system->check_host();
		if( $check_host[ 'status' ] == false ) {
			echo '<p>Unable to connect to API.</p>';
			return false;
		}

		$check_company_id = $vehicle_management_system->check_company_id();
		if( $check_company_id[ 'status' ] == false ) {
			echo '<p>Unable to validate company information.</p>';
			return false;
		}

		$company_information = $vehicle_management_system->get_company_information();
		$company_information = $company_information[ 'data' ];
		$state = $company_information->state;
		$city = $company_information->city;

		$check_inventory = $vehicle_management_system->check_inventory();

		if( $check_inventory[ 'status' ] == false && $check_inventory[ 'code' ] != 200 ) {
			echo '<p>Unable to retrieve inventory.</p>';
			return false;
		}

		$inventory = $vehicle_management_system->get_inventory(
			array(
				'photo_view' => 1,
				'per_page' => vehicle_management_system_widget::limit,
				'icons' => $instance[ 'tag' ],
				'saleclass' => $instance[ 'saleclass' ]
			)
		);

		if( $inventory === false ) {
			echo '<p>The inventory feed timed out while trying to display. Please refresh the page. If the feed refuses to return data, then the given parameters may be invalid.</p>';
			return false;
		}

		echo '<div id="' . $this->id . '" class="vms-widget ' . $layout .'" style="' . $float . '">';
		echo '<div class="vms-before-widget">' . $before_widget . '</div>';

		if( $title ) {
			echo '<div class="vms-widget-before-title">' . $before_title . '</div>';
			echo '<div class="vms-widget-title">' . $title . '</div>';
			echo '<div class="vms-widget-after-title">' . $after_title . '</div>';
		}
		$sale_class = isset( $instance[ 'saleclass' ] ) ? ucwords( $instance[ 'saleclass' ] ) : 'All';

		$content_block = NULL;
		foreach( $inventory as $inventory_item ) {
			setlocale( LC_MONETARY , 'en_US' );
			$prices = $inventory_item->prices;
			$use_was_now = $prices->{ 'use_was_now?' };
			$use_price_strike_through = $prices->{ 'use_price_strike_through?' };
			$on_sale = $prices->{ 'on_sale?' };
			$sale_price = isset( $prices->sale_price ) ? $prices->sale_price : NULL;
			$sale_expire = isset( $prices->sale_expire ) ? $prices->sale_expire : NULL;
			$retail_price = $prices->retail_price;
			$default_price_text = $prices->default_price_text;
			$asking_price = $prices->asking_price;
			$year = $inventory_item->year;
			$make = $inventory_item->make;
			$model = urldecode( $inventory_item->model_name );
			$vin = $inventory_item->vin;
			$trim = urldecode( $inventory_item->trim );
			$thumbnail = urldecode( $inventory_item->photos[ 0 ]->small );
			$body_style = $inventory_item->body_style;
			$drive_train = $inventory_item->drive_train;
			if( !empty( $wp_rewrite->rules ) ) {
				$inventory_url = site_url() . '/inventory/' . $year . '/' . $make . '/' . $model . '/' . $state . '/' . $city . '/'. $vin . '/';
			} else {
				$inventory_url = '?taxonomy=inventory&amp;saleclass=' . $sale_class . '&amp;make=' . $make . '&amp;model=' . $model . '&amp;state=' . $state . '&amp;city=' . $city . '&amp;vin='. $vin;
			}
			$generic_vehicle_title = $year . ' ' . $make . ' ' . $model;

			$price_block = NULL;

			$ais_incentive = isset( $inventory_item->ais_incentive->to_s ) ? $inventory_item->ais_incentive->to_s : NULL;
			$incentive_price = 0;
			preg_match( '/\$\d*\s/' , $ais_incentive , $incentive );
			$incentive_price = isset( $incentive[ 0 ] ) ? str_replace( '$' , NULL, $incentive[ 0 ] ) : 0;

			if( $on_sale && $sale_price > 0 ) {
				$now_text = 'Price: ';
				if( $use_was_now ) {
					$price_class = ( $use_price_strike_through ) ? 'vms-strike-through vms-asking-price' : 'vms-asking-price';
					if( $incentive_price > 0 ) {
						$price_block .= '<div class="' . $price_class . '">Was: ' . money_format( '%(#0n' , $sale_price ) . '</div>';
					} else {
						$price_block .= '<div class="' . $price_class . '">Was: ' . money_format( '%(#0n' , $asking_price ) . '</div>';
					}
					$now_text = 'Now: ';
				}
				if( $incentive_price > 0 ) {
					$price_block .= '<div class="vms-ais-incentive">Savings: ' . $ais_incentive . '</div>';
					$price_block .= '<div class="vms-sale-price">' . $now_text . money_format( '%(#0n' , $sale_price - $incentive_price ) . '</div>';
				} else {
					if( $ais_incentive != NULL ) {
						$price_block .= '<div class="vms-ais-incentive">Savings: ' . $ais_incentive . '</div>';
					}
					$price_block .= '<div class="vms-sale-price">' . $now_text . money_format( '%(#0n' , $sale_price ) . '</div>';
					if( $sale_expire != NULL ) {
						$price_block .= '<div class="vms-sale-expires">Sale Expires: ' . $sale_expire . '</div>';
					}
				}
			} else {
				if( $asking_price > 0 ) {
					if( $incentive_price > 0 ) {
						$price_block .= '<div class="vms-asking-price">Retail Price: ' . money_format( '%(#0n' , $asking_price ) . '</div>';
						$price_block .= '<div class="vms-ais-incentive">Savings: ' . $ais_incentive . '</div>';
						$price_block .= '<div class="vms-asking-price">Your Price: ' . money_format( '%(#0n' , $asking_price - $incentive_price ) . '</div>';
						} else {
							if( $ais_incentive != NULL ) {
								$price_block .= '<div class="vms-ais-incentive">Savings: ' . $ais_incentive . '</div>';
						}
						$price_block .= '<div class="vms-asking-price">Price: ' . money_format( '%(#0n' , $asking_price ) . '</div>';
					}
				} else {
					if( $ais_incentive != NULL ) {
						$price_block .= '<div class="vms-ais-incentive">Savings: ' . $ais_incentive . '</div>';
					}
					$price_block .= $default_price_text;
				}
			}

			$content_block .= '
				<div class="vms-widget-item">
					<a href="' . $inventory_url . '" title="' . $generic_vehicle_title . '">
						<img src="' . $thumbnail . '" alt="' . $generic_vehicle_title . '" title="' . $generic_vehicle_title . '" />
						<div class="vms-widget-main-line">
							<div class="vms-widget-year">' . $year . '</div>
							<div class="vms-widget-make">' . $make . '</div>
							<div class="vms-widget-model">' . $model . '</div>
						</div>
						<div class="vms-widget-price">
							' . $price_block . '
						</div>
					</a>
				</div>
			';
		}

		echo "\n" . '<!--' . "\n";
		echo '##################################################' . "\n";
		echo print_r( $this , true ) . "\n";
		echo print_r( $instance , true ) . "\n";
		echo print_r( $args , true ) . "\n";
		echo "[ Inventory Widget Information ]\n";
		echo print_r( $vehicle_management_system , true ) . "\n";
		echo '##################################################' . "\n";
		echo '-->' . "\n";

		echo '
			<div class="vms-widget-content ' . $carousel . '">
				<ul>
					<li><a href="#' .$this->id . '-content" title="Inventory">Inventory</a></li>
				</ul>
				<div id="' . $this->id . '-content" class="vms-widget-content-wrapper">
					<div class="vms-widget-item-wrapper">
						' . $content_block . '
					</div>
				</div>
			</div>
		';
		echo '<div class="vms-after-widget">' . $after_widget . '</div>';
		echo '</div>';
	}

	function vms_styles() {
		wp_enqueue_style(
			'dealertrend-inventory-api-vms-widget',
			$this->plugin_information[ 'WidgetURL' ] . 'css/vehicle-management-system-widget.css'
		);
		wp_enqueue_style(
			'jquery-ui-' . $this->jquery_theme,
			$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui/1.8.11/themes/' . $this->jquery_theme . '/jquery-ui.css',
			false,
			'1.8.11'
		);
	}

	function vms_scripts() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script(
			'jquery-carousel',
			$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-carousel/0.9.8/js/jquery.carousel.min.js',
			array( 'jquery' ),
			false,
			true
		);
		wp_enqueue_script(
			'dealertrend-inventory-api-vms-widget',
			$this->plugin_information[ 'WidgetURL' ] . 'js/vehicle-management-system-widget.js',
			array( 'jquery' , 'jquery-ui-core' , 'jquery-ui-tabs', 'jquery-carousel' ),
			false,
			true
		);
	}

	function update( $new_instance , $old_instance ) {
		$instance = $old_instance;
		$instance[ 'title' ] = isset( $new_instance[ 'title' ] ) ? strip_tags( $new_instance[ 'title' ] ) : NULL;
		$instance[ 'layout' ] = isset( $new_instance[ 'layout' ] ) ? $new_instance[ 'layout' ] : 'small';
		$instance[ 'float' ] = isset( $new_instance[ 'float' ] ) ? $new_instance[ 'float' ] : 'none';
		$instance[ 'saleclass' ] = isset( $new_instance[ 'saleclass' ] ) ?$new_instance[ 'saleclass' ] : 'all';
		$instance[ 'tag' ] = isset( $new_instance[ 'tag' ] ) ? $new_instance[ 'tag' ] : NULL;
		$instance[ 'carousel' ] = isset( $new_instance[ 'carousel' ] ) ? $new_instance[ 'carousel' ] : false;
		
		return $instance;
	}

	function form( $instance ) {

		$title = isset( $instance[ 'title' ] ) ? esc_attr( $instance[ 'title' ] ) : NULL;
		$layout = isset( $instance[ 'layout' ] ) ? $instance[ 'layout' ] : 'small';
		$float = isset( $instance[ 'float' ] ) ? esc_attr( $instance[ 'float' ] ) : 'none';
		$sale_class = isset( $instance[ 'saleclass' ] ) ? $instance[ 'saleclass' ] : 'all';
		$tag = isset( $instance[ 'tag' ] ) ? $instance[ 'tag' ] : NULL;
		$carousel = isset( $instance[ 'carousel' ] ) ? $instance[ 'carousel' ] : false;

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'title' ) . '">' . _e( 'Title:' ) . '</label>';
		echo '<input class="widefat" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . $title . '" />';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'carousel' ) . '">' . _e( 'Carousel:' ) . '</label>';
		$checked = ( $carousel == true ) ? 'checked="checked"' : NULL;
		echo '<input id="' . $this->get_field_id( 'carousel' ) . '" name="' . $this->get_field_name( 'carousel' ) . '" type="checkbox" ' . $checked . '	value="true" />';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'layout' ) . '">' . _e( 'Layout:' ) . '</label>';
		echo '<select id="' . $this->get_field_id( 'layout' ) . '" name="' . $this->get_field_name( 'layout' ) . '" class="vrs-layout">';
		$layout_options = array( 'small' , 'medium' , 'large' );
		foreach( $layout_options as $layout_possibility ) {
			$selected = $layout == $layout_possibility ? 'selected' : NULL;
			echo '<option value="' . $layout_possibility . '" ' . $selected . '>' . ucfirst( $layout_possibility ) . '</option>';
		}
		echo '</select>';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'float' ) . '">' . _e( 'Float:' ) . '</label>';
		echo '<select name="' . $this->get_field_name( 'float' ) . '" id="' . $this->get_field_id( 'float' ) . '">';
		$float_values = array( NULL , 'left' , 'right' );
		foreach( $float_values as $float_option ) {
			$selected = ( $float_option == $float ) ? 'selected' : NULL;
			echo '<option ' . $selected . ' value="' . $float_option . '">' . ucfirst( $float_option ) . '</option>';
		}
		echo '</select>';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'saleclass' ) . '">' . _e( 'Sale Class:' ) . '</label>';
		echo '<select name="' . $this->get_field_name( 'saleclass' ) . '" id="' . $this->get_field_id( 'saleclass' ) . '">';
		foreach( $this->sale_class as $sale_class_option ) {
			$selected = ( $sale_class_option == $sale_class ) ? 'selected' : NULL;
			echo '<option ' . $selected . ' value="' . $sale_class_option . '">' . ucfirst( $sale_class_option ) . '</option>';
		}
		echo '</select>';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'tag' ) . '">' . _e( 'Filter By:' ) . '</label>';
		echo '<select name="' . $this->get_field_name( 'tag' ) . '" id="' . $this->get_field_id( 'tag' ) . '">';
		foreach( $this->tags as $tag_option ) {
			$selected = ( $tag_option == $tag ) ? 'selected' : NULL;
			echo '<option ' . $selected . ' value="' . $tag_option . '">' . $tag_option . '</option>';
		}
		echo '</select>';
		echo '</p>';

	}

	function load_options() {
		$this->options = get_option( 'dealertrend_inventory_api' );
	}

}

?>
