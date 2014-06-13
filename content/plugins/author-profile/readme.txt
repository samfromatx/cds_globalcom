=== Author Spotlight (Widget) ===
Contributors: debashish
Tags: author,authors,profile,author profile,author bio,bio,coauthors,multiple authors,social,icons
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: 3.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A Sidebar widget to display the Author(s) profile on any Page or Post along with Social web links. 

== Description ==
Author Spotlight widget displays the profile of the author(s) with Social links (example: website, Twitter, Facebook) and profile picture or gravatar on any post or page that has an Author. The widget automatically detects the current author(s) of the displayed post or page; just drag and drop the widget on your sidebar and you are done.

To display a custom photograph with the Author's Profile you may install the [User Photo](http://wordpress.org/extend/plugins/user-photo/ "User Photo Wordpress plugin"). In absence of this plugin the 'Author Spotlight" widget will fall-back to displaying the Gravatar associated with the user. If your posts have multiple authors you may use the [Co-Authors Plus](http://wordpress.org/extend/plugins/co-authors-plus/ "Co-Authors Plus Wordpress plugin"), the Author Spotlight will then display all the author profiles one below another for such post/page.

Note that installing the User Photo or Co-Author plugin is purely optional. This widget will work fine without them, but they are nice to have.

If you face any issues with the plugin or have any suggestion/feature requests please do submit there [at this place](http://wordpress.org/tags/author-profile "Author Spotlight plugin support forum").

IMPORTANT NOTE: If you upgraded the plugin from 2.x to 3.x version then you will have to add the Sidebar Widget again and reconfigure it since due to the plugin rewrite to the new Wordpress API the old settings are no longer used.

== Installation ==

1. Download and unzip `author-profile.zip`. Then upload the folder containing `author-profile.php` to the `/wp-content/plugins/` directory.
1. Alternatively, you may use the Automatic installation feature of Wordpress.
1. Activate the plugin through the `Plugins` menu in WordPress. For details [refer this Codex page](http://codex.wordpress.org/Managing_Plugins "Managing Plugins").
1. To display `Author Spotlight`, browse to `Appearance > Widgets` and drag-and-drop the 'Author Spotlight" widget to desired sidebar. You may configure the widget according to your needs. Save your changes and you are done.
1. To enable the default look & feel of your widget you may copy/paste the suggested CSS at the end of your theme CSS file (usually `style.css`). Please refer the answer to the [FAQ section](http://wordpress.org/extend/plugins/author-profile/faq/ "Author Spotlight FAQ") for the CSS code.
1. To add the ability to add Social URLs in the User profile, please add the following add to your Theme functions file:
`
/*Add Social URLs*/
function author_spotlight_contactmethods( $contactmethods ) {
	if ( !isset( $contactmethods['twitter'] ) )
		$contactmethods['twitter'] = 'Twitter';
	if ( !isset( $contactmethods['facebook'] ) )
		$contactmethods['facebook'] = 'Facebook';
	if ( !isset( $contactmethods['linkedin'] ) )
		$contactmethods['linkedin'] = 'LinkedIn'; 	
	if ( !isset( $contactmethods['flickr'] ) )
		$contactmethods['flickr'] = 'Flickr'; 	
	if ( !isset( $contactmethods['myspace'] ) )
		$contactmethods['myspace'] = 'MySpace'; 	
	if ( !isset( $contactmethods['friendfeed'] ) )
		$contactmethods['friendfeed'] = 'Friendfeed'; 	
	if ( !isset( $contactmethods['delicious'] ) )
		$contactmethods['delicious'] = 'Delicious'; 	
	if ( !isset( $contactmethods['digg'] ) )
		$contactmethods['digg'] = 'Digg'; 	
	if ( !isset( $contactmethods['feed'] ) )
		$contactmethods['feed'] = 'XML Feed'; 	
	if ( !isset( $contactmethods['tumblr'] ) )
		$contactmethods['tumblr'] = 'Tumblr'; 	
	if ( !isset( $contactmethods['youtube'] ) )
		$contactmethods['youtube'] = 'YouTube'; 
	if ( !isset( $contactmethods['blogger'] ) )
		$contactmethods['blogger'] = 'Blogger'; 
	if ( !isset( $contactmethods['googleplus'] ) )
		$contactmethods['googleplus'] = 'Google+'; 
	if ( !isset( $contactmethods['instagram'] ) )
		$contactmethods['instagram'] = 'Instagram'; 
	if ( !isset( $contactmethods['slideshare'] ) )
		$contactmethods['slideshare'] = 'Slideshare'; 
	if ( !isset( $contactmethods['stackoverflow'] ) )
		$contactmethods['stackoverflow'] = 'Stackoverflow'; 
	if ( !isset( $contactmethods['posterous'] ) )
		$contactmethods['posterous'] = 'Posterous'; 
     		
	return $contactmethods;
}

add_filter('user_contactmethods','author_spotlight_contactmethods');
`
== Frequently Asked Questions ==

= What if I don't set any value in the widget settings? =

If you don't set any values then widget displays the profile with some default text and values. The default widget title is 'Author Spotlight', other label defaults are `More posts by the Author &raquo;` and `Read Full`. The number of characters displayed in author profile is limited to 1000 characters by default. Social URLs are turned off by default.

= Does the widget only work with Single pages? =

Beginning 3.0 version this plugin works with any Page, including the Single (or Post) page. Till the time this widget can detect an author, it can display the profile.

= I don't like my avatar image, can I upload my own photo? =

If you wish to display a custom photograph to go with the Author's Profile you may install the [User Photo](http://wordpress.org/extend/plugins/user-photo/ "User Photo Wordpress plugin"). In absence of this plugin the 'Author Spotlight" widget will fallback to displaying the gravatar associated with the user.

= The widget doesn't display my website OR my profile details are incomplete! =

The widget picks details from User's Wordpress profile. Needless to say that if this information is not there the widget cannot display it. To set your own user-profile details go to `Users > Your Profile`. If you are an admin of the blog the you may also edit the profile of other users and add their profile image if the `User Photo` plugin is installed.

= The link to the author profile page doesn't work for me! =

For the link to the full profile page of the author to work you would need to ensure that your wordpress theme contains the `author.php`. [Refer this codex page](http://codex.wordpress.org/Author_Templates "Author Templates") for details on the author templates.

= My blog posts are written by multiple authors, how can I display all profiles on the post page? =

Beginning 2.0 version this plugin supports the excellent [Co-Authors Plus](http://wordpress.org/extend/plugins/co-authors-plus/ "Co-Authors Plus Wordpress plugin") plugin, if you have this plugin installed all author profiles would show up on the Single page without any extra configuration. This plugin lets you add multiple authors to a post. Remember that our plugin works even without the Co-Authors Plus plugin.

= The plugin configuration screen shows many social icons, but where do the Authors add these URLs? The Author profile page of Wordpress only allows adding Website, AIM, Yahoo IM & Jabber / Google Talk. =

In order to add the facility to add additional contact methods or social URLs you need to copy-paste the code provided in the [Installation](http://wordpress.org/extend/plugins/author-profile/installation "Installation") section in your Theme functions file. Once added, you will these additional fields in the User profile page.

= I was using the 2.x version of the plugin and the Widget stopped working after upgrading to 3.x. What's wrong? =

If you upgraded the plugin from 2.x to 3.x version then you will have to add the Sidebar Widget again and reconfigure it since due to the plugin rewrite to the new Wordpress API the old settings are no longer used. If you are using any caching plugin (example SuperCache) then don't forget to refresh your cache.

= I don't want to add or display the Social icons for the widget. How do I turn it off? =

Adding Social icons is optional and by default its turned off. Just ensure that on the Widget Admin screen all checkboxes against these icons are un-checked. The icons wouldn't show up on your blog/website.

= My Author Profiles don't at all look like the ones you display in the screenshots =

That's because your theme style must be altered accordingly. Please find below a suggested CSS (feel free to customize to your needs):
`
/* Author Spotlight style begins */
#author-spotlight {
	background: #f2f7fc;
	border-top: 4px solid #000;
	clear: both;
	font-size: 13px;
	line-height: 15px;
	overflow: hidden;
	padding: 10px;
}
#author-spotlight #author-avatar {
	background: #fff;
	border: 1px solid #e7e7e7;
	float: left;
	align:left;
	padding: 5px;
}
#author-spotlight #author-profile{
	float: left;
}
#author-spotlight #author-description{
	margin-top:5px; 
}
#author-spotlight #author-link{
	margin-top:5px; 
    float:right;	        
}
#author-spotlight #social-icons{
	padding-bottom:16px; 
    margin-top:2px;
	padding-top:0px;
	align:center;
	background:#DFF3F9;
}
#author-spotlight #social-icons img{
	margin:0px;
	border:none;
	background:none;
}
#author-spotlight h2 {
	color: #000;
	font-size: 100%;
	font-weight: bold;
	margin-bottom: 0;
}
#author-spotlight img {
	align:left;
	float:left;
	padding:5px;
	margin:5px 10px 0.5px 0px;
	background: #fff;
	border: 1px solid #e7e7e7;
}
/* Author Spotlight style ends */
`
== Screenshots ==

1. Widget configuration screen. 
2. Multiple author profiles (co-authors) displayed on the sidebar. 
3. This is how the widget looks like on the single-page sidebar after configuration. 

== Changelog ==
= 3.2 =
* Fixed issues with description text inadvertently showing up as hyperlink for some icons.
* Added support for 6 more social sites (Google+, Posterous, Stackoverflow, Slideshare, Instagram and Blogger). Remember to update the `author_spotlight_contactmethods` function in your theme functions file to be able to add these URLs to the author profiles (refer the installation instructions).

= 3.1 =
Fixed issues with image path. Some other minor fixes.

= 3.0 =
* Rewritten to the new Widget API. Now works with any page (not just "Single"). Added feature to display Social icons (optional). Provided example CSS.

= 2.1 =
* Bug fix to correct a method call.

= 2.0 =
* Added support for the "Co-Authors Plus" plugin to display multiple author profiles for co-authored posts.

= 1.2 =
* Minor change: Fallback to show Website URL label only when URL is present.

= 1.1 =
* Bug-fix to ensure proper fall-back if the User-photo plugin doesn't exist. Added plugin URL.

= 1.0 =
* Initial public release.

== Upgrade Notice ==
= 3.2 =
The plugin author advises an upgrade for a minor bug-fix and to gain advantage from the support for 6 additional social icons. Remember to update the `author_spotlight_contactmethods` function in your theme functions file to be able to add these URLs to the author profiles (refer the installation instructions).

= 3.1 =
If you use version 3.0 of this plugin, you must update to 3.1 to fix some issues.

= 3.0 =
This release is a rewrite to the new Widget API and would work with PHP5.x, it has not been tested with PHP4. If you are using PHP4 then we recommend not to upgrade and continue using the 2.1 version of this plugin.

= 2.1 =
This release corrects a bug in the 2.0 release. Please upgrade if you are using the 2.0 version.