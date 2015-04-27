<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_Staff extends ORM {
	protected $_table_name = 'staffs';
	protected $_belongs_to = array(
		'user' => array(
			'model' => 'User',
			'foreign_key' => 'user_id',
		),
		'UserPlane' => array(
			'model' => 'UserPlane',
			'foreign_key' => 'plane_id',
		),
		'city' => array(
			'model' => 'City',
			'foreign_key' => 'position',
		),
	);

	public function isBusy() {
		try {
			if ($this->plane_id == NULL) {
				return false;
			}

			return $this->UserPlane->isBusy();
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function isPracBusy($timestamp = 0) {
		try {
			if ($timestamp == 0) {
				$timestamp = time();
			}

			$zalog = $this;
			if ($zalog->plane_id != 0) {
				$plane = $zalog->UserPlane->flights->where('started', '<=', $timestamp)->and_where('end', '>', $timestamp)->count_all();
				if ($plane > 0) {
					//sendError('Samolot jest w trakcie lotu.');
					return true;
				}
			}

			$params = ORM::factory("EventParameter")->where('key', '=', 'pracId')->and_where('value', '=', $this->id)->find_all();
			if ($params->count() == 0) {
				return false;
			}

			foreach ($params as $param) {
				if ($param->event->when > $timestamp && $param->event->done == 0 && $param->event->type != 10 && $param->event->type != 11) {
					return true;
				}
			}
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function getExperience()// 0% - 100%
	{
		try {
			$l = Experience::getLevelByExp($this->experience);
			return ($l <= 100) ? $l : 100;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return 0;
	}

	public function addExperience($e) {
		try {
			$przed = $this->getExperience();

			$bonus = (($this->condition - 50) * 0.4) / 100;
			$bonus2 = ($this->satisfaction * 0.2) / 100;
			$this->experience += $e * (1 + $bonus) * (1 + $bonus2);
			$this->save();
			$ile = round($e / 100);

			for ($i = $ile; $i > 0; $i--) {
				$this->checkWage(time() - (3600 * $i));
			}

			$po = $this->getExperience();
			if ($przed < $po) {
				$this->onLevelAdvance();
			}

			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	//Bonus do spalania paliwa
	public function getOilBonus()// 1% expa = 0.3% bonusu
	{
		return $this->getExperience() * 0.2;
	}

	public function changeCondition($c, $when = false) {
		try {
			if (!$when) {
				$when = time();
			}

			$przed = $this->condition;

			$bonus = 1;
			if ($this->satisfaction < 0 && $c < 0) {
				$bonus = 1 + ((-$this->satisfaction * 0.33) / 100);
			}

			$this->condition += $c * $bonus;

			if ($this->condition <= 5) {
				$this->leaveJob($when);
				return false;
			} elseif ($this->condition > 100) {
				$this->condition = 100;
			}

			if ($this->condition <= 20 && $c < 0) {
				$this->changeSatisfaction($this->condition - 20);
			}

			if ($this->condition != $przed) {
				$this->save();
			}

			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function getFlights($from, $to) {
		if ($to < $from) {
			return false;
		}

		$userFlights = $this->user->flights->where_open()->where('started', '<=', $from)->and_where('end', '>=', $to)->where_close()
		                    ->or_where_open()->where('started', '>=', $from)->and_where('started', '<=', $to)->or_where_close()
		                    ->or_where_open()->where('end', '>=', $from)->and_where('end', '<=', $to)->or_where_close()
		                    ->and_where('checked', '=', 1)->find_all();
		$flights = array();
		foreach ($userFlights as $uF) {
			$staff = json_decode($uF->staff, true);
			if ($staff == NULL || !is_array($staff)) {
				continue;
			}

			foreach ($staff as $s) {
				if ($s[0] == $this->id) {
					$flights[] = $uF;
				}
			}
		}

		return $flights;
	}

	public function regenerateCondition($when = false) {
		try {
			if (!$when) {
				$when = time();
			}

			$lastRegen = $this->lastRegeneration;

			if ($lastRegen >= $when) {
				return true;
			}

			$flights = $this->getFlights($lastRegen, $when);
			$czas = 0;
			/**
			 * |----|xxxxxx|--|xxxxx|-------
			 * last    fli      fli       when
			 */
			$czas = $when - $lastRegen;
			if (count($flights) > 0) {
				$czasLotow = 0;
				foreach ($flights as $f) {
					$start = 0;
					$stop = 0;
					if ($f->started <= $lastRegen) {
						$start = $lastRegen;
					} else {
						$start = $f->started;
					}
					if ($f->end <= $when) {
						$stop = $f->end;
					} else {
						$stop = $when;
					}
					$czasLotow += $stop - $start;
				}
				$czas -= $czasLotow;
			}

			$naMinute = 0.2;

			$x = round(($naMinute + $this->getExperience() / 1000) * ($czas / 60));
			$this->lastRegeneration = $when;
			$this->changeCondition($x, $when);
			$this->save();
			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function changeSatisfaction($s, $when = false) {
		try {
			if (!$when) {
				$when = time();
			}

			$przed = $this->satisfaction;
			$this->satisfaction += $s;
			if ($this->satisfaction <= -70) {
				$this->leaveJob($when);
				return false;
			} elseif ($this->satisfaction > 100) {
				$this->satisfaction = 100;
			}

			if ($this->satisfaction != $przed) {
				$this->save();
			}

			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function drawSatisfactionBar() {
		try {
			$width = $this->satisfaction;
			$left = 100;
			if ($width < 0) {
				$width = -$width;
				$left = 100 - $width;
			}
			return "<div><div class='graph-100'><div style='width: " . round($width) . "px; left: " . round($left) . "px;'></div></div> " . $this->satisfaction . "%</div>";
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return "";
	}

	public function drawConditionBar() {
		try {
			$this->regenerateCondition();
			$condition = $this->condition;
			if ($this->conditionFuture != null) {
				$flight = $this->UserPlane->getFlight();
				if ($flight->loaded()) {
					$czas = $flight->end - $flight->started;
					$percent = (time() - $flight->started) / $czas;
					$roznica = $this->condition - $this->conditionFuture;
					$condition -= $roznica * $percent;
				}
			}
			return "<div><div class='graph-0'><div style='width: " . round($condition) * 2 . "px'></div></div> " . round($condition, 2) . "%</div>";
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return "";
	}

	public function drawExperienceBar() {
		try {
			return "<div><div class='graph-0'><div style='width: " . $this->getExperience() * 2 . "px'></div></div> " . $this->getExperience() . "%</div>";
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return "";
	}

	public function drawAccidentChanceBar() {
		try {
			if ($this->type != 'pilot') {
				return "";
			}

			$chance = $this->getAccidentChance();
			$width = round($chance);
			$left = 100;
			if ($width < 0) {
				$width = -$width;
				$left = 100 - $width;
			}
			return "<div><div class='graph-100'><div style='width: " . $width . "px; left: " . $left . "px;'></div></div> " . $chance . "%</div>";
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return "";
	}

	public function leaveJob($when = false) {
		try {
			if (!$when) {
				$when = time();
			}

			$this->user->sendMiniMessage("Pracownik odszedł z pracy.", "Pracownik " . $this->name . "(" . $this->UserPlane->fullName() . ") odszedł z pracy.", $when);
			$this->user_id = 0;
			$this->plane_id = 0;
			$this->save();
			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function getAccidentChance()// Return 0+
	{
		try {
			if (!$this->UserPlane->loaded()) {
				return 0;
			}

			$miejsc = $this->UserPlane->plane->miejsc;
			$config = Kohana::$config->load('staffAccident');
			$xp = $this->getExperience();
			$closest = findClosestValue($config, $miejsc);
			$roz = $xp - $closest;
			$r = 5 - $roz;
			if ($r < -5) {
				$r = -5;
			}

			$r /= 2;
			$cond = round($this->condition / 10);
			$r += 7 - $cond;

			return $r;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return 0;
	}

	public function checkWage($t = false) {
		try {
			if (!$t) {
				$t = time();
			}

			if ($this->lastCheckWage > $t - 3600) {
				return false;
			}

			$wage = $this->wage;
			$wanted = $this->wantedWage;
			$s = round(($wage - $wanted) / 100, 2);
			/*if($s < -5)
			$s = -5;
			elseif($s > 5)
			$s = 5;*/

			$this->changeSatisfaction($s);
			$this->lastCheckWage = $t;
			$this->save();
			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
	}

	public function updateWantedWage() {
		try {
			$wage = $this->wantedWage;
			$lvlWage = round($wage / 50);
			$xp = $this->getExperience();
			if ($lvlWage != $xp) {
				if ($this->type == 'pilot') {
					$this->wantedWage = 50 + $xp * 5;
				} else {
					$this->wantedWage = 30 + $xp * 2;
				}

				$this->updateWage(); // + save
				return true;
			}
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function countWagePercent() {
		try {
			$this->wagePercent = ($this->wage - ($this->wantedWage - 25)) * 2;
			if ($this->wagePercent < 0) {
				$this->wagePercent = 0;
			}

			if ($this->wagePercent > 100) {
				$this->wagePercent = 100;
			}

			$this->save();
			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function updateWage() {
		$this->wage = ($this->wantedWage - 25) + ($this->wagePercent / 2);
		$this->save();
	}

	public function onLevelAdvance() {
		$this->countWagePercent();
		$this->updateWantedWage();
		$this->updateWage();
	}

	public function lotSwobodnyZaloga($to, $plane = false) {
		try {
			if ($this->plane_id != 0) {
				sendError("Pracownik ma przypisany samolot.");
				return false;
			}

			if ($this->isPracBusy()) {
				return false;
			}

			$distance = $this->city->getDistanceTo($to);
			if (!$distance || $distance == 0) {
				sendError("Do jakiego miasta ty to próbujesz wysłać? o.O");
				return false;
			}
			$czas = $distance / 895;
			$czas = $czas * 3600;

			$koszt = $distance * 1.2;

			$newEvent = ORM::factory("Event");
			$newEvent->user_id = $this->user->id;
			$newEvent->when = time() + $czas;
			$newEvent->type = 7;
			$newEvent->save();

			$event_id = $newEvent->id;

			//Parametry
			$params = array();
			$params[] = '(' . $event_id . ', "pracId", ' . $this->id . ')';
			$params[] = '(' . $event_id . ', "to", ' . $to . ')';
			if ($plane && (int) $plane > 0) {
				$params[] = '(' . $event_id . ', "plane", ' . $plane . ')';
			}

			Events::insertEventParams($params);
			unset($params);

			$info = array('type' => Financial::LotSwobodnyPrac, 'staff_id' => $this->id);
			$this->user->operateCash(-$koszt, 'Lot swobodny (' . $this->city->name . ' -> ' . Map::getCityName($to) . ') pracownika - ' . $this->name . '.', time(), $info);

			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function genName($nationality, $gender = NULL) {
		try {
			//Pzenieść do configa
			$names = array(
				'PL' => array(
					'M' => array(
						'imie' => array('Zygmunt', 'Stefan', 'Stanisław', 'Wojciech', 'Zbigniew', 'Tadeusz', 'Robert', 'Bronisław', 'Cezary', 'Włodzimierz', 'Racław', 'Bogumił', 'Karol', 'Radosław', 'Cyryl', 'Ludwik'),
						'nazwisko' => array('Nowak', 'Kowalski', 'Nociński', 'Noceń', 'Schab', 'Schabowski', 'Snitkowski', 'Sobalski', 'Sobczak', 'Sobczyński', 'Maciejewski', 'Kowalczyk', 'Dąbrowski', 'Pawlak', 'Jasiński'),
					),
					'K' => array(
						'imie' => array('Alicja', 'Aleksandra', 'Katarzyna', 'Urszula', 'Ewa', 'Justyna', 'Julita', 'Celestyna', 'Kaja', 'Malwina', 'Asia', 'Wioletta', 'Halina', 'Magdalena', 'Brygida', 'Jagoda', 'Klaudia'),
						'nazwisko' => array('Nowak', 'Kowalska', 'Nocińska', 'Noceń', 'Schab', 'Schabowska', 'Snitkowska', 'Sobalska', 'Sobczak', 'Sobczyńska', 'Wysocka', 'Jabłońska', 'Król', 'Piotrowska', 'Jaworska', 'Ostrowska'),
					),
				),
				'DE' => array(
					'M' => array(
						'imie' => array('Tom', 'Alexander', 'Ralf', 'Sven', 'Matthias', 'Max', 'Marcel', 'Dirk', 'Michael', 'Wolfgang', 'Niklas', 'Mike', 'Jürgen', 'Luca', 'Marko', 'Dennis', 'Eric', 'Christian', 'Jonas'),
						'nazwisko' => array('Hilzt', 'Bayer', 'Hahn', 'Urner', 'Lang', 'Baier', 'Dresner', 'Möller', 'Herz', 'Schultheiss', 'Austerlitz', 'Eichel', 'Wannemaker', 'Baumgaertner', 'Bumgarner', 'Weber'),
					),
					'K' => array(
						'imie' => array('Petra', 'Juliane', 'Lea', 'Simone', 'Karolin', 'Nicole', 'Laura', 'Stephanie', 'Lisa', 'Katharina', 'Melanie', 'Sabrina', 'Sandra', 'Christin', 'Monika', 'Anna', 'Susanne'),
						'nazwisko' => NULL,
					),
				),
			);
			try {
				$namesSorted = $names[$nationality];
			} catch (Exception $e) {
				$namesSorted = $names['PL'];
				$nationality = 'PL';
			}
			$genders = array('M', 'K');
			if ($gender == NULL) {
				$genderR = rand(0, 1);
				$gender = $genders[$genderR];
			}
			$namesSorted = $namesSorted[$gender];
			$imion = count($namesSorted['imie']);
			if ($namesSorted['nazwisko'] == NULL) {
				$namesSorted['nazwisko'] = $names[$nationality]['M']['nazwisko'];
			}
			$nazwisk = count($namesSorted['nazwisko']);
			$imieR = rand(0, $imion - 1);
			$nazwiskoR = rand(0, $nazwisk - 1);
			$imie = $namesSorted['imie'][$imieR];
			$nazwisko = $namesSorted['nazwisko'][$nazwiskoR];

			$name = $imie . ' ' . $nazwisko;
			$this->name = $name;
			$this->save();
			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}
}