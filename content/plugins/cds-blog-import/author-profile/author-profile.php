<?php
//-----------------------------------------------------------------------------
/*
Plugin Name: Author Spotlight (Widget)
Version: 3.2
Plugin URI: http://nullpointer.debashish.com/author-spotlight-wordpress-widget
Description: Sidebar widget to display author profile on a post or page with Social icons. If you wish to have custom photos with User Profiles, please install/activate the <a href="http://wordpress.org/extend/plugins/user-photo">User photo</a> plugin (optional). If your blogs are co-authored by multiple people then you can use <a href="http://wordpress.org/extend/plugins/co-authors-plus">Co-Authors Plus</a> plugin (optional) to display all author profiles on the post page.
Author: Debashish Chakrabarty
Author URI: http://www.debashish.com
Min WP Version: 3.0
*/
//-----------------------------------------------------------------------------
?>
<?php
add_action('widgets_init', create_function('', 'return register_widget("AuthorSpotlight_Widget");'));

class AuthorSpotlight_Widget extends WP_Widget {

	var $icon_image_url;

	function __construct() {	   
		$widget_ops = array('classname' => 'AuthorSpotlight_Widget', 'description' => "Sidebar widget to display Author(s)' profile on a post page." );
		/* Widget control settings. */
		$control_ops = array('width' => 200, 'height' => 300);
		parent::__construct('authorspotlight', __('Author Spotlight'), $widget_ops, $control_ops);
		$this->icon_image_url = plugin_dir_url(__FILE__) . 'images/';
	}

