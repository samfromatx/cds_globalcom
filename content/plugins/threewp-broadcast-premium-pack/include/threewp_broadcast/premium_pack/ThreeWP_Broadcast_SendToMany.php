<?php

namespace threewp_broadcast\premium_pack\sendtomany;

use \threewp_broadcast\broadcasting_data;

/**
	@brief		Allows mass broadcast of several posts to blogs at once.
	@since		20131010
**/
class ThreeWP_Broadcast_SendToMany
	extends \threewp_broadcast\premium_pack\base
{
	protected $sdk_version_required = 20130505;		// user_id()

	protected $site_options = [
		'database_version' => 0,					// Version of database and settings
		'role_to_use' => 'super_admin',				// Role to use the plugin
	];

	public function _construct()
	{
		$this->add_action( 'admin_footer' );
		$this->add_action( 'admin_print_footer_scripts' );
		$this->add_action( 'threewp_broadcast_menu' );
		$this->add_action( 'wp_ajax_broadcast_sendtomany_get_meta_box' );
		$this->add_action( 'wp_ajax_broadcast_sendtomany_send_to_many' );
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
		}

		$this->update_site_option( 'database_version', $db_ver );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Admin
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Show the settings tab.f
		@since		20131010
	**/
	public function admin_menu_overview()
	{
		$contents = file_get_contents( __DIR__ . '/sendtomany/html/overview.html' );
		$contents = wpautop( $contents );
		echo $this->broadcast()->html_css();
		echo $contents;
	}

	/**
		@brief		Show the settings tab.
		@since		20131010
	**/
	public function admin_menu_settings()
	{
		$form = $this->form2();

		$roles = $this->roles_as_options();
		$roles = array_flip( $roles );

		$fs = $form->fieldset( 'general' )
			->label_( 'General' );

		$role_to_use = $fs->select( 'role_to_use' )
			->value( $this->get_site_option( 'role_to_use' ) )
			->description_( 'The user role required to use the Send To Many button.' )
			->label_( 'Role to use' )
			->options( $roles );

		$save = $form->primary_button( 'save' )
			->value_( 'Save settings' );

		if ( $form->is_posting() )
		{
			$form->post();
			$form->use_post_values();

			$this->update_site_option( 'role_to_use', $role_to_use->get_post_value() );

			$this->message( 'Options saved!' );
		}

		$r = $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	/**
		@brief		Show all the tabs.
		@since		20131010
	**/
	public function admin_menu_tabs()
	{
		$this->load_language();

		$tabs = $this->tabs();
		$tabs->tab( 'overview' )		->callback_this( 'admin_menu_overview' )		->name_( 'Overview' );

		if ( is_super_admin() )
		{
			$tabs->tab( 'settings' )		->callback_this( 'admin_menu_settings' )		->name_( 'Settings' );
			$tabs->tab( 'uninstall' )		->callback_this( 'admin_uninstall' )			->name_( 'Uninstall' );
		}

		echo $tabs;
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------

	public function admin_footer()
	{
		if ( ! $this->may_use() )
			return;

		wp_enqueue_script( 'broadcast_sendtomany', $this->paths[ 'url' ] . '/sendtomany/js/js.min.js' );
	}

	public function admin_print_footer_scripts()
	{
		if ( ! $this->may_use() )
			return;

		?>
<script type='text/javascript'>
window.broadcast_sendtomany_data = {
	'actions' : {
		'get_meta_box' : 'broadcast_sendtomany_get_meta_box',
		'send_to_many' : 'broadcast_sendtomany_send_to_many'
	},
	'strings' : {
		'loading' : '<?php echo $this->_( 'Loading Send To Many panel...' ) ?>.',
		'no_blogs_selected' : '<?php echo $this->_( 'You need to select at least one blog.' ) ?>.',
		'no_posts_selected' : '<?php echo $this->_( 'You need to select at least one non-child post to be sent.' ) ?>.',
		'send_to_many' : '<?php echo $this->_( 'Send to many' ) ?>',
		'sending' : '<?php echo $this->_( 'Sending...' ) ?>.'
	}
};
</script>
		<?php
	}

	public function wp_ajax_broadcast_sendtomany_get_meta_box()
	{
		if ( ! $this->may_use() )
			return;

		$ajax = new \threewp_broadcast\premium_pack\ajax_data;
		$post_ids = $_POST[ 'post_ids' ];

		$post_ids = explode( ',', $post_ids );
		$post_ids = array_filter( $post_ids );

		// Retrieve the first post so that we can give to the meta box creation method.
		$post_id = intval( reset( $post_ids ) );
		$post = get_post( $post_id );

		if ( ! is_object( $post ) )
		{
			$ajax->error = $this->_( 'An error occured: Could not retrieve the first selected post: %s', $id );
			$ajax->to_json();
		}

		$meta_box_data = new \threewp_broadcast\meta_box\data;
		$meta_box_data->blog_id = get_current_blog_id();
		$meta_box_data->broadcast_data = $this->broadcast()->get_post_broadcast_data( $meta_box_data->blog_id, $post->ID );
		$meta_box_data->form = $this->form2();
		$meta_box_data->post = $post;
		$meta_box_data->post_id = $post->ID;

		$action = new \threewp_broadcast\actions\prepare_meta_box;
		$action->meta_box_data = $meta_box_data;
		$action->apply();

		// Conv
		$form = $meta_box_data->form;

		// Add our CSS
		$meta_box_data->css->put( 'threewp_broadcast_sendtomany', $this->paths[ 'url' ] . '/sendtomany/css/css.scss.css' );

		// Add some broadcast information.
		$header = $this->h3( $this->_( 'Send To Many' ) );
		$meta_box_data->html->insert_before( 'link', 'send_to_many_header' , $header );
		$meta_box_data->html->insert_before( 'link', 'send_to_many_info' , $this->p_( 'After selecting the blogs to which you want to broadcast the selected, press the Send To Many button.', count( $post_ids ) ) );

		// Add a "send to many" button
		$button = $form->primary_button( 'send_to_many' )
			->id( 'send_to_many' )
			->value_( 'Send To Many' );
		$meta_box_data->html->put( 'send_to_many', $button->display_input() );

		// Add a "cancel" button
		$button = $form->secondary_button( 'cancel_send_to_many' )
			->id( 'cancel_send_to_many' )
			->value_( 'Cancel' );
		$meta_box_data->html->put( 'cancel_send_to_many', $button->display_input() );

		// And convert the HTML to a complete form.
		$html = $form->open_tag()
			. $meta_box_data->html
			. $form->close_tag();
		$ajax->html = $html;
		$ajax->css = $meta_box_data->css->toArray();
		$ajax->js = $meta_box_data->js->toArray();
		$ajax->action = 'broadcast_sendtomany_send_to_many';

		$ajax->to_json();
	}

	/**
		@brief		Add ourself to Broadcast's menu.
		@since		20131006
	**/
	public function threewp_broadcast_menu( $action )
	{
		if ( ! $this->may_use() )
			return;

		$action->broadcast->add_submenu_page(
			'threewp_broadcast',
			$this->_( 'Broadcast Send To Many' ),
			$this->_( 'Send To Many' ),
			'edit_posts',
			'threewp_broadcast_send_to_many',
			[ &$this, 'admin_menu_tabs' ]
		);
	}

	public function wp_ajax_broadcast_sendtomany_send_to_many()
	{
		$ajax = new \threewp_broadcast\premium_pack\ajax_data;

		if ( ! $this->may_use() )
			return;

		$upload_dir = wp_upload_dir();

		$original_post = $_POST;

		$post_ids = $_POST[ 'post_ids' ];
		$post_ids = explode( ',', $post_ids );
		$post_ids = array_filter( $post_ids );

		foreach( $post_ids as $post_id )
		{
			$post_id = intval( $post_id );

			if ( $post_id < 1 )
			{
				$this->debug( 'Skipping post %s.', $post_id );
				continue;
			}

			$post = get_post( $post_id );

			if ( ! $post )
			{
				$this->debug( 'Skipping non-post %s on blog %s. %s', $post_id, get_current_blog_id(), $this->broadcast()->code_export( $post ) );
				continue;
			}
			else
				$this->debug( 'Post %s on blog %s is OK.', $post_id, get_current_blog_id() );

			$meta_box_data = $this->broadcast()->create_meta_box( $post );

			// Allow plugins to modify the meta box with their own info.
			$action = new \threewp_broadcast\actions\prepare_meta_box;
			$action->meta_box_data = $meta_box_data;
			$action->apply();

			$_POST = $original_post;

			$broadcasting_data = new broadcasting_data( [
				'_POST' => $_POST,
				'meta_box_data' => $meta_box_data,
				'parent_blog_id' => get_current_blog_id(),
				'parent_post_id' => $post_id,
				'post' => $post,
				'upload_dir' => $upload_dir,
			] );

			$action = new \threewp_broadcast\actions\prepare_broadcasting_data;
			$action->broadcasting_data = $broadcasting_data;
			$action->apply();

			if ( $broadcasting_data->has_blogs() )
			{
				$this->debug( 'Sending post %s on blog %s.', $post_id, get_current_blog_id() );
				$this->filters( 'threewp_broadcast_broadcast_post', $broadcasting_data );
			}
		}
		$ajax->to_json();
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------

	/**
		@brief		May the user use Send To Many?
		@since		20131010
	**/
	public function may_use()
	{
		// Is the cache property set?
		if ( isset( $this->_may_use ) )
			return $this->_may_use;

		// Is the broadcast meta box displayable at all?
		if ( ! $this->broadcast()->display_broadcast_meta_box )
			$this->_may_use = false;

		if ( ! isset( $this->_may_use ) )
			$this->_may_use = ( is_super_admin() || $this->role_at_least( $this->get_site_option( 'role_to_use' ) ) );

		return $this->may_use();
	}
}

$ThreeWP_Broadcast_SendToMany = new ThreeWP_Broadcast_SendToMany;
