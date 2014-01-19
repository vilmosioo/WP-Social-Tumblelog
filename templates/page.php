<?php 
	require_once WP_SOCIAL_TUMBLELOG_PLUGIN_DIR.'inc/WPSocialTumblelog_Resources.php';
	require_once WP_SOCIAL_TUMBLELOG_PLUGIN_DIR.'inc/WPSocialTumblelog_Twitter_API.php';
	$option = get_option(WPSocialTumblelog_Resources::DATA);
	// $tweets = WPSocialTumblelog_Twitter_API::get_data();
?>
<div class='wrap wpsocialtumblelog-options'>
	<h2><?php echo WPSocialTumblelog_Resources::PAGE_TITLE; ?></h2>

	<h3 class='title'>Social</h3>
	<p>Connect to different social networks</p>
	<p id="wpsocial-error-primary"></p>	
	<p>
		<label for='wpsocial-client-key'>Client key</label><br>
		<input type='text' id='wpsocial-client-key' class="regular-text" value="">
	</p>
	<p>
		<label for='wpsocial-client-secret'>Client secret</label><br>
		<input type='text' id='wpsocial-client-secret' class="regular-text" value="">
	</p>
	<p>
		<label for='wpsocial-social'>Social network</label><br>
		<select id='wpsocial-social'>
			<option value='' selected></option>
			<option value='Instagram'>Instagram</option>
			<option value='twitter'>Twiter</option>
		</select>
	</p>
	<p>
		<a class='button' id='wpsocial-submit'>Connect</a>
		<img id="wpsocial-spinner" src="<?php echo admin_url('images/spinner.gif'); ?>">
		<p id="wpsocial-error"></p>
	</p>
	
	<h3 class='title'>RSS Feeds<i class='fa fa-rss'></i></h3>
	<p id="wpsocial-tumblelog-options-error-primary"></p>	
	<ol id="wpsocial-tumblelog-options-list" class="rectangle-list">
	<?php 
	// echo '<pre>'; print_r($tweets); echo '</pre>';
	// echo '<pre>'; print_r($option); echo '</pre>';
	$feeds = $option['feeds'];
	if(is_array($feeds)) foreach ($feeds as $key => $value) {
		echo "<li>";
		echo "<i class='fa fa-minus-circle delete'></i>";
		echo "<div class='feed'>";
		echo "<a href='".$value['url']."' target='_blank'>".(array_key_exists('title', $value) ? $value['title'] : $value['url'])."</a>"; 
		echo " - <span class='description'>".(array_key_exists('description', $value) ? $value['description'] : '')."</span>"; 
		echo "</div>";
		echo "</li>";
	}
	?>
	</ol>
	<div>
		<input type='text' id='wpsocial-tumblelog-options-feed' class='regular-text' value=''>
		<a class='button' id='wpsocial-tumblelog-options-add_feed' >Add new</a>
		<img id="wpsocial-tumblelog-options-spinner" src="<?php echo admin_url('images/spinner.gif'); ?>">
		<p id="wpsocial-tumblelog-options-error"></p>	
	</div>
</div>