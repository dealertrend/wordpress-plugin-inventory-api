<?php

namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

print_me( __FILE__ );

require_once( dirname( __FILE__ ) . '/http_request.php' );

class Updater {

	public $current_plugin_information = array();
	public $new_plugin_information = array();
	public $new_version = NULL;

	function __construct( $current_plugin_information ) {
print_me( __METHOD__ );
		$this->load_plugin_information( $current_plugin_information );
		$this->queue_plugin_updater();
	}

	function load_plugin_information( $current_plugin_information ) {
print_me( __METHOD__ );
		$this->current_plugin_information = $current_plugin_information;
	}

	function queue_plugin_updater() {
print_me( __METHOD__ );
		add_action( 'site_transient_update_plugins', array( &$this, 'filter_plugin_count' ) );
	}

	function display_update_notice( $version_check = array() ) {
print_me( __METHOD__ );
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
print_me( __METHOD__ );
		remove_all_actions( 'after_plugin_row_' . $this->current_plugin_information[ 'PluginBaseName' ] );
		add_action('after_plugin_row_' . $this->current_plugin_information[ 'PluginBaseName' ], array( &$this, 'plugin_row'), 9, 2 );
	}

	function plugin_row() {
print_me( __METHOD__ );

		$filename = $this->current_plugin_information[ 'PluginBaseName' ];

		$autoupdate_url = wp_nonce_url( self_admin_url('update.php?action=upgrade-plugin&plugin=') . $filename, 'upgrade-plugin_' . $filename);

		$url = 'https://dealertrend.backpackit.com/pub/2653226-dealertrend-inventory-api-changelog?TB_iframe=true&version=' . $this->current_plugin_information[ 'Version' ];

		echo '<tr class="plugin-update-tr"><td colspan="3" class="plugin-update colspanchange"><div class="update-message">';
		echo 'There is a new version of ' . $this->current_plugin_information[ 'Name' ] . ' from ' . $this->current_plugin_information[ 'Author' ] . ' available. <a id="dealertrend-changelog" href="' . $url . '" class="thickbox" title="Latest Changes">View version ' . $this->new_version . ' details</a> or <a href="' . $autoupdate_url . '">update automatically</a>.';
		echo '</div></td></tr>';
	}

	function check_for_updates() {
print_me( __METHOD__ );
		$plugin_check_list = function_exists( 'get_site_transient' ) ? get_site_transient( 'update_plugins' ) : get_transient( 'update_plugins' );
		$url = 'http://github.com/api/v2/json/repos/show/dealertrend/wordpress-plugin-inventory-api/tags';
		$request_handler = new http_request( $url , 'dealetrend_plugin_updater' );

		$data = $request_handler->cached() ? $request_handler->cached() : $request_handler->get_file();
		$body = isset( $data[ 'body' ] ) ? $data[ 'body' ] : false;
		if( $body ) {
			$json = json_decode( $body );
			$tags = $json->tags;
			$versions = array_keys( get_object_vars( $tags ) );
			foreach( $versions as $key => $value ) {
				$filtered_versions[ $key ] = str_replace( 'v' , NULL , $value );
				$filtered_versions[ $key ] = str_replace( '.' , NULL , $filtered_versions[ $key ] );
				$reference[ $filtered_versions[ $key ] ] = $value;
			}

			sort( $filtered_versions , SORT_NUMERIC );
			$versions = array_reverse( $filtered_versions );
			if( !empty( $versions ) ) {
				$latest_version = str_replace( 'v' , NULL , $reference[ $versions[ 0 ] ] );
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
print_me( __METHOD__ );
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
