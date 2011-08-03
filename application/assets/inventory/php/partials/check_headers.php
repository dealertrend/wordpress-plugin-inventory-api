<?php
	/*
			This is intended to be the source of truth for the response header logic.
	 */
	$check_host = $vehicle_management_system->check_host();
	status_header( '400' );
	if( $check_host[ 'status' ] != false ) {
		$check_company_id = $vehicle_management_system->check_company_id();
		if( $check_company_id[ 'status' ] != false ) {
			$check_inventory = $vehicle_management_system->check_inventory();
			if( $check_inventory[ 'status' ] != false ) {
				$inventory = $vehicle_management_system->get_inventory( $this->parameters );
				if( $inventory !== false ) {
					if( count( $inventory ) > 0 ) {
						status_header( '200' );
					} else {
						status_header( '404' );
					}	 
				}
			}
		}
	}
?>
