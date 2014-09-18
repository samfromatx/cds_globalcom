<?php

namespace threewp_broadcast\premium_pack\queue;

class data
	extends \threewp_broadcast\premium_pack\db_object
{
	use \plainview\sdk\wordpress\traits\db_aware_object;

	public $id;
	public $broadcasting_data;
	public $created;
	public $parent_blog_id;
	public $parent_post_id;
	public $user_id;

	/**
		@brief		Broadcast this data to a blog.
		@since		2014-01-16 20:43:20
	**/
	public function broadcast( $blog )
	{
		$bcd = $this->broadcasting_data;
		$bcd->stop_after_broadcast = false;
		$bcd->blogs->flush();
		$bcd->broadcast_to( $blog );
		\threewp_broadcast\ThreeWP_Broadcast::instance()->broadcast_post( $bcd );
		// TODO: would be great is broadcast_post returned errors...
		return true;
	}

	/**
		@brief		Set when the data was created.
		@since		20131004
	**/
	public function created( $created = null )
	{
		if ( $created === null )
			$created = \plainview\sdk\wordpress\base::now();
		return $this->set_key( 'created', $created );
	}

	public static function db_table()
	{
		global $wpdb;
		return $wpdb->base_prefix. '3wp_broadcast_queue_data';
	}

	public static function keys()
	{
		return [
			'id',
			'broadcasting_data',
			'created',
			'parent_blog_id',
			'parent_post_id',
			'user_id',
		];
	}

	public static function keys_to_serialize()
	{
		return [
			'broadcasting_data',
		];
	}
}
