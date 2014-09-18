<?php

namespace threewp_broadcast\premium_pack\local_links\meta_box_data;

/**
	@brief		Meta box data item for Local Links.
	@since		20131027
**/
class item
extends \threewp_broadcast\meta_box\item
{
	public $inputs;

	public function _construct()
	{
		$form = $this->data->form;

		// Add the "update local links" checkbox
		$this->inputs->set( 'local_links', $form->checkbox( 'local_links' )
			->checked( isset( $this->data->last_used_settings[ 'local_links' ] ) )
			->label_( 'Update local links' )
			->title_( 'Update each link to local, broadcasted posts in each child post.' )
		);
	}
}
