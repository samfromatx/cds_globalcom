<?php

namespace threewp_broadcast\premium_pack\ubs_posts;

use \threewp_broadcast\broadcasting_data;

/**
	@brief		Post quickly from the post overview using User & Blog Settings modifications.
	@since		2014-08-01 21:17:44
**/
class ThreeWP_Broadcast_UserBlogSettings_Post
	extends \threewp_broadcast\premium_pack\base
{
	public function _construct()
	{
		$this->add_action( 'admin_footer' );
		$this->add_action( 'admin_print_footer_scripts' );
		$this->add_action( 'wp_ajax_broadcast_ubs_post' );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Admin
	// --------------------------------------------------------------------------------------------

	public function admin_footer()
	{
		if ( ! $this->may_use() )
			return;

		wp_enqueue_script( 'broadcast_ubs_post', $this->paths[ 'url' ] . '/ubs_post/js.js' );
	}

	public function admin_print_footer_scripts()
	{
		if ( ! $this->may_use() )
			return;

		// Return a list of all available modifications.
		$modifications = $this->ubs->get_modifications();

		$form = $this->form2();
		$setting = $form->select( 'ubs_setting' );
		$setting->option( $this->_( 'Post with UBS' ), '' );
		foreach( $modifications as $modification )
			$setting->option( $modification->data->name, $modification->id );
		$options = addslashes( $setting->display_input() );
		$options = str_replace( "\n", '', $options );

		?>
<script type='text/javascript'>
window.ubs_post_data = {
	'actions' : {
		'post' : 'broadcast_ubs_post'
	},
	'select' : '<?php echo $options; ?>',
	'strings' : {
		'no_posts_selected' : '<?php echo $this->_( 'You need to select at least one non-child post to be sent.' ) ?>.',
		'broadcasting' : '<?php echo $this->_( 'Broadcasting...' ) ?>.'
	}
};
</script>
		<?php
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------
	public function wp_ajax_broadcast_ubs_post()
	{
		$ajax = new \threewp_broadcast\premium_pack\ajax_data;

		if ( ! $this->may_use() )
			return;

		$modifications = $this->ubs->get_modifications();
		$upload_dir = wp_upload_dir();

		$original_post = $_POST;

		$post_ids = $_POST[ 'post_ids' ];
		$post_ids = explode( ',', $post_ids );
		$post_ids = array_filter( $post_ids );

		$modification_id = $_POST[ 'modification_id' ];
		if ( ! isset( $modifications[ $modification_id ] ) )
		{
			$this->debug( 'Invalid modification ID!' );
			return false;
		}

		$modification = $modifications[ $modification_id ];

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

			$modification->modify_meta_box( $meta_box_data );

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
	// ----------------------------------------- Misc functions
	// --------------------------------------------------------------------------------------------

	/**
		@brief		May the user use the button?
		@since		2014-08-01 21:28:23
	**/
	public function may_use()
	{
		// Is the cache property set?
		if ( isset( $this->_may_use ) )
			return $this->_may_use;

		// The UBS plugin must be enabled.
		$this->ubs = \threewp_broadcast\premium_pack\userblogsettings\ThreeWP_Broadcast_UserBlogSettings::instance();
		if ( ! is_object( $this->ubs ) )
			$this->_may_use = false;

		// Is the broadcast meta box displayable at all?
		if ( ! isset( $this->_may_use ) )
			if ( ! $this->broadcast()->display_broadcast_meta_box )
				$this->_may_use = false;

		if ( ! isset( $this->_may_use ) )
			$this->_may_use = ( is_super_admin() || $this->role_at_least( $this->get_site_option( 'role_to_use' ) ) );

		return $this->may_use();
	}
}

$ThreeWP_Broadcast_UserBlogSettings_Post = new ThreeWP_Broadcast_UserBlogSettings_Post;
