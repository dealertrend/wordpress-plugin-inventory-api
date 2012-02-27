<?php


class Dealertrend_Inventory_Api_Requirements {

	private $_error_message = null;
	private $_plugin_basename = null;
	private $_local_versions = array();
	private $_required_versions = array();

	public function has_been_checked() {
		return get_option( $this->_get_option_key );
	}

	private function _get_option_key() {
		return 'dealertrend_inventory_api_requirements_checked';
	}

	public function set_master_file( $file ) {
		$this->_master_file = $file;
	}

	public function check_requirements() {
		$this->_get_local_versions();
		$this->_get_required_versions();
		if( $this->_compare_versions() == false ) {
			$this->_unable_to_load();
			return false;
		} else {
			$this->_set_has_been_checked_flag();
			return true;
		}
	}

	private function _get_local_versions() {
		$this->_local_versions[ 'php' ] = $this->_get_php_version();
		$this->_local_versions[ 'wordpress' ] = $this->_get_wordpress_version();
	}

	private function _get_php_version() {
		return floatval( phpversion() );
	}

	private function _get_wordpress_version() {
		global $wp_version;
		return floatval( $wp_version );
	}

	private function _get_required_versions() {
		$this->_required_versions[ 'php' ] = $this->_get_required_php_version();
		$this->_required_versions[ 'wordpress' ] = $this->_get_required_wordpress_version();;
	}

	private function _get_required_php_version() {
		if( ! isset( $this->_required_versions[ 'php' ] ) ){
			$this->_required_versions[ 'php' ]  = 5.3;
		}
		return $this->_required_versions[ 'php' ];
	}

	private function _get_required_wordpress_version() {
		if( ! isset( $this->_required_versions[ 'wordpress' ] ) ){
			$this->_required_versions[ 'wordpress' ]  = 3.2;
		}
		return $this->_required_versions[ 'wordpress' ];
	}

	private function _compare_versions() {
		foreach( $this->_required_versions as $requirement_name => $required_version ) {
			if( $this->_local_versions[ $requirement_name ] < $required_version ) {
				$this->_error_message = '<strong> ' .
				strtoupper( $requirement_name ) . ' is required to be <span class="file-error">' . $required_version . '</span> or greater.
				 Your version is: <span class="file-error">' . $this->_local_versions[ $requirement_name ] . '</span>
				</strong>';
				return false;
			}
		}
		return true;
	}

	private function _unable_to_load() {
		add_action( 'admin_notices' , array( &$this , 'display_admin_error' ) );
		add_action( 'admin_init' , array( &$this , 'deactivate_plugin' ) );
	}

	public function display_admin_error() {
		echo
		'<div class="error">
			<p><span class="file-error">ERROR::</span> Unable to activate plugin. System requirements are not met.</p>
			<p>' . $this->_error_message . '</p>
			<p>Plugin has been <strong>deactivated</strong>.</p>
		</div>';
		$this->_hide_default_activate_notice();
	}

	private function _hide_default_activate_notice() {
	}

	public function deactivate_plugin() {
		deactivate_plugins( plugin_basename( $this->_master_file ) );
	}

	private function _set_has_been_checked_flag() {
		update_option( $this->_get_option_key() , true );
	}

}

?>
