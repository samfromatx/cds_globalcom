<?php
/*
Author:			edward_plainview
Author Email:	edward@plainview.se
Author URI:		http://www.plainview.se
Description:	Expand ThreeWP Broadcast with extra functionality from plugins.
Plugin Name:	ThreeWP Broadcast Premium Pack
Plugin URI:		http://plainview.se/wordpress/threewp-broadcast-premium-pack/
Version:		4
*/

namespace threewp_broadcast\premium_pack;

if ( ! class_exists( '\\threewp_broadcast\\premium_pack\\base' ) )	require_once( __DIR__ . '/base.php' );

class ThreeWP_Broadcast_Premium_Pack
extends \threewp_broadcast\premium_pack\base
{
	public $plugin_version = 4;

	protected $sdk_version_required = 20130505;		// add_action / add_filter

	protected $site_options = [
		'enabled_plugins' => [],
	];

	public function _construct()
	{
		$this->add_action( 'threewp_broadcast_menu' );
		$this->load_enabled_plugins();
	}

	public function activate()
	{
		$this->load_enabled_plugins();
		foreach( $this->plugins() as $plugin )
			if ( $plugin->is_loaded() )
				$plugin->activate();
	}

	public function deactivate()
	{
		foreach( $this->plugins() as $plugin )
			if ( $plugin->is_loaded() )
				$plugin->deactivate();
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Admin menu
	// --------------------------------------------------------------------------------------------
	/**
		@brief		Settings menu.
		@since		2014-04-05 00:55:12
	**/
	public function admin_menu_plugins()
	{
		$plugins = $this->plugins();
		$form = $this->form2();
		$r = '';

		$table = $this->table()->css_class( 'plugins' );
		$row = $table->head()->row();
		$table->bulk_actions()
			->form( $form )
			->add( $this->_( 'Activate plugin' ), 'enable_plugin' )
			->add( $this->_( 'Deactivate plugin' ), 'disable_plugin' )
			->add( $this->_( 'Uninstall plugin' ), 'uninstall_plugin' )
			->cb( $row );
		$row->th()->text( 'Plugin' );
		$row->th()->text( 'Description' );

		if ( $form->is_posting() )
		{
			if ( $table->bulk_actions()->pressed() )
			{
				$ids = $table->bulk_actions()->get_rows();
				$enabled_plugins = $this->get_site_option( 'enabled_plugins', [] );
				switch ( $table->bulk_actions()->get_action() )
				{
					case 'enable_plugin':
						foreach( $plugins->from_ids( $ids ) as $plugin )
						{
							$plugin->activate();
							$enabled_plugins[ $plugin->get_filename() ] = $plugin->get_quickload_array();
						}
						$r .= $this->message_( 'The selected plugins have been activated.' );
						break;
					case 'disable_plugin':
						foreach( $plugins->from_ids( $ids ) as $plugin )
						{
							$plugin->deactivate();
							unset( $enabled_plugins[ $plugin->get_filename() ] );
						}
						$r .= $this->message_( 'The selected plugins have been deactivated.' );
						break;
					case 'uninstall_plugin':
						foreach( $plugins->from_ids( $ids ) as $plugin )
						{
							$plugin->uninstall();
							unset( $enabled_plugins[ $plugin->get_filename() ] );
						}
						$r .= $this->message_( 'The selected plugins have been uninstalled from the database and deactivated.' );
						break;
				}
				$this->update_site_option( 'enabled_plugins', $enabled_plugins );
			}
		}

		$enabled_plugins = $this->get_site_option( 'enabled_plugins', [] );

		foreach( $plugins as $plugin )
		{
			$row = $table->body()->row();
			$table->bulk_actions()->cb( $row, $plugin->get_id() );

			$td = $row->td();
			$td->text( $plugin->get_name() );
			$td->css_class( 'plugin-title' );

			if ( isset( $enabled_plugins[ $plugin->get_filename() ] ) )
				$row->css_class( 'active' );
			else
				$row->css_class( 'inactive' );

			$text = $plugin->get_brief_description();
			$row->td()->text( $text );
		}

		$r .= $form->open_tag();
		$r .= $table;
		$r .= $form->close_tag();

		$r .= $this->p_( 'Premium pack version: %s', $this->plugin_version );

		$r .= $this->p_( 'The author can be contacted at: <a href="mailto:broadcast@plainview.se">broadcast@plainview.se</a>' );

		echo $r;
	}

	public function admin_menu_tabs()
	{
		$this->load_language();

		$tabs = $this->tabs();
		$tabs->tab( 'plugins' )		->callback_this( 'admin_menu_plugins' )		->name_( 'Plugins' );
		$tabs->tab( 'uninstall' )		->callback_this( 'admin_uninstall' )	->name_( 'Uninstall' );

		echo $tabs;
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Hide the premium pack info.
		@since		20131030
	**/
	public function threewp_broadcast_menu( $action )
	{
		if ( ! is_admin() )
			return;
		$this->remove_premium_pack_info_menu();
		$this->broadcast()->add_submenu_page(
			'threewp_broadcast',
			$this->_( 'Premium Pack' ),
			$this->_( 'Premium Pack' ),
			'edit_posts',
			'bc_pp',
			[ &$this, 'admin_menu_tabs' ]
		);
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Misc functions
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Loads all of the enabled plugins.
		@since		2014-04-06 07:19:26
	**/
	public function load_enabled_plugins()
	{
		$enabled_plugins = $this->get_site_option( 'enabled_plugins', [] );
		foreach( $enabled_plugins as $plugin )
			plugins\plugin::quickload( $plugin );
	}

	/**
		@brief		Return the plugins object.
		@since		2014-04-05 08:33:35
	**/
	public function plugins()
	{
		if ( isset( $this->_plugins ) )
			return $this->_plugins;
		$this->_plugins = new plugins\plugins( $this );
		return $this->_plugins;
	}
}

$ThreeWP_Broadcast_Premium_Pack = new ThreeWP_Broadcast_Premium_Pack;
