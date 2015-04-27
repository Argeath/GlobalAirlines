<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_Flight extends ORM {
	protected $_table_name = 'flights';
	protected $_belongs_to = array(
		'user' => array(
			'model' => 'User',
			'foreign_key' => 'user_id',
		),
		'UserPlane' => array(
			'model' => 'UserPlane',
			'foreign_key' => 'plane_id',
		),
	);

	public function cancel($when = false) {
		try {
			if (!$when) {
				$when = time();
			}

			$zlecenie = ORM::factory('UserOrder')->where('flight_id', '=', $this->id)->find();
			$plane = $this->UserPlane;

			$costs = $this->costs * 0.75;
			$info = array('type' => Financial::LotZwrot, 'plane_id' => $plane->id, 'order_id' => (($zlecenie->loaded()) ? $zlecenie->id : NULL));
			$this->user->operateCash($costs, 'Zwrot kosztów lotu.', $when, $info);

			if ($zlecenie->loaded()) {
				$zlecenie->flight_id = NULL;
				$zlecenie->save();
			}

			$this->canceled = 1;
			$this->save();
			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '] ' . $e->getMessage());
		}
		return false;
	}

	public function cancelQuietly($when = false) {
		try {
			if (!$when) {
				$when = time();
			}

			$zlecenie = ORM::factory('UserOrder')->where('flight_id', '=', $this->id)->find();
			$plane = $this->UserPlane;

			$costs = $this->costs;
			$this->user->cash += $costs;

			if ($zlecenie->loaded()) {
				$zlecenie->flight_id = NULL;
				$zlecenie->save();
			}

			$this->canceled = 1;
			$this->save();
			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '] ' . $e->getMessage());
		}
		return false;
	}

}