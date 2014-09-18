<?php

namespace threewp_broadcast\premium_pack\Purge_Children;

/**
	@brief		Allow purging of child posts, which removes their attached data.
	@since		2014-04-17 23:55:31
**/
class ThreeWP_Broadcast_Purge_Children
	extends \threewp_broadcast\premium_pack\base
{
	protected $sdk_version_required = 20130418;		// wp_die()

	public function _construct()
	{
		$this->add_action( 'threewp_broadcast_broadcast_menu_tabs' );
		$this->add_filter( 'threewp_broadcast_manage_posts_custom_column' );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Add the purge links in the menu.
		@since		2014-04-18 09:04:28
	**/
	public function threewp_broadcast_broadcast_menu_tabs( $action )
	{
		$tabs = $action->tabs;		// Conv.

		if ( isset( $_GET[ 'action' ] ) )
		{
			switch( $_GET[ 'action' ] )
			{
				case 'purge':
					$tabs->tab( 'purge' )
						->callback( $this, 'tab_purge' )
						->heading_( 'Purge a child post' )
						->name_( 'Purge child post' );
					break;
				case 'purge_all':
					$tabs->tab( 'purge_all' )
						->callback( $this, 'tab_purge_all' )
						->heading_( 'Purge all child posts' )
						->name_( 'Purge child posts' );
					break;
			}
		}
	}
	/**
		@brief		Adds a "purge" action to the posts.
		@since		2014-04-18 08:49:26
	**/
	public function threewp_broadcast_manage_posts_custom_column( $filter )
	{
		// Add the single purges
		$strings = $filter->html->get( 'broadcasted_to', '' );

		if ( $strings == '' )
			return;

		foreach( $strings as $string )
		{
			$child_blog_id = $string->metadata()->get( 'child_blog_id' );
			$url = sprintf( "admin.php?page=threewp_broadcast&amp;action=purge&amp;post=%s&amp;child=%s", $filter->parent_post_id, $child_blog_id );
			$url = wp_nonce_url( $url, 'purge_' . $child_blog_id . '_' . $filter->parent_post_id );
			$string->insert_after( 'delete', 'purge', sprintf( '<span class="trash"><a href="%s" title="%s">%s</a></span>',
				$url,
				$this->_( 'Purge this child' ),
				$this->_( 'Purge' )
			) );
			$string->insert_after( 'delete', 'purge_separator', ' | ' );
		}

		$strings = $filter->html->get( 'delete_all', '' );

		if ( $strings == '' )
			return;

		$url = sprintf( "admin.php?page=threewp_broadcast&amp;action=purge_all&amp;post=%s", $filter->parent_post_id );
		$url = wp_nonce_url( $url, 'purge_all_' . $filter->parent_post_id );

		$strings->insert_after( 'delete_all', 'purge_all', sprintf( '<span class="trash"><a href="%s" title="%s">%s</a></span>',
			$url,
			$this->_( 'Purge all child posts from the child blogs' ),
			$this->_( 'Purge' )
		) );
		$strings->insert_after( 'delete_all', 'purge_all_separator', ' | ' );

		$filter->html->set( 'delete_all', $strings );
	}

	/**
		@brief		Purge a child post.
		@since		2014-04-18 12:26:59
	**/
	public function tab_purge()
	{
		$post_id = intval( $_GET[ 'post' ] );
		$child_blog_id = intval( $_GET[ 'child' ] );

		if ( $child_blog_id < 1 )
			$this->wp_die( 'Invalid blog ID: %s', $child_blog_id );

		if ( $post_id < 1 )
			$this->wp_die( 'Invalid post ID: %s', $post_id );

		$nonce = $_GET[ '_wpnonce' ];
		$real_nonce = sprintf( 'purge_%s_%s', $child_blog_id, $post_id );
		if ( ! wp_verify_nonce( $nonce, $real_nonce ) )
			die( __method__ . " security check failed." );

		$broadcast_data = $this->broadcast()->get_post_broadcast_data( get_current_blog_id(), $post_id );

		switch_to_blog( $child_blog_id );
		$broadcasted_post_id = $broadcast_data->get_linked_child_on_this_blog();

		if ( $broadcasted_post_id === null )
			$this->wp_die( 'No broadcasted child post found on this blog!' );
		$this->purge_post( $broadcasted_post_id );
		$broadcast_data->remove_linked_child( $child_blog_id );

		restore_current_blog();

		$broadcast_data = $this->broadcast()->set_post_broadcast_data( get_current_blog_id(), $post_id, $broadcast_data );

		$message = $this->_( 'The child post has been purged.' );

		echo $this->message( $message);
		echo sprintf( '<p><a href="%s">%s</a></p>',
			wp_get_referer(),
			$this->_( 'Back to post overview' )
		);
	}

	/**
		@brief		Purge a post.
		@since		2014-04-18 09:08:53
	**/
	public function tab_purge_all()
	{
		$post_id = intval( $_GET[ 'post' ] );

		if ( $post_id < 1 )
			$this->wp_die( 'Invalid post ID: %s', $post_id );

		// Check the nonce
		$nonce = $_GET[ '_wpnonce' ];
		$real_nonce = 'purge_all_' . $post_id;

		if ( ! wp_verify_nonce( $nonce, $real_nonce ) )
			$this->wp_die( __method__ . " security check failed." );

		$blog_id = get_current_blog_id();
		$broadcast_data = $this->broadcast()->get_post_broadcast_data( $blog_id, $post_id );
		foreach( $broadcast_data->get_linked_children() as $child_blog_id => $child_post_id )
		{
			switch_to_blog( $child_blog_id );
			$this->purge_post( $child_post_id );
			$broadcast_data->remove_linked_child( $child_blog_id );
			restore_current_blog();
		}

		$broadcast_data = $this->broadcast()->set_post_broadcast_data( $blog_id, $post_id, $broadcast_data );

		$message = $this->_( "All of the child posts have been purged." );

		echo $this->message( $message);
		echo sprintf( '<p><a href="%s">%s</a></p>',
			wp_get_referer(),
			$this->_( 'Back to post overview' )
		);
	}

	/**
		@brief		Purge a child off of this blog.
		@since		2014-04-18 09:24:20
	**/
	public function purge_post( $post_id )
	{
		$attachments = get_children( 'post_parent='.$post_id . '&post_type=attachment' );
		foreach( $attachments as $attachment )
			wp_delete_attachment( $attachment->ID );
		wp_delete_post( $post_id, true );
	}
}

$ThreeWP_Broadcast_Purge_Children = new ThreeWP_Broadcast_Purge_Children;
