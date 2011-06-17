<?php

if ( class_exists( 'dealetrend_plugin_updater' ) ) { 
	return false;
}

class dealetrend_plugin_updater {

	public $current_plugin_information = array();
	public $new_plugin_information = array();
	public $new_version = NULL;

	function __construct( $current_plugin_information ) {
		$this->load_plugin_information( $current_plugin_information );
		$this->queue_plugin_updater();
	}

	function load_plugin_information( $current_plugin_information ) {
		$this->current_plugin_information = $current_plugin_information;
	}

	function queue_plugin_updater() {
		add_action( 'site_transient_update_plugins', array( &$this, 'filter_plugin_count' ) );
	}

	function display_update_notice( $version_check = array() ) {
		if( $version_check[ 'current' ] < $version_check[ 'latest' ] ) {
			$update_data = (object) array(
				'new_version' => $version_check[ 'latest' ],
				'url' => $this->current_plugin_information[ 'PluginURI' ],
				'package' => 'http://github.com/downloads/dealertrend/wordpress-plugin-inventory-api/dealertrend-inventory-api.zip',
				'upgrade_notice' => ''
			);
			$this->new_plugin_information = $update_data;
			if( isset( $this->current_plugin_information[ 'PluginBaseName' ] ) ) {
				delete_site_transient( 'update_plugins' );
			}
			$plugin_check_list->response[ $this->current_plugin_information[ 'PluginBaseName' ] ] = $update_data;
			set_site_transient( 'update_plugins' , $plugin_check_list );
			add_action( 'admin_init', array( &$this, 'filter_plugin_rows' ), 15 );
		}
	}

	function filter_plugin_rows() {
		remove_all_actions( 'after_plugin_row_' . $this->current_plugin_information[ 'PluginBaseName' ] );
		add_action('after_plugin_row_' . $this->current_plugin_information[ 'PluginBaseName' ], array( &$this, 'plugin_row'), 9, 2 );
	}

	function plugin_row() {
		$filename = $this->current_plugin_information[ 'PluginBaseName' ];

		$autoupdate_url = wp_nonce_url( self_admin_url('update.php?action=upgrade-plugin&plugin=') . $filename, 'upgrade-plugin_' . $filename);

		$url = 'http://github.com/dealertrend/wordpress-plugin-inventory-api/commits/master?action=details&&TB_iframe=true&width=640&height=438';

		echo '<tr class="plugin-update-tr"><td colspan="3" class="plugin-update colspanchange"><div class="update-message">';
		echo 'There is a new version of ' . $this->current_plugin_information[ 'Name' ] . ' from ' . $this->current_plugin_information[ 'Author' ] . ' available. <a href="' . $url . '" class="thickbox" title="Latest Changes">View version ' . $this->new_version . ' details</a> or <a href="' . $autoupdate_url . '">update automatically</a>.';
		echo '</div></td></tr>';
	}

	function check_for_updates() {
		# Get the current list of plugins that need to be updated.
		$plugin_check_list = function_exists( 'get_site_transient' ) ? get_site_transient( 'update_plugins' ) : get_transient( 'update_plugins' );
		# This is where we check for new versions.
		$url = 'http://github.com/api/v2/json/repos/show/dealertrend/wordpress-plugin-inventory-api/tags';
		$request_handler = new http_api_wrapper( $url , 'dealetrend_plugin_updater' );

		$data = $request_handler->cached() ? $request_handler->cached() : $request_handler->get_file();
		$body = isset( $data[ 'body' ] ) ? $data[ 'body' ] : false;
		if( $body ) {
			$json = json_decode( $body );
			$tags = $json->tags;
			# We just want to version numbers.
			$versions = array_keys( get_object_vars( $tags ) );
			# Descending.
			sort( $versions );
			$versions = array_reverse( $versions );
			if( !empty( $versions ) ) {
				$latest_version = str_replace( 'v' , NULL , $versions[ 0 ] );
			} else {
				$latest_version = '0';
			}
			$this->new_version = $latest_version;
			$current_version = $this->current_plugin_information[ 'Version' ];

			return array( 'current' => $current_version , 'latest' => $latest_version );
		} else {

			return false;
		}
	}

	function filter_plugin_count( $current_values ) {
		if( $this->new_version > $this->current_plugin_information[ 'Version' ] ) {
			$new_values = $current_values;
			if( !isset( $new_values->response[ $this->current_plugin_information[ 'PluginBaseName' ] ] ) ){
				$new_values->response[ $this->current_plugin_information[ 'PluginBaseName' ] ] = $this->new_plugin_information;
			}

			return $new_values;
		} else {

			return $current_values;
		}
	}

}

?>
