<?php

namespace threewp_broadcast\premium_pack\protect_child_attachments;

class item
	extends \threewp_broadcast\meta_box\item
{
	public $inputs;

	public function _construct()
	{
		$form = $this->data->form;

		$this->inputs->set( 'protect_child_attachments', $form->checkbox( 'protect_child_attachments' )
			->checked( isset( $this->data->last_used_settings[ 'protect_child_attachments' ] ) )
			->label_( 'Protect child attachments' )
			->title_( 'Protect the attachments of each linked child post instead of deleting them as per default.' )
		);
	}
}
