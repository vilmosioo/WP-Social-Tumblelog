<?php
/*
* Class to handle Ajax requests
*/

require_once WP_SOCIAL_TUMBLELOG_PLUGIN_DIR.'inc/WPSocialTumblelog_Utils.php';
require_once WP_SOCIAL_TUMBLELOG_PLUGIN_DIR.'inc/WPSocialTumblelog_Resources.php';
require_once WP_SOCIAL_TUMBLELOG_PLUGIN_DIR.'inc/WPSocialTumblelog_Twitter_API.php';

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
				$option = $this->get_option();

				$key = $this->get_key($option['feeds'],$feed);
				if($key == false){
					$rss = $this->validate_feed($feed);
					if($rss == false){
						$code = 400;
						$feed = WPSocialTumblelog_Resources::URL_IS_NOT_FEED;
					} else {
						array_push($option['feeds'], $rss);
						update_option(WPSocialTumblelog_Resources::DATA, $option);
					}
				} else {
					$code = 400;
					$feed = WPSocialTumblelog_Resources::FEED_ALREADY_EXISTS;
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

	public function get_option(){
		$option = get_option(WPSocialTumblelog_Resources::DATA);
		if(
			is_array($option) && 
			array_key_exists('feeds', $option) && is_array($option['feeds']) && 
			array_key_exists('social', $option) && is_array($option['social'])
		) {
			return $option;
		} else {
			$option = array(
				'feeds' => array(), 
				'social' => array()
			);
			update_option(WPSocialTumblelog_Resources::DATA, $option);
		}
		return $option;
	}

	public function remove_feed(){
		$feed = '';
		$code = 200;
		if(array_key_exists('feed', $_POST)){
			$feed = $_POST['feed'];
			if(filter_var($feed, FILTER_VALIDATE_URL)){
				// initialise options
				$option = $this->get_option();
				$key = $this->get_key($option['feeds'],$feed);
				if($key !== false){
				  unset($option['feeds'][$key]);
				  update_option(WPSocialTumblelog_Resources::DATA, $option);
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

	public function connect(){

		$code = 200;
		$social = $_POST['social'];
		$key = $_POST['key'];
		$secret = $_POST['secret'];
		$error = '';

		if(!empty($social) && !empty($key) && !empty($secret)){
			$option = $this->get_option();
			switch ($social) {
				case 'instagram':
					$url = '';
					break;
				case 'twitter':
					$option['social'][$social] = array(
						'client_key' => $key,
						'client_secret' => $secret,
						'access_token' => null
					);
					update_option(WPSocialTumblelog_Resources::DATA, $option);
					
					$connect = WPSocialTumblelog_Twitter_API::get_access_token();
					if($connect !== true){
						unset($option['social'][$social]);
						update_option(WPSocialTumblelog_Resources::DATA, $option);

						$code = 400;
						$error = $connect;
					}

					break;
				
				default:
					$code = 400;
					$error = WPSocialTumblelog_Resources::INVALID_SOCIAL;
					break;
			}
		} else {
			$code = 400;
			$error = WPSocialTumblelog_Resources::FIELDS_MANDATORY;
		}


		die(json_encode(array(
			'code' => $code,
			'error' => $error
		)));
	}

}