
jQuery(document).ready(function () {

	//Click - Trims
	jQuery('.trim-detail-variation').click(function (e) {
	    current_tab = jQuery('.trim-detail-variation.active');
		tab_id = e.target.id;

	    next_tab = jQuery('#' + tab_id);

	    current_tab.removeClass('active');
	    next_tab.addClass('active');

		//Transmissions -
		jQuery('.trim-transmissions.active').removeClass('active');
		jQuery('.trim-transmissions.' + tab_id).addClass('active');
		sub_id = jQuery('.trim-transmissions.' + tab_id + ' input.checked').val();

		//Trim Body -
	    jQuery('.trim-detail.active').removeClass('active');
	    jQuery('.trim-detail.' + sub_id).addClass('active');

		//custom message - form title - link
		jQuery('.showcase-count-message.active').removeClass('active');
		jQuery('.showcase-count-message.' + sub_id).addClass('active');
		jQuery('#showcase-trim-details .showcase-similar-link.active').removeClass('active');
		jQuery('#showcase-trim-details .showcase-similar-link.' + sub_id).addClass('active');
		jQuery('.trim-headline.form-message').text( jQuery('.hidden-form-message.' + sub_id).text() );
		jQuery('.showcase-trim-similar-wrapper.active').removeClass('active');
		jQuery('.showcase-trim-similar-wrapper.' + sub_id).addClass('active');
		move_video_player();

	    e.preventDefault();
	});

	//Click - Transmission
	jQuery('.trim-transmissions input').click(function (e) {
		current_radio = jQuery('.trim-transmissions.active input.checked');
		sub_id = e.target.value;

		current_radio.removeClass('checked');
		jQuery( e.target ).addClass('checked');

		//Trim Body -
	    jQuery('.trim-detail.active').removeClass('active');
	    jQuery('.trim-detail.' + sub_id).addClass('active');

		//custom message - form title - link
		jQuery('.showcase-count-message.active').removeClass('active');
		jQuery('.showcase-count-message.' + sub_id).addClass('active');
		jQuery('#showcase-trim-details .showcase-similar-link.active').removeClass('active');
		jQuery('#showcase-trim-details .showcase-similar-link.' + sub_id).addClass('active');
		jQuery('.trim-headline.form-message').text( jQuery('.hidden-form-message.' + sub_id).text() );
		jQuery('.showcase-trim-similar-wrapper.active').removeClass('active');
		jQuery('.showcase-trim-similar-wrapper.' + sub_id).addClass('active');
		move_video_player();

	});

	//Click - Swatches
	jQuery('.trim-detail .color-swatches a').click(function (e) {
	    current_image = jQuery('.trim-detail.active .trim-detail-img-wrap .active');
		swatch_name = e.target.name;
	    next_image = jQuery('.trim-detail.active .trim-detail-img-wrap .img-' + swatch_name);

	    current_image.removeClass('active');
	    jQuery('.trim-detail.active .color-text').text(e.target.title);
	    next_image.addClass('active');

	    current_image = next_image;
	    jQuery('.trim-detail.active .color-swatches .active').removeClass('active');
	    jQuery('.trim-detail.active .color-swatches .swatch-' + swatch_name).addClass('active');
	    e.preventDefault();
	});

	//Click - Tabs
	jQuery('.trim-detail .detail-tab').click(function (e) {
	    current_tab = jQuery('.trim-detail.active .detail-tab.active');
		tab_title = e.target.title;
	    next_tab = jQuery('.trim-detail.active .tab-' + tab_title);

	    current_tab.removeClass('active');
	    next_tab.addClass('active');

	    jQuery('.trim-detail.active .tab-info.active').removeClass('active');
	    jQuery('.trim-detail.active .tab-info-' + tab_title).addClass('active');
	    e.preventDefault();
	});

	//Hover - Swatches
	jQuery('.trim-detail .color-swatches a').hover(function (e) {
	    current_image = jQuery('.trim-detail.active .trim-detail-img-wrap .active');
		swatch_name = e.target.name;
	    next_image = jQuery('.trim-detail.active .trim-detail-img-wrap .img-' + swatch_name);
	    current_image.removeClass('active');
	    next_image.addClass('active');
	    jQuery('.trim-detail.active .color-text').text(e.target.title);
	}, function (e) {
	    next_image.removeClass('active');
	    current_image.addClass('active');
	    jQuery('.trim-detail.active .color-text').text(jQuery('.trim-detail.active .color-swatches .active').attr('title'));
	});

	//Click - Equipment
	jQuery('.trim-detail .tab-info .group > h4').click(function (e) {
		sib = jQuery( e.target ).siblings('ul'); 
		if( sib.attr('class') == 'active' ) {
			sib.removeClass('active');
		} else {
			sib.addClass('active');
		}
	});

	//Click - Form Data
	if( jQuery('#showcase-trim-details .form-showcase-data').length ){
		jQuery('.form-showcase-data textarea').attr('readonly', 'readonly');
		jQuery('#showcase-form-wrapper').click( function() {
			jQuery('.form-showcase-data').css({'display':'block'});
			make_v = jQuery('#trim-detail-make').text();
			model_v = jQuery('#trim-detail-model').text();
			trim_v = jQuery('#trim-detail-trim').text();
			year_v = jQuery('#trim-detail-year').text();
			trim_s_v = jQuery('.trim-detail-variation.active').html();
			trim_s_v = trim_s_v.replace('<br>', ' ');
			trans_v = jQuery('.trim-transmissions.active').find('.checked').siblings('.trim-transmission-value').html();
			variation_v = jQuery('.trim-transmissions.active').find('.checked').siblings('.trim-name-variation').html();
			color_v = jQuery('.trim-detail.active').find('.swatch.active').html();
			if( variation_v != '' ){
				variation_t = 'Variation: ' + variation_v + '\n';
			} else {
				variation_t = '';
			}
			output_v = 'Make: ' + make_v + '\n' + 'Model: ' + model_v + '\n' + 'Trim: ' + trim_v + '\n' + 'Type: ' + trim_s_v + '\n' + 'Trans: ' + trans_v + '\n' + 'Year: ' + year_v + '\n' + variation_t + 'Color: ' + color_v;
			jQuery('.gform_wrapper .form-showcase-data textarea').val( output_v );
		});
	}

	//Models page filter
	var sf_array = [];

	function toggle_filter( val ){

		if( jQuery.inArray(val, sf_array) != -1 ){
			sf_array.splice( jQuery.inArray(val, sf_array), 1);
		} else {
			sf_array.push( val );
		}
		display_toggle(sf_array);
	}

	jQuery('.filter-item input').click(function(){
		val = jQuery(this).attr('name');
		toggle_filter( val );
	});

	jQuery('.filter-item .clear-button').click(function(){
		sf_array = [];
		jQuery('.filter-item input').each(function(){
			jQuery(this).attr('checked',false);
		});
		display_toggle(sf_array);
	});

	if( jQuery('#showcase-detail').length ){
		// Apollo adjust Info height to match Img height
		function check_img_height() {
			img_height = jQuery('.trim-detail.active .trim-detail-img-wrap').height();
			info_height = jQuery('.trim-detail.active .trim-detail-info-wrap').height();

			if (img_height != info_height){
				jQuery('.trim-detail .trim-detail-info-wrap').css({'height':img_height});
			}
			setTimeout(check_img_height, 500);
		}
		check_img_height();
	}

	//Display Video Player
	function move_video_player(){
		if( jQuery('#video-check').length ){
			jQuery('.trim-detail.active .detail-tab.tab-video').css({'display':'block'});
			if( jQuery('.trim-detail.active .detail-tab.tab-video.active').length ){
				jQuery('.trim-detail.active .trim-detail-tab-info .tab-info.active').removeClass('active');
				jQuery('.trim-detail.active .trim-detail-tab-info').append( jQuery('.tab-info.tab-info-video') );
				jQuery('.trim-detail.active .tab-info.tab-info-video').addClass('active');
			} else {
				jQuery('.trim-detail.active .trim-detail-tab-info').append( jQuery('.tab-info.tab-info-video') );
				jQuery('.trim-detail.active .tab-info.tab-info-video').removeClass('active');
			}
		} else if( jQuery('.trim-detail.active .detail-tab.tab-video.active').length ) {
			jQuery('.trim-detail.active .detail-tab.tab-video.active').removeClass('active');
			jQuery('.trim-detail.active .detail-tab.tab-photo').addClass('active');
			jQuery('.trim-detail.active .tab-info.tab-info-photo').addClass('active');
		}
	}

	move_video_player();

	function getQueryParams(qs) {
		qs = qs.split("+").join(" ");
		var params = {},
		    tokens,
		    re = /[?&]?([^=]+)=([^&]*)/g;

		while (tokens = re.exec(qs)) {
		    params[decodeURIComponent(tokens[1])]
		        = decodeURIComponent(tokens[2]);
		}

		return params;
	}

	$_GET = getQueryParams(document.location.search);
	if( $_GET['filter'] ){
		toggle_filter( $_GET['filter'] );
	}
});

function display_toggle( data ) {
	if ( data.length === 0 ) {
		jQuery('.group.models > div').addClass('active');
	} else {
		jQuery('.group.models > div').removeClass('active');
		jQuery.each( data, function( key, value ) {
			jQuery('.group.models > div').each(function(){
				el = jQuery(this);
				val = jQuery(this).attr('class');
				if ( val.match( new RegExp(value) ) ) {
					el.addClass('active');
				}
			});
		});
	}
}


