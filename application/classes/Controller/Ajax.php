<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ajax extends Controller {
	public function before() {
		$this->auto_render = !$this->request->is_ajax();
		if ($this->auto_render === TRUE) {
			parent::before();
		}
	}

	public function action_index() {
		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}
	}

	public function action_zlecenie() {
		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->response->status(403);
			return false;
		}

		$flightId = (int) $this->request->param('id');
		if ($flightId == 0) {
			return false;
		}

		$flight = ORM::factory("Flight", $flightId);
		if (!$flight->loaded() || $flight->user_id != $user->id) {
			return false;
		}

		$arr = array();

		$order = ORM::factory("UserOrder")->where("flight_id", "=", $flightId)->find();
		if ($order->loaded()) {
			$zlecenie = $order->order;
			$arr['from'] = Helper_Map::getCityName($zlecenie->from);
			$arr['to'] = Helper_Map::getCityName($zlecenie->to);
			$arr['deadline'] = Helper_TimeFormat::timestampToText($zlecenie->deadline);
			$arr['cash'] = formatCash($zlecenie->cash) . ' ' . WAL;
			//$arr['class'] = classToName($zlec->class);
			$arr['done'] = ($order->done == 1) ? 'Tak' : 'Nie';
			//$arr['biuro'] = $zlec->biuro;
			$arr['count'] = $zlecenie->count;
		} else {
			$arr['from'] = Helper_Map::getCityName($flight->from);
			$arr['to'] = Helper_Map::getCityName($flight->to);
			$arr['deadline'] = "Lot swobodny";
			$arr['cash'] = "Lot swobodny";
			$arr['punish'] = "Lot swobodny";
			//$arr['class'] = classToName($zlec['class']);
			$arr['done'] = ($flight->end <= time() && $flight->checked == 1 && $flight->canceled == 0) ? 'Tak' : 'Nie';
			//$arr['biuro'] = $zlec['biuro'];
			$arr['count'] = "Lot swobodny";
		}
		if ($flight) {
			$arr['flightId'] = $flight->id;
			$arr['odprawa'] = Helper_TimeFormat::timestampToText($flight->odprawa);
			$arr['started'] = Helper_TimeFormat::timestampToText($flight->started);
			$arr['end'] = Helper_TimeFormat::timestampToText($flight->end);
			$arr['movecancel'] = ((($flight->started - time()) >= 1800) || ($flight->checked == 0));
		}

		$jsonEncoded = json_encode($arr);
		echo $jsonEncoded;
	}

	public function action_planeColor() {
		$user = Auth::instance()->get_user();
		if (!$user) {
			echo "LOGOUT";
			return false;
		}

		$planeId = (int) $this->request->param('id');
		$color = $this->request->param('param');
		$p = ORM::factory("UserPlane", $planeId);
		if (!$p->loaded() || $p->user_id != $user->id) {
			sendError("Nieoczekiwany blad. Spróbuj ponownie.");
			$this->response->status(404);
		}
		$p->color = $color;
		$p->save();

	}

	public function action_deleteMiniMessage() {
		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->response->status(403);
			return false;
		}

		$id = (int) $this->request->param('id');

		$minis = ORM::factory("MiniMessage", $id);

		if ($minis->loaded() && $minis->user_id == $user->id) {
			$minis->checked = 1;
			$minis->save();
			echo "true";
		} else {
			$this->response->status(400);
		}
	}

	public function action_searchUser() {
		$term = $this->request->param('id');
		$arr = array();
		if ($term) {
			$q = ORM::factory("User")->where('username', 'LIKE', '%' . $term . '%')->find_all();
			foreach ($q as $p) {
				$arr[] = $p->username;
			}
		}

		$jsonEncoded = json_encode($arr);
		echo $jsonEncoded;
	}

	public function action_getNote() {
		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->response->status(404);
			return false;
		}

		$id = (int) $this->request->param('id');
		if ($id == 0) {
			$note = ORM::factory("Note");
			$note->user_id = $user->id;
			$note->name = "";
			$note->text = "";
			$note->save();
			$id = $note->id;
		}

		$note = ORM::factory("Note", $id);

		if ($note->loaded() && $note->user_id == $user->id) {
			$arr = array();
			$arr['id'] = $note->id;
			$arr['name'] = $note->name;
			$arr['text'] = $note->text;
			echo json_encode($arr);
		} else {
			$this->response->status(400);
		}
	}

	public function action_getNotes() {
		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->response->status(403);
			return false;
		}
		$arr = array();
		$notes = $user->notes->find_all();
		foreach ($notes as $note) {
			$a = array();
			$a['id'] = $note->id;
			$a['name'] = $note->name;
			$a['text'] = $note->text;
			$arr[] = $a;
		}
		echo json_encode($arr);
	}

	public function action_saveNote() {
		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->response->status(403);
			return false;
		}
		$id = (int) $this->request->param('id');
		$post = $this->request->post();

		if (!isset($post) || empty($post)) {
			$this->response->status(400);
			return false;
		}
		$note = ORM::factory("Note", $id);

		if ($note->loaded() && $note->user_id == $user->id) {
			try {
				$note->user_id = $user->id;
				$note->name = $post['name'];
				$note->text = $post['text'];
				$note->save();
				echo "true";
			} catch (Exception $e) {
				errToDB("[AJAX][" . __FUNCTION__ . "][Exception: " . $e->getMessage() . "]");
				$this->response->status(400);
			}
		}
	}

	public function action_deleteNote() {
		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->response->status(404);
			return false;
		}

		$id = (int) $this->request->param('id');
		$note = ORM::factory("Note", $id);

		if ($note->loaded() && $note->user_id == $user->id) {
			$note->delete();
			echo "true";
		} else {
			$this->response->status(400);
		}
	}

	public function action_staffWage() {
		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->response->status(403);
			return false;
		}

		$id = (int) $this->request->param('id');
		$post = $this->request->post();

		if (!isset($post) || empty($post)) {
			$this->response->status(400);
			return false;
		}
		$staff = ORM::factory("Staff", $id);

		if ($staff->loaded() && $staff->user_id == $user->id) {
			try {
				if ($post['wage'] < $staff->wantedWage - 25 || $post['wage'] > $staff->wantedWage + 25) {
					return false;
				}

				$staff->wage = (int) $post['wage'];
				$staff->save();
				$staff->countWagePercent();
				echo "true";
			} catch (Exception $e) {
				errToDB("[AJAX][" . __FUNCTION__ . "][Exception: " . $e->getMessage() . "]");
				$this->response->status(400);
			}
		}
	}

	public function action_upgradeInfo() {
		try {
			$user = Auth::instance()->get_user();
			if (!$user) {
				$this->response->status(404);
				return false;
			}

			$id = (int) $this->request->param('id');
			$plane = ORM::Factory("UserPlane", $id);
			if (!$plane->loaded() || $plane->user_id != $user->id) {
				$this->response->status(404);
				return false;
			}

			$model = $plane->plane;
			$upgradedM = $plane->getUpgradedModel();

			$category = $this->request->param('param');
			$element = $this->request->param('param2');

			$config = (array) Kohana::$config->load('upgrades');
			$configUpgrades = $config['upgrades'];
			$upgrades = json_decode($model->upgrades, true);
			$upgraded = json_decode($plane->upgrades, true);

			if (!isset($configUpgrades[$category][$element])) {
				$this->response->status(404);
				return false;
			}
			$efektyArray = array();
			$level = (int) $upgraded[$category][$element];
			if ($level < 5) {
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

				$efekty = $arrayElem[$category][$element];

				if (!isset($efekty) || empty($efekty))//Impossible
				{
					return false;
				}

				$percents = $config['percents'][$level];

				foreach ($efekty as $ef) {
					$efektyArray[$ef] = array();
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
					$efektyArray[$ef][0] = $upgradedM->$ef;

					$efektyArray[$ef][1] = $upgradedM->$ef + round($one_percent * $percents, $zmiennoprzecinkowa);

					$efektyArray[$ef][2] = $zmiennoprzecinkowa;
					if ($efektyArray[$ef][0] == $efektyArray[$ef][1]) {
						unset($efektyArray[$ef]);
					}
				}
			}

			$efektyText = "";
			foreach ($efektyArray as $ef => $a) {
				$efektyText .= '<b>' . ucFirst($ef) . '</b>: ' . $a[0] . ' => ' . $a[1] . ' ' . getStatUnit($ef) . '<br />';
			}

			$cost = round($model->klasa * 2000 * pow(1.4, $level + 1));
			$costP = round($model->klasa * 2 * pow(1.3, $level + 1));
			$przyciski = Form::open('warsztat/ulepszenia/' . $plane->id) . '
					<input type="hidden" name="category" value="' . $category . '"/>
					<input type="hidden" name="element" value="' . $element . '"/>
					<div class="col-xs-6">
						<button name="itemy" value="1" class="btn btn-primary btn-block">Kup za ' . formatCash($cost) . ' ' . WAL . '</button>
					</div>
					<div class="col-xs-6">
						<input type="submit" name="punkty" value="Kup za punkty (' . $costP . ' pkt)" class="btn btn-success btn-block"/>
					</div>
					</form>';
			if ($level >= 5) {
				$przyciski = "";
			}

			echo '<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title" id="upgradeModalLabel">Ulepszenie</h4>
			  </div>
			  <div class="modal-body">
				<div class="page-header">
					<h2>' . ucFirst($category) . ' <small>' . ucFirst($element) . '</small></h2>
					Aktualny poziom: <b>' . $level . '</b> / 5<br /><br />
					' . $efektyText . '<br />
					Wymagane przedmioty itd.<br />
					<br />
					' . $przyciski . '
				</div>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Zamknij</button>
			  </div>';
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return true;
	}

	public function action_tutorial() {
		try {
			$user = Auth::instance()->get_user();
			if (!$user) {
				$this->response->status(404);
				return false;
			}

			$id = (int) $this->request->param('id');
			if ($user->tutorialStep + 1 == $id) {
				$user->tutorialStep = $id;
				$user->save();
			} else {
				errToDb('[HACK][' . __CLASS__ . '][' . __FUNCTION__ . '][User: ' . $user->id . '][Tutorial: ' . $id . ' (Real: ' . $user->tutorialStep . ')]');
			}
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return true;
	}

	public function action_getChat() {
		try {
			$user = Auth::instance()->get_user();
			if (!$user) {
				$this->response->status(404);
				return false;
			}

			$limit = time() - 1800;
			$lastMessage = (int) $this->request->param('id');
			if ($lastMessage > 0) {
				$limit = $lastMessage;
			}

			$jsonArray = array('type' => 'history',
				'data' => array());
			$messages = ORM::factory("Chat")->where('date', '>', $limit)->order_by('date', 'ASC')->find_all();
			foreach ($messages as $msg) {
				$array = [];
				$array['author'] = $msg->user->username;
				$array['avatar'] = $msg->user->getAvatar();
				$array['text'] = $msg->msg;
				$array['time'] = $msg->date;
				$jsonArray['data'][] = $array;
			}
			echo json_encode($jsonArray);
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return true;
	}

	/**
	 * Deprecated
	 **/
	public function action_newChatMessage() {
		try {
			$user = Auth::instance()->get_user();
			if (!$user) {
				$this->response->status(404);
				return false;
			}

			$post = $this->request->post();
			if (!isset($post) || empty($post)) {
				$this->response->status(400);
				return false;
			}

			$msg = ORM::factory("Chat");
			$msg->user_id = $user->id;
			$msg->date = time();
			$msg->msg = $post['msg'];
			$msg->save();
			echo "TRUE";

		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return true;
	}

	public function action_findOrders() {
		try {
			$user = Auth::instance()->get_user();
			if (!$user) {
				$this->response->status(404);
				return false;
			}

			$planeId = (int) $this->request->param('id');
			if ($planeId == 0) {
				$this->response->status(404);
				return false;
			}

			$from = (int) $this->request->param('param');
            if ($from == 0) {
                echo json_encode(['from' => $from]);
                return false;
            }

			$to = (int) $this->request->param('param2');

			$plane = ORM::factory("UserPlane", $planeId);
			if (!$plane->loaded()) {
				$this->response->status(400);
				return false;
			}

			$model = $plane->getUpgradedModel();

			$offices = $plane->plane->getOffices();

			if ($to == 0) {
				$citiesTo = ORM::factory('City')->select('id')->order_by('region', 'asc')->order_by('name', 'asc')->find_all()->as_array();
			} else {
				$citiesTo = [$to];
			}
			$miejsc = $model->miejsc;

			$orders = ORM::factory('Order')
				->where('biuro', 'IN', $offices)
				->and_where('from', '=', $from)
				->and_where('to', 'IN', $citiesTo)
				->and_where('count', '<=', $miejsc)
				->and_where('taken', '=', 0)
				->and_where('test', '=', 0)
				->order_by('from', 'ASC')
				->find_all();

			$array = [];
			$cityNames = DB::select('id', 'name')->from('cities')->execute()->as_array('id', 'name');
			$text = "";

			$distArr = array();

			foreach ($orders as $o) {
				if (!isset($distArr[$o->from]) || !isset($distArr[$o->from][$o->to])) {
					$from = ORM::factory("City", $o->from);
					if (!isset($distArr[$o->from])) {
						$distArr[$o->from] = $from->getDistances();
					}
				}

				$dist = $distArr[$o->from][$o->to];
				$rmv = false;

				if ($dist > $model->zasieg) {
					$rmv = true;
				}

				$deadlineRed = $_SERVER['REQUEST_TIME'] + 36000;

				$arr = [];

				if (!$rmv) {
					$arr['id'] = $o->id;
					$arr['from'] = $o->from;
					$arr['to'] = $o->to;
					$arr['dist'] = $dist;
					$arr['count'] = $o->count;
					$arr['cash'] = $o->cash;
					$text = "";
					//$text .= '<tr class="elem" zlid="' . $o->id . '" sort_z="' . $o->from . '" sort_do="' . $o->to . '" dystans="' . $dist . '" pasazerow="' . $o->count . '" zaplata="' . $o->cash . '">';
					$text .= '<td>' . cleanString($cityNames[$o->from], false, " ") . '</td>';
					$text .= '<td>' . cleanString($cityNames[$o->to], false, " ") . '</td>';
					$text .= '<td>' . formatCash($dist, 0) . ' km</td>';
					$text .= '<td>' . formatCash($o->cash, 0) . ' ' . WAL . '</td>';
					$text .= '<td>' . $o->count . '</td>';
					//$text .= '<td class="hidden-xs">'.formatCash($o->punish, 0).' '.WAL.'</td>';
					if ($o->deadline <= $deadlineRed) {
						$text .= '<td style="color: red;">' . date("H:i d.m.y", $o->deadline) . '</td>';
					} else {
						$text .= '<td>' . date("H:i d.m.y", $o->deadline) . '</td>';
					}
					$text .= '<td class="stars"></td>';
					//$text .= "</tr>";

					$arr['tds'] = $text;

					$array[] = $arr;
				}
			}
			echo json_encode($array);
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
	}

};