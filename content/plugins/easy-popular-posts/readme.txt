=== Plugin Name ===
Contributors: christopherross
Plugin URI: http://thisismyurl.com/plugins/easy-popular-posts/
Tags: popular posts, best, post-plugins, most-viewed, popular, posts,comments, most popular, sidebar, widget, theme, php, code, plugin, post, posts
Donate link:  http://thisismyurl.com/
Requires at least: 3.0.0
Tested up to: 3.5.1
Stable tag: 2.6.5

An easy to use WordPress function to <a href='http://thisismyurl.com/plugins/easy-popular-posts/'>add popular posts to any WordPress theme</a>.

== Description ==

An easy to use WordPress function to <a href='http://thisismyurl.com/plugins/easy-popular-posts/'>add popular posts to any WordPress theme</a>.

You can also include the the list in a post using a shortcode [thisismyurl_easy_popular_posts] or as a widget.

This plugin is maintained by Christopher Ross, http://thisismyurl.com or you can find him on Twitter at http://twitter.com/thisismyurl/


== Installation ==

To install the plugin, please upload the folder to your plugins folder and active the plugin.

== Screenshots ==

1. Screenshot of Widget

== Updates ==
Updates to the plugin will be posted here, to [Christopher Ross]
(http://thisismyurl.com)

== Frequently Asked Questions ==

= How do I display the results in my PHP code? =

&lt;?php thisismyurl_easy_popular_posts(); ?&gt;

= How do I display the results in my posts? =

Include the shortcode [thisismyurl_easy_popular_posts] in any post or page.

= How do I include the results as a widget? =

On your Widgets page, simply drag and drop the widget to your sidebar!

=General results=
Without passing any parameters, the plugin will return ten results or fewer depending on how many posts you have.

&lt;?php thisismyurl_easy_popular_posts(); ?&gt;

=Specific number of results=
If you would like to return a specific number of results as your maximum:

&lt;?php thisismyurl_easy_popular_posts('count=10'); ?&gt;

=Altering the before and after values=<
By default the plugin wraps your code in list item (&lt;li&gt;) tags but you can specify how to format the results using the following code:

&lt;?php thisismyurl_easy_popular_posts('before=&lt;p&gt;&amp;after=&lt;/p&gt;'); ?&gt;


=The Order=<
You can now change the order of the results using ASC, DESC or RAND to return the results in ascending, descending or random order.

&lt;?php thisismyurl_easy_popular_posts('order=ASC'); ?&gt;


=Echo vs. Return=
Finally, if you'd like to copy the results into a variable you can return the results as follows:

&lt;?php thisismyurl_easy_popular_posts('show=false'); ?&gt;

=Combining Arguements=

If you'd like to call multiple arguments you can do so by separating them with a & symbol:

&lt;?php thisismyurl_easy_popular_posts('show=false&order=ASC'); ?&gt;


== Donations ==
If you would like to donate to help support future development of this tool, please visit [Christopher Ross]
(http://thisismyurl.com/)


== Change Log ==

= 2.6.5 =

* long overdue error checking
* fixed shortcode error

= 2.6.0 =

* added the category to the <li> tag class

= 2.5.6 =

* fixed incorrectly closed <span> tag

= 2.5.5 =

* fixed display error if no posts

= 2.5.2 =

* fixed image display in widget

= 2.5 =

* Added sort by total
* Added sort by month
* Added sort by week
* Added sort by day

= 2.1.1 =

* Added optional credit link

= 2.1 =

* Added excerpt handling
* Added thumbnail handling

= 2.0 =

* Tested up to WordPress 3.2
* Added controls to Widget
* Renamed functions for compatibility
* Reduced font size on credit link


= 1.7.3 =

* fixed credit link from Widget


= 1.7.2 =

* Fixed localhost reference
* Removed opacity attribute

= 1.7.1 =

* Fixed show=false and credit=false
* added language support
* updated admin tool


= 1.7 =

* Rewrote widget from the ground up using WP_Widget class introduced in 2.8.
* Output may differ from previous versions as tags/classes are no longer hard-coded, but now driven by the theme.


= 1.6.5 =

* removed RSS feed from thisismyurl.com
* removed manual link
* fixed credit link to work reliably for show / no-show options


= 1.6.4 =

* Added title option for Widget
* Added credit option for Widget


= 1.5.4 =

* Removed update routines, now using WP.org

= 1.5.2 =

* Fixed documentation
* Updated Links

= 1.5.1 =

* Added widget

= 1.5.0 =

* Rewrote common functions
* Removed options page (no options)
* Added credit option to plugin

= 1.1.5  =

* WP2.8.6 Compatibility Review, documentation fixes

= 1.1.0   =

* WP2.8 Compatibility Fixes

= 1.0.0 =

* Official Release

= 0.1.0 =

* Added admin menus
