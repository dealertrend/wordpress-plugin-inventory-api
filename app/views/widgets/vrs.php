<?php

class VehicleReferenceSystemWidget extends WP_Widget {

	public $options = array();

	public $meta_information = array();

	function __construct() {
		parent::__construct( false , $name = 'Vehicle Reference System' , array( 'description' => 'A customizable widget to display vehicle reference information for research purposes. Feeds provided by DealerTrend, Inc.' ) );
		$this->meta_information[ 'WidgetURL' ] =	WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ) , '' , plugin_basename( __FILE__ ) );
		if( is_admin() ) {
			add_action( 'admin_print_styles' , array( &$this , 'vrs_admin_styles' ) , 1 );
			add_action( 'admin_print_scripts', array( &$this , 'vrs_admin_scripts' ) , 1 );
		} else {
			add_action( 'wp_print_styles' , array( &$this , 'vrs_front_styles' ) , 1 );
			add_action( 'wp_print_scripts', array( &$this , 'vrs_front_scripts' ) , 1 );
		}
		$this->load_options();
	}

	function vrs_admin_styles() {
		wp_register_style(
			'jquery-ui-multiselect',
			'https://github.com/ehynds/jquery-ui-multiselect-widget/raw/1.10/jquery.multiselect.css'
		);
		wp_enqueue_style( 'jquery-ui-multiselect' );

		wp_register_style(
			'jquery-ui-multiselect-filter',
			'http://github.com/ehynds/jquery-ui-multiselect-widget/raw/master/jquery.multiselect.filter.css'
		);
		wp_enqueue_style( 'jquery-ui-multiselect-filter' );

		wp_register_style(
			'jquery-ui-black-tie',
			'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/black-tie/jquery-ui.css'
		);
		wp_enqueue_style( 'jquery-ui-black-tie' );

		wp_register_style(
			'dealertrend-inventory-api-vrs-widget',
			$this->meta_information[ 'WidgetURL' ] . 'css/vrs-admin.css'
		);
		wp_enqueue_style( 'dealertrend-inventory-api-vrs-widget' );
	}

	function vrs_front_styles() {
		wp_register_style(
			'dealertrend-inventory-api-vrs-widget',
			$this->meta_information[ 'WidgetURL' ] . 'css/vrs-widget.css'
		);
		wp_enqueue_style( 'dealertrend-inventory-api-vrs-widget' );

		wp_register_style(
			'jquery-ui-black-tie',
			'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.11/themes/black-tie/jquery-ui.css'
		);
		wp_enqueue_style( 'jquery-ui-black-tie' );
	}

	function vrs_admin_scripts() {
		wp_enqueue_script(
			'jquery-ui-multiselect',
			'http://github.com/ehynds/jquery-ui-multiselect-widget/raw/1.10/src/jquery.multiselect.min.js',
			array( 'jquery' ),
			false,
			true
		);

		wp_enqueue_script(
			'jquery-ui-multiselect-filter',
			'http://github.com/ehynds/jquery-ui-multiselect-widget/raw/master/src/jquery.multiselect.filter.js',
			array( 'jquery' , 'jquery-ui-multiselect' ),
			false,
			true
		);

		wp_enqueue_script(
			'dealertrend-inventory-api-vrs-widget',
			$this->meta_information[ 'WidgetURL' ] . 'js/vrs-admin.js',
			array( 'jquery' , 'jquery-ui-multiselect' , 'jquery-ui-multiselect-filter' ),
			false,
			true
		);
	}

	function vrs_front_scripts() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script(
			'jquery-carousel',
			$this->meta_information[ 'WidgetURL' ] . 'js/jquery.carousel.min.js',
			array( 'jquery' ),
			false,
			true
		);
		wp_enqueue_script(
			'dealertrend-inventory-api-vrs-widget',
			$this->meta_information[ 'WidgetURL' ] . 'js/vrs-widget.js',
			array( 'jquery' , 'jquery-ui-core' , 'jquery-ui-tabs', 'jquery-carousel' ),
			false,
			true
		);
	}

	function widget( $args , $instance ) {
		global $wp_rewrite;

		extract( $args );

		$title = apply_filters( 'widget_title' , $instance[ 'title' ] );
		$width = isset( $instance[ 'width' ] ) ? 'width: ' . $instance[ 'width' ] . ';' : NULL;
		$height = isset( $instance[ 'height' ] ) ? 'height: ' . $instance[ 'height' ] . ';' : NULL;
		$float = isset( $instance[ 'float' ] ) ? 'float: ' . $instance[ 'float' ] . ';' : NULL;
		$carousel = isset( $instance[ 'carousel' ] ) ? 'carousel' : NULL;
		$makes = isset( $instance[ 'makes' ] ) ? $instance[ 'makes' ] : array();
		$models = isset( $instance[ 'models' ] ) ? $instance[ 'models' ] : array();

		$vehicle_reference_system = new vehicle_reference_system(
			$this->options[ 'vehicle_reference_system' ][ 'host' ]
		);

		$check_host = $vehicle_reference_system->check_host();
		if( $check_host[ 'status' ] == false ) {
			echo '<p>Unable to connect to API.</p>';
			return false;
		}

		$check_feed = $vehicle_reference_system->check_feed();

		if( $check_feed[ 'status' ] == false && $check_feed[ 'code' ] != 200 ) {
			echo '<p>Unable to retrieve feed.</p>';
			return false;
		}

		echo "\n" . '<!--' . "\n";
		echo '##################################################' . "\n";
		echo print_r( $this , true ) . "\n";
		echo print_r( $instance , true ) . "\n";
		echo print_r( $args , true ) . "\n";
		echo "[ Inventory Widget Information ]\n";
		echo print_r( $vehicle_reference_system , true ) . "\n";
		echo '##################################################' . "\n";
		echo '-->' . "\n";

		echo '<div id="' . $this->id . '" class="vrs-widget" style="' . $width . $height . $float . '">';
		echo '<div class="vrs-before-widget">' . $before_widget . '</div>';
		if( $title ) {
			echo '<div class="vrs-widget-before-title">' . $before_title . '</div>';
			echo '<div class="vrs-widget-title">' . $title . '</div>';
			echo '<div class="vrs-widget-after-title">' . $after_title . '</div>';
		}
		echo '<div class="vrs-widget-content ' . $carousel . '">';
		echo '<div class="vrs-widget-item-wrapper ' . $carousel . '">';

		echo '<ul>';
		foreach( $makes as $make ) {
			echo '<li><a href="#vrs-' . $this->id . '-' . preg_replace( '/(\W+)/i' , '_' , $make ) . '">' . $make . '</a></li>';
		}
		echo '</ul>';
		foreach( $makes as $make ) {
			echo '<div id="vrs-' . $this->id . '-' . preg_replace( '/(\W+)/i' , '_' , $make ) . '" class="vrs-widget items ' . $carousel . '">';
			$model_data = $vehicle_reference_system->get_models( array( 'make' => $make ) );
			$model_values = $model_data[ 'data' ];
			echo '<div>';
			if( isset( $model_values ) && is_array( $model_values) ) {
				foreach( $model_values as $model ) {
					if( in_array( $model->name , $instance[ 'models' ] ) ) {
						if( !empty( $wp_rewrite->rules ) ) {
							$inventory_url = site_url() . '/inventory/New/' . $make . '/' . $model->name . '/'; 
						} else {
							$inventory_url = '?taxonomy=inventory&amp;saleclass=New&amp;make=' . $make . '&amp;model=' . $model->name;
						}
						$generic_vehicle_title = $model->name;
						$thumbnail = urldecode( $model->image_urls->small );
						echo '<div class="vrs-widget-item">';
						echo '<a href="' . $inventory_url . '" title="' . $generic_vehicle_title . '">';
						echo '<div class="vrs-widget-thumbnail"><img src="' . $thumbnail . '" alt="' . $generic_vehicle_title . '" title="' . $generic_vehicle_title . '" /></div>';
						echo '<div class="vrs-widget-main-line">';
						echo '<div class="vrs-widget-make">' . $model->name . '</div>';
						echo '</div>';
						echo '</a>';
						echo '</div>';
					}
				}
			} else {
				echo '<div class="vrs-widget-item">';
					echo '<div class="vrs-widget-main-line">';
					echo '<p>Data Not Available.</p>';
					echo '</div>';
				echo '</div>';
			}
			echo '</div>';
			echo '</div>';
		}
		echo '</div>';
		echo '</div>';
		echo '<div class="vrs-after-widget">' . $after_widget . '</div>';
		echo '</div>';
	}

	function update( $new_instance , $old_instance ) {
		$instance = $old_instance;
		$instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
		$instance[ 'width' ] = $new_instance[ 'width' ];
		$instance[ 'height' ] = $new_instance[ 'height' ];
		$instance[ 'float' ] = $new_instance[ 'float' ];
		$instance[ 'carousel' ] = $new_instance[ 'carousel' ];
		$instance[ 'makes' ] = $new_instance[ 'makes' ];
		$instance[ 'models' ] = $new_instance[ 'models' ];

		return $instance;
	}

	function form( $instance ) {
		$title = isset( $instance[ 'title' ] ) ? esc_attr( $instance[ 'title' ] ) : NULL;
		$width = isset( $instance[ 'width' ] ) ? esc_attr( $instance[ 'width' ] ) : '310px';
		$height = isset( $instance[ 'height' ] ) ? esc_attr( $instance[ 'height' ] ) : '250px';
		$float = isset( $instance[ 'float' ] ) ? esc_attr( $instance[ 'float' ] ) : NULL;
		$carousel = isset( $instance[ 'carousel' ] ) ? $instance[ 'carousel' ] : NULL;
		$makes = isset( $instance[ 'makes' ] ) ? $instance[ 'makes' ] : array();
		$models = isset( $instance[ 'models' ] ) ? $instance[ 'models' ] : array();

		$vehicle_reference_system = new vehicle_reference_system(
			$this->options[ 'vehicle_reference_system' ][ 'host' ]
		);

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'title' ) . '">' . _e( 'Title:' ) . '</label>';
		echo '<input class="widefat" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . $title . '" />';
		echo '</p>';

		$make_data = $vehicle_reference_system->get_makes();
		$make_values = $make_data[ 'data' ];

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'makes' ) . '">' . _e( 'Makes:' ) . '</label>';
		echo '<select id="' . $this->get_field_id( 'makes' ) . '" name="' . $this->get_field_name( 'makes' ) . '[]" class="vrs-makes" size="4" multiple="multiple">';
		foreach( $make_values as $make ) {
			$selected = in_array( $make->name , $makes ) ? 'selected="selected"' : NULL;
			echo '<option value="' . $make->name . '" ' . $selected . '>' . $make->name . '</option>';
		}
		echo '</select>';
		echo '</p>';

		if( count( $makes ) > 0 ) {
			echo '<p>';
			echo '<label for="' . $this->get_field_id( 'models' ) . '">' . _e( 'Models:' ) . '</label>';
			echo '<select id="' . $this->get_field_id( 'models' ) . '" name="' . $this->get_field_name( 'models' ) . '[]" class="vrs-models" size="4" multiple="multiple">';
			foreach( $makes as $make ) {
				$model_data = $vehicle_reference_system->get_models( array( 'make' => $make ) );
				$model_values = $model_data[ 'data' ];
				echo '<optgroup label="' . $make . '">';
				foreach( $model_values as $model ) {
					$selected = in_array( $model->name , $models ) ? 'selected="selected"' : NULL;
					echo '<option value="' . $model->name . '" ' . $selected . '>' . $model->name . '</option>';
				}
				echo '</optgroup>';
			}
			echo '</select>';
			echo '</p>';
		}
		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'carousel' ) . '">' . _e( 'Carousel:' ) . '</label>';
		$checked = ( $carousel == true ) ? 'checked="checked"' : NULL;
		echo '<input id="' . $this->get_field_id( 'carousel' ) . '" name="' . $this->get_field_name( 'carousel' ) . '" type="checkbox" ' . $checked . '	value="true" />';
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
	}

	function load_options() {
		$this->options = get_option( 'dealertrend_inventory_api' );
	}

}

?>
