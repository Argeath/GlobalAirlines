<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Airport extends Controller_Template {
	public function action_index() {
		$this->template->content = View::factory('lotnisko/index')
		     ->bind('office_id', $officeId)
		     ->bind('flights', $flights)
		     ->bind('departures', $departures)
		     ->bind('lotnisko', $lotnisko)
		     ->bind('city_id', $city_id)
		     ->bind('action', $action);

		$action = $this->request->action();
		$city_id = (int) $this->request->param('id');
		$officeId = 0;

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$lotnisko = ORM::factory("City", $city_id);
		if (!$lotnisko->loaded()) {
			sendError('Wystąpił błąd. Spróbuj ponownie.');
			$this->redirect('Podglad');
		}

		$this->template->title = "Lotnisko - " . $lotnisko->getName();

		$office = $user->bazy->where('city_id', '=', $lotnisko->id)->find();
		if ($office->loaded()) {
			$officeId = $office->id;
		}

		$flights = ORM::factory("Flight")->where('to', '=', $city_id)->and_where('end', '>', time())->and_where('checked', '=', 1)->find_all();
		$departures = ORM::factory("Flight")->where('from', '=', $city_id)->and_where('started', '>', time())->find_all();
	}

	public function action_offices() {
		$this->template->title = "Lotnisko - Biura";
		$this->template->content = View::factory('lotnisko/offices')
		     ->bind('office_id', $officeId)
		     ->bind('lotnisko', $lotnisko)
		     ->bind('city_id', $city_id)
		     ->bind('offices', $offices)
		     ->bind('action', $action);

		$action = $this->request->action();
		$city_id = (int) $this->request->param('id');
		$officeId = 0;

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$lotnisko = ORM::factory("City", $city_id);
		if (!$lotnisko->loaded()) {
			sendError('Wystąpił błąd. Spróbuj ponownie.');
			$this->redirect('Podglad');
		}

		$this->template->title = "Lotnisko - " . $lotnisko->getName() . " - Biura";

		$office = $user->bazy->where('city_id', '=', $lotnisko->id)->find();
		if ($office->loaded()) {
			$officeId = $office->id;
		}

		$offices = $lotnisko->offices->find_all();

	}

	public function action_stats() {
		$this->template->content = View::factory('lotnisko/stats')
		     ->bind('office_id', $officeId)
		     ->bind('lotnisko', $lotnisko)
		     ->bind('city_id', $city_id)
		     ->bind('ruch', $ruch)
		     ->bind('zarobki', $zarobki)
		     ->bind('zarobkiSuma', $zarobkiSuma)
		     ->bind('udzial', $udzial)
		     ->bind('udzialSuma', $udzialSuma)
		     ->bind('users', $users)
		     ->bind('action', $action);

		$action = $this->request->action();
		$city_id = (int) $this->request->param('id');
		$officeId = 0;

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$lotnisko = ORM::factory("City", $city_id);
		if (!$lotnisko->loaded()) {
			sendError('Wystąpił błąd. Spróbuj ponownie.');
			$this->redirect('Podglad');
		}

		$this->template->title = "Lotnisko - " . $lotnisko->getName() . " - Statystyki";

		$office = $user->bazy->where('city_id', '=', $lotnisko->id)->find();
		if ($office->loaded()) {
			$officeId = $office->id;
		}

		$flights = ORM::Factory("Flight")->where('end', '>', time() - Date::WEEK * 2)->and_where_open()->where('from', '=', $lotnisko->id)->or_where('to', '=', $lotnisko->id)->and_where_close()->and_where('checked', '=', 1)->find_all();
		$ruch = array();
		$udzial = array();
		$udzialSuma = 0;
		$users = array();
		foreach ($flights as $f) {
			$data = strftime("%Y-%m-%d", $f->end);
			if (!isset($ruch[$data])) {
				$ruch[$data] = 0;
			}

			$ruch[$data]++;
			if (!isset($udzial[$f->user_id])) {
				$udzial[$f->user_id] = 0;
			}

			$udzial[$f->user_id]++;
			$udzialSuma++;
			if (!isset($users[$f->user_id])) {
				$users[$f->user_id] = $f->user;
			}
		}

		$zarobki = array();
		$zarobkiSuma = 0;

		//$orders = ORM::Factory("Order")->where('deadline', '>', time() - Date::WEEK*2)->and_where_open()->where('from', '=', $lotnisko->id)->or_where('to', '=', $lotnisko->id)->and_where_close()->and_where('done', '=', 1)->find_all();
		$orders = ORM::Factory("Order")->where('deadline', '>', time() - Date::WEEK * 2)->and_where('from', '=', $lotnisko->id)->and_where('taken', '=', 1)->find_all();
		foreach ($orders as $o) {
			$flight = $o->flight;
			if (!$flight->loaded()) {
				continue;
			}

			$data = strftime("%Y-%m-%d", $flight->end);
			if (!isset($zarobki[$data])) {
				$zarobki[$data] = 0;
			}

			$zarobki[$data] += $o->cash;
			$zarobkiSuma += $o->cash;
		}

		for ($i = 0; $i < 14; $i++) {
			$data = strftime("%Y-%m-%d", time() - ($i * 86400));
			if (!isset($ruch[$data])) {
				$ruch[$data] = 0;
			}
		}

		asort($udzial);
		$udzial = array_reverse($udzial, true);
	}

	public function action_office() {
		$this->template->content = View::factory('lotnisko/buildings')
		     ->bind('tankVolume', $tankVolume)
		     ->bind('zbiornik_cena_wal', $zbiornik_cena_wal)
		     ->bind('zbiornik_cena_pkt', $zbiornik_cena_pkt)
		     ->bind('punktodpraw_cena_wal', $punktodpraw_cena_wal)
		     ->bind('punktodpraw_cena_pkt', $punktodpraw_cena_pkt)
		     ->bind('punktodpraw_perLevel', $punktodpraw_perLevel)
		     ->bind('punktodpraw_upgrade', $punktodpraw_upgrade)
		     ->bind('airport_costs', $airport_costs)
		     ->bind('lotnisko', $lotnisko)
		     ->bind('city_id', $city_id)
		     ->bind('office_id', $office_id)
		     ->bind('mainBase', $mainBase)
		     ->bind('checkins', $checkins)
		     ->bind('action', $action);

		$action = $this->request->action();
		$office_id = (int) $this->request->param('id');
		$mainBase = false;

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$lotnisko = ORM::factory('Office', $office_id);
		if (!$lotnisko->loaded() && ($lotnisko->user_id != $user->id && !$user->isAdmin())) {
			sendError('Wystąpił błąd. Spróbuj ponownie.');
			$this->redirect('podglad');
		}
		if ($lotnisko->id == $user->base_id) {
			$mainBase = true;
		}

		$city_id = $lotnisko->city_id;

		$this->template->title = "Lotnisko - " . $lotnisko->getName() . " - Biuro";
		$airportConfig = Kohana::$config->load('airport');

		$airport_costs = $airportConfig['cost'];

		$checkins = $lotnisko->checkins->find_all();

		$tank = $airportConfig['tank'];
		$tankVolume = $tank['volume'];
		$zbiornik_cena_wal = $tank['costWAL'];
		$zbiornik_cena_pkt = $tank['costPKT'];

		$punktowOdpraw = $lotnisko->checkins->count_all();
		$checkin = $airportConfig['checkin'];
		$punktodpraw_cena_pkt = $checkin['costPKT'] * ($punktowOdpraw + 1);
		$punktodpraw_perLevel = $checkin['bonus'];
		$punktodpraw_upgrade = $checkin['levelCost'];

		try {
			$punktodpraw_cena_wal = $checkin['costWAL'] * $airportConfig['cost'][$punktowOdpraw];
		} catch (Exception $e) {
			$punktodpraw_cena_wal = 99999999999;
		}

		$post = $this->request->post();
		if (!empty($post)) {
			$typ = $post['typ'];
			if ($post['module'] == 'kupZbiornik') {
				if ($typ == "wal") {
					if ($user->cash >= $zbiornik_cena_wal) {
						$lotnisko->cysterny++;
						$lotnisko->save();
						$info = array('type' => Financial::LotniskoRozbudowa, 'office_id' => $lotnisko->id);
						$user->operateCash(-$zbiornik_cena_wal, 'Zakup zbiornika paliwa na lotnisku - ' . $lotnisko->getName() . '.', time(), $info);
						sendMsg('Kupiłeś zbiornik paliwa na lotnisku w mieście ' . $lotnisko->getName() . '.');
						$this->redirect('airport/office/' . $lotnisko->id);
					} else {
						return sendError('Nie masz wystarczającej ilości pieniędzy.');
					}

				} elseif ($typ == "pkt") {
					if ($user->premium_points >= $zbiornik_cena_pkt) {
						$lotnisko->cysterny++;
						$lotnisko->save();
						$info = array('type' => Financial::LotniskoRozbudowa, 'office_id' => $lotnisko->id);
						$user->operatePoints(-$zbiornik_cena_pkt, 'Zakup zbiornika paliwa na lotnisku - ' . $lotnisko->getName() . '.', time(), $info);
						sendMsg('Kupiłeś zbiornik paliwa na lotnisku w mieście ' . $lotnisko->getName() . '.');
						$this->redirect('airport/office/' . $lotnisko->id);
					} else {
						return sendError('Nie masz wystarczającej ilości punktów.');
					}
				}
			} elseif ($post['module'] == 'kupPunktOdpraw') {
				if ($typ == "wal") {
					if ($user->cash >= $punktodpraw_cena_wal) {

						$check = ORM::factory("Checkin");
						$check->user_id = $user->id;
						$check->office_id = $lotnisko->id;
						$check->city_id = $lotnisko->city_id;
						$check->save();

						$info = array('type' => Financial::LotniskoRozbudowa, 'office_id' => $lotnisko->id);
						$user->operateCash(-$punktodpraw_cena_wal, 'Zakup punktu odpraw na lotnisku - ' . $lotnisko->getName() . '.', time(), $info);
						sendMsg('Kupiłeś punkt odpraw na lotnisku w mieście ' . $lotnisko->getName() . '.');
						$this->redirect('airport/office/' . $lotnisko->id);
					} else {
						return sendError('Nie masz wystarczającej ilości pieniędzy.');
					}

				} elseif ($typ == "pkt") {
					if ($user->premium_points >= $punktodpraw_cena_pkt) {

						$check = ORM::factory("Checkin");
						$check->user_id = $user->id;
						$check->office_id = $lotnisko->id;
						$check->city_id = $lotnisko->city_id;
						$check->save();

						$info = array('type' => Financial::LotniskoRozbudowa, 'office_id' => $lotnisko->id);
						$user->operatePoints(-$punktodpraw_cena_pkt, 'Zakup punktu odpraw na lotnisku - ' . $lotnisko->getName() . '.', time(), $info);
						sendMsg('Kupiłeś punkt odpraw na lotnisku w mieście ' . $lotnisko->getName() . '.');
						$this->redirect('airport/office/' . $lotnisko->id);
					} else {
						return sendError('Nie masz wystarczającej ilości punktów.');
					}
				}
			} elseif ($post['module'] == 'punktOdpraw') {
				$check = ORM::factory("Checkin", $post['checkin']);
				if (!$check->loaded() || $check->user_id != $user->id) {
					return true;
				}

				if ($typ == 'public') {
					$check->public ^= 1;
					$check->save();
					$this->redirect('airport/office/' . $lotnisko->id);
				} elseif ($typ == 'cash') {
					if ($check->cash > 0) {
						$cash = $check->cash;
						$check->cash = 0;
						$check->earned += $cash;
						$check->save();
						$info = array('type' => Financial::LotniskoPunktOdpraw, 'office_id' => $lotnisko->id, 'checkin_id' => $check->id);
						$user->operateCash($cash, 'Zebranie pieniędzy z punktu odpraw na lotnisku - ' . $lotnisko->getName() . '.', time(), $info);
						sendMsg('Zebrałeś ' . formatCash($cash) . ' ' . WAL . ' z punktu odpraw na lotnisku ' . $lotnisko->getName() . '.');
						$this->redirect('airport/office/' . $lotnisko->id);
					} else {
						sendError('Nie masz żadnych pieniędzy do zebrania.');
					}
				} elseif ($typ == 'upgrade') {
					$upgradeCost = $airport_costs[$check->level] * $punktodpraw_upgrade;
					if ($check->level < 5) {
						if ($user->cash > $upgradeCost) {
							$check->level++;
							$check->save();
							$info = array('type' => Financial::LotniskoUlepszeniePunktOdpraw, 'office_id' => $lotnisko->id, 'checkin_id' => $check->id);
							$user->operateCash(-$upgradeCost, 'Ulepszenie punktu odpraw na lotnisku - ' . $lotnisko->getName() . '.', time(), $info);
							sendMsg('Ulepszyłeś punkt odpraw na lotnisku ' . $lotnisko->getName() . '.');
							$this->redirect('airport/office/' . $lotnisko->id);
						} else {
							sendError('Nie masz wystarczającej ilości pieniędzy.');
						}
					} else {
						sendError('Ten punkt odpraw ma maksymalny poziom ulepszeń.');
					}
				} elseif ($typ == 'settings') {
					$this->redirect('airport/settings/' . $check->id);
				} elseif ($typ == 'activity') {
					$this->redirect('airport/activity/' . $check->id);
				}
			}
		}
	}

	public function action_settings() {
		$this->template->title = "Lotnisko - Punkt odpraw - ustawienia";
		$this->template->content = View::factory('lotnisko/settings')
		     ->bind('office_id', $office_id)
		     ->bind('checkin', $checkin)
		     ->bind('checkin_id', $checkin_id)
		     ->bind('lotnisko', $lotnisko)
		     ->bind('city_id', $city_id)
		     ->bind('action', $action);

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$action = $this->request->action();
		$checkin_id = (int) $this->request->param('id');
		$office_id = 0;

		$checkin = ORM::factory("Checkin", $checkin_id);
		if (!$checkin->loaded() || $checkin->user_id != $user->id) {
			sendError('Wystąpił błąd. Spróbuj ponownie.');
			$this->redirect('Podglad');
		}
		$city_id = $checkin->city_id;
		$office_id = $checkin->office_id;
		$lotnisko = ORM::factory("City", $city_id);
		if (!$lotnisko->loaded()) {
			sendError('Wystąpił błąd. Spróbuj ponownie.');
			$this->redirect('Podglad');
		}
		$post = $this->request->post();
		if (!empty($post)) {
			$cost = (int) $post['cost'];
			$reservations = (int) $post['reservations'];
			$minCheckin = (int) $post['minCheckin'];
			$maxCheckin = (int) $post['maxCheckin'];
			$checkin->cost = $cost;
			$checkin->reservations = $reservations * 3600;
			$checkin->maxCheckin = $maxCheckin * 60;
			$checkin->minCheckin = $minCheckin * 60;
			$checkin->save();
		}
	}

	public function action_activity() {
		$this->template->title = "Lotnisko - Punkt odpraw - aktywność";
		$this->template->content = View::factory('lotnisko/activity')
		     ->bind('office_id', $office_id)
		     ->bind('checkin', $checkin)
		     ->bind('checkin_id', $checkin_id)
		     ->bind('startH', $startH)
		     ->bind('dni', $dni)
		     ->bind('samoloty', $samoloty)
		     ->bind('suwakGodzinaPoz', $suwakGodzinaPoz)
		     ->bind('dateText', $dateText)
		     ->bind('textT', $textT)
		     ->bind('lotnisko', $lotnisko)
		     ->bind('city_id', $city_id)
		     ->bind('action', $action);

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$action = $this->request->action();
		$checkin_id = (int) $this->request->param('id');
		$office_id = 0;

		$checkin = ORM::factory("Checkin", $checkin_id);
		if (!$checkin->loaded() || ($checkin->user_id != $user->id && !$user->isAdmin())) {
			sendError('Wystąpił błąd. Spróbuj ponownie.');
			$this->redirect('Podglad');
		}
		$city_id = $checkin->city_id;
		$office_id = $checkin->office_id;
		$lotnisko = ORM::factory("City", $city_id);
		if (!$lotnisko->loaded()) {
			sendError('Wystąpił błąd. Spróbuj ponownie.');
			$this->redirect('Podglad');
		}

		$godziny = array();
		$startH = 0;
		$dni = array();
		$suwakGodzinaPoz = 0;

		$dateT = getDate();
		$suwakGodzinaPoz = round((($dateT['hours'] * 60 + $dateT['minutes']) / 3) + 10);

		for ($i = 0; $i <= 24; $i++) {
			$g = $i;
			$zero = '';
			if ($g < 10) {
				$zero = '0';
			}

			if ($g % 2 == 0 || $g == 0) {
				$godziny[$i] = $zero . '' . $g . '<span class="smallMinutes">00</span>';
			} else {
				$godziny[$i] = '';
			}
		}

		for ($i = 0; $i <= 24; $i++) {
			if ($godziny[$i]) {
				$poz = ($i * 20);
				$textT .= "<div class='suwakLabel' style='left: " . $poz . "px;'>" . $godziny[$i] . "</div>\n";
			}
		}

		$id = (int) $this->request->param('id2');
		$date = getDate();
		if ($id > 1356998400)// 1.01.2013
		{
			$date = getDate($id);
		}

		$dateText = $date['mday'] . '.' . $date['mon'] . '.' . $date['year'];

		for ($i = 1; $i <= 7; $i++) {
			$timeS = mktime(0, 0, 0, $date['mon'], $date['mday'] + ($i - 1), $date['year']);
			$timeS2 = mktime(0, 0, 0, $date['mon'], $date['mday'] + $i, $date['year']);

			$dateS = getDate($timeS);
			$data = date("d.m.Y", $timeS);
			$dni[$i]['name'] = strftime("%A", $timeS);
			$dni[$i]['date'] = $data;
			$dni[$i]['today'] = false;

			if ($dateS['mday'] == $dateT['mday'] && $dateS['mon'] == $dateT['mon'] && $dateS['year'] == $dateT['year']) {
				$dni[$i]['today'] = true;
			}

			$zleceniaT = "";
			$q = $checkin->activities->where('to', '>=', $timeS)->and_where('from', '<=', $timeS2)->find_all();
			foreach ($q as $a) {
				$started = $a->from;

				$czas = $a->to - $started;

				$width = $czas / (3 * 60);
				$pozx = (($started - $timeS) / (3 * 60)) + 14;
				$gwidth = $width;
				$part = 0;

				if ($a->to > $timeS2) {
					$part = 1;
					$width = ($timeS2 - $started) / (3 * 60);
				} elseif ($a->from < $timeS) {
					$part = 2;
					$rwidth = $timeS - $started;
					$width -= $rwidth / (3 * 60);
					$pozx = 14;
				}

				$percent = round(($width / $gwidth) * 100);

				$zlecNapis = "";

				$zlecLength = strlen($zlecNapis);

				if ($percent < 100) {
					$napisPart = round(($zlecLength * $percent) / 100);
					if ($part == 1) {
						$zlecNapis = substr($zlecNapis, 0, $napisPart);
					} elseif ($part == 2) {
						$zlecNapis = substr($zlecNapis, $napisPart);

					}
				}

				$zlecText = "<div class='suwakZlecenie";

				if ($part == 1) {
					$zlecText .= " suwakZleceniePart1";
				} elseif ($part == 2) {
					$zlecText .= " suwakZleceniePart2";
				}

				$zlecText .= "' style='width: " . round($width) . "px; left: " . round($pozx) . "px; background-color: rgba(0, 0, 0, 0.3);'>";
				$zlecText .= $zlecNapis;
				$zlecText .= "</div>";
				$zleceniaT .= $zlecText;
			}

			$dni[$i]['zlecenia'] = $zleceniaT;
		}
	}

	public function action_newOffice() {
		$this->template->content = View::factory('lotnisko/newOffice')
		     ->bind('office_id', $officeId)
		     ->bind('lotnisko', $lotnisko)
		     ->bind('city_id', $city_id)
		     ->bind('cena', $cena)
		     ->bind('action', $action);

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$action = $this->request->action();
		$city_id = (int) $this->request->param('id');
		$officeId = 0;

		$lotnisko = ORM::factory("City", $city_id);
		if (!$lotnisko->loaded()) {
			sendError('Wystąpił błąd. Spróbuj ponownie.');
			$this->redirect('Podglad');
		}

		$lotnisk = $user->bazy->count_all();
		$cenaConfig = Kohana::$config->load('airport.cost');
		$cena = $cenaConfig[$lotnisk - 1];

		$this->template->title = "Lotnisko - " . $lotnisko->getName();

		$office = $user->bazy->where('city_id', '=', $lotnisko->id)->find();
		if ($office->loaded()) {
			$this->redirect('airport/office/' . $office->id);
		}

		$post = $this->request->post();
		if (!empty($post)) {
			if (isset($post['potwierdzenie']) && $post['potwierdzenie'] == 'tak') {
				if ($user->cash >= $cena) {
					$bazaTest = ORM::factory("Office")->where('user_id', '=', $user->id)->and_where('city_id', '=', $city_id)->count_all();
					if ($bazaTest == 0) {
						$user->operateCash(-$cena, 'Zakup biura na lotnisku - ' . $lotnisko->name . '.');
						$nowaBaza = ORM::factory("Office");
						$nowaBaza->user_id = $user->id;
						$nowaBaza->city_id = $city_id;
						$nowaBaza->save();
						sendMsg("Wykupiłeś biuro w mieście - " . $lotnisko->name . ".");
						$this->redirect('airport/index/' . $city_id);
					} else {
						return sendError('W tym mieście masz już biuro.');
					}
				} else {
					return sendError('Nie masz wystarczającej ilości pieniędzy.');
				}
			} else {
				return sendError('Nie potwierdziłeś chęci kupna.');
			}
		}
	}

	public function action_leaveOffice() {
		$this->template->content = "";

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$action = $this->request->action();
		$officeId = (int) $this->request->param('id');

		$lotnisko = ORM::factory("Office", $officeId);
		if (!$lotnisko->loaded() || $lotnisko->user_id != $user->id) {
			sendError('Wystąpił błąd. Spróbuj ponownie.');
			$this->redirect('Podglad');
		}
		$city = $lotnisko->city;
		$lotnisko->delete();
		sendMsg('Opuściłeś biuro w mieście ' . $city->name . '.');
		$this->redirect('airport/index/' . $city->id);
	}

	public function action_find() {
		$this->template->title = "Wyniki wyszukiwania";
		$this->template->content = View::factory('lotnisko/find')
		     ->bind('miasta', $miasta);

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$nick = "";
		$post = $this->request->post();
		if (!$post) {
			sendError('Wystąpił błąd. Spróbuj ponownie.');
			$this->redirect('Podglad');
		} else {
			$nick = $post['nick'];
		}

		$miasta = "";

		$q = ORM::factory("City")->where('name', 'LIKE', '%' . $nick . '%')->or_where('code', 'LIKE', $nick)->find_all();
		if (count($q) == 1) {
			$this->redirect('airport/index/' . $q[0]->id);
		} elseif (count($q) >= 1) {
			foreach ($q as $p) {
				$text = HTML::anchor('airport/index/' . $p->id, $p->name . " (" . $p->code . ")");
				$miasta .= "<div class='thumbnail col-lg-1 col-md-2 col-xs-4'><i class='flag flag-" . strtolower($p->getCountry()) . "'></i> " . $text . "</div>";
			}
		} else {
			sendError('Nie znaleziono żadnego miasta.');
			$this->redirect('Podglad');
		}
	}
};

?>