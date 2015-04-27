<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_UserPlane extends ORM {
	protected $_table_name = 'user_planes';
	protected $_belongs_to = array(
		'user' => array(
			'model' => 'User',
			'foreign_key' => 'user_id',
		),
		'plane' => array(
			'model' => 'Plane',
			'foreign_key' => 'plane_id',
		),
		'city' => array(
			'model' => 'City',
			'foreign_key' => 'position',
		),
	);

	protected $_has_many = array(
		'staff' => array(
			'model' => 'Staff',
			'foreign_key' => 'plane_id',
		),
		'flights' => array(
			'model' => 'Flight',
			'foreign_key' => 'plane_id',
		),
		'auctions' => array(
			'model' => 'Auction',
			'foreign_key' => 'plane_id',
		),
	);

	public function rules() {
		return array(
			'rejestracja' => array(
				array('min_length', array(':value', 2)),
				array('max_length', array(':value', 8)),
				array('regex', array(':value', '/^[a-zA-Z0-9_.-]++$/iD')),
			),
		);
	}

	public function getNextFlight($when = false) {
		if (!$when) {
			$when = time();
		}

		$flight = $this->flights->where('started', '>', $when)->where('canceled', '=', 0)->order_by('started', 'ASC')->limit(1)->find();
		return $flight;
	}

	public function getNextFlights($when = false) {
		if (!$when) {
			$when = time();
		}

		$flights = $this->flights->where('started', '>', $when)->where('canceled', '=', 0)->order_by('started', 'ASC')->find_all();
		return $flights;
	}

	public function countSamePlanes() {
		$user = $this->user;
		$planes = $user->UserPlanes->find_all();

		$c = 0;

		foreach ($planes as $p) {
			if ($p->plane->producent == $this->plane->producent && $p->plane->model == $this->plane->model) {
				$c++;
			}
		}

		return $c;
	}

	public function getPreferStaffExpLevel() {
		try {
			$closest = $this->getPreferStaffExp();
			if ($closest < 6) {
				return 0;
			} elseif ($closest < 17) {
				return 1;
			} elseif ($closest < 32) {
				return 2;
			} else {
				return 3;
			}

		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return 0;
	}

	public function getPreferStaffExp() {
		try {
			$model = $this->getUpgradedModel();
			$miejsc = $model->miejsc;
			$config = Kohana::$config->load('staffAccident');
			$closest = findClosestValue($config, $miejsc);
			return $closest;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return 0;
	}

	public function doAccident($flight) {
		try {
			$accidentsConfig = Kohana::$config->load("accidents.accidents");
			$effectsConfig = Kohana::$config->load("accidents.effects");

			$possibleAccidents = array();

			foreach ($accidentsConfig as $id => $accident) {
				if ($id == 0) {
					continue;
				}

				if ($accident['condition'] >= $this->stan) {
					$possibleAccidents[$id] = $accident;
				}
			}

			$possibleAccidentsKeys = array_keys($possibleAccidents);
			$effectsKeys = array_keys($effectsConfig);

			$possibleAccidentsCount = count($possibleAccidents);
			$effectsCount = count($effectsConfig);

			$accidentRand = rand(0, $possibleAccidentsCount - 1);
			$accidentInfo = $possibleAccidents[$possibleAccidentsKeys[$accidentRand]];

			$effectRand = rand(0, $effectsCount - 1);
			$effectInfo = $effectsConfig[$effectsKeys[$effectRand]];

			$time = 0;
			$flightTime = $flight->end - $flight->started;
			if ($accidentInfo['when'] == 0)// Przy starcie
			{
				$time = $flight->started;
			} elseif ($accidentInfo['when'] == 1)// Chwile po starcie
			{
				$time = $flight->started + rand($flightTime * 0.05, $flightTime * 0.1);
			} elseif ($accidentInfo['when'] == 2)// Chwile po starcie
			{
				$time = $flight->started + rand($flightTime * 0.3, $flightTime * 0.9);
			}

			$effectDelay = rand($effectInfo['minDelay'] * 60, $effectInfo['maxDelay'] * 60);
			$effectCondition = rand($effectInfo['minCondition'], $effectInfo['maxCondition']);

			$accident = ORM::factory("Accident");
			$accident->user_id = $this->user_id;
			$accident->plane_id = $this->id;
			$accident->flight_id = $flight->id;
			$accident->type = $possibleAccidentsKeys[$accidentRand];
			$accident->effect = $effectsKeys[$effectRand];
			$accident->time = $time;
			$accident->delay = $effectDelay;
			$accident->condition = $effectCondition;
			$accident->save();

			$event = ORM::factory("Event");
			$event->user_id = $this->user_id;
			$event->type = 14;
			$event->when = $time;
			$event->save();

			$params = array();
			$params[] = '(' . $event->id . ', "accident", ' . $accident->id . ')';
			Events::insertEventParams($params);

			return $accident;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;

	}

	public function getAccidentChance() {
		try {
			$staff = $this->staff->where('type', '=', 'pilot')->find_all();
			$chance = 0;
			foreach ($staff as $k => $s) {
				$chance += $s->getAccidentChance();
			}

			$stan = 100 - $this->stan;
			$chance += $stan / 3;
			if ($chance < 0) {
				$chance = 0;
			}

			return round($chance, 2);
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return 0;
	}

	public function getOilBonus() {
		try {
			$staff = $this->staff->where('type', '=', 'pilot')->find_all();
			$bonus = 0;
			foreach ($staff as $k => $s) {
				$bonus += $s->getOilBonus();
			}

			$bonus /= count($staff);
			$bonus /= 100;
			$bonusIlosc = count($staff) * 0.2 + 1;
			$bonus *= $bonusIlosc;
			return round($bonus, 2);
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return 0;
	}

	public function drawAccidentChanceBar() {
		try {
			$chance = $this->getAccidentChance();
			$width = round($chance);
			return '<div class="well" style="padding: 0; margin: 0;">Awaryjność: ' . $chance . '%<br /><div class="graph-0"><div style="width: ' . $width * 2 . 'px;"></div></div></div>';
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return "";
	}

	public function fullName($rejestracja = true) {
		try {
			$model = $this->plane;
			return (($rejestracja) ? $this->rejestracja . " " : "") . '(' . $model->producent . ' ' . $model->model . ')';
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return "";
	}

	public function getUpgradesCount() {
		try {
			$model = clone $this->plane;
			$config = (array) Kohana::$config->load('upgrades');
			$configUpgrades = $config['upgrades'];
			$upgrades = json_decode($model->upgrades, true);
			$ilosc = 0;

			if (empty($upgrades)) {
				return 0;
			}

			foreach ($upgrades as $c => $a) {
				foreach ($a as $e => $b) {
					$ilosc += 5;
				}
			}
			return $ilosc;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return 0;
	}

	public function getUpgradedCount() {
		try {
			$model = clone $this->plane;
			$config = (array) Kohana::$config->load('upgrades');
			$configUpgrades = $config['upgrades'];
			$upgrades = json_decode($model->upgrades, true);
			$upgraded = json_decode($this->upgrades, true);
			$ilosc = 0;

			if (empty($upgrades) || empty($upgraded)) {
				return 0;
			}

			foreach ($upgraded as $c => $a) {
				foreach ($a as $e => $b) {
					$ilosc += $b;
				}
			}
			return $ilosc;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return 0;
	}

	public function getUpgradedModel() {
		try {
			$model = clone $this->plane;
			$cena = $model->cena;
			$config = (array) Kohana::$config->load('upgrades');
			$configUpgrades = $config['upgrades'];
			$upgrades = json_decode($model->upgrades, true);
			$upgraded = json_decode($this->upgrades, true);

			if (empty($upgrades)) {
				return $this->plane;
			}

			if (!isset($upgraded) || empty($upgraded)) {
				$upgraded = array();
				foreach ($upgrades as $c => $a) {
					$upgraded[$c] = array();
					foreach ($a as $e => $b) {
						if ($b) {
							$upgraded[$c][$e] = 0;
						}
					}
				}

				$this->upgrades = json_encode($upgraded);
				$this->save();
				return $model;
			}
			$ilosciElem = array();
			$arrayElem = array();
			foreach ($upgrades as $c => $a) {
				$arrayElem[$c] = array();
				foreach ($a as $e => $b) {
					if ($b) {
						$arrayElem[$c][$e] = $configUpgrades[$c][$e];
					}
				}
			}

			$wszystkich = 0;
			$ilosciEfekt = array();
			foreach ($arrayElem as $c => $a) {
				foreach ($a as $e => $b) {
					foreach ($b as $ef) {
						if (!isset($ilosciEfekt[$ef])) {
							$ilosciEfekt[$ef] = 0;
						}

						$ilosciEfekt[$ef]++;
						$wszystkich++;
					}
				}
			}
			foreach ($upgraded as $c => $a) {
				if (empty($a)) {
					continue;
				}

				foreach ($a as $e => $lv) {
					if (!(int) $lv > 0) {
						continue;
					}

					$efekty = $arrayElem[$c][$e];

					if (!isset($efekty) || empty($efekty))//Impossible
					{
						continue;
					}

					$percents = 0;

					for ($p = 0; $p < $lv; $p++) {
						$percents += $config['percents'][$p];
					}

					foreach ($efekty as $ef) {
						$zmiennoprzecinkowa = 2;
						$min = $model->$ef;
						if ((int) $min == $min) {
							$zmiennoprzecinkowa = 0;
						}

						$max = $model->{'max' . ucFirst($ef)};
						if ($max == NULL) {
							continue;
						}

						$one_percent = (($max - $min) / 100) / $ilosciEfekt[$ef];
						$model->$ef += round($one_percent * $percents, $zmiennoprzecinkowa);
						$model->cena += ($percents * ($cena / $wszystkich)) / 100;
					}
				}
			}
			return $model;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return $this->plane;
	}

	public function getCost() {
		try {
			$model = $this->getUpgradedModel();

			return $model->cena;

		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function getMultiplier() {
		try {
			$model = $this->getUpgradedModel();
			switch ($model->klasa) {
				case 1:
					return 1;
				case 2:
					return 2;
				case 3:
					return 4;
				case 4:
					return 6;
				case 5:
					return 8;
				case 6:
					return 10;
				case 7:
					return 12;
				case 8:
					return 14;
				case 9:
					return 16;
				case 10:
					return 6;
				case 11:
					return 6;
			}
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return 1;
	}

	public function printStaff($stan = false) {
		try {
			$zaloga = $this->staff->find_all();
			$tekst = "";
			foreach ($zaloga as $z) {
				$color = ($z->condition < 30) ? 'style="color: red;"' : '';
				$tekst .= $z->name . " (" . $z->type . ")";
				if ($stan) {
					$tekst .= " <span " . $color . ">(stan " . $z->condition . "%)</span>";
				}

				$tekst .= "<br />";
			}
			if (empty($tekst)) {
				$tekst = "Brak załogi.";
			}

			return $tekst;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return "Brak załogi.";
	}

	public function getZalogaCost($dist) {
		try {
			$zaloga = $this->staff->find_all();
			$cost = 0;
			$d = ceil($dist / 100);
			foreach ($zaloga as $z) {
				$cost += $z->wage;
			}

			$cost *= $d;
			return round($cost, 2);
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return 0;
	}

	public function updateStaffCondition($c, $when = false) {
		try {
			$zaloga = $this->staff->find_all();
			foreach ($zaloga as $z) {
				$z->changeCondition($c, $when);
			}

			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function updateStaffConditionToFuture($when = false) {
		try {
			$zaloga = $this->staff->find_all();
			foreach ($zaloga as $z) {
				$z->regenerateCondition($when);
				$r = $z->conditionFuture - $z->condition;
				$z->conditionFuture = null;
				$z->changeCondition($r, $when);
			}
			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function updateStaffConditionFuture($c) {
		try {
			$zaloga = $this->staff->find_all();
			foreach ($zaloga as $z) {
				$z->conditionFuture = $z->condition + $c;
				$z->save();
			}
			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function clearStaffConditionFuture() {
		try {
			$zaloga = $this->staff->find_all();
			foreach ($zaloga as $z) {
				$z->conditionFuture = null;
				$z->save();
			}
			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function updateStaffExperience($e) {
		$e = ceil($e);

		try {
			$zaloga = $this->staff->find_all();
			foreach ($zaloga as $z) {
				$z->addExperience($e);
			}

			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function updateStaffPosition() {
		try {
			$zaloga = $this->staff->find_all();
			foreach ($zaloga as $z) {
				$z->position = $this->position;
			}

			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function isBusy($timestamp = 0, $flight = 0) {
		try {
			if ($timestamp == 0) {
				$timestamp = time();
			}

			$plane = $this->flights->where('started', '<=', $timestamp)->and_where('end', '>', $timestamp)->and_where('id', '!=', $flight)->and_where('checked', '=', 1)->and_where('canceled', '=', 0)->count_all();
			if ($plane > 0) {
				return Busy::InAir;
			}

			$auctions = ORM::factory("Auction")->where('plane_id', '=', $this->id)->and_where('end', '>=', time())->count_all();
			if ($auctions != 0) {
				return Busy::OnAuction;
			}

			$accident = ORM::factory("Accident")->where('plane_id', '=', $this->id)->order_by('time', 'DESC')->limit(1)->find();
			if ($accident->time + $accident->delay > $timestamp) {
				return Busy::Accident;
			}

			$params = ORM::factory("EventParameter")->where('key', '=', 'plane')->and_where('value', '=', $this->id)->find_all();
			if ($params->count() == 0) {
				return Busy::NotBusy;
			}

			foreach ($params as $param) {
				if ($param->event->when > $timestamp && $param->event->done == 0 && $param->event->type != 10 && $param->event->type != 11) {
					return Busy::Unidentified;
				}
			}
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return Busy::NotBusy;
	}

	public function drawConditionBar() {
		try {
			return '<div class="well" style="padding: 0; margin: 0;">Stan: ' . $this->stan . '%<br /><div class="graph-0"><div style="width: ' . round($this->stan) * 2 . 'px"></div></div></div>';
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return "";
	}

	public function getFlight($w = false) {
		try {
			if (!$w) {
				$w = time();
			}

			return $this->flights->where('started', '<=', $w)->and_where('end', '>=', $w)->and_where('checked', '=', 1)->find();
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function getPlaceInQueue($time, $from = false) {
		if (!$from) {
			$from = time();
		}

		$activities = $this->flights->where('end', '>=', $from)->find_all();
		if ($activities->count() == 0) {
			return $from;
		}

		$lastTime = $from;
		foreach ($activities as $act) {
			if ($lastTime + $time < $act->odprawa) {
				return $lastTime;
			}

			$lastTime = $act->end + 1;
		}
		return $lastTime;
	}

	public function przegladGeneralny() {
		try {
			$typ = $this->plane;
			if (!$typ) {
				return sendError('Błąd. Zły typ samolotu.');
			}

			if ($this->isBusy() != Busy::NotBusy) {
				return sendError('Błąd. Samolot jest aktualnie używany.');
			}

			$stan = $this->stan;
			$cost = (100 - $stan) * ($typ->cena / 50); // 1% ubytku stanu * cena samolotu/50
			$czas = (100 - $stan) * $typ->klasa * 300; // 1% ubytku stanu * klasa = 10 min

			$newEvent = ORM::factory("Event");
			$newEvent->user_id = $this->user_id;
			$newEvent->when = time() + $czas;
			$newEvent->type = 9;
			$newEvent->save();

			$newParam = ORM::factory("EventParameter");
			$newParam->event_id = $newEvent->id;
			$newParam->key = 'plane';
			$newParam->value = $this->id;
			$newParam->save();

			$info = array('plane_id' => $this->id, 'type' => Financial::Przeglad);
			$this->user->operateCash(-$cost, 'Przegląd generalny samolotu - ' . $this->fullName() . '.', time(), $info);
			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function lotSwobodny($to, $start = false) {
		//TODO
		//Samolot zepsuty
		try {
			if (!$start) {
				$start = time();
			}

			$model = $this->getUpgradedModel();
			if (!$model) {
				sendError("Nieznany model samolotu.");
				return false;
			}
			$busy = $this->isBusy($start);
			if ($busy != Busy::NotBusy) {
				sendError("Samolot jest lub będzie zajęty - " . Busy::getText($busy) . ".");
				return false;
			}

			$from = $this->user->predictPosition($this->id, $start);
			$fromC = ORM::factory("City", $from);
			$distance = $fromC->getDistanceTo($to);
			if (!$distance || $distance == 0) {
				sendError("Do jakiego miasta ty to próbujesz wysłać? o.O");
				return false;
			}

			if ($model->zasieg < $distance) {
				sendError("Samolot nie ma wystarczającego zasiegu.");
				return false;
			}

			$staffs = $this->staff->find_all();
			foreach ($staffs as $s) {
				$s->regenerateCondition();
			}

			$pilotow = $model->piloci;
			$juzPilotow = $this->staff->where('type', '=', 'pilot')->count_all();
			if ($juzPilotow < $pilotow) {
				sendError("Nie masz pełnej załogi.");
				return false;
			}
			$czasDodatkowy = ceil($model->miejsc / 100) / 12;
			if ($czasDodatkowy > 0.5) {
				$czasDodatkowy = 0.5;
			}

			$czas = ($distance / ($model->predkosc * 0.85)) + $czasDodatkowy; //Dodatkowy czas na lądowanie i startowanie
			$czas = round($czas * 3600);

			$paliwowym = $distance * $model->spalanie;

			$paliwo = $paliwowym * 1.2;

			$kosztP = Oil::getOilCost($paliwo);
			$kosztZ = $this->getZalogaCost($distance);

			$koszt = $kosztP + $kosztZ;

			$odprawa = 300;

			$newEvent = ORM::factory("Event");
			$newEvent->user_id = $this->user_id;
			$newEvent->when = $start;
			$newEvent->type = 11;
			$newEvent->save();

			$event_id = $newEvent->id;

			$flight = ORM::factory("Flight");
			$flight->user_id = $this->user;
			$flight->plane_id = $this->id;
			$flight->from = $from;
			$flight->to = $to;
			$flight->odprawa = $start;
			$flight->started = $start + $odprawa;
			$flight->end = $start + $odprawa + $czas;
			$flight->event = $event_id;
			$flight->costs = $koszt;
			$flight->save();

			$flightId = $flight->id;

			$params = array();
			$params[] = '(' . $event_id . ', "distance", ' . round($distance) . ')';
			$params[] = '(' . $event_id . ', "czas", ' . round($czas) . ')';
			$params[] = '(' . $event_id . ', "plane", ' . round($this->id) . ')';
			$params[] = '(' . $event_id . ', "to", ' . round($to) . ')';
			$params[] = '(' . $event_id . ', "from", ' . round($from) . ')';
			$params[] = '(' . $event_id . ', "paliwo", ' . round($paliwo) . ')';
			$params[] = '(' . $event_id . ', "odprawa", ' . round($odprawa) . ')';
			$params[] = '(' . $event_id . ', "flight", ' . round($flightId) . ')';
			Events::insertEventParams($params);
			unset($params);

			$info = array('plane_id' => $this->id, 'type' => Financial::LotSwobodny);
			$this->user->operateCash(-$koszt, 'Opłaty lotu swobodny (' . Map::getCityName($from) . ' -> ' . Map::getCityName($to) . ') wykonywanego samolotem - ' . $this->fullName() . '.', $start, $info);
			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function lotZlecenie($zlecenieId, $timestamp, $bazaPaliwo = false, $checkinId) {
		//TODO
		//Samolot zepsuty
		try {
			if ($timestamp < time()) {
				$timestamp = time();
			}

			$model = $this->getUpgradedModel();
			if (!$model) {
				sendError("Nieznany model samolotu.");
				return false;
			}

			$busy = $this->isBusy($timestamp);
			if ($busy != Busy::NotBusy) {
				sendError("Samolot jest lub będzie zajęty - " . Busy::getText($busy) . ".");
				return false;
			}

			$zlecenie = ORM::factory("UserOrder", $zlecenieId);
			if (!$zlecenie->loaded() || $zlecenie->user_id != $this->user->id) {
				sendError('Nie znaleziono takiego zlecenia.');
				return false;
			}
			$from = ORM::factory("City", $zlecenie->order->from);
			$to = ORM::factory("City", $zlecenie->order->to);
			$distance = $from->getDistanceTo($to);
			$dystans = $distance;
			$miejsc = $model->miejsc;
			$pasazerow = $zlecenie->order->count;

			$airportConfig = Kohana::$config->load('airport');
			$checkin = ORM::factory("Checkin", $checkinId);
			if (!$checkin->loaded() || $checkin->public != 1) {
				sendError('Nie znaleziono takiego punktu odpraw.');
				return false;
			}

			$odprawa = $zlecenie->order->count * 15 * (1 - ($checkin->level * $airportConfig['checkin']['bonus'] / 100));
			$odprawaT = secondsToText($odprawa);

			$start = $checkin->findPlaceInQueue($odprawa, $timestamp);
			if (!$start || (($checkin->minCheckin > $odprawa || $checkin->maxCheckin < $odprawa || ($timestamp - time()) > $checkin->reservations) && $checkin->user_id != $this->user->id)) {
				sendError('Nie ma już miejsca w tym punkcie odpraw.');
				return false;
			}

			$start = $checkin->reserve($this->user, $this, $odprawa, $timestamp);
			if (!$start) {
				sendError('Nie ma już miejsca w tym punkcie odpraw.');
				return false;
			}

			$kosztO = $checkin->getCost($this->user, $odprawa);

			if (!$distance || $distance == 0) {
				sendError("Do jakiego miasta ty to próbujesz wysłać? o.O");
				return false;
			}

			if ($model->miejsc < $pasazerow) {
				sendError("Nie masz tyle miejsc w samolocie.");
				return false;
			}

			if ($model->zasieg < $distance) {
				sendError("Nie masz takiego zasiegu. Samolot " . $this->fullName() . " ma zasięgu " . $model->zasieg . "km, a lot jest na odległość " . $distance . "km.");
				return false;
			}

			$staffs = $this->staff->find_all();
			foreach ($staffs as $s) {
				$s->regenerateCondition();
			}

			$pilotow = $model->piloci;
			$dodatkowej = $model->zaloga_dodatkowa;
			$juzPilotow = $this->staff->where('type', '=', 'pilot')->count_all();
			$juzDodatkowej = $this->staff->where('type', '!=', 'pilot')->count_all();

			if ($juzPilotow < $pilotow || $juzDodatkowej < $dodatkowej) {
				sendError("Nie masz pełnej załogi.");
				return false;
			}

			$bazaB = false;
			$bazaOil = 0;

			$baza = $this->user->bazy->where('city_id', '=', $zlecenie->order->from)->find();
			if ($baza->loaded()) {
				$bazaB = true;
				$bazaOil = $baza->oil;
			}
			$czasDodatkowy = ceil($model->miejsc / 100) / 6;
			if ($czasDodatkowy > 0.5) {
				$czasDodatkowy = 0.5;
			}

			$czas = ($distance / ($model->predkosc * 0.85)) + $czasDodatkowy; //Dodatkowy czas na lądowanie i startowanie
			$czas = $czas * 3600;

			$bonusOil = $this->getOilBonus();

			$paliwowym = $distance * $model->spalanie * (1 - $bonusOil);

			$dodatkowe = $paliwowym * 0.1;
			if ($dodatkowe > 100) {
				$dodatkowe = 100;
			}

			$paliwo = $paliwowym + $dodatkowe;

			$paliwoZBazy = 0;
			if ($bazaB && $bazaPaliwo) {
				if ($bazaOil >= $paliwo) {
					$paliwoZBazy = $paliwo;
				} else {
					$paliwoZBazy = $bazaOil;
				}
				$kosztP = Oil::getOilCost($paliwo - $paliwoZBazy);
			} else {
				$kosztP = Oil::getOilCost($paliwo);
			}

			Oil::updateOilDemand($paliwo - $paliwoZBazy);
			$kosztZ = $this->getZalogaCost($distance);

			$oplaty = 0;
			$znizka = 1;
			$regionTenSam = false;
			if ($from->region == $to->region) {
				$regionTenSam = true;
			}

			if ($zlecenie->order->biuro == 1) {
				$oplaty = $pasazerow * ($distance / 5) * (1 + $pasazerow / 50);
			} elseif ($zlecenie->order->biuro == 2) {
				$oplaty = ($pasazerow * 100 * ($dystans / 500)) * (1 + $pasazerow / 50);
			} elseif ($zlecenie->order->biuro == 3) {
				$oplaty = ($pasazerow * 50 * ($dystans / 500)) * (1 + $pasazerow / 50);
			} elseif ($zlecenie->order->biuro == 4 || ($zlecenie->order->biuro == 5 && $model->klasa == 4)) {
				$oplaty = ($pasazerow * 25 * ($dystans / 500)) * (1 + $pasazerow / 100);
			} elseif ($zlecenie->order->biuro == 5 || $zlecenie->order->biuro == 6 || ($zlecenie->order->biuro == 7 && $model->klasa == 5)) {
				$oplaty = ($pasazerow * 15 * ($dystans / 500)) * (1 + $pasazerow / 100);
			} elseif ($zlecenie->order->biuro == 7) {
				$oplaty = ($pasazerow * 15 * ($dystans / 500)) * (1 + $pasazerow / 100);
			} elseif ($zlecenie->order->biuro == 8) {
				$oplaty = $pasazerow * 4 * ($dystans / 1000);
			} elseif ($zlecenie->order->biuro == 9) {
				$oplaty = $pasazerow * 4 * ($dystans / 1000);
			} elseif ($zlecenie->order->biuro == 10) {
				$oplaty = $pasazerow * 4 * ($dystans / 1000);
			} elseif ($zlecenie->order->biuro == 11) {
				$oplaty = ($pasazerow * 100 * ($dystans / 500)) * (1 + $pasazerow / 50);
			} elseif ($zlecenie->order->biuro == 12) {
				$oplaty = ($pasazerow * 100 * ($dystans / 500)) * (1 + $pasazerow / 40);
			}

			if ($regionTenSam && $miejsc <= 5)//Dla połączeń krajowych zniżka 15%(wg. danych z lotniska w Warszawie)
			{
				$znizka = 0.7;
			} elseif ($regionTenSam && $miejsc <= 20) {
				$znizka = 0.8;
			}

			$koszt = $kosztP + $kosztZ + $kosztO + ($oplaty * $znizka);

			if ($this->user->cash + 10000 <= $koszt) {
				sendError('Nie masz wystarczającej ilości pieniędzy.');
				return false;
			}

			$info = array('plane_id' => $this->id, 'type' => Financial::OLotZlecenie, 'order_id' => $zlecenie->id);
			$this->user->operateCash(-$koszt, 'Opłaty lotu na zlecenie (' . Map::getCityName($zlecenie->order->from) . ' -> ' . Map::getCityName($zlecenie->order->to) . ') wykonywanego samolotem - ' . $this->fullName() . '.', time(), $info);

			$newEvent = ORM::factory("Event");
			$newEvent->user_id = $this->user_id;
			$newEvent->when = $timestamp;
			$newEvent->type = 10;
			$newEvent->save();

			$event_id = $newEvent->id;

			$flight = ORM::factory("Flight");
			$flight->user_id = $this->user->id;
			$flight->plane_id = $this->id;
			$flight->from = $zlecenie->order->from;
			$flight->to = $zlecenie->order->to;
			$flight->odprawa = $timestamp;
			$flight->started = $timestamp + $odprawa;
			$flight->end = $timestamp + $odprawa + $czas;
			$flight->event = $event_id;
			$costs = $oplaty * $znizka;
			$costs += Oil::getOilCost($paliwoZBazy);
			$costs += $kosztZ;
			$costs += $kosztP;
			$flight->costs = $costs;
			$flight->save();

			$flightId = $flight->id;

			$zlecenie->flight_id = $flightId;
			$zlecenie->save();

			$params = array();
			$params[] = '(' . $event_id . ', "distance", ' . round($distance) . ')';
			$params[] = '(' . $event_id . ', "czas", ' . round($czas) . ')';
			$params[] = '(' . $event_id . ', "plane", ' . round($this->id) . ')';
			$params[] = '(' . $event_id . ', "zlecenie", ' . round($zlecenieId) . ')';
			$params[] = '(' . $event_id . ', "paliwo", ' . round($paliwo) . ')';
			$params[] = '(' . $event_id . ', "to", ' . round($to->id) . ')';
			$params[] = '(' . $event_id . ', "odprawa", ' . round($odprawa) . ')';
			Events::insertEventParams($params);
			unset($params);

			if ($baza->loaded()) {
				$baza->oil -= $paliwoZBazy;
				$baza->save();
			}

			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}
}