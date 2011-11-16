<?php

	namespace WordPress\Plugins\DealerTrend\InventoryAPI;

	global $wp_rewrite;

	$site_url = site_url();

	wp_enqueue_style(
		'jquery-ui-' . $this->options[ 'jquery' ][ 'ui' ][ 'theme' ],
		$this->plugin_information[ 'PluginURL' ] . '/application/assets/jquery-ui/1.8.11/themes/' . $this->options[ 'jquery' ][ 'ui' ][ 'theme' ] . '/jquery-ui.css',
		false,
		'1.8.11'
	);
	wp_enqueue_style( 'dealertrend-showcase' , $this->plugin_information[ 'PluginURL' ] . '/application/views/showcase/default/css/showcase.css' , false );
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_script( 'jquery-ui-dialog' );
	wp_enqueue_script( 'jquery-ui-button' );
	wp_enqueue_script( 'dealertrend-showcase', $this->plugin_information[ 'PluginURL' ] . '/application/views/showcase/default/js/showcase.js', array( 'jquery-ui-tabs' ) );

	$ajax_nonce = wp_create_nonce( 'ajax!' );
	$parameters = $this->parameters;
	$query = '?' . http_build_query( $_GET );

	$make = isset( $parameters[ 'make' ] ) ? urldecode( $parameters[ 'make' ] ) : false;
	$model = isset( $parameters[ 'model' ] ) ? urldecode( $parameters[ 'model' ] ) : false;
	$trim = isset( $parameters[ 'trim' ] ) ? urldecode( $parameters[ 'trim' ] ) : false;

	$type = ( $make == false ) ? 'makes' : ( ( $model == false ) ? 'models' : 'trims' );

	if(
		( isset( $makes ) && $makes != false && ! in_array( $make , $this->options[ 'vehicle_reference_system' ][ 'data' ][ 'makes' ] ) ) ||
		( isset( $models ) && $models != false && ! in_array( $model , $this->options[ 'vehicle_reference_system' ][ 'data' ][ 'models' ] ) )
	) {
		status_header( 400 );
		$type = false;
	}

	get_header();

	$current_year = date( 'Y' );
	$last_year = $current_year - 1;
	$next_year = $current_year + 1;

?>
<div id="loader" style="display:none; height:50px; width:50px;"></div>
<script type="text/javascript">
function addCommas(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

		var dealertrend = jQuery.noConflict();
		dealertrend(document).ready(function () {
			dealertrend('#loader').hide();

		dealertrend( '#loader' ).dialog({
			autoOpen: false,
			modal: true,
			resizable: false,
			position: 'center',
			maxHeight: 50,
			maxWidth: 50,
			title: false
		});

			dealertrend( '#showcase .jquery-ui-button' ).button();
			dealertrend( '#showcase .jquery-ui-button' ).click( function(e) {
					button = dealertrend(this);
		 			dealertrend('#loader').dialog('open');

					button.css('cursor','wait');
					// trims


					dealertrend.ajax(
					{
						url: '/dealertrend-ajax/showcase/<?php echo $make; ?>/<?php echo $model; ?>/' + e.target.parentNode.id.replace( /\// , '_' ) + '/?_ajax_nonce=<?php echo $ajax_nonce; ?>&mode=default',
						context: document.body,
						success: function(data) {
							var json = JSON.parse(data);
							dealertrend( '#variation' ).html( 'TRIM: '+ json[ 0 ].name_variation );
							dealertrend( '#pricing #msrp' ).html( '$' + addCommas( json[ 0 ].msrp ) );
							dealertrend( '#trim' ).addClass( json[ 0 ].acode );
							var default_photo = json[ 0 ].images.medium.replace( /IMG=(.*)\.\w{3,4}/i , function(a, b) {
								return 'IMG=' + b + '.png';
							});
							dealertrend( '#spotlight' ).html('<img src="' + default_photo  + '" class="active" />');
							button.css('cursor','pointer');
							populate_page( json[ 0 ].acode );
						}
					});

					function populate_page( acode ) {
						button.css('cursor','wait');
						// fuel economy
						dealertrend.ajax(
						{
							url: '/dealertrend-ajax/showcase/<?php echo $make; ?>/<?php echo $model; ?>/' + e.target.parentNode.id.replace( /\// , '_' ) + '/?_ajax_nonce=<?php echo $ajax_nonce; ?>&mode=fuel_economy&acode=' + acode,
							context: document.body,
							success: function(data) {
								var json = JSON.parse(data);
								dealertrend( '#fuel #city .number' ).html( json[ 0 ].city_mpg );
								dealertrend( '#fuel #hwy .number' ).html( json[ 0 ].highway_mpg );
								button.css('cursor','pointer');
							}
						});

					dealertrend.ajax(
					{
						url: '/dealertrend-ajax/showcase/<?php echo $make; ?>/<?php echo $model; ?>/' + e.target.parentNode.id.replace( /\// , '_' ) + '/?_ajax_nonce=<?php echo $ajax_nonce; ?>&mode=colors&acode=' + acode,
						context: document.body,
						success: function(data) {
							var json = JSON.parse(data);
							var count = 0;
							var color_history = null;
							if( json.length > 0 ) {
								dealertrend( '#spotlight' ).html('');
								json.forEach( function( color ) {
									if( color.rgb != null ) {
										if( color_history == null || color_history.search( /color.rgb/i , color_history ) == -1 ) {
											color_history += '[' + color.rgb + ']';
											var image = document.createElement('img');
											var link = document.createElement('a');
											var text = document.createTextNode( color.name );
											link.href = '#' + color.code;
											link.title = 'Color: ' + color.name;
											link.id = '#swatch-' + color.code;
											link.className = 'swatch';
											link.style.backgroundColor = 'rgb(' + color.rgb + ')';
											link.appendChild(text);
											image.src = color.image_urls.medium.replace( /IMG=(.*)\.\w{3,4}/i , function(a, b) {
												return 'IMG=' + b + '.png';
											});
											image.id = color.code;
											if( count == 0 ) {
												image.className = 'active';
												link.className += ' active';
												var color_text = dealertrend( '#color-text' ).html( 'Color: ' + color.name );
												dealertrend( '#swatches' ).html( color_text );
												count++;
											}
											dealertrend( '#spotlight' ).append( image );
											dealertrend( '#swatches' ).append( link );
										}
									}
								});
							} else {
								
							}
		 					dealertrend('#loader').dialog('close');
				var current_image, next_image, color_text;
				color_text = dealertrend('#color-text');
				dealertrend('#swatches a').click(function (e) {
						current_image = dealertrend('#spotlight .active');
						next_image = dealertrend('#spotlight ' + e.target.hash);
						current_image.removeClass('active').hide();

						color_text.text(e.target.title);

						next_image.show().addClass('active');
						current_image = next_image;
						dealertrend('#swatches .active').removeClass('active');
						dealertrend('#' + e.target.id).addClass('active');
						e.preventDefault();
				});
				dealertrend('#swatches a').hover(function (e) {
						current_image = dealertrend('#spotlight .active');
						next_image = dealertrend('#spotlight ' + e.target.hash);
						current_image.removeClass('active').hide();
						next_image.show().addClass('active');
						color_text.text(e.target.title);
				}, function (e) {
						next_image.removeClass('active').hide();
						current_image.show().addClass('active');
						color_text.text(dealertrend('#swatches .active').attr('title'));
				});
						}
					});

				dealertrend.ajax(
				{
					url: '/dealertrend-ajax/showcase/<?php echo $make; ?>/<?php echo $model; ?>/' + e.target.parentNode.id.replace( /\// , '_' ) + '/?_ajax_nonce=<?php echo $ajax_nonce; ?>&mode=reviews&acode=' + acode,
					context: document.body,
					success: function( data ) {
					}
				});

				dealertrend.ajax(
				{
					url: '/dealertrend-ajax/showcase/<?php echo $make; ?>/<?php echo $model; ?>/' + e.target.parentNode.id.replace( /\// , '_' ) + '/?_ajax_nonce=<?php echo $ajax_nonce; ?>&mode=equipment&acode=' + acode,
					context: document.body,
					success: function( data ) {
					}
				});

				dealertrend.ajax(
				{
					url: '/dealertrend-ajax/showcase/<?php echo $make; ?>/<?php echo $model; ?>/' + e.target.parentNode.id.replace( /\// , '_' ) + '/?_ajax_nonce=<?php echo $ajax_nonce; ?>&mode=photos&acode=' + acode,
					context: document.body,
					success: function( data ) {
						var json = JSON.parse(data);
						json.forEach( function( photo ) {
							dealertrend('#trim #photos').append( '<img src="' + photo.image_urls.small + '" />' );
						});
					}
				});

					}
					e.preventDefault();
			} );

		});
</script>

<?php

	flush();

	if( $type ) {
		echo '<div id="showcase">';
		include( dirname( __FILE__ ) . '/' . $type . '.php' );
		echo '</div>';
	}

	echo "\n" . '<!--' . "\n";
	echo '##################################################' . "\n";
	echo print_r( $this , true ) . "\n";
	echo print_r( $vehicle_reference_system , true ) . "\n";
	echo '##################################################' . "\n";
	echo '-->' . "\n";

	flush();
	get_footer();
	flush();

?>
