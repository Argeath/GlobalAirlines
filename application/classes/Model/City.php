<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_City extends ORM {
	protected $_table_name = 'cities';

	protected $_has_many = array(
		'checkins' => array(
			'model' => 'Checkin',
			'foreign_key' => 'city_id',
		),
		'offices' => array(
			'model' => 'Office',
			'foreign_key' => 'city_id',
		),
	);

	public function getDistanceTo($second) {
		try {
			if (is_object($second)) {
				$second = $second->id;
			}

			if ($this->id == $second) {
				return false;
			}

			if (is_array(DB::select('distance')->from('distances')->cached())) {
				$q = DB::select('distance')->from('distances')->cached()->where_open()->where('from', '=', $this->id)->and_where('to', '=', $second)->where_close()->or_where_open()->where('from', '=', $second)->and_where('to', '=', $this->id)->or_where_close()->execute()->get('distance');
			} else {
				$q = DB::select('distance')->from('distances')->where_open()->where('from', '=', $this->id)->and_where('to', '=', $second)->where_close()->or_where_open()->where('from', '=', $second)->and_where('to', '=', $this->id)->or_where_close()->execute()->get('distance');
			}

			if (empty($q)) {
				return false;
			}

			return $q;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return 0;
	}

	public function countDistanceTo($second) {
		try {
			if (!is_object($second)) {
				$second = ORM::factory("City", $second);
			}

			if (!$second->loaded() || $this->id == $second->id) {
				return false;
			}

			$d = Helper_Distance::haversineGreatCircleDistance($this->coordX, $this->coordY, $second->coordX, $second->coordY);
			return round($d / 1000);
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return 0;
	}

	public function getDistances() {
		try {
			try {
				$q = DB::select()->from('distances')->cached(3600)->where('from', '=', $this->id)->or_where('to', '=', $this->id)->execute()->as_array();
			} catch(Exception $e) {
				$q = DB::select()->from('distances')->where('from', '=', $this->id)->or_where('to', '=', $this->id)->execute()->as_array();
			}
			$distances = array();
			foreach ($q as $d) {
				$id = ($this->id == $d['from']) ? $d['to'] : $d['from'];
				$distances[$id] = $d['distance'];
			}
			return $distances;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function checkDistances() {
		try {
			$i = 0;
			$arrvals = array();
			$cities = ORM::factory("City")->where('id', '!=', $this->id)->find_all();
			foreach ($cities as $c) {
				if ($this->getDistanceTo($c)) {
					continue;
				}

				$d = $this->countDistanceTo($c);
				$arrvals[] = "(" . $this->id . ", " . $c->id . ", " . $d . ")";
				$i++;
			}
			$vals = implode(',', $arrvals);
			if (!empty($vals)) {
				DB::query(Database::INSERT, 'INSERT INTO `distances` (`from`, `to`, `distance`) VALUES ' . $vals . '')->execute();
			}

			return $i;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function getName() {
		return $this->name;
	}

	public function getDepartures() {
		try {
			return ORM::factory("Flight")->where('from', '=', $this->id)->where('started', '>', time())->and_where('checked', '=', 0)->and_where('canceled', '=', 0)->find_all();
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function getCountry() {
		try {
			return substr($this->region, 0, 2);
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function getContinent() {
		try {
			return substr($this->region, 3, 2);
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function getFlag() {
		try {
			$country = strtolower($this->getCountry());
			return "<div class=\"flag flag-" . $country . "\"></div>";
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return '';
	}

	static public function cmpCities($a, $b) {
		if ($a->region == $b->region) {
			return 0;
		}
		return strcmp($a->region, $b->region);
	}

	public function getCitiesInRange($array, $from = 1, $to = 30000) {
		try {
			$miasta = array();
			$distances = $this->getDistances();
			foreach ($array as $c) {
				if (isset($distances[$c->id])) {
					$dystans = $distances[$c->id];
				} else {
					$dystans = $this->countDistanceTo($c);
				}
				if ($dystans >= $from && $dystans <= $to && $dystans > 1) {
					while (isset($miasta[$dystans])) {
						$dystans++;
					}

					$miasta[$dystans] = $c;
				}
			}
			ksort($miasta);
			return $miasta;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}
}