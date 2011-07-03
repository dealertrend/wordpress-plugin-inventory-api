<?php

class vehicle_reference_system_widget extends WP_Widget {

	public $options = array();

	public $plugin_information = array();

	public $jquery_theme = NULL;

	function __construct() {
		parent::__construct( false , $name = 'Vehicle Reference System' , array( 'description' => 'A customizable widget to display vehicle reference information for research purposes. Feeds provided by DealerTrend, Inc.' ) );
		$plugin_options = get_option( 'dealertrend_inventory_api' );
		$this->jquery_theme = $plugin_options[ 'jquery' ][ 'ui' ][ 'theme' ];
		$plugin_file = pathinfo( __FILE__ );
		$this->plugin_information[ 'PluginURL' ] = WP_PLUGIN_URL . '/dealertrend-inventory-api';
		$this->plugin_information[ 'WidgetURL' ] = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__ ) , '' , plugin_basename( __FILE__ ) );
		if( !is_admin() ) {
			if( is_active_widget( false, $this->id , $this->id_base , true ) ) {
				add_action( 'wp_print_styles' , array( &$this , 'vrs_front_styles' ) , 1 );
				add_action( 'wp_print_scripts', array( &$this , 'vrs_front_scripts' ) , 1 );
			}
		} else {
			add_action( 'admin_print_styles' , array( &$this , 'vrs_admin_styles' ) , 1 );
			add_action( 'admin_print_scripts', array( &$this , 'vrs_admin_scripts' ) , 1 );
		}
		$this->load_options();
	}

	function vrs_admin_styles() {
		wp_enqueue_style(
			'jquery-ui-multiselect',
			$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui-multiselect-widget/1.10/css/jquery.multiselect.css',
			false,
			'1.10'
		);
		wp_enqueue_style(
			'jquery-ui-multiselect-filter',
			$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui-multiselect-widget/1.10/css/jquery.multiselect.filter.css',
			false,
			'1.10'
		);
		wp_enqueue_style(
			'jquery-ui-' . $this->jquery_theme,
			$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui/1.8.11/themes/' . $this->jquery_theme . '/jquery-ui.css',
			false,
			'1.8.11'
		);
	}

	function vrs_front_styles() {
		wp_enqueue_style(
			'dealertrend-inventory-api-vehicle-reference-system-widget',
			$this->plugin_information[ 'WidgetURL' ] . 'css/vehicle-reference-system-widget.css'
		);
		wp_enqueue_style(
			'jquery-ui-' . $this->jquery_theme,
			$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui/1.8.11/themes/' . $this->jquery_theme . '/jquery-ui.css',
			false,
			'1.8.11'
		);
	}

	function vrs_admin_scripts() {
		wp_enqueue_script(
			'jquery-ui-multiselect',
			$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui-multiselect-widget/1.10/js/jquery.multiselect.min.js',
			array( 'jquery' ),
			'1.10',
			true
		);
		wp_enqueue_script(
			'jquery-ui-multiselect-filter',
			$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui-multiselect-widget/1.10/js/jquery.multiselect.filter.min.js',
			array( 'jquery' , 'jquery-ui-multiselect' ),
			'1.10',
			true
		);
		wp_enqueue_script(
			'dealertrend-inventory-api-vehicle-reference-system-widget',
			$this->plugin_information[ 'WidgetURL' ] . 'js/vehicle-reference-system-admin.js',
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
			$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-carousel/0.9.8/js/jquery.carousel.min.js',
			array( 'jquery' ),
			'1.8.11',
			true
		);
		wp_enqueue_script(
			'dealertrend-inventory-api-vehicle-reference-system-widget',
			$this->plugin_information[ 'WidgetURL' ] . 'js/vehicle-reference-system-widget.js',
			array( 'jquery' , 'jquery-ui-core' , 'jquery-ui-tabs', 'jquery-carousel' ),
			false,
			true
		);
	}

	function widget( $args , $instance ) {
		global $wp_rewrite;

		extract( $args );

		$title = isset( $instance[ 'title' ] ) ? apply_filters( 'widget_title' , $instance[ 'title' ] ) : NULL;
		$layout = isset( $instance[ 'layout' ] ) ? $instance[ 'layout' ] : 'small';
		$float = isset( $instance[ 'float' ] ) && $instance[ 'float' ] == true ? 'float: ' . $instance[ 'float' ] . ';' : false;
		$carousel = isset( $instance[ 'carousel' ] ) && $instance[ 'carousel' ] == true ? 'carousel' : false;

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
		echo print_r( $vehicle_reference_system , true ) . "\n";
		echo '##################################################' . "\n";
		echo '-->' . "\n";

		echo '<div id="' . $this->id . '" class="vrs-widget ' . $layout . '" style="' . $float . '">';
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

			$current_year = date( 'Y' );
			$last_year = $current_year - 1;
			$next_year = $current_year + 1;

			$model_data[ $last_year ] = $vehicle_reference_system->get_models( array( 'make' => $make , 'year' => $last_year ) );
			$model_data[ $current_year ] = $vehicle_reference_system->get_models( array( 'make' => $make , 'year' => $current_year ) );
			$model_data[ $next_year ] = $vehicle_reference_system->get_models( array( 'make' => $make , 'year' => $next_year ) );

			$model_data[ $last_year ][ 'data' ] = is_array( $model_data[ $last_year ][ 'data' ] ) ? $model_data[ $last_year ][ 'data' ] : array();
			$model_data[ $current_year ][ 'data' ] = is_array( $model_data[ $current_year ][ 'data' ] ) ? $model_data[ $current_year ][ 'data' ] : array();
			$model_data[ $next_year ][ 'data' ] = is_array( $model_data[ $next_year ][ 'data' ] ) ? $model_data[ $next_year ][ 'data' ] : array();

			$model_data[ 'data' ] = array_merge( $model_data[ $last_year ][ 'data' ] , $model_data[ $current_year ][ 'data' ] , $model_data[ $next_year ][ 'data' ] );

			$i_can_haz_model = array();
			foreach( $model_data[ 'data' ] as $key => $value ) {
				$existing_data = array_search( $value->name , $i_can_haz_model );
				if( $existing_data === false ) {
					$i_can_haz_model[ $key ] = $value->name;
				} else {
					$model_data[ 'data' ][ $existing_data ] = $value;
					unset( $model_data[ 'data' ][ $key ] );
				}
			}

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
						echo '<span class="vrs-widget-thumbnail"><img src="' . $thumbnail . '" alt="' . $generic_vehicle_title . '" title="' . $generic_vehicle_title . '" /></span>';
						echo '<span class="vrs-widget-main-line">';
						echo '<span class="vrs-widget-make">' . $model->name . '</span>';
						echo '</span>';
						echo '</a>';
						echo '</div>';
					}
				}
			} else {
				echo '<div class="vrs-widget-item">';
					echo '<span class="vrs-widget-main-line">';
					echo '<p>Data Not Available.</p>';
					echo '</span>';
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
		$instance[ 'title' ] = isset( $new_instance[ 'title' ] ) ? strip_tags( $new_instance[ 'title' ] ) : NULL;
		$instance[ 'layout' ] = isset( $new_instance[ 'layout' ] ) ? $new_instance[ 'layout' ] : 'small';
		$instance[ 'float' ] = isset( $new_instance[ 'float' ] ) ? $new_instance[ 'float' ] : false;
		$instance[ 'carousel' ] = isset( $new_instance[ 'carousel' ] ) ? $new_instance[ 'carousel' ] : false;
		$instance[ 'makes' ] = isset( $new_instance[ 'makes' ] ) ? $new_instance[ 'makes' ] : array();
		$instance[ 'models' ] = isset( $new_instance[ 'models' ] ) ? $new_instance[ 'models' ] : array();

		return $instance;
	}

	function form( $instance ) {
		$title = isset( $instance[ 'title' ] ) ? esc_attr( $instance[ 'title' ] ) : NULL;
		$layout = isset( $instance[ 'layout' ] ) ? $instance[ 'layout' ] : 'small';
		$float = isset( $instance[ 'float' ] ) ? esc_attr( $instance[ 'float' ] ) : false;
		$carousel = isset( $instance[ 'carousel' ] ) ? $instance[ 'carousel' ] : false;
		$makes = isset( $instance[ 'makes' ] ) ? $instance[ 'makes' ] : array();
		$models = isset( $instance[ 'models' ] ) ? $instance[ 'models' ] : array();

		$vehicle_reference_system = new vehicle_reference_system(
			$this->options[ 'vehicle_reference_system' ][ 'host' ]
		);

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'title' ) . '">' . _e( 'Title:' ) . '</label>';
		echo '<input class="widefat" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . $title . '" />';
		echo '</p>';

		$current_year = date( 'Y' );
		$last_year = $current_year - 1;
		$next_year = $current_year + 1;

		$make_data[ $last_year ] = $vehicle_reference_system->get_makes( array( 'year' => $last_year ) );
		$make_data[ $current_year ] = $vehicle_reference_system->get_makes( array( 'year' => $current_year ) );
		$make_data[ $next_year ] = $vehicle_reference_system->get_makes( array( 'year' => $next_year ) );

		$make_data[ 'data' ] = array_merge( $make_data[ $last_year ][ 'data' ] , $make_data[ $current_year ][ 'data' ] , $make_data[ $next_year ][ 'data' ] );

		# It would be cool if there was a better way to do this.
		$i_can_haz_make = array();
		foreach( $make_data[ 'data' ] as $key => $value ) {
			$existing_data = array_search( $value->name , $i_can_haz_make );
			if( $existing_data === false ) {
				$i_can_haz_make[ $key ] = $value->name;
			} else {
				$make_data[ 'data' ][ $existing_data ] = $value;
				unset( $make_data[ 'data' ][ $key ] );
			}
		}

		$make_values = $make_data[ 'data' ];

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'makes' ) . '">' . _e( 'Makes:' ) . '</label>';
		echo '<select id="' . $this->get_field_id( 'makes' ) . '" name="' . $this->get_field_name( 'makes' ) . '[]" class="vrs-makes" size="4" multiple="multiple">';
		foreach( $make_values as $make ) {
			$selected = in_array( $make->name , $makes ) ? 'selected' : NULL;
			echo '<option value="' . $make->name . '" ' . $selected . '>' . $make->name . '</option>';
		}
		echo '</select>';
		echo '</p>';

		if( count( $makes ) > 0 ) {
			echo '<p>';
			echo '<label for="' . $this->get_field_id( 'models' ) . '">' . _e( 'Models:' ) . '</label>';
			echo '<select id="' . $this->get_field_id( 'models' ) . '" name="' . $this->get_field_name( 'models' ) . '[]" class="vrs-models" size="4" multiple="multiple">';
			foreach( $makes as $make ) {
				$model_data[ $last_year ] = $vehicle_reference_system->get_models( array( 'make' => $make , 'year' => $last_year ) );
				$model_data[ $current_year ] = $vehicle_reference_system->get_models( array( 'make' => $make , 'year' => $current_year ) );
				$model_data[ $next_year ] = $vehicle_reference_system->get_models( array( 'make' => $make , 'year' => $next_year ) );

				$model_data[ $last_year ][ 'data' ] = is_array( $model_data[ $last_year ][ 'data' ] ) ? $model_data[ $last_year ][ 'data' ] : array();
				$model_data[ $current_year ][ 'data' ] = is_array( $model_data[ $current_year ][ 'data' ] ) ? $model_data[ $current_year ][ 'data' ] : array();
				$model_data[ $next_year ][ 'data' ] = is_array( $model_data[ $next_year ][ 'data' ] ) ? $model_data[ $next_year ][ 'data' ] : array();

				$model_data[ 'data' ] = array_merge( $model_data[ $last_year ][ 'data' ] , $model_data[ $current_year ][ 'data' ] , $model_data[ $next_year ][ 'data' ] );

				# It would be cool if there was a better way to do this.
				$i_can_haz_model = array();
				foreach( $model_data[ 'data' ] as $key => $value ) {
					$existing_data = array_search( $value->name , $i_can_haz_model );
					if( $existing_data === false ) {
						$i_can_haz_model[ $key ] = $value->name;
					} else {
						$model_data[ 'data' ][ $existing_data ] = $value;
						unset( $model_data[ 'data' ][ $key ] );
					}
				}
				$model_values = $model_data[ 'data' ];
				echo '<optgroup label="' . $make . '">';
				foreach( $model_values as $model ) {
					$selected = in_array( $model->name , $models ) ? 'selected' : NULL;
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
		$float_values = array( 'none' , 'left' , 'right' );
		foreach( $float_values as $float_option ) {
			$selected = ( $float_option == $float ) ? 'selected' : NULL;
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
