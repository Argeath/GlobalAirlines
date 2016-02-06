<?php defined('SYSPATH') or die('No direct script access.');

class Helper_Bot {
	private $user;

	function Helper_Bot($user) {
		$this->user = $user;
	}

	public function getUser() {
		if ($this->user == null) {
			$this->user = ORM::Factory("User", 208);
		}
		return $this->user;
	}

	public function doNextAction($when = null) {
		if ($when == null) {
			$when = time();
		}

		$u = $this->getUser();
		$planes = $u->UserPlanes->find_all();
		if ($planes->count() == 0 || $u->cash > $this->getCashLevel()) {
			$this->buyPlane($when);
			usleep(500000);
		}
		$this->checkRegisters();
		$this->hireCrew();
		$this->getOrders($when);
		$this->deleteMultipleFlights($when);
	}

	private function getOrders($when) {
		$u = $this->getUser();
		$planes = $u->UserPlanes->find_all();

		/** @var Model_UserPlane $plane */
		foreach ($planes as $plane) {
			$accidentChance = $plane->getAccidentChance();

			$staffs = $plane->staff->find_all();
			$break = false;
			/** @var Model_Staff $staff */
			foreach ($staffs as $staff) {
				$staff->regenerateCondition($when);
				if ($staff->condition < 85) {
					$break = true;
				}
			}

			if ($break) {
				continue;
			}

			if ($accidentChance > 15) {
				if ($plane->isBusy($when)) {
					continue;
				}
				if ($plane->stan <= 90 && $plane->plane->cost <= $plane->cash / 25) {
					foreach ($staffs as $staff) {
						$staff->delete();
					}
					$planeValue = $plane->getCost();
					$condition = $plane->stan;
					$za_stan = 1 + ((1 - ($condition / 100)) * 3);
					$planeValue *= 0.8;
					$planeValue /= $za_stan;
					$planeValue = round($planeValue);

					$info = array('type' => Helper_Financial::SklepSprzedaz, 'plane_id' => $plane->id);
					$u->operateCash($planeValue, 'Sprzedaż samolotu - ' . $plane->fullName() . '.', $when, $info);

					$plane->user_id = 0;
					$plane->save();
				} elseif ($plane->stan <= 80 && $plane->plane->cost >= $u->cash / 2) {
					$plane->przegladGeneralny();
				}
				continue;
			}

			$nextFlight = $plane->getNextFlight($when);
			$flight = $plane->getFlight($when);

			if ($nextFlight != null && $nextFlight->loaded()) {
				continue;
			}

			$city = $plane->position;
			if ($nextFlight != null && $nextFlight->loaded()) {
				$city = $nextFlight->to;
				$flight = $nextFlight;
			} elseif ($flight != null && $flight->loaded()) {
				$city = $flight->to;
			}

			if ($flight != null && $flight->end - $when > 10800) {
				continue;
			}

			$model = $plane->getUpgradedModel();
			$offices = $plane->plane->getOffices();

			$planeSeats = $model->miejsc;

			$orders = ORM::factory('Order')
				->where('taken', '=', 0)
				->and_where('biuro', 'IN', $offices)
				->and_where('from', '=', $city)
				->and_where('taken', '=', 0)
				->and_where('test', '=', 0)
				->and_where('count', '<=', $planeSeats)
				->order_by('cash', 'DESC')
				->limit(10)
				->find_all();

			$ordersCount = $orders->count();
			if ($ordersCount == 0) {
				continue;
			}

			$orders = $orders->as_array();

			$zlecenia = [];

			foreach ($orders as $o) {
				$dist = Helper_Map::getDistanceBetween($o->from, $o->to);

				if ($model->zasieg >= $dist) {
					$zlecenia[] = $o;
				}
			}
			if (count($zlecenia) < 1) {
				continue;
			}

			$rand = mt_rand(0, count($zlecenia) - 1);
			$order = $zlecenia[$rand];

			$order->taken = 1;
			$order->save();

			$orderTaken = ORM::factory("UserOrder");
			$orderTaken->user_id = $u->id;
			$orderTaken->order_id = $order->id;
			$orderTaken->save();

			$newEvent = ORM::factory("Event");
			$newEvent->user_id = $u->id;
			$newEvent->when = $order->deadline;
			$newEvent->type = 3;
			$newEvent->save();

			$newParam = ORM::factory("EventParameter");
			$newParam->event_id = $newEvent->id;
			$newParam->key = 'zlecenie';
			$newParam->value = $orderTaken->id;
			$newParam->save();

			$checkin = ORM::factory("Checkin")->where('public', '=', 1)->and_where('city_id', '=', $city)->order_by('level', 'DESC')->limit(1)->find();
			if ($checkin == null) {
				echo "Brak checkinu w " . $city . "<br />";
				continue;
			}

			$plane->lotZlecenie($orderTaken->id, $flight->end + 120, false, $checkin->id);
			usleep(200000);
		}
	}

