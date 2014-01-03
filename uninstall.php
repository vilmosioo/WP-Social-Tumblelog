<?php
//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
		exit();

require_once plugin_dir_url(__FILE__).'inc/WPSocialTumblelog_Resources.php';

// For Single site
if (!is_multisite()){
	delete_option(WPSocialTumblelog_Resources::DATA);
} 
// For Multisite
else {
	global $wpdb;
	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
	$original_blog_id = get_current_blog_id();
	foreach ($blog_ids as $blog_id){
		switch_to_blog( $blog_id );
		delete_option(WPSocialTumblelog_Resources::DATA);
	}
	switch_to_blog( $original_blog_id );
}
?>