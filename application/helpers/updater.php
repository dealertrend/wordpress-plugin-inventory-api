<?php
namespace Wordpress\Plugins\Dealertrend\Inventory\Api;

class wp_auto_update
{
	/**
	 * The plugin current version
	 * @var string
	 */
	public $current_version;

	/**
	 * The plugin current version
	 * @var string
	 */
	public $remote_version;
 
	/**
	 * The plugin remote update path
	 * @var string
	 */
	public $update_path;
 
	/**
	 * Plugin Slug (plugin_directory/plugin_file.php)
	 * @var string
	 */
	public $plugin_slug;
 
	/**
	 * Plugin name (plugin_file)
	 * @var string
	 */
	public $slug;

	/**
	 * Package URL
	 * @var string
	 */
	public $package;
 
	/**
	 * Initialize a new instance of the WordPress Auto-Update class
	 * @param string $current_version
	 * @param string $update_path
	 * @param string $plugin_slug
	 */
	public function __construct($current_version, $plugin_slug ){
		// Set the class public variables
		$this->current_version = $current_version;
		$this->update_path = 'http://updates.s3.dealertrend.com/wp-plugin-inventory-api/update.json';
		$this->plugin_slug = $plugin_slug;
		list ($t1, $t2) = explode('/', $plugin_slug);
		$this->slug = str_replace('.php', '', $t2);

		if (is_admin()) {
			// define the alternative API for updating checking
			add_filter('pre_set_site_transient_update_plugins', array(&$this, 'check_update'));
			add_action('install_plugins_pre_plugin-information', array($this, 'display_changelog'));
		}

	}
 
	/**
	 * Add our self-hosted autoupdate plugin to the filter transient
	 *
	 * @param $transient
	 * @return object $ transient
	 */
	public function check_update($transient){
		if (empty($transient->checked)) {
			return $transient;
		}

		// Get the remote data
		$this->getRemote_data();

		// If a newer version is available, add the update
		if( $this->remote_version ){
			if (version_compare($this->current_version, $this->remote_version, '<')) {
				$obj = new \stdClass();
				$obj->slug = $this->slug;
				$obj->new_version = $this->remote_version;
				$obj->url = $this->update_path;
				$obj->package = $this->package;
				$transient->response[$this->plugin_slug] = $obj;
			}
		}
		return $transient;
	}
 
	/**
	 * Return the remote version
	 * @return string $remote_version
	 */
	public function getRemote_data(){
		//$request = wp_remote_post($this->update_path, array('body' => array('action' => 'version')));
		$request = new http_request( $this->update_path, 'dealetrend_plugin_updater' );
		$request = $request->cached() ? $request->cached() : $request->get_file();
		if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
			$results = json_decode( $request['body'] );
			$this->remote_version = $results->version;
			$this->package = $results->info->download_link;
		}
	}

	/**
	 * Displays changlog on wp auto update page
	 */
    public function display_changelog(){
        if ($_REQUEST['plugin'] != $this->slug)
            return;

		$request = new http_request( 'http://updates.s3.dealertrend.com/wp-plugin-inventory-api/changelog.html', 'dealetrend_plugin_changelog' );
		$request = $request->cached() ? $request->cached() : $request->get_file();

		if (is_wp_error($request) || 200 != wp_remote_retrieve_response_code($request) ) {
            $page_text = sprintf(__("Connection lost.%sPlease try again or %scontact support%s.", 'dealertrend'), "<br/>", "<a href='http://www.dealertrend.com'>", "</a>");
        }else{
            $page_text = $request['body'];
        }
        echo stripslashes($page_text);

        exit;
    }

}

?>
