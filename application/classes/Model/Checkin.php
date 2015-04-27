<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_Checkin extends ORM {
	protected $_table_name = 'checkins';
	protected $_has_many = array(
		'activities' => array(
			'model' => 'CheckinActivity',
			'foreign_key' => 'checkin_id',
		),
	);
	protected $_belongs_to = array(
		'user' => array(
			'model' => 'User',
			'foreign_key' => 'user_id',
		),
		'office' => array(
			'model' => 'Office',
			'foreign_key' => 'office_id',
		),
		'city' => array(
			'model' => 'City',
			'foreign_key' => 'city_id',
		),
	);

	public function reserve($user, $plane, $time, $from = false) {
		try {
			if (!$user->loaded() || !$plane->loaded()) {
				return false;
			}

			if (!$from || $from < time()) {
				$from = time();
			}

			$start = $this->findPlaceInQueue($time, $from);
			$airportConfig = Kohana::$config->load('airport');
			$cost = $this->getCost($user, $time);

			$this->cash += $cost;
			$this->save();

			$activity = ORM::Factory("CheckinActivity");
			$activity->user_id = $user->id;
			$activity->checkin_id = $this->id;
			$activity->plane_id = $plane->id;
			$activity->from = $start;
			$activity->to = $start + $time;
			$activity->cost = $cost;
			$activity->save();

			return $activity;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function findPlaceInQueue($time, $from) {
		try {
			$airportConfig = Kohana::$config->load('airport');
			$activities = $this->activities->where('to', '>=', $from)->find_all();
			if ($activities->count() == 0) {
				return $this->checkReservations($from);
			}

			$lastTime = $from;
			foreach ($activities as $act) {
				if ($lastTime + $time < $act->from) {
					return $this->checkReservations($lastTime);
				}

				$lastTime = $act->to + 1;
			}
			return $this->checkReservations($lastTime);
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function getCost($user, $time) {
		if ($this->user_id == $user->id) {
			return 0;
		}

		return ceil($time / 60) * $this->cost;
	}

	public function checkReservations($from) {
		return ($this->reservations >= ($from - time())) ? $from : false;
	}
}