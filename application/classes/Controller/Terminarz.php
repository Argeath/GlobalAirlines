<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Terminarz extends Controller_Template {
	public function action_index() {
		$this->template->title = "Terminarz lotów";

		$this->template->content = View::factory('biuro/terminarz')
		     ->bind('startH', $startH)
		     ->bind('dni', $dni)
		     ->bind('samoloty', $samoloty)
		     ->bind('suwakGodzinaPoz', $suwakGodzinaPoz)
		     ->bind('dateText', $dateText)
		     ->bind('textT', $textT);

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$godziny = array();
		$startH = 0;
		$dni = array();
		$samoloty = array();
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

		$planes = $user->UserPlanes->find_all();
		foreach ($planes as $plane) {
			$planeA = array();

			$color = $plane->color;
			if (!$color || $color == NULL) {
				$color = sprintf("%06x", rand(0, 0xFFFFFF));
				$plane->color = $color;
				$plane->save();
			}

			$text = $plane->rejestracja;
			$textcolor = getContrastYIQ($color);
			$planeA['color'] = $color;
			$planeA['buttonCode'] = "<div planeId='" . $plane->id . "' style='width: 130px; padding: 0; float: left;'>
				<div class='input-group'>
					<span class='input-group-btn'>
						<button class='btn btn-default terminarzBtn' style='background-color: #" . $color . "; color: " . $textcolor . "; font-size: 12px;'><input type='checkbox' class='plane_sort' style='float: left;'/> <div style='float: right; width: 50px; overflow: hidden;'>" . $text . "</div><div class='clearfix'></div></button>
					</span>
					<span class='input-group-addon'>
						<div class='colorpick' style='background-color: #" . $color . ";'></div>
					</span>
				</div>
			</div>";

			$samoloty[] = $planeA;
		}

		$id = (int) $this->request->param('id');
		$date = getDate();
		if ($id > 1356998400)// 1.01.2013
		{
			$date = getDate($id);
		}

		$dateText = $date['mday'] . '.' . $date['mon'] . '.' . $date['year'];

		for ($i = 1; $i <= 7; $i++) {
			$timeS = mktime(0, 0, 0, $date['mon'], $date['mday'] + ($i - 1), $date['year']);
			$timeS2 = mktime(0, 0, 0, $date['mon'], $date['mday'] + $i, $date['year']);

			if (date('I', $timeS) == "1") {
				$timeS += 3600;
				$timeS2 += 3600;
			}

			$dateS = getDate($timeS);
			$data = date("d.m.Y", $timeS);
			$dni[$i]['name'] = strftime("%A", $timeS);
			$dni[$i]['date'] = $data;
			$dni[$i]['today'] = false;

			if ($dateS['mday'] == $dateT['mday'] && $dateS['mon'] == $dateT['mon'] && $dateS['year'] == $dateT['year']) {
				$dni[$i]['today'] = true;
			}

			$zleceniaT = "";
			$q = $user->flights->where('end', '>=', $timeS)->and_where('started', '<=', $timeS2)->and_where('canceled', '=', 0)->find_all();
			foreach ($q as $flight) {
				$started = $flight->odprawa;
				if ($started == 0) {
					$started = $flight->started;
				}

				$czas = $flight->end - $started;
				$plane = $flight->UserPlane;
				if (!$plane->loaded()) {
					continue;
				}

				$bgcolor = $plane->color;
				$width = $czas / (3 * 60);
				$pozx = (($started - $timeS) / (3 * 60)) + 14;
				$gwidth = $width;
				$part = 0;

				if ($flight->end > $timeS2) {
					$part = 1;
					$width = ($timeS2 - $started) / (3 * 60);
				} elseif ($flight->started < $timeS) {
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

				$rgb = HexToRGB($bgcolor);
				$rgbT = "rgba(" . $rgb['r'] . ", " . $rgb['g'] . ", " . $rgb['b'] . ", 0.5)";
				$zlecText .= "' style='width: " . round($width) . "px; left: " . round($pozx) . "px; background-color: " . $rgbT . ";' planeId='" . $plane->id . "' flightId='" . $flight->id . "'>";
				$zlecText .= $zlecNapis;
				$zlecText .= "</div>";
				$zleceniaT .= $zlecText;
			}

			$dni[$i]['zlecenia'] = $zleceniaT;
		}
	}

	public function action_update() {
		$this->template->title = "Terminarz lotów";

		$this->template->content = View::factory('biuro/terminarzUpdate')
		     ->bind('plane', $plane)
		     ->bind('flight', $flight);

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$post = $this->request->post();
		if (!isset($post) || empty($post) || !isset($post['option']) || !isset($post['flightId'])) {
			sendError('Wystąpił błąd. Spróbuj ponownie.');
			$this->redirect('terminarz');
		}

		$flight = ORM::factory("Flight", (int) $post['flightId']);
		if (!$flight->loaded() || ($flight->started < (time() + 1800))) {
			sendError('Wystąpił błąd. Spróbuj ponownie.');
			$this->redirect('terminarz');
		}

		$plane = ORM::factory('UserPlane', $flight->plane_id);

		if ($post['option'] == 'move' && isset($post['planowany_start'])) {
			$czas = $post['planowany_start'];
			$d = new DateTime($czas);
			$t = $d->getTimestamp();
			$czasLotu = $flight->end - $flight->started;
			$odprawa = $flight->started - $flight->odprawa;
			if ($t < time() + $odprawa) {
				sendError('Nie możesz ustawić tak lotu, ponieważ już jest za późno aby wystartował.');
				$this->redirect('terminarz');
			}
			$roznica = $t - $flight->odprawa;
			$flight->odprawa = $t;
			$flight->started = $t + $odprawa;
			$flight->end = $t + $odprawa + $czasLotu;
			$flight->save();

			$event = ORM::factory("Event", $flight->event);
			$event->when += $roznica;
			$event->save();

			sendMsg('Przesunąłeś lot.');
			$this->redirect('terminarz');
		} elseif ($post['option'] == 'cancel') {
			$flight->cancel();
			sendMsg('Lot został anulowany.');
			$this->redirect('terminarz');
		}
	}
};