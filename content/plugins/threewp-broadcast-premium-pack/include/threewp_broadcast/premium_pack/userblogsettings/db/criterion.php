<?php

namespace threewp_broadcast\premium_pack\userblogsettings\db;

use \plainview\sdk\collections\collection;

class criterion
	extends \threewp_broadcast\premium_pack\db_object
{
	use \plainview\sdk\wordpress\traits\db_aware_object;

	public $id;
	public $blog_id = null;
	public $modification_id;
	public $role_id = null;
	public $user_id = null;

	public function __toString()
	{
		$info = [];
		$ubs = \threewp_broadcast\premium_pack\userblogsettings\ThreeWP_Broadcast_UserBlogSettings::instance();

		if ( $this->blog_id > 0 )
		{
			$blogs = $ubs->cached_blogs();
			if ( ! $blogs->has( $this->blog_id ) )
				$info[] = $ubs->_( 'Blog %s does no longer exist.', $this->blog_id );
			else
				$info[] = $blogs->get( $this->blog_id )->blogname;
		}

		if ( $this->role_id > 0 )
		{
			$role_options = array_flip( $ubs->roles_as_ids() );
			$info[] = $role_options[ $this->role_id ];
		}

		if ( $this->user_id > 0 )
		{
			$users = $ubs->cached_users();
			$users = array_flip( $users );
			if ( isset( $users[ $this->user_id ] ) )
				$info[] = $users[ $this->user_id ];
			else
				$info[] = $ubs->_( 'User %s does no longer exist.', $this->user_id );
		}

		if ( count( $info ) < 1 )
			$info[] = $ubs->_( 'All users on all blogs.' );

		return implode( ', ', $info );
	}

	public static function db_table()
	{
		global $wpdb;
		return $wpdb->base_prefix. '3wp_broadcast_ubs_criteria';
	}

	public static function keys()
	{
		return [
			'id',
			'blog_id',
			'modification_id',
			'role_id',
			'user_id',
		];
	}

	public function matches( $o )
	{
		$matches = true;
		foreach( [ 'blog_id', 'role_id', 'user_id' ] as $key )
		{
			if ( ! isset( $o->$key ) )
				$value = 0;
			else
				$value = $o->$key;
			if ( $this->$key != $value )
			{
				$matches = false;
				break;
			}
		}
		return $matches;
	}
}
