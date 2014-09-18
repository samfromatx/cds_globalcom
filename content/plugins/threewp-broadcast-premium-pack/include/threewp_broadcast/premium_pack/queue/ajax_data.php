<?php

namespace threewp_broadcast\premium_pack\queue;

class ajax_data
extends \threewp_broadcast\premium_pack\ajax_data
{
	use \plainview\sdk\traits\method_chaining;

	/**
		@brief		Show debug strings?
		@since		2014-04-30 09:11:26
	**/
	public $debug = false;

	/**
		@brief		(Maybe) display a debug string.
		@since		2014-04-30 09:11:41
	**/
	public function debug( $string )
	{
		if ( ! $this->debug )
			return;
		$text = call_user_func_array( 'sprintf', func_get_args() );
		if ( $text == '' )
			$text = $string;
		$this->queue()->debug( $text );
	}

	public function finished( $finished = true )
	{
		$this->set_key( 'finished', $finished );
	}

	/**
		@brief		Return the Queue class.
		@since		2014-04-30 09:13:01
	**/
	public function queue()
	{
		return ThreeWP_Broadcast_Queue::instance();
	}

	public function wait( $wait )
	{
		$this->set_int( 'wait', $wait );
	}

}
