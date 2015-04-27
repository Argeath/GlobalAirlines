<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_Plane extends ORM {
	protected $_table_name = 'planes';

	public function fullName() {
		return $this->producent . ' ' . $this->model;
	}

	public function getPreferStaffExp() {
		try {
			$miejsc = $this->miejsc;
			$config = Kohana::$config->load('staffAccident');
			$closest = findClosestValue($config, $miejsc);
			return $closest;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return 0;
	}

	public function getPreferStaffCost() {
		try {
			$pilotow = $this->piloci;
			$stewardess = $this->zaloga_dodatkowa;
			$xp = $this->getPreferStaffExp();
			$kosztP = $pilotow * (50 + $xp * 5);
			$kosztS = $stewardess * (30 + $xp * 2);
			return $kosztP + $kosztS;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return 0;
	}

	public function getOffices() {
		$offices = [];
		$ilosciConfig = Kohana::$config->load('zlecenia.zlecenia');

		foreach ($ilosciConfig as $n => $city) {
			foreach ($city as $k => $v) {
				if ($v[$this->klasa - 1] > 0) {
					$offices[] = $n;
				}
			}
		}
		array_unique($offices, SORT_NUMERIC);

		return $offices;
	}
}