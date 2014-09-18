<?php

namespace threewp_broadcast\premium_pack\plugins;

/**
	@brief		Container class for plugins
	@since		2014-04-05 08:42:25
**/
class plugins
	extends \plainview\sdk\collections\collection
{
	/**
		@brief		Constructor.
		@since		2014-04-05 08:48:56
	**/
	public function __construct( $pp )
	{
		$this->pp = $pp;
		$this->find_files();
	}

	/**
		@brief		Find all of the plugin files.
		@since		2014-04-05 20:20:19
	**/
	public function find_files()
	{
		$this->flush();
		$files = glob( __DIR__ . '/../ThreeWP_Broadcast_*php' );
		foreach( $files as $filename )
		{
			$filename = str_replace( 'plugins/../', '', $filename );
			$plugin = new plugin( $filename );
			$this->set( $plugin->get_filename(), $plugin );
		}
	}

	/**
		@brief		Return an array of plugins that are derived from the array of IDs.
		@since		2014-04-05 21:01:02
	**/
	public function from_ids( $ids )
	{
		$ids = array_flip( $ids );
		$r = [];
		foreach( $this->items as $plugin )
		{
			if ( isset( $ids[ $plugin->get_id() ] ) )
				$r[] = $plugin;
		}
		return $r;
	}
}
