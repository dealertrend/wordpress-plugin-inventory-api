<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

require_once( dirname( __FILE__ ) . '/http_request.php' );

class Updater {

	private $_latest_version = 0;
	private $_new_update_information = array();
	private $_current_update_information = array();

	public function check_for_updates() {
		$this->_current_update_information = $this->_get_current_wordpress_update_data();
		$this->_new_update_information[ 'last_checked' ] = time();

		$enough_time_passed = $this->_has_enough_time_passed();
		$changed = $this->_have_plugins_changed( $this->_get_installed_plugin_headers() );

		if ( ! $enough_time_passed && ! $changed ) {
			return false;
		}

		$this->_check_github_api();
	}

	private function _get_current_wordpress_update_data() {
		$data = get_site_transient( 'update_plugins' );

		return is_object( $data ) ? $data : new \stdClass;
	}

	private function _has_enough_time_passed() {
		return
		isset( $this->_current_update_information->last_checked ) &&
		$this->_get_timeout_value() > ( time() - $this->_current_update_information->last_checked );
	}

	private function _get_timeout_value() {
		return in_array( current_filter() , array( 'load-plugins.php', 'load-update.php', 'load-update-core.php' ) ) ? 3600 : 43200;
	}

	private function _have_plugins_changed( $installed_plugin_headers ) {
		foreach ( $installed_plugin_headers as $basename => $headers ) {
			$this->_new_update_information[ 'checked' ][ $basename ] = $headers[ 'Version' ];
			if( $this->_not_checked_or_different_versions( $basename , $headers ) ) {
				return true;
			}
			if( $this->_not_in_response( $installed_plugin_headers ) ) {
				return true;
			}
		}

		return false;
	}

	private function _not_checked_or_different_versions( $basename , $headers ) {
		return (
			! isset( $this->_current_update_information->checked[ $basename ] ) ||
			strval( $this->_current_update_information->checked[ $basename ] ) !== strval( $headers[ 'Version' ] )
		);
	}

	private function _not_in_response( $installed_plugin_headers ) {
		if ( isset ( $this->_current_update_information->response ) && is_array( $this->_current_update_information->response ) ) {
			foreach ( $this->_current_update_information->response as $basename => $update_details ) {
				if ( ! isset( $installed_plugin_headers[ $basename ] ) ) {
					return true;
				}
			}
		}
		return false;
	}

	private function _get_installed_plugin_headers() {
		return get_plugins();
	}

	private function _check_github_api() {
		$this->_latest_version = $this->_get_latest_version( $this->_get_tags() );
		if( ! $this->_latest_version ) {
			return false;
		}
		$current = $this->_plugin_information[ 'Version' ];

		if( $this->_latest_version > $current ) {
			add_action( 'site_transient_update_plugins', array( &$this , 'filter_plugin_count' ) );
			$this->_display_update_notice();
		}
	}

	private function _get_latest_version( $tags ) {
			if( ! $tags ) {
				return false;
			}
			$version_list = array_keys( get_object_vars( $tags ) );
			foreach( $version_list as $key => $version ) {
				$filtered_versions[ $key ] = str_replace( 'v' , NULL , $version );
				$filtered_versions[ $key ] = str_replace( '.' , NULL , $filtered_versions[ $key ] );
				$reference[ $filtered_versions[ $key ] ] = $version;
			}
			sort( $filtered_versions , SORT_NUMERIC );
			$versions = array_reverse( $filtered_versions );

			return isset( $version ) && ! empty( $versions ) ? str_replace( 'v' , NULL , $reference[ $versions[ 0 ] ] ) : 0;
	}

	private function _get_tags() {
		$request = new Http_Request();
	//	$request->set_request_information( $this->tags_url() , $this->cache_key() );
	//	$data = $request->get_cached_data() ? $request->get_cached_data() : $request->get_file();

		return isset( $data[ 'body' ] ) ? json_decode( $data[ 'body' ] )->tags : false;
	}

	private function _tags_url() {
		return 'http://github.com/api/v2/json/repos/show/dealertrend/wordpress-plugin-inventory-api/tags';
	}

	private function _cache_key() {
		return 'dealertrend_inventory_api_plugin_updater';
	}

	private function _set_new_version( $version ) {
		$this->_new_update_information[ 'checked' ][ $this->_plugin_information[ 'PluginBaseName' ] ] = $version;
	}

	public function _filter_plugin_count( $data ) {
		if( ! isset( $data->response[ $this->_plugin_information[ 'PluginBaseName' ] ] ) ) {
			$data->response[ $this->_plugin_information[ 'PluginBaseName' ] ] = $this->_create_update_object();
		}

		return $data;
	}

	private function _display_update_notice() {
		$data = $this->_create_update_object();

		if( isset( $this->_plugin_information[ 'PluginBaseName' ] ) ) {
			delete_site_transient( 'update_plugins' );
		}

		$new = (object) $this->_new_update_information;
		$this->_set_new_version( $this->_latest_version );
		$new->response[ $this->_plugin_information[ 'PluginBaseName' ] ] = $data;

		set_site_transient( 'update_plugins' , $new );
		add_action( 'admin_init' , array( &$this , 'filter_plugin_rows' ) , 15 );
	}

	private function create_update_object() {
		$update_information = (object) array(
			'new_version' => $this->_latest_version,
			'url' => $this->_plugin_information[ 'PluginURI' ],
			'package' => $this->_download_url(),
			'upgrade_notice' => ''
		);

		return $update_information;
	}

	private function _download_url() {
		return 'http://github.com/downloads/dealertrend/wordpress-plugin-inventory-api/dealertrend-inventory-api.zip';
	}

	public function filter_plugin_rows() {
		remove_all_actions( 'after_plugin_row_' . $this->_plugin_information[ 'PluginBaseName' ] );
		add_action( 'after_plugin_row_' . $this->_plugin_information[ 'PluginBaseName' ] , array( &$this , 'plugin_row' ) , 9 , 2 );
	}

	public function plugin_row() {
		echo '<tr class="plugin-update-tr"><td colspan="3" class="plugin-update colspanchange"><div class="update-message">';
		echo
			'There is a new version of ' . $this->_plugin_information[ 'Name' ] . ' from ' . $this->_plugin_information[ 'Author' ] . ' available. 
			<a href="' . $this->_get_changelog_url() . '" class="thickbox" title="Latest Changes">View version ' . $this->_latest_version . ' details</a> or <a href="' . $this->_get_autoupdate_url() . '">update automatically</a>.';
		echo '</div></td></tr>';
	}

	private function _get_autoupdate_url() {
		return wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $this->_plugin_information[ 'PluginBaseName' ] , 'upgrade-plugin_' . $this->_plugin_information[ 'PluginBaseName' ] );
	}

	private function _get_changelog_url() {
		return 'http://dealertrend.backpackit.com/pub/2653226-dealertrend-inventory-api-changelog?TB_iframe=true&width=640&height=438&version=' . $this->_plugin_information[ 'Version' ];
	}


}

?>
