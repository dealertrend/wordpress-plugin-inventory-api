<?php

if ( class_exists( 'inventory_widget' ) ) {
	return false;
}

/**
 * FooWidget Class
 */
class InventoryWidget extends WP_Widget {

	public $options = array();

	public $meta_information = array();

	const limit = 20;

	public $sale_class = array(
		'all',
		'new',
		'used'
	);

	public $tags = array(
		NULL,
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

	/** constructor */
	function __construct() {

		parent::__construct( false , $name = 'DealerTrend, Inc. VMS Widget' , array( 'description' => 'A customizable widget to display inventory items in widget areas throughout your site. Feeds provided by DealerTrend, Inc.' ) );

		if( !is_admin() ) {
			$plugin = new dealertrend_inventory_api;
			$this->meta_information = $plugin->meta_information;
			add_action( 'wp_print_styles' , array( &$this , 'inventory_styles' ) , 1 );
			add_action( 'wp_print_scripts', array( &$this , 'inventory_scripts' ) , 1 );
		}
		$this->load_options();

	}

	/** @see WP_Widget::widget */
	function widget( $args , $instance ) {

		global $wp_rewrite;

		extract( $args );

		$title = apply_filters( 'widget_title' , $instance[ 'title' ] );
		$width = isset( $instance[ 'width' ] ) ? 'width: ' . $instance[ 'width' ] . ';' : NULL;
		$height = isset( $instance[ 'height' ] ) ? 'height: ' . $instance[ 'height' ] . ';' : NULL;
		$float = isset( $instance[ 'float' ] ) ? 'float: ' . $instance[ 'float' ] . ';' : NULL;
		$carousel = isset( $instance[ 'carousel' ] ) ? 'carousel' : NULL;

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
				'per_page' => InventoryWidget::limit,
				'icons' => $instance[ 'tag' ],
				'saleclass' => $instance[ 'saleclass' ]
			)
		);

