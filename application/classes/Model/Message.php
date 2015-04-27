<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_Message extends ORM {
	protected $_table_name = 'messages';
	protected $_belongs_to = array(
		'user' => array(
			'model' => 'User',
			'foreign_key' => 'user_id',
		),
		'sender' => array(
			'model' => 'User',
			'foreign_key' => 'sender_id',
		),
	);

	/*
public function rules()
{
return array(
'title' => array(
// Uses Valid::not_empty($value);
array('not_empty')
),
'message' => array(
// Uses Valid::not_empty($value);
array('not_empty')
),
'typ' => array(
// Uses Valid::not_empty($value);
array('not_empty')
),
'sender' => array(
// Uses Valid::not_empty($value);
array('not_empty')
),
);
}*/

}