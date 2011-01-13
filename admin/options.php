<?php

  global $dealertrend_api;

  if( $_POST ) { 
    if( !wp_verify_nonce( $_POST[ '_wpnonce' ], 'dealertrend_api_options_update' ) ) die( 'Security check failed.' );
    if( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'update' ) {        
      $dealertrend_api->options[ 'company_information' ] = $_POST[ 'company_information' ];
      $dealertrend_api->save_options();
    }   
  }

?>
<div class="wrap">
  <div id="icon-dealertrend" class="icon32"><br /></div>
  <h2><?php echo $this->plugin_meta_data[ 'Name' ]; ?> Settings</h2>
  <form name="dealertrend_api_options_form" method="post" action="">
    <?php wp_nonce_field( 'dealertrend_api_options_update' ); ?>
    <table class="form-table">
      <caption><h3 class="title" align="left">Company Information</h3></caption>
      <tr valign="top">
        <th scope="row">Company ID:</th>
        <td><input type="text" name="company_information[id]" value="<?php echo $dealertrend_api->options[ 'company_information' ][ 'id' ] ?>" /></td>
      </tr>
      <tr valign="top">
        <th scope="row">Country Code:</th>
        <td><input type="text" name="company_information[country]" value="<?php echo $dealertrend_api->options[ 'company_information' ][ 'country' ] ?>" /></td>
      </tr>
    </table>
    <input type="hidden" name="action" value="update" />
    <p class="submit">
      <input type="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" />
    </p>
  </form>
</div>