	function widget( $args, $instance ) {		
		// If Co-Authors plus plugin exists display all co-aothor profiles one after another
		if(function_exists('coauthors_posts_links')) {
			$i = new CoAuthorsIterator(); 
			$cnt = 1;			
			while($i->iterate()){
				// the iterator overwrites the global authordata variable on each iteration
				$instance['seq'] = $cnt++;
				$instance['isLast'] = $i->is_last();
				$this->_displayAuthor($args, $instance);
			}
		}
		else {
			// Normal behavior, one author per blog post
			$instance['seq'] = 1;
			$instance['isLast'] = true;
			$this->_displayAuthor( $args, $instance);	
		}
	}

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => 'Author Spotlight', 'readfulltext' => 'Read Full', 'moretext' => 'More posts by the Author &raquo;', 'websitetext' => 'Website: ', 'charlimit' => '1000') );
		$title = strip_tags($instance['title']);
		$readfulltext = strip_tags($instance['readfulltext']);
		$moretext = strip_tags($instance['moretext']);
		$websitetext = strip_tags($instance['websitetext']);
		$charlimit = strip_tags($instance['charlimit']);		
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		<p>
		    Which social icons should be displayed for the author(s)?<br/>
			<?php
			foreach ( $this->_getIconsAsArray() as $key => $data ) {
				printf('<input type="checkbox" value="1" id="%s" name="%s"', $this->get_field_id($key), $this->get_field_name($key));
				printf("%s", checked( 1, $instance[$key] ));
				echo(' />&nbsp;');
				printf('<img style="margin-right:3px;" src="%s" title="%s" alt="%s"/>', $data['img_src'], $data['img_title'], $data['img_title'] );
				printf('%s', $data['img_seperator']);
			}
			?>		
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('moretext'); ?>"><i>"More articles by author"</i> text: </label>
			<input class="widefat" id="<?php echo $this->get_field_id('moretext'); ?>" name="<?php echo $this->get_field_name('moretext'); ?>" type="text" value="<?php echo esc_attr($moretext); ?>" />			
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('readfulltext'); ?>"><i>"Read full profile"</i> text: </label>
			<input class="widefat" id="<?php echo $this->get_field_id('readfulltext'); ?>" name="<?php echo $this->get_field_name('readfulltext'); ?>" type="text" value="<?php echo esc_attr($readfulltext); ?>" />			
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('charlimit'); ?>">Author profile character limit: </label>
			<input class="widefat" id="<?php echo $this->get_field_id('charlimit'); ?>" name="<?php echo $this->get_field_name('charlimit'); ?>" size="4" type="text" value="<?php echo esc_attr($charlimit); ?>" />			
		</p>
		<p><small><strong>Note:</strong> To display custom photos with User Profiles, please use the <a href="http://wordpress.org/extend/plugins/user-photo" target="_blank">User photo</a> plugin. The <a href="http://wordpress.org/extend/plugins/co-authors-plus" target="_blank">CoAuthors Plus</a> plugin will help you add multiple authors and display their profiles. To add the Social URLs add the relevant code to your theme functions file as directed at the <a href="http://wordpress.org/extend/plugins/author-profile/installation/" target="_blank">installation instructions</a>.</small></p>
		<?php	  
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['readfulltext'] = strip_tags($new_instance['readfulltext']);
		$instance['moretext'] = strip_tags($new_instance['moretext']);
		$instance['charlimit'] = strip_tags($new_instance['charlimit']);		
		foreach ( $this->_getIconsAsArray() as $key => $data ) {
			$instance[$key] = strip_tags($new_instance[$key]);
		}
		return $instance;
	}
	
	// Does the actual work of preparing the profile markup
	private function _displayAuthor($args, $instance){
		global $authordata;
		extract( $args ); // extract arguments
		$isHome = is_home() || is_front_page(); //Don't show the Widget on home page

		if(!$isHome && (is_single() || is_page()) && $authordata->ID){			
			if($instance['seq'] == 1){
				echo $before_widget;
				echo $before_title . $instance['title'] . $after_title;
			}			
			echo '<div id="author-spotlight">';			
			echo '<div id="author-profile">';
			// Display author's name
			echo '<h2>'.get_the_author_firstname().' '.get_the_author_lastname().'</h2>';
			
			//Display the social icons?
			$socialDiv = "";			
			$iconCount = 0;
			$style = "height:12px";
			
			foreach ( $this->_getIconsAsArray() as $key => $data ) {	
                                $print_img = false;
				$social_url = get_the_author_meta( $key, $authordata->ID );	
				
				// Other Social URLs come from Author meta, we added
				if($social_url != "") {
					$print_img = true;
				}
				
				// The Website or Homepahge URL should be read from Author-Data					
				if($data['img_title'] == 'Homepage' && $authordata->user_url){
					$social_url = $authordata->user_url;
					$print_img = true;
				}
				
				// If the URL is available & the Icon is enabled from Widget Admin, display it
				if($print_img && $instance[$key]){
					$socialDiv .= '<a href="'.$social_url.'" target="_blank" title="'.$data['img_title'].'">';
					$socialDiv .= '<img src="'.$data['img_src'].'" title="'.$data['img_title'].'" alt="'.$data['img_title'].'" />';
					$socialDiv .= '</a>';
					++$iconCount;
				}
			}
			
			if($iconCount <= 0){
				$style = "display:none;";
			}
			else if($iconCount > 6 && $iconCount <= 12){
				$style = "height: 36px";
			}
			else if($iconCount > 12){
				$style = "height: 62px";
			}
			
			printf('<div id="social-icons" style="%s">', $style);
			echo $socialDiv;
			echo "</div><!--#social-icons-->";
			
			//Display User photo OR the Gravatar
			if(function_exists('userphoto_exists') && userphoto_exists($authordata)){
				userphoto_thumbnail($authordata);
			}
			else {
				echo get_avatar($authordata->ID, 96);	
			}	
			
			//Display author profile, with link to full profile
			$author_posts_link = get_author_posts_url($authordata->ID, $authordata->user_nicename );
			echo '<div id="author-description">';
			echo $this->_getSnippet(get_the_author_description(),$instance['charlimit'],'...').'&nbsp;<i><a href="'.$author_posts_link.'" title="Read full Profile">'.$instance['readfulltext'].'</a></i>';			
			echo "</div><!--#author-description-->";
			echo '<div id="author-link"><a href="'.$author_posts_link.'" title="More articles by this author">'.$instance['moretext'] .'</a></div>';
			echo "</div><!--#author-profile-->";
			echo "</div><!--#author-spotlight-->";
			
			if($instance['isLast']){
				echo $after_widget;  
			}
		}
	}
	
	// Returns a trimmed String of specified length
	function _getSnippet($text, $length=1000, $tail="...") {
		$text = trim($text);
		$txtl = strlen($text);
		if($txtl > $length) {
			for($i=1;$text[$length-$i]!=" ";$i++) {
				if($i == $length) {
					return substr($text,0,$length) . $tail;
				}
			}
			$text = substr($text,0,$length-$i+1) . $tail;
		}
		return $text;
	}
	
	function _getIconsAsArray() {
		return array(
			'home' => array(
				'img_src' => $this->icon_image_url . 'home.png',
				'img_title' => 'Homepage',
				'img_seperator' => '&nbsp;'
			),
			'facebook' => array(
				'img_src' => $this->icon_image_url . 'facebook.png',
				'img_title' => 'Facebook',
				'img_seperator' => '&nbsp;'
			),
			'twitter' => array(
				'img_src' => $this->icon_image_url . 'twitter.png',
				'img_title' => 'Twitter',
				'img_seperator' => '&nbsp;'
			),
			'linkedin' => array(
				'img_src' => $this->icon_image_url . 'linkedin.png',
				'img_title' => 'LinkedIn',
				'img_seperator' => '&nbsp;'
			),
			'flickr' => array(
				'img_src' => $this->icon_image_url . 'flickr.png',
				'img_title' => 'Flickr',
				'img_seperator' => '&nbsp;'
			),
			'myspace' => array(
				'img_src' => $this->icon_image_url . 'myspace.png',
				'img_title' => 'MySpace',
				'img_seperator' => '<br/>'
			),
			'friendfeed' => array(
				'img_src' => $this->icon_image_url . 'friendfeed.png',
				'img_title' => 'Friend Feed',
				'img_seperator' => '&nbsp;'
			),
			'delicious' => array(
				'img_src' => $this->icon_image_url . 'delicious.png',
				'img_title' => 'Delicious',
				'img_seperator' => '&nbsp;'
			),
			'digg' => array(
				'img_src' => $this->icon_image_url . 'digg.png',
				'img_title' => 'Digg',
				'img_seperator' => '&nbsp;'
			),			
			'feed' => array(
				'img_src' => $this->icon_image_url . 'feed.png',
				'img_title' => 'Feed',
				'img_seperator' => '&nbsp;'
			),
			'tumblr' => array(
				'img_src' => $this->icon_image_url . 'tumblr.png',
				'img_title' => 'Tumblr',
				'img_seperator' => '&nbsp;'
			),
			'youtube' => array(
				'img_src' => $this->icon_image_url . 'youtube.png',
				'img_title' => 'YouTube',
				'img_seperator' => '<br/>'
			),
			'blogger' => array(
				'img_src' => $this->icon_image_url . 'blogger.png',
				'img_title' => 'Blogger',
				'img_seperator' => '&nbsp;'
			),
			'googleplus' => array(
				'img_src' => $this->icon_image_url . 'googleplus.png',
				'img_title' => 'Google+',
				'img_seperator' => '&nbsp;'
			),
			'instagram' => array(
				'img_src' => $this->icon_image_url . 'instagram.png',
				'img_title' => 'Instagram',
				'img_seperator' => '&nbsp;'
			),
			'slideshare' => array(
				'img_src' => $this->icon_image_url . 'slideshare.png',
				'img_title' => 'Slide Share',
				'img_seperator' => '&nbsp;'
			),
			'stackoverflow' => array(
				'img_src' => $this->icon_image_url . 'stackoverflow.png',
				'img_title' => 'Stackoverflow',
				'img_seperator' => '&nbsp;'
			),
			'posterous' => array(
				'img_src' => $this->icon_image_url . 'posterous.png',
				'img_title' => 'Posterous',
				'img_seperator' => ''
			),
		);
	}
}
?>