<div id="feeds">
	<table width="450">
		<tr>
			<td colspan="2"><h3 class="title">Vehicle Management System Feed</h3></td>
		</tr>
		<tr>
			<td width="125">Feed Status:</td>
			<?php
				if( ! empty( $this->instance->options[ 'vehicle_management_system' ][ 'host' ] ) ) {
					if( isset( $this->vehicle_management_system->status[ 'host' ][ 'results' ][ 'response' ][ 'code' ] ) ) {
						$response_code = $this->vehicle_management_system->status[ 'host' ][ 'results' ][ 'response' ][ 'code' ];
						if( $response_code == '200' ) {
							if( isset( $this->vehicle_management_system->status[ 'inventory_feed' ][ 'results' ][ 'response' ][ 'code' ] ) ) {
								$response_code = $this->vehicle_management_system->status[ 'inventory_feed' ][ 'results' ][ 'response' ][ 'code' ];
								if( $response_code == '200' ) {
									echo '<td><span class="success">Loaded</span></td>';
								}
							} else {
								echo '<td><span class="fail">Unavailable</span>';
								if( isset( $this->vehicle_management_system->status[ 'company_feed' ][ 'results' ][ 'code' ] ) ) {
									$response_code = $this->vehicle_management_system->status[ 'company_feed' ][ 'results' ][ 'code' ];
									if( $response_code == '404' ) {
										echo
										'<br /><span><small>Unable to verify company information.<br />
										Either the Company ID is invalid or the VMS Host is incorrect.<br />
										Check your <a id="settings-link" href="#settings" title="DealerTrend API Settings">Settings</a>.</small></span>';
									}
								}
								echo '</td>';
							}
						}
					} else {
						switch( $this->vehicle_management_system->status[ 'host' ][ 'results' ][ 'message' ] ) {
							case 'A valid URL was not provided.':
								echo
								'<td><span style="color:red;">A valid URL was not provided</span>.<br />
								<small>Check your <a id="settings-link" href="#settings" title="DealerTrend API Settings">VMS Host</a> address for accuracy.</smal></td>';
							break;
							case 'Maximum (5) redirects followed':
								echo
								'<td><span style="color:red;">The URL provided appears to not resolve</span>.<br />
								<small>Check your <a id="settings-link" href="#settings" title="DealerTrend API Settings">VMS Host</a> address for accuracy.</small></td>';
							break;
							default:
								echo '<td><span style="color:red;">An unknown error has occured</span>.<br />' . $this->vehicle_management_system->status[ 'host' ][ 'results' ][ 'message' ] . '</td>';
							break;
						}
					}
				} else {
					echo '<td><span class="fail">Not Configured</span></td>';
				}
			?>
		</tr>
	</table>
	<table width="450">
		<tr>
			<td colspan="2"><h3 class="title">Vehicle Reference System Feed</h3></td>
		</tr>
		<tr>
			<td width="125">Feed Status:</td>
			<?php
				if( ! empty( $this->instance->options[ 'vehicle_reference_system' ][ 'host' ] ) ) {
					if( isset( $this->vehicle_reference_system->status[ 'host' ][ 'results' ][ 'response' ][ 'code' ] ) ) {
						$response_code = $this->vehicle_reference_system->status[ 'host' ][ 'results' ][ 'response' ][ 'code' ];
						if( $response_code == '200' ) {
							if( isset( $this->vehicle_reference_system->status[ 'feed' ][ 'results' ][ 'response' ][ 'code' ] ) ) {
								$response_code = $this->vehicle_reference_system->status[ 'feed' ][ 'results' ][ 'response' ][ 'code' ];
								if( $response_code == '200' ) {
									echo '<td><span class="success">Loaded</span></td>';
								}
							} else {
								echo '<td><span class="fail">Unavailable.</span>';
							}
						}
					} else {
						switch( $this->vehicle_reference_system->status[ 'host' ][ 'results' ][ 'message' ] ) {
							case 'A valid URL was not provided.':
								echo
								'<td><span style="color:red;">A valid URL was not provided</span>.<br />
								<small>Check your <a id="settings-link" href="#settings" title="DealerTrend API Settings">VRS Host</a> address for accuracy.</smal></td>';
							break;
							case 'Maximum (5) redirects followed':
								echo
								'<td><span style="color:red;">The URL provided appears to not resolve</span>.<br />
								<small>Check your <a id="settings-link" href="#settings" title="DealerTrend API Settings">VRS Host</a> address for accuracy.</small></td>';
							break;
							default:
								echo '<td><span style="color:red;">An unknown error has occured</span>.<br />' . $this->vehicle_reference_system->status[ 'host' ][ 'results' ][ 'message' ] . '</td>';
							break;
						}
					}
				} else {
					echo '<td><span class="fail">Not Configured</span></td>';
				}
			?>
		</tr>
	</table>
	<table width="450">
		<tr>
			<td colspan="2"><h3 class="title">Company Information Feed</h3></td>
		</tr>
		<tr>
			<td width="125">Account Status:</td>
				<td>
					<?php
						if( $this->instance->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] != 0 ) {
							if( isset( $this->vehicle_management_system->status[ 'company_feed' ][ 'results' ][ 'code' ] ) ) {
								$response_code = $this->vehicle_management_system->status[ 'company_feed' ][ 'results' ][ 'code' ];
							}
							if( $response_code == 200 ) {
								echo '<span class="success">Loaded</span>';
							} else {
								echo '<span class="fail">Unavailable</span>';
								echo '<br/><small>Error Message: ' . $this->vehicle_management_system->status[ 'company_feed' ][ 'results' ][ 'message' ] . '</small>';
							}
						} else {
							echo '<span class="fail">Not Configured</span>';
						}
					?>
				</td>
			</tr>
			<?php
				$response_code = isset( $this->vehicle_management_system->status[ 'company_feed' ][ 'results' ][ 'response' ][ 'code' ] ) ? $this->vehicle_management_system->status[ 'company_feed' ][ 'results' ][ 'response' ][ 'code' ] : false;
				if( $response_code == 200 ):
					$company_information = $this->vehicle_management_system->get_company_information()->please();
					$company_information = json_decode( $company_information[ 'body' ] );
			?>
			<tr>
				<td>Name:</td>
				<td><strong><?php echo $company_information->name; ?></strong></td>
			</tr>
			<tr>
				<td>Address:</td>
				<td><strong><?php echo $company_information->street . ' ' . $company_information->city . ' ' . $company_information->state . ' ' . $company_information->zip; ?></strong></td>
			</tr>
			<tr>
				<td>Phone:</td>
				<td><strong><?php echo $company_information->phone; ?></strong></td>
			</tr>
			<?php if( strlen( $company_information->fax ) > 0 ) { ?>
			<tr>
				<td>Fax:</td>
				<td><strong><?php echo $company_information->fax; ?></strong></td>
			</tr>
			<?php } ?>
			<tr>
				<td>Country Code:</td>
				<td><strong><?php echo $company_information->country_code; ?></strong></td>
			</tr>
			<?php
				$api_keys = isset( $company_information->api_keys ) ? $company_information->api_keys : array();
				if( count( $api_keys ) > 0 ) {
			?>
			<tr>
				<td>API Keys:</td>
				<td>
					<ul>
						<?php
							foreach( $api_keys as $key => $value ) {
								echo '<li>' . $key . ': ' . $value . '</li>';
							}
						?>
					</ul>
				</td>
			</tr>
			<?php } ?>
		<?php endif; ?>
	</table>
</div>
