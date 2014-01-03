<?php
/*
* Class to handle Ajax requests
*/

require_once WP_SOCIAL_TUMBLELOG_PLUGIN_DIR.'inc/WPSocialTumblelog_Utils.php';
require_once WP_SOCIAL_TUMBLELOG_PLUGIN_DIR.'inc/WPSocialTumblelog_Resources.php';

class WPSocialTumblelog_Ajax{

	public function add_feed($data){
		$feed = '';
		$code = 200;

		if(array_key_exists('feed', $_POST)){
			$feed = $_POST['feed'];
			if(filter_var($feed, FILTER_VALIDATE_URL)){
				// initialise options
				$option = get_option(WPSocialTumblelog_Resources::DATA);
				if(is_array($option)){			
					$key = array_search($feed,$option);
					if($key == false){
						array_push($option, $feed);
					} else {
						$code = 400;
						$feed = WPSocialTumblelog_Resources::FEED_ALREADY_EXISTS;
					}
				} else {
					$option = array($feed);
				}

				update_option(WPSocialTumblelog_Resources::DATA, $option);
			} else {
				$code = 400;
				$feed = WPSocialTumblelog_Resources::INVALID_FEED;
			}
		}

		die(json_encode(array(
			'code' => $code,
			'feed' => $feed 
		)));
	}

	public function remove_feed($data){
		$feed = '';
		$code = 200;

		if(array_key_exists('feed', $_POST)){
			$feed = $_POST['feed'];
			if(filter_var($feed, FILTER_VALIDATE_URL)){
				// initialise options
				$option = get_option(WPSocialTumblelog_Resources::DATA);
				if(is_array($option)){
					$key = array_search($feed,$option);
					if($key !== false){
					  unset($option[$key]);
					  update_option(WPSocialTumblelog_Resources::DATA, $option);
					} else {
						$code = 400;
						$feed = WPSocialTumblelog_Resources::FEED_NOT_FOUND . ' - ' . $feed;
					}
				} else {
					$code = 400;
					$feed = WPSocialTumblelog_Resources::INVALID_FEED;
				}
			} else {
				$code = 400;
				$feed = WPSocialTumblelog_Resources::INVALID_FEED;
			}
		}

		die(json_encode(array(
			'code' => $code,
			'feed' => $feed
		)));
	}

}