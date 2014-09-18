<?php

namespace threewp_broadcast\premium_pack\attachment_shortcodes;

use \Exception;
use \plainview\sdk\collections\collection;
use \threewp_broadcast\attachment_data;

/**
	@brief		Broadcast attachments referred to in custom shortcodes.
	@since		2014-03-12 13:18:37
**/
class ThreeWP_Broadcast_Attachment_Shortcodes
	extends \threewp_broadcast\premium_pack\base
{
	protected $sdk_version_required = 20130505;		// add_action / add_filter

	public function _construct()
	{
		$this->add_action( 'threewp_broadcast_menu' );
		$this->add_action( 'threewp_broadcast_broadcasting_modify_post' );
		$this->add_action( 'threewp_broadcast_broadcasting_started' );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Admin
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Help text.
		@since		2014-03-14 08:51:38
	**/
	public function admin_menu_help()
	{
		echo $this->wpautop_file( $this->directory( 'html/help.html' ) );
	}

	/**
		@brief		Edit a shortcode.
		@since		2014-03-12 15:59:17
	**/
	public function admin_menu_shortcode( $id )
	{
		$shortcodes = $this->shortcodes();
		if ( ! $shortcodes->has( $id ) )
			wp_die( 'No shortcode with this ID exists!' );

		$form = $this->form2();
		$shortcode = $shortcodes->get( $id );
		$r = '';
		$r .= $this->wpautop_file( $this->directory( 'html/shortcode.html' ) );

		$form->text( 'name' )
			->description( 'The name of the shortcode.' )
			->size( 20, 128 )
			->label( 'Shortcode name' )
			->trim()
			->required()
			->value( $shortcode->shortcode );

		$form->textarea( 'value' )
			->description( 'One shortcode attribute per line that contains a single attachment ID.' )
			->label( 'Single ID attributes' )
			->rows( 5, 20 )
			->trim()
			->value( $shortcode->get_value_text() );

		$form->textarea( 'values' )
			->description( 'One shortcode attribute per line that contains mulitple attachment IDs. Delimiters are written separated by spaces after the attribute.' )
			->label( 'Multiple ID attributes' )
			->rows( 5, 20 )
			->trim()
			->value( $shortcode->get_values_text() );

		$form->markup( 'values_info' )
			->markup( 'Delimiters can be mixed within the same attribute, meaning that if you have specified commas and semicolons as delimiters, <em>ids="123,234;345"</em> will work.' );

		$form->create = $form->primary_button( 'save' )
			->value_( 'Save the shortcode data' );

		if ( $form->is_posting() )
		{
			$form->post()->use_post_values();

			try
			{
				$shortcode->set_shortcode( $form->input( 'name' )->get_filtered_post_value() );
				$shortcode->parse_value( $form->input( 'value' )->get_filtered_post_value() );
				$shortcode->parse_values( $form->input( 'values' )->get_filtered_post_value() );
				$shortcodes->save();
				$this->message_( 'The shortcode has been updated!' );
			}
			catch( Exception $e )
			{
				$this->error( 'You have errors in your settings: %s', $e->getMessage() );
			}
		}

		$r .= $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	/**
		@brief		The shortcodes tab.
		@since		2014-03-12 16:06:06
	**/
	public function admin_menu_shortcodes()
	{
		$form = $this->form2();
		$r = $this->broadcast()->html_css();

		$form->select( 'type' )
			->description( 'Choose to create an empty template or use a known shortcode.' )
			->label( 'Wizard' )
			->options( [
				'Empty shortcode' => '',
				'Example: Attribute and attributes, with delimiters' => 'example_complete',
				'Visual Composer: Gallery' => 'vc_gallery',
				'Visual Composer: Image hover' => 'vc_image_hover',
				'Visual Composer: Image with text' => 'vc_image_with_text',
				'Visual Composer: Image with text over' => 'vc_image_with_text_over',
				'Visual Composer: Single image' => 'vc_single_image',
				'Wordpress: Gallery' => 'gallery',
			] );

		$form->create = $form->primary_button( 'create' )
			->value_( 'Create a new shortcode' );

		$table = $this->table();
		$row = $table->head()->row();
		$table->bulk_actions()
			->form( $form )
			->add( $this->_( 'Delete' ), 'delete' )
			->cb( $row );
		$row->th()->text( 'Shortcode' );
		$row->th()->text( 'Example' );

		$shortcodes = $this->shortcodes();

		if ( $form->is_posting() )
		{
			$form->post();
			if ( $table->bulk_actions()->pressed() )
			{
				switch ( $table->bulk_actions()->get_action() )
				{
					case 'delete':
						$ids = $table->bulk_actions()->get_rows();
						foreach( $ids as $id )
							$shortcodes->forget( $id );
						$shortcodes->save();
						$this->message_( 'The selected shortcodes have been deleted!' );
					break;
				}
			}
			if ( $form->create->pressed() )
			{
				$shortcodes = $this->shortcodes();
				$shortcode = $shortcodes->new_shortcode( $form->input( 'type' )->get_filtered_post_value() );
				$shortcodes->append( $shortcode );
				$shortcodes->save();
				$this->message_( 'Shortcode %s has been created!', $shortcode->get_shortcode() );
			}
		}

		foreach( $shortcodes as $index => $shortcode )
		{
			$row = $table->body()->row();
			$table->bulk_actions()->cb( $row, $index );
			$url = sprintf( '<a href="%s">%s</a>', add_query_arg( [
				'tab' => 'edit',
				'id' => $index,
			] ), $shortcode->get_shortcode() );
			$row->td()->text( $url );
			$row->td()->text( $shortcode->get_info() );
		}

		$r .= $form->open_tag();
		$r .= $table;
		$r .= $this->p( 'The spaces in the example column are for legibility.' );
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	/**
		@brief		Show the admin tabs.
		@since		20131209
	**/
	public function admin_menu_tabs()
	{
		$this->load_language();

		$tabs = $this->tabs();

		$tabs->tab( 'shortcodes' )
			->callback_this( 'admin_menu_shortcodes' )
			->name_( 'Shortcodes' );

		if ( $tabs->get_is( 'edit' ) )
		{
			$tabs->tab( 'edit' )
				->callback_this( 'admin_menu_shortcode' )
				->parameters( intval( $_GET[ 'id' ] ) )
				->name_( 'Edit' );
		}

		$tabs->tab( 'help' )
			->callback_this( 'admin_menu_help' )
			->name_( 'Help' );

		$tabs->tab( 'uninstall' )
			->callback_this( 'admin_uninstall' )
			->name_( 'Uninstall' );

		echo $tabs;
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Add ourself to Broadcast's menu.
		@since		20131209
	**/
	public function threewp_broadcast_menu( $action )
	{
		// Only super admin is allowed to see AS.
		if ( ! is_super_admin() )
			return;

		$action->broadcast->add_submenu_page(
			'threewp_broadcast',
			$this->_( 'Broadcast Attachment Shortcodes' ),
			$this->_( 'Attachment Shortcodes' ),
			'edit_posts',
			'threewp_broadcast_attachment_shortcodes',
			[ &$this, 'admin_menu_tabs' ]
		);
	}

	public function threewp_broadcast_broadcasting_modify_post( $action )
	{
		// Conv
		$bcd = $action->broadcasting_data;

		// Did we find any shortcodes?
		if ( ! isset( $bcd->attachment_shortcodes ) )
			return;

		// Use the modified post data.
		$mp = $bcd->modified_post;

		foreach( $bcd->attachment_shortcodes as $find )
		{
			$shortcode = $find->original;
			// Find single IDs
			foreach( $find->value as $attribute => $id )
			{
				foreach( $bcd->copied_attachments as $copy )
				{
					if ( $copy->old->id == $id )
					{
						$old_attribute = sprintf( '%s="%s"', $attribute, $id );
						$new_attribute = sprintf( '%s="%s"', $attribute, $copy->new->ID );
						$shortcode = str_replace( $old_attribute, $new_attribute, $shortcode );
					}
				}
			}

			// Find multiple IDs
			foreach( $find->values as $attribute => $data )
			{
				$ids = $data[ 'ids' ];
				$delimiters = $data[ 'delimiters' ];

				$old_ids = $ids;
				$new_ids = $old_ids;
				foreach( $ids as $index => $id )
				{
					foreach( $bcd->copied_attachments as $copy )
					{
						if ( $copy->old->id == $id )
							$new_ids[ $index ] = $copy->new->id;
					}
				}
				$old_regexp = sprintf( '/%s="%s"/', $attribute, implode( '(.*)', $old_ids ) );
				$new_regexp = reset( $new_ids );
				array_shift( $new_ids );
				foreach( $new_ids as $index => $new_id )
					$new_regexp .= sprintf( '${%s}%s', $index+1, $new_id );
				$new_regexp = sprintf( '%s="%s"', $attribute, $new_regexp );

				$this->debug( 'Replacing old shortcode <em>%s</em> with new shortcode <em>%s</em>.', $find->original, $shortcode );
				$shortcode = preg_replace( $old_regexp, $new_regexp, $shortcode );
			}
			$mp->post_content = str_replace( $find->original, $shortcode, $mp->post_content );
		}
	}

	public function threewp_broadcast_broadcasting_started( $action )
	{
		$bcd = $action->broadcasting_data;
		$as = new \stdClass;
		$bcd->attachment_shortcodes = $as;

		$shortcodes = $this->shortcodes();

		$this->debug( 'Looking for these shortcodes: %s', implode( ', ', $shortcodes->get_shortcodes() ) );
		$matches = $this->broadcast()->find_shortcodes( $bcd->post->post_content, $shortcodes->get_shortcodes() );

		if ( count( $matches[ 0 ] ) < 1 )
			return;

		$finds = [];

		// We've found something!
		// [2] contains only the shortcode command / key. No options.
		foreach( $matches[ 2 ] as $index => $key )
		{
			// Go through all of our known shortcodes
			foreach( $shortcodes as $shortcode )
			{
				// Does the key match this shortcode?
				if ( $key !== $shortcode->shortcode )
					continue;
				$find = new \stdClass;
				$find->value = new collection;
				$find->values = new collection;

				// Complete match is in 0.
				$find->original = $matches[ 0 ][ $index ];

				$this->debug( 'Found shortcode %s as %s', $key, $find->original );

				// Extract the image ID
				foreach( $shortcode->value as $attribute => $ignore )
				{
					// Does this shortcode use this attribute?
					if ( strpos( $find->original, $attribute . '=' ) === false )
					{
						$this->debug( 'The shortcode does not contain the attribute %s.', $attribute );
						continue;
					}

					// Remove anything before the attribute
					$string = preg_replace( '/.*' . $attribute .'=\"/', '', $find->original );
					// And everything after the quotes.
					$string = preg_replace( '/\".*/', '', $string );
					$id = $string;

					// We are not interested in anything other than numbers.
					if ( ! is_numeric( $id ) )
					{
						$this->debug( 'Ignoring attribute %s because it contains a non-numerical ID: %s', $attribute, $id );
						continue;
					}

					$this->debug( 'Found attachment %s in attribute %s.', $id, $attribute );

					// Got ourselves a lovely number. Save it for later.
					$find->value->set( $attribute, $id );

					$ad = attachment_data::from_attachment_id( $id, $bcd->upload_dir );
					$bcd->attachment_data[ $id ] = $ad;

				}

				// Extract the images IDs
				foreach( $shortcode->values as $attribute => $delimiters )
				{
					// Does this shortcode use this attribute?
					if ( strpos( $find->original, $attribute . '=' ) === false )
					{
						$this->debug( 'The shortcode does not contain the attribute %s.', $attribute );
						continue;
					}

					// Remove anything before the attribute
					$string = preg_replace( '/.*' . $attribute .'=\"/', '', $find->original );
					// And everything after the quotes.
					$string = preg_replace( '/\".*/', '', $string );
					$ids = $string;

					// Convert all delimiters to commas.
					foreach( $delimiters as $delimiter )
						$ids = str_replace( $delimiter, ',', $ids );

					$this->debug( 'While looking in attribute %s, we found this: <em>%s</em>', $attribute, $ids );
					// And now explode the ids.
					$ids = explode( ',', $ids );

					// Clear the non-numerics.
					foreach( $ids as $index => $id )
						if ( ! is_numeric( $id ) )
							unset( $ids[ $index ] );

					$this->debug( 'After clearing out non-numerical IDs, there are %s IDs left.', count( $ids ) );
					if ( count( $ids ) < 1 )
						continue;

					// Got ourselves a lovely number. Save it for later.
					$find->values->set( $attribute, [
						'ids' => $ids,
						'delimiters' => $delimiters,
					] );

					$this->debug( 'Found attachments %s in attribute %s', implode( ', ', $ids ), $attribute );

					foreach( $ids as $id )
					{
						$ad = attachment_data::from_attachment_id( $id, $bcd->upload_dir );
						$bcd->attachment_data[ $id ] = $ad;
					}

				}

				$finds [] = $find;
			}
		}

		$this->debug( 'Found %s shortcode occurrences in the post.', count( $finds ) );

		if ( count( $finds ) < 1 )
			return;

		// Copy into the bcd.
		$bcd->attachment_shortcodes = $finds;
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Misc functions
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Return the shortcodes object.
		@since		2014-03-12 13:44:58
	**/
	public function shortcodes()
	{
		if ( isset( $this->_settings_shortcodes ) )
			return $this->_settings_shortcodes;
		$this->_settings_shortcodes = $this->get_site_option( 'shortcodes', null );
		if ( ! $this->_settings_shortcodes )
			$this->_settings_shortcodes = new shortcodes;
		return $this->_settings_shortcodes;
	}
}
$ThreeWP_Broadcast_Attachment_Shortcodes = new ThreeWP_Broadcast_Attachment_Shortcodes();
