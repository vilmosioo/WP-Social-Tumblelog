<?php
/*
* Theme options, the easy way
* Each tab is saved in a separate WordPress option, as an array of key-value pairs
* Only support text inputs at the moment but can easily be extended.
*
* Hint: you can display all sorts of content for your theme options page. Previews, images, WP query results etc. 
*
*	Example usage: 
*		$theme_options = new WPSocialTumblelog_Options();
*		$theme_options->addTab(array(
*			'name' => 'General',
*			'options' => array(
*				array('name' => 'Option name', 'type' => WPSocialTumblelog_Options::TEXT)
*			)
*		));
*		$theme_options->addTab(...)
*		...
*		$theme_options->render(); 
*
* Required: WPSocialTumblelog_Utils.php
*/

require_once 'WPSocialTumblelog_Utils.php';

class WPSocialTumblelog_Options{

	protected $tabs;
	protected $current;

	const TEXT = 0;
	const LIST_TYPE = 1;
	const HEADING = 2;
	const TEXT_LIVE = 3;
	const PREFIX = 'WPSocialTumblelog_options_';
	const ID = 'wp_social_tumblelog';

	static function create(){
		$options = new WPSocialTumblelog_Options();
		$options->addTab(array(
			'name' => 'General',
			'desc' => $options->get_social(),
			'options' => array(
				array(
					'name' => '<h2>RSS feeds</h2>',
					'type' => WPSocialTumblelog_Options::HEADING,
					'desc' => "<ul><li><a href='#'>http://google.com</<list>></li></ul>"
				),
				array(
					'name' => 'Add new feed',
					'type' => WPSocialTumblelog_Options::TEXT_LIVE,
					'desc' => '<a class="button">Save</a>'
				)
			)
		));
		$options->addTab(array(
			'name' => 'Preview',
			'options' => array()
		));

		$options->render();
		return $options;
	}

	public function __construct(){
		if(!is_admin()) return;
		$this->current = ( isset( $_GET['tab'] ) ? $_GET['tab'] : '' ); 
	}

	public function get_social(){
		return '<h2>Connect</h2><div class="well">List of social networks to connect to</div>';
	}

	// Add a field to a tab
	// Parameters : slug, name, description, tab
	protected function addField($args = array()){
		if(!is_array($args['option']) && is_string($args['option'])){
			$args['option'] = array('name' => $args['option']);
		}

		$args['option'] = array_merge ( array(
			"name" => 'Option name',
			"desc" => "",
			"type" => self::TEXT
		), $args['option'] );

		$this->tabs[$args['tab']]['options'][WPSocialTumblelog_Utils::generate_slug($args['option']['name'])] = array(
			'name' => $args['option']['name'],
			'desc' => $args['option']['desc'],
			'type' => $args['option']['type']
		);
	}

	// Add a new tab. 
	// Parameters : tab name, description, option array
	public function addTab($args = array()){
		$args = array_merge ( array(
			"name" => 'General',
			"desc" => "",
			"options" => array()
		), $args );

		$slug = WPSocialTumblelog_Utils::generate_slug($args['name']);
		$this->current = empty($this->current) ? $slug : $this->current;

		$this->tabs[$slug] = array(
			'name' => $args['name'],
			'desc' => $args['desc']
		);

		foreach ($args['options'] as $option) {
			$this->addField(array('tab' => $slug, 'option' => $option));        	
		} 
	}

	// display the tabs
	public function render(){
		// initialise options
		foreach($this->tabs as $slug => $tab){
			if(!get_option(WPSocialTumblelog_Options::PREFIX.$slug)){
				$defaults = array();
				
				if(is_array($tab["options"])) foreach( $tab['options'] as $option){
					$name = WPSocialTumblelog_Utils::generate_slug($option['name']);
					$title = $option['name'];
					$desc = $option['desc'];
				
					$defaults[$name] = $title;
				}
			
				update_option( WPSocialTumblelog_Options::PREFIX.$slug, $defaults );
			}	
		}

		add_action('admin_menu', array(&$this, 'init'));
		add_action('admin_init', array(&$this, 'register_mysettings') );
	}

