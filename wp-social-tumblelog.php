<?php
/*
Plugin Name: WP Social Tumblelog
Plugin URI: TODO
Description: A WordPress plugin that allows users to automatically generate a tumblelog from their various social networks. 
Version: 0.0.1
Author: Vilmos Ioo
Author URI: http://vilmosioo.co.uk
License: GPL2

	Copyright 2014 Vilmos Ioo

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
*/

// Define constants
define('WP_SOCIAL_TUMBLELOG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_SOCIAL_TUMBLELOG_PLUGIN_URL', plugin_dir_url(__FILE__));

// Includes
require_once(WP_SOCIAL_TUMBLELOG_PLUGIN_DIR.'inc/WPSocialTumblelog_Settings.php');
require_once(WP_SOCIAL_TUMBLELOG_PLUGIN_DIR.'inc/WPSocialTumblelog_Widget.php');

class WPSocialTumblelog_Plugin {

	static function init(){
		return new WPSocialTumblelog_Plugin();
	}

	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	private function __construct() {
		register_activation_hook(__FILE__, array( &$this, 'activate' ) );
		register_deactivation_hook(__FILE__, array( &$this, 'deactivate' ) );
		
		WPSocialTumblelog_Settings::init();
		add_shortcode('tumblelog', array( &$this, 'display_tumblog' ));
		add_action( 'widgets_init', array( &$this, 'register_widget' ));

	} 

	public function register_widget(){
		register_widget( 'WPSocialTumblelog_Widget' );
	}

	private function format_feed($url){
		// Get a SimplePie feed object from the specified feed source.
		$rss = fetch_feed($url); // specify the source feed
		if (!is_wp_error( $rss ) ) { // Checks that the object is created correctly 
			// Figure out how many total items there are, but limit it to 5. 
			$maxitems = $rss->get_item_quantity(); 
			// Build an array of all the items, starting with element 0 (first element).
			$rss_items = $rss->get_items(0, $maxitems); 
		} else {
			return false;
		}

		$feed = array();
		if ($maxitems != 0) {
			foreach ($rss_items as $item ){
				$feed[ strtotime($item->get_date()) ] = $item;
			}
		}
		return $feed;
  }

	// display activity chart for a repository
	public function display_tumblog($atts, $content = null){
		extract(shortcode_atts(array('wrap_class' => '','item_class' => '', 'count' => 10), $atts));
		$feed = array();
  	$option = get_option(WPSocialTumblelog_Resources::DATA);
  	foreach ($option as $key => $value) {
  		$feed += $this->format_feed($value['url']);
  	}

  	// sort items descending by date
  	krsort($feed);
  	$feed = array_slice($feed, 0, is_numeric($count) ? $count : 10);

		$s = "<div class='tumblelog $wrap_class'>";

		foreach($feed as $item){
			$s .= "<article class='tumblelog-item $item_class'>";
			$s .= "<h3><a href='".$item->get_link()."' target='_blank'>".$item->get_title()."</a></h3>";
			$s .= $item->get_description();
			$s .= "</article>";
		}

		$s .= "</div>";

		return $s;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog 
	 */
	public function activate( $network_wide ) {
		$option = get_option(WPSocialTumblelog_Resources::DATA);
		if(!is_array($option)){
			update_option(WPSocialTumblelog_Resources::DATA, array());
		}
	} 

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog 
	 */
	function deactivate( $network_wide ) {

	} 

} // end class

add_action( 'plugins_loaded', array('WPSocialTumblelog_Plugin', 'init' ) );

?>