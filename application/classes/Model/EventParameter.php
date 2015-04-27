<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_EventParameter extends ORM
{
	protected $_table_name = 'event_parameters';
	
	protected $_belongs_to = array(
		'event' => array(
			'model' => 'Event', 
			'foreign_key' => 'event_id'
		),
	);
}