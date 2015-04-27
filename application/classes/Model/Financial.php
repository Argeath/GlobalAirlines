<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_Financial extends ORM {
	protected $_table_name = 'financials';
	protected $_belongs_to = array(
		'user' => array(
			'model' => 'User',
			'foreign_key' => 'user_id',
		),
	);

	public function getInfo() {
		try {
			return json_decode($this->info, true);
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}
}