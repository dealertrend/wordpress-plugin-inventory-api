<?php

# XXXX ..... Need to make a call
# XXXX ..... Construct call
# XXXX ..... Send to ajax handler via XMLHTTPRequest
# XXXX ..... Handler makes server side request.
# XXXX ..... Handler returns result.
# XXXX ..... ???
# XXXX ..... Profit!

namespace WordPress\Plugins\DealerTrend\InventoryAPI;

class ajax {
	function __construct( $parameters ) {
		$nonce = isset( $_REQUEST['_ajax_nonce'] ) ? $_REQUEST['_ajax_nonce'] : false;
		if( ! wp_verify_nonce( $nonce , 'ajax!' ) ) {
			status_header( '403' );
			die('<img src="http://www.schlagging.com/storage/dennis_nedry.gif" /><br /><a href="http://nedry.ytmnd.com/">You didn\'t say the magic word...!</a>');
		}
		$request_handler = new http_request( $parameters[ 'request' ] , 'vehicle_reference_system' );
		$data = $request_handler->cached() ? $request_handler->cached() : $request_handler->get_file();
		echo isset( $data[ 'body' ] ) ? $data[ 'body' ] : NULL;
	}
}

?>
