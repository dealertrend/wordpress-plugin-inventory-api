<?php

	$autocheck_error = '';

	if( isset( $this->options[ 'vehicle_management_system' ][ 'host' ] ) && isset( $this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] ) ){

		if ( substr($this->options[ 'vehicle_management_system' ][ 'host' ], 0, 7) == 'http://' ){
			$host = $this->options[ 'vehicle_management_system' ][ 'host' ];
		} else {
			$host = 'http://'.$this->options[ 'vehicle_management_system' ][ 'host' ];
		}

		$autocheck_url = $host . '/companies/'.$this->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ].'/autochecks/';

		if( isset( $this->autocheck_vin ) ){
			$autocheck_url .= $this->autocheck_vin . '/';
		} else {
			$autocheck_url = '';
		}
	} else {
		$autocheck_url = '';
	}

	if( !empty( $autocheck_url ) ){
		if( extension_loaded("curl") ){
			$site = get_proxy_site_page($autocheck_url);

			if( $site['http_code'] != 200 ){
				$autocheck_error = '<h1 style="color:#FF0000;">Vehicle Not found in VMS system.<br>AC_ERROR #002</h1>';
			}
		} else {
			$autocheck_error = '<h1 style="color:#FF0000;">Please contact Host provider as curl extension is not installed and is required.<br>AC_ERROR #000</h1>';
		}
	} else {
			$autocheck_error = '<h1 style="color:#FF0000;">Admin settings have not been setup to access AutoCheck.<br>AC_ERROR #001</h1>';
	}

	if( empty($autocheck_error) ){
		echo substr($site['content'], $site['header_size'] );
	} else {
		echo $autocheck_error;
	}

?>
