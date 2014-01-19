<?php
/*
* twitter API helper class
* 
* Uses the twitter API v3 to retrieve public gists, repos or commits for a specific user
*/
class WPSocialTumblelog_Twitter_API{

	static function get_access_token(){
		$option = get_option(WPSocialTumblelog_Resources::DATA);

		if(
			is_array($option) && 
			array_key_exists('social', $option) &&
			is_array($option['social']) &&
			array_key_exists('twitter', $option['social']) &&
			is_array($option['social']['twitter'])
		){
			$twitter = $option['social']['twitter'];
			$client_key = urlencode($twitter['client_key']);
			$client_secret = urlencode($twitter['client_secret']);
			
			$args = array(
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode($client_key . ':' . $client_secret),
					'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8' 
				),
				'body' => 'grant_type=client_credentials',
				'sslverify' => false
			);
			$response = wp_remote_post('https://api.twitter.com/oauth2/token', $args);

			if(is_wp_error($response) || $response['response']['code'] != 200) {
				unset($option['social']['twitter']);
				update_option(WPSocialTumblelog_Resources::DATA, $option);
				return json_encode($response);
			} else {
				$body = $response['body'];
				if(empty($body['access_token'])){
					unset($option['social']['twitter']);
					update_option(WPSocialTumblelog_Resources::DATA, $option);
					return json_encode($response);
				} else {
					$option['social']['twitter']['access_token'] = json_decode($body)->access_token;
					update_option(WPSocialTumblelog_Resources::DATA, $option);
					return true;
				}
			}
		}
		return 'Client data not found';
	}

	static function get_data(){
		$option = get_option(WPSocialTumblelog_Resources::DATA);
		$access_token = null;
		if(
			is_array($option) && 
			array_key_exists('social', $option) &&
			is_array($option['social']) &&
			array_key_exists('twitter', $option['social']) &&
			is_array($option['social']['twitter']) &&
			array_key_exists('access_token', $option['social']['twitter'])
		){
			$access_token = $option['social']['twitter']['access_token'];
		}
		if(empty($access_token)) return array(); // do not proceed without access token

		$response = wp_remote_get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=vilmosioo", array( 
			'sslverify' => false,
			'headers' => array(
				'Authorization' => 'Bearer '.$access_token
			)
		));
		if(is_wp_error($response) || $response['response']['code'] != 200) {
			return array();
		}
		return json_decode($response['body']);
	}
}
?>