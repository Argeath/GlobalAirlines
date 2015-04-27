<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_Contact extends ORM {
	protected $_table_name = 'contacts';
	protected $_belongs_to = array(
		'user' => array(
			'model' => 'User',
			'foreign_key' => 'user_id',
		),
		'user2' => array(
			'model' => 'User',
			'foreign_key' => 'user2_id',
		),
	);
}