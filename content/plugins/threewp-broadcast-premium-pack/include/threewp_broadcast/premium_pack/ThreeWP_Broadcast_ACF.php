<?php

namespace threewp_broadcast\premium_pack\acf;

use \plainview\sdk\collections\collection;
use \threewp_broadcast\attachment_data;

/**
	@brief		Supports images and post object fields from Elliot Condon's <a href="http://wordpress.org/plugins/advanced-custom-fields/">Advanced Custom Field</a> plugin.
	@details

	Updates the image fields with the proper (new) attached image ID.
**/
class ThreeWP_Broadcast_ACF
	extends \threewp_broadcast\premium_pack\base
{
	protected $sdk_version_required = 20130505;		// add_action / add_filter

	public function _construct()
	{
		$this->add_action( 'threewp_broadcast_prepare_meta_box' );
		// Usually, I'd want to put these two actions in a has_acf() conditional, but this causes a problem if BC ACF is activated network wide and ACF locally = ACF doesn't exist by the time BC ACF queries for ACF existence.
		$this->add_action( 'threewp_broadcast_broadcasting_before_restore_current_blog' );
		$this->add_action( 'threewp_broadcast_broadcasting_started' );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------
	/**
		@brief		Add information to the broadcast box about the status of Broadcast ACF.
		@since		20131030
	**/
	public function threewp_broadcast_prepare_meta_box( $action )
	{
		$action->meta_box_data->html->put( 'broadcast_acf', sprintf( '<div class="broadcast_acf">%s</div><!-- broadcast_acf -->',
			$this->generate_meta_box_info()
		) );
	}

	/**
		@brief		Handle updating of the advanced custom fields image fields.
		@param		$action		Broadcast action.
		@since		20131030
	**/
	public function threewp_broadcast_broadcasting_before_restore_current_blog( $action )
	{
		if ( ! $this->has_acf() )
			return;

		$bcd = $action->broadcasting_data;

		// Was any ACF data saved at all?
		if ( ! property_exists( $bcd, 'acf' ) )
			return;

		$acf = $bcd->acf;
		$fields = get_field_objects( $bcd->new_post[ 'ID' ] );

		// Replace all of the image fields with the new attachment values.
		foreach( $acf as $item )
		{
			$this->debug( 'Handling field %s which is a %s', $item->field->name, $item->field->type );
			switch( $item->field->type )
			{
				case 'file':
				case 'image':
					$old_id = $this->extract_id( $item->field->value );
					$this->debug( 'Handling old image ID %s', $old_id );
					foreach( $bcd->copied_attachments as $copied_attachment )
					{
						if ( $copied_attachment->old->post->ID == $old_id )
						{
							$new_id = $copied_attachment->new->ID;
							// Replace the ID of the attachment with the new ID
							$this->debug( 'Replacing old ID %s from %s with new attachment ID %s', $old_id, $item->field->name, $new_id );
							update_post_meta( $bcd->new_post()->ID, $item->field->name, $new_id );
						}
					}
				break;
				case 'gallery':
					$new_meta = [];
					foreach( $item->original_ids as $original_id )
						foreach( $bcd->copied_attachments as $copied_attachment )
							if ( $copied_attachment->old->post->ID == $original_id )
								$new_meta[] = $copied_attachment->new->ID;
					// Replace the IDs of the posts with the new IDs
					$this->debug( 'The new gallery is: %s', $new_meta );
					update_post_meta( $bcd->new_post()->ID, $item->field->name, $new_meta );
				break;
				case 'relationship':
					$new_meta = [];
					$this->debug( 'Handling relationship %s', $item->field->name );
					foreach( $item->original_posts as $old_id => $old_post )
					{
						$post_bcd = new \threewp_broadcast\broadcasting_data;
						$post_bcd->custom_fields = true;
						$post_bcd->taxonomies = true;
						$post_bcd->link = true;
						$post_bcd->parent_blog_id = $bcd->parent_blog_id;
						$post_bcd->parent_post_id = $old_id;
						$post_bcd->post = $old_post;
						$post_bcd->upload_dir = $bcd->upload_dir;

						// Broadcast only to this blog.
						$blog = new \threewp_broadcast\broadcast_data\blog;
						$blog->id = $bcd->current_child_blog_id;
						$this->debug( 'Broadcasting old post %s', $old_id );
						$post_bcd->broadcast_to( $blog );

						// The GUID must be nulled.
						unset( $post_bcd->post->guid );
						$this->broadcast()->broadcast_post( $post_bcd );

						$new_post_id = $post_bcd->new_post()->ID;
						$this->debug( 'Old post %s broadcasted as child %s', $old_id, $new_post_id );

						$new_meta[] = $new_post_id;
					}
					$this->debug( 'The new relationship is: %s', $new_meta );
					// Replace the ID of the post with the new ID
					update_post_meta( $bcd->new_post()->ID, $item->field->name, $new_meta );
				break;
				case 'post_object':
					$old_id = $this->extract_id( $item->field->value );

					$this->debug( 'Handling old post object ID %s', $old_id );
					$post_bcd = new \threewp_broadcast\broadcasting_data;
					$post_bcd->custom_fields = true;
					$post_bcd->taxonomies = true;
					$post_bcd->link = true;
					$post_bcd->parent_blog_id = $bcd->parent_blog_id;
					$post_bcd->parent_post_id = $old_id;
					$post_bcd->post = $item->post;
					$post_bcd->upload_dir = $bcd->upload_dir;

					// Broadcast only to this blog.
					$blog = new \threewp_broadcast\broadcast_data\blog;
					$blog->id = $bcd->current_child_blog_id;
					$this->debug( 'Broadcasting old post %s', $old_id );
					$post_bcd->broadcast_to( $blog );

					// The GUID must be nulled.
					unset( $post_bcd->post->guid );
					$this->broadcast()->broadcast_post( $post_bcd );

					$new_post_id = $post_bcd->new_post[ 'ID' ];
					$this->debug( 'Post broadcasted as child %s', $new_post_id );

					// Replace the ID of the post with the new ID
					update_post_meta( $bcd->new_post()->ID, $item->field->name, $new_post_id );
				break;
			}
		}
	}

	/**
		@brief		Save info about the broadcast.
		@param		Broadcast_Data		The BCD object.
		@since		20131030
	**/
	public function threewp_broadcast_broadcasting_started( $action )
	{
		if ( ! $this->has_acf() )
			return;

		$bcd = $action->broadcasting_data;
		$fields = get_field_objects( $bcd->post->ID );

		if ( ! is_array( $fields ) )
			return;

		$acf = new acf\collection;

		$o = new \stdClass;
		$o->acf = $acf;
		$o->bcd = $bcd;

		$this->debug( 'The ACF fields are <pre>%s</pre>', var_export( $fields, true ) );

		foreach( $fields as $field )
		{
			$this->debug( 'Parsing field: %s', $field[ 'label' ] );
			// Convenience.
			$o->field = (object)$field;
			$this->parse_field( $o );
		}

		// Any fields to be saved?
		if ( count( $acf ) < 1 )
			return;

		// Save the fields into the bcd object.
		$bcd->acf = $acf;
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Misc functions
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Add a field automatically.
		@since		2014-03-27 20:42:09
	**/
	public function add_field( $o )
	{
		$field = $o->field;

		$this->debug( 'Attempting to add a %s field', $field->type );
		switch( $field->type )
		{
			case 'file':
			case 'image':
				$this->add_image_field( $o );
			break;
			case 'gallery':
				$this->add_gallery_field( $o );
			break;
			case 'relationship':
				$this->add_post_relationship_field( $o );
			break;
			case 'post_object':
				$this->add_post_obj_field( $o );
			break;
		}
	}

	/**
		@brief		Adds a gallery.
		@since		2014-06-06 14:30:44
	**/
	public function add_gallery_field( $o )
	{
		$field = $o->field;

		$item = new acf\item;
		$item->field = $field;
		$item->original_ids = [];

		foreach( $field->value as $value )
		{
			$id = $this->extract_id( $value );
			if ( ! $id )
				continue;
			$item->original_ids[] = $id;

			// If the image is not already being broadcasted, then add it
			if ( ! isset( $o->bcd->attachment_data[ $id ] ) )
			{
				$this->debug( 'Adding attachment data for the image %s.', $id );
				$ad = attachment_data::from_attachment_id( $id, $o->bcd->upload_dir );
				$o->bcd->attachment_data[ $id ] = $ad;
			}
			else
				$this->debug( 'Attachment %s is already noted.', $id );
		}

		if ( count( $item->original_ids ) < 1 )
			return;

		$o->acf->append( $item );
	}

	/**
		@brief		Adds this image field to the ACF object.
		@since		2014-01-22 11:57:22
	**/
	public function add_image_field( $o )
	{
		$field = $o->field;

		// Convenience
		$id = $this->extract_id( $field->value );

		// No image selected.
		if ( ! $id )
		{
			$this->debug( 'No image selected.' );
			return;
		}

		$item = new acf\item;
		$item->field = $field;
		$item->original_id = $id;

		// If the image is not already being broadcasted, then add it
		if ( ! isset( $o->bcd->attachment_data[ $id ] ) )
		{
			$this->debug( 'Adding attachment data for the image %s.', $id );
			$ad = attachment_data::from_attachment_id( $id, $o->bcd->upload_dir );
			$o->bcd->attachment_data[ $id ] = $ad;
		}
		else
			$this->debug( 'Attachment %s is already noted.', $id );

		$o->acf->append( $item );
	}

	/**
		@brief		Adds this post field to the ACF object.
		@since		2014-03-25 18:40:00
	**/
	public function add_post_relationship_field( $o )
	{
		$field = $o->field;

		$posts = [];
		foreach( $field->value as $value )
		{
			$id = $this->extract_id( $value );
			$this->debug( 'Post relationship ID is %s.', $id );
			if ( ! $id )
				return;
			$posts[ $id ] = get_post( $id );
		}

		$item = new acf\item;
		$item->field = $field;
		$item->original_posts = $posts;
		$item->broadcast_data = $this->broadcast()->get_post_broadcast_data( get_current_blog_id(), $id );
		$this->debug( 'Adding extra posts %s.', $ids );
		$o->acf->append( $item );
	}

	/**
		@brief		Adds this post field to the ACF object.
		@since		2014-03-25 18:40:00
	**/
	public function add_post_obj_field( $o )
	{
		$field = $o->field;

		// Convenience
		$id = $this->extract_id( $field->value );

		$this->debug( 'Post object ID is %s.', $id );

		if ( ! $id )
			return;

		$item = new acf\item;
		$item->field = $field;
		$item->original_id = $id;
		$item->post = get_post( $id );
		$item->broadcast_data = $this->broadcast()->get_post_broadcast_data( get_current_blog_id(), $id );
		$this->debug( 'Adding extra post %s.', $id );
		$o->acf->append( $item );
	}

	/**
		@brief		Extracts an ID out of this object / array / whatever.
		@since		2014-03-27 21:37:06
	**/
	public function extract_id( $id )
	{
		// Convert objects to arrays.
		if ( is_object( $id ) )
			$id = (array) $id;
		// And extract the ID from the array.
		if ( is_array( $id ) )
		{
			foreach( [ 'ID', 'id' ] as $key )
				if ( isset( $id[ $key ] ) )
				{
					$id = $id[ $key ];
					break;
				}
			if ( is_array( $id ) )
				return false;
		}

		// Last chance: if this is not an integer...
		$id = intval( $id );
		if ( $id < 1 )
			return false;
		// But it is!
		return $id;
	}

	/**
		@brief		Output some data about WPML support.
		@return		string		HTML string containing Broadcast WPML info.
		@since		20131030
	**/
	public function generate_meta_box_info( $o = [] )
	{
		$this->load_language();

		$acf_text = sprintf( '%sAdvanced Custom Fields%s', '<a href="http://wordpress.org/plugins/advanced-custom-fields/">', '</a>' );

		if ( ! $this->has_acf() )
			return $this->_( '%s was not detected.', $acf_text );

		global $acf;

		$r = [];

		$r []= $this->_( '%s v%s detected.',
			$acf_text,
			self::open_close_tag( $acf->settings[ 'version' ], 'em' )
		);

		return \plainview\sdk\base::implode_html( $r, '<div>', '</div>' );
	}

	/**
		@brief		Check for the existence of WPML.
		@return		bool		True if WPML is alive and kicking. Else false.
		@since		20131030
	**/
	public function has_acf()
	{
		global $acf;
		return $acf !== null;
	}

	/**
		@brief		Parse an ACF field, deciding what to do with it.
		@since		2014-01-22 11:52:15
	**/
	public function parse_field( $o )
	{
		$this->debug( 'Field is of type: %s', $o->field->type );
		switch( $o->field->type )
		{
			case 'flexible_content':
				$image_fields = [];
				foreach( $o->field->layouts as $layout_id => $layout )
				{
					$this->debug( 'Search layout ID: ', $layout_id );
					if ( ! isset( $layout[ 'sub_fields' ] ) )
						continue;
					$this->debug( 'Layout contains %s sub fields.', count( $layout[ 'sub_fields' ] ) );
					foreach( $layout[ 'sub_fields' ] as $sub_field )
					{
						$name = $sub_field[ 'name' ];

						// Make a temp field.
						$tempfield = (object)$sub_field;
						$tempfield->name = sprintf( '%s_%s_%s', $o->field->name, $layout_id, $name );
						$tempfield->value = $o->field->value[ $layout_id ][ $name ];
						$tempfield->flexible_content = true;
						$o2 = clone( $o );
						$o2->field = $tempfield;
						$this->add_field( $o2 );
					}
				}
				break;
			case 'file':
			case 'gallery':
			case 'image':
			case 'post_object':
			case 'relationship':
				$this->add_field( $o );
				break;
			case 'repeater':
				$repeater_fields = [];
				foreach( $o->field->sub_fields as $index => $sub_field )
				{
					$sub_field = (object)$sub_field;
					switch( $sub_field->type )
					{
						case 'file':
						case 'image':
						case 'post_object':
							$this->debug( 'Repeater field has a known type: %s', $sub_field->type );
							$repeater_fields [ $sub_field->name ] = $sub_field;
						break;
						default:
							$this->debug( 'Repeater field has an unknown type: %s', $sub_field->type );
							break;
					}
				}

				foreach( $repeater_fields as $name => $field )
				{
					$this->debug( 'Attempting to find a value for field %s', $name );
					// Find all of the subfield values
					foreach( $o->field->value as $index => $values )
					{
						foreach( $values as $value_key => $value_value )
						{
							// We are looking for all values that have this field name.
							if ( $value_key != $name )
							{
								$this->debug( 'Ignoring subvalue key %s', $value_key );
								continue;
							}

							// Make a temp field.
							$tempfield = clone( $field );
							$tempfield->name = sprintf( '%s_%s_%s', $o->field->name, $index, $name );
							$tempfield->value = $value_value;
							$tempfield->repeater = true;
							// We can't use $o because its already used as the method parameter.
							$o2 = clone( $o );
							$o2->field = $tempfield;
							$this->debug( 'We want to add the field with the repeated name %s', $tempfield->name );
							$this->add_field( $o2 );
						}
					}
				}
				break;
		}
	}
}
$ThreeWP_Broadcast_ACF = new ThreeWP_Broadcast_ACF;
