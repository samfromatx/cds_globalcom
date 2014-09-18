<?php

namespace threewp_broadcast\premium_pack\userblogsettings;

use \Exception;
use \plainview\sdk\collections\collection;
use \threewp_broadcast\premium_pack\userblogsettings\db\criterion;
use \threewp_broadcast\premium_pack\userblogsettings\db\modification;

/**
	@brief		Hide the broadcast meta box and/or menu, modify the meta box to force/prevent broadcast to blogs, with separate settings for users / blogs / roles.
	@since		20131014
**/
class ThreeWP_Broadcast_UserBlogSettings
	extends \threewp_broadcast\premium_pack\base
{
	protected $sdk_version_required = 20131109;		// form2 select toString div

	protected $site_options = [
		'database_version' => 0,					// Version of database and settings
	];

	public function _construct()
	{
		define( 'THREEWP_BROADCAST_USERBLOGSETTINGS_DIR', __DIR__ . '/userblogsettings' );
		$this->add_action( 'threewp_broadcast_admin_menu' );
		$this->add_action( 'threewp_broadcast_menu' );
		$this->add_filter( 'threewp_broadcast_prepare_meta_box', 50 );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Activate / Deactivate
	// --------------------------------------------------------------------------------------------

	public function activate()
	{
		$db_ver = $this->get_site_option( 'database_version', 0 );

		if ( $db_ver < 1 )
		{
			$db_ver = 1;

			$query = sprintf( "CREATE TABLE IF NOT EXISTS `%s` (
				`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Row ID',
				`data` longtext NOT NULL COMMENT 'Serialized data',
				PRIMARY KEY (`id`)
				)  DEFAULT CHARSET=latin1"
				, modification::db_table()
			);
			$this->query( $query );

			$query = sprintf( "CREATE TABLE IF NOT EXISTS `%s` (
				`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Row ID',
				`blog_id` int(11) DEFAULT NULL COMMENT 'Blog ID',
				`modification_id` int(11) NOT NULL,
				`role_id` int(11) DEFAULT NULL,
				`user_id` int(11) DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `modification_id` (`modification_id`)
				) DEFAULT CHARSET=latin1"
				, criterion::db_table()
			);
			$this->query( $query );
		}

		$this->update_site_option( 'database_version', $db_ver );
	}

	public function uninstall()
	{
		$query = sprintf( "DROP TABLE `%s`", modification::db_table() );
		$this->query( $query );
		$query = sprintf( "DROP TABLE `%s`", criterion::db_table() );
		$this->query( $query );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Admin
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Edit a modification.
		@since		20131016
	**/
	public function admin_menu_edit_modification( $modification )
	{
		$form = $this->form2();
		// Make a quick lookup property so that we don't have to parse all of the form inputs looking for modifiations.
		$form->modifications = new collection;
		$meta_box_form = $this->form2();
		$post = $this->fake_a_post();
		$r = '';

		$bc = \threewp_broadcast\ThreeWP_Broadcast::instance();
		$meta_box_data = $bc->create_meta_box( $post );
		$meta_box_data->form = $meta_box_form;
		// Fake an empty modification
		$meta_box_data->ubs_modifications = new collections\modification( [ new db\modification ] );

		// Allow all modules to modify the box
		$action = new \threewp_broadcast\actions\prepare_meta_box;
		$action->meta_box_data = $meta_box_data;
		$action->apply();

		$fs = $form->fieldset( 'fs_general' )
			->label_( 'General settings' );

		$name_input = $fs->text( 'name' )
			->description_( 'The name of the modification that only the super admin sees.' )
			->label_ ( 'Modification name' )
			->required()
			->size( 40, 128 )
			->value( $form::unfilter_text( $modification->get_data( 'name', 'Name' ) ) );

		$fs = $form->fieldset( 'fs_display_settings' )
			->label_( 'Display settings' );

		$display_broadcast_columns = $fs->checkbox( 'display_broadcast_columns' )
			->checked( $modification->get_data( 'display_broadcast_columns', true ) )
			->description_( 'Display the broadcast columns in the post overview showing link information.' )
			->label_ ( 'Display broadcast columns' );

		$display_broadcast_menu = $fs->checkbox( 'display_broadcast_menu' )
			->checked( $modification->get_data( 'display_broadcast_menu', true ) )
			->description_( 'Display the broadcast menu in the post overview showing link information.' )
			->label_ ( 'Display broadcast menu' );

		$display_broadcast_meta_box = $fs->checkbox( 'display_broadcast_meta_box' )
			->checked( $modification->get_data( 'display_broadcast_meta_box', true ) )
			->description_( 'Display the broadcast meta box in the post overview showing link information.' )
			->label_ ( 'Display broadcast meta box' );

		$fs = $form->fieldset( 'fs_modifications' )
			->label_( 'Meta box settings' );

		$fs->markup( 'info_modifications' )
			->p_( 'Select the modifcations for each setting in the meta box.' );

		$checkbox_options = [
			$this->_( 'Leave it alone' ) => '',
			$this->_( 'Leave it alone and hide it' ) => '_hide_',
			$this->_( 'On' ) => '_on_',
			$this->_( 'Force on' ) => '_on_readonly_',
			$this->_( 'Force on and hide' ) => '_on_hide_',
			$this->_( 'Off' ) => '_off_',
			$this->_( 'Force off' ) => '_off_readonly_',
			$this->_( 'Force off and hide' ) => '_off_hide_',
		];

		// And now show the modifiable data
		foreach( $meta_box_form->inputs as $input )
		{
			$input_class = get_class( $input );
			$modification_name = modification::input_id( $input );
			switch( $input_class )
			{
				case 'plainview\\sdk\\form2\\inputs\\checkbox':
					$select = $fs->select( $modification_name )
						->options( $checkbox_options )
						->value( $modification->data->modifications->get( $modification_name, '' ) );
					$select->get_label()->content = $form::unfilter_text( $input->get_label()->content );
					$form->modifications->append( $modification_name );
				break;
				case 'plainview\\sdk\\form2\\inputs\\checkboxes':
					$old_fs = $fs;

					$fs = $form->fieldset( 'fs_modifications_' . $input->make_name() )
						->label_( 'Meta box settings: %s', $input->get_label()->content );

					$handling_blogs = ( $input->get_name() == 'blogs' );

					foreach( $input->inputs as $checkbox_name => $checkbox_input )
					{
						$modification_name = modification::input_id( $checkbox_input );
						$select = $fs->select( $modification_name )
							->options( $checkbox_options )
							->value( $modification->data->modifications->get( $modification_name, '' ) );
						$select->get_label()->content = $form::unfilter_text( $checkbox_input->get_label()->content );

						// If we're handling the blogs fieldset and we're modifying the current blog
						if ( $handling_blogs )
						{
							$cb_blog_id = $checkbox_input->get_name();
							$cb_blog_id = preg_replace( '/.*_/', '', $cb_blog_id );
							if ( $cb_blog_id == get_current_blog_id() )
								$select->description( 'Note: you are currently editing the settings from this blog and it will therefore not be shown in the preview below.' );
						}

						$form->modifications->append( $modification_name );
					}

					$fs = $old_fs;
				break;
				case 'plainview\\sdk\\form2\\inputs\\select':
					$select_ubs_setting = $modification_name . '_ubs_setting';
					$select_input = $fs->select( $select_ubs_setting )
						->label( 'With the select input below' )
						->options([
							'Do nothing' => '',
							'Use the value below' => '_on_',
							'Force the value below' => '_on_readonly_',
							'Use the value below and hide the input' => '_on_hide_',
						])
						->value( $modification->data->modifications->get( $select_ubs_setting, '' ) );
					$mod = $fs->select( $modification_name );
					$mod->label = clone( $input->label );
					$mod->options = $input->options;
					$mod->value( $modification->data->modifications->get( $modification_name, '' ) );
					$form->modifications->append( $modification_name );
					$form->modifications->append( $select_ubs_setting );
				break;
				default:
					$fs->markup( 'info_' . $input->make_name() )
						->p_( 'No modifications available for the %s input.', '<em>' . $input->get_label() . '</em>' );
				break;
			}
		}

		$save_button = $form->primary_button( 'save' )
			->value_( 'Save the settings' );

		// Handle the posting of the form
		if ( $form->is_posting() )
		{
			$form->post();
			if ( $save_button->pressed() )
			{
				$form->use_post_values();

				$modification->data->name = $name_input->get_value();

				// Save the display options.
				$modification->data->display_broadcast_columns = $display_broadcast_columns->is_checked();
				$modification->data->display_broadcast_menu = $display_broadcast_menu->is_checked();
				$modification->data->display_broadcast_meta_box = $display_broadcast_meta_box->is_checked();

				// Save the modification values.
				$modification->data->modifications->flush();
				foreach( $form->modifications as $input_modification_name )
				{
					$input_modification = $form->input( $input_modification_name );
					$modification->data->modifications->put( $input_modification_name, $input_modification->get_post_value() );
				}

				$modification->db_update();

				$this->message_( 'The modification settings have been saved.' );
			}
		}

		// Display the edit form
		$r .= $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		$post = $this->fake_a_post();

		// Display a preview.

		$r .= $this->h3( $this->_( 'Preview' ) );

		// Get the meta box.
		$meta_box_data = new \threewp_broadcast\meta_box\data;
		$meta_box_data->blog_id = get_current_blog_id();
		$meta_box_data->broadcast_data = new \threewp_broadcast\BroadcastData;
		$meta_box_data->form = $this->form2();
		$meta_box_data->post = $post;
		$meta_box_data->post_id = $post->ID;
		$meta_box_data->ubs_modifications = new collections\modification( [ $modification ] );

		$action = new \threewp_broadcast\actions\prepare_meta_box;
		$action->meta_box_data = $meta_box_data;
		$action->apply();

		if ( ! $modification->data->display_broadcast_meta_box )
			$r .= $this->_( 'The broadcast meta box is not shown to the user. But if it were visible, it would look like this:' );
		else
			$r .= $this->_( 'The broadcast meta box is visible and looks like this:' );

		$r .= '<div id="threewp_broadcast" class="postbox clear" style="max-width: 40em;"><div class="inside">';
		$r .= $meta_box_data->html;
		$r .= '</div></div>';

		foreach( $meta_box_data->js as $key => $value )
			wp_enqueue_script( $key, $value );
		foreach( $meta_box_data->css as $index => $css )
			wp_enqueue_style( $index, $css  );

		echo $r;
	}

	/**
		@brief		Edit the criteria for a modification.
		@since		20131016
	**/
	public function admin_menu_edit_modfication_criteria( $modification )
	{
		$form = $this->form2();
		$table = $this->table();
		$r = '';

		$fs = $form->fieldset( 'fs_update' )
			->label_( 'Update existing criteria' );

		$button_update = $fs->primary_button( 'update_criteria' )
			->value_( 'Update above criteria' );

		$fs = $form->fieldset( 'fs_create' )
			->label_( 'Create a new modification criterion' );

		$button_create = $fs->secondary_button( 'create_criteria' )
			->value_( 'Create new criterion' );

		$table->bulk_actions()
			->form( $form )
			->add( $this->_( 'Delete' ), 'delete' );

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
						{
							$criterion = criterion::db_load( $id );
							if ( ! $criterion )
								continue;
							$criterion->db_delete();
							$this->message_( 'Criteria %s has been deleted.',
								$criterion->id
							);
						}
					break;
				}
			}
			if ( $button_update->pressed() )
			{
				$this->message_( 'The criteria have been saved.' );
			}

			if ( $button_create->pressed() )
			{
				$criterion = new criterion;
				$criterion->modification_id = $modification->id;
				$criterion->db_update();
				$this->message_( 'A new modification criterion, %s, has been created.',
					'<em>' . $criterion->id . '</em>'
				);
			}
		}

		// Find all the criteria
		$criteria = $this->get_modification_criteria( $modification );

		$row = $table->head()->row();
		$table->bulk_actions()->cb( $row );
		$row->th()->text_( 'Blog' );
		$row->th()->text_( 'Role' );
		$row->th()->text_( 'User' );

		// Collect the blogs
		$blog_options = [];
		foreach( $this->cached_blogs() as $blog )
		{
			$blogname = $form::unfilter_text( $blog->blogname );
			$blog_options[ $blogname ] = $blog->id;
		}

		// Collect the roles
		$role_options = $this->roles_as_ids();

		// Collect the users
		$user_options = $this->cached_users();

		$temp_form = clone( $form );
		foreach( $criteria as $c )
		{
			$modified = false;
			$row = $table->body()->row();
			$table->bulk_actions()->cb( $row, $c->id );

			foreach( [
				[
					'key' => 'b',
					'label' => $this->_( 'Blog' ),
					'options' => $blog_options,
					'value_key' => 'blog_id',
				],
				[
					'key' => 'r',
					'label' => $this->_( 'Role' ),
					'options' => $role_options,
					'value_key' => 'role_id',
				],
				[
					'key' => 'u',
					'label' => $this->_( 'User' ),
					'options' => $user_options,
					'value_key' => 'user_id',
				],
			] as $type )
			{
				$type = (object) $type;
				$input = $temp_form->select( $type->key . $c->id )
					->label( $type->label )
					->option_( 'Any', '' )
					->options( $type->options )
					->prefix( 'criteria', $c->id, $type->key )
					->value( $c->{ $type->value_key } );
				if ( $button_update->pressed() )
				{
					$new_value = $input->get_post_value();
					$input->use_post_value();
					if ( $c->{ $type->value_key } != $new_value )
					{
						$c->{ $type->value_key } = $new_value;
						$modified = true;
					}
				}
				$row->td()->text( $this->hide_input_label( $input ) );
			}

			if ( $modified )
				$c->db_update();
		}

		$r .= $form->open_tag();
		$r .= $table;

		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	/**
		@brief		Show the help text.
		@since		20131016
	**/
	public function admin_menu_help()
	{
		echo $this->broadcast()->html_css();
		$contents = file_get_contents( THREEWP_BROADCAST_USERBLOGSETTINGS_DIR . '/html/help.html' );
		$contents = wpautop( $contents );
		echo $contents;
	}

	/**
		@brief		Show the modifications tab.
		@since		20131014
	**/
	public function admin_menu_modifications()
	{
		$r = '';

		$form = $this->form2();
		$table = $this->table();

		$button_create_modification = $form->primary_button( 'create_modification' )
			->value_( 'Create modification' );

		$table->bulk_actions()
			->form( $form )
			->add( $this->_( 'Delete' ), 'delete' );

		if ( $form->is_posting() )
		{
			if ( $table->bulk_actions()->pressed() )
			{
				switch ( $table->bulk_actions()->get_action() )
				{
					case 'delete':
						$ids = $table->bulk_actions()->get_rows();
						foreach( $ids as $id )
						{
							$modification = modification::db_load( $id );
							if ( ! $modification )
								continue;
							$this->delete_modification( $modification );
							$this->message_( 'Modification, %s, has been deleted.',
								'<em>' . $modification->data->name . '</em>'
							);
						}
					break;
				}
			}
			if ( $button_create_modification->pressed() )
			{
				$modification = new db\modification;
				$modification->data->name = $this->_( 'Modification created %s', $this->now() );
				$modification->db_update();
				$criterion = new criterion;
				$criterion->modification_id = $modification->id;
				$criterion->db_update();
				$this->message_( 'A new modification, %s, has been created.',
					'<em>' . $modification->data->name . '</em>'
				);
			}
		}

		$r .= $this->p_( 'This table lists all the available modifications and a short summary of which settings are altered.' );

		// Find all the modifications
		$modifications = $this->get_modifications();

		$row = $table->head()->row();
		$table->bulk_actions()->cb( $row );
		$row->th()->text_( 'Modification' );
		$row->th()->text_( 'Modifications' );
		$row->th()->text_( 'Applies to' );

		foreach( $modifications as $modification )
		{
			$row = $table->body()->row();

			$table->bulk_actions()->cb( $row, $modification->id );

			$edit_url = add_query_arg( [
				'id' => $modification->id,
				'tab' => 'edit_modification',
			] );
			$criteria_url = add_query_arg( [
				'id' => $modification->id,
				'tab' => 'edit_modification_criteria',
			] );
			$text = sprintf( '<a href="%s">%s</a>', $edit_url, $modification->data->name );
			$text .= sprintf( '<div class="row-actions">
				<a href="%s" title="%s">%s</a>
				| <a href="%s" title="%s">%s</a>
				</div>
			',
				$edit_url,
				$this->_( 'Edit the modification' ),
				$this->_( 'Edit the modification' ),
				$criteria_url,
				$this->_( 'Edit the criteria used for this modification' ),
				$this->_( 'Edit the criteria' )
			);

			$row->td()->text( $text );

			// Count the modifications.
			$text = [];
			$count = $modification->count_modifications();
			if ( $count == 1 )
				$text []= $this->_( '1 meta box settings' );
			else
				if ( $count != 0 )
					$text []= $this->_( '%s meta box settings', $count );
			$count = $modification->count_display_modifications();
			if ( $count == 1 )
				$text []= $this->_( '1 display setting' );
			else
				if ( $count != 0 )
					$text []= $this->_( '%s display settings', $count );

			$row->td()->text( implode( '<br/>', $text ) );

			// Show all of the criteria associated with this mod.
			$criteria = $this->get_modification_criteria( $modification );
			$text = [];
			foreach( $criteria as $criterion )
				$text []= $criterion;
			$row->td()->text( implode( '<br/>', $text ) );

		}

		$r .= $form->open_tag();
		$r .= $table;

		// Create a new modification
		$r .= $this->h3( $this->_( 'Create a new modification' ) );
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	/**
		@brief		Show the settings tab.
		@since		20131014
	**/
	public function admin_menu_settings()
	{
		$form = $this->form2();

		$fs = $form->fieldset( 'general' )
			->label_( 'General' );

		$input_enabled = $fs->checkbox( 'enabled' )
			->checked( $this->get_site_option( 'enabled' ) )
			->description_( 'Modify the Broadcast settings for users?' )
			->label_( 'Enabled' );

		$save = $form->primary_button( 'save' )
			->value_( 'Save settings' );

		if ( $form->is_posting() )
		{
			$form->post();
			$form->use_post_values();

			$this->update_site_option( 'enabled', $input_enabled->is_checked() );

			$this->message( 'Options saved!' );
		}

		$r = $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	/**
		@brief		Show the admin tabs.
		@since		20131014
	**/
	public function admin_menu_tabs()
	{
		$this->load_language();

		$tabs = $this->tabs();

		$tabs->tab( 'modifications' )
			->callback_this( 'admin_menu_modifications' )
			->name_( 'Modifications' );

		if ( $tabs->get_is( 'edit_modification' ) )
		{
			$id = intval( $_GET[ 'id' ] );
			$modification = modification::db_load( $id );
			if ( ! $modification )
				wp_die( 'Modification does not exist.' );

			$tabs->tab( 'edit_modification' )
				->callback_this( 'admin_menu_edit_modification' )
				->heading_( 'Editing modification %s', '<em>' . $modification->data->name . '</em>' )
				->parameters( $modification )
				->name_( 'Edit modification' );
		}

		if ( $tabs->get_is( 'edit_modification_criteria' ) )
		{
			$id = intval( $_GET[ 'id' ] );
			$modification = modification::db_load( $id );
			if ( ! $modification )
				wp_die( 'Modification does not exist.' );

			$tabs->tab( 'edit_modification_criteria' )
				->callback_this( 'admin_menu_edit_modfication_criteria' )
				->heading_( 'Editing modification criteria %s', '<em>' . $modification->data->name . '</em>' )
				->name_( 'Edit modification criteria' )
				->parameters( $modification );
		}

		$tabs->tab( 'test' )
			->callback_this( 'admin_menu_test' )
			->heading_( 'Test modifications' )
			->name_( 'Test' );

		$tabs->tab( 'help' )
			->callback_this( 'admin_menu_help' )
			->name_( 'Help' );

		$tabs->tab( 'uninstall' )
			->callback_this( 'admin_uninstall' )
			->name_( 'Uninstall' );

		echo $tabs;
	}

	public function admin_menu_test()
	{
		$form = $this->form2();
		$r = $this->p_( 'Use this form to find out which modifcations affect different combinations of blogs / roles / users.' );

		// Collect the blogs
		$blog_options = [];
		foreach( $this->cached_blogs() as $blog )
		{
			$blogname = $form::unfilter_text( $blog->blogname );
			$blog_options[ $blogname ] = $blog->id;
		}

		// Collect the roles
		$role_options = $this->roles_as_ids();

		// Collect the users
		$user_options = $this->cached_users();

		$blog_id_input = $form->select( 'blog_id' )
			->label_( 'Assume we are on blog' )
			->option_( 'None in particular', 0 )
			->options( $blog_options );

		$role_id_input = $form->select( 'role_id' )
			->label_( 'Assume the user has the role' )
			->option_( 'None in particular', 0 )
			->options( $role_options );

		$user_id_input = $form->select( 'user_id' )
			->label_( 'Assume this user' )
			->option_( 'None in particular', 0 )
			->options( $user_options );

		$form->primary_button( 'test' )
			->value_( 'Test modifications' );

		if ( $form->is_posting() )
		{
			$form->post();

			$blog_id = $blog_id_input->get_post_value();
			$role_id = $role_id_input->get_post_value();
			$user_id = $user_id_input->get_post_value();

			$modifications = $this->find_modifications([
				'blog_id' => $blog_id,
				'role_id' => $role_id,
				'user_id' => $user_id,
			]);

			if ( count( $modifications ) < 1 )
			{
				$this->message_ ( 'No modifications affect this blog / role / user combination.' );
			}
			else
			{
				$names = [];
				foreach( $modifications as $modification )
					$names []= $modification->data->name;
				$names = '<ul>' . $this->implode_html( $names ) . '</ul>';
				echo $this->broadcast()->html_css();
				$this->message_ ( 'The following modifications affect the combination you have chosen: %s', $names );

			}
		}

		$r .= $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Hide Broadcast?
		@since		20131015
	**/
	public function threewp_broadcast_admin_menu( $action )
	{
		$modifications = $this->cached_user_modifications();

		if ( ! $modifications->display_broadcast_columns() )
			$this->broadcast()->display_broadcast_columns = false;

		if ( ! $modifications->display_broadcast_menu() && ! is_super_admin() )
			$this->broadcast()->display_broadcast_menu = false;

		if ( ! $modifications->display_broadcast_meta_box() )
			$this->broadcast()->display_broadcast_meta_box = false;
	}

	/**
		@brief		Add ourself to Broadcast's menu.
		@since		20131014
	**/
	public function threewp_broadcast_menu( $action )
	{
		// Only super admin is allowed to see UBS.
		if ( ! is_super_admin() )
			return;

		$action->broadcast->add_submenu_page(
			'threewp_broadcast',
			$this->_( 'Broadcast User & Blog Settings' ),
			$this->_( 'User & Blog Settings' ),
			'edit_posts',
			'threewp_broadcast_userblogsettings',
			[ &$this, 'admin_menu_tabs' ]
		);
	}

	/**
		@brief		Modify the meta box with modification data.
		@since		20131016
	**/
	public function threewp_broadcast_prepare_meta_box( $action )
	{
		// If there isn't a modification set, try to find one.
		if ( ! isset( $action->meta_box_data->ubs_modifications ) )
		{
			$action->meta_box_data->ubs_modifications = $this->cached_user_modifications();
		}

		// No modification found? Don't do anything.
		if ( ! isset( $action->meta_box_data->ubs_modifications ) )
			return;

		// Modifications found. Make them apply themselves to the meta box.
		foreach( $action->meta_box_data->ubs_modifications as $modification )
			$modification->modify_meta_box( $action->meta_box_data );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Misc functions
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Return a cached list of blogs.
		@since		20131016
	**/
	public function cached_blogs()
	{
		if ( ! isset( $this->_cached_blogs ) )
		{
			$filter = new \threewp_broadcast\filters\get_user_writable_blogs( $this->user_id() );
			$this->_cached_blogs = $filter->apply()->blogs;
		}
		return $this->_cached_blogs;
	}

	/**
		@brief		Return a list of modifications applicable to the current user.
		@since		20131016
	**/
	public function cached_user_modifications()
	{
		if ( ! isset( $this->_cached_user_modifications ) )
		{
			$this->_cached_user_modifications = new collections\modification;
			try
			{
				// Get the user's role ID
				$user_role = $this->get_user_role();
				$role_options = $this->roles_as_options();
				$role_ids = $this->roles_as_ids();

				// We have the role, convert it to a string.
				$user_role = $role_options[ $user_role ];
				// And to an ID
				$role_id = $role_ids[ $user_role ];

				$this->_cached_user_modifications = $this->find_modifications([
					'blog_id' => get_current_blog_id(),
					'role_id' => $role_id,
					'user_id' => $this->user_id(),
				]);
			}
			catch ( Exception $e )
			{
			}
		}
		return $this->_cached_user_modifications;
	}

	/**
		@brief		Return a cached list of users.
		@since		20131016
	**/
	public function cached_users()
	{
		if ( ! isset( $this->_cached_users ) )
		{
			$users = get_users();
			$this->_cached_users = [];
			foreach( $users as $user )
				$this->_cached_users[ $user->data->user_login ] = $user->data->ID;
		}
		return $this->_cached_users;
	}

	/**
		@brief		Create a fake post.
		@since		20131016
	**/
	public function fake_a_post()
	{
		$object = new \stdClass;
		return new \WP_Post( $object );
		// Get the first best post to use as an example.
		$posts = get_posts([
			'posts_per_page' => 1,
		]);

		if ( count( $posts ) < 1 )
			throw new Exception( $this->_( 'Please create at least one public post to be used as a post template.' ) );
		return reset( $posts );
	}

	/**
		@brief		Display an input label + input, hiding the label.
		@since		20131016
	**/
	public function hide_input_label( $input )
	{
		return sprintf( '<div class="screen-reader-text">%s</div>%s',
			$input->display_label(),
			$input->display_input()
		);
	}

	/**
		@brief		Find the modifications that match these search terms.
		@details

		The search terms are a combination of: blog ID, role ID and user ID.

		@return		collections\modification		A collection of modifications.
		@since		20131016
	**/
	public function find_modifications( $o )
	{
		$o = $this->merge_objects( [
			'blog_id' => 0,
			'role_id' => 0,
			'user_id' => 0,
		], $o );

		$where = [];

		if ( $o->blog_id > 0 )
			$where [] = sprintf("`blog_id` = '%s'", $o->blog_id );
		if ( $o->role_id > 0 )
			$where [] = sprintf("`role_id` = '%s'", $o->role_id );
		if ( $o->user_id > 0 )
			$where [] = sprintf("`user_id` = '%s'", $o->user_id );

		$query = sprintf( "SELECT * FROM `%s` WHERE %s", criterion::db_table(),
			implode( ' OR ', $where )
		);
		$criteria = $this->query( $query );
		$criteria = criterion::sqls( $criteria );

		$modifications = new collections\modification;

		$priorities = [
			[ 'user', 'blog' ],
			[ 'user', 'role' ],
			[ 'user' ],
			[ 'role', 'blog' ],
			[ 'blog' ],
			[ 'role' ],
		];

		foreach( $priorities as $priority )
		{
			if ( count( $modifications ) > 0 )
				break;

			foreach( $criteria as $c )
			{
				// Build the parameter for match.
				$o2 = new \stdClass;
				foreach( $priority as $column )
				{
					$column_name = $column . '_id';
					$o2->$column_name = $o->$column_name;
				}

				// Does this c match?
				if ( $c->matches( $o2 ) )
				{
					$modification_id = $c->modification_id;
					// Put only the ID in there, so that we can later load all of the modifications at the same time.
					if ( ! $modifications->has( $modification_id ) )
						$modifications->put( $modification_id, null );
				}
			}

		}

		// Are there any criteria that match 0 0 0?
		$query = sprintf( "SELECT * FROM `%s` WHERE `blog_id` = '0' AND `role_id` = '0' AND `user_id` = '0'", criterion::db_table() );
		$criteria = $this->query( $query );
		$criteria = criterion::sqls( $criteria );
		foreach( $criteria as $c )
			$modifications->put( $c->modification_id, null );

		// Load all of the mods at once, so save on DB queries.
		if ( count( $modifications ) > 0 )
		{
			$ids = array_keys( $modifications->toArray() );
			$query = sprintf( "SELECT * FROM `%s` WHERE `id` IN ('%s')", modification::db_table(),
				implode( "','", $ids )
			);
			$results = $this->query( $query );
			$results = modification::sqls( $results );
			foreach( $results as $result )
				$modifications->put( $result->id, $result );
		}

		return $modifications;
	}

	/**
		@brief		Get the roles as an array of [ role => id ].
		@since		2014-04-13 20:05:14
	**/
	public function roles_as_ids()
	{
		// 2014-04-13 - roles() has been removed, so we have to replicate behavior using standard role handling.
		// Behavior = [ role => id ]
		$role_options = [];
		foreach( array_reverse( $this->roles_as_options() ) as $role_option )
			$role_options[ count( $role_options ) + 1 ] = $role_option;
		$role_options = array_flip( $role_options );
		return $role_options;
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- SQL
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Delete this modifiction and its criteria.
		@since		20131016
	**/
	public function delete_modification( $modification )
	{
		$query = sprintf( "DELETE FROM `%s` WHERE `modification_id` = '%s'", criterion::db_table(), $modification->id );
		$results = $this->query( $query );
		$modification->db_delete();
	}

	/**
		@brief		Retrieve all of the modifications.
		@since		20131016
	**/
	public function get_modifications()
	{
		$query = sprintf( "SELECT * FROM `%s`", modification::db_table() );
		$results = $this->query( $query );
		$r = new collections\modification( modification::sqls( $results ) );
		$r->sort_by_name();
		return $r;
	}

	/**
		@brief		Retrieve the modification criteria for this modification.
		@since		20131016
	**/
	public function get_modification_criteria( $modification )
	{
		$query = sprintf( "SELECT * FROM `%s` WHERE `modification_id` = '%s'", criterion::db_table(), $modification->id );
		$results = $this->query( $query );
		return criterion::sqls( $results );
	}

}

$ThreeWP_Broadcast_UserBlogSettings = new ThreeWP_Broadcast_UserBlogSettings;
