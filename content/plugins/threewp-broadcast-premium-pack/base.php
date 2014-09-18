<?php

namespace threewp_broadcast\premium_pack;

require_once( 'vendor/autoload.php' );

class Base
	extends \plainview\sdk\wordpress\base
{
	/**
		@brief		Return the instance of Broadcast.
		@return		ThreeWP_Broadcast		The instance of ThreeWP Broadcast.
		@since		20131004
	**/
	public function broadcast()
	{
		return \threewp_broadcast\ThreeWP_Broadcast::instance();
	}

	/**
		@brief		Send debug info to Broadcast.
		@since		2014-02-24 00:47:19
	**/
	public function debug( $string )
	{
		$bc = $this->broadcast();
		$args = func_get_args();
		// Get the name of the class
		$class_name = get_called_class();
		// But without the namespace
		$class_name = preg_replace( '/.*\\\/', '', $class_name );
		// And append it at the beginning of the string.
		$args[ 0 ] =  $class_name . ': ' . $args[ 0 ];
		return call_user_func_array( [ $bc, 'debug' ] , $args );
	}

	/**
		@brief		Return the directory of the plugin's include directory.
		@since		20131209
	**/
	public function directory( $extra = false )
	{
		$ns = get_called_class();
		$ns = preg_replace( '/(.*)\\\\.*/', '\1', $ns );
		$ns = str_replace( '\\', '/', $ns );
		$r = __DIR__ . '/include/' . $ns;
		if ( $extra )
			$r .= '/' . $extra;
		return $r;
	}

	/**
		@brief		Loads and paragraphs a file.
		@since		20131207
	**/
	public function wpautop_file( $filepath )
	{
		$r = file_get_contents( $filepath );
		$r = wpautop( $r );
		return $r;
	}

	public function remove_premium_pack_info_menu()
	{
		$this->broadcast()->submenu_pages->forget( 'threewp_broadcast_premium_pack_info' );
	}
}
