<?php

namespace threewp_broadcast\premium_pack\views;

/**
	@brief		Broadcast the correct View Template from the <a href="http://wp-types.com/">onTheGo Systems' TOOLSET plugin</a>.
	@since		20131007
**/
class ThreeWP_Broadcast_Views
	extends \threewp_broadcast\premium_pack\base
{
	const VIEWS_TEMPLATE_CUSTOM_FIELD = '_views_template';

	protected $sdk_version_required = 20130505;		// add_action / add_filter

	public function _construct()
	{
		$this->add_action( 'threewp_broadcast_prepare_meta_box' );
		$this->add_action( 'threewp_broadcast_broadcasting_after_switch_to_blog' );
		$this->add_action( 'threewp_broadcast_broadcasting_started' );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Add information to the broadcast box about the status of Broadcast WPML.
		@since		20131007
	**/
	public function threewp_broadcast_prepare_meta_box( $action )
	{
		// Is Views installed_
		if ( $this->views_installed() )
			$text = sprintf( 'Views v%s detected.', WPV_VERSION );
		else
			$text = sprintf( 'Views was not detected.' );

		$action->meta_box_data->html->put( 'broadcast_views', $text );
	}

	/**
		@brief		Set the correct view
		@since		20131007
	**/
	public function threewp_broadcast_broadcasting_after_switch_to_blog( $action )
	{
		if ( ! $this->views_installed() )
			return;

		$bcd = $action->broadcasting_data;

		// Any views data?
		if ( ! isset( $bcd->views ) )
			return;

		// Conv
		$views = $bcd->views;
		// Conv
		$post = $views->post;

		// Find the post on this blog with the view template name.
		$args = array(
			'name' => $post->post_name,
			'numberposts' => 1,
			'post_type'=> $post->post_type,
		);
		$template = get_posts( $args );

		// Was the template with the exact same name found on this blog?
		if ( count( $template ) != 1 )
		{
			// There is no equivalent template. Remove the custom field.
			unset( $bcd->post_custom_fields[ self::VIEWS_TEMPLATE_CUSTOM_FIELD ] );
			return;
		}

		// We want the first (and only) result.
		$template = reset( $template );

		// Update the ID of the view on this blog.
		$bcd->post_custom_fields[ self::VIEWS_TEMPLATE_CUSTOM_FIELD ] = [ $template->ID ];
	}

	/**
		@brief		Save info about the View.
		@since		20131007
	**/
	public function threewp_broadcast_broadcasting_started( $action )
	{
		if ( ! $this->views_installed() )
			return;

		$bcd = $action->broadcasting_data;

		// Is there a _views_template custom field?
		if ( ! isset( $bcd->post_custom_fields[ self::VIEWS_TEMPLATE_CUSTOM_FIELD ] ) )
			return;

		$view_id = $bcd->post_custom_fields[ self::VIEWS_TEMPLATE_CUSTOM_FIELD ];
		$view_id = reset( $view_id );

		// Create the views data in the broadcasting data
		$bcd->views = new \stdClass;

		// Conv
		$views = $bcd->views;

		// Save the template (post) data
		$views->post = get_post( $view_id );
	}


	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Misc functions
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Is Views installed?
		@since		20131007
	**/
	public function views_installed()
	{
		return defined( 'WPV_VERSION' );
	}
}
$ThreeWP_Broadcast_Views = new ThreeWP_Broadcast_Views();
