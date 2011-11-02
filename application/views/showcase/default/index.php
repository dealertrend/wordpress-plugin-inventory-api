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
	wp_enqueue_script( 'jquery-ui-button' );
	wp_enqueue_script( 'dealertrend-showcase', $this->plugin_information[ 'PluginURL' ] . '/application/views/showcase/default/js/showcase.js', array( 'jquery-ui-tabs' ) );

	get_header();

	$ajax_nonce = wp_create_nonce( 'ajax!' );
	$parameters = $this->parameters;
	$query = '?' . http_build_query( $_GET );

	$make = isset( $parameters[ 'make' ] ) ? urldecode( $parameters[ 'make' ] ) : false;
	$model = isset( $parameters[ 'model' ] ) ? urldecode( $parameters[ 'model' ] ) : false;
	$trim = isset( $parameters[ 'trim' ] ) ? urldecode( $parameters[ 'trim' ] ) : false;

	$type = ( $make === false ) ? 'makes' : ( ( $model === false ) ? 'models' : 'trims' );

	$current_year = date( 'Y' );
	$last_year = $current_year - 1;
	$next_year = $current_year + 1;

?>

<script type="text/javascript">
    var dealertrend = jQuery.noConflict();
    dealertrend(document).ready(function () {
      dealertrend( '#showcase .jquery-ui-button' ).button();
      dealertrend( '#showcase .jquery-ui-button' ).click( function(e) {
          dealertrend.ajax(
          {
            url: '/dealertrend-ajax/?_ajax_nonce=<?php echo $ajax_nonce; ?>&request=http://vrs.dealertrend.com/trims/' + e.target.parentNode.id + '.json&year=<?php echo $last_year; ?>',
            context: document.body,
            success: function(data) {
              
            }
          });
          e.preventDefault();
      } );
    });
</script>

<?php

	flush();

	echo '<div id="showcase">';
	include( dirname( __FILE__ ) . '/' . $type . '.php' );
	echo '</div>';

	get_footer();
	flush();

?>
