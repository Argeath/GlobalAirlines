<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Samoloty extends Controller_Template {
	public function action_index() {
		$this->template->title = "Samoloty";

		$this->template->content = View::factory('hangar/samoloty')
		     ->bind('planesText', $planesText);

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$planes = $user->UserPlanes->order_by('rejestracja', 'ASC')->find_all();
		$klasy = (array) Kohana::$config->load('classes');
		$klasyKeys = array_keys($klasy);

		$planesText = "";
		foreach ($planes as $plane) {
			$typ = $plane->getUpgradedModel();
			$poz = $plane->city->name;
			$busy = $plane->isBusy();
			$accidentT = "";
			if ($busy != Busy::NotBusy) {
				$poz = Busy::getText($busy);
			}

			if ($busy == Busy::Accident) {
				$accident = ORM::factory("Accident")->where('plane_id', '=', $plane->id)->order_by('time', 'DESC')->find();
				if (!$accident->loaded()) {
					$busy = Busy::NotBusy;
				}

				$accidentTime = round((time() - ($accident->delay + $accident->time)) / 1800) * 2;
				$accidentT = "<br />Trwa naprawa<br />Potrwa jeszcze około " . $accidentTime . "h";
			}
			$pilotow = $plane->staff->where('type', '=', 'pilot')->count_all();
			$stewardess = $plane->staff->where('type', '=', 'stewardessa')->count_all();
			$wartosc = $plane->getCost();
			$klasa = (isset($klasyKeys[$typ->klasa - 1])) ? $klasyKeys[$typ->klasa - 1] : "";
			$planesText .= "<tr>
			<td><img src='" . URL::base(TRUE) . "assets/samoloty/" . $plane->plane_id . ".jpg' class='img-rounded hidden-xs' style='width: 150px;'/><br />" . $plane->fullName() . "<br />(" . $poz . ")" . $accidentT . "</td>
			<td>
				<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Klasa samolotu' style='display:inline-block;width: 160px; margin-bottom: 30px;'>" . $klasa . "</div>
				<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Zasięg samolotu' style='display:inline-block;width: 120px; margin-bottom: 30px;'><i class='fa fa-arrows-h'></i> " . $typ->zasieg . " km</div>
				<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Miejsca pasażerskie' style='display:inline-block;width: 70px; margin-bottom: 30px;'><i class='fa fa-users'></i> " . $typ->miejsc . "</div>
				<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Spalanie silników' style='display:inline-block;width: 120px; margin-bottom: 30px;'><i class='fa fa-fire'></i> " . $typ->spalanie . " kg/km</div>
				<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Prędkość maksymalna' style='display:inline-block;width: 120px; margin-bottom: 30px;'><i class='fa fa-tachometer'></i> " . $typ->predkosc . " km/h</div>
				<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Maksymalna wartość samolotu' style='display:inline-block;width: 120px; margin-bottom: 30px;'>" . WAL . " " . formatCash($wartosc) . "</div>
			</td>
			<td>
				<div class='text-rounded " . (($pilotow == $typ->piloci) ? 'bg-blue' : 'bg-red') . " Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Wymaganych pilotów' style='display:inline-block;width: 70px; margin-bottom: 30px;'><i class='fa fa-user'></i> " . $pilotow . " / " . $typ->piloci . "</div>
				<div class='text-rounded " . (($stewardess == $typ->zaloga_dodatkowa) ? 'bg-blue' : 'bg-red') . " Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Wymaganych stewardess' style='display:inline-block;width: 70px; margin-bottom: 30px;'><i class='fa fa-female'></i> " . $stewardess . " / " . $typ->zaloga_dodatkowa . "</div>
				<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Mnożnik serwisowy' style='display:inline-block;width: 70px; margin-bottom: 30px;'><i class='glyphicon glyphicon-wrench'></i> x" . $typ->mechanicy . "</div>
				<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Preferowane doświadczenie pilotów' style='display:inline-block;width: 70px; margin-bottom: 30px;'><i class='fa fa-graduation-cap'></i> " . ($plane->getPreferStaffExp() + 5) . "%</div>
			</td>

			<td>" . $plane->drawConditionBar() . "" . $plane->drawAccidentChanceBar() . "Pokonana trasa: " . formatCash($plane->km) . "km<br />Czasu w powietrzu: " . secondsToText($plane->hours) . "</td>
			<td class='list-group'>
				" . HTML::anchor('samoloty/lotswobodny/' . $plane->id, Form::submit('opcje', 'Lot swobodny', array('class' => "btn btn-primary btn-block btn-success"))) . "
				" . HTML::anchor('samoloty/zaloga/' . $plane->id, Form::submit('opcje', 'Załoga', array('class' => "btn btn-primary btn-block"))) . "
				" . HTML::anchor('samoloty/rejestracja/' . $plane->id, Form::submit('opcje', 'Zmiana rejestracji', array('class' => "btn btn-default btn-block btn-success"))) . "
				" . HTML::anchor('samoloty/wystaw/' . $plane->id, Form::submit('opcje', 'Wystaw na aukcji', array('class' => "btn btn-default btn-block btn-warning"))) . "
				" . HTML::anchor('samoloty/sprzedaj/' . $plane->id, Form::submit('opcje', 'Sprzedaj', array('class' => "btn btn-default btn-block btn-danger"))) . "
			</td></tr>";
		}

	}

	public function action_rejestracja() {
		$this->template->title = "Zmiana rejestracji samolotu";

		$this->template->content = View::factory('hangar/rejestracja')
		     ->bind('planeId', $planeId);

		$planeId = (int) $this->request->param('id');
		$plane = ORM::factory("UserPlane", $planeId);

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		if (!$plane->loaded() || $user->id != $plane->user_id) {
			return sendError('To nie jest twój samolot');
		}

		$post = $this->request->post();
		if ($post && isset($post['nowa'])) {
			try
			{
				$plane->rejestracja = $post['nowa'];
				$plane->save();
				sendMsg('Zmieniłeś rejestrację.');
				$this->redirect('Samoloty');
			} catch (ORM_Validation_Exception $e) {
				foreach ($e->errors('models') as $err) {
					sendError($err);
				}
			}
		}
	}

	public function action_wystaw() {
		$this->template->title = "Wystawianie na aukcjach";

		$this->template->content = View::factory('hangar/wystaw')
		     ->bind('planeId', $planeId);

		$planeId = (int) $this->request->param('id');
		$plane = ORM::factory("UserPlane", $planeId);

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$post = $this->request->post();
		if ($post && isset($post['minprice'])) {
			$price = (int) $post['minprice'];
			if ($price == 0) {
				return sendError('Cena minimalna musi być większa.');
			}

			$zaloga = ORM::factory("Staff")->where("plane_id", "=", $plane->id)->count_all();
			if ($zaloga > 0) {
				return sendError('Nie może do samolotu być przypisana żadna załoga.');
			}

			if ($plane->isBusy() != Busy::NotBusy) {
				return sendError('Samolot jest lub będzie używany.');
			}

			$auction = ORM::factory('Auction');
			$auction->user_id = $user->id;
			$auction->plane_id = $plane->id;
			$auction->minprice = $price;
			$auction->end = time() + (24 * 60 * 60);
			$auction->save();

			$newEvent = ORM::factory("Event");
			$newEvent->user_id = $user->id;
			$newEvent->when = $auction->end;
			$newEvent->type = 12;
			$newEvent->save();

			$newParam = ORM::factory("EventParameter");
			$newParam->event_id = $newEvent->id;
			$newParam->key = 'auction';
			$newParam->value = $auction->id;
			$newParam->save();

			sendMsg('Aukcja została stworzona.');
			$this->redirect('samoloty');
		}
	}

	public function action_sprzedaj() {
		$this->template->title = "Sprzedawanie samolotu";
		$this->template->content = View::factory('hangar/sprzedaj')
		     ->bind('kasa', $wartosc)
		     ->bind('planeId', $planeId);

		$planeId = (int) $this->request->param('id');

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$plane = ORM::factory("UserPlane", $planeId);
		if (!$plane->loaded() || $plane->user_id != $user->id) {
			sendError('Wystapił błąd. Spróbuj ponownie.');
			$this->redirect('samoloty');
		}

		$wartosc = $plane->getCost();
		$stan = $plane->stan;
		$za_stan = 1 + ((1 - ($stan / 100)) * 3);
		$wartosc *= 0.8;
		$wartosc /= $za_stan;
		$wartosc = round($wartosc);

		$post = $this->request->post();
		if ($post && isset($post['sprzedaj']) && $post['sprzedaj'] == "tak") {
			$zaloga = ORM::factory("Staff")->where("plane_id", "=", $plane->id)->find_all();
			if ($zaloga->count() > 0) {
				return sendError('Nie może do samolotu być przypisana żadna załoga.');
			}

			if ($plane->isBusy() != Busy::NotBusy) {
				return sendError('Samolot jest lub będzie używany.');
			}

			$info = array('type' => Financial::SklepSprzedaz, 'plane_id' => $plane->id);
			$user->operateCash($wartosc, 'Sprzedaż samolotu - ' . $plane->fullName() . '.', time(), $info);
			$plane->user_id = 0;
			$plane->save();
			sendMsg('Sprzedałeś samolot za ' . formatCash($wartosc) . ' ' . WAL . '.');
			$this->redirect('samoloty');
		}
	}

	public function action_lotswobodny() {
		$this->template->title = "Lot swobodny samolotu";

		$this->template->content = View::factory('hangar/lotswobodny')
		     ->bind('planeId', $planeId)
		     ->bind('citiesText', $citiesText);

		$planeId = (int) $this->request->param('id');
		$plane = ORM::factory("UserPlane", $planeId);

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		if (!$plane->loaded() || $user->id != $plane->user_id) {
			return sendError('To nie jest twój samolot');
		}

		$post = $this->request->post();
		if ($post && isset($post['dokad'])) {
			$to = (int) $post['dokad'];
			$this->template->content = View::factory('hangar/lotswobodny2')
			     ->bind('planeId', $planeId)
			     ->bind('z', $z)
			     ->bind('distance', $distance)
			     ->bind('zasieg', $zasieg)
			     ->bind('pilotow', $pilotow)
			     ->bind('juzPilotow', $juzPilotow)
			     ->bind('czas', $czas)
			     ->bind('paliwo', $paliwo)
			     ->bind('kosztP', $kosztP)
			     ->bind('kosztZ', $kosztZ)
			     ->bind('koszt', $koszt)
			     ->bind('razem', $razem)
			     ->bind('odprawa', $odprawa)
			     ->bind('zalogaT', $zalogaT)
			     ->bind('stan', $stan)
			     ->bind('startTimestamp', $startTimestamp)
			     ->bind('dokad', $to);

			$z = $plane->position;
			$from = ORM::factory("City", $z);
			$distance = $from->getDistanceTo($to);

			$model = $plane->getUpgradedModel();
			if (!$model) {
				sendError("Nieznany model samolotu.");
				return false;
			}

			$zasieg = $model->zasieg;

			$pilotow = $model->piloci;
			$juzPilotow = $plane->staff->where('type', '=', 'pilot')->count_all();

			$start = $post['planowany_start'];
			$d = new DateTime();
			if (!empty($start)) {
				$d = new DateTime($start);
			}

			$startTimestamp = $d->getTimestamp();

			$czasDodatkowy = ceil($model->miejsc / 100) / 12;
			if ($czasDodatkowy > 0.5) {
				$czasDodatkowy = 0.5;
			}

			$czas = ($distance / ($model->predkosc * 0.85)) + $czasDodatkowy; //Dodatkowy czas na lądowanie i startowanie
			$czas = round($czas * 3600);

			$paliwowym = $distance * $model->spalanie;

			$paliwo = $paliwowym * 1.2;

			$kosztP = Oil::getOilCost($paliwo);
			$kosztZ = $plane->getZalogaCost($czas);

			$koszt = $kosztP + $kosztZ;

			$razem = -$koszt;

			$odprawa = round($paliwo / 10);

			$zalogaT = $plane->printStaff(true);

			$stan = round($plane->stan, 2);

		} else {
			$citiesText = "";

			$cities = ORM::factory('City')->order_by('name', 'desc')->find_all();
			foreach ($cities as $city) {
				if ($city->id != $plane->position) {
					$citiesText .= "<option value='" . $city->id . "'>" . $city->name . "</option>";
				}
			}

			$this->template->additJs = '<script type="text/javascript">
			 $(function() {
				$("#planowany_start_input").datetimepicker({
					 lang:"pl",
					 timepicker:true,
					 format:"d.m.Y H:i",
					 minDate:0,
					 minTime:0,
					 step:5
					});
				$("#planowany_start_button").click(function(){
				 $("#planowany_start_input").datetimepicker("show");
				});
			 });
			</script>';
		}
	}

	public function action_lotswobodnywyslij() {
		$planeId = (int) $this->request->param('id');
		$plane = ORM::factory("UserPlane", $planeId);

		$user = Auth::instance()->get_user();
		if (!$user || !$plane) {
			$this->redirect('user/login');
		}

		if ($user->id != $plane->user_id) {
			sendError("To nie jest twoj samolot.");
			$this->redirect('samoloty');
		}

		$post = $this->request->post();
		if ($post && isset($post['dokad'])) {
			$czas = (int) $post['start'];
			if ($czas <= time()) {
				$czas = time();
			}

			if ($plane->lotSwobodny((int) $post['dokad'], $czas)) {
				sendMsg('Wysłano.');
			}
		}
		$this->redirect('podglad');
	}

	public function action_zaloga() {
		$this->template->title = "Załoga samolotu";

		$this->template->content = View::factory('hangar/zaloga')
		     ->bind('planeId', $planeId)
		     ->bind('zaloga', $zaloga)
		     ->bind('pilotow', $pilotow)
		     ->bind('juzPilotow', $juzPilotow)
		     ->bind('dodatkowej', $dodatkowej)
		     ->bind('juzDodatkowej', $juzDodatkowej)
		     ->bind('kadry', $kadry);

		$planeId = (int) $this->request->param('id');
		$plane = ORM::factory("UserPlane", $planeId);
		$planeModel = $plane->plane;

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		if (!$plane->loaded() || !isset($planeModel) || empty($planeModel) || $user->id != $plane->user_id) {
			sendError("Nie można znaleźć samolotu.");
			$this->redirect('Samoloty');
		}
		$pilotow = $planeModel->piloci;
		$dodatkowej = $planeModel->zaloga_dodatkowa;
		$juzPilotow = $plane->staff->where('type', '=', 'pilot')->count_all();
		$juzDodatkowej = $plane->staff->where('type', '!=', 'pilot')->count_all();
		$post = $this->request->post();
		if (isset($post) && !empty($post)) {
			if (isset($post['opcje']) && $post['opcje'] == 'Przypisz' && !empty($post['pracId'])) {
				$zalogant = ORM::factory("Staff", (int) $post['pracId']);
				if ($zalogant->loaded() && (($zalogant->type == 'pilot' && ($juzPilotow < $pilotow)) || ($zalogant->type == 'stewardessa' && $juzDodatkowej < $dodatkowej)) && $zalogant->user_id == $user->id) {
					if ($zalogant->position != $plane->position) {
						sendError('Nie możesz przypisać tego załoganta do tego samolotu. Załogant nie jest w tym samym miejscu co samolot.');
						$this->redirect('samoloty/zaloga/' . $plane->id);
					}
					if (!$zalogant->isPracBusy()) {
						$zalogant->plane_id = $plane->id;
						$zalogant->save();
						$this->redirect('samoloty/zaloga/' . $plane->id);

					} else {
						sendError('Nie możesz przypisać tego załoganta do tego samolotu. Załogant jest w tej chwili zajęty.');
						$this->redirect('samoloty/zaloga/' . $plane->id);
					}
				} else {
					sendError('Nie możesz przypisać tego załoganta do tego samolotu. Nie masz wystarczająco miejsc.');
					$this->redirect('samoloty/zaloga/' . $plane->id);
				}
			} elseif (isset($post['opcje']) && $post['opcje'] == 'Odwołaj' && !empty($post['pracId'])) {
				$z = ORM::factory("Staff", $post['pracId']);
				if (!$z->loaded() || $z->user_id != $user->id) {
					sendError("Wystąpił błąd. Spróbuj ponownie.");
					$this->redirect("Samoloty");
				}
				$z->plane_id = null;
				$z->position = $plane->position;
				$z->save();
				sendMsg("Załogant został odwołany.");
				$this->redirect('samoloty/zaloga/' . $plane->id);
			}
		}

		$q = $user->staff->where('plane_id', '=', $planeId)->order_by('type', 'ASC')->find_all();
		$kadry = "";
		foreach ($q as $r) {
			$kadry .= "<tr>
						<td>" . $r->name . " (" . $r->type . ")</td>
						<td>" . $r->drawAccidentChanceBar() . "</td>
						<td>" . $r->drawExperienceBar() . "</td>
						<td>" . $r->drawConditionBar() . "</td>
						<td>" . Form::open('samoloty/zaloga/' . $planeId) . "<input type='hidden' name='pracId' value='" . $r->id . "'/>" . Form::submit('opcje', 'Odwołaj', array('class' => "btn btn-primary btn-block")) . "" . Form::close() . "</td>
					  </tr>";
		}
		if (empty($kadry)) {
			$kadry = "<tr><td colspan='5'>Brak załogi przypisanej do tego samolotu</td></tr>";
		}

		$w = $user->staff->where('plane_id', 'IS', NULL)->and_where('position', '=', $plane->position)->order_by('type', 'ASC')->find_all();
		$zaloga = "";
		foreach ($w as $r) {
			$zaloga .= "<option value='" . $r->id . "'>" . $r->name . " (" . $r->type . ")</option>";
		}
	}
};