<?php
/*
Plugin Name: Easy Popular Posts
Plugin URI: http://thisismyurl.com/plugins/easy-popular-posts/
Description: An easy to use WordPress function to add Popular Posts to any theme.
Author: Christopher Ross
Tags: future, upcoming posts, upcoming post, upcoming, draft, Post, popular, preview, plugin, post, posts
Author URI: http://thisismyurl.com/
Version: 2.6.5
*/


/**
 * Easy Popular Posts core file
 *
 * This file contains all the logic required for the plugin
 *
 * @link		http://wordpress.org/extend/plugins/easy-popular-posts/
 *
 * @package 	Easy Popular Posts
 * @copyright	Copyright (c) 2008, Chrsitopher Ross
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License, v2 (or newer)
 *
 * @since 		Easy Popular Posts 1.0
 */

add_shortcode( 'thisismyurl_easy_popular_posts', 'thisismyurl_easy_popular_posts_shortcode' );

function thisismyurl_easy_popular_posts_shortcode( $options = '' ) {
	$plugin_defaults = array(
		"count"    => "10",
		"comments" => "0",
		"before"   => "<li>",
		"after"    => "</li>\n",
		"order"    => "desc",
		"nofollow" => false,
		"excerpt" => false,
		"creditlink" => false,
		"featureimage" => false,
		"link" => true,
		"displaytype" => "comment",
		"show"     => 		FALSE
	 );


	$instance = wp_parse_args( $options, $plugin_defaults );

	return thisismyurl_easy_popular_posts( $instance );
}


function thisismyurl_easy_popular_posts( $options = '' ) {

	$plugin_defaults = array(
		"count"    => "10",
		"comments" => "0",
		"before"   => "<li>",
		"after"    => "</li>\n",
		"order"    => "desc",
		"nofollow" => false,
		"excerpt" => false,
		"creditlink" => false,
		"featureimage" => false,
		"link" => true,
		"displaytype" => "comment",
		"show"     => true
	 );


	$options = wp_parse_args( $options, $plugin_defaults );



	if ( $options['displaytype'] == 'comment' ) {

		if ( strtolower( $options['order'] ) == "desc" ) {
		$sqlorder = "ORDER BY comment_count DESC";
		}
		if ( strtolower( $options['order'] ) == "asc" ) {
			$sqlorder = "ORDER BY comment_count ASC";
		}
		if ( strtolower( $options['order'] ) == "rand" ) {
			$sqlorder = "ORDER BY RAND( )";
		}
		global $wpdb;
		$popular_posts = $wpdb->get_results( "
			SELECT ID
			FROM " . $wpdb->posts . "
			WHERE post_type='post' AND post_status = 'publish' AND comment_count >= " . $options['comments']."
			" . $sqlorder . " LIMIT 0 , " . $options['count']
		 );

	} else if ( $options['displaytype'] == 'total' ) { $myposts = thisismyurl_popular_posts_objectToArray( json_decode( get_option( "thisismyurl_popular_posts_total" ) ) );
	} else if ( $options['displaytype'] == 'monthly' ) { $myposts = thisismyurl_popular_posts_objectToArray( json_decode( get_option( "thisismyurl_popular_posts_month_".date( 'Y_m' ) ) ) );
	} else if ( $options['displaytype'] == 'weekly' ) { $myposts = thisismyurl_popular_posts_objectToArray( json_decode( get_option( "thisismyurl_popular_posts_week_".date( 'Y_W' ) ) ) );
	} else if ( $options['displaytype'] == 'daily' ) { $myposts = thisismyurl_popular_posts_objectToArray( json_decode( get_option( "thisismyurl_popular_posts_day_".date( 'Y_z' ) ) ) );
	}

		if ( isset( $myposts ) && count( $myposts )>0 || count( $popular_posts )>0 ) {

			if ( isset( $myposts ) ) {
				arsort( $myposts );
				if ( $myposts ) {

					foreach ( $myposts as $key=>$value ) {

						if ( count( $popular_posts ) < $options['count'] && $value > 0 ) {
							$post = new stdClass;
							$post->ID = $key;
							$popular_posts[] = $post;
						}

					}
				}
			}

		if ( count( $popular_posts )>0 ) {
			$popular = '';


				foreach ( $popular_posts as $post ) {



					if ( $options['before'] == '<li>' && isset( $the_post ) ) {

						$post_categories = wp_get_post_categories( $the_post->ID );
						$category_slugs = '';

						foreach( $post_categories as $category ){
							$category_details = get_category( $category );
							$category_slugs .= ' category-' . $category_details->slug;
						}

						$popular .= "\n" . '<li class="' . trim( $category_slugs ) . '">';
					}
					else
						$popular .= $options['before'];

					$the_post = get_post( $post->ID );

					if ( $options['link'] == 'true' ) {
						$popular .= "<a href='".get_permalink( $the_post->ID )."' ";
						if ( $options['nofollow'] == 'true' ) {$popular .= 'nofollow';}
						$popular .= ">";
					}
					$popular .=  "<span class='title'>".get_the_title( $the_post->ID )."</span>";
					if ( $options['link'] == 'true' ) {$popular .= "</a>";}
					if ( $options['featureimage'] == 'true' ) {
						if ( has_post_thumbnail( $the_post->ID ) ) {$popular .=  "<div class='thumbnail'>".get_the_post_thumbnail( $the_post->ID,'thumbnail' )."</div>";}
					}
					if ( $options['excerpt'] == 'true' ) {$popular .=  "<div class='excerpt'>".$the_post->post_excerpt."</div>";}
					$popular .=  $options['after'];
				}
				if ( $options['creditlink'] == 'true' && is_home( ) ) {
					$popular .=  $options['before']."<a class='creditlink' href='http://thisismyurl.com/plugins/easy-popular-posts/'>Easy Popular Posts WordPress Plugin</a>".$options['after'];
				}
				if ( $options['show'] ) {
					echo $popular;
				} else {
					return $popular;
				}
			}
		}


}

