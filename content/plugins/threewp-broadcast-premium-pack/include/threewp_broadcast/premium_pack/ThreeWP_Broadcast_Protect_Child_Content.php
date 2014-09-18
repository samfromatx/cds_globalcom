<?php

namespace threewp_broadcast\premium_pack\protect_child_content;

use \plainview\sdk\collections\collection;

/**
	@brief		Prevent child post content from being overwritten.
	@since		2014-07-21 23:22:56
**/
class ThreeWP_Broadcast_Protect_Child_Content
	extends \threewp_broadcast\premium_pack\base
{
	public function _construct()
	{
		$this->add_action( 'threewp_broadcast_prepare_meta_box' );
		$this->add_action( 'threewp_broadcast_broadcasting_after_switch_to_blog' );
		$this->add_action( 'threewp_broadcast_broadcasting_modify_post' );
		$this->add_action( 'threewp_broadcast_broadcasting_started' );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------

	public function threewp_broadcast_prepare_meta_box( $action )
	{
		$meta_box_data = $action->meta_box_data;
		$form = $meta_box_data->form;

		$name = 'protect_child_content';
		$input = $form->checkbox( $name )
			->checked( isset( $meta_box_data->last_used_settings[ $name ] ) )
			->label( 'Protect child content' )
			->title( 'Prevent the content of the child posts from being overwritten' );

		$action->meta_box_data->html->insert_before( 'blogs', $input );
	}

	public function threewp_broadcast_broadcasting_after_switch_to_blog( $action )
	{
		$bcd = $action->broadcasting_data;

		if ( ! isset( $bcd->protect_child_content ) )
			return;

		if ( ! $this->parent_broadcast_data->has_linked_child_on_this_blog() )
		{
			$this->debug( 'No child post on this blog.' );
			return;
		}

		// We need the child ID
		$child_post_id = $this->parent_broadcast_data->get_linked_child_on_this_blog();
		$this->debug( 'Child post ID on this blog is %s.', $child_post_id );
		// So that we can retrieve the child
		$child_post = get_post( $child_post_id );
		// And retrieve its content.
		$bcd->protect_child_content->set( get_current_blog_id(), $child_post->post_content );
		$this->debug( 'Saved %s characters of post content.', strlen( $child_post->post_content ) );
	}
	/**
		@brief		Maybe restore the post content for the child.
		@since		2014-07-21 23:37:25
	**/
	public function threewp_broadcast_broadcasting_modify_post( $action )
	{
		$bcd = $action->broadcasting_data;

		if ( ! isset( $bcd->protect_child_content ) )
			return;

		$blog_id = get_current_blog_id();
		if ( ! $bcd->protect_child_content->has( get_current_blog_id() ) )
			return;

		$text = $bcd->protect_child_content->get( get_current_blog_id() );
		$bcd->modified_post->post_content = $text;
		$this->debug( 'Restored %s characters of post content.', strlen( $text ) );
	}


	public function threewp_broadcast_broadcasting_started( $action )
	{
		$bcd = $action->broadcasting_data;

		// No link = no old child posts will be found.
		if ( ! $bcd->link )
		{
			$this->debug( 'Linking is not enabled. Plugin will not run.' );
			return;
		}

		$this->parent_broadcast_data = $this->broadcast()->get_post_broadcast_data( get_current_blog_id(), $bcd->post->ID );

		$bcd = $action->broadcasting_data;

		$checked = $bcd->meta_box_data->form->input( 'protect_child_content' )->is_checked();
		if ( ! $checked )
		{
			$this->debug( 'Child post content will not be protected.' );
			return;
		}

		$this->debug( 'Child post content will be protected.' );

		$bcd->post->post_content = '';
		$bcd->protect_child_content = new collection;
	}
}
$ThreeWP_Broadcast_Protect_Child_Content = new ThreeWP_Broadcast_Protect_Child_Content;
