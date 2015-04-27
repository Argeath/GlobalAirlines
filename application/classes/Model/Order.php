<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_Order extends ORM {
	protected $_table_name = 'zlecenia';

	protected $_has_one = array(
		'UserOrder' => array(
			'model' => 'UserOrder',
			'foreign_key' => 'order_id',
		),
	);
}