	private function deleteMultipleFlights($when) {
		$u = $this->getUser();
		/*$planes = $u->UserPlanes->find_all();

		foreach ($planes as $plane) {
		$flights = $plane->getNextFlights($when);

		$lastFlight = [];
		foreach ($flights as $f) {
		if (isset($lastFlight[$f->odprawa])) {
		$f->cancelQuietly($when);
		} else {
		$lastFlight[$f->odprawa] = 1;
		}
		}
		}*/

		$id = $u->id;
		$orderTaken = ORM::factory('UserOrder')
			->where('user_id', '=', $id)
			->and_where('flight_id', 'IS', NULL)
			->find_all();
		foreach ($orderTaken as $o) {
			$o->delete();
		}
	}

	private function hireCrew() {
		$u = $this->getUser();
		$planes = $u->UserPlanes->find_all();

		foreach ($planes as $plane) {
			$pilots = $plane->staff->where('type', '=', 'pilot')->count_all();
			$needed = $plane->plane->piloci;
			$exp = $plane->getPreferStaffExpLevel();

			if ($pilots < $needed) {
				for ($m = 0; $m < $needed - $pilots; $m++) {
					$staff = $u->hireStaff('pilot', $exp);
					$staff->plane_id = $plane->id;
					$staff->position = $plane->position; // CHEAT
					$staff->save();
				}
			}

			$stews = $plane->staff->where('type', '=', 'stewardessa')->count_all();
			$needed = $plane->plane->zaloga_dodatkowa;

			if ($stews < $needed) {
				for ($m = 0; $m < $needed - $stews; $m++) {
					$staff = $u->hireStaff('stewardessa', 0);
					$staff->plane_id = $plane->id;
					$staff->position = $plane->position; // CHEAT
					$staff->save();
				}
			}
		}
	}

	private function buyPlane($when) {
		$u = $this->getUser();
		$cash = ($u->cash / 10);
		if ($cash > 1000000) {
			$cash = 1000000;
		}

		$planes = ORM::factory("Plane")->where('wyrozSpalanie', '=', 0)->and_where('cena', '<', ($u->cash - $cash))->and_where('ukryty', '=', 0)->order_by('cena', 'DESC')->find_all();

		$count = $planes->count();
		if ($count < 1) {
			return false;
		}

		$rand = mt_rand(0, $count - 1);

		$planes = $planes->as_array();

		$plane = $planes[$rand];

		$p = ORM::factory("UserPlane");
		$p->user_id = $u->id;
		$p->plane_id = $plane->id;
		$p->position = $u->base->city->id;
		$p->save();
		$info = array('type' => Helper_Financial::Sklep, 'plane_id' => $p->id);
		$u->operateCash(-$plane->cena, 'Zakup samolotu - ' . $plane->producent . ' ' . $plane->model . '.', $when, $info);
		sendMsg("Kupiłeś " . $plane->producent . " " . $plane->model . " za " . formatCash($plane->cena) . " " . WAL . ".");
	}

	private function checkRegisters() {
		$u = $this->getUser();
		$planes = $u->UserPlanes->find_all();

		foreach ($planes as $p) {
			if ($p->rejestracja == "") {
				$p->rejestracja = $p->plane->producent[0] . $p->plane->model[0] . "-" . $p->countSamePlanes();
				$p->save();
			}
		}

	}

	private function getCashLevel() {
		$u = $this->getUser();
		$lvl = $u->getLevel();

		return round(20000 * pow($lvl, 1.5) * ceil($lvl / 10) / 10000) * 10000;
	}

}