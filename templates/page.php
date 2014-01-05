<?php 
	require_once WP_SOCIAL_TUMBLELOG_PLUGIN_DIR.'inc/WPSocialTumblelog_Resources.php';
?>
<div class='wrap wpsocialtumblelog-options'>
	<h2><?php echo WPSocialTumblelog_Resources::PAGE_TITLE; ?></h2>
<!--
	<h2>Social</h2>
	<div class='well social'>
		<p><i class='fa fa-facebook-square'></i><span class='title'>Facebook</span><a class='button'>Connect</a></p>
		<p><i class='fa fa-twitter'></i><span class='title'>Twitter</span><a class='button'>Connect</a></p>
		<p><i class='fa fa-github'></i><span class='title'>Github</span><a class='button'>Connect</a></p>
	</div>
-->
	<h3 class='title'>RSS Feeds<i class='fa fa-rss'></i></h3>
	<p id="wpsocial-tumblelog-options-error-primary"></p>	
	<ol id="wpsocial-tumblelog-options-list" class="rectangle-list">
	<?php 
	$feeds = get_option(WPSocialTumblelog_Resources::DATA);
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