<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Generate extends Controller {

	public $startTime;

	public $cities;

	public $maxOrdersConfig;
	public $countsConfig;
	public $classesConfig;
	public $classesRangeConfig;
	public $classesConfigKeys;
	public $classesAveragesConfig;

	public $statsCount;

	public $regionCities;

	public function action_index() {
		//$benchmark = Profiler::start('GENERATE', __FUNCTION__);
		try {
			$this->startTime = $_SERVER['REQUEST_TIME'];

			$this->cities = GlobalVars::getCities();
			$citiesCount = $this->cities->count();
			$setting = ORM::Factory("Setting")->where('key', '=', 'last_city')->find();

			$this->maxOrdersConfig = Kohana::$config->load('maxorders');
			$this->countsConfig = Kohana::$config->load('zlecenia.zlecenia');
			$this->classesConfig = (array) Kohana::$config->load('classes');
			$this->classesRangeConfig = (array) Kohana::$config->load('classes-zasieg');
			$this->classesAveragesConfig = (array) Kohana::$config->load('classesAverages.classes');
			$this->classesConfigKeys = array_keys($this->classesConfig);
			$this->statsCount = 0;

			$this->regionCities = Helper_Map::getAllRegionCities();

			$start = $setting->value;

			$cities = $this->cities->as_array();
			DB::delete('zlecenia')->where('deadline', '<=', $_SERVER['REQUEST_TIME'] + 57600)->and_where('taken', '=', 0)->execute();
			while (1) {
				if (time() - 10 > $this->startTime) {
					break;
				}

				$city = $cities[$setting->value - 1];
				if (!$city->loaded()) {
					$setting->value++;
					continue;
				}

				$this->generate($city);

				$setting->value++;
				if ($setting->value <= 0 || $setting->value >= $citiesCount) {
					$setting->value = 1;
				}

				if ($setting->value == $start) {
					break;
				}

				unset($city);
			}
			$setting->save();
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
			return false;
		}
		//Profiler::stop($benchmark);
		//echo View::factory('profiler/stats');
	}

	public function generate($city) {
		try {
			$arrvals = array();
			$ilArr = null;
			foreach ($this->countsConfig as $biuro => $classes) {
				if (empty($classes)) {
					continue;
				}

				switch ($biuro) {
					case 1:
					case 2:
						$region = $city->region;
						break;
					case 3:
					case 4:
					case 5:
					case 6:
						$region = $city->getContinent();
						break;
					case 7:
					case 8:
					case 9:
					case 10:
					case 11:
					case 12:
						$region = 'WR';
						break;
					default:
						errToDb('[ERROR][' . __CLASS__ . '][' . __FUNCTION__ . '][WRONG BIURO: ' . $biuro . ']');
				}
				$onlyBig = false;
				if ($biuro == 9 || $biuro == 10 || $biuro == 11 || $biuro == 12) {
					$onlyBig = true;
					if ($city->rozmiar == 0) {
						continue;
					}
				}

				if ($biuro <= 2) {
					$cities = $this->regionCities['regions'][$region];
				} elseif ($biuro <= 6) {
					$cities = $this->regionCities['continents'][$region];
				} elseif ($biuro <= 8) {
					$cities = $this->regionCities['worldSmall'];
				} else {
					$cities = $this->regionCities['worldBig'];
				}

				$counts = $classes[$city->rozmiar];
				foreach ($counts as $class => $count) {
					if ($count == 0) {
						continue;
					}

					if ($ilArr == null) {
						$ilArr = DB::select(array(DB::expr('COUNT(`id`)'), 'total_orders'), 'biuro')->from('zlecenia')->where('from', '=', $city->id)->and_where('taken', '=', 0)->and_where('deadline', '>', $this->startTime)->group_by('biuro')->execute()->as_array('biuro', 'total_orders');
					}

					//$il = DB::select(array(DB::expr('COUNT(`id`)'), 'total_orders'))->from('zlecenia')->where('from', '=', $city->id)->and_where('region', '=', $region)->and_where('biuro', '=', $biuro)->and_where('taken', '=', 0)->and_where('deadline', '>', $this->startTime)->execute()->get('total_orders', 0);
					if (isset($ilArr[$biuro])) {
						$il = $ilArr[$biuro];
					} else {
						$il = 0;
					}

					if ($il >= $count) {
						continue;
					}

					$className = $this->classesConfigKeys[$class];
					$classRange = $this->classesRangeConfig[$className];

					$possibleCities = $city->getCitiesInRange($cities, $classRange[0], $classRange[1]);
					$possibleCitiesCount = count($possibleCities) - 1;
					if ($possibleCitiesCount < 1) {
						continue;
					}

					$possibleCitiesKeys = array_keys($possibleCities);

					$averages = $this->classesAveragesConfig[$this->classesConfigKeys[$class]];
					$R = $averages['averageRange'];
					$M = $averages['averageSeats'];

					$missing = $count - $il;
					$i = 1;
					while ($i <= $missing) {
						$toId = mt_rand(0, $possibleCitiesCount);
						$dystans = $possibleCitiesKeys[$toId];
						$to = $possibleCities[$possibleCitiesKeys[$toId]];
						if (!$to->loaded()) {
							errToDB('[Zlecenia][Biuro: ' . $biuro . '][Region: ' . $region . '][CitiesCount: ' . $possibleCitiesCount . ']');
							continue;
						}
						if ($city->id == $to->id) {
							continue;
						}

						$miejsc = mt_rand($this->classesConfig[$this->classesConfigKeys[$class]][0], $this->classesConfig[$this->classesConfigKeys[$class]][1]);
						$P = $miejsc;
						$D = $dystans;
						if ($biuro == 1) {
							$kasa = ((pow(((($D - $R) / 2) + $R) / ($R / 100), 2) * pow(((($P - $M) / 2) + $M) / 3, 2)) + $D) * 0.8;
						} elseif ($biuro == 2) {
							$kasa = ((pow(((($D - $R) / 2) + $R) / ($R / 100), 2) * pow(((($P - $M) / 2) + $M) / 3, 2)) + ($D * 10)) * 0.25;
						} elseif ($biuro == 3 || ($biuro == 4 && $class == 2))//$K = 3 Przewozowe
						{
							$kasa = ((pow(((($D - $R) / 2) + $R) / ($R / 100), 2) * pow(((($P - $M) / 2) + $M) / 3, 2)) + ($D * 4)) * 0.1;
						} elseif ($biuro == 4 || $biuro == 5)//$K = 4 Turystyczne
						{
							$kasa = ((pow(((($D - $R) / 2) + $R) / ($R / 100), 2) * pow(((($P - $M) / 2) + $M) / 3, 2)) + ($D * 2)) * 0.027;
						} elseif ($biuro == 6 || ($biuro == 7 && $class == 4))// $K = 5 Regionalne
						{
							$kasa = ((pow(((($D - $R) / 2) + $R) / ($R / 100), 2) * pow(((($P - $M) / 2) + $M) / 3, 2)) + ($D * 2)) * 0.013;
						} elseif ($biuro == 7 || ($biuro == 8 && $class == 5))// Wąskokadłubowe
						{
							$kasa = ((pow(((($D - $R) / 2) + $R) / ($R / 100), 2) * pow(((($P - $M) / 2) + $M) / 3, 2)) + ($D * 2)) * 0.012;
						} elseif ($biuro == 8) {
							$kasa = ((pow(((($D - $R) / 2) + $R) / ($R / 100), 2) * pow(((($P - $M) / 2) + $M) / 3, 2)) + ($D * 2)) * 0.012;
						} elseif ($biuro == 9) {
							$kasa = ((pow(((($D - $R) / 2) + $R) / ($R / 100), 2) * pow(((($P - $M) / 2) + $M) / 3, 2)) + ($D * 2)) * 0.009;
						} elseif ($biuro == 10) {
							$kasa = ((pow(((($D - $R) / 2) + $R) / ($R / 100), 2) * pow(((($P - $M) / 2) + $M) / 3, 2)) + ($D * 2)) * 0.008;
						} elseif ($biuro == 11) {
							$kasa = pow(((($D - $R) / 2) + $R) / ($R / 100), 2) * pow(((($P - $M) / 2) + $M) / 2, 2);
						} elseif ($biuro == 12) {
							$kasa = pow(((($D - $R) / 2) + $R) / ($R / 100), 2) * pow(((($P - $M) / 4) + $M) / 2, 2);
						} else {
							$kasa = (($miejsc * $dystans) + ($miejsc * mt_rand(300, 350) * ceil($dystans / 1000)));
						}

						$kasa += mt_rand(1, 500);
						$arrvals[] = "('" . $region . "', " . $biuro . ", " . $city->id . ", " . $to->id . ", " . round($kasa) . ", " . $miejsc . ",  " . ($_SERVER['REQUEST_TIME'] + mt_rand(172800, 345600)) . ")";
						$i++;
					}
				}
			}
			$vals = implode(',', $arrvals);
			if (!empty($vals)) {
				DB::query(Database::INSERT, 'INSERT INTO `zlecenia` (`region`, `biuro`, `from`, `to`, `cash`, `count`, `deadline`) VALUES ' . $vals . '')->execute();
			}

			$this->statsCount += count($arrvals);

		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
			return false;
		}
		return true;
	}

	public function generateClassesConstants() {
		echo '"classes" => array(<br />';
		foreach ($this->classesConfigKeys as $kl => $N) {
			$K = $kl + 1;
			$averageRangeSum = 0;
			$averageMiejscSum = 0;
			$planes = ORM::factory("Plane")->where('klasa', '=', $K)->and_where('ukryty', '=', 0)->find_all();
			foreach ($planes as $p) {
				$averageRangeSum += $p->zasieg;
				$averageMiejscSum += $p->miejsc;
			}
			$R = round($averageRangeSum / count($planes));
			$M = round($averageMiejscSum / count($planes));
			echo '"' . $N . '" => array("averageRange" => ' . $R . ', "averageSeats" => ' . $M . '),<br />';
		}
		echo ')';
	}

	/*public function getTotalOrders() {
$arr = [];
// Pobranie wszystkich zleceń zabije serwer ;C
$ord = DB::select()->from('zlecenia')->where('taken', '=', 0)->and_where('deadline', '>', $this->startTime)->order_by('biuro', 'ASC')->order_by('region', 'ASC')->execute();
foreach ($this->countsConfig as $biuro => $classes) {
foreach ($ord as $o) {
if ($o['biuro'] == $biuro) {
$regions = Map::getRegions();
$arr[$biuro] = [];
foreach ($regions as $r) {
if ($o['region'] == $r) {
$arr[$biuro][$r] = $o;
break;
}
}
$regions = Map::getContinents();
foreach ($regions as $r) {
if ($o['region'] == $r) {
$arr[$biuro][$r] = $o;
break;
}
}
if ($o['region'] == 'WR') {
$arr[$biuro]['WR'] = $o;
}
}
}
}
return $arr;
}*/
};