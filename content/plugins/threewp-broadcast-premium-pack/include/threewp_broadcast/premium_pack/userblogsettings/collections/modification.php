<?php

namespace threewp_broadcast\premium_pack\userblogsettings\collections;

class modification
extends \plainview\sdk\collections\collection
{
	/**
		@brief		Do all of the modifications allow display of columns?
		@since		20131015
	**/
	public function display_broadcast_columns()
	{
		foreach( $this->items as $item )
			if ( ! $item->data->display_broadcast_columns )
				return false;
		return true;
	}

	/**
		@brief		Do all of the modifications allow display of the menu?
		@since		20131015
	**/
	public function display_broadcast_menu()
	{
		foreach( $this->items as $item )
			if ( ! $item->data->display_broadcast_menu )
				return false;
		return true;
	}

	/**
		@brief		Do all of the modifications allow display of the meta box?
		@since		20131015
	**/
	public function display_broadcast_meta_box()
	{
		foreach( $this->items as $item )
			if ( ! $item->data->display_broadcast_meta_box )
				return false;
		return true;
	}

	/**
		@brief		Sort the items by the name in the data property.
		@since		20131015
	**/
	public function sort_by_name()
	{
		$this->sortBy( function( $item )
		{
			return $item->data->name;
		});
	}
}