class thisismyurl_popular_posts_widget extends WP_Widget
{


	function thisismyurl_popular_posts_widget( ){
		$widget_ops = array( 'classname' => 'widget_thisismyurl_popular_posts', 'description' => __( "A WordPress widget to add popular posts to any WordPress theme. Learn more at http://thisismyurl.com" ) );
		$control_ops = array( 'width' => 300, 'height' => 300 );
		$this->WP_Widget( 'thisismyurl_popular_posts_widget', __( 'Easy Popular Posts' ), $widget_ops, $control_ops );
	}

	function update( $new_instance, $old_instance ){
		$instance = $old_instance;
		$instance['title'] = strip_tags( stripslashes( $new_instance['title'] ) );
		$instance['count'] = strip_tags( stripslashes( $new_instance['count'] ) );
		$instance['order'] = strip_tags( stripslashes( $new_instance['order'] ) );
		$instance['link'] = strip_tags( stripslashes( $new_instance['link'] ) );
		$instance['excerpt'] = strip_tags( stripslashes( $new_instance['excerpt'] ) );
		$instance['featureimage'] = strip_tags( stripslashes( $new_instance['featureimage'] ) );
		$instance['creditlink'] = strip_tags( stripslashes( $new_instance['creditlink'] ) );
		$instance['displaytype'] = strip_tags( stripslashes( $new_instance['displaytype'] ) );

		return $instance;
	}

