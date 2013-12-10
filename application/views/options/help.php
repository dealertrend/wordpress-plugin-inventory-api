<?php

	$rules = get_option( 'rewrite_rules' );

	$inventory_link = isset($rules['^(inventory)']) ? '/inventory/' : '?taxonomy=inventory';
	$site_link = '<span style="white-space:nowrap;"><a href="http://www.dealertrend.com" target="_blank" title="DealerTrend, Inc: Shift Everything">DealerTrend, Inc.</a></span>';
?>
	<div id="help">
		<h3 class="title">Initial Setup</h3>
		<p>To get the plugin started you'll need to specify a VMS and a valid Company ID.</p>
		<p>Both of these will be provided to you upon purchasing a license with <?php echo $site_link; ?></p>
		<p>After you've received a valid VMS and Company ID, you'll need to go to the <a id="settings-link" href="#settings" title="DealerTrend API Settings">settings page</a> and fill in their respective fields. Once you click "Save				 Changes" it will start pulling in your Inventory and Company Feeds.</p>
		<h3 class="title">Viewing Inventory</h3>
		<?php
			if( isset( $check_inventory[ 'status' ] ) && $check_inventory[ 'status' ] == false ) {
				echo '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Alert:</strong> Inventory is not			 working. Please check your settings.</p></div></div>';
			}
		?>
		<p>If the VMS and Company Feed are both loaded, you may view your inventory here: <a href="<?php bloginfo( 'url' ); echo $inventory_link; ?>" target="_blank"><?php bloginfo( 'url' ); echo $inventory_link; ?></a></p>
		<p>Please note that any pages or sub-pages that reside at this permalink will no longer be shown.</p>
		<h3 class="title">Plugin Legend</h3>
		<table width="450" cellspacing="20">
			<tr>
				<td><span class="fail">Unavailable</span></td>
				<td>This means that the feed is currently not available. If this is showing, then that feed will not display information on your site.</td>
			</tr>
			<tr>
				<td><span class="success">Loaded</a></td>
				<td>If you see this, that means the feed is loaded and the information will be displayed on your website.</td>
			</tr>
		</table>
		<h3 class="title">Sitemap Links</h3>
			<a target="_blank" href="<?php bloginfo( 'url' ); echo '/new-vehicle-sitemap.xml'; ?>">Inventory New</a>
			<a target="_blank" href="<?php bloginfo( 'url' ); echo '/used-vehicle-sitemap.xml'; ?>">Inventory Used</a>
	</div>
