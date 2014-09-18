<?php

namespace threewp_broadcast\premium_pack\woocommerce;

/**
	@brief		Enable broadcasting of WooCommerce product variations.
	@since		20131117
**/
class ThreeWP_Broadcast_WooCommerce
extends \threewp_broadcast\premium_pack\base
{
	protected $sdk_version_required = 20130505;		// add_action / add_filter

	public function _construct()
	{
		$this->add_action( 'threewp_broadcast_prepare_meta_box' );
		$this->add_action( 'threewp_broadcast_broadcasting_before_restore_current_blog' );
		$this->add_action( 'threewp_broadcast_broadcasting_started' );
	}

	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Callbacks
	// --------------------------------------------------------------------------------------------
	/**
		@brief		Add information to the broadcast box about the status of Broadcast ACF.
		@since		20131117
	**/
	public function threewp_broadcast_prepare_meta_box( $action )
	{
		$action->meta_box_data->html->put( 'broadcast_woocommerce', sprintf( '<div class="broadcast_acf">%s</div><!-- broadcast_acf -->',
			$this->generate_meta_box_info()
		) );
	}

	/**
		@brief		Handle updating of the advanced custom fields image fields.
		@param		$action		Broadcast action.
		@since		20131117
	**/
	public function threewp_broadcast_broadcasting_before_restore_current_blog( $action )
	{
		if ( ! $this->has_woocommerce() )
			return;

		$bcd = $action->broadcasting_data;

		if ( ! isset( $bcd->woocommerce ) )
			return;

		if ( ! isset( $bcd->woocommerce->variations ) )
			return;

		// Return the current product and variations.
		$product = get_product( $bcd->new_post()->ID );
		if ( $product && $product->is_type( 'variable' ) )
		{
			$children = $product->get_children();
			foreach( $children as $child )
			{
				$this->debug( 'Deleting product child %s.', $child );
				wp_delete_post( $child );
			}
		}

		// An array of old_variation_post_id => new_post_object.
		$variation_equivalents = [];

		// Add the current variations
		foreach( $bcd->woocommerce->variations as $variation )
		{
			// Create the variation broadcasting data.
			$variation_bcd = new \threewp_broadcast\broadcasting_data;
			$variation_bcd->custom_fields = true;
			$variation_bcd->taxonomies = true;
			$variation_bcd->link = false;		// Maybe in a later version this will be feasible. Right now it's easier to just delete the current variations and re-add them.
			$variation_bcd->parent_blog_id = $bcd->parent_blog_id;
			$variation_bcd->parent_post_id = $variation->ID;
			$variation_bcd->post = clone( $variation );
			$variation_bcd->upload_dir = $bcd->upload_dir;

			// Broadcast only to this blog.
			$blog = new \threewp_broadcast\broadcast_data\blog;
			$blog->id = $bcd->current_child_blog_id;
			$variation_bcd->broadcast_to( $blog );

			// The GUID must be nulled.
			unset( $variation_bcd->post->guid );

			// The slug should already be pointing to the new item ID
			$variation_bcd->post->post_name = str_replace( $bcd->post->ID, $bcd->new_post()->ID, $variation_bcd->post->post_name );

			$this->debug( 'Broadcasting variation %s', $variation );

			$this->broadcast()->broadcast_post( $variation_bcd );

			$new_post = (object)$variation_bcd->new_post;

			$variation_equivalents[ $variation->ID ] = $new_post;

			$fixed_stuff = [
			  'ID' => $new_post->ID,
			  'post_title' => str_replace( '#' . $variation->ID, '#' . $variation_bcd->new_post[ 'ID' ], $new_post->post_title ),
			  // Have the new variation's parent point to the new post on this blog.
			  'post_parent' => $bcd->new_post()->ID,
			];
			wp_update_post( $fixed_stuff );
		}

		$this->debug( 'equivalents %s', $variation_equivalents );

		foreach( [
			'_min_price_variation_id',
			'_max_price_variation_id',
			'_min_regular_price_variation_id',
			'_max_regular_price_variation_id',
		] as $key )
		{
			$value = reset( $bcd->post_custom_fields[ $key ] );
			$new_post_id = $variation_equivalents[ $value ];
			$new_post_id = $new_post_id->ID;
			$this->debug( 'Updating %s post ID from %s to %s', $key, $value, $new_post_id );
			update_post_meta( $bcd->new_post()->ID, $key, $new_post_id );
		}

		$transient_name = 'wc_product_children_ids_' . $bcd->new_post()->ID;
		delete_transient( $transient_name );
	}

	/**
		@brief		Save info about the broadcast.
		@param		Broadcast_Data		The BCD object.
		@since		20131117
	**/
	public function threewp_broadcast_broadcasting_started( $action )
	{
		if ( ! $this->has_woocommerce() )
			return;

		$bcd = $action->broadcasting_data;

		// Is this a product being broadcasted?
		$product = get_product( $bcd->post->ID );

		if ( $product === false )
		{
			$this->debug( 'Post is not a product.' );
			return;
		}

		// This is a product. Is it a variation?
		if ( ! $product->is_type( 'variable' ) )
		{
			$this->debug( 'Post is not a variation.' );
			return;
		}

		$variations = $product->get_available_variations();

		if ( count( $variations ) < 1 )
		{
			$this->debug( 'Variation does not have any variations.' );
			return;
		}

		if ( ! isset( $bcd->woocommerce ) )
			$bcd->woocommerce = new \stdClass;

		$bcd->woocommerce->variations = [];
		foreach( $variations as $variation )
		{
			$variation = get_post( $variation[ 'variation_id' ] );
			$bcd->woocommerce->variations []= $variation;
		}
		$this->debug( 'Varations found: %s', $bcd->woocommerce->variations );
	}


	// --------------------------------------------------------------------------------------------
	// ----------------------------------------- Misc functions
	// --------------------------------------------------------------------------------------------

	/**
		@brief		Output some data about WPML support.
		@return		string		HTML string containing Broadcast WPML info.
		@since		20131117
	**/
	public function generate_meta_box_info( $o = [] )
	{
		$this->load_language();

		$wc_text = sprintf( '%sWooCommerce%s', '<a href="http://wordpress.org/plugins/woocommerce/">', '</a>' );

		if ( ! $this->has_woocommerce() )
			return $this->_( '%s was not detected.', $wc_text );

		$r = [];

		$r []= $this->_( '%s v%s detected.',
			$wc_text,
			self::open_close_tag( $GLOBALS['woocommerce']->version, 'em' )
		);

		return \plainview\sdk\base::implode_html( $r, '<div>', '</div>' );
	}

	/**
		@brief		Check for the existence of WooCommerce.
		@return		bool		True if WooCommerce is alive and kicking. Else false.
		@since		20131117
	**/
	public function has_woocommerce()
	{
		return function_exists( 'get_product' );
	}
}

$ThreeWP_Broadcast_WooCommerce = new ThreeWP_Broadcast_WooCommerce;