		if( $inventory === false ) {
			echo '<p>The inventory feed timed out while trying to display. Please refresh the page. If the feed refuses to return data, then the given parameters may be invalid.</p>';
			return false;
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

		echo '<div id="' . $this->id . '" class="inventory-widget" style="' . $width . $height . $float . '">';
		echo '<div class="inventory-before-widget">' . $before_widget . '</div>';
		if( $title ) {
			echo '<div class="inventory-widget-before-title">' . $before_title . '</div>';
			echo '<div class="inventory-widget-title">' . $title . '</div>';
			echo '<div class="inventory-widget-after-title">' . $after_title . '</div>';
		}
		echo '<div class="inventory-widget-content ' . $carousel . '">';
		echo '<div class="inventory-widget-item-wrapper ' . $carousel . '">';
		$sale_class = isset( $instance[ 'saleclass' ] ) ? ucwords( $instance[ 'saleclass' ] ) : 'All';
		foreach( $inventory as $inventory_item ) {
			setlocale( LC_MONETARY , 'en_US' );
			$prices = $inventory_item->prices;
			$use_was_now = $prices->{ 'use_was_now?' };
			$use_price_strike_through = $prices->{ 'use_price_strike_through?' };
			$on_sale = $prices->{ 'on_sale?' };
			$sale_price = isset( $prices->sale_price ) ? $prices->sale_price : NULL;
			$retail_price = $prices->retail_price;
			$default_price_text = $prices->default_price_text;
			$asking_price = $prices->asking_price;
			$year = $inventory_item->year;
			$make = $inventory_item->make;
			$model = urldecode( $inventory_item->model_name );
			$vin = $inventory_item->vin;
			$trim = urldecode( $inventory_item->trim );
			$engine = $inventory_item->engine;
			$transmission = $inventory_item->transmission;
			$exterior_color = $inventory_item->exterior_color;
			$interior_color = $inventory_item->interior_color;
			$stock_number = $inventory_item->stock_number;
			$odometer = $inventory_item->odometer;
			$icons = $inventory_item->icons;
			$thumbnail = urldecode( $inventory_item->photos[ 0 ]->small );
			$body_style = $inventory_item->body_style;
			$drive_train = $inventory_item->drive_train;
			$doors = $inventory_item->doors;
			$headline = $inventory_item->headline;
			if( !empty( $wp_rewrite->rules ) ) {
				$inventory_url = '/inventory/' . $year . '/' . $make . '/' . $model . '/' . $state . '/' . $city . '/'. $vin . '/';
			} else {
				$inventory_url = '?taxonomy=inventory&amp;saleclass=' . $sale_class . '&amp;make=' . $make . '&amp;model=' . $model . '&amp;state=' . $state . '&amp;city=' . $city . '&amp;vin='. $vin;
			}
			$generic_vehicle_title = $year . ' ' . $make . ' ' . $model;
			echo '<div class="inventory-widget-item">';
			echo '<a href="' . $inventory_url . '" title="' . $generic_vehicle_title . '">';
			echo '<div class="inventory-widget-thumbnail"><img src="' . $thumbnail . '" alt="' . $generic_vehicle_title . '" title="' . $generic_vehicle_title . '" /></div>';
			echo '<div class="inventory-widget-main-line">';
			echo '<div class="inventory-widget-model">' . $year . '</div>';
			echo '<div class="inventory-widget-model">' . $make . '</div>';
			echo '<div class="inventory-widget-model">' . $model . '</div>';
			echo '</div>';
			echo '<div class="inventory-widget-price">';
			if( $on_sale ) {
				$now_text = 'Price: ';
				if( $use_was_now ) {
					$price_class = ( $use_price_strike_through ) ? 'inventory-widget-strike-through inventory-widget-asking-price' : 'inventory-widget-asking-price';
					echo '<div class="' . $price_class . '">Was: ' . money_format( '%(#0n' , $asking_price ) . '</div>';
					$now_text = 'Now: ';
				}
				echo '<div class="inventory-widget-sale-price">' . $now_text . money_format( '%(#0n' , $sale_price ) . '</div>';
			} else {
				if( $asking_price > 0 ) {
					echo '<div class="inventory-widget-asking-price">Price: ' . money_format( '%(#0n' , $asking_price ) . '</div>';
				} else {
					echo $default_price_text;
				}
			}
			echo '</div>';
			echo '</a>';
			echo '</div>';
		}
		echo '</div>';
		echo '</div>';
		echo '<div class="inventory-after-widget">' . $after_widget . '</div>';
		echo '</div>';
	}

	function inventory_styles() {
		wp_register_style(
			'dealertrend-inventory-api-vms-widget',
			$this->meta_information[ 'PluginURL' ] . '/app/views/widgets/css/inventory-widget.css',
			false,
			$this->meta_information[ 'Version' ]
		);
		wp_enqueue_style( 'dealertrend-inventory-api-vms-widget' );
	}

