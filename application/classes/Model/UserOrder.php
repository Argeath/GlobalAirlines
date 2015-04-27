<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_UserOrder extends ORM {
	protected $_table_name = 'zlecenia_taken';
	protected $_belongs_to = array(
		'user' => array(
			'model' => 'User',
			'foreign_key' => 'user_id',
		),
		'flight' => array(
			'model' => 'Flight',
			'foreign_key' => 'flight_id',
		),
		'order' => array(
			'model' => 'Order',
			'foreign_key' => 'order_id',
		),
	);

}