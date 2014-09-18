<?php

namespace threewp_broadcast\premium_pack\protect_child_attachments;

use threewp_broadcast\premium_pack\protect_child_attachments\item;

use \plainview\sdk\collections\collection;

/**
	@brief		Protect the child post's attachments instead of deleting them when updating a broadcast.
	@since		2014-06-20 11:47:31
**/
class ThreeWP_Broadcast_Protect_Child_Attachments
	extends \threewp_broadcast\premium_pack\base
{
	protected $sdk_version_required = 20131015;

	public function _construct()
	{
		$this->add_filter( 'threewp_broadcast_prepare_meta_box' );
		$this->add_filter( 'threewp_broadcast_prepare_broadcasting_data' );
	}

	public function threewp_broadcast_prepare_meta_box( $action )
	{
		$meta_box_data = $action->meta_box_data;
		$item = new item( $meta_box_data );
		$meta_box_data->html->insert_before( 'blogs', 'protect_child_attachments', $item );
	}

	public function threewp_broadcast_prepare_broadcasting_data( $action )
	{
		$bcd = $action->broadcasting_data;
		$mbd = $bcd->meta_box_data;
		$item = $mbd->html->get( 'protect_child_attachments' );
		$protect_child_attachments = $item->inputs->get( 'protect_child_attachments' );
		if ( ! $protect_child_attachments->is_checked() )
			return;

		// The user has requested that the attachments be kept.
		$bcd->delete_attachments = false;
	}
}

$ThreeWP_Broadcast_Protect_Child_Attachments = new ThreeWP_Broadcast_Protect_Child_Attachments;