	/*
	* Init function
	* 
	* Initializes the theme's options. Called on admin menu action.
	*/
	public function init(){
		$page = add_menu_page('Tumblelog', 'Tumblelog', 'manage_options', WPSocialTumblelog_Options::ID, array(&$this, 'settings_page_setup'));
		add_action( "admin_print_scripts-$page", array(&$this, 'settings_styles_and_scripts'));
	}

	public function settings_styles_and_scripts(){
		wp_enqueue_script('wpsocial-tumblelog-settings-page-script', WP_SOCIAL_TUMBLELOG_PLUGIN_URL. 'js/admin.js');
		wp_enqueue_style('wpsocial-tumblelog-settings-page-style', WP_SOCIAL_TUMBLELOG_PLUGIN_URL. 'css/admin.css');
	}
	/*
	* Settings page set up
	*
	* Handles the display of the Theme Options page (under Appearance)
	*/
	public function settings_page_setup() {
		echo '<div class="wrap wpsocialtumblelog-options">';
		$this->page_tabs() ;
		if ( isset( $_GET['settings-updated'] ) ) {
			echo "<div class='updated'><p>Tumblelog settings updated successfully.</p></div>";
		} 
		?>
		<form method="post" action="options.php">
			<?php settings_fields( WPSocialTumblelog_Options::PREFIX.$this->current ); ?>
			<?php do_settings_sections( WPSocialTumblelog_Options::ID ); ?>
				<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
		</div>
		<?php 
	} 

	/*
	* Page tabs
	*
	* Prints out the naviagtion for page tabs
	*/
	protected function page_tabs(){		
		
		$links = array();

		foreach( $this->tabs as $slug => $tab ){
			$active_class = $slug == $this->current ? "nav-tab-active" : "";
			$links[] = "<a class='nav-tab $active_class' href='?page=".WPSocialTumblelog_Options::ID."&tab=$slug'>$tab[name]</a>";
		}

		echo '<div id="icon-themes" class="icon32"><br /></div>'.
			'<h2 class="nav-tab-wrapper">';
		
		foreach ( $links as $link ){
			echo $link;
		}

		echo '</h2>';
	}

	/*
	* Register settings
	* 
	* Register all settings and setting sections
	*/
	public function register_mysettings() {		
		foreach($this->tabs as $slug=>$tab){
			register_setting( WPSocialTumblelog_Options::PREFIX.$slug, WPSocialTumblelog_Options::PREFIX.$slug );
			if($slug != $this->current) continue;
			add_settings_section( 'options_section_'.$slug, '', array(&$this, 'section_handler'), WPSocialTumblelog_Options::ID ); 
			if(is_array($tab["options"])) foreach($tab['options'] as $key => $option){
				add_settings_field( $key, $option['name'], array(&$this, 'input_handler'), WPSocialTumblelog_Options::ID, 'options_section_'.$slug, array("tab" => $slug, 'option' => array_merge(array('slug' => $key), $option)));
			}
		}
	}

	public function section_handler($args){
		$id = substr($args['id'], 16); // 16 is the length of options_section_
		if(!empty($this->tabs[$id]['title'])){
			echo "<h2 class='section'>".$this->tabs[$id]['title']."</h2>"; 
		}
		echo $this->tabs[$id]['desc']; 
	}

	public function input_handler($args){
		$option = $args['option'];
		$id = $option['slug'];
		$name = WPSocialTumblelog_Options::PREFIX.$args['tab']."[$id]";
		$values = get_option(WPSocialTumblelog_Options::PREFIX.$args['tab']);
		$value = $values[$id];
		$desc = $option['desc'];

		switch ($option['type']) {
			case WPSocialTumblelog_Options::HEADING:
				echo "</td></tr><tr valign=\"top\"><td colspan=\"2\" class='heading_text'>$desc</td></tr>";
			break;
			case WPSocialTumblelog_Options::TEXT_LIVE:
				echo "<input type='text' class='regular-text' id='$id' name='$name' value='$value'>";
				echo "<span class='description'>$desc</span>";
				break; 
			default:
				echo "<input type='text' class='regular-text' id='$id' name='$name' value='$value'>";
				echo "<br><span class='description'>$desc</span>"; 
			break;
		}
	}
}
?>