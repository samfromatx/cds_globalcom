<?php

namespace threewp_broadcast\premium_pack\local_links\links;

/**
	@brief		Contains information about each a href link in the post content text.
	@details

	Helps keep track of which links in the post content point to which posts on the parent blog - and, consequently, which child posts on the child blogs.

	@since		20131028
**/
class link
{
	/**
		@brief		The broadcast data of the linked post.
		@since		20131028
	**/
	public $broadcast_data;

	/**
		@brief		The anchor element in the post content.
		@since		20131028
	**/
	public $element;

	/**
		@brief		The ID of the post which this link points to.
		@since		20131028
	**/
	public $post_id;
}
