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

jQuery( 'body' ).ajaxSuccess( function( evt, request, settings ) { 
	if ( settings != undefined ) {
		if( typeof( settings.data ) !== 'undefined' ) {
		    if( settings.data.search( '/id_base=vehicle_reference_system_widget.*action=save-widget/ig' ) ) {
		        implement_ui();
		    }
		}
	}
});

// Inventory Tags

	jQuery('#inventory-add-tag').click(function(){
		current_v = jQuery('#inventory-tags-counter').val();
		jQuery('#inventory-tags-counter').val( parseInt(current_v) + 1 );
		id_value = 'inventory-tag-'+current_v;

		main_wrapper = '<div class="inventory-tags-wrapper '+id_value+'">';
		sub_wrapper = '<div class="inventory-tag-value '+id_value+'">';
		div_close = '</div>';
		remove_element = '<div id="'+id_value+'" class="inventory-tag-remove '+id_value+'">Remove -</div>';

		label_name = '<label for="inventory_tag['+current_v+'][name]" class="inventory-tag-label '+id_value+'">Tag Name: </label>';
		input_name = '<input type="text" name="inventory_tag['+current_v+'][name]" id="inventory-tag-'+current_v+'-name" class="inventory-tag-text '+id_value+'" value="" />';
		label_order = '<label for="inventory_tag['+current_v+'][order]" class="inventory-tag-label '+id_value+'">Tag Order: </label>';
		input_order = '<input type="number" name="inventory_tag['+current_v+'][order]" id="inventory-tag-'+current_v+'-order" class="inventory-tag-number '+id_value+'" value="" />';
		label_url = '<a id="'+id_value+'" href="#" for="inventory_tag['+current_v+'][url]" class="custom_media_upload inventory-tag-label '+id_value+'">Upload</a>';
		img_url = '<img class="custom_media_image inventory-tag-label '+id_value+'" src="" />';
		input_url = '<input id="inventory-tag-'+current_v+'-url" class="custom_media_url inventory-tag-text '+id_value+'" type="text" name="inventory_tag['+current_v+'][url]" value="">';

		content = main_wrapper;
		content += remove_element;
		content += sub_wrapper + label_name + input_name + div_close;
		content += sub_wrapper + label_order + input_order + div_close;
		content += sub_wrapper + label_url + img_url + input_url + div_close;
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

	jQuery('#inventory-tags-td').on( 'click', '.inventory-tag-remove', function (e) {
		message_id = e.target.id;
		jQuery('.' + message_id).remove();
	});


// Apollo Theme Settings
	jQuery('#apollo-add-custom-message').click(function(){
		current_v = jQuery('#apollo-custom-message-counter').val();
		jQuery('#apollo-custom-message-counter').val( parseInt(current_v) + 1 );
		id_value = 'apollo-custom-message-'+current_v;

		main_wrapper = '<div class="custom-message-wrapper '+id_value+'">';
		sub_wrapper = '<div class="custom-message-value '+id_value+'">';
		div_close = '</div>';
		remove_element = '<div id="'+id_value+'" class="apollo-custom-message-remove '+id_value+'">Remove -</div>';

		label_count = '<label for="apollo_custom_message['+current_v+'][count]" class="apollo_label '+id_value+'">Value To Evaluate:</label>';
		input_count = '<input type="number" name="apollo_custom_message['+current_v+'][count]" id="apollo_custom_message_'+current_v+'_count" class="apollo_input_num '+id_value+'" value="0" />';
		label_message = '<label for="apollo_custom_message['+current_v+'][message]" class="apollo_label '+id_value+'">Message:</label>';
		input_message = '<textarea type="text" name="apollo_custom_message['+current_v+'][message]" id="apollo_custom_message_'+current_v+'_message" class="'+id_value+'" />';
		label_form = '<label for="apollo_custom_message['+current_v+'][form_title]" class="apollo_label '+id_value+'">Form Title:</label>';
		input_form = '<input type="text" name="apollo_custom_message['+current_v+'][form_title]" id="apollo_custom_message_'+current_v+'_count" class="'+id_value+'" />';

		label_operator = '<label for="apollo_custom_message['+current_v+'][count_operator]" class="apollo_label '+id_value+'" >Value Operator:</label>';
		select_s = '<select name="apollo_custom_message['+current_v+'][count_operator]" id="apollo_custom_message_['+current_v+']_count_operator" class="'+id_value+'" >';
		option_1 = '<option class="'+id_value+'" value=">" >&gt;</option>';
		option_2 = '<option class="'+id_value+'" value="<" >&lt;</option>';
		option_3 = '<option class="'+id_value+'" value=">=" >&ge;</option>';
		option_4 = '<option class="'+id_value+'" value="<=" >&le;</option>';
		option_5 = '<option class="'+id_value+'" value="=" >=</option>';
		option_6 = '<option class="'+id_value+'" value="!=" >&ne;</option>';
		select_e = '</select>';
		select_operator = select_s + option_1 + option_2 + option_3 + option_4 + option_5 + option_6 + select_e;

		content = main_wrapper;
		content += remove_element;
		content += sub_wrapper + label_count + input_count + div_close;
		content += sub_wrapper + label_operator + select_operator + div_close;
		content += sub_wrapper + label_message + input_message + div_close;
		content += sub_wrapper + label_form + input_form + div_close;
		content += div_close;

		jQuery('#apollo-custom-message-td').append(content);

	});

	jQuery('#apollo-add-custom-video').click(function(){
		current_v = jQuery('#apollo-custom-video-counter').val();
		jQuery('#apollo-custom-video-counter').val( parseInt(current_v) + 1 );
		id_value = 'apollo-custom-video-'+current_v;

		main_wrapper = '<div class="custom-video-wrapper '+id_value+'">';
		sub_wrapper = '<div class="custom-video-value '+id_value+'">';
		div_close = '</div>';
		remove_element = '<div id="'+id_value+'" class="apollo-custom-video-remove '+id_value+'">Remove -</div>';

		label_make = '<label for="apollo_custom_video['+current_v+'][make]" class="apollo_label '+id_value+'">Make:</label>';
		input_make = '<input type="text" name="apollo_custom_video['+current_v+'][make]" id="apollo_custom_video_'+current_v+'_make" class="apollo_input_text '+id_value+'" value="" />';
		label_model = '<label for="apollo_custom_video['+current_v+'][model]" class="apollo_label '+id_value+'">Model:</label>';
		input_model = '<input type="text" name="apollo_custom_video['+current_v+'][model]" id="apollo_custom_video_'+current_v+'_model" class="apollo_input_text '+id_value+'" value="" />';
		label_url = '<label for="apollo_custom_video['+current_v+'][url]" class="apollo_label '+id_value+'">Video URL:</label>';
		input_url = '<input type="text" name="apollo_custom_video['+current_v+'][url]" id="apollo_custom_video_'+current_v+'_url" class="apollo_input_text '+id_value+'" value="" />';

		content = main_wrapper;
		content += remove_element;
		content += sub_wrapper + label_make + input_make + div_close;
		content += sub_wrapper + label_model + input_model + div_close;
		content += sub_wrapper + label_url + input_url + div_close;
		content += div_close;

		jQuery('#apollo-custom-video-td').append(content);

	});

	jQuery('#apollo-custom-message-td').on( 'click', '.apollo-custom-message-remove', function (e) {
		message_id = e.target.id;
		jQuery('.' + message_id).remove();
	});

	jQuery('#apollo-custom-video-td').on( 'click', '.apollo-custom-video-remove', function (e) {
		message_id = e.target.id;
		jQuery('.' + message_id).remove();
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

});
