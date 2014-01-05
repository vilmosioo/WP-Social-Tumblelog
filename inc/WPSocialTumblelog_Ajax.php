<?php
/*
* Class to handle Ajax requests
*/

require_once WP_SOCIAL_TUMBLELOG_PLUGIN_DIR.'inc/WPSocialTumblelog_Utils.php';
require_once WP_SOCIAL_TUMBLELOG_PLUGIN_DIR.'inc/WPSocialTumblelog_Resources.php';

class WPSocialTumblelog_Ajax{

	public function validate_feed($feed){
		// Get a SimplePie feed object from the specified feed source.
		$rss = fetch_feed($feed); // specify the source feed

		// Checks that the object is created correctly
		if (!is_wp_error($rss)){  
			return array(
				'url' => $feed,
				'title' => $rss->get_title(),
				'description' => $rss->get_description()
			);
		}

		return false;
	}

	public function add_feed($data){
		$feed = '';
		$code = 200;

		if(array_key_exists('feed', $_POST)){
			$feed = $_POST['feed'];
			if(filter_var($feed, FILTER_VALIDATE_URL)){
				// initialise options
				$option = get_option(WPSocialTumblelog_Resources::DATA);
				if(is_array($option)){	
					$key = $this->get_key($option,$feed);
					if($key == false){
						$rss = $this->validate_feed($feed);
						if($rss == false){
							$code = 400;
							$feed = WPSocialTumblelog_Resources::URL_IS_NOT_FEED;
						} else {
							array_push($option, $rss);
							update_option(WPSocialTumblelog_Resources::DATA, $option);
						}
					} else {
						$code = 400;
						$feed = WPSocialTumblelog_Resources::FEED_ALREADY_EXISTS;
					}
				} else {
					$rss = $this->validate_feed($feed);
					if($rss == false){
						$code = 400;
						$feed = WPSocialTumblelog_Resources::URL_IS_NOT_FEED;
					} else {
						$option = array($rss);
						update_option(WPSocialTumblelog_Resources::DATA, $option);
					}
				}

			} else {
				$code = 400;
				$feed = WPSocialTumblelog_Resources::INVALID_FEED;
			}
		}

		die(json_encode(array(
			'code' => $code,
			'feed' => empty($rss) ? $feed : $rss 
		)));
	}

	private function get_key($array, $feed){
		foreach ($array as $key => $value) {
			if($value['url'] == $feed){
				return $key;
			}
		}
		return false;
	}

	public function remove_feed(){
		$feed = '';
		$code = 200;
		if(array_key_exists('feed', $_POST)){
			$feed = $_POST['feed'];
			if(filter_var($feed, FILTER_VALIDATE_URL)){
				// initialise options
				$option = get_option(WPSocialTumblelog_Resources::DATA);
				if(is_array($option)){
					$key = $this->get_key($option,$feed);
					if($key !== false){
					  unset($option[$key]);
					  update_option(WPSocialTumblelog_Resources::DATA, $option);
					} else {
						$code = 400;
						$feed = WPSocialTumblelog_Resources::FEED_NOT_FOUND . ' - ' . $feed;
					}
				} else {
					$code = 400;
					$feed = WPSocialTumblelog_Resources::FEED_NOT_FOUND . ' - ' . $feed;
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