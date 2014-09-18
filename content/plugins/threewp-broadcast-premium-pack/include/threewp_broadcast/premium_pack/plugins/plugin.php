<?php

namespace threewp_broadcast\premium_pack\plugins;

/**
	@brief		A plugin.
	@since		2014-04-05 08:42:25
**/
class plugin
	extends \plainview\sdk\collections\collection
{
	/**
		@brief		The filename of the plugin.
		@since		2014-04-05 20:16:59
	**/
	public $filename;

	/**
		@brief		The complete file path.
		@since		2014-04-05 20:57:12
	**/
	public $path;

	/**
		@brief		The name of the plugin.
		@since		2014-04-05 20:24:12
	**/
	public $plugin_name;

	/**
		@brief		Constructor
		@since		2014-04-05 08:43:21
	**/
	public function __construct( $path )
	{
		$this->path = $path;
		$this->filename = basename( $path );
		$this->plugin_name = $this->get_name();
	}

	/**
		@brief		Activate the plugin.
		@since		2014-04-05 21:05:12
	**/
	public function activate()
	{
		$this->plugin()->activate_internal();
	}

	/**
		@brief		Deactivate the plugin.
		@since		2014-04-05 21:05:12
	**/
	public function deactivate()
	{
		$this->plugin()->deactivate_internal();
	}

	/**
		@brief		Return the plugin description.
		@since		2014-04-05 20:44:58
	**/
	public function get_brief_description()
	{
		return $this->get_comment()->brief;
	}

	/**
		@brief		Return the content of the plugin.
		@since		2014-04-06 11:01:06
	**/
	public function get_comment()
	{
		if ( isset ( $this->_comment ) )
			return $this->_comment;

		$text = $this->get_file_contents();
		$comments = array_filter( token_get_all( $text ), function( $entry )
		{
			return $entry[0] == T_DOC_COMMENT;
		});
		$comment = array_shift( $comments );
		$comment = $comment[ 1 ];

		$current_key = '';
		$lines = explode( "\n", $comment );
		$r = [];

		// Parse the comment into its various headings.
		foreach( $lines as $line )
		{
			$line = trim( $line );
			if ( $line == '/**' )
				continue;
			if ( $line == '**/' )
				continue;

			if ( ( strlen( $line ) > 0 ) && ( $line[ 0 ] == '@' ) )
			{
				$current_key = preg_replace( '/@([a-zA-Z0-9]*).*/', '\1', $line );
				$text = preg_replace( '/@[a-zA-Z0-9]*[\t]*+/', '', $line );
				if ( $text == '' )
					continue;
			}
			else
				$text = $line;

			if ( ! isset( $r[ $current_key ] ) )
			{
				if ( $text != '' )
					$r[ $current_key ] = $text;
			}
			else
				$r[ $current_key ] .= "\n" . $text;
		}

		$this->_comment = (object)$r;
		return $this->_comment;
	}

	/**
		@brief		Loads the file contents from disk.
		@since		2014-04-06 10:59:46
	**/
	public function get_file_contents()
	{
		if ( ! isset( $this->_file_contents ) )
			$this->_file_contents = file_get_contents( $this->path );
		return $this->_file_contents;
	}

	/**
		@brief		Returns the plugin filename.
		@since		2014-04-05 08:48:15
	**/
	public function get_filename()
	{
		return $this->filename;
	}

	/**
		@brief		Return an unique ID for this plugin. 8 characters.
		@since		2014-04-05 20:42:50
	**/
	public function get_id()
	{
		$id = md5( $this->filename );
		$id = substr( $id, 0, 8 );
		return $id;
	}

	/**
		@brief		Returns the name of the plugin (built using the filename).
		@since		2014-04-05 20:23:33
	**/
	public function get_name()
	{
		$name = $this->filename;
		$name = str_replace( 'ThreeWP_Broadcast_', '', $name );
		$name = str_replace( '.php', '', $name );
		$name = str_replace( '_', ' ', $name );
		return $name;
	}

	/**
		@brief		Return the complete path to the plugin.
		@since		2014-04-05 21:04:12
	**/
	public function get_path()
	{
		return $this->path;
	}

	/**
		@brief		Return an array with the necessary data needed to quickly load the plugin.
		@since		2014-04-05 21:35:07
	**/
	public function get_quickload_array()
	{
		return [
			'filename' => $this->filename,
			'namespace' => $this->_namespace,
			'path' => $this->path,
		];
	}

	/**
		@brief		Is this plugin loaded?
		@since		2014-04-06 11:16:20
	**/
	public function is_loaded()
	{
		return isset( $this->_plugin );
	}

	/**
		@brief		Load the plugin from disk.
		@since		2014-04-05 21:03:49
	**/
	public function load()
	{
		require_once( $this->get_path() );

		if ( ! isset( $this->_namespace ) )
		{
			$data = file_get_contents( $this->get_path() );
			$ns = preg_replace( '/.*namespace/s', '\1', $data );
			$ns = preg_replace( '/;.*/s', '', $ns );
			$ns = trim( $ns );
			$this->_namespace = $ns;
		}

		return $this;
	}

	/**
		@brief		Create and load the plugin using the quickload array.
		@since		2014-04-05 21:36:49
	**/
	public static function quickload( $array )
	{
		if ( ! file_exists( $array[ 'path' ] ) )
			return;
		$plugin = new plugin( $array[ 'path' ] );
		$plugin->namespace = $array[ 'namespace' ];
		$plugin->plugin();
	}

	/**
		@brief		Return the plugin instance, once loaded.
		@since		2014-04-05 21:07:15
	**/
	public function plugin()
	{
		if ( ! isset( $this->_plugin ) )
		{
			require_once( $this->get_path() );

			$data = $this->get_file_contents();
			$ns = preg_replace( '/.*namespace/s', '\1', $data );
			$ns = preg_replace( '/;.*/s', '', $ns );
			$ns = trim( $ns );
			$this->_namespace = $ns;

			$name = $this->get_filename();
			$name = str_replace( '.php', '', $name );
			$name = sprintf( '%s\\%s', $this->_namespace, $name );
			$this->_plugin = $name::instance();

			\threewp_broadcast\premium_pack\ThreeWP_Broadcast_Premium_Pack::instance()->plugins()->set( $this->get_filename(), $this );
		}

		return $this->_plugin;
	}

	/**
		@brief		Uninstall the plugin.
		@since		2014-04-05 21:05:12
	**/
	public function uninstall()
	{
		$this->plugin()->uninstall_internal();
	}
}
