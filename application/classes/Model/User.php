<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_User extends Model_Auth_User {
	protected $_belongs_to = array(
		'referrer' => array(
			'model' => 'User',
			'foreign_key' => 'referrer_id',
		),
	);

	protected $_has_many = array(
		'user_tokens' => array('model' => 'User_Token'),
		'roles' => array('model' => 'Role', 'through' => 'roles_users'),
		'UserPlanes' => array(
			'model' => 'UserPlane',
			'foreign_key' => 'user_id',
		),
		'bazy' => array(
			'model' => 'Office',
			'foreign_key' => 'user_id',
		),
		'messages' => array(
			'model' => 'Message',
			'foreign_key' => 'user_id',
		),
		'miniMessages' => array(
			'model' => 'MiniMessage',
			'foreign_key' => 'user_id',
		),
		'staff' => array(
			'model' => 'Staff',
			'foreign_key' => 'user_id',
		),
		'flights' => array(
			'model' => 'Flight',
			'foreign_key' => 'user_id',
		),
		'orders' => array(
			'model' => 'UserOrder',
			'foreign_key' => 'user_id',
		),
		'financials' => array(
			'model' => 'Financial',
			'foreign_key' => 'user_id',
		),
		'auctions' => array(
			'model' => 'Auction',
			'foreign_key' => 'user_id',
		),
		'events' => array(
			'model' => 'Event',
			'foreign_key' => 'user_id',
		),
		'referrals' => array(
			'model' => 'User',
			'foreign_key' => 'referrer_id',
		),
		'notes' => array(
			'model' => 'Note',
			'foreign_key' => 'user_id',
		),
		'checkins' => array(
			'model' => 'Checkin',
			'foreign_key' => 'user_id',
		),
		'activities' => array(
			'model' => 'CheckinActivity',
			'foreign_key' => 'user_id',
		),
	);

	protected $_has_one = array(
		'base' => array(
			'model' => 'Office',
			'foreign_key' => 'user_id',
		),
	);

	public function isAdmin() {
		return ($this->roles->where('name', '=', 'admin')->count_all() > 0) ? true : false;
	}

	public function doBotStuff($when = false) {
		if ($this->isBot == 0) {
			return false;
		}
		if (!$when) {
			$when = time();
		}

		$event = ORM::factory("Event")->where('user_id', '=', $this->id)->and_where('type', '=', 15)->and_where('done', '=', 0)->find();

		if ($event == null || !$event->loaded()) {
			$newEvent = ORM::factory("Event");
			$newEvent->user_id = $this->id;
			$newEvent->when = $when + 1800;
			$newEvent->type = 15;
			$newEvent->save();
		}

		$bot = new Bot($this);
		$bot->doNextAction($when);
	}

	public function getLevel() {
		return Experience::getLevelByExp($this->exp);
	}

	public function addExperience($xp, $when = false) {
		try {
			if (!$when) {
				$when = time();
			}

			$przed = $this->getLevel();
			$this->exp += $xp;
			$po = $this->getLevel();
			if ($przed < $po) {
				if (!$this->onAdvance($when)) {
					$this->save();
				}
			} else {
				$this->save();
			}
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
			return false;
		}
		return true;
	}

	public function onAdvance($when = false) {
		try {
			if (!$when) {
				$when = time();
			}

			$newLvl = $this->getLevel();
			$points = 10 + $newLvl * 2;
			$this->sendMiniMessage("Awansowałeś ! Twój nowy poziom to " . $newLvl . ".", "Gratulacje ! Awansowałeś ! Twój nowy poziom to " . $newLvl . ". W nagrodę dostajesz " . $points . " punktów premium.", $when);
			$this->premium_points += $points;

			if ($this->referrer_id > 0) {
				$ref = $this->referrer;
				if ($ref->loaded()) {
					$points = 0;
					if ($this->referred_level < 10 && $newLvl >= 10) {
						$points += 20;
					}

					if ($this->referred_level < 20 && $newLvl >= 20) {
						$points += 40;
					}

					if ($this->referred_level < 30 && $newLvl >= 30) {
						$points += 60;
					}

					if ($this->referred_level < 40 && $newLvl >= 40) {
						$points += 80;
					}

					if ($this->referred_level < 50 && $newLvl >= 50) {
						$points += 100;
					}

					if ($points > 0) {
						$ref->premium_points += $points;
						$this->referred_points += $points;
					}

					// Kamienie milowe - Milestones
					if ($this->referred_level < 10 && $newLvl >= 10) {
						$ref->ref_milestones++;
						switch ($ref->ref_milestones) {
							case 1:
								$ref->premium_points += 100;
								break;
							case 5:
								$ref->premium_points += 200;
								break;
							case 10:
								$p = ORM::factory("UserPlane");
								$p->user_id = $ref->id;
								$p->plane_id = 49;
								$p->position = $ref->base->city->id;
								$p->save();
								break;
							case 15:
								$ref->cash += 500000;
								break;
							case 20:
								$p = ORM::factory("UserPlane");
								$p->user_id = $ref->id;
								$p->plane_id = 99;
								$p->position = $ref->base->city->id;
								$p->save();
								break;
							case 30:
								$ref->cash += 5000000;
								break;
							case 50:
								$p = ORM::factory("UserPlane");
								$p->user_id = $ref->id;
								$p->plane_id = 129;
								$p->position = $ref->base->city->id;
								$p->save();
								break;
						}
						$ref->sendMiniMessage("Osiągnąłeś kamień milowy poleceń.", "Osiągnąłeś kamień milowy poleceń. Aby dowiedzieć się więcej, wejdź w System Poleconych.", $when);
					}
					$ref->save();
					$this->referred_level = $newLvl;
				} else {
					errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Unknown referrer]');
				}
			}

			$this->save();
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
			return false;
		}
		return true;
	}

	public function operateCash($change, $description = NULL, $when = false, $info = NULL) {
		if (!$when) {
			$when = time();
		}

		try {
			$check = $this->financials->where('data', '=', $when)->and_where('change', '=', $change)->count_all();
			if ($check > 0) {
				errToDb('[Exception][Model_User][' . __FUNCTION__ . '] [Double operateCash][Change: ' . $change . '] [Desc: ' . $description . ']');
				return false;
			}
			$this->cash += $change;

			$f = ORM::factory("Financial");
			$f->user_id = $this->id;
			$f->data = $when;
			$f->change = $change;
			$f->balance = $this->cash;
			$f->description = $description;
			$f->info = json_encode($info);
			$f->save();
			$this->save();
			//$this->profil();
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
			return false;
		}
		return true;
	}

	public function operatePoints($change, $description = NULL, $when = false, $info = NULL) {
		if (!$when) {
			$when = time();
		}

		try {
			$check = $this->financials->where('data', '=', $when)->and_where('change', '=', $change)->count_all();
			if ($check > 0) {
				errToDb('[Exception][Model_User][' . __FUNCTION__ . '] [Double operatePoints][Change: ' . $change . '] [Desc: ' . $description . ']');
				return false;
			}
			$this->premium_points += $change;

			if (empty($info)) {
				$info = array();
			}

			$f = ORM::factory("Financial");
			$f->user_id = $this->id;
			$f->data = $when;
			$f->change = $change;
			$f->balance = $this->premium_points;
			$f->description = $description;
			if (!isset($info['wal']) || $info['wal'] != 'pkt') {
				$info['wal'] = 'pkt';
			}

			$f->info = json_encode($info);
			$f->save();
			$this->save();
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
			return false;
		}
		return true;
	}

	public function revertOperationsToDate($date, $onlyBad = false) {
		try {
			$financials = $this->financials->where('data', '>', $date)->find_all();
			$zmiana = 0;
			$zmianaPkt = 0;
			foreach ($financials as $f) {
				$info = json_decode($f->info, true);
				if (($onlyBad && $f->change < 0) || !$onlyBad) {
					if (isset($info['wal']) && $info['wal'] == 'pkt') {
						$zmianaPkt -= $f->change;
					} else {
						$zmiana -= $f->change;
					}
				}
			}

			if ($zmiana != 0) {
				$this->operateCash($zmiana, "Przywrócenie stanu konta do dnia: " . timestampToText($date) . ".");
			}

			if ($zmianaPkt != 0) {
				$this->operatePoints($zmianaPkt, "Przywrócenie punktów premium do dnia: " . timestampToText($date) . ".");
			}

			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function sendMiniMessage($msg, $long, $time = false) {
		try {
			if (!$time) {
				$time = time();
			}

			$eves = $this->miniMessages->where('data', '=', $time)->and_where('msg', '=', $msg)->count_all();
			if ($eves <= 0) {
				$e = ORM::factory("MiniMessage");
				$e->user_id = $this->id;
				$e->data = $time;
				$e->msg = $msg;
				$e->long = $long;
				$e->save();
			}
			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function bazy() {
		global $bazy;
		$bazy = $this->bazy->find_all();
		return $bazy;
	}

	public function profil() {
		try {
			global $profil;
			$profil['id'] = $this->id;
			$profil['nick'] = $this->username;
			$profil['username'] = $this->username;
			$profil['password'] = $this->password;
			$profil['facebook'] = $this->facebook;
			$profil['avatar'] = (($this->avatar) ? $this->avatar : 'avatar');
			$profil['cash'] = $this->cash;
			$profil['km'] = $this->km;
			$profil['hours'] = $this->hours;
			$profil['pasazerow'] = $this->pasazerow;
			$profil['zlecen'] = $this->zlecen;
			$profil['exp'] = $this->exp;
			$profil['expPercent'] = Experience::getPercentOfLevel($this->exp);
			$profil['expLabel'] = Experience::getExpLabel($this->exp);
			$profil['premium_points'] = $this->premium_points;
			$profil['admin'] = $this->isAdmin();
			$profil['token'] = $this->token;
			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function nowychWiadomosci() {
		try {
			global $nowych_wiadomosci;
			$wiadomosci = $this->messages->where('checked', '=', 0)->and_where('deleted', '=', 0)->and_where('saved', '=', 0)->and_where('typ', '=', 1)->count_all();
			//$wiadomosci = DB::select()->from('messages')->where('user', '=', $user)->and_where('checked', '=', 0)->and_where('deleted', '=', 0)->and_where('saved', '=', 0)->and_where('typ', '=', 1)->execute()->count();
			$nowych_wiadomosci = $wiadomosci;
			return $wiadomosci;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return 0;
	}

	public function nowychPowiadomien() {
		try {
			global $nowych_powiadomien;
			$powiadomien = $this->miniMessages->where('checked', '=', 0)->count_all();
			$nowych_powiadomien = $powiadomien;
			return $powiadomien;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return 0;
	}

	public function nowychKontaktow() {
		try {
			global $nowych_kontaktow;
			$kontaktow = ORM::factory("Contact")->where('accepted', '=', 0)->and_where('user2_id', '=', $this->id)->count_all();
			$nowych_kontaktow = $kontaktow;
			return $kontaktow;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return 0;
	}

	public function menuZlecen() {
		try {
			global $menu_zlecen;
			$ile = 0;
			$zlecenia = $this->orders->where('done', '=', 0)->and_where('punished', '=', 0)->find_all();
			foreach ($zlecenia as $zl) {
				$flight = $zl->flight;
				if (($flight->loaded() && ($flight->started >= time() || $flight->checked == 1) && $flight->canceled == 0) || $zl->order->deadline <= time()) {
					continue;
				}

				$ile++;
			}
			$menu_zlecen = $ile;
			return $ile;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return 0;
	}

	public function getAvatar() {
		try {
			$path = URL::base(TRUE) . 'uploads/';
			return ($this->avatar == NULL) ? $path . 'avatar.png' : $path . $this->avatar . '.jpg';
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return '';
	}

	public function drawButton() {
		return "<a href=" . URL::site('profil/' . $this->id) . " class='btn btn-xs btn-primary'>" . $this->username . "</a>";
	}

	public function drawAvatar() {
		$avatar = $this->getAvatar();
		return '<img src="' . $avatar . '" style="width: 100px; height: 100px;" class="img-thumbnail"/>';
	}

	public function getActiveRegions() {
		try {
			$query = $this->getActiveCities();
			$regions = array();
			foreach ($query as $city) {
				$region = $city->region;
				if (!in_array($region, $regions)) {
					array_push($regions, $region);
				}
			}
			return $regions;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function getActiveContiRegions() {
		try {
			$query = $this->getActiveCities();
			$regions = array();
			foreach ($query as $city) {
				$region = $city->region;
				$region = substr($region, 3);
				if (!in_array($region, $regions)) {
					array_push($regions, $region);
				}
			}
			return $regions;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function getActiveCities() {
		try {
			$cities = array();
			foreach ($this->UserPlanes->find_all() as $p) {
				$c = ORM::factory("City", $p->position);
				if (!in_array($c, $cities)) {
					$cities[] = $c;
				}
			}

			foreach ($this->flights->where('end', '>=', time())->find_all() as $f) {
				$c = ORM::factory("City", $f->to);
				if (!in_array($c, $cities)) {
					$cities[] = $c;
				}
			}

			uasort($cities, 'Model_City::cmpCities');
			return $cities;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function isBeingDeleted() {
		try {
			$ret = $this->events->where('type', '=', 6)->find();
			if (!$ret->loaded()) {
				return false;
			}

			return $ret->when;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function predictPosition($planeId, $timestamp) {
		try {
			$plane = ORM::factory("UserPlane", $planeId);
			if (!$plane->loaded() || $plane->user_id != $this->id) {
				sendError("Nie można znaleźć takiego samolotu.");
				return false;
			}

			$czas = time();
			$to = $plane->position;

			$zlecenia = $this->orders->find_all();
			foreach ($zlecenia as $z) {
				$flight = $z->flight;
				if (!$flight->loaded() || $flight->user_id != $this->id) {
					continue;
				}

				if ($flight->plane_id != $planeId) {
					continue;
				}

				if ($flight->end > $czas && $flight->end <= $timestamp) {
					$to = $flight['to'];
				}
			}

			return $to;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function updateFbData($data) {
		try {
			$is = DB::select()->from('fb_users')->where('player_id', '=', $this->id)->execute()->as_array();
			$data = json_encode($data);
			if (empty($is)) {
				DB::insert('fb_users')->columns(array('player_id', 'data'))->values(array($this->id, $data))->execute();
			} else {
				DB::update('fb_users')->set(array('data' => $data))->where('player_id', '=', $this->id)->execute();
			}

			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function getDepartures() {
		try {
			$array = array();
			$q = $this->flights->where('end', '>=', time())->and_where('started', '>', time())->and_where('checked', '=', 0)->find_all();
			foreach ($q as $elem) {
				$arr = array();
				$arr['when'] = $elem->started;
				$arr['parameters'] = array();
				$arr['parameters']['czas'] = $elem->end - $elem->started;
				$arr['parameters']['to'] = $elem->to;
				$arr['parameters']['from'] = $elem->from;
				$arr['parameters']['plane'] = $elem->plane_id;
				$zlec = ORM::factory("UserOrder")->where('flight_id', '=', $elem->id)->find();
				if ($zlec->loaded()) {
					$arr['parameters']['type'] = 10;
				} else {
					$arr['parameters']['type'] = 11;
				}

				$array[] = $arr;
			}
			return $array;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function getEvents() {
		return $this->events->where('done', '=', 0)->and_where('when', '>=', time())->and_where('type', 'IN', array(6, 7, 9))->order_by('when', 'ASC')->find_all();
	}

	public function getContacts() {
		return ORM::factory("Contact")->where('accepted', '=', 1)->and_where_open()->where('user_id', '=', $this->id)->or_where('user2_id', '=', $this->id)->and_where_close()->find_all();
	}

	public function getNotAcceptedContacts() {
		return ORM::factory("Contact")->where('accepted', '=', 0)->and_where('user2_id', '=', $this->id)->find_all();
	}

	public function getSentContacts() {
		return ORM::factory("Contact")->where('accepted', '=', 0)->and_where('user_id', '=', $this->id)->find_all();
	}

	public function isInContactWith($user) {
		try {
			$contacts = $this->getContacts();
			foreach ($contacts as $con) {
				if ($con->user_id == $user->id || $con->user2_id == $user->id) {
					return true;
				}
			}
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function addContact($new) {
		try {
			if ($this->isInContactWith($new)) {
				return false;
			}

			$contacts = $this->getSentContacts();
			foreach ($contacts as $con) {
				if ($con->user_id == $new->id || $con->user2_id == $new->id) {
					return false;
				}
			}

			$contacts = $this->getNotAcceptedContacts();
			foreach ($contacts as $con) {
				if ($con->user_id == $new->id || $con->user2_id == $new->id) {
					return $this->acceptContact($new);
				}
			}

			$c = ORM::factory("Contact");
			$c->user_id = $this->id;
			$c->user2_id = $new->id;
			$c->save();

			sendMsg("Zaprosiłeś gracza do znajomych.");

			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function acceptContact($new) {
		try {
			$contacts = $this->getNotAcceptedContacts();
			foreach ($contacts as $con) {
				if ($con->user_id == $new->id) {
					$con->accepted = 1;
					$con->save();
				}
			}
			sendMsg("Zaakceptowałeś znajomość z graczem.");
			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function refuseContact($new) {
		try {
			$contacts = $this->getNotAcceptedContacts();
			foreach ($contacts as $con) {
				if ($con->user_id == $new->id || $con->user2_id == $new->id) {
					$con->delete();
				}
			}

			sendMsg("Odrzuciłeś znajomość z graczem.");
			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function hireStaff($type, $experience) {
		try {
			$nationality = $this->base->city->region;
			$typy = array('', 'pilot', 'stewardessa');
			if ($type == 'pilot' || $type == 'stewardessa') {
				$typ = $type;
			} else {
				$typ = $typy[$type];
			}

			$gender = NULL;
			if ($typ == 'pilot') {
				$gender = 'M';
			} else {
				$gender = 'K';
			}

			$xp = 0;
			if ($experience == 0) {
				$xp = 250;
			} elseif ($experience == 1) {
				$xp = 1500;
			} elseif ($experience == 2) {
				$xp = 7500;
			} elseif ($experience == 3) {
				$xp = 30000;
			}

			$s = ORM::factory("Staff");
			$s->user_id = $this->id;
			$s->name = $s->genName($nationality, $gender);
			$s->type = $typ;
			$s->region = $nationality;
			$s->position = $this->base->city_id;
			$s->experience = $xp;
			$s->updateWantedWage();
			$s->wage = $s->wantedWage;
			$s->save();
			return $s;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function updateBalanceHistory($since = null) {
		try {
			if ($this->balanceHistory == null) {
				$history = array();
			} else {
				$history = json_decode($this->balanceHistory, true);
			}

			if ($since == null) {
				$since = time();
			}

			$sinceD = getDate($since);
			$day = mktime(0, 0, 0, $sinceD['mon'], $sinceD['mday'], $sinceD['year']);
			$todayD = getDate();
			$today = mktime(0, 0, 0, $todayD['mon'], $todayD['mday'], $todayD['year']);

			$elements = count($history);
			if ($elements == 0) {
				$dni = floor((time() - $day) / (24 * 60 * 60));
			} else {
				$last = $history[$elements - 1]['date'];
				$dni = 0;
				if ($last > $day) {
					$dni = floor((time() - $last) / (24 * 60 * 60));
				} else {
					$dni = floor((time() - $day) / (24 * 60 * 60));
				}
			}

			if ($dni == 0) {
				return false;
			}

			for ($i = 0; $i < $dni; $i++) {
				$arr = array();
				$arr['date'] = $today - (24 * 60 * 60 * $i);
				$balance = null;
				$fin = $this->financials->where('data', '<=', $arr['date'])->order_by('data', 'DESC')->limit(1)->find();
				if ($fin->loaded()) {
					$balance = $fin->balance;
				}

				$arr['balance'] = $balance;
				$history[] = $arr;
			}
			usort($history, "cmp_balanceHistory");
			$this->balanceHistory = json_encode($history);
			$this->save();
			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function updateProfitHistory($since = null) {
		try {
			if ($this->profitHistory == null) {
				$history = array();
			} else {
				$history = json_decode($this->profitHistory, true);
			}

			if ($since == null) {
				$since = time();
			}

			$sinceD = getDate($since);
			$day = mktime(0, 0, 0, $sinceD['mon'], $sinceD['mday'], $sinceD['year']);
			$todayD = getDate();
			$today = mktime(0, 0, 0, $todayD['mon'], $todayD['mday'], $todayD['year']);

			$elements = count($history);
			if ($elements == 0) {
				$dni = floor((time() - $day) / (24 * 60 * 60));
			} else {
				$last = $history[$elements - 1]['date'];
				$dni = 0;
				if ($last > $day) {
					$dni = floor((time() - $last) / (24 * 60 * 60));
				} else {
					$dni = floor((time() - $day) / (24 * 60 * 60));
				}
			}

			if ($dni == 0) {
				return false;
			}

			for ($i = 0; $i < $dni; $i++) {
				$arr = array();
				$arr['date'] = $today - (24 * 60 * 60 * $i);
				$od = $arr['date'] - (24 * 60 * 60);
				$balance = null;

				$fin = $this->financials->where('data', 'BETWEEN', array($od, $arr['date']))->find_all();
				$profit = 0;
				foreach ($fin as $f) {
					$profit += $f->change;
				}
				$arr['profit'] = $profit;
				$history[] = $arr;
			}
			usort($history, "cmp_balanceHistory");
			$this->profitHistory = json_encode($history);
			$this->save();
			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}
}

?>