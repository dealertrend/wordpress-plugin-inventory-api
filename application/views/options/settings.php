<?php  global $wp_rewrite;

  $inventory_link = ! empty( $wp_rewrite->rules ) ? '/inventory/' : '?taxonomy=inventory';
  $site_link = '<span style="white-space:nowrap;"><a href="http://www.dealertrend.com" target="_blank" title="DealerTrend, Inc: Shift Everything">DealerTrend, Inc.</a></span>';
?>

  <div id="settings">
    <form name="dealertrend_inventory_api_plugin_settings" method="post" action="#feeds">
      <?php wp_nonce_field( 'dealertrend_inventory_api' ); ?>
      <table width="450">
        <tr>
          <td colspan="2">
            <h3 class="title">API Settings</h3>
          </td>
        </tr>
        <tr>
          <td width="200"><label for="vehicle-management-system">Vehicle Management System:</label></td>
          <td><input type="text" id="vehicle-management-system" name="vehicle_management_system[host]" value="<?php echo $this->instance->options[ 'vehicle_management_system' ][ 'host' ] ?>" class="long_input" /></td>
        </tr>
        <tr>
          <td width="200"></td>
          <td><small>Inventory will not be available without providing a valid VMS from <?php echo $site_link; ?></small></td>
        </tr>
        <tr>
          <td width="200"><label for="vehicle-reference-system">Vehicle Reference System:</label></td>
          <td><input type="text" id="vehicle-reference-system" name="vehicle_reference_system[host]" value="<?php echo $this->instance->options[ 'vehicle_reference_system' ][ 'host' ] ?>" class="long_input" /></td>
        </tr>
        <tr>
          <td width="200"></td>
          <td><small>Showcase and certain tools will not be available without providing a valid VRS from <?php echo $site_link; ?></small></td>
        </tr>
      </table>
      <table width="450">
        <tr>
          <td colspan="2"><h3 class="title">Company Settings</h3></td>
        </tr>
        <tr>
          <td width="125"><label for="company-id">Company ID:</a></td>
          <td>
            <input type="text" name="vehicle_management_system[company_information][id]" id="company-id" value="<?php echo $this->instance->options[ 'vehicle_management_system' ][ 'company_information' ][ 'id' ] ?>" />
          </td>
        </tr>
        <tr>
          <td width="200"><small>Pulls inventory from a specific dealership.</small></td>
          <td><small>Inventory will not be retreived without providing a valid company ID from <?php echo $site_link; ?></small></td>
        </tr>
      </table>
      <table>
        <tr>
          <td>
            <input type="hidden" name="action" value="update" />
            <p class="submit">
              <input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
            </p>
          </td>
          <td>
            <button id="uninstall" name="uninstall" value="true">Perform Clean Uninstall</button>
          </td>
        </tr>
      </table>
    </form>
  </div>