	function inventory_scripts() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script(
			'jquery-carousel',
			'http://thomlx.free.fr/js/jquery.carousel.min.js',
			array( 'jquery' ),
			false,
			true
		);
		wp_enqueue_script(
			'dealertrend-inventory-api-vms-widget',
			$this->meta_information[ 'PluginURL' ] . '/app/views/widgets/js/inventory-widget.js',
			array( 'jquery' , 'jquery-ui-core' , 'jquery-ui-tabs', 'jquery-carousel' ),
			$this->meta_information[ 'Version' ],
			true
		);

	}

	/** @see WP_Widget::update */
	function update( $new_instance , $old_instance ) {
		$instance = $old_instance;
		$instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
		$instance[ 'width' ] = $new_instance[ 'width' ];
		$instance[ 'height' ] = $new_instance[ 'height' ];
		$instance[ 'float' ] = $new_instance[ 'float' ];
		$instance[ 'saleclass' ] = $new_instance[ 'saleclass' ];
		$instance[ 'tag' ] = $new_instance[ 'tag' ];
		$instance[ 'carousel' ] = $new_instance[ 'carousel' ];
		
		return $instance;
	}

	/** @see WP_Widget::form */
	function form( $instance ) {
		$title = isset( $instance[ 'title' ] ) ? esc_attr( $instance[ 'title' ] ) : NULL;
		$width = isset( $instance[ 'width' ] ) ? esc_attr( $instance[ 'width' ] ) : '310px';
		$height = isset( $instance[ 'height' ] ) ? esc_attr( $instance[ 'height' ] ) : '250px';
		$float = isset( $instance[ 'float' ] ) ? esc_attr( $instance[ 'float' ] ) : NULL;
		$sale_class = isset( $instance[ 'saleclass' ] ) ? $instance[ 'saleclass' ] : NULL;
		$tag = isset( $instance[ 'tag' ] ) ? $instance[ 'tag' ] : NULL;
		$carousel = isset( $instance[ 'carousel' ] ) ? $instance[ 'carousel' ] : NULL;

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'title' ) . '">' . _e( 'Title:' ) . '</label>';
		echo '<input class="widefat" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . $title . '" />';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'carousel' ) . '">' . _e( 'Carousel:' ) . '</label>';
		$checked = ( $carousel == true ) ? 'checked="checked"' : NULL;
		echo '<input id="' . $this->get_field_id( 'carousel' ) . '" name="' . $this->get_field_name( 'carousel' ) . '" type="checkbox" ' . $checked . '  value="true" />';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'width' ) . '">' . _e( 'Width:' ) . '</label>';
		echo '<input id="' . $this->get_field_id( 'width' ) . '" name="' . $this->get_field_name( 'width' ) . '" type="text" size="8" value="' . $width . '" />';
		echo '<br /><small>Use Ems, Pixels, Points or Percents.</small>';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'height' ) . '">' . _e( 'Height:' ) . '</label>';
		echo '<input id="' . $this->get_field_id( 'height' ) . '" name="' . $this->get_field_name( 'height' ) . '" type="text" size="8" value="' . $height . '" />';
		echo '<br /><small>Use Ems, Pixels, Points or Percents.</small>';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'float' ) . '">' . _e( 'Float:' ) . '</label>';
		echo '<select name="' . $this->get_field_name( 'float' ) . '" id="' . $this->get_field_id( 'float' ) . '">';
		$float_values = array( NULL , 'left' , 'right' );
		foreach( $float_values as $float_option ) {
			$selected = ( $float_option == $float ) ? 'selected="selected"' : NULL;
			echo '<option ' . $selected . ' value="' . $float_option . '">' . ucfirst( $float_option ) . '</option>';
		}
		echo '</select>';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'saleclass' ) . '">' . _e( 'Sale Class:' ) . '</label>';
		echo '<select name="' . $this->get_field_name( 'saleclass' ) . '" id="' . $this->get_field_id( 'saleclass' ) . '">';
		foreach( $this->sale_class as $sale_class_option ) {
			$selected = ( $sale_class_option == $sale_class ) ? 'selected="selected"' : NULL;
			echo '<option ' . $selected . ' value="' . $sale_class_option . '">' . ucfirst( $sale_class_option ) . '</option>';
		}
		echo '</select>';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'tag' ) . '">' . _e( 'Filter By:' ) . '</label>';
		echo '<select name="' . $this->get_field_name( 'tag' ) . '" id="' . $this->get_field_id( 'tag' ) . '">';
		foreach( $this->tags as $tag_option ) {
			$selected = ( $tag_option == $tag ) ? 'selected="selected"' : NULL;
			echo '<option ' . $selected . ' value="' . $tag_option . '">' . $tag_option . '</option>';
		}
		echo '</select>';
		echo '</p>';

	}

	function load_options() {
		$this->options = get_option( 'dealertrend_inventory_api' );
	}

}

# register FooWidget widget
add_action( 'widgets_init' , create_function( '' , 'return register_widget("InventoryWidget");' ) );

?>
