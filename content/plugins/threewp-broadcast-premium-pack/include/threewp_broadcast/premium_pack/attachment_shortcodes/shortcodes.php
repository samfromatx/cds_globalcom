<?php

namespace threewp_broadcast\premium_pack\attachment_shortcodes;

use \plainview\sdk\collections\collection;

class shortcodes
	extends \plainview\sdk\collections\collection
{

	/**
		@brief		Return an array of all shortcode names.
		@since		2014-03-13 21:07:04
	**/
	public function get_shortcodes()
	{
		$names = [];
		foreach( $this->items as $item )
			$names[ $item->get_shortcode() ] = $item->get_shortcode();
		return $names;
	}

	/**
		@brief		Create a new shortcode and return it.
		@since		2014-03-12 14:27:56
	**/
	public function new_shortcode( $type = null )
	{
		$sc = new shortcode;
		$sc->set_shortcode( 'shortcode_' . rand( 1000, 9999 ) );

		switch( $type )
		{
			case 'example_complete':
				$sc->set_shortcode( 'example1' );
				$sc->add_value( 'id' );
				$sc->add_value( 'image' );
				$sc->add_value( 'pic' );
				$sc->add_values( 'ids' );
				$sc->add_values( 'images', [ ':', ';', ',' ] );
			break;
			case 'gallery':
				$sc->set_shortcode( $type );
				$sc->add_values( 'ids' );
			break;
			case 'vc_gallery':
				$sc->set_shortcode( $type );
				$sc->add_values( 'images' );
			break;
			case 'vc_image_hover':
				$sc->set_shortcode( 'image_hover' );
				$sc->add_value( 'image' );
				$sc->add_value( 'hover_image' );
			break;
			case 'vc_image_with_text':
				$sc->set_shortcode( 'image_with_text' );
				$sc->add_value( 'image' );
			break;
			case 'vc_image_with_text_over':
				$sc->set_shortcode( 'image_with_text_over' );
				$sc->add_value( 'image' );
			break;
			case 'vc_single_image':
				$sc->set_shortcode( $type );
				$sc->add_value( 'image' );
			break;
		}
		return $sc;
	}

	/**
		@brief		Saves the shortcodes to the Wordpress database.
		@since		2014-03-12 14:30:39
	**/
	public function save()
	{
		$this->parent()->update_site_option( 'shortcodes', $this );
	}

	/**
		@brief		Returns the attachment shortcodes class.
		@since		2014-03-12 14:37:40
	**/
	public function parent()
	{
		return ThreeWP_Broadcast_Attachment_Shortcodes::instance();
	}
}
