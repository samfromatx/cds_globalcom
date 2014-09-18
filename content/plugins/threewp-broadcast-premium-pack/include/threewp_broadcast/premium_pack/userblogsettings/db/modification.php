<?php

namespace threewp_broadcast\premium_pack\userblogsettings\db;

use \plainview\sdk\collections\collection;

class modification
	extends \threewp_broadcast\premium_pack\db_object
{
	use \plainview\sdk\wordpress\traits\db_aware_object;

	public $id;
	public $data;

	public static $display_broadcast_properties = [
		'display_broadcast_columns' => true,
		'display_broadcast_menu' => true,
		'display_broadcast_meta_box' => true,
	];

	public function __construct()
	{
		$this->data = new \stdClass;
		foreach( self::$display_broadcast_properties as $property => $value )
			$this->data->$property = $value;
		$this->data->modifications = new collection;
	}

	public function count_modifications()
	{
		$count = 0;
		foreach( $this->data->modifications as $modification => $value )
			if ( $value != '' )
				$count++;
		return $count;
	}

	public function count_display_modifications()
	{
		$count = 0;
		foreach( self::$display_broadcast_properties as $property => $value )
			if ( $this->data->$property != $value )
				$count++;
		return $count;
	}

	public static function db_table()
	{
		global $wpdb;
		return $wpdb->base_prefix. '3wp_broadcast_ubs_modifications';
	}

	public function get_data( $key, $default = null )
	{
		if ( ! isset( $this->data->$key ) )
			return $default;
		return $this->data->$key;
	}

	public static function keys()
	{
		return [
			'id',
			'data',
		];
	}

	public static function keys_to_serialize()
	{
		return [
			'data',
		];
	}

	public function modify_meta_box( $meta_box_data )
	{
		$form = $meta_box_data->form;

		if ( $form->is_posting() && ! $form->has_posted )
				$form->post();

		$form_inputs = $form->inputs();

		foreach( $this->data->modifications as $id => $mod )
		{
			// Go through all of the inputs in the form, look for the same id.
			foreach( $form_inputs as $input )
			{
				if ( self::input_id( $input ) != $id )
					continue;

				$hide = strpos( $mod, '_hide_' ) !== false;
				$on = strpos( $mod, '_on_' ) !== false;
				$off = strpos( $mod, '_off_' ) !== false;
				$readonly = strpos( $mod, '_readonly_' ) !== false;

				if ( get_class( $input ) == 'plainview\\sdk\\form2\\inputs\\select' )
				{
					// We need to figure out what to do with this select.
					// That data is stored in $name_ubs_
					$name = self::input_id( $input );
					$select_ubs_setting = $name . '_ubs_setting';
					$ubs_setting = $this->data->modifications->get( $select_ubs_setting, '' );

					$hide = strpos( $ubs_setting, '_hide_' ) !== false;
					$on = strpos( $ubs_setting, '_on_' ) !== false;
					$off = strpos( $ubs_setting, '_off_' ) !== false;
					$readonly = strpos( $ubs_setting, '_readonly_' ) !== false;

					if ( $hide )
						$input->hidden();

					if ( $on )
					{
						$input->value( $mod );
						$input->set_post_value( $mod );
					}

					if ( $readonly )
					{
						$input->readonly();
						$input->disabled();
					}
				}

				if ( get_class( $input ) == 'plainview\\sdk\\form2\\inputs\\checkbox' )
				{
					if ( $hide )
						$input->hidden();

					if ( $off )
					{
						$input->set_post_value( '' );
						$input->checked( false );
					}

					if ( $on )
					{
						$value = $input->get_value();
						$input->set_post_value( $value );
						$input->checked( true );
					}

					if ( $readonly )
					{
						$input->readonly();
						$input->disabled();
					}
					break;
				}
			}
		}
	}

	public static function input_id( $input )
	{
		return $input->make_id() . '_modification';
	}
}
