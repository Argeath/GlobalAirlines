<?php defined('SYSPATH') or die('No direct script access.');

class Helper_Map {
	static function getContinents() {
		return array('EU', 'AZ', 'AF', 'AN', 'AS', 'AU');
	}

	static function getRegions() {
		$query = GlobalVars::getCities();
		$regions = array();
		foreach ($query as $city) {
			$region = $city->region;
			if (!in_array($region, $regions)) {
				array_push($regions, $region);
			}
		}
		return $regions;
	}

	static function countRegionMaxOrders( $office, $region) {
		//$zlec = DB::select(array('COUNT(*)', 'zlecen'))->from('zlecenia')->where('region', '=', $region)->execute()->get('zlecen');
		if (in_array( $office, array( 1, 2, 5, 6))) {
			$cities = Helper_Map::getRegionCities($region);
		} else {
			$cities = Helper_Map::getRegionCities($region, true);
		}

		$orders = 0;
		$countConfig = Kohana::$config->load('zlecenia.zlecenia');
		$countConfig = $countConfig[ $office];
		foreach ($cities as $city) {

			$count = $countConfig[$city->rozmiar];
			$orders += array_sum($count);
		}
		return $orders;
	}

	static function getRegionCities($region, $onlyBig = false) {
		$citySize = 0;
		if ($onlyBig) {
			$citySize = 1;
		}

		if ($region == 'WR') {
			return ORM::factory("City")->cached()->where('rozmiar', '>=', $citySize)->order_by('id', 'asc')->distinct(true)->find_all();
		}

		$continents = Helper_Map::getContinents();
		if (in_array($region, $continents)) {
			return ORM::factory("City")->cached()->where('region', 'LIKE', '%-' . $region . '%')->and_where('rozmiar', '>=', $citySize)->order_by('id', 'asc')->distinct(true)->find_all();
		}
		return ORM::factory("City")->cached()->where('region', '=', $region)->and_where('rozmiar', '>=', $citySize)->order_by('id', 'asc')->distinct(true)->find_all();
	}

	static function getAllRegionCities() {
		$arr = ['regions' => [], 'continents' => [], 'worldSmall' => [], 'worldBig' => []];
		$regions = Helper_Map::getRegions();
		foreach ($regions as $r) {
			$cities = Helper_Map::getRegionCities($r, false);
			$arr['regions'][$r] = $cities;
		}
		$regions = Helper_Map::getContinents();
		foreach ($regions as $r) {
			$cities = Helper_Map::getRegionCities($r, false);
			$arr['continents'][$r] = $cities;
		}
		$cities = Helper_Map::getRegionCities('WR', false);
		$arr['worldSmall'] = $cities;

		$cities = Helper_Map::getRegionCities('WR', true);
		$arr['worldBig'] = $cities;

		return $arr;
	}

	static function getBigCities() {
		return Helper_Map::getRegionCities('WR', true);
	}
	/*
	//
	//	Dodawanie pustych dystansów dla nowych miast
	//
	function prepareDistances()
	{
	$query = DB::select()->from('cities')->execute()->as_array();

	foreach($query as $city)
	{
	foreach($query as $city2)
	{
	if($city == $city2)
	continue;
	$q = DB::select()->from('distances')->where('from', '=', $city['id'])->and_where('to', '=', $city2['id'])->execute()->as_array();
	$q2 = DB::select()->from('distances')->where('from', '=', $city2['id'])->and_where('to', '=', $city['id'])->execute()->as_array();
	if(empty($q) && empty($q2))
	{
	DB::insert('distances', array('from', 'fromName', 'to', 'toName'))
	->values(array($city['id'], $city['name'], $city2['id'], $city2['name']))
	->execute();
	}
	}
	}
	}*/

	static function getCityName($city) {
		return ORM::factory("City", $city)->cached()->get("name");
	}

	static function getCityCode($city) {
		return ORM::factory("City", $city)->cached()->get("code");
	}

	static function getDistanceBetween($city1, $city2) {
		try {
			if (!is_object($city1)) {
                /** @var Model_City $city1 */
				$city1 = ORM::factory("City", $city1);
			}

			return $city1->countDistanceTo($city2);
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return 0;
	}

	static function findCityOnPath($city1, $city2, $dist) {
		try {
			if (!is_object($city1)) {
				/** @var Model_City $city1 */
				$city1 = ORM::factory("City", $city1);
			}

			if (!is_object($city2)) {
				/** @var Model_City $city2 */
				$city2 = ORM::factory("City", $city2);
			}

			$distance = $city1->countDistanceTo($city2);
			$cities = GlobalVars::getCities();
			$city1Distances = $city1->getCitiesInRange($cities, $dist - 100, $dist + 100);

			if (empty($city1Distances)) {
				return false;
			}

			$closestDist = 999999;
			$closest = null;

			/**
			 * @var int $distanceToCity
			 * @var Model_City $city
			 */
			foreach ($city1Distances as $distanceToCity => $city) {
				$cDist = $city->countDistanceTo($city2);
				if (($distanceToCity + $cDist - 50) <= $distance && $cDist <= $closestDist) {
					$closest = $city;
					$closestDist = $cDist;
				}
			}

			return $closest;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	static function findPath($city1, $city2) {
		try {
			if (!is_object($city1)) {
				/** @var Model_City $city1 */
				$city1 = ORM::factory("City", $city1);
			}

			if (!is_object($city2)) {
				/** @var Model_City $city2 */
				$city2 = ORM::factory("City", $city2);
			}

			$distance = $city1->countDistanceTo($city2);
			if ($distance <= 200) {
				return false;
			}

			$hundredsCount = round($distance / 100) - 1;
			$path = array();
			for ($i = 1; $i <= $hundredsCount; $i++) {
				$path[$i] = Helper_Map::findCityOnPath($city1, $city2, $i * 100);
			}

			return $path;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}
};