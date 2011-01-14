<?php

  global $dealertrend_api;

  if( $_POST ) { 
    if( !wp_verify_nonce( $_POST[ '_wpnonce' ], 'dealertrend_api_options_update' ) ) die( 'Security check failed.' );
    if( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'update' ) {        
      $dealertrend_api->options[ 'company_information' ] = $_POST[ 'company_information' ];
      $dealertrend_api->save_options();
    }   
  }

  $inventory_data = $dealertrend_api->get_inventory();
  $company_information = $dealertrend_api->get_company_information();

?>
<div class="wrap">
  <div id="icon-dealertrend" class="icon32"><br /></div>
  <h2><?php echo $this->plugin_meta_data[ 'Name' ]; ?> Settings</h2>

  <table>
  </table>

  <table width="450">
    <caption><h3 class="alignleft">Inventory Information</h3></caption>
    <tr>
      <td width="125">Feed Status:</td>
      <td><?php echo ( $dealertrend_api->status[ 'inventory_json' ] === true ) ? '<span class="success">Loaded</span>' : '<span class="fail">Unavailable</span>' ?></td>
    </tr>
  </table>

  <table width="450">
    <caption><h3 class="alignleft">Company Information</h3></caption>
    <tr>
      <td width="125">Account Status:</td>
      <td><?php echo ( $dealertrend_api->status[ 'company_information' ] === true ) ? '<span class="success">Loaded</span>' : '<span class="fail">Unavailable</span>' ?></td>
    </tr>
    <?php if( $dealertrend_api->status[ 'company_information' ] === true ): ?>
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
    <tr>
      <td>Fax:</td>
      <td><strong><?php echo $company_information->fax; ?></strong></td>
    </tr>
    <tr>
      <td>Country Code:</td>
      <td><strong><?php echo $company_information->country_code; ?></strong></td>
    </tr>
    <?php endif; ?>
  </table>

  <form name="dealertrend_api_options_form" method="post" action="">
    <?php wp_nonce_field( 'dealertrend_api_options_update' ); ?>
    <table class="form-table">
      <caption><h3 class="title" align="left">Company Settings</h3></caption>
      <tr valign="top">
        <th scope="row">Company ID:</th>
        <td><input type="text" name="company_information[id]" value="<?php echo $dealertrend_api->options[ 'company_information' ][ 'id' ] ?>" /></td>
      </tr>
    </table>
    <input type="hidden" name="action" value="update" />
    <p class="submit">
      <input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
    </p>
  </form>
</div>
