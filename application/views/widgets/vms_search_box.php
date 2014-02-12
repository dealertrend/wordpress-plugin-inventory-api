<?php
/**********
	VMS Search Box
**********/
class vms_search_box_widget extends WP_Widget {
	// constructor
	function vms_search_box_widget() {
		parent::WP_Widget(false, $name = 'VMS Search Box', array( 'classname' => 'vms-search-box-widget', 'description' => 'Creates a VMS search box, which allows a user to search through the assigned VMS ID inventory.' ) );

		global $pagenow;

		wp_register_script(
			'vms-search-box-js' ,
			plugins_url( 'js/vms_search_box_widget.js' , __FILE__ ),
			array( 'jquery' ),
			1.0,
			true
		);

		wp_register_style(
			'vms-search-box-css',
			plugins_url( 'css/vms_search_box_widget.css' , __FILE__ ),
			false,
			1.0
		);

		if( is_admin() && $pagenow == 'widgets.php' ) {
			add_action( 'admin_enqueue_scripts', array( &$this , 'vms_enqueue_color_picker' ) );
			wp_enqueue_style('vms-search-box-css');
			wp_enqueue_script( 'vms-search-box-js' );
		}

	}

	function widget($arge, $instance) {

		extract( $arge );

		$ajax_path = admin_url('admin-ajax.php');
		$site_url = site_url();
		$ajax_widget = new ajax_widgets();

		wp_enqueue_style('vms-search-box-css');
		wp_enqueue_script( 'vms-search-box-js' );

		// Widget options
		$title = apply_filters('widget_title', $instance['title']);
		$trims = $instance['show_trims'];
		$text = $instance['show_text_search'];
		$display = $instance['salesclass_display'];
		$default = $instance['salesclass_default'];
		$colors = $instance['custom_colors'];

		//Get Custom Color Styles
		$title_style .= (isset($colors['title']['text']) ) ? ' color: ' . $colors['title']['text'] . ';': '';
		$title_style .= (isset($colors['title']['bg']) ) ? ' background-color: ' . $colors['title']['bg'] . ';' : '';
		$widget_style .= (isset($colors['widget']['bg']) ) ? ' background-color: ' . $colors['widget']['bg'] . ';' : '';
		$label_style .= (isset($colors['label']['text']) ) ? ' color: ' . $colors['label']['text'] . ';': '';
		$select_style .= (isset($colors['select']['text']) ) ? ' color: ' . $colors['select']['text'] . ';': '';
		$select_style .= (isset($colors['select']['bg']) ) ? ' background-color: ' . $colors['select']['bg'] . ';': '';
		$input_style .= (isset($colors['input']['text']) ) ? ' color: ' . $colors['input']['text'] . ';': '';
		$input_style .= (isset($colors['input']['bg']) ) ? ' background-color: ' . $colors['input']['bg'] . ';': '';
		$save_style .= (isset($colors['save']['text']) ) ? ' color: ' . $colors['save']['text'] . ';': '';
		$save_style .= (isset($colors['save']['bg']) ) ? ' background-color: ' . $colors['save']['bg'] . ';' : '';
		?>
		<script>
			ajax_path = '<?php echo $ajax_path ?>';

			jQuery(document).ready(function() {
				jQuery('.vms-search-box select').change( function() {
					p_data = {};

					p_data['id'] = jQuery(this).attr('name');
					parent_class = jQuery(this).parent().parent().attr('class');
					jQuery('.' + parent_class + ' select').each( function(){
						p_data[ jQuery(this).attr('name') ] = jQuery(this).val();
					})

					jQuery.ajax({
						url: ajax_path,
						data: {'action' : 'ajax_widget_request', 'fn': 'getSearchBox', 'data': p_data},
						dataType: 'json',
						beforeSend: function(){
							jQuery('.vms-search-box').attr('disabled', true)
							jQuery('.vms-sb-search-button').text('Loading...');
						},
						complete: function(){
							jQuery('.vms-search-box').attr('disabled', false)
							jQuery('.vms-sb-search-button').text('SEARCH');
						},
						success: function(data) {
							if( p_data['id'] == 'makes' || p_data['id'] == 'sc'  ){
								vms_clear_dd('models', 'Make');
								vms_clear_dd('trims', 'Model');
							} else if( p_data['id'] == 'models' ){
								vms_clear_dd('trims', 'Model');
							}
							if( p_data['sc'] == 'Used'){
								jQuery('.vms-sb-certified').addClass('active');
							} else {
								jQuery('.vms-sb-certified').removeClass('active');
							}
							if( p_data['id'] != 'trims' ){
								vms_dd_populate( data );
							}

						},
						error: function(xhr, status, error) {
							alert('Ajax call failed.');
		   				}
					});

				});
			});
		</script>
		<?php

		$salesclass = ( $display == 'both' ? $default : $display );

		$widget_content = '';

		$widget_content = $before_widget;

		$widget_content .= '<div class="vms-search-box" style="'.$widget_style.'">';

		//Display Title
		$widget_content .= '<div class="vms-sb-title" style="'.$title_style.'">' . $title . '</div>';

		//Display Salesclass/Condition
		$widget_content .= '<div class="vms-sb-salesclass-wrap">';
		$widget_content .= '<label class="vms-sb-salesclass-label" style="'.$label_style.'">Condition: </label>';
		$widget_content .= '<select class="vms-sb-salesclass" name="sc" style="'.$select_style.'">';
		if( $display == 'both' ){
			$widget_content .= '<option value="New" '. ($default == 'new' ? 'selected': '') .'>New</option>';
			$widget_content .= '<option value="Used" '. ($default == 'used' ? 'selected': '') .'>Used</option>';
		} else {
			$widget_content .= '<option value="'. ucfirst($display) .'" selected >'. ucfirst($display) .'</option>';
		}
		$widget_content .= '</select>';
		$widget_content .= '</div>';

		//Display Makes
		$widget_content .= '<div class="vms-sb-makes-wrap">';
		$widget_content .= '<label class="vms-sb-makes-label" style="'.$label_style.'">Makes: </label>';
		$widget_content .= '<select class="vms-sb-makes" name="makes" style="'.$select_style.'">';
			$makes = $ajax_widget->ajax_widgets_get_makes( $salesclass );
			$selected = ( count($makes) == 1 ? 'selected': '' );
			$widget_content .= ( count($makes) > 1 ? '<option value="all" selected >All</option>' : '' );
			foreach( $makes as $make ){
				$widget_content .= '<option value="'. $make .'" '. $selected .'>'. ucfirst($make) .'</option>';
			}
		$widget_content .= '</select>';
		$widget_content .= '</div>';

		//Display Models
		$widget_content .= '<div class="vms-sb-models-wrap">';
		$widget_content .= '<label class="vms-sb-models-label" style="'.$label_style.'">Models: </label>';
		$widget_content .= '<select class="vms-sb-models" name="models" style="'.$select_style.'">';
			if( count($makes) == 1 ){
				$models = $ajax_widget->ajax_widgets_get_models( $salesclass, $makes[0] );
				$selected = ( count($models) == 1 ? 'selected' : '' );
				$widget_content .= ( count($models) > 1 ? '<option value="all" selected >ALL</option>' : '' );
				foreach( $models as $model ){
					$widget_content .= '<option value="'. $model .'" '. $selected .'>'. ucfirst($model) .'</option>';
				}
			} else {
				$widget_content .= '<option value="all">Select a Make</option>';
			}
		$widget_content .= '</select>';
		$widget_content .= '</div>';

		//Display Trims
		if( !empty($trims) ){
			$widget_content .= '<div class="vms-sb-trims-wrap">';
			$widget_content .= '<label class="vms-sb-trims-label" style="'.$label_style.'">Trims: </label>';
			$widget_content .= '<select class="vms-sb-trims" name="trims" style="'.$select_style.'">';
				if( count($models) == 1 ){
					$trims = $ajax_widget->ajax_widgets_get_trims( $salesclass, $makes[0], $models[0] );
					$selected = ( count($trims) == 1 ? 'selected' : '' );
					$widget_content .= ( count($trims) > 1 ? '<option value="all" selected >All</option>' : '' );
					foreach( $models as $model ){
						$widget_content .= '<option value="'. $trim .'" '. $selected .'>'. ucfirst($trim) .'</option>';
					}
				} else {
					$widget_content .= '<option value="all">Select a Model</option>';
				}
			$widget_content .= '</select>';
			$widget_content .= '</div>';
		}

		//Display Text Search
		if( !empty($text) ){
			$widget_content .= '<div class="vms-sb-text-wrap">';
			$widget_content .= '<label class="vms-sb-text-label" style="'.$label_style.'">Text Search: </label>';
			$widget_content .= '<input type="text" class="vms-sb-text-input" value="" style="'.$input_style.'" />';
			$widget_content .= '</div>';

		}

		//Search Button
		$widget_content .= '<div alt="'.$site_url.'" class="vms-sb-search-button" style="'.$save_style.'">SEARCH</div>';

		$widget_content .= '</div>';

		$widget_content .= $after_widget;

		echo $widget_content;
	}


    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = isset( $new_instance['title'] ) ? strip_tags($new_instance['title']) : NULL;
		$instance['salesclass_display'] = isset( $new_instance['salesclass_display'] ) ? $new_instance['salesclass_display'] : 'both';
		$instance['salesclass_default'] = isset( $new_instance['salesclass_default'] ) ? $new_instance['salesclass_default'] : 'new';
		$instance['show_trims'] = isset( $new_instance['show_trims'] ) ? $new_instance['show_trims'] : 0;
		$instance['show_text_search'] = isset( $new_instance['show_text_search'] ) ? $new_instance['show_text_search'] : 0;
		$instance['custom_colors'] = isset( $new_instance['custom_colors'] ) ? $new_instance['custom_colors'] : array();

