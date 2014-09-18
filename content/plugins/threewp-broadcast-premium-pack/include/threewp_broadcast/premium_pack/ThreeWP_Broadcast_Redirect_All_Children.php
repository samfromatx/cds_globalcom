<?php

namespace threewp_broadcast\premium_pack\redirect_all_children;

/**
	@brief		Redirect all views of child posts back to the parent.
	@since		2014-07-22 18:49:01
**/
class ThreeWP_Broadcast_Redirect_All_Children
	extends \threewp_broadcast\premium_pack\base
{
	public function _construct()
	{
		$this->add_action( 'template_redirect', 'maybe_redirect' );
	}

	public function maybe_redirect()
	{
		if ( ! is_singular() )
			return;

		// We need the BC instance.
		$bc = $this->broadcast();

		global $post;
		$post_id = $post->ID;

		// Fetch the broadcast data for this post.
		$bcd = $bc->get_post_broadcast_data( get_current_blog_id(), $post_id );

		$parent = $bcd->get_linked_parent();

		if ( ! $parent )
			return;

		// Switch to the parent blog to retrieve the parent's guid.
		switch_to_blog( $parent[ 'blog_id' ] );

		$parent_post = get_post( $parent[ 'post_id' ] );

		$location = "Location: " . get_permalink( $parent[ 'post_id' ] );
		header( $location );

		die();
	}
}
$ThreeWP_Broadcast_Redirect_All_Children = new ThreeWP_Broadcast_Redirect_All_Children;
