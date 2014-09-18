<?php

namespace threewp_broadcast\premium_pack\local_links;

use \threewp_broadcast\premium_pack\local_links\links\link;
use \threewp_broadcast\premium_pack\local_links\links\links;
use \threewp_broadcast\premium_pack\local_links\meta_box_data\item;
use \plainview\sdk\collections\collection;
use \DOMDocument;

/**
	@brief		Automatically updates links to local posts on each child blog.
	@since		20131014
**/
class ThreeWP_Broadcast_LocalLinks
	extends \threewp_broadcast\premium_pack\base
{
	protected $sdk_version_required = 20131015;

	protected $site_options = [
	];

	public function _construct()
	{
		$this->add_filter( 'threewp_broadcast_broadcasting_after_switch_to_blog' );
		$this->add_filter( 'threewp_broadcast_prepare_meta_box' );
		$this->add_filter( 'threewp_broadcast_prepare_broadcasting_data' );

	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Admin
	// --------------------------------------------------------------------------------------------

	public function activate()
	{
		if ( ! class_exists( 'DOMDocument' ) )
			wp_die( 'ThreeWP Broadcast Local Links requires the DOM PHP extension to be installed.' );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------

	public function threewp_broadcast_broadcasting_after_switch_to_blog( $action )
	{
		$bcd = $action->broadcasting_data;
		if ( ! property_exists( $bcd, 'local_links' ) )
			return;
		$links = $bcd->local_links;
		$blog_id = get_current_blog_id();

		$html = new DOMDocument;

		// Go through all of the links in the post content.
		foreach( $links as $link )
		{
			if ( ! $link->broadcast_data->has_linked_child_on_this_blog( $blog_id ) )
				continue;

			// There exists a child on this blog.
			$child_post_id = $link->broadcast_data->get_linked_child_on_this_blog();
			$new_post_url = get_permalink( $child_post_id );

			// Get the complete <a> element.
			$old_anchor = $link->element;
			$html->loadHTML( $old_anchor );
			// And replace just the URL.
			$new_anchor = str_replace( $html->getElementsByTagName( 'a' )->item( 0 )->getAttribute( 'href' ), $new_post_url, $old_anchor );

			// Modify the post.
			$bcd->new_post[ 'post_content' ] = str_replace( $old_anchor, $new_anchor, $bcd->new_post[ 'post_content' ] );
		}
	}

	/**
		@brief		Add the boxes.
		@since		20131027
	**/
	public function threewp_broadcast_prepare_meta_box( $action )
	{
		$meta_box_data = $action->meta_box_data;
		$item = new item( $meta_box_data );
		$meta_box_data->html->insert_before( 'blogs', 'local_links', $item );
	}

	/**
		@brief		Get a list of all the local links.
		@since		20131028
	**/
	public function threewp_broadcast_prepare_broadcasting_data( $action )
	{
		$bcd = $action->broadcasting_data;
		$mbd = $bcd->meta_box_data;
		$item = $mbd->html->get( 'local_links' );
		$local_links = $item->inputs->get( 'local_links' );
		if ( ! $local_links->is_checked() )
			return;

		// The user has requested that the local links be updated!

		// Get the post content
		$content = $bcd->post->post_content;

		if ( strlen( $content ) < 1 )
			return;

		// Create a DOMDocument.
		$html = new DOMDocument;
		// @ because sometimes HTML is badly formed.
		@$html->loadHTML( $content );

		// We need to get the url of this blog.
		$url = get_bloginfo( 'url' );

		$links = new links;

		// Find all anchors
		$anchors = $html->getElementsByTagName( 'a' );
		foreach( $anchors as $a )
		{
			$href = $a->getAttribute( 'href' );
			$local = false;

			// Does the href contain a site url?
			if ( !$local && ( strpos( $href, $url ) !== false ) )
				$local = true;

			// No scheme = local
			if ( ! $local )
			{
				$parsed_href = parse_url( $href );
				$parsed_href = (object) $parsed_href;
				if ( ! $local && ! property_exists( $parsed_href, 'scheme' ) )
					$local = true;
			}

			if ( ! $local )
				continue;

			// The path is local. Try to find the associated post.
			$post_id = url_to_postid( $href );
			if ( $post_id < 1 )
				continue;

			// This link exists as a post.
			$link = new link;
			$link->element = $a->ownerDocument->saveXML( $a );
			$link->post_id = $post_id;

			// Do not get the broadcast data yet.

			$links->append( $link );
		}

		// Tell the broadcast data cache to get them all at once.
		$blog_id = get_current_blog_id();
		$ids = $links->get_post_ids();
		$cache = $this->broadcast()->broadcast_data_cache();
		$cache->expect( $blog_id, $ids );
		foreach( $links as $index => $link )
		{
			$post_broadcast_data = $cache->get_for( $blog_id, $link->post_id );
			if ( $post_broadcast_data->has_linked_children() )
				$link->broadcast_data = $post_broadcast_data;
			else
				$links->forget( $index );
		}

		// Are there any links left after checking the broadcast data?
		if ( count( $links ) < 1 )
			return;

		// Save the local links in the broadcasting data, ready to be used for each child post.
		$bcd->local_links = $links;
	}
}

$ThreeWP_Broadcast_LocalLinks = new ThreeWP_Broadcast_LocalLinks;
