	function vms_clear_dd( widget, value, text ){
		if( widget.find('.vms-sb-'+value).length ){
			widget.find('.vms-sb-'+value).children().remove();
			widget.find('.vms-sb-'+value).append('<option value="all" selected>Select a '+text+'</option>');
		}
	}

	function vms_dd_populate( widget, data ){
		dd_check = '';
		jQuery.each( data, function(key) {
			if( dd_check != key ){
				dd_check = key;
				dd_value = widget.find('.vms-sb-'+key);
				dd_value.children().remove();
			}

			add_all = false;

			if( data[key] ){
				jQuery.each( data[key], function(index, value) {
					if( data[key].length == 1 ){
						dd_value.append('<option value="'+value+'" selected>'+value+'</option>');
					} else if( !add_all ) {
						add_all = true;
						dd_value.append('<option value="all" selected>All</option>');
					}

					if( add_all ){
						dd_value.append('<option value="'+value+'">'+value+'</option>');
					}

				});
			} else {
				dd_value.append('<option value="all" selected>Not Available</option>');
			}
		});
	}

	jQuery(document).ready(function() {

		jQuery('#widgets-right').on( 'click', '.vms-color-picker', function(e){
			if( jQuery(e.target).attr('alt') == 'off' ){
				jQuery(e.target).attr('alt','on')
				jQuery(e.target).iris({
					hide: false,
					width: 220,
					palettes: true
				});
			} else {
				jQuery(e.target).iris('toggle');
			}
		});

		jQuery('.vms-sb-search-button').click(function(e){

	 		url = jQuery(e.target).attr('alt') + '/inventory/';
			param = '';

			condition = jQuery(e.target).siblings().find( jQuery('.vms-sb-salesclass') ).val();
			make = jQuery(e.target).siblings().find( jQuery('.vms-sb-makes') ).val();
			model = jQuery(e.target).siblings().find( jQuery('.vms-sb-models') ).val();
			trim = jQuery(e.target).siblings().find( jQuery('.vms-sb-trims') ).val();
			text = jQuery(e.target).siblings().find( jQuery('.vms-sb-text-input') ).val();

			if( condition ){
				url += condition + '/';
			}

			if( make && make != 'all' ){
				url += make + '/';
			}

			if( model && model != 'all' ){
				url += model + '/';
			}

			if( trim && trim != 'all' ){
				param = '?trim=' + trim;
			}

			if( text ){
				if( param ) {
					param += '&search=' + text;
				} else {
					param = '?search=' + text;
				}
			}

			window.location = url + param;
		});
		
		jQuery('.vms-search-box select').change( function(e) {
			p_data = {};

			p_data['id'] = jQuery(e.target).attr('name');
			parent_class = jQuery(e.target).parent().parent().attr('class');
			var parent_wrap = jQuery(e.target).parent().parent().parent();
			parent_wrap.find('.' + parent_class + ' select').each( function(){
				p_data[ jQuery(this).attr('name') ] = jQuery(this).val();
			})

			jQuery.ajax({
				url: ajax_path,
				data: {'action' : 'ajax_widget_request', 'fn': 'getSearchBox', 'data': p_data},
				dataType: 'json',
				beforeSend: function(){
					parent_wrap.attr('disabled', true);
					parent_wrap.find('.vms-sb-search-button').text('Loading...');
				},
				complete: function(){
					parent_wrap.attr('disabled', false);
					parent_wrap.find('.vms-sb-search-button').text('SEARCH');
				},
				success: function(data) {
					if( p_data['id'] == 'makes' || p_data['id'] == 'sc'  ){
						vms_clear_dd(parent_wrap, 'models', 'Make');
						vms_clear_dd(parent_wrap, 'trims', 'Model');
					} else if( p_data['id'] == 'models' ){
						vms_clear_dd(parent_wrap, 'trims', 'Model');
					}
					if( p_data['sc'] == 'Used'){
						parent_wrap.find('.vms-sb-certified').addClass('active');
					} else {
						parent_wrap.find('.vms-sb-certified').removeClass('active');
					}
					if( p_data['id'] != 'trims' ){
						vms_dd_populate( parent_wrap, data );
					}

				},
				error: function(xhr, status, error) {
					alert('Ajax call failed.');
   				}
			});
		});
		
	});


