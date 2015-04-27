<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Map extends Controller_Template {
	public function action_index() {
		$this->template->title = "Mapa połączeń";

		$this->template->content = View::factory('biuro/map')
		     ->bind('citymap', $citymap)
		     ->bind('zlmap', $zlmap);

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$citymap = "";
		$cityCoords = array();
		$cities = ORM::factory('City')->order_by('name', 'desc')->find_all();
		foreach ($cities as $city) {
			$citymap .= "
				citymap['" . $city->name . "'] = {
				  name:	  '" . $city->name . " " . $city->getFlag() . "',
				  weight: " . ($city->rozmiar + 1) . ",
				  radius: " . (($city->rozmiar + 1) * 9000) . ",
				  center: new google.maps.LatLng(" . $city->coordX . ", " . $city->coordY . ")
				};";
			$cityCoords[$city->id] = array('x' => $city->coordX, 'y' => $city->coordY);
		}

		$loty = $user->flights->where('end', '>=', time())->and_where('canceled', '=', 0)->find_all();

		$cache = array();
		$highest = 0;

		$zlmap = "";
		if (!empty($loty)) {
			foreach ($loty as $lot) {
				if (!isset($cache[$lot->from])) {
					$cache[$lot->from] = array();
				}

				if (!isset($cache[$lot->from][$lot->to])) {
					$cache[$lot->from][$lot->to] = 1;
				} else {
					$cache[$lot->from][$lot->to]++;
				}

				if ($cache[$lot->from][$lot->to] > $highest) {
					$highest = $cache[$lot->from][$lot->to];
				}
			}
		}

		$i = 1;
		foreach ($cache as $fromId => $tos) {
			$from = $cityCoords[$fromId];
			foreach ($tos as $toId => $intensity) {
				$to = $cityCoords[$toId];
				$zlmap .= "
					zlmap['" . $i++ . "'] = {
					  first: new google.maps.LatLng(" . $from['x'] . ", " . $from['y'] . "),
					  second: new google.maps.LatLng(" . $to['x'] . ", " . $to['y'] . "),
					  color: '#" . percent2Color($intensity, 150, $highest) . "'
					};";
			}
		}
		unset($cache);
	}

	public function action_path() {
		$this->template->title = "Mapa trasy";

		$this->template->content = View::factory('biuro/map')
		     ->bind('citymap', $citymap)
		     ->bind('zlmap', $zlmap);

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$flightId = (int) $this->request->param("id");
		$flight = ORM::factory("Flight", $flightId);

		$from = ORM::factory("City", $flight->from);
		$to = ORM::factory("City", $flight->to);

		$cache = array();
		$highest = 0;
		$zlmap = "zlmap[0] = {
                      first: new google.maps.LatLng(" . $from->coordX . ", " . $from->coordY . "),
                      second: new google.maps.LatLng(" . $to->coordX . ", " . $to->coordY . "),
                      color: '#FF0000'
                    };";

		$path = Map::findPath($from, $to);

		$citymap = "";
		$cityCoords = array();
		$cities = ORM::factory('City')->order_by('name', 'desc')->find_all();
		foreach ($cities as $city) {
			$rozmiar = $city->rozmiar;
			$additText = "";
			if ($path && in_array($city, $path)) {
				$dist1 = $city->countDistanceTo($from);
				$dist2 = $city->countDistanceTo($to);
				$additText = " From: " . $dist1 . ", To: " . $dist2;
				$rozmiar = 3;
			}
			$citymap .= "
				citymap['" . $city->name . "'] = {
				  name:	  '" . $city->name . " " . $city->getFlag() . "" . $additText . "',
				  weight: " . ($rozmiar + 1) . ",
				  radius: " . (($rozmiar + 1) * 9000) . ",
				  center: new google.maps.LatLng(" . $city->coordX . ", " . $city->coordY . ")
				};";
			$cityCoords[$city->id] = array('x' => $city->coordX, 'y' => $city->coordY);
		}
	}
};