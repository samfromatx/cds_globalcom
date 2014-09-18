<?php

namespace threewp_broadcast\premium_pack\queue\process_data;

/**
	@brief		Parameter class for build_process_data().
	@since		20131006
**/
class data
{
	/**
		@brief		INPUT: Display the "ready" string if the queue is empty.
		@var		$display_ready_string
		@since		20131006
	**/
	public $display_ready_string = true;

	/**
		@brief		OUTPUT: HTML to display.
		@var		$html
		@since		20131006
	**/
	public $html;

	/**
		@brief		INPUT: How many items are in the queue for this data
		@var		$item_count
		@since		20131006
	**/
	public $item_count = null;

	/**
		@brief		INPUT: ID of parent blog
		@var		$parent_blog_id
		@since		20131006
	**/
	public $parent_blog_id;

	/**
		@brief		INPUT: ID of parent post
		@var		$parent_post_id
		@since		20131006
	**/
	public $parent_post_id;
}
