<?php
/*
* Resource file. Used to store and retrieve static data used within the plugin.
*/

require_once WP_SOCIAL_TUMBLELOG_PLUGIN_DIR.'inc/WPSocialTumblelog_Utils.php';

class WPSocialTumblelog_Resources{

	const DATA = 'wp_social_tumblelog_settings';
	const PAGE_TITLE = 'Tumblelog';
	const ADMIN_SCRIPT_HANDLE = 'wpsocial-tumblelog-settings-page-script';
	const ADMIN_STYLE_HANDLE = 'wpsocial-tumblelog-settings-page-style';
	const INVALID_FEED = 'Invalid feed uri';
	const FEED_NOT_FOUND = 'Feed not found';
	const FEED_ALREADY_EXISTS = 'Feed already exists. Please try again.';
	const URL_IS_NOT_FEED = 'The url does not point to a valid feed. Please try again.';
	const AJAX_ACTION_ADD_FEED = 'wp_social_tumblelog_add_feed';
	const AJAX_ACTION_REMOVE_FEED = 'wp_social_tumblelog_remove_feed';

}