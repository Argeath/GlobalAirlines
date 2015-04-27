<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_Event extends ORM {
	protected $_table_name = 'events';

	protected $_belongs_to = array(
		'user' => array(
			'model' => 'User',
			'foreign_key' => 'user_id',
		),
	);

	protected $_has_many = array(
		'parameters' => array(
			'model' => 'EventParameter',
			'foreign_key' => 'event_id',
		),
	);
}