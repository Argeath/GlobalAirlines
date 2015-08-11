<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Zlecenia2 extends Controller_Template {

	public function action_index() {
		$this->template->title = "Realizacja zlecenia - wybór zlecenia";

		$this->template->content = View::factory('biuro/zlecenia')
		     ->bind('ordersData', $ordersData);

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$zlecenia = $user->orders->where('done', '=', 0)->and_where('punished', '=', 0)->find_all();
		$ordersData = [];
		foreach ($zlecenia as $zl) {
			$order = $zl->order;
			$flight = $zl->flight;
			if (($flight->loaded() && ($flight->started >= time() || $flight->checked == 1) && $flight->canceled == 0) || $order->deadline <= time()) {
				continue;
			}

            $ordersData[] = [
                'order' => $order,
                'zlecenie' => $zl
            ];
		}
	}

	public function action_plane() {
		$this->template->title = "Realizacja zlecenia - wybór samolotu";

		$this->template->content = View::factory('biuro/zleceniaPlane')
		     ->bind('order', $order)
		     ->bind('zlecenie', $zlecenie)
		     ->bind('planes', $planes);

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$post = $this->request->post();
		if (!$post) {
			sendError('Nothing sent.');
			$this->redirect('zlecenie');
		}
		$zlecenie = ORM::factory("UserOrder", (int) $post['zlecenie']);
		if (!$zlecenie->loaded() || $zlecenie->user_id != $user->id) {
			return sendError('Nie znaleziono takiego zlecenia');
		}
		$order = $zlecenie->order;

		$planes = $user->UserPlanes->order_by('rejestracja', 'ASC')->find_all();
	}

	public function action_checkin() {
		$this->template->title = "Realizacja zlecenia - wybór punktu odpraw";

		$this->template->content = View::factory('biuro/zleceniaCheckin')
		     ->bind('zlecenieId', $zlecenieId)
		     ->bind('bazaPaliwo', $bazaPaliwo)
		     ->bind('planowany_start', $timestamp)
		     ->bind('zlecenie', $zlecenie)
		     ->bind('checks', $checks)
		     ->bind('planeId', $planeId);

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$post = $this->request->post();
		if (!$post) {
			sendError('Nothing sent.');
			$this->redirect('zlecenie');
		}

		$zlecenie = ORM::factory("UserOrder", (int) $post['zlecenie']);
		if (!$zlecenie->loaded() || $zlecenie->user_id != $user->id) {
			return sendError('Nie znaleziono takiego zlecenia.');
		}

		$zlecenieId = $zlecenie->id;
		$biuroId = $zlecenie->order->biuro;

		$plane = ORM::factory("UserPlane", (int) $post['plane']);
		if (!$plane->loaded() || $plane->user_id != $user->id) {
			sendError('Nie znaleziono takiego samolotu.');
			$this->redirect('zlecenie');
		}

		$planeId = $plane->id;

		if (empty($plane->rejestracja)) {
			$this->redirect('samoloty/rejestracja/' . $plane->id);
		}

		$model = $plane->getUpgradedModel();
		if (!$model->loaded()) {
			sendError("Nieznany model samolotu.");
			$this->redirect('zlecenie');
		}

		$bazaPaliwo = (isset($post['bazaPaliwo']) && $post['bazaPaliwo'] == 'on') ? $post['bazaPaliwo'] : 'off';

		$czas = $post['planowany_start'];
		$d = new DateTime();
		if (!empty($czas)) {
			$d = new DateTime($czas);
		}

		$timestamp = $d->getTimestamp();

		$city = ORM::Factory("City", $zlecenie->order->from);
		if (!$city->loaded()) {
			sendError('Nie znaleziono takiego miasta.');
			$this->redirect('zlecenie');
		}

		$odprawa = $zlecenie->order->count * 15;

		$airportConfig = Kohana::$config->load('airport');
		$userId = $user->id;
		$checkins = $city->checkins->where_open()
		                 ->where_open()
		                 ->where('public', '=', 1)->and_where('minCheckin', '<=', $odprawa)->and_where('maxCheckin', '>=', $odprawa)->and_where('reservations', '>', ($timestamp - time()))
		                 ->where_close()->or_where('user_id', '=', $userId)
		                 ->where_close()->order_by('cost', 'ASC')->find_all();
		$checks = array();

		foreach ($checkins as $ch) {
			$check = array();
			$check['checkin'] = $ch;
			$check['bonus'] = $ch->level * $airportConfig['checkin']['bonus'];
			$check['odprawa'] = $odprawa * (1 - ($check['bonus'] / 100));
			$check['cost'] = $ch->getCost($user, $check['odprawa']);
			$place = $ch->findPlaceInQueue($check['odprawa'], $timestamp);
			$check['place'] = $place;
			$checks[] = $check;
		}
	}

	public function action_confirm() {
		$this->template->title = "Realizacja zlecenia - potwierdzenie";

		$this->template->content = View::factory('biuro/zleceniaConfirm')
		     ->bind('zlecenieId', $zlecenieId)
		     ->bind('checkinId', $checkinId)
		     ->bind('z', $z)
		     ->bind('do', $do)
		     ->bind('dystans', $distance)
		     ->bind('zasieg', $zasieg)
		     ->bind('pasazerow', $pasazerow)
		     ->bind('miejsc', $miejsc)
		     ->bind('planowany_start', $planowany_start)
		     ->bind('odprawa', $odprawa)
		     ->bind('odprawaT', $odprawaT)
		     ->bind('czas', $czas)
		     ->bind('czasT', $czasT)
		     ->bind('paliwo', $paliwo)
		     ->bind('kosztP', $kosztP)
		     ->bind('kosztZ', $kosztZ)
		     ->bind('kosztO', $kosztO)
		     ->bind('zaplata', $zaplata)
		     ->bind('razem', $razem)
		     ->bind('pilotow', $pilotow)
		     ->bind('dodatkowej', $dodatkowej)
		     ->bind('juzPilotow', $juzPilotow)
		     ->bind('juzDodatkowej', $juzDodatkowej)
		     ->bind('zalogaT', $zalogaT)
		     ->bind('stan', $stan)
		     ->bind('bazaB', $bazaB)
		     ->bind('bazaOil', $bazaOil)
		     ->bind('paliwoZBazy', $paliwoZBazy)
		     ->bind('oplaty', $oplaty)
		     ->bind('znizka', $znizka)
		     ->bind('start', $start)
		     ->bind('planeId', $planeId);

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$zlecenieId = 0;
		$planeId = 0;
		$z = "";
		$do = "";
		$distance = 0;
		$zasieg = 0;
		$czasT = "";
		$paliwo = 0;
		$kosztP = 0;
		$kosztZ = 0;
		$kosztO = 0;
		$zaplata = 0;
		$odprawaT = "";
		$razem = 0;
		$pasazerow = 0;
		$miejsc = 0;
		$pilotow = 0;
		$dodatkowej = 0;
		$juzPilotow = 0;
		$juzDodatkowej = 0;
		$zalogaT = "";
		$stan = 0;
		$bazaB = 0;
		$bazaOil = 0;
		$oplaty = 0;
		$znizka = 1;
		$regionTenSam = false;
		$planowany_start = 0;
		$start = 0;

		$post = $this->request->post();
		if (!$post) {
			sendError('Nothing sent.');
			$this->redirect('zlecenie');
		}

		$zlecenie = ORM::factory("UserOrder", (int) $post['zlecenie']);
		if (!$zlecenie->loaded() || $zlecenie->user_id != $user->id) {
			sendError('Nie znaleziono takiego zlecenia.');
			$this->redirect('zlecenie');
		}

		$zlecenieId = $zlecenie->id;
		$biuroId = $zlecenie->order->biuro;

		$plane = ORM::factory("UserPlane", (int) $post['plane']);
		if (!$plane->loaded() || $plane->user_id != $user->id) {
			sendError('Nie znaleziono takiego samolotu.');
			$this->redirect('zlecenie');
		}

		$planeId = $plane->id;

		if (empty($plane->rejestracja)) {
			$this->redirect('samoloty/rejestracja/' . $plane->id);
		}

		$model = $plane->getUpgradedModel();
		if (!$model->loaded()) {
			sendError("Nieznany model samolotu.");
			$this->redirect('zlecenie');
		}

		$airportConfig = Kohana::$config->load('airport');
		$checkin = ORM::factory("Checkin", (int) $post['checkin']);
		if (!$checkin->loaded() || $checkin->public != 1) {
			sendError('Nie znaleziono takiego punktu odpraw.');
			$this->redirect('zlecenie');
		}

		$checkinId = $checkin->id;

		$odprawa = $zlecenie->order->count * 15 * (1 - ($checkin->level * $airportConfig['checkin']['bonus'] / 100));
		$odprawaT = TimeFormat::secondsToText($odprawa);

		$planowany_start = (int) $post['planowany_start'];
		$start = $checkin->findPlaceInQueue($odprawa, $planowany_start);
		if (!$start || (($checkin->minCheckin > $odprawa || $checkin->maxCheckin < $odprawa || ($planowany_start - time()) > $checkin->reservations) && $checkin->user_id != $user->id)) {
			sendError('Nie ma już miejsca w tym punkcie odpraw.');
			$this->redirect('zlecenie');
		}

		$kosztO = $checkin->getCost($user, $odprawa);

		$bazaPaliwo = (isset($post['bazaPaliwo']) && $post['bazaPaliwo'] == 'on') ? true : false;
		$baza = $user->bazy->where('city_id', '=', $zlecenie->order->from)->find();
		if ($baza->loaded() && $bazaPaliwo) {
			$bazaB = 1;
			$bazaOil = $baza->oil;
		}

		$miejsc = $model->miejsc;
		$pasazerow = $zlecenie->order->count;

		$from = ORM::factory("City", $zlecenie->order->from);
		if (!$from->loaded()) {
			return false;
		}

		$to = ORM::factory("City", $zlecenie->order->to);
		if (!$to->loaded()) {
			return false;
		}

		$z = $from->name;
		$do = $to->name;
		$distance = $from->getDistanceTo($to);
		$dystans = &$distance;
		$zasieg = $model->zasieg;

		if ($from->region == $to->region) {
			$regionTenSam = true;
		}

		$czas = ($distance / ($model->predkosc * 0.85)) + (ceil($model->miejsc / 75) / 4); //Dodatkowy czas na lądowanie i startowanie
		$czas = $czas * 3600;
		$czasT = TimeFormat::secondsToText($czas);

		$bonusOil = $plane->getOilBonus();

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

		$kosztZ = $plane->getZalogaCost($distance);

		$zaplata = $zlecenie->order->cash;
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

		/*
		else {
		//Opłata odlotu - normalnie wg. wagi samolotu
		if($miejsc > 20)
		$oplaty += 200 + ($pasazerow*30);

		//Opłata odprawy 50j za pasazera(Dane z lotniska w Gdańsku)
		if($miejsc > 10)
		$oplaty += $pasazerow * 50;

		//Opłata odprawy stała(Dane z lotniska w Gdańsku)
		$oplaty += 100;

		//Catering powyżej 600 km
		if($dystans > 600)
		$oplaty += $pasazerow*30;

		//Wykorzystanie rękawu powyżej 40 pasażerów
		if($pasazerow >= 40)
		$oplaty += 1000 * ceil($pasazerow/25);

		//System check-in - 1,80 za pasażera
		$oplaty += 5 * $pasazerow;

		//PRM (pomoc dla pasażerów o ograniczonej sprawności ruchowej) - 1 za pasażera
		$oplaty += 3 * $pasazerow;

		//Opłata trasowa (liczona wg. Polski i wg. pasażerów a nie wagi)
		$oplaty += round(sqrt($miejsc / 10) * ($dystans / 100) * 150);

		$oplaty += floor(($miejsc - $pasazerow) / 5) * 1000; // Opłata środowiskowa za niewykorzystane miejsca w samolocie
		}*/

		if ($regionTenSam && $miejsc <= 5)//Dla połączeń w tym samym kraju zniżka 15%(wg. danych z lotniska w Warszawie) Tutaj 30% dla samolotow do 5 miejsc
		{
			$znizka = 0.7;
		} elseif ($regionTenSam && $miejsc <= 20)//Dla połączeń w tym samym kraju zniżka 15%(wg. danych z lotniska w Warszawie) Tutaj 20% dla samolotow do 20 miejsc
		{
			$znizka = 0.8;
		}

		$razem = $zaplata - $kosztP - $kosztZ - $kosztO - ($oplaty * $znizka);

		$pilotow = $model->piloci;
		$dodatkowej = $model->zaloga_dodatkowa;
		$juzPilotow = $plane->staff->where('type', '=', 'pilot')->count_all();
		$juzDodatkowej = $plane->staff->where('type', '!=', 'pilot')->count_all();
		$zalogaT = $plane->printStaff(true);

		$stan = round($plane->stan, 2);
	}

	public function action_send() {
		$this->template->title = "Realizacja zlecenia - wysyłanie";

		$this->template->content = "";

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$zlecenieId = 0;
		$planeId = 0;

		$post = $this->request->post();
		if (!$post) {
			sendError('Nothing sent.');
			$this->redirect('podglad');
		}

		$zlecenie = ORM::factory("UserOrder", (int) $post['zlecenie']);
		if (!$zlecenie->loaded() || $zlecenie->user != $user->id) {
			sendError('Nie znaleziono takiego zlecenia.');
			$this->redirect('podglad');
		}

		$zlecenieId = $zlecenie->id;

		$plane = ORM::factory("UserPlane", (int) $post['plane']);
		if (!$plane->loaded() || $plane->user_id != $user->id) {
			sendError('Nie znaleziono takiego samolotu.');
			$this->redirect('podglad');
		}

		if ($zlecenie->flight_id != NULL) {
			sendError('Zlecenie jest aktualnie w trakcie wykonywania.');
			$this->redirect('podglad');
		}

		$bazaPaliwo = ((int) $post['bazaPaliwo'] == 1) ? true : false;

		$czas = (int) $post['planowany_start'];

		$checkin = ORM::factory("Checkin", (int) $post['checkin']);
		if (!$checkin->loaded() || $checkin->public != 1) {
			sendError('Nie znaleziono takiego punktu odpraw.');
			$this->redirect('zlecenie');
		}

		$checkinId = $checkin->id;

		if ($plane->lotZlecenie($zlecenieId, $czas, $bazaPaliwo, $checkinId)) {
			sendMsg('Wysłano');
			global $menu_zlecen;
			if ($menu_zlecen > 0) {
				$menu_zlecen--;
			}
		} else {
			sendError('Wystąpił błąd. Spróbuj ponownie.');
		}

		$this->redirect('zlecenie');
	}
};