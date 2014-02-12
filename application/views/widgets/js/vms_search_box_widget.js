	function vms_clear_dd( value, text ){
		if( jQuery('.vms-sb-'+value).length ){
			jQuery('.vms-sb-'+value).children().remove();
			jQuery('.vms-sb-'+value).append('<option value="all" selected>Select a '+text+'</option>');
		}
	}

	function vms_dd_populate( data ){
		dd_check = '';
		jQuery.each( data, function(key) {
			if( dd_check != key ){
				dd_check = key;
				dd_value = jQuery('.vms-sb-'+key);
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
	});


