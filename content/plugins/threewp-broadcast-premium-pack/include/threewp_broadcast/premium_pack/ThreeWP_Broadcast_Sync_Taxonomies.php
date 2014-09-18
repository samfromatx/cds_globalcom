<?php

namespace threewp_broadcast\premium_pack\sync_taxonomies;

use \plainview\sdk\collections\collection;

/**
	@brief		Synchronize the taxonomies of target blogs with those from a source blog.
	@since		2014-04-08 11:46:07
**/
class ThreeWP_Broadcast_Sync_Taxonomies
	extends \threewp_broadcast\premium_pack\base
{
	protected $sdk_version_required = 20130505;		// add_action / add_filter

	public function _construct()
	{
		$this->add_action( 'threewp_broadcast_menu' );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Admin
	// --------------------------------------------------------------------------------------------

	public function admin_menu_sync()
	{
		$form = $this->form2();
		$r = '';
		$source_blog_id = get_current_blog_id();

		$form->taxonomies = $form->select( 'taxonomies' )
			->description_( 'Select the taxonomies on this blog you with to synchronize on the target blogs.' )
			->label_( 'Taxonomies' )
			->multiple()
			->required();
		$form->taxonomies->taxonomy = new collection;

		$options = [];
		$post_types = get_post_types( [], 'objects' );
		foreach( $post_types as $post_type_name => $post_type )
		{
			$post_taxonomies = get_object_taxonomies( [ 'post_type' => $post_type_name ], 'objects' );
			foreach( $post_taxonomies as $taxonomy_name => $taxonomy )
			{
				$label = sprintf( '%s %s', $post_type->labels->singular_name, strtolower( $taxonomy->labels->singular_name ) );
				$value = sprintf( '%s_%s', $post_type->name, $taxonomy->name );
				$options[ $label ] = $value;
				$form->taxonomies->taxonomy->set( $value, [ $post_type, $taxonomy ] );
			}
		}

		// Sort the options.
		ksort( $options );

		// And put them in the select.
		array_flip( $options );
		$form->taxonomies->options( $options )
			->autosize();

		// List of blogs
		$form->blogs = $form->select( 'blogs' )
			->description_( 'Select the target blogs that will have their taxonomies synced with this blog.' )
			->label_( 'Blogs' )
			->multiple()
			->required()
			->size( 10 );

		$filter = new \threewp_broadcast\filters\get_user_writable_blogs( $this->user_id() );
		$blogs = $filter->apply()->blogs;
		foreach( $blogs as $blog )
			if ( $blog->id != $source_blog_id )
				$form->blogs->option( $blog->blogname, $blog->id );

		// Apply button
		$form->primary_button( 'apply' )
			->value_( 'Synchronize selected taxonomies' );

		if ( $form->is_posting() )
		{
			$form->post();
			$selected_taxonomies = $form->taxonomies->get_post_value();

			foreach( $selected_taxonomies as $selected_taxonomy )
			{
				$data = $form->taxonomies->taxonomy->get( $selected_taxonomy );
				$post_type = $data[ 0 ];
				$taxonomy = $data[ 1 ];
				$this->debug( 'Handling taxonomy %s for post type %s.', $taxonomy->name, $post_type->name );

				$bcd = new \threewp_broadcast\broadcasting_data;
				$bcd->add_new_taxonomies = true;
				$bcd->post = new \stdClass;
				$bcd->post->post_type = $post_type->name;
				$this->broadcast()->collect_post_type_taxonomies( $bcd );

				foreach( $form->blogs->get_post_value() as $blog_id )
				{
					$this->debug( 'Switching to %s (ID: %s)', $blogs->get( $blog_id )->blogname, $blog_id );
					switch_to_blog( $blog_id );

					$this->broadcast()->sync_terms( $bcd, $taxonomy->name );

					$this->debug( 'Switching back to %s (ID: %s)', $blogs->get( $source_blog_id )->blogname, $source_blog_id );
					restore_current_blog();
				}
			}
			if ( ! $this->broadcast()->debugging() )
				$r .= $this->message_( 'Finished synchronizing.' );
			else
				$this->debug( 'Finished synchronizing.' );
		}

		$r .= $this->p_( 'The selected taxonomies will be copied to the selected target blogs. If the taxonomies exist they will be updated if the name, description or parent differs.' );

		$action = new actions\sync_taxonomies_get_info;
		$action->apply();
		$r .= $action->text;

		$r .= $this->p_( 'Enable Broadcast debug mode to see tons of debug information.' );

		$r .= $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	public function admin_menu_tabs()
	{
		$this->load_language();

		$tabs = $this->tabs();

		$tabs->tab( 'sync' )
			->callback_this( 'admin_menu_sync' )
			->name_( 'Sync' );

		echo $tabs;
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------

	public function threewp_broadcast_menu( $action )
	{
		$role = $this->broadcast()->get_site_option( 'role_taxonomies' );

		if ( ! $this->broadcast()->role_at_least( $role ) )
			return;

		$action->broadcast->add_submenu_page(
			'threewp_broadcast',
			$this->_( 'Broadcast Sync Taxonomies' ),
			$this->_( 'Sync Taxonomies' ),
			'edit_posts',
			'threewp_broadcast_sync_taxonomies',
			[ &$this, 'admin_menu_tabs' ]
		);
	}
	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Misc
	// --------------------------------------------------------------------------------------------
}
$ThreeWP_Broadcast_Sync_Taxonomies = new ThreeWP_Broadcast_Sync_Taxonomies;
