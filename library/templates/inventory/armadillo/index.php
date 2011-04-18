<?php
	# If the feed request failed, do not continue.
	# The assumption here is that we have a valid company feed, otherwise we wouldn't be able to get an inventory feed.
	if( !$this->status[ 'inventory_json_request' ] ) {
		echo '<h2 style="font-family:Helvetica,Arial; color:red;">Unable to display inventory. Please contact technical support.</h2><br class="clear" />';
		echo '<p>Unable to connect to API.</p>';
		return false;
	}

	# Define our core variables.
	# Inventory is already defined.
	$company_information = wp_cache_get( $this->options[ 'api' ][ 'vehicle_management_system' ] . '/api/companies/' . $this->options[ 'company_information' ][ 'id' ] , 'dealertrend_api' );
	$company_name = ucfirst( $company_information->name );
	$state = $company_information->state;
	$city = $company_information->city;

	# Let's set an anchor for usability.
	echo '<br class="clear" id="top" />';

	# We'll need an content wrapper.
	echo '<div id="dealertrend-wrapper">';

	# We don't want these variables to show up in the breadcrumbs.
	$do_not_show = array( 'page' , 'per_page', 'trim', 'body_style', 'vehicleclass', 'sort' );

	# Let's build the breadcrumbs
	$breadcrumbs = '<a href="/" title="' . $company_name . ': Home Page"><span>></span>' . urldecode( $company_name ) . '</a>';

	# Figoure out if we canuse clean URL structures or not.
	# TODO: Use an actual image for the separator...
	if( count( $this->parameters > 1 ) ) {
		$crumb_trail = null;
		if( !empty( $wp_rewrite->rules ) ) {
			foreach( $this->parameters as $key => $value ) {
				if( !in_array( $key ,$do_not_show ) && $key != 'taxonomy' ) {
					$crumb_trail .= '/inventory/' . $value;
					$breadcrumbs .= '<a href=' . $crumb_trail . '><span>></span>' . ucfirst( urldecode( $value ) ) . '</a>';
				}
			}
		} else {
			$crumb_trail = '?taxonomy=inventory';
			foreach( $this->parameters as $key => $value ) {
				if( !in_array( $key ,$do_not_show ) && $key != 'taxonomy' ) {
					$crumb_trail .= '&amp;' . $key . '=' . $value;
					$breadcrumbs .= '<a href=' . $crumb_trail . '><span>></span>' . ucfirst( urldecode( $value ) ) . '</a>';
				}
			}
		}
	}

	$breadcrumbs = '<div class="breadcrumbs">' . $breadcrumbs . '</div>';

	echo $breadcrumbs;

	flush();

	# What kind of page do we need to display?
	$type = isset( $inventory->vin ) ? 'detail' : 'list';
	include( dirname( __FILE__ ) . '/' . $type . '.php' );

	flush();

	# These should be at the top and bottom
	echo $breadcrumbs;
	echo '<a href="#top" title="Return to Top" class="return-to-top">Return to Top</a>';

	echo '</div>';
?>

<script type="text/javascript">
	var dealertrend = jQuery.noConflict();

	dealertrend(document).ready(function() {
		dealertrend('#dealertrend-wrapper #detail .slideshow .images')
		.cycle({
			slideExpr: 'img',
			fx: 'fade',
			pager: '#dealertrend-wrapper #detail .slideshow .navigation',
			pagerAnchorBuilder: function(idx, slide) { 
				return '<a href="#"><img src="' + slide.src + '" width="70" height"40" /></a>'; 
			} 
		});
	});

	dealertrend('#search-list > ul > li > span').click(function() {
		if(dealertrend(this).parent().hasClass('collapsed')) {
			dealertrend(this).parent().removeClass('collapsed');
			dealertrend(this).parent().addClass('expanded');
		} else {
			dealertrend(this).parent().addClass('collapsed');
			dealertrend(this).parent().removeClass('expanded');
		}
		if( dealertrend(this).parent().children('ul').is(":hidden")) {
			dealertrend(this).parent().children('ul').slideDown('slow', function() {});
		} else {
			dealertrend(this).parent().children('ul').slideUp('slow', function() {});
		}
	});

</script>

