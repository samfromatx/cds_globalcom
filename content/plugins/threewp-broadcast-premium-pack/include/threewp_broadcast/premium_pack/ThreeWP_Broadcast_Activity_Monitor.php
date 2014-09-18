<?php

namespace threewp_broadcast\premium_pack\activity_monitor;

use \plainview\sdk\collections\collection;

/**
	@brief		Adds a Broadcast hook to the Plainview Activity Monitor, at the same time disabling post related hooks during broadcasting to prevent unnecessary logging.
	@since		2014-05-06 23:01:59
**/
class ThreeWP_Broadcast_Activity_Monitor
	extends \threewp_broadcast\premium_pack\base
{
	public function _construct()
	{
		$this->add_action( 'plainview_activity_monitor_manifest_hooks' );
		$this->add_action( 'threewp_broadcast_broadcasting_finished' );
		$this->add_action( 'threewp_broadcast_broadcasting_started' );
		$this->add_action( 'threewp_broadcast_broadcasting_before_restore_current_blog' );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Tell the Activity Monitor which hooks we supply.
		@since		2014-05-06 22:34:55
	**/
	public function plainview_activity_monitor_manifest_hooks( $action )
	{
		$class = new hooks\broadcast;
		$class->register_with( $action->hooks );
	}

	public function threewp_broadcast_broadcasting_before_restore_current_blog( $action )
	{
		$bcd = $action->broadcasting_data;
		if ( ! isset( $bcd->activity_monitor ) )
			return;

		$hook = $bcd->activity_monitor->hook;
		$temp_post = (object)$bcd->new_post;
		$temp_post->post_title = get_bloginfo( 'blogname' );
		$hook->html()->append( '%s', $hook->post_html( $temp_post ) );
	}

	public function threewp_broadcast_broadcasting_finished( $action )
	{
		$bcd = $action->broadcasting_data;
		if ( ! isset( $bcd->activity_monitor ) )
			return;

		// Execute the hook
		$this->debug( 'Executing the broadcast hook.' );
		$bcd->activity_monitor->hook->_log();

		// Re-enable all disabled hooks.
		foreach( $bcd->activity_monitor->disabled_hooks as $disabled_hook )
		{
			$this->debug( 'Re-enabling hook %s', get_class( $disabled_hook ) );
			$disabled_hook->disabled( false );
		}
	}

	public function threewp_broadcast_broadcasting_started( $action )
	{
		if ( ! $this->enabled() )
			return;

		$bcd = $action->broadcasting_data;
		$bcd->activity_monitor = new \stdClass;
		$hook = new hooks\broadcast();
		$bcd->activity_monitor->hook = $hook;

		$this->debug( 'Disabling hooks that are related to broadcasts.' );

		// Disable any hooks that are unnecessary doing broadcast (post updated and what not)
		$disabled_hooks = new collection;
		$bcd->activity_monitor->disabled_hooks = $disabled_hooks;
		$logged_hooks = $hook->activity_monitor()->logged_hooks();
		foreach( $logged_hooks as $logged_hook )
		{
			// Any hooks that have to do with pages being updated and what not should be disabled.
			if ( ! is_subclass_of( $logged_hook, 'plainview\\wordpress\\activity_monitor\\hooks\\posts' ) )
				continue;
			$this->debug( 'Disabling hook %s', get_class( $logged_hook ) );
			$disabled_hooks->append( $logged_hook );
			$logged_hook->disabled();
		}

		// Add the parent post
		$hook->html()->append( 'Broadcasting %s', $hook->post_html( $bcd->post ) );
	}
	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Misc
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Are we to use the activity monitor hooks and classes?
		@since		2014-05-06 22:40:35
	**/
	public function enabled()
	{
		return class_exists( '\\plainview\\wordpress\\activity_monitor\\Plainview_Activity_Monitor' );
	}
}
$ThreeWP_Broadcast_Activity_Monitor = new ThreeWP_Broadcast_Activity_Monitor;