	    return $instance;
    }


	function form($instance) {

	    $title = esc_attr($instance['title']);
		$display = $instance['salesclass_display'];
		$default = $instance['salesclass_default'];
		$trims = $instance['show_trims'];
		$text = $instance['show_text_search'];
		$colors = $instance['custom_colors'];
    	?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Search Box Title'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<hr class="div" />
		<br>

		<p>
		<label for="<?php echo $this->get_field_id( 'salesclass_display' ); ?>"><?php _e( 'Sale Class Display:' ); ?></label>
		<select id="<?php echo $this->get_field_id( 'salesclass_display' ); ?>" name="<?php echo $this->get_field_name( 'salesclass_display' ); ?>">
		<?php
			$layout_options = array( 'both' , 'new' , 'used' );
			foreach( $layout_options as $layout_possibility ) {
				$selected = $display == $layout_possibility ? 'selected' : NULL;
				echo '<option value="' . $layout_possibility . '" ' . $selected . '>' . ucfirst( $layout_possibility ) . '</option>';

				( $display == 'both' ) ? $class = 'active' : $class = '';
			}
		?>
		</select>
		</p>

		<p class="saleclass-default <?php echo $class; ?>">
		<label for="<?php echo $this->get_field_id( 'salesclass_default' ); ?>"><?php _e( 'Sale Class Default:' ); ?></label>
		<select id="<?php echo $this->get_field_id( 'salesclass_default' ); ?>" name="<?php echo $this->get_field_name( 'salesclass_default' ); ?>">
		<?php
			$layout_options = array( 'new' , 'used' );
			foreach( $layout_options as $layout_possibility ) {
				$selected = $default == $layout_possibility ? 'selected' : NULL;
				echo '<option value="' . $layout_possibility . '" ' . $selected . '>' . ucfirst( $layout_possibility ) . '</option>';
			}
		?>
		</select>
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'show_trims' ); ?>"><?php _e( 'Show Trims:' ); ?></label>
		<?php $checked = ( $trims == true ) ? 'checked="checked"' : NULL; ?>
		<input id="<?php echo $this->get_field_id( 'show_trims' ); ?>" name="<?php echo $this->get_field_name( 'show_trims' ); ?>" type="checkbox" <?php echo $checked; ?>	value="true" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'show_text_search' ); ?>"><?php _e( 'Show Text Search:' ); ?></label>
		<?php $checked = ( $text == true ) ? 'checked="checked"' : NULL; ?>
		<input id="<?php echo $this->get_field_id( 'show_text_search' ); ?>" name="<?php echo $this->get_field_name( 'show_text_search' ); ?>" type="checkbox" <?php echo $checked; ?>	value="true" />
		</p>

		<hr class="div" />
		<div class="vms-cc-header"><?php _e( 'Custom Colors'); ?></div>
		<div class="vms-cc-wrap">
		<label class="vms-cc-label" for="<?php echo $this->get_field_id( 'custom_colors' ) . '[title][text]'; ?>"><?php _e( 'Title Text:'); ?></label>
		<input alt="off" id="<?php echo $this->get_field_id( 'custom_colors' ) . '-title-text'; ?>" name="<?php echo $this->get_field_name( 'custom_colors' ) . '[title][text]'; ?>" type="text" class="vms-color-picker vms-cc-input" value="<?php echo isset($colors['title']['text'] ) ? $colors['title']['text'] : '' ;?>" />
		</div>
		<div class="vms-cc-wrap">
		<label class="vms-cc-label" for="<?php echo $this->get_field_id( 'custom_colors' ) . '[title][bg]'; ?>"><?php _e( 'Title Background:'); ?></label>
		<input alt="off" id="<?php echo $this->get_field_id( 'custom_colors' ) . '-title-bg'; ?>" name="<?php echo $this->get_field_name( 'custom_colors' ) . '[title][bg]'; ?>" type="text" class="vms-color-picker vms-cc-input" value="<?php echo isset($colors['title']['bg'] ) ? $colors['title']['bg'] : '' ;?>" />
		</div>
		<div class="vms-cc-wrap">
		<label class="vms-cc-label" for="<?php echo $this->get_field_id( 'custom_colors' ) . '[widget][bg]'; ?>"><?php _e( 'Widget Background:'); ?></label>
		<input alt="off" id="<?php echo $this->get_field_id( 'custom_colors' ) . '-widget-bg'; ?>" name="<?php echo $this->get_field_name( 'custom_colors' ) . '[widget][bg]'; ?>" type="text" class="vms-color-picker vms-cc-input" value="<?php echo isset($colors['widget']['bg'] ) ? $colors['widget']['bg'] : '' ;?>" />
		</div>
		<div class="vms-cc-wrap">
		<label class="vms-cc-label" for="<?php echo $this->get_field_id( 'custom_colors' ) . '[label][text]'; ?>"><?php _e( 'Label Text:'); ?></label>
		<input alt="off" id="<?php echo $this->get_field_id( 'custom_colors' ) . '-label-text'; ?>" name="<?php echo $this->get_field_name( 'custom_colors' ) . '[label][text]'; ?>" type="text" class="vms-color-picker vms-cc-input" value="<?php echo isset($colors['label']['text'] ) ? $colors['label']['text'] : '' ;?>" />
		</div>
		<div class="vms-cc-wrap">
		<label class="vms-cc-label" for="<?php echo $this->get_field_id( 'custom_colors' ) . '[select][text]'; ?>"><?php _e( 'Select Text:'); ?></label>
		<input alt="off" id="<?php echo $this->get_field_id( 'custom_colors' ) . '-select-text'; ?>" name="<?php echo $this->get_field_name( 'custom_colors' ) . '[select][text]'; ?>" type="text" class="vms-color-picker vms-cc-input" value="<?php echo isset($colors['select']['text'] ) ? $colors['select']['text'] : '' ;?>" />
		</div>
		<div class="vms-cc-wrap">
		<label class="vms-cc-label" for="<?php echo $this->get_field_id( 'custom_colors' ) . '[select][bg]'; ?>"><?php _e( 'Select Background:'); ?></label>
		<input alt="off" id="<?php echo $this->get_field_id( 'custom_colors' ) . '-select-bg'; ?>" name="<?php echo $this->get_field_name( 'custom_colors' ) . '[select][bg]'; ?>" type="text" class="vms-color-picker vms-cc-input" value="<?php echo isset($colors['select']['bg'] ) ? $colors['select']['bg'] : '' ;?>" />
		</div>
		<div class="vms-cc-wrap">
		<label class="vms-cc-label" for="<?php echo $this->get_field_id( 'custom_colors' ) . '[input][text]'; ?>"><?php _e( 'Input Text:'); ?></label>
		<input alt="off" id="<?php echo $this->get_field_id( 'custom_colors' ) . '-input-text'; ?>" name="<?php echo $this->get_field_name( 'custom_colors' ) . '[input][text]'; ?>" type="text" class="vms-color-picker vms-cc-input" value="<?php echo isset($colors['input']['text'] ) ? $colors['input']['text'] : '' ;?>" />
		</div>
		<div class="vms-cc-wrap">
		<label class="vms-cc-label" for="<?php echo $this->get_field_id( 'custom_colors' ) . '[input][bg]'; ?>"><?php _e( 'Input Background:'); ?></label>
		<input alt="off" id="<?php echo $this->get_field_id( 'custom_colors' ) . '-input-bg'; ?>" name="<?php echo $this->get_field_name( 'custom_colors' ) . '[input][bg]'; ?>" type="text" class="vms-color-picker vms-cc-input" value="<?php echo isset($colors['input']['bg'] ) ? $colors['input']['bg'] : '' ;?>" />
		</div>
		<div class="vms-cc-wrap">
		<label class="vms-cc-label" for="<?php echo $this->get_field_id( 'custom_colors' ) . '[save][text]'; ?>"><?php _e( 'Save Text:'); ?></label>
		<input alt="off" id="<?php echo $this->get_field_id( 'custom_colors' ) . '-save-text'; ?>" name="<?php echo $this->get_field_name( 'custom_colors' ) . '[save][text]'; ?>" type="text" class="vms-color-picker vms-cc-input" value="<?php echo isset($colors['save']['text'] ) ? $colors['save']['text'] : '' ;?>" />
		</div>
		<div class="vms-cc-wrap">
		<label class="vms-cc-label" for="<?php echo $this->get_field_id( 'custom_colors' ) . '[save][bg]'; ?>"><?php _e( 'Save Background:'); ?></label>
		<input alt="off" id="<?php echo $this->get_field_id( 'custom_colors' ) . '-save-bg'; ?>" name="<?php echo $this->get_field_name( 'custom_colors' ) . '[save][bg]'; ?>" type="text" class="vms-color-picker vms-cc-input" value="<?php echo isset($colors['save']['bg'] ) ? $colors['save']['bg'] : '' ;?>" />
		</div>
		<?php
	}


	function vms_enqueue_color_picker( $hook_suffix ) {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'iris',
            admin_url( 'js/iris.min.js' ),
            array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ),
            false,
            1
		);
	}


}


?>
