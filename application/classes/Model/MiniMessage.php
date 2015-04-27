<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_MiniMessage extends ORM {
	protected $_table_name = 'mini_messages';
	protected $_belongs_to = array(
		'user' => array(
			'model' => 'User',
			'foreign_key' => 'user_id',
		),
	);
}