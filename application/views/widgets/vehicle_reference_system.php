<?php

class vehicle_reference_system_widget extends WP_Widget {

	public $options = array();

	public $plugin_information = array();

	public $jquery_theme = NULL;

	function __construct() {
		parent::__construct( false , $name = 'Vehicle Reference System' , array( 'description' => 'A customizable widget to display vehicle reference information for research purposes. Feeds provided by DealerTrend, Inc.' ) );
		$plugin_options = get_option( 'dealertrend_inventory_api' );
		$this->jquery_theme = $plugin_options[ 'jquery' ][ 'ui' ][ 'showcase-theme' ];
		$plugin_file = pathinfo( __FILE__ );
		$this->plugin_information[ 'PluginURL' ] = plugins_url( '' , __FILE__) . '/';
		$this->plugin_information[ 'PluginURL' ] = preg_replace( '/(\/application.*)/i' , NULL , $this->plugin_information[ 'PluginURL' ] );
		$this->plugin_information[ 'WidgetURL' ] = plugins_url( '' , __FILE__ ) . '/';
		if( ! is_admin() ) {
			if( is_active_widget( false, $this->id , $this->id_base , true ) ) {
				add_action( 'wp_print_styles' , array( &$this , 'vrs_front_styles' ) , 1 );
				add_action( 'wp_print_scripts', array( &$this , 'vrs_front_scripts' ) , 1 );
			}
		} else {
			if( ! ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'dealertrend_inventory_api' ) ) {
				add_action( 'admin_print_styles' , array( &$this , 'vrs_admin_styles' ) , 1 );
				add_action( 'admin_print_scripts', array( &$this , 'vrs_admin_scripts' ) , 1 );
			}
		}
		$this->load_options();
	}

	function vrs_admin_styles() {
		wp_enqueue_style(
			'jquery-ui-multiselect',
			$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui-multiselect-widget/1.14/css/jquery.multiselect.css',
			false,
			'1.14'
		);
		wp_enqueue_style(
			'jquery-ui-multiselect-filter',
			$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui-multiselect-widget/1.14/css/jquery.multiselect.filter.css',
			false,
			'1.14'
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
			$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui-multiselect-widget/1.14/js/jquery.multiselect.min.js',
			array( 'jquery' ),
			'1.14',
			true
		);
		wp_enqueue_script(
			'jquery-ui-multiselect-filter',
			$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui-multiselect-widget/1.14/js/jquery.multiselect.filter.min.js',
			array( 'jquery', 'jquery-ui-multiselect' ),
			'1.14',
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

		$settings = isset( $instance[ 'settings' ] ) ? $instance[ 'settings' ] : array();
		$title = isset( $settings[ 'title' ] ) ? apply_filters( 'widget_title' , empty( $settings[ 'title' ] ) ? '' : $settings[ 'title' ] , $settings , $this->id_base ) : NULL;
		$layout = isset( $settings[ 'layout' ] ) ? $settings[ 'layout' ] : 'small';
		$float = isset( $settings[ 'float' ] ) ? $settings[ 'float' ] : false;
		$carousel_flag = isset( $settings[ 'carousel' ] ) ? $settings[ 'carousel' ] : false;

		$showcase = isset( $instance[ 'showcase' ] ) ? $instance[ 'showcase' ] : array();
		$sc_setup = isset( $showcase[ 'setup' ] ) ? $showcase[ 'setup' ] : '';
		$sc_link = isset( $showcase[ 'link' ] ) ? $showcase[ 'link' ] : '';

		if( empty($sc_setup) ) {
			$data = isset( $instance[ 'data' ] ) ? $instance[ 'data' ] : array();
			$makes = isset( $data[ 'makes' ] ) ? $data[ 'makes' ] : array();
			$year_filter = isset( $data[ 'year_filter' ] ) ? $data[ 'year_filter' ] : 0;
			$years = $this->get_valid_years_w( $year_filter );
		} else {
			$admin_settings = $this->options[ 'vehicle_reference_system' ][ 'data' ];
			$data = array();
			$data['makes'] = $admin_settings['makes'];
			$data['year_filter'] = $admin_settings['year_filter'];
			$data['models'] = $admin_settings['models'];
			$data['models_next'] = $admin_settings['models_manual']['next'];
			$data['models_current'] = $admin_settings['models_manual']['current'];
			$makes = isset( $data[ 'makes' ] ) ? $data[ 'makes' ] : array();
			$year_filter = isset( $data[ 'year_filter' ] ) ? $data[ 'year_filter' ] : 0;
			$years = $this->get_valid_years_w( $year_filter );
		}

		if( !empty( $carousel_flag ) ){
			$carousel = 'carousel';
		}

		$country_code = $this->set_country_code();
		$vehicle_reference_system = new Wordpress\Plugins\Dealertrend\Inventory\Api\vehicle_reference_system(
			$this->options[ 'vehicle_reference_system' ][ 'host' ],
			$country_code
		);

		$check_feed = $vehicle_reference_system->check_feed()->please();
		if( $check_feed[ 'response' ][ 'code' ] != 200 ) {
			echo '<p>Unable to retrieve feed.</p>';
			return false;
		}

		echo '<div id="' . $this->id . '" class="vrs-widget ' . $layout . '" style="' . $float . '">';
		echo '<div class="vrs-before-widget">' . $before_widget . '</div>';
		if( ! empty( $title ) ) {
			echo '<div class="vrs-widget-title">' . $before_title . $title . $after_title . '</div>';
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

			$models = $this->get_model_array_w( $vehicle_reference_system, $years, $make, $year_filter, $data );

			echo '<div>';
			if( isset( $models ) && is_array( $models ) ) {
				foreach( $models as $model ) {
						if( empty($sc_link) ){
							if( !empty( $wp_rewrite->rules ) ) {
								$inventory_url = site_url() . '/inventory/New/' . $make . '/' . $model['name'] . '/';
							} else {
								$inventory_url = '?taxonomy=inventory&amp;saleclass=New&amp;make=' . $make . '&amp;model=' . $model['name'];
							}
						} else {
								$inventory_url = '/showcase/' . $make . '/' . $model['name'] . '/';
						}
						$generic_vehicle_title = $model['name'];
						$thumbnail = urldecode( $model['img'] );
						echo '<div class="vrs-widget-item">';
						echo '<a href="' . $inventory_url . '" title="' . $generic_vehicle_title . '">';
						echo '<span class="vrs-widget-thumbnail"><img src="' . $thumbnail . '" alt="' . $generic_vehicle_title . '" title="' . $generic_vehicle_title . '" /></span>';
						echo '<span class="vrs-widget-main-line">';
						echo '<span class="vrs-widget-make">' . $model['name'] . '</span>';
						echo '</span>';
						echo '</a>';
						echo '</div>';

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

		echo "\n" . '<!--' . "\n";
		echo '##################################################' . "\n";
		echo print_r( $this , true ) . "\n";
		echo print_r( $instance , true ) . "\n";
		echo print_r( $args , true ) . "\n";
		echo print_r( $vehicle_reference_system , true ) . "\n";
		echo '##################################################' . "\n";
		echo '-->' . "\n";
	}

	function update( $new_instance , $old_instance ) {
		$instance = $old_instance;
		$instance[ 'settings' ] = isset( $new_instance[ 'settings' ] ) ? $new_instance[ 'settings' ] : array();
		$instance[ 'showcase' ] = isset( $new_instance[ 'showcase' ] ) ? $new_instance[ 'showcase' ] : array();
		$instance[ 'data' ] = isset( $new_instance[ 'data' ] ) ? $new_instance[ 'data' ] : array();

		return $instance;
	}

	function form( $instance ) {

		$settings = isset( $instance[ 'settings' ] ) ? $instance[ 'settings' ] : array();
		$title = isset( $settings[ 'title' ] ) ? $settings[ 'title' ] : NULL;
		$layout = isset( $settings[ 'layout' ] ) ? $settings[ 'layout' ] : 'small';
		$float = isset( $settings[ 'float' ] ) ? $settings[ 'float' ] : false;
		$carousel = isset( $settings[ 'carousel' ] ) ? $settings[ 'carousel' ] : false;

		$showcase = isset( $instance[ 'showcase' ] ) ? $instance[ 'showcase' ] : array();
		$sc_setup = isset( $showcase[ 'setup' ] ) ? $showcase[ 'setup' ] : '';
		$sc_link = isset( $showcase[ 'link' ] ) ? $showcase[ 'link' ] : '';

		$data = isset( $instance[ 'data' ] ) ? $instance[ 'data' ] : array();
		$year_filter = isset( $data[ 'year_filter' ] ) ? $data[ 'year_filter' ] : 0;

		$country_code = $this->set_country_code();

		$vehicle_reference_system = new Wordpress\Plugins\Dealertrend\Inventory\Api\vehicle_reference_system(
			$this->options[ 'vehicle_reference_system' ][ 'host' ],
			$country_code
		);

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'settings' ) . '[title]">' . _e( 'Title:' ) . '</label>';
		echo '<input class="widefat" id="' . $this->get_field_id( 'settings' ) . '-title" name="' . $this->get_field_name( 'settings' ) . '[title]" type="text" value="' . $title . '" />';
		echo '</p>';

		echo '<hr>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'showcase' ) . '[setup] ">' . _e( 'Use Showcase Setup:' ) . '</label>';
		$checked = ( $sc_setup == true ) ? 'checked="checked"' : NULL;
		echo '<input  id="' . $this->get_field_id( 'showcase' ) . '-setup" name="' . $this->get_field_name( 'showcase' ) . '[setup]" type="checkbox" ' . $checked . ' value="true" />';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'showcase' ) . '[link] ">' . _e( 'Link to Showcase:' ) . '</label>';
		$checked = ( $sc_link == true ) ? 'checked="checked"' : NULL;
		echo '<input  id="' . $this->get_field_id( 'showcase' ) . '-link" name="' . $this->get_field_name( 'showcase' ) . '[link]" type="checkbox" ' . $checked . ' value="true" />';
		echo '</p>';

		if( empty($sc_setup) ){
			$years = $this->get_valid_years_w( $year_filter );
			$makes = $this->get_make_array_w( $vehicle_reference_system, $years );
			$makes = $this->remove_data_dups( $makes, 'name');
			natcasesort($makes);

			$selected_makes = isset( $data[ 'makes' ] ) ? $data[ 'makes' ] : array();

			echo '<p>';
			echo '<label for="' . $this->get_field_id( 'data' ) . '[makes][]">' . _e( 'Makes:' ) . '</label>';
			echo '<select id="' . $this->get_field_id( 'data' ) . '-makes" name="' . $this->get_field_name( 'data' ) . '[makes][]" class="vrs-makes" size="4" multiple="multiple">';
				$this->create_dd_w($makes, $selected_makes);
			echo '</select>';
			echo '</p>';

			if( count($selected_makes) > 0 ){
				echo '<p>';
				echo '<label for="' . $this->get_field_id( 'data' ) . '[year_filter]">' . _e( 'Year Filter:' ) . '</label>';
				echo '<select id="' . $this->get_field_id( 'data' ) . '-year-filter" name="' . $this->get_field_name( 'data' ) . '[year_filter]" class="vrs-layout">';
					echo '<option value="0" ' . ( ($year_filter == 0)?"selected":"" ) . ' >Default</option>';
					echo '<option value="1" ' . ( ($year_filter == 1)?"selected":"" ) . ' >Current Year Only</option>';
					echo '<option value="2" ' . ( ($year_filter == 2)?"selected":"" ) . ' >Manual Selection</option>';
				echo '</select>';
				echo '</p>';

				if( $year_filter != 2 ){
					echo '<label for="' . $this->get_field_id( 'data' ) . '[models][]">' . _e( 'Models:' ) . '</label>';
					echo '<select id="' . $this->get_field_id( 'data' ) . '-models" name="' . $this->get_field_name( 'data' ) . '[models][]" class="vrs-models" size="4" multiple="multiple">';
					foreach( $selected_makes as $make ){
						$models = $this->get_model_array_w( $vehicle_reference_system, $years, $make, FALSE, FALSE, TRUE );
						$models = $this->remove_data_dups( $models, 'name');
						natcasesort($models);
						$selected_models = isset( $data[ 'models' ] ) ? $data[ 'models' ] : array();

						echo '<optgroup label="' . $make . '">';
							$this->create_dd_w($models, $selected_models);
						echo '</optgroup>';
					}
					echo '</select>';
				} else {
					echo '<label for="' . $this->get_field_id( 'data' ) . '[models_next][]">' . _e( 'Next Model Year:' ) . '</label>';
					echo '<select id="' . $this->get_field_id( 'data' ) . '-models-next" name="' . $this->get_field_name( 'data' ) . '[models_next][]" class="vrs-models" size="4" multiple="multiple">';
					foreach( $selected_makes as $make ){
						$models = $this->get_model_array_w( $vehicle_reference_system, array( 0 => $years[0] ), $make, FALSE, FALSE, TRUE );
						$models = $this->remove_data_dups( $models, 'name');
						natcasesort($models);
						$selected_models_n = isset( $data[ 'models_next' ] ) ? $data[ 'models_next' ] : array();

						echo '<optgroup label="' . $make . '">';
							$this->create_dd_w($models, $selected_models_n);
						echo '</optgroup>';
					}
					echo '</select>';

					echo '<label for="' . $this->get_field_id( 'data' ) . '[models_current][]">' . _e( 'Current Model Year:' ) . '</label>';
					echo '<select id="' . $this->get_field_id( 'data' ) . '-models-current" name="' . $this->get_field_name( 'data' ) . '[models_current][]" class="vrs-models" size="4" multiple="multiple">';
					foreach( $selected_makes as $make ){
						$models = $this->get_model_array_w( $vehicle_reference_system, array( 0 => $years[1] ), $make, FALSE, FALSE, TRUE );
						$models = $this->remove_data_dups( $models, 'name');
						natcasesort($models);
						$selected_models_c = isset( $data[ 'models_current' ] ) ? $data[ 'models_current' ] : array();

						echo '<optgroup label="' . $make . '">';
							$this->create_dd_w($models, $selected_models_c);
						echo '</optgroup>';
					}
					echo '</select>';
				}
			}

		}

		echo '<hr>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'settings' ) . '[carousel]">' . _e( 'Carousel:' ) . '</label>';
		$checked = ( $carousel == true ) ? 'checked="checked"' : NULL;
		echo '<input id="' . $this->get_field_id( 'settings' ) . '-carousel" name="' . $this->get_field_name( 'settings' ) . '[carousel]" type="checkbox" ' . $checked . '	value="true" />';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'settings' ) . '[layout]">' . _e( 'Layout:' ) . '</label>';
		echo '<select id="' . $this->get_field_id( 'settings' ) . '-layout" name="' . $this->get_field_name( 'settings' ) . '[layout]" class="vrs-layout">';
		$layout_options = array( 'small' , 'medium' , 'large' );
		foreach( $layout_options as $layout_possibility ) {
			$selected = $layout == $layout_possibility ? 'selected' : NULL;
			echo '<option value="' . $layout_possibility . '" ' . $selected . '>' . ucfirst( $layout_possibility ) . '</option>';
		}
		echo '</select>';
		echo '</p>';

		echo '<p>';
		echo '<label for="' . $this->get_field_id( 'settings' ) . '[float]">' . _e( 'Float:' ) . '</label>';
		echo '<select name="' . $this->get_field_name( 'settings' ) . '[float]" id="' . $this->get_field_id( 'settings' ) . '-float">';
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

	function set_country_code() {
		$get_company_information = new Wordpress\Plugins\Dealertrend\Inventory\Api\vehicle_management_system(
			$this->options[ 'vehicle_management_system' ][ 'host' ],
			$this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ]
		);

		$check_company_id = $get_company_information->check_company_id()->please();
		if( $check_company_id[ 'response' ][ 'code' ] != 200 ) {
			echo '<p>Unable to validate company information.</p>';
			return false;
		}

		$company_information = $get_company_information->get_company_information()->please();
		$company_information = json_decode( $company_information[ 'body' ] );
		$country_code_value = $company_information->country_code;

		return $country_code_value;
	}

	function get_valid_years_w( $filter ){

		$current_year = date( 'Y' );

		switch( $filter ){
			case 0: // Default Next, Current and Past Year
				$value = array( ($current_year + 1), $current_year, ($current_year - 1) );
				break;
			case 1: // Current Year Only
				$value = array( $current_year );
				break;
			case 2: // Manual Next and Current Year
				$value = array( ($current_year + 1), $current_year );
				break;
		}

		return $value;
	}

	function get_make_array_w( $vrs_object, $years ){
		$value = array();
		foreach( $years as $year ){
			$temp = $vrs_object->get_makes()->please( array( 'year' => $year ) );
			$temp = isset( $temp[ 'body' ] ) ? json_decode( $temp[ 'body' ] ) : array();
			if( ! $this->is_Empty($temp) ) {
				$value = array_merge( $value, $temp );
			}
		}

		return $value;
	}

	function get_model_array_w( $vrs_object, $years, $make, $filter, $options, $backend = FALSE ){
		$value = array();
		foreach( $years as $year ){
			$temp = $vrs_object->get_models()->please( array( 'make' => $make , 'year' => $year ) );
			$temp = isset( $temp[ 'body' ] ) ? json_decode( $temp[ 'body' ] ) : array();
			if( empty( $backend ) ){
				if( !empty( $temp ) ) {
					foreach( $temp as $item ) {
						if( !$this->in_array_r_w( $item->name, $value ) ) {
							if( $this->check_selected_models_w( $item->name, $filter, $year, $options ) ){
								$value[] = array( 'name' => $item->name, 'class' => $item->classification, 'year' => $year, 'img' => $item->image_urls->small );
							}
						}
					}
				}
			} else {
				$value = array_merge( $value, $temp );
			}
		}

		return $value;
	}

	function check_selected_models_w( $item, $filter, $year, $options ){
		$check = false;
		switch( $filter ){
			case 2: // Manual Check
				if( $year != date( 'Y' ) ){
					( in_array( str_replace( '&', '&amp;', $item ), $options[ 'models_next' ] ) ) ? $check = true : $check = false;
				} else {
					( in_array( str_replace( '&', '&amp;', $item ), $options[ 'models_current' ] ) ) ? $check = true : $check = false;
				}
				break;

			default: // Default Check
				( in_array( str_replace( '&', '&amp;', $item ), $options[ 'models' ] ) ) ? $check = true : $check = false;
				break;
		}

		return $check;

	}

	function in_array_r_w($needle, $haystack, $strict = false) {
		foreach ($haystack as $item) {
		    if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && $this->in_array_r_w($needle, $item, $strict))) {
		        return true;
		    }
		}

		return false;
	}

	function create_dd_w( $data, $data_check ){
		foreach($data as $data_name){
			$selected = in_array( str_replace( '&', '&amp;', $data_name ) , $data_check ) ? 'selected' : NULL;
			echo '<option value="' . $data_name . '" ' . $selected . '>' . $data_name . '</option>';
		}
	}

	function remove_data_dups( $data, $name) {
		//Cleans data by removing duplicate entries
		$cleaned_data = array();
		foreach($data as $data_scrub){
			array_push($cleaned_data, $data_scrub->$name);
		}

		return array_unique($cleaned_data);
	}

	function is_Empty($obj){
		if( empty($obj) ){
			return true;
		} else if( is_numeric( $obj ) ){
			return false;
		}else if( is_string($obj) ){
			return !strlen(trim($obj));
		}else if( is_object($obj) ){
			return $this->is_Empty((array)$obj);
		}
		// It's an array!
		foreach($obj as $element)
			if ( $this->is_Empty($element)) continue; // so far so good.
			else return false;

		// all good.
		return true;
	}

}

?>
