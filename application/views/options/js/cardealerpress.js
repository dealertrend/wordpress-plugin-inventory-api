jQuery(document).ready(function(){

	// If they want to abandon the uninstall, let's make that easy.
	jQuery('#uninstall').click(function(){
		jQuery('#uninstall-dialog').dialog();
		jQuery('#uninstall-dialog #uninstall-cancel').click(function(){
			jQuery('#uninstall-dialog').dialog('close');
			return false;
		});
		return false;
	});

	// Let's create awesome tabular stuff.
	jQuery('#option-tabs').tabs();

	// If they click on the settings ink on the options page - let's take them to our settings page.
	jQuery('#settings-link').click(function() {
		jQuery('#option-tabs').tabs('select', '#settings');
		return false;
	});

	jQuery("#option-tabs").bind("tabsshow", function(event, ui) {
		window.location.hash = ui.tab.hash;
	})

	// Controls tabs
	jQuery('.tab-button').click(function() {
		tab_name = jQuery(this).attr('name');
		jQuery(this).siblings('.active').removeClass('active');
		jQuery(this).parent().parent().find('.tab-content.active').removeClass('active');
		jQuery(this).addClass('active');
		jQuery('.tab-content-'+tab_name).addClass('active');
	});

	function implement_ui() {

		jQuery( 'select[multiple="multiple"]' ).each( function() {
		    var label;
		    var classes;
		    var instance =  jQuery( this );

		    if( instance.has( 'button' ).length ) {
		        return true;
		    }

		    if( instance.attr( 'name' ) == 'widget-vehicle_reference_system_widget[__i__][makes][]' ) {
		        return true;
		    }

		    if( instance.hasClass( 'vrs-makes' ) ) {
		        label = 'Select a Make';
		        classes = 'makes';
		    } else if( instance.hasClass( 'vrs-models' ) ) {
		        label = 'Select a Model';
		        classes = 'models';
			} else if( instance.hasClass( 'vms-makes' ) ) {
		        label = 'Select a Make';
		        classes = 'makes';
		    } else {
		        return true;
		    }

		    instance
		    .multiselect( {
		         noneSelectedText: label,
		         classes: classes,
		         selectedList: 4
		    } ) 
		    .multiselectfilter();

    	} );
	}

	jQuery( document ).ready( function() {
	    implement_ui();
	} );

	jQuery( document ).ajaxSuccess( function( evt, request, settings ) { 
		if ( settings != undefined ) {
			if( typeof( settings.data ) !== 'undefined' ) {
			    if( settings.data.search( '/id_base=vehicle_reference_system_widget.*action=save-widget/ig' ) ) {
			        implement_ui();
			    }
			}
		}
	});

	// Show Hidden Tables
	jQuery('.edit-table-button').click(function(){
		table_name = jQuery(this).attr('name');
		if( jQuery('#'+table_name+'.active').length ){
			jQuery('#'+table_name).removeClass('active');
		} else {
			jQuery('.hidden-table.active').removeClass('active');
			jQuery('#'+table_name).addClass('active');
		}

	});

	// Save Forms
	jQuery('.form-save-button').click(function(){
		form_name = jQuery(this).attr('name');
		jQuery('#'+form_name).submit();
	});

	// Inventory Tags
	jQuery('#inventory-add-tag').click(function(){
		current_v = jQuery('#inventory-tags-counter').val();
		jQuery('#inventory-tags-counter').val( parseInt(current_v) + 1 );
		id_value = 'inventory-tag-'+current_v;

		main_wrapper = '<div class="inventory-tags-wrapper '+id_value+'">';
		div_close = '</div>';
		remove_element = '<div id="'+id_value+'" class="tag-remove inventory-tag-remove '+id_value+'"><span>[x]</span></div>';

		input_name = '<div class="tag-name inventory-tag-value '+id_value+'"><input type="text" name="inventory_tag['+current_v+'][name]" id="inventory-tag-'+current_v+'-name" class="inventory-tag-text '+id_value+'" value="" /></div>';

		input_order = '<div class="tag-order inventory-tag-value '+id_value+'"><input type="number" name="inventory_tag['+current_v+'][order]" id="inventory-tag-'+current_v+'-order" class="inventory-tag-number '+id_value+'" value="" /></div>';

		input_upload = '<div class="tag-upload inventory-tag-value '+id_value+'"><a id="'+id_value+'" href="#" for="inventory_tag['+current_v+'][url]" class="custom_media_upload inventory-tag-label '+id_value+'">Upload</a><input id="inventory-tag-'+current_v+'-url" class="custom_media_url inventory-tag-text '+id_value+'" type="text" name="inventory_tag['+current_v+'][url]" value=""></div>';
		img_icon = '<div class="tag-icon '+id_value+'>"<img class="custom_media_image inventory-tag-label '+id_value+'" src="" /></div>';

		content = main_wrapper;
		content += input_name;
		content += input_order;
		content += input_upload;
		content += img_icon;
		content += remove_element;
		content += div_close;

		jQuery('#inventory-tags-td').append(content);

	});

	jQuery('#inventory-tags-td').on( 'click', '.custom_media_upload', function (e)  {
    	e.preventDefault();
		media_id = e.target.id;

    	var custom_uploader = wp.media({
    	    title: 'Upload Icon',
			width: 500,
			height: 500,
    	    button: {
    	        text: 'Apply Icon'
    	    },
    	    multiple: false  // Set this to true to allow multiple files to be selected
    	})
    	.on('select', function() {
    	    var attachment = custom_uploader.state().get('selection').first().toJSON();
    	    jQuery('.custom_media_image.'+media_id).attr('src', attachment.url);
    	    jQuery('.custom_media_url.'+media_id).val(attachment.url);
    	})
    	.open();
	});

	jQuery('#inventory-tags-td').on( 'click', '.inventory-tag-remove span', function (e) {
		message_id = jQuery(e.target).parent();
		jQuery('.' + message_id[0].id).remove();
	});


	// Apollo Theme Settings
	jQuery('#apollo-add-custom-message').click(function(){
		current_v = jQuery('#apollo-custom-message-counter').val();
		jQuery('#apollo-custom-message-counter').val( parseInt(current_v) + 1 );
		id_value = 'apollo-custom-message-'+current_v;

		main_wrapper = '<div class="custom-message-wrapper '+id_value+'">';
		div_close = '</div>';
		remove_element = '<div id="'+id_value+'" class="message-remove apollo-custom-message-remove '+id_value+'"><span>[x]</span></div>';

		input_count = '<div class="message-count custom-message-value '+id_value+'"><input type="number" name="vrs_theme_settings[apollo][custom_message][data]['+current_v+'][count]" id="apollo_custom_message_'+current_v+'_count" class="apollo_input_num '+id_value+'" value="0" /></div>';

		input_message = '<div class="message-text custom-message-value '+id_value+'"><textarea type="text" name="vrs_theme_settings[apollo][custom_message][data]['+current_v+'][message]" id="apollo_custom_message_'+current_v+'_message" class="'+id_value+'" /></div>';

		input_form = '<div class="message-form custom-message-value '+id_value+'"><input type="text" name="vrs_theme_settings[apollo][custom_message][data]['+current_v+'][form_title]" id="apollo_custom_message_'+current_v+'_count" class="'+id_value+'" /></div>';

		select_s = '<div class="message-operator custom-message-value '+id_value+'"><select name="vrs_theme_settings[apollo][custom_message][data]['+current_v+'][count_operator]" id="apollo_custom_message_['+current_v+']_count_operator" class="'+id_value+'" >';
		option_1 = '<option class="'+id_value+'" value=">" >&gt;</option>';
		option_2 = '<option class="'+id_value+'" value="<" >&lt;</option>';
		option_3 = '<option class="'+id_value+'" value=">=" >&ge;</option>';
		option_4 = '<option class="'+id_value+'" value="<=" >&le;</option>';
		option_5 = '<option class="'+id_value+'" value="=" >=</option>';
		option_6 = '<option class="'+id_value+'" value="!=" >&ne;</option>';
		select_e = '</select></div>';
		select_operator = select_s + option_1 + option_2 + option_3 + option_4 + option_5 + option_6 + select_e;

		content = main_wrapper;
		content += input_count;
		content += select_operator;
		content += input_message;
		content += input_form;
		content += remove_element;
		content += div_close;

		jQuery('#apollo-custom-message-td').append(content);

	});

	jQuery('#apollo-add-custom-video').click(function(){
		current_v = jQuery('#apollo-custom-video-counter').val();
		jQuery('#apollo-custom-video-counter').val( parseInt(current_v) + 1 );
		id_value = 'apollo-custom-video-'+current_v;

		main_wrapper = '<div class="custom-video-wrapper '+id_value+'">';
		div_close = '</div>';
		remove_element = '<div id="'+id_value+'" class="video-remove apollo-custom-video-remove '+id_value+'"><span>[x]</span></div>';

		input_make = '<div class="video-make custom-video-value '+id_value+'"><input type="text" name="vrs_theme_settings[apollo][custom_videos][data]['+current_v+'][make]" id="apollo_custom_video_'+current_v+'_make" class="apollo_input_text '+id_value+'" value="" /></div>';
		input_model = '<div class="video-model custom-video-value '+id_value+'"><input type="text" name="vrs_theme_settings[apollo][custom_videos][data]['+current_v+'][model]" id="apollo_custom_video_'+current_v+'_model" class="apollo_input_text '+id_value+'" value="" /></div>';
		input_url = '<div class="video-url custom-video-value '+id_value+'"><input type="text" name="vrs_theme_settings[apollo][custom_videos][data]['+current_v+'][url]" id="apollo_custom_video_'+current_v+'_url" class="apollo_input_text '+id_value+'" value="" /></div>';

		content = main_wrapper;
		content += input_make;
		content += input_model;
		content += input_url;
		content += remove_element;
		content += div_close;

		jQuery('#apollo-custom-video-td').append(content);

	});

	jQuery('#apollo-custom-message-td').on( 'click', '.apollo-custom-message-remove span', function (e) {
		message_id = jQuery(e.target).parent();
		jQuery('.' + message_id[0].id).remove();
	});

	jQuery('#apollo-custom-video-td').on( 'click', '.apollo-custom-video-remove span', function (e) {
		message_id = jQuery(e.target).parent();
		jQuery('.' + message_id[0].id).remove();
	});

	jQuery('#apollo-help-message').click(function(){
		jQuery('#apollo-help-dialog-box').dialog({
	        autoOpen: true,
	        height: 600,
	        width: 500,
			closeOnEscape: true,
			modal: true,
			resizeable: false,
			button: [
				{
					text: "Close",
					click: function(){
						jQuery(this).dialog( "close" );
					}
				}
			]
		})
	});

	// Gravity Forms
	jQuery('#inventory-add-form').click(function(){
		current_v = jQuery('#inventory-form-counter').val();
		theme_v = jQuery('#inventory-form-theme').val();
		jQuery('#inventory-form-counter').val( parseInt(current_v) + 1 );
		id_value = 'inventory-form-'+current_v;

		main_wrapper = '<div class="inventory-form-wrapper '+id_value+'">';
		div_close = '</div>';
		remove_element = '<div id="'+id_value+'" class="form-remove inventory-form-remove '+id_value+'"><span>[x]</span></div>';

		input_id = '<div class="form-id inventory-form-value '+id_value+'"><input type="number" name="custom_settings['+theme_v+'][gravity_form][data]['+current_v+'][id]" id="inventory-form-'+current_v+'-id" class="inventory-form-number '+id_value+'" value="" /></div>';

		input_button = '<div class="form-button inventory-form-value '+id_value+'"><input type="checkbox" name="custom_settings['+theme_v+'][gravity_form][data]['+current_v+'][button]" id="inventory-form-'+current_v+'-button" class="inventory-form-checkbox '+id_value+'" checked /></div>';

		input_title = '<div class="form-title inventory-form-value '+id_value+'"><input type="text" name="custom_settings['+theme_v+'][gravity_form][data]['+current_v+'][title]" id="inventory-form-'+current_v+'-title" class="inventory-form-text '+id_value+'" value="" /></div>';

		input_order = '<div class="form-saleclass inventory-form-value '+id_value+'"><select name="custom_settings['+theme_v+'][gravity_form][data]['+current_v+'][saleclass]" id="inventory-form-'+current_v+'-saleclass" class="inventory-form-number '+id_value+'" ><option value="0">All</option><option value="1">New</option><option value="2">Used</option></select></div>';

		content = main_wrapper;
		content += input_id;
		content += input_button;
		content += input_title;
		content += input_order;
		content += remove_element;
		content += div_close;

		jQuery('#inventory-form-td').append(content);

	});

	jQuery('#inventory-form-td').on( 'click', '.inventory-form-remove span', function (e) {
		message_id = jQuery(e.target).parent();
		jQuery('.' + message_id[0].id).remove();
	});

});
