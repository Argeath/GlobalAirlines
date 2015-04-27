<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Podglad extends Controller_Template {

	public function action_index() {
		$this->template->title = "Podgląd";
		$this->template->content = View::factory('biuro/podglad')
		     ->bind('flightsText', $flightsText)
		     ->bind('odlotyText', $odlotyText)
		     ->bind('otherText', $otherText)
		     ->bind('financials', $financials)
		     ->bind('otherEmpty', $otherEmpty)
		     ->bind('km', $km)
		     ->bind('cash', $cash)
		     ->bind('hours', $hours);

		$otherEmpty = true;

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$cs = ORM::factory("City")->find_all();
		foreach ($cs as $c) {
			if ($c->code == "") {
				$c->code = null;
				$c->save();
			}
		}
		//cleanDB();

		/*set_time_limit(0);
		$cities = ORM::factory("City")->find_all();
		$checks = 0;
		foreach($cities as $c)
		$checks += $c->checkDistances();
		echo Debug::vars($checks);*/

		$financials = $this->Journals($user);

		$flights = $user->flights->where('started', '<=', time())->and_where('end', '>=', time())->and_where('checked', '=', 1)->order_by('end', 'ASC')->find_all();
		if ($flights->count() > 0) {
			$flightsText = "";
			foreach ($flights as $flight) {
				$plane = $flight->UserPlane;
				if (!$plane->loaded()) {
					continue;
				}

				$typ = $plane->getUpgradedModel();

				$planeHTML = "<img src='" . URL::base(TRUE) . "assets/samoloty/" . $plane->plane_id . ".jpg' class='img-rounded hidden-xs' style='width: 150px;'/><br />" . $typ->producent . " " . $typ->model . "<br />Miejsc: " . $typ->miejsc . "<br />Spalanie: " . $typ->spalanie . "kg/km<br />Prędkość przelotowa: " . $typ->predkosc . "km/h<br />Stan: " . round($plane->stan, 2) . "%<br />Pokonana trasa: " . formatCash($plane->km) . "km<br />Czasu w powietrzu: " . secondsToText($plane->hours) . "";

				$event = ORM::factory("Event", $flight->event);
				if (!$event->loaded()) {
					continue;
				}

				$delayed = "";
				if ($flight->delayed > 0) {
					$delayed = ' <span style="color: red;" class="Jtooltip" data-container=".main" data-toggle="tooltip" data-placement="bottom" title="Opóźnienie lotu">(' . $flight->delayed . 'M)</span>';
				}

				$flightsText .= "<tr>";
				$flightsText .= '<td><span class="Jtooltip" data-container=".main" data-toggle="tooltip" data-placement="bottom" title="' . eventTypeToName($event->type) . '">[' . eventTypeToShort($event->type) . ']</span></td>';
				$flightsText .= '<td><span class="Jpopover" data-container=".main" data-toggle="popover" data-placement="bottom" data-html="true" data-content="' . $planeHTML . '">' . $plane->rejestracja . '</span></td>';
				$flightsText .= '<td><span class="Jtooltip" data-container=".main" data-toggle="tooltip" data-placement="bottom" title="' . Map::getCityName($flight->from) . '">' . HTML::anchor("airport/index/" . $flight->from, cleanString(Map::getCityCode($flight->from), false, " ")) . '</a></td>';
				$flightsText .= '<td><span class="Jtooltip" data-container=".main" data-toggle="tooltip" data-placement="bottom" title="' . Map::getCityName($flight->to) . '">' . HTML::anchor("airport/index/" . $flight->to, cleanString(Map::getCityCode($flight->to), false, " ")) . '</td>';
				$flightsText .= "<td>" . timestampToText($flight->end) . $delayed . "</td>";
				$flightsText .= "<td class='zegarCountdown' czas='" . $flight->end . "' now='" . time() . "'>" . secondsToText($flight->end - time()) . "</td>";
				$flightsText .= "</tr>";
			}
		} else {
			$flightsText = "<tr><td colspan='6' style='text-align: center;'>Nie ma zadnych lotow</td></tr>";
		}
		//$flightsText = cleanString($flightsText, false, " ");
		$departures = $user->flights->where('started', '>', time())->and_where('canceled', '=', 0)->order_by('started', 'ASC')->find_all();
		if ($departures->count() > 0) {
			foreach ($departures as $departure) {

				$typ = $departure->UserPlane->getUpgradedModel();
				$zlec = $user->orders->where('flight_id', '=', $departure->id)->find();
				$remainingTime = $departure->odprawa;
				if ($zlec->loaded()) {
					$type = 10;
				} else {
					$type = 11;
				}

				if ($departure->odprawa < time()) {
					if ($type == 10) {
						$type = 4;
					} else {
						$type = 1;
					}

					$remainingTime = $departure->started;
				}
				$remaining = $remainingTime - time();

				$delayed = "";
				if ($departure->delayed > 0) {
					$delayed = ' <span style="color: red;" class="Jtooltip" data-container=".main" data-toggle="tooltip" data-placement="bottom" title="Opóźnienie lotu">(' . $departure->delayed . 'M)</span>';
				}

				$planeHTML = "<img src='" . URL::base(TRUE) . "assets/samoloty/" . $typ->id . ".jpg' class='img-rounded hidden-xs' style='width: 100%;'/><br />" . $typ->fullName() . "<br />Miejsc: " . $typ->miejsc . "<br />Spalanie: " . $typ->spalanie . "kg/km<br />Prędkość przelotowa: " . $typ->predkosc . "km/h<br />Stan: " . round($departure->UserPlane->stan, 2) . "%<br />Pokonana trasa: " . formatCash($departure->UserPlane->km) . "km<br />Czasu w powietrzu: " . secondsToText($departure->UserPlane->hours) . "";

				$odlotyText .= "<tr>";
				$odlotyText .= '<td><span class="Jtooltip" data-container=".main" data-toggle="tooltip" data-placement="bottom" title="' . eventTypeToName($type) . '">[' . eventTypeToShort($type) . ']</span></td>';
				$odlotyText .= '<td><span class="Jpopover" data-container=".main" data-toggle="popover" data-placement="bottom" data-html="true" data-content="' . $planeHTML . '">' . $departure->UserPlane->rejestracja . '</span></td>';
				$odlotyText .= '<td><span class="Jtooltip" data-container=".main" data-toggle="tooltip" data-placement="bottom" title="' . Map::getCityName($departure->from) . '">' . HTML::anchor("airport/index/" . $departure->from, cleanString(Map::getCityCode($departure->from), false, " ")) . '</a></td>';
				$odlotyText .= '<td><span class="Jtooltip" data-container=".main" data-toggle="tooltip" data-placement="bottom" title="' . Map::getCityName($departure->to) . '">' . HTML::anchor("airport/index/" . $departure->to, cleanString(Map::getCityCode($departure->to), false, " ")) . '</td>';
				$odlotyText .= "<td>" . timestampToText($departure->end) . $delayed . "</td>";
				$odlotyText .= "<td class='zegarCountdown' czas='" . $remainingTime . "' now='" . time() . "'>" . secondsToText($remaining) . "</td>";
				$odlotyText .= "</tr>";
			}
		} else {
			$odlotyText = "<tr><td colspan='6' style='text-align: center;'>Nie ma zadnych odlotow</td></tr>";
		}
		//$odlotyText = cleanString($odlotyText, false, " ");
		$other = $user->getEvents();
		if ($other->count() > 0) {
			$otherText = "";
			foreach ($other as $o) {
				$otherEmpty = false;
				$otherText .= "<tr>";
				//$otherText .= '<td><span class="Jtooltip" data-toggle="tooltip" data-container=".main" data-placement="right" title="'.eventTypeToName($o->type).'">['.eventTypeToShort($o->type).']</span></td>';
				$otherText .= '<td>' . eventTypeToName($o->type) . '</td>';
				$otherText .= "<td class='zegarCountdown' czas='" . $o->when . "' now='" . time() . "'>" . secondsToText($o->when - time()) . "</td>";
				$otherText .= "</tr>";
			}
		}

		$km = $user->km;
		$hours = $user->hours;
		$cash = $user->cash;
	}

	private function Journals($user) {
		$financials = $user->financials->order_by('data', 'DESC')->limit(5)->find_all();
		$financialsArr = array();
		foreach ($financials as $f) {
			$arr = array();
			$info = json_decode($f->info, true);
			$arr['dzien'] = strftime("%A, %d.%m.%Y", $f->data);

			$arr['byl'] = false;
			//if(isInArrayByElements($financialsArr, array('dzien' => $arr['dzien'])))
			//	$arr['byl'] = true;

			$arr['godzina'] = strftime("%R", $f->data);
			$arr['description'] = $f->description;
			$arr['change'] = $f->change;
			$arr['balance'] = $f->balance;
			$arr['wal'] = (isset($info['wal'])) ? 'PP' : WAL;

			$financialsArr[] = $arr;
		}
		return $financialsArr;
	}
};