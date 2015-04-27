<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_Office extends ORM {
	protected $_table_name = 'offices';
	protected $_belongs_to = array(
		'user' => array(
			'model' => 'User',
			'foreign_key' => 'user_id',
		),
		'city' => array(
			'model' => 'City',
			'foreign_key' => 'city_id',
		),
	);

	protected $_has_many = array(
		'checkins' => array(
			'model' => 'Checkin',
			'foreign_key' => 'office_id',
		),
	);

	public function rules() {
		return array(
			'city_id' => array(
				// Uses Valid::not_empty($value);
				array('not_empty'),
			),
		);
	}

	public function getName() {
		return $this->city->name;
	}

	public function isHome() {
		return $this->id == $this->user->base_id;
	}

	public function getCountry() {
		return $this->city->getCountry();
	}

	public function getFlag() {
		return $this->city->getFlag();
	}
}