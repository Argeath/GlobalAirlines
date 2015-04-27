<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_Accident extends ORM {
	protected $_table_name = 'accidents';
	protected $_belongs_to = array(
		'user' => array(
			'model' => 'User',
			'foreign_key' => 'user_id',
		),
		'plane' => array(
			'model' => 'UserPlane',
			'foreign_key' => 'plane_id',
		),
		'flight' => array(
			'model' => 'Flight',
			'foreign_key' => 'flight_id',
		),
	);

	public function getAccidentInfo() {
		try {
			$config = Kohana::$config->load("accidents.accidents");
			return $config[$this->type];
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function getEffectInfo() {
		try {
			$config = Kohana::$config->load("accidents.effects");
			return $config[$this->effect];
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}
}