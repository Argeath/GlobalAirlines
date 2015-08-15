<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Zlecenia extends Controller_Template {
	public $cities = array();
	public $regions = array();
	public $continents = array();
	public $contiregions = array();

	public $biura = array();
	public $klasy = array();
	public $klasyKeys = array();
	public function action_index() {
		$biuroId = (int) $this->request->param('biuropodrozy');
		$klasa = (int) $this->request->param('klasa');

		$this->biura = (array) Kohana::$config->load('biura');
		$this->klasy = (array) Kohana::$config->load('classes');
		$this->klasyKeys = array_keys($this->klasy);

		if ($biuroId == 0) {
			$biuroId = 1;
		}

		if ($biuroId > 0) {
			if ($biuroId > count($this->biura)) {
				sendError('Błąd. Złe biuro.');
				$this->redirect("Zlecenia");
			}
			$biuro = $this->biura[$biuroId];
		}

		$klasyZlecenia = Kohana::$config->load('zlecenia.zlecenia');
		$klasyZlecenia = $klasyZlecenia[$biuroId][1];
		$mozliweKlasy = array();
		$klasyText = "";
		foreach ($klasyZlecenia as $k => $v) {
			if ($v == 0) {
				continue;
			}

			$mozliweKlasy[] = $k;
		}
		if (!in_array($klasa, $mozliweKlasy)) {
			$klasa = $mozliweKlasy[0];
		}

		if (count($mozliweKlasy) > 1) {
			foreach ($klasyZlecenia as $k => $v) {
				if ($v == 0) {
					continue;
				}

				$klasyText .= '<li ' . (($k == $klasa) ? "class='active'" : "") . '>' . HTML::anchor('zlecenia/' . $biuroId . '/' . $k, $this->klasyKeys[$k]) . '</li>';
			}
		}

		$this->template->content = View::factory('zlecenia')
		     ->bind('biuro', $biuro)
		     ->bind('biuroId', $biuroId)
		     ->bind('planes', $planes)
		     ->bind('citiess', $this->cities)
		                            ->bind('showAll', $showAll)
		                            ->bind('klasyText', $klasyText)
		                            ->bind('zlecenia', $zlecenia);

		$this->template->title = "Biuro podróży - " . $biuro;

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$planes = $user->UserPlanes->find_all();

		if (isMenuBlocked($user->exp, $biuroId)) {
			sendError("Błąd. Nie masz wymaganego poziomu.");
			$this->redirect("zlecenia");
		}
		$post = $this->request->post();
		if (isset($post) && !empty($post) && isset($post['showAll']) && (int) $post['showAll'] == 1) {
			$user->wszystkie_zlecenia = (int) !$user->wszystkie_zlecenia;
			$user->save();
			$this->redirect('zlecenia/' . $biuroId . '/' . $klasa);
		}
		$showAll = $user->wszystkie_zlecenia;

		$this->template->is_zlecenie = true;
		$continents = Helper_Map::getContinents();
		if ($showAll == 0) {
			$this->regions = $user->getActiveRegions();
		} else {
			$this->regions = Helper_Map::getRegions();
		}

		if ($showAll == 0) {
			$this->contiregions = $user->getActiveContiRegions();
		} else {
			$this->contiregions = Helper_Map::getContinents();
		}

		if ($showAll == 0) {
			$this->cities = $user->getActiveCities();
		} else {
			$this->cities = ORM::factory('City')->select('id')->order_by('region', 'asc')->order_by('name', 'asc')->find_all()->as_array();
		}

		$this->clear();

		$zlecenia = $this->drawZlecenia($user, $biuroId, $klasa);
	}

	public function action_take() {
		$id = (int) $this->request->param('id');

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$zlecenie = ORM::factory("Order", $id);
		if (!$zlecenie->loaded() || $zlecenie->taken == 1) {
			sendError('Zlecenie to zostało już podjęte przez kogoś innego. Byłeś zbyt wolny :)');
			$this->redirect('orders');
			return;
		}

		/*{
		sendError('Zlecenia zostały tymczasowo wyłączone, aby ustabilizować sytuacje w powietrzu i znaleźć błąd powodujący zlecenia widma. Zajmie to 2-3 dni.');
		$this->redirect('zlecenia');
		}*/

		$zlecenie->taken = 1;
		$zlecenie->save();

		$orderTaken = ORM::factory("UserOrder");
		$orderTaken->user_id = $user->id;
		$orderTaken->order_id = $zlecenie->id;
		$orderTaken->save();

		$newEvent = ORM::factory("Event");
		$newEvent->user_id = $user->id;
		$newEvent->when = $zlecenie->deadline;
		$newEvent->type = 3;
		$newEvent->save();

		$newParam = ORM::factory("EventParameter");
		$newParam->event_id = $newEvent->id;
		$newParam->key = 'zlecenie';
		$newParam->value = $id;
		$newParam->save();

		$user->menuZlecen();
		sendMsg('Wzięto zlecenie.');
		$referrer = $this->request->referrer();
		$this->redirect($referrer);
	}

	public function clear() {
		//prepareDistances();
		DB::delete('zlecenia')->where('deadline', '<=', $_SERVER['REQUEST_TIME'] + 43200)->and_where('flight_id', '=', NULL)->and_where('taken', '=', 0)->execute();
	}

	public function drawZlecenia($user, $biuro, $klasa) {
		$text = '';
		if ($user->UserPlanes->count_all() == 0) {
			return '<tr><td colspan="9" style="text-align: center;">Aby zobaczyć zlecenia musisz mieć przynajmniej 1 samolot.</td></tr>';
		}

		$array = ORM::factory('Order')
			->where('user_id', 'IS', NULL)
			->and_where('biuro', '=', $biuro)
			->and_where_open()
			->where('region', 'IN', $this->regions)
		                              ->or_where('region', 'IN', $this->contiregions)
		                                                              ->or_where('region', '=', 'WR')
		                                                              ->and_where_close()
		                                                              ->and_where('from', 'IN', $this->cities)
		                                                                                             ->and_where('taken', '=', 0)
		                                                                                             /*->and_where('count', 'BETWEEN', $this->klasy[$this->klasyKeys[$klasa]])*/
		                                                                                             ->and_where('test', '=', 0)
		                                                                                             ->order_by('from', 'ASC')
		                                                                                             ->find_all();
		if (isset($array) && !empty($array) && $array->count() > 0) {
			$distArr = array();
			$cityNames = DB::select('id', 'name')->from('cities')->execute()->as_array('id', 'name');
			$deadlineRed = $_SERVER['REQUEST_TIME'] + 36000;
			foreach ($array as $k => $a) {
				if (!isset($distArr[$a->from]) || !isset($distArr[$a->from][$a->to])) {
					$from = ORM::factory("City", $a->from);
					if (!isset($distArr[$a->from])) {
						$distArr[$a->from] = $from->getDistances();
					}
				}

				$distance = $distArr[$a->from][$a->to];
				$text .= '<tr class="elem" zlid="' . $a->id . '" sort_z="' . $a->from . '" sort_do="' . $a->to . '" dystans="' . $distance . '" pasazerow="' . $a->count . '" zaplata="' . $a->cash . '">';
				$text .= '<td>' . cleanString($cityNames[$a->from], false, " ") . '</td>';
				$text .= '<td>' . cleanString($cityNames[$a->to], false, " ") . '</td>';
				$text .= '<td>' . formatCash($distance, 0) . ' km</td>';
				$text .= '<td>' . formatCash($a->cash, 0) . ' ' . WAL . '</td>';
				$text .= '<td>' . $a->count . '</td>';
				//$text .= '<td class="hidden-xs">'.formatCash($a->punish, 0).' '.WAL.'</td>';
				if ($a->deadline <= $deadlineRed) {
					$text .= '<td style="color: red;">' . date("H:i d.m.y", $a->deadline) . '</td>';
				} else {
					$text .= '<td>' . date("H:i d.m.y", $a->deadline) . '</td>';
				}
				$text .= '<td class="stars"></td></tr>';
			}
			unset($array);
			unset($distArr);
			return $text;
		} else {
			return '<tr><td colspan="9" style="text-align: center;">Nie ma żadnych dostępnych zleceń.</td></tr>';
		}

	}
}
