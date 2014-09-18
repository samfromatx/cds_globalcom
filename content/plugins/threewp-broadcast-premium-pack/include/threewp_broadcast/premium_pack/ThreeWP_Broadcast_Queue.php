<?php

namespace threewp_broadcast\premium_pack\queue;

use \plainview\sdk\collections\collection;
use \threewp_broadcast\blog;

/**
	@brief		Adds a broadcast queue which helps to broadcast posts to tens / hundreds / more blogs.
	@since		20131006
**/
class ThreeWP_Broadcast_Queue
	extends \threewp_broadcast\premium_pack\base
{
	protected $sdk_version_required = 20130505;		// user_id()

	protected $site_options = [
		'database_version' => 0,					// Version of database and settings
		'enabled' => true,							// Accept posts into the queue
		'process_queue' => true,					// Process the queue
	];

	public function _construct()
	{
		$this->add_action( 'threewp_broadcast_prepare_meta_box' );
		$this->add_action( 'threewp_broadcast_broadcast_post', 9 );		// Just before Broadcast itself.
		$this->add_action( 'threewp_broadcast_manage_posts_custom_column' );
		$this->add_action( 'threewp_broadcast_menu' );
		$this->add_action( 'wp_ajax_broadcast_queue_process' );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Activate / Deactivate
	// --------------------------------------------------------------------------------------------

	public function activate()
	{
		$db_ver = $this->get_site_option( 'database_version', 0 );

		if ( $db_ver < 1 )
		{
			$this->query("CREATE TABLE IF NOT EXISTS `".$this->wpdb->base_prefix."3wp_broadcast_queue_data` (
				`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Row ID',
				`broadcasting_data` longtext NOT NULL COMMENT 'Serialized broadcasting_data object',
				`created` datetime NOT NULL COMMENT 'When the data was queued',
				`parent_blog_id` int(11) NOT NULL COMMENT 'Parent blog ID',
				`parent_post_id` int(11) NOT NULL COMMENT 'Parent post ID',
				`user_id` int(11) NOT NULL COMMENT 'ID of user that broadcasted',
				PRIMARY KEY (`id`),
				KEY `parent_blog_id` (`parent_blog_id`,`parent_post_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1;
			");

			$this->query("CREATE TABLE IF NOT EXISTS `".$this->wpdb->base_prefix."3wp_broadcast_queue_items` (
				`id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Row ID',
				`blog` text NOT NULL COMMENT 'Child blog ID as blog object',
				`data_id` int(11) NOT NULL COMMENT 'ID of data row',
				`lock_key` varchar(6) NOT NULL COMMENT 'Key used to lock the row',
				`touched` datetime NOT NULL COMMENT 'When this row was lasted touched',
				PRIMARY KEY (`id`),
				KEY `data_id` (`data_id`,`touched`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1;
			");

			$db_ver = 1;
		}

		$this->update_site_option( 'database_version', $db_ver );
	}

	public function uninstall()
	{
		$this->query("DROP TABLE `".$this->wpdb->base_prefix."3wp_broadcast_queue_data`");
		$this->query("DROP TABLE `".$this->wpdb->base_prefix."3wp_broadcast_queue_items`");
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Admin
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Show the item queue.
		@since		20131006
	**/
	public function admin_menu_queue()
	{
		$count = $this->get_queue_items( [ 'count' => true ] );

		$per_page = 250;
		$max_pages = floor( $count / $per_page );
		$page = ( isset( $_GET['paged'] ) ? intval( $_GET['paged'] ) : 1 );
		$page = $this->minmax( $page, 1, $max_pages );

		$items = $this->get_queue_items( [
			'limit' => $per_page,
			'page' => ( $page-1 ),
		] );

		$page_links = paginate_links( array(
			'base' => add_query_arg( 'paged', '%#%' ),
			'format' => '',
			'prev_text' => '&laquo;',
			'next_text' => '&raquo;',
			'current' => $page,
			'total' => $max_pages,
		));

		if ( $page_links )
			$page_links = '<div style="width: 50%; float: right;" class="tablenav"><div class="tablenav-pages">' . $page_links . '</div></div>';

		$form = $this->form2();
		$r = $page_links;
		$table = $this->table();

		$row = $table->head()->row();
		$table->bulk_actions()
			->form( $form )
			->add( $this->_( 'Delete' ), 'delete' )
			->add( $this->_( 'Process' ), 'process' )
			->cb( $row );
		$row->th()->text( 'User' );
		$row->th()->text( 'From' );
		$row->th()->text( 'To' );
		$row->th()->text( 'Status' );

		if ( $form->is_posting() )
		{
			$this->debug( 'Handling bulk actions.' );
			$form->post();
			if ( $table->bulk_actions()->pressed() )
			{
				switch ( $table->bulk_actions()->get_action() )
				{
					case 'delete':
						$ids = $table->bulk_actions()->get_rows();

						foreach( $ids as $id )
						{
							$item = $this->get_queue_item( $id );
							if ( $item )
								$item->db_delete();
							$data = $this->get_queue_data( $item->data_id );

							$max = $this->get_queue_items([
								'count' => true,
								'parent_blog_id' => $data->parent_blog_id,
								'parent_post_id' => $data->parent_post_id,
							]);

							// If there are no more items that require this data, delete it.
							if ( $max < 1 )
								$data->db_delete();
						}

						echo $this->message_( 'The selected rows were deleted! Please reload this page to see the current queue.' );
					break;
					case 'process':
						$ids = $table->bulk_actions()->get_rows();

						$m = [];

						$this->debug( 'Beginning to process %s items.', count( $ids ) );

						foreach( $ids as $id )
						{
							$this->debug( 'Fetching queue item %s.', $id );
							$item = $this->get_queue_item( $id );

							if ( ! $item )
							{
								$this->debug( 'Skipping item because it is invalid.' );
								continue;
							}
							$this->debug( 'Retrieving data.' );
							$data = $this->get_queue_data( $item->data_id );

							$this->debug( 'Data is %s bytes long.', strlen( serialize( $data ) ) );
							$text = $this->wp_ajax_broadcast_queue_process([
								'debug' => $this->broadcast()->debugging(),
								'display' => false,
								'parent_blog_id' => $data->parent_blog_id,
								'parent_post_id' => $data->parent_post_id,
								'display' => false,
							]);
							$this->debug( 'Item processed.' );
							$m []= $this->p_( 'Processing item %s: %s', $id, htmlspecialchars( $text ) );
						}

						$m []= $this->_( 'The selected rows were processed! Please click on the queue tab to reload this page and see the current queue.' );

						echo $this->message( '<ul>' . $this->implode_html( $m ) . '</ul>' );
					break;
				}
			}
		}

		$this->cache = new collection;
		$cache = $this->cache;

		$item_counts = new collection;

		$datas = new collection;
		foreach( $items as $item )
		{
			$data_id = $item->data_id;		// Conv
			if ( ! $datas->has( $data_id ) )
			{
				$data = new collection;
				$datas->put( $data_id, $data );
			}
			$data = $datas->get( $data_id );
			$data->put( $item->id, $item );

			// Count the items
			$key = sprintf( '%s_%s', $item->parent_blog_id, $item->parent_post_id );
			if ( ! $item_counts->has( $key ) )
				$item_counts->put( $key, 0 );
			$item_count = $item_counts->get( $key );
			$item_count++;
			$item_counts->put( $key, $item_count );
		}

		$process_queue = $this->get_site_option( 'process_queue' );

		foreach( $datas as $data )
		{
			$item = $data->first();

			$row = $table->body()->row();

			$table->bulk_actions()->cb( $row, $item->id );

			// USER
			$key = 'user' . $item->user_id;
			$user = $cache->get( $key );
			if ( $user === null )
			{
				$user = get_userdata( $item->user_id );
				$cache->set( $key, $user );
			}

			$row->td()->text( $user->data->user_login );

			// FROM
			$key = 'blog' . $item->parent_blog_id;
			$blog = $cache->get( $key );
			if ( $blog === null )
			{
				$blog = get_blog_details( $item->parent_blog_id );
				$cache->set( $key, $blog );
			}

			$key = 'post' . $item->parent_blog_id . '_' . $item->parent_post_id;
			$post = $cache->get( $key );
			if ( $post === null )
			{
				switch_to_blog( $item->parent_blog_id );
				$post = get_post( $item->parent_post_id );
				$cache->set( $key . 'permalink', get_permalink( $item->parent_post_id ) );
				restore_current_blog();
				$cache->set( $key, $post );
			}
			$row->td()->text( sprintf( '<a href="%s"><em>%s</em></a> from %s',
				$cache->get( $key . 'permalink' ),
				$post->post_title,
				$blog->blogname
			) );

			// TO
			$text = new collection;
			foreach( $data as $item )
			{
				$text->append( '<span title="Item was last touched:' . $item->touched . '">' . $item->blog );
			}
			$row->td()->text( $this->implode_html( $text->toArray() ) );

			$key = sprintf( '%s_%s', $item->parent_blog_id, $item->parent_post_id );
			$item_count = $item_counts->get( $key );

			// STATUS
			$pd = new process_data\data;
			$pd->parent_blog_id = $item->parent_blog_id;
			$pd->parent_post_id = $item->parent_post_id;
			$pd->item_count = $item_count;
			$this->build_process_data( $pd );
			$row->td()->text( $pd->html );
		}

		$r .= $this->p_( 'The Broadcast Queue plugin put broadcasts of new and updated posts into a queue, which is processed via Javascript either in the post edit window, the post list table or this page.' );

		if ( $this->get_site_option( 'process_queue' ) )
			$r .= $this->p_( 'The processing is done automatically in the background as long as either of the pages are being viewed by the user or yourself.' );
		else
			$r .= $this->p_( 'Queue processing has been disabled in the settings.' );

		$r .= $form->open_tag();
		$r .= $table;
		$r .= $form->close_tag();

		$r .= $page_links;
		echo $r;
	}

	/**
		@brief		Show the settings tab.
		@since		20131006
	**/
	public function admin_menu_settings()
	{
		$form = $this->form2();

		$fs = $form->fieldset( 'general' )
			->label_( 'General' );

		$input_enabled = $fs->checkbox( 'enabled' )
			->checked( $this->get_site_option( 'enabled' ) )
			->description_( 'Accept new items into the queue.' )
			->label_( 'Accept posts into queue' );

		$input_process_queue = $fs->checkbox( 'process_queue' )
			->checked( $this->get_site_option( 'process_queue' ) )
			->description_( 'Automatically process the queue items.' )
			->label_( 'Process the queue' );

		$save = $form->primary_button( 'save' )
			->value_( 'Save settings' );

		if ( $form->is_posting() )
		{
			$form->post();
			$form->use_post_values();

			$this->update_site_option( 'enabled', $input_enabled->is_checked() );
			$this->update_site_option( 'process_queue', $input_process_queue->is_checked() );

			$this->message( 'Options saved!' );
		}

		$r = $form->open_tag();
		$r .= $form->display_form_table();
		$r .= $form->close_tag();

		echo $r;
	}

	/**
		@brief		Show all the tabs.
		@since		20131006
	**/
	public function admin_menu_tabs()
	{
		$this->load_language();

		$tabs = $this->tabs();
		$tabs->tab( 'queue' )			->callback_this( 'admin_menu_queue' )			->name_( 'Queue' );
		$tabs->tab( 'settings' )		->callback_this( 'admin_menu_settings' )		->name_( 'Settings' );
		$tabs->tab( 'uninstall' )		->callback_this( 'admin_uninstall' )			->name_( 'Uninstall' );

		echo $tabs;
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Output some javascript containing string translations.
		@since		20131006
	**/
	public function admin_print_footer_scripts()
	{
		echo sprintf( '<script type="text/javascript">
			window.threewp_broadcast_queue_strings = {
				"no_json" : "%s",
				"processing" : "%s",
				"waiting" : "%s"
			};
		</script>',
			$this->_( 'No JSON in reply.' ),
			$this->_( 'Processing queue...' ),
			$this->_( 'Retrying in <span class=\"seconds\" /> seconds.' )
		);
	}

	public function post_row_actions( $actions, $post )
	{
		$this->item_cache()->expect_from_wp_query();
		return $actions;
	}

	/**
		@brief		Add queue information to the meta box.
		@since		20131006
	**/
	public function threewp_broadcast_prepare_meta_box( $action )
	{
		$mbd = $action->meta_box_data;

		$this->item_cache()->expect( $action->meta_box_data->blog_id, $action->meta_box_data->post_id );

		$process_data = new process_data\data;
		$process_data->parent_blog_id = $action->meta_box_data->blog_id;
		$process_data->parent_post_id = $action->meta_box_data->post_id;
		$this->build_process_data( $process_data );

		$mbd->html->put( 'broadcast_queue', $process_data->html );
	}

	/**
		@brief		Add ourself to Broadcast's menu.
		@since		20131006
	**/
	public function threewp_broadcast_menu( $action )
	{
		if ( ! is_super_admin() )
			return;

		$this->add_action( 'post_row_actions', 10, 2 );

		$action->broadcast->add_submenu_page(
			'threewp_broadcast',
			$this->_( 'Broadcast Queue' ),
			$this->_( 'Queue' ),
			'edit_posts',
			'threewp_broadcast_queue',
			[ &$this, 'admin_menu_tabs' ]
		);
	}

	/**
		@brief		Intercept broadcasting of posts.
		@since		20131006
	**/
	public function threewp_broadcast_broadcast_post( $broadcasting_data )
	{
		if ( ! $this->get_site_option( 'enabled' ) )
			return $broadcasting_data;

		// TODO: trim the queue of whatever touchable items are left of this blog+post.
		// TODO: add only those blogs that have already been queued + broadcasted.

		// Create a new queue data object.
		$data = new data;
		$data->created = $this->now();
		$data->broadcasting_data = $broadcasting_data;
		$data->user_id = $this->user_id();
		$data->parent_blog_id = $broadcasting_data->parent_blog_id;
		$data->parent_post_id = $broadcasting_data->parent_post_id;
		$data->db_insert();

		// And now insert each blog (item)
		foreach( $broadcasting_data->blogs as $blog )
		{
			$item = new item;
			$item->blog = $blog;
			$item->data_id = $data->id;
			$item->touched = '2013-01-01';
			$item->lock_key = item::generate_lock_key();
			$item->db_insert();
		}

		// We've handled the broadcasting data. Don't give it back to Broadcast.
		return null;
	}

	/**
		@brief		Add queue information to the posts custom column.
		@since		20131006
	**/
	public function threewp_broadcast_manage_posts_custom_column( $action )
	{
		$process_data = new process_data\data;
		$process_data->display_ready_string = false;
		$process_data->parent_blog_id = $action->parent_blog_id;
		$process_data->parent_post_id = $action->parent_post_id;
		$this->build_process_data( $process_data );

		$action->html->put( 'queue', $process_data->html );
	}

	/**
		@brief		Process a queue item for a specific blog/post.
		@since		20131006
	**/
	public function wp_ajax_broadcast_queue_process( $POST = '' )
	{
		// Display only fatal errors
		error_reporting( E_ERROR );

		$this->clean_queue();

		if ( $POST === '' )
			$POST = $_POST;

		$ajax = new ajax_data;

		if ( isset( $POST[ 'display' ] ) )
			$ajax->display( $POST[ 'display' ] );

		if ( isset( $POST[ 'debug' ] ) )
		{
			error_reporting( E_ALL || E_ERROR );
			$ajax->debug = true;
		}

		$ajax->debug( 'Debugging is enabled.' );

		$parent_blog_id = intval( $POST[ 'parent_blog_id' ] );
		$parent_post_id = intval( $POST[ 'parent_post_id' ] );

		$max = $this->get_queue_items([
			'count' => true,
			'parent_blog_id' => $parent_blog_id,
			'parent_post_id' => $parent_post_id,
		]);

		$ajax->debug( 'Max is: %s', $max );

		// No items what so ever?
		if ( $max < 1 )
		{
			$ajax->debug( '' );
			$ajax->finished = true;
			$ajax->html( $this->get_queue_ready_string() );
			return $ajax->to_json();
		}

		$data_items = $this->get_queue_items([
			'parent_blog_id' => $parent_blog_id,
			'parent_post_id' => $parent_post_id,
			'touchable' => true,
		]);

		$wait = item::$touchable_seconds;
		$ajax->wait( $wait );
		$ajax->debug( 'Wait is set to %s seconds.', $wait );

		if ( count( $data_items ) < 1 )
		{
			$message = $this->_( 'Zero of %s items are ready.', $max );
			$ajax->debug( $message );
			$ajax->html( $message );
			$ajax->no_items = true;
			return $ajax->to_json();
		}

		$ajax->debug( 'Searching for a broadcastable item amongst %s.', count( $data_items ) );

		foreach( $data_items as $data_item )
		{
			// Convert to an item in order to
			$item = item::sql( $data_item );
			// item will try to unserialize blog, which is already unserialized.
			foreach( item::keys_to_serialize() as $key )
				$item->$key = $data_item->$key;

			if ( ! $item->is_touchable() )
			{
				$ajax->debug( 'Item is not touchable.' );
				continue;
			}

			// It is touchable. Try to lock it.
			$item = $item->lock();
			if ( ! $item->locked )
			{
				$ajax->debug( 'Item is locked.' );
				continue;
			}

			$ajax->debug( 'Now loading item.' );

			$data = data::db_load( $item->data_id );
			try
			{
				$ajax->debug( 'Preparing to broadcast.' );
				$result = $data->broadcast( $item->blog );
				$ajax->debug( 'Broadcast complete. Return value: %d', $result );
				if ( $result )
				{
					$max--;

					$ajax->debug( 'Items in queue: %s', $max );
					$ajax->html( $this->_( 'Items in queue: %s', $max ) );
					$item->db_delete();

					// Delete the data?
					if ( $max == 0 )
					{
						$ajax->debug( 'Deleting the data.' );
						$data = $this->get_queue_data( $item->data_id );
						$data->db_delete();
						$ajax->finished = true;
						$ajax->debug( 'We are finished.' );
						$ajax->html( $this->get_queue_ready_string() );
					}

					$ajax->wait( 1 );
				}
				else
				{
					$ajax->debug( 'Error broadcasting to the blog.' );
					$ajax->html( $this->_( 'Error broadcasting to %s.',
						$data_item->blog
					) );
				}

				$ajax->wait( 1 );
			}
			catch( \Exception $e )
			{
				$ajax->debug( 'Exception thrown during broadcast: %s', $e->getMessage() );
			}
			break;
		}

		return $ajax->to_json();
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Misc functions
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Build the process data with js and HTML.
		@since		20131006
	**/
	public function build_process_data( $process_data )
	{
		if ( $process_data->item_count === null )
		{
			$data = $this->item_cache()->get_for( $process_data->parent_blog_id, $process_data->parent_post_id );
			if ( $data )
				$process_data->item_count = $data->item_count;
			else
				$process_data->item_count = 0;
		}

		$enabled = $this->get_site_option( 'enabled' );

		if ( $process_data->item_count < 1 )
		{
			if ( $enabled )
			{
				if ( $process_data->display_ready_string )
					$process_data->html = $this->get_queue_ready_string();
			}
			else
				$process_data->html = $this->_( 'Queue disabled.' );
			return;
		}

		if ( $this->get_site_option( 'process_queue' ) )
		{
			// Is processing.
			$this->enqueue_js();

			$process_data->html = sprintf( '<div id="%s" class="broadcast_queue_widget active" data-parent_blog_id="%s" data-parent_post_id="%s" data-action="%s">%s</div>',
				md5( microtime() ),
				$process_data->parent_blog_id,
				$process_data->parent_post_id,
				'broadcast_queue_process',
				$this->_( 'Items in queue: %s', $process_data->item_count )
			);
		}
		else
		{
			// Not processing.
			if ( $enabled )
				$process_data->html = $this->_( 'Queue enabled but processing disabled. Items in queue: %s', $process_data->item_count );
			else
				$process_data->html = $this->_( 'Queue and processing disabled. Items in queue: %s', $process_data->item_count );
		}

	}

	/**
		@brief		Enqueue Queue's JS file.
		@since		20131006
	**/
	public function enqueue_js()
	{
		if ( isset( $this->js_enqueued ) )
			return;
		wp_enqueue_script( 'broadcast_queue', $this->paths[ 'url' ] . '/queue/js/queue.min.js', '', $this->plugin_version );
		$this->add_action( 'admin_print_footer_scripts' );
		$this->js_enqueued = true;
	}

	/**
		@brief		Return a HTML string that says that the queue is ready.
		@since		20131006
	**/
	public function get_queue_ready_string()
	{
		return $this->_( '%sQueue%s ready.',
			'<a href="http://plainview.se/wordpress/threewp-broadcast-premium-pack/">',
			'</a>'
		);
	}

	/**
		@brief		Returns the current item_cache object.
		@return		process_data\\cache		A newly-created or old cache object.
		@since		201301009
	**/
	public function item_cache()
	{
		$property = 'item_cache';
		if ( ! property_exists( $this, 'item_cache' ) )
			$this->$property = new item_cache;
		return $this->$property;
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- SQL
	// --------------------------------------------------------------------------------------------

	public function clean_queue()
	{
		// Delete orphaned items
		$query = sprintf( "DELETE FROM `%s`
			WHERE data_id NOT IN ( SELECT DISTINCT id FROM `%s` )
		",
			$this->wpdb->base_prefix."3wp_broadcast_queue_items",
			$this->wpdb->base_prefix."3wp_broadcast_queue_data"
		);
		$result = $this->query_single($query);

		// Delete orphaned data
		$query = sprintf( "DELETE FROM `%s`
			WHERE id NOT IN ( SELECT DISTINCT data_id FROM `%s` )
		",
			$this->wpdb->base_prefix."3wp_broadcast_queue_data",
			$this->wpdb->base_prefix."3wp_broadcast_queue_items"
		);
		$result = $this->query_single($query);
	}

	/**
		@brief		Retrieve a queue data object.
		@since		20131006
	**/
	public function get_queue_data( $id )
	{
		$query = ( "SELECT * FROM `".$this->wpdb->base_prefix."3wp_broadcast_queue_data` as d
			WHERE d.`id` = '$id'
			LIMIT 1"
		);
		$result = $this->query_single($query);
		return data::sql( $result );
	}

	/**
		@brief		Retrieve a queue data item.
		@since		20131006
	**/
	public function get_queue_item( $id )
	{
		$query = ( "SELECT *, `i`.`id` as `id` FROM `".$this->wpdb->base_prefix."3wp_broadcast_queue_items` as i
			RIGHT JOIN `".$this->wpdb->base_prefix."3wp_broadcast_queue_data` as d
			ON ( i.`data_id` = d.`id` )
			WHERE i.`id` = '$id'
			LIMIT 1"
		);
		$result = $this->query_single($query);
		return item::sql( $result );
	}

	/**
		@brief		Multimethod to query the queue items.
		@since		20131006
	**/
	public function get_queue_items( $o )
	{
		$o = $this->merge_objects( [
			'count' => false,
			'data_id' => 0,
			'limit' => 1000,
			'page' => 0,
			'parent_blog_id' => null,
			'parent_post_id' => null,
			'parent_post_ids' => null,
			'select' => null,
			'touchable' => false,
			'where' => [ '1=1' ],
		], $o );

		$select_keys = [];

		if ( $o->count )
			$select_keys []= 'count(*) as ROWS';

		$group_by = '';

		if ( $o->page > 0)
			$o->page = $o->page * $o->limit;

		if ( $o->data_id > 0 )
			$o->where []= 'd.`id` = ' . $o->data_id;

		if ( $o->parent_blog_id > 0 )
			$o->where []= 'd.`parent_blog_id` = ' . $o->parent_blog_id;

		if ( $o->parent_post_id > 0 )
			$o->where []= 'd.`parent_post_id` = ' . $o->parent_post_id;

		if ( $o->parent_post_ids !== null )
		{
			$o->where []= sprintf( "d.`parent_post_id` IN ('%s')", implode( "', '", $o->parent_post_ids ) );
			$group_by = 'GROUP BY `i`.`data_id`';
			$select_keys = data_item::keys();
			$select_keys [ 'item_count' ]= 'COUNT( `data_id` ) as `item_count`';
		}

		if ( count( $select_keys ) < 1 )
			$select_keys = data_item::keys();

		if ( $o->select === null )
		{
			// Fix ambiguous id
			$select_keys = array_flip( $select_keys );
			unset( $select_keys [ 'id' ] );

			// Remove item count, because we might put in a nicer, existing one later...
			unset( $select_keys [ 'item_count' ] );
			$select_keys [ '`i`.`id` as `id`' ] = time();

			$select_keys = array_flip( $select_keys );

			// Maybe fix item count.
			if ( ! isset( $select_keys [ 'item_count' ] ) )
				$select_keys [ 'item_count' ] = '1 as `item_count`';

			$o->select = implode( ', ', $select_keys );
		}

		if ( $o->touchable > 0 )
			$o->where []= 'i.`touched` < now() - INTERVAL ' . item::$touchable_seconds.  ' SECOND';

		$query = ("SELECT ".$o->select.", `i`.`id` as `id` FROM `".$this->wpdb->base_prefix."3wp_broadcast_queue_items` as i
			INNER JOIN `".$this->wpdb->base_prefix."3wp_broadcast_queue_data` as d
			ON ( i.`data_id` = d.`id` )
			WHERE " . implode( ' AND ', $o->where ) . "
			" . $group_by. "
			ORDER BY i.`id`
			".( isset( $o->limit ) ? "LIMIT " . $o->page. "," . $o->limit : '')."
		");
		$result = $this->query($query);

		if ( $o->count )
		 	return $result[0]['ROWS'];
		else
			return data_item::sqls( $result );
	}
}

$ThreeWP_Broadcast_Queue = new ThreeWP_Broadcast_Queue;
