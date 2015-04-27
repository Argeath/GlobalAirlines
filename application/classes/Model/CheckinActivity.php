<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_CheckinActivity extends ORM {
	protected $_table_name = 'checkins_activity';

	protected $_belongs_to = array(
		'user' => array(
			'model' => 'User',
			'foreign_key' => 'user_id',
		),
		'checkin' => array(
			'model' => 'Checkin',
			'foreign_key' => 'checkin_id',
		),
		'UserPlane' => array(
			'model' => 'UserPlane',
			'foreign_key' => 'plane_id',
		),
	);

	public function rereserve($from = false) {
		if (!$from) {
			$from = time();
		}

		$time = $this->end - $this->start;
		$firstTime = $this->checkin->findPlaceInQueue($time, $from);
		if ($firstTime >= $from + 3600) {
			return false;
		}

		$this->checkin->reserve($this->user, $this->UserPlane, $time, $from);
		return true;
	}

}