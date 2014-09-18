<?php

namespace threewp_broadcast\premium_pack\sendtomany;

use \threewp_broadcast\broadcast_data\blog;

/**
	@brief		Allows users to broadcast to all blogs in the network without having to be a user of the blog.
	@since		20140104
**/
class ThreeWP_Broadcast_All_Blogs
	extends \threewp_broadcast\premium_pack\base
{
	protected $sdk_version_required = 20130505;		// user_id()

	public function _construct()
	{
		$this->add_filter( 'threewp_broadcast_get_user_writable_blogs' );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------
	/**
		@brief		Return a list of all the blogs in the network.
		@since		20140104
	**/
	public function threewp_broadcast_get_user_writable_blogs( $filter )
	{
		$blogs = wp_get_sites( [ 'limit' => PHP_INT_MAX ] );
		foreach( $blogs as $blog)
		{
			// After having retrieved the blog, get the details because wp_get_sites doesn't do that.
			$blog = get_blog_details( $blog[ 'blog_id' ] );
			$blog = blog::make( $blog );
			$blog->id = $blog->id;
			$filter->blogs->set( $blog->id, $blog );
		}
		$filter->blogs->sort_logically();
		$filter->applied();
		return $filter;
	}
}

$ThreeWP_Broadcast_All_Blogs = new ThreeWP_Broadcast_All_Blogs;
