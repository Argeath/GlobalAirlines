<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Chat extends ORM {

	protected $_table_name = 'chat';

	protected $_belongs_to = array(
		'user' => array(
			'model' => 'User',
			'foreign_key' => 'user_id',
		));

	public function rules() {
		return array(
			'msg' => array(
				// Uses Valid::not_empty($value);
				array('not_empty'),
			),
			'date' => array(
				// Uses Valid::not_empty($value);
				array('not_empty'),
			),
		);
	}
}