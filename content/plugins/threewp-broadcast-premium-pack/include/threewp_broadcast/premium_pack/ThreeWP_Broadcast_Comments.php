<?php

namespace threewp_broadcast\premium_pack\comments;

use \plainview\sdk\collections\collection;

/**
	@brief		Adds support for broadcasting of comments.
	@since		2014-05-20 18:15:43
**/
class ThreeWP_Broadcast_Comments
	extends \threewp_broadcast\premium_pack\base
{
	public function _construct()
	{
		$this->add_action( 'threewp_broadcast_prepare_meta_box' );
		$this->add_action( 'threewp_broadcast_broadcasting_before_restore_current_blog' );
		$this->add_action( 'threewp_broadcast_broadcasting_started' );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------
	/**
		@brief		Allow the user to choose what to do with comments.
		@since		2014-05-20 18:16:32
	**/
	public function threewp_broadcast_prepare_meta_box( $action )
	{
		$meta_box_data = $action->meta_box_data;
		$form = $meta_box_data->form;

		$name = 'broadcast_comments';
		$broadcast_comments = $form->checkbox( $name )
			->checked( isset( $meta_box_data->last_used_settings[ $name ] ) )
			->label( 'Comments' )
			->title( 'Broadcast the comments of the parent to the children' );

		$action->meta_box_data->html->insert_before( 'custom_fields', $broadcast_comments );
	}

	/**
		@brief		Maybe broadcast the comments.
		@since		2014-05-20 18:28:40
	**/
	public function threewp_broadcast_broadcasting_before_restore_current_blog( $action )
	{
		$bcd = $action->broadcasting_data;

		if ( ! isset( $bcd->comments ) )
			return;

		// Delete the current comments from the child post.
		$child_post_comments = get_comments( [ 'post_id' => $bcd->new_post()->ID ] );
		foreach( $child_post_comments as $index => $child_post_comment )
		{
			$this->debug( 'Deleting existing child comment %s.', $index + 1 );
			wp_delete_comment( $child_post_comment->comment_ID, true );		// True to force delete.
		}

		// Insert these new comments
		foreach( $bcd->comments->comments->to_array() as $index => $comment )
		{
			// The post ID must be updated for this new post.
			$comment->comment_post_ID = $bcd->new_post()->ID;
			// The comment ID should be removed.
			unset( $comment->comment_ID );
			$this->debug( 'Inserting comment %s.', $index + 1 );
			wp_insert_comment( (array)$comment );
		}
	}

	/**
		@brief		Prepare the broadcasting of comments. Maybe.
		@since		2014-05-20 18:17:36
	**/
	public function threewp_broadcast_broadcasting_started( $action )
	{
		$bcd = $action->broadcasting_data;

		$broadcast_comments = $bcd->meta_box_data->form->input( 'broadcast_comments' )->is_checked();
		if ( ! $broadcast_comments )
		{
			$this->debug( 'User did not request that comments be synced.' );
			return;
		}

		$this->debug( 'Comments are going to be synced.' );

		$bcd->comments = new collection;
		$bcd->comments->comments = new collection( get_comments( [ 'post_id' => $bcd->post->ID ] ) );
		$this->debug( '%s comments are going to be broadcasted.', $bcd->comments->comments->count() );
	}
}
$ThreeWP_Broadcast_Comments = new ThreeWP_Broadcast_Comments;
