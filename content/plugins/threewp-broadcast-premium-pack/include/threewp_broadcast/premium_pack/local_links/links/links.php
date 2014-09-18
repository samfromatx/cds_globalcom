<?php

namespace threewp_broadcast\premium_pack\local_links\links;

/**
	@brief		Container for a collection of link objects.
	@since		20131028
**/
class links
extends \plainview\sdk\collections\collection
{
	/**
		@brief		Return an array of all of the post IDs in all of the items (links).
		@since		20131028
	**/
	public function get_post_ids()
	{
		$ids = [];
		foreach( $this->items as $item )
			$ids[ $item->post_id ] = true;
		return array_keys( $ids );
	}
}
