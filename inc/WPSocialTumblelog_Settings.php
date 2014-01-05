<?php
/*
* Theme options, the easy way
* Each tab is saved in a separate WordPress option, as an array of key-value pairs
* Only support text inputs at the moment but can easily be extended.
*
* Required: WPSocialTumblelog_Utils.php
*/

require_once WP_SOCIAL_TUMBLELOG_PLUGIN_DIR.'inc/WPSocialTumblelog_Utils.php';
require_once WP_SOCIAL_TUMBLELOG_PLUGIN_DIR.'inc/WPSocialTumblelog_Resources.php';
require_once WP_SOCIAL_TUMBLELOG_PLUGIN_DIR.'inc/WPSocialTumblelog_Ajax.php';

class WPSocialTumblelog_Settings{

	// Generates an instance of the Settings class
	static public function init(){
		return new WPSocialTumblelog_Settings();
	}

	// Initializes the plugin by setting localization, filters, and administration functions.
	private function __construct() {
		if(!is_admin()) return;

		$ajax_handler = new WPSocialTumblelog_Ajax();

		add_action('admin_menu', array(&$this, 'register_settings_page'));
		add_action( 'wp_ajax_'.WPSocialTumblelog_Resources::AJAX_ACTION_ADD_FEED, array(&$ajax_handler, 'add_feed') );
		add_action( 'wp_ajax_'.WPSocialTumblelog_Resources::AJAX_ACTION_REMOVE_FEED, array(&$ajax_handler, 'remove_feed') );
	} 

	public function register_settings_page(){
		$page = add_options_page(
			WPSocialTumblelog_Resources::PAGE_TITLE, 
			WPSocialTumblelog_Resources::PAGE_TITLE, 
			'manage_options', 
			WPSocialTumblelog_Resources::DATA, 
			array(&$this, 'print_page')
		);
		add_action( "admin_print_scripts-$page", array(&$this, 'settings_styles_and_scripts'));
	}

	public function settings_styles_and_scripts(){
		wp_enqueue_script(WPSocialTumblelog_Resources::ADMIN_SCRIPT_HANDLE, WP_SOCIAL_TUMBLELOG_PLUGIN_URL. 'js/admin.js');
		wp_enqueue_style(WPSocialTumblelog_Resources::ADMIN_STYLE_HANDLE, WP_SOCIAL_TUMBLELOG_PLUGIN_URL. 'css/admin.css');
		wp_enqueue_style('font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css');
	}

	public function print_page(){
		include WP_SOCIAL_TUMBLELOG_PLUGIN_DIR.'templates/page.php';
	}

}