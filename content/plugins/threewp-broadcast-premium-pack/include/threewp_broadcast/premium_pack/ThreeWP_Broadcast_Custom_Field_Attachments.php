<?php

namespace threewp_broadcast\premium_pack\custom_field_attachments;

use \plainview\sdk\collections\collection;
use \threewp_broadcast\attachment_data;

/**
	@brief		Allow post custom field containing attachment IDs to be broadcasted correctly.
	@since		2014-04-06 23:19:04
**/
class ThreeWP_Broadcast_Custom_Field_Attachments
	extends \threewp_broadcast\premium_pack\base
{
	protected $sdk_version_required = 20130505;		// add_action / add_filter

	protected $site_options = array(
		'id_fields' => [],					// Array of custom fields that are expected to contain an attachment ID.
	);

	public function _construct()
	{
		$this->add_action( 'threewp_broadcast_menu' );
		$this->add_action( 'threewp_broadcast_broadcasting_before_restore_current_blog' );
		$this->add_action( 'threewp_broadcast_broadcasting_started' );
		$this->add_action( 'threewp_broadcast_prepare_meta_box' );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Admin
	// --------------------------------------------------------------------------------------------

	public function admin_menu_settings()
	{
		$form = $this->form2();
		$form->id( 'custom_field_attachments' );

		$id_fields = $form->textarea( 'id_fields' )
			->description( 'A list of custom field names that contain one or more attachment IDs separated by characters that are not numbers (commas, spaces, etc).' )
			->label_( 'ID fields' )
			->rows( 10, 20 )
			->trim()
			->value( implode( "\n", $this->get_site_option( 'id_fields', [] ) ) );

		$save = $form->primary_button( 'save' )
			->value_( 'Save settings' );

		if ( $form->is_posting() )
		{
			$form->post();
			$form->use_post_values();

			foreach( [ 'id_fields' ] as $key )
			{
				$value = $$key->get_filtered_post_value();
				$values = $this->parse_textarea_lines( $value );
				foreach( $values as $index => $value )
					$values[ $index ] = trim( $value );
				$this->update_site_option( $key, $values );
			}

			$this->message( 'Options saved!' );
		}

		$r = $this->p_( "Some post custom fields can contain attachment IDs that normally aren't updated when broadcasting to child blogs." );

		$r .= $this->p_( "Enter the names of the fields in the text box to tell Broadcast that the attachments need to be broadcasted and their new child-IDs set into the named custom fields. Specify wildcards with an asterisk." );

		$r .= $this->p_( "To see the names of the custom fields, enable Broadcast debug mode and look at the Broadcast meta box in the post editor of an existing post." );

		$r .= $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		$r .= $this->p_( "Some examples:" );
		$r .= $this->p_( "<code>article_image<br/>gallery_image_*<br/>set_*_image_*</code>" );

		echo $r;
	}

	public function admin_menu_tabs()
	{
		$this->load_language();

		$tabs = $this->tabs();

		$tabs->tab( 'settings' )
			->callback_this( 'admin_menu_settings' )
			->name_( 'Settings' );

		$tabs->tab( 'uninstall' )
			->callback_this( 'admin_uninstall' )
			->name_( 'Uninstall' );

		echo $tabs;
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Put in the new attachment IDs.
		@since		2014-04-06 15:54:36
	**/
	public function threewp_broadcast_broadcasting_before_restore_current_blog( $action )
	{
		$bcd = $action->broadcasting_data;

		if ( ! isset( $bcd->custom_field_attachments ) )
			return;

		$custom_field_attachments = $bcd->custom_field_attachments;

		foreach( $custom_field_attachments->ids as $key => $old_ids )
		{
			$old_meta_value = get_post_meta( $bcd->new_post[ 'ID' ], $key );
			$old_meta_value = reset( $old_meta_value );
			$new_meta_value = $old_meta_value;
			foreach( $old_ids as $old_id )
			{
				foreach( $bcd->copied_attachments as $copied_attachment )
				{
					if ( $copied_attachment->old->post->ID == $old_id )
					{
						$new_id = $copied_attachment->new->ID;
						// Replace the ID of the attachment with the new ID
						$new_meta_value = preg_replace( '/' . $old_id . '/', $new_id, $new_meta_value, 1 );
					}
				}
			}
			$this->debug( 'Replacing %s from %s with %s', $old_meta_value, $key, $new_meta_value );
			update_post_meta( $bcd->new_post[ 'ID' ], $key, $new_meta_value );
		}
	}

	/**
		@brief		Maybe store our info.
		@since		2014-04-06 15:46:04
	**/
	public function threewp_broadcast_broadcasting_started( $action )
	{
		$bcd = $action->broadcasting_data;
		$custom_field_attachments = new collection;
		$custom_field_attachments->ids = new collection;
		$custom_field_attachments->count = 0;
		$id_fields = $this->get_site_option( 'id_fields' );
		$meta = get_post_meta( $bcd->post->ID );

		$this->debug( 'Going through %s keys.', count( $meta ) );

		foreach( $meta as $key => $value )
		{
			$this->debug( 'Is %s an ID field?', $key );
			if ( $this->key_matches_field( $key, $id_fields ) )
			{
				$value = reset( $value );
				$ids = preg_split( '/[^0-9]/', $value );
				$custom_field_attachments->ids->set( $key, $ids );
				foreach( $ids as $id )
				{
					$this->debug( 'Yes. Saving attachment from %s: %s', $key, $id );
					if ( ! isset( $o->bcd->attachment_data[ $id ] ) )
					{
						$this->debug( 'Adding attachment data for the image %s.', $id );
						$custom_field_attachments->count++;
						$ad = attachment_data::from_attachment_id( $id, $bcd->upload_dir );
						$bcd->attachment_data[ $id ] = $ad;
					}
				}
			}
			else
			{
				$this->debug( 'No.' );
			}
		}

		$this->debug( 'Saved %s attachments.', $custom_field_attachments->count );

		if ( $custom_field_attachments->count < 1 )
			return;

		$bcd->custom_field_attachments = $custom_field_attachments;
	}

	/**
		@brief		Hide the premium pack info.
		@since		20131030
	**/
	public function threewp_broadcast_menu( $action )
	{
		$action->broadcast->add_submenu_page(
			'threewp_broadcast',
			$this->_( 'Broadcast Custom Field Attachments' ),
			$this->_( 'Custom Field Attachments' ),
			'edit_posts',
			'threewp_broadcast_custom_field_attachments',
			[ &$this, 'admin_menu_tabs' ]
		);
	}

	/**
		@brief		Add debug information to the meta box.
		@since		2014-04-06 15:01:34
	**/
	public function threewp_broadcast_prepare_meta_box( $action )
	{
		if ( ! $this->broadcast()->debugging() )
			return;

		$mbd = $action->meta_box_data;

		$r = '<h4>Custom Field Attachments</h4>';

		// Get a list of all of the post's custom fields.
		$meta = get_post_meta( $mbd->post->ID );
		// And all of the fields we are handling.
		$id_fields = $this->get_site_option( 'id_fields' );

		if ( count( $meta ) < 1 )
		{
			$r .= $this->broadcast()->p_( 'This post has no custom fields.' );
		}
		else
		{
			$r .= $this->broadcast()->p_( 'The custom fields in bold should specify attachment IDs:' );
			$r .= '<ul>';
			foreach( $meta as $key => $value )
			{
				$div = new \plainview\sdk\html\div;
				$div->tag = 'li';

				if ( $this->key_matches_field( $key, $id_fields ) )
					$div->css_style( 'font-weight: bold;' );

				$div->content = $key;
				$r .= $div;
			}
			$r .= '</ul>';
		}


		$mbd->html->set( 'custom_field_attachments', $r );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Misc
	// --------------------------------------------------------------------------------------------
	/**
		@brief		Find the key in the field name array.
		@since		2014-04-06 15:25:32
	**/
	public function key_matches_field( $key, $field_names )
	{
		foreach( $field_names as $field_name )
		{
			// No wildcard = straight match
			if ( strpos( $field_name, '*' ) === false )
			{
				if ( $field_name == $key )
					return true;
			}
			else
			{
				$preg = str_replace( '*', '.*', $field_name );
				$preg = sprintf( '/%s/', $preg );
				$result = preg_replace( $preg, '', $key );
				if ( $result !== $key )
					return true;
			}
		}
		return false;
	}

	/**
		@brief		Parses a textarea into an array of unique lines.
		@since		2014-04-19 23:55:38
	**/
	public function parse_textarea_lines( $text )
	{
		$lines = array_filter( explode( "\n", $text ) );
		$lines = array_flip( $lines );
		$lines = array_flip( $lines );
		return $lines;
	}
}
$ThreeWP_Broadcast_Custom_Field_Attachments = new ThreeWP_Broadcast_Custom_Field_Attachments;