	function form( $instance ){
		$instance = wp_parse_args( ( array ) $instance, array( 'title'=>'Popular Posts', 'count'=>'5', 'order'=>'desc', 'link'=>'false', 'excerpt'=>'false', 'featureimage'=>'false','displaytype'=>'comment','creditlink'=>'false' ) );

		$title = htmlspecialchars( $instance['title'] );
		$count = htmlspecialchars( $instance['count'] );
		$order = htmlspecialchars( $instance['order'] );
		$link = htmlspecialchars( $instance['link'] );
		$excerpt = htmlspecialchars( $instance['excerpt'] );
		$featureimage = htmlspecialchars( $instance['featureimage'] );
		$creditlink = htmlspecialchars( $instance['creditlink'] );
		$displaytype = htmlspecialchars( $instance['displaytype'] );
		$countoption ='';
		foreach (array(3,5,10,15,20,25) as $i) {
			$countoption .= "<option value='$i' ";
			if ( $count == $i ) {$countoption .= "selected";}
			$countoption .= ">$i</option>";
		}

		$displaytypeoption = '';
		$orderoption = '';
		$linkoption = '';
		$excerptoption = '';
		$featureimageoption = '';
		$creditlinkoption = '';

		if ( $displaytype == "comment" ) {$displaytypeoption .= "<option value='comment' selected >Comment</option>";} else {$displaytypeoption .= "<option value='comment'>Comment</option>";}
		if ( $displaytype == "total" ) {$displaytypeoption .= "<option value='total' selected >Total</option>";} else {$displaytypeoption .= "<option value='total'>Total</option>";}
		if ( $displaytype == "monthly" ) {$displaytypeoption .= "<option value='monthly' selected >Monthly</option>";} else {$displaytypeoption .= "<option value='monthly'>Monthly</option>";}
		if ( $displaytype == "weekly" ) {$displaytypeoption .= "<option value='weekly' selected >Weekly</option>";} else {$displaytypeoption .= "<option value='weekly'>Weekly</option>";}
		if ( $displaytype == "daily" ) {$displaytypeoption .= "<option value='daily' selected >Daily</option>";} else {$displaytypeoption .= "<option value='daily'>Daily</option>";}

		if ( $order == "desc" ) {$orderoption .= "<option value='desc' selected >Descending</option>";} else {$orderoption .= "<option value='desc'>Descending</option>";}
		if ( $order == "asc" ) {$orderoption .= "<option value='asc' selected >Ascending</option>";} else {$orderoption .= "<option value='asc'>Ascending</option>";}


		if ( $link == "true" ) {$linkoption .= "<option value='true' selected >Yes</option>";} else {$linkoption .= "<option value='true'>Yes</option>";}
		if ( $link == "false" ) {$linkoption .= "<option value='false' selected >No</option>";} else {$linkoption .= "<option value='false'>No</option>";}

		if ( $excerpt == "true" ) {$excerptoption .= "<option value='true' selected >Yes</option>";} else {$excerptoption .= "<option value='true'>Yes</option>";}
		if ( $excerpt == "false" ) {$excerptoption .= "<option value='false' selected >No</option>";} else {$excerptoption .= "<option value='false'>No</option>";}

		if ( $featureimage == "true" ) {$featureimageoption .= "<option value='true' selected >Yes</option>";} else {$featureimageoption .= "<option value='true'>Yes</option>";}
		if ( $featureimage == "false" ) {$featureimageoption .= "<option value='false' selected >No</option>";} else {$featureimageoption .= "<option value='false'>No</option>";}

		if ( $creditlink == "true" ) {$creditlinkoption .= "<option value='true' selected >Yes</option>";} else {$creditlinkoption .= "<option value='true'>Yes</option>";}
		if ( $creditlink == "false" ) {$creditlinkoption .= "<option value='false' selected >No</option>";} else {$creditlinkoption .= "<option value='false'>No</option>";}

		# Output the options

		echo '	<p style="text-align:left;"><label for="' . $this->get_field_name( 'title' ) . '">' . __( 'Title:' ) . '</label><br />
				<input style="width: 300px;" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . $title . '" />
				</p>';

		echo '	<p style="text-align:left;"><label for="' . $this->get_field_name( 'count' ) . '">' . __( 'Count:' ) . '</label><br />
				<select id="' . $this->get_field_id( 'count' ) . '" name="' . $this->get_field_name( 'count' ) . '">'.$countoption.'</select>
				</p>';

		echo '	<p style="text-align:left;"><label for="' . $this->get_field_name( 'displaytype' ) . '">' . __( 'Display Type:' ) . '</label><br />
				<select id="' . $this->get_field_id( 'displaytype' ) . '" name="' . $this->get_field_name( 'displaytype' ) . '">'.$displaytypeoption.'</select>
				</p>';

		echo '	<p style="text-align:left;"><label for="' . $this->get_field_name( 'order' ) . '">' . __( 'Order:' ) . '</label><br />
				<select id="' . $this->get_field_id( 'order' ) . '" name="' . $this->get_field_name( 'order' ) . '">'.$orderoption.'</select>
				</p>';

		echo '	<p style="text-align:left;"><label for="' . $this->get_field_name( 'link' ) . '">' . __( 'Include Link?' ) . '</label><br />
				<select id="' . $this->get_field_id( 'link' ) . '" name="' . $this->get_field_name( 'link' ) . '">'.$linkoption.'</select>
				</p>';

		echo '	<p style="text-align:left;"><label for="' . $this->get_field_name( 'featureimage' ) . '">' . __( 'Include Image?' ) . '</label><br />
				<select id="' . $this->get_field_id( 'featureimage' ) . '" name="' . $this->get_field_name( 'featureimage' ) . '">'.$featureimageoption.'</select>
				</p>';

		echo '	<p style="text-align:left;"><label for="' . $this->get_field_name( 'excerpt' ) . '">' . __( 'Include Excerpt?' ) . '</label><br />
				<select id="' . $this->get_field_id( 'excerpt' ) . '" name="' . $this->get_field_name( 'excerpt' ) . '">'.$excerptoption.'</select>
				</p>';

		echo '	<p style="text-align:left;"><label for="' . $this->get_field_name( 'creditlink' ) . '">' . __( 'Include Credit Link?' ) . '</label><br />
				<select id="' . $this->get_field_id( 'creditlink' ) . '" name="' . $this->get_field_name( 'creditlink' ) . '">'.$creditlinkoption.'</select>
				</p>';

	}


	/*  Displays the Widget */
	function widget( $args, $instance ){
		extract( $args );
		$instance = wp_parse_args( ( array ) $instance, array( 'title'=>'Popular Posts', 'count'=>'5', 'order'=>'desc', 'link'=>'false', 'excerpt'=>'false', 'featureimage'=>'false','displaytype'=>'comment','creditlink'=>'false' ) );

		# Before the widget
		echo $before_widget;
		echo '<h3 class="widgettitle">'.$instance['title'].'</h3>';
		echo '<ul>'.thisismyurl_easy_popular_posts( 'show=0&link='.$instance['link'].'&count='.$instance['count'].'&order='.$instance['order'].'&excerpt='.$instance['excerpt'].'&displaytype='.$instance['displaytype'].'&featureimage='.$instance['featureimage'].'&creditlink='.$instance['creditlink'] ).'</ul>';
		echo $after_widget;

	}

}// END class

function thisismyurl_popular_posts_widget_Init( ) {
	register_widget( 'thisismyurl_popular_posts_widget' );
}
add_action( 'widgets_init', 'thisismyurl_popular_posts_widget_Init' );

function thisismyurl_popular_posts_count( $content ) {
	if ( is_single( ) ) {

		global $post;
		$id = $post->ID;

		$total_counts = 	thisismyurl_popular_posts_objectToArray( json_decode( get_option( "thisismyurl_popular_posts_total" ) ) );
		$monthly_counts = 	thisismyurl_popular_posts_objectToArray( json_decode( get_option( "thisismyurl_popular_posts_month_".date( 'Y_m' ) ) ) );
		$weekly_counts = 	thisismyurl_popular_posts_objectToArray( json_decode( get_option( "thisismyurl_popular_posts_week_".date( 'Y_W' ) ) ) );
		$daily_counts = 	thisismyurl_popular_posts_objectToArray( json_decode( get_option( "thisismyurl_popular_posts_day_".date( 'Y_z' ) ) ) );


		$total_counts[$id]++;
		$monthly_counts[$id]++;
		$weekly_counts[$id]++;
		$daily_counts[$id]++;

		update_option( "thisismyurl_popular_posts_total",json_encode( $total_counts ) );
		update_option( "thisismyurl_popular_posts_month_".date( 'Y_m' ),json_encode( $monthly_counts ) );
		update_option( "thisismyurl_popular_posts_week_".date( 'Y_W' ),json_encode( $weekly_counts ) );
		update_option( "thisismyurl_popular_posts_day_".date( 'Y_z' ),json_encode( $daily_counts ) );

	}
	return $content;
}
add_action( 'the_content', 'thisismyurl_popular_posts_count' );

function thisismyurl_popular_posts_objectToArray( $d ) {
	if ( is_object( $d ) ) {
		// Gets the properties of the given object
		// with get_object_vars function
		$d = get_object_vars( $d );
	}

	if ( is_array( $d ) ) {
		/*
		* Return array converted to object
		* Using __FUNCTION__ ( Magic constant )
		* for recursive call
		*/
		return array_map( __FUNCTION__, $d );
	}
	else {
		// Return array
		return $d;
	}
}
