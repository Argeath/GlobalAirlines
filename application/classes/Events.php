<?php defined('SYSPATH') or die('No direct script access.');

class Events {

	static function insertEventParams($paramVals) {
		try {
			if (!empty($paramVals)) {
				$paramValsT = implode(',', $paramVals);
				DB::query(Database::INSERT, 'INSERT INTO `event_parameters` (`event_id`, `key`, `value`) VALUES ' . $paramValsT)->execute();
				unset($paramValsT);
			}
			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __FILE__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	static function updateCheckEventTypes($type, $duration) {
		//if(Kohana::$environment == Kohana::PRODUCTION)
		//	return false;
		try {
			$eves = DB::select()->from('check_eventTypes')->where('id', '=', $type)->execute()->as_array();
			if (!empty($eves)) {
				$eves = $eves[0];
				$suma = $eves['suma'];
				$ilosc = $eves['ilosc'];
				$startOfWeek = $eves['startOfWeek'];
				if ($startOfWeek + 604800 <= time()) {
					$suma = 0;
					$ilosc = 0;
					$startOfWeek = time();
				}
				$suma += $duration;
				$ilosc++;
				DB::update('check_eventTypes')->set(array('lastTime' => time(), 'suma' => $suma, 'ilosc' => $ilosc, 'startOfWeek' => $startOfWeek))->where('id', '=', $eves['id'])->execute();
			} else {
				DB::insert('check_eventTypes')->columns(array('id', 'lastTime', 'suma', 'ilosc', 'startOfWeek'))->values(array($type, time(), $duration, 1, time()))->execute();
			}

			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __FUNCTION__ . '] ' . $e->getMessage());
		}
		return false;
	}

	static function checkEvents() {
		try {
			$start = microtime_float();
			$user = Auth::instance()->get_user();
			$userId = ($user && $user != null) ? $user->id : 0;
			$ech = DB::select()->from('check_events')->where('data', '=', time())->execute()->as_array();
			if (!empty($ech)) {
				return false;
			}

			$setting = ORM::factory("Setting")->where('key', '=', 'lock_events')->find();
			if (!$setting->loaded()) {
				$setting = ORM::factory("Setting");
				$setting->key = 'lock_events';
				$setting->value = 0;
				$setting->save();
			}

			if ($setting->value == 1) {
				return false;
			}

			$checkEventsId = 0;
			$cycles = 0;
			do {
				$needOneMore = false;
				$qs = ORM::factory("Event")->where('done', '=', 0)->and_where('when', '<=', time())->find_all();
				if ($qs->count() == 0) {
					break;
				}

				$checkEventsId = DB::insert('check_events')->columns(array('data', 'user', 'text'))->values(array(time(), $userId, json_encode($qs)))->execute();
				$checkEventsId = $checkEventsId[0];

				foreach ($qs as $q) {
					$cycles++;
					if ($q->user_id != NULL) {
						if ($q->user->loaded()) {
							// Usunięcie podwójnych eventów
							$eves = $q->user->events->where('type', '=', $q->type)->and_where('id', '!=', $q->id)->and_where('done', '=', 0)->and_where('when', '=', $q->when)->find_all();
							foreach ($eves as $eveD) {
								$paramsD = $eveD->parameters->find_all();
								if ($paramsD != $q->parameters->find_all()) {
									continue;
								}

								$eveD->done = 1;
								$eveD->save();
								errToDb('[Double event][ID: ' . $q->id . ' && ' . $eveD->id . ']');
							}
						}
					}// Koniec usuwania podwójnych eventów

					$paramValues = array();
					if ($q->type == 1)//Lot swobodny
					{
						$check_start = microtime_float();
						$flight = $q->user->flights->where('event', '=', $q->id)->find();
						if ($flight->loaded()) {
							$plane = $flight->UserPlane;
							$to = $flight->to;
							$distance = $q->parameters->where('key', '=', 'distance')->find()->value;
							$timeInAir = $q->parameters->where('key', '=', 'czas')->find()->value;

							$hours = round($timeInAir / 3600);

							//Jeżeli mechanik na pokładzie to mniejszy spadek stanu etc
							$stan = round((rand(1, 5) / 100) * $hours, 2);

							$q->user->sendMiniMessage("Samolot " . $plane->rejestracja . " dotarł do miasta " . Helper_Map::getCityName($to), "Samolot " . $plane->fullName() . " dotarł do miasta " . Helper_Map::getCityName($to) . ".<br />Spadek stanu samolotu: " . $stan . "%", 1, $flight->end);

							$plane->position = $to;
							$plane->km += $distance;
							$plane->hours += $timeInAir;
							$plane->stan -= $stan;
							$plane->save();

							$plane->updateStaffConditionToFuture($q->when);

							$q->user->km += $distance;
							$q->user->hours += $timeInAir;
							$q->user->save();
							$check_stop = microtime_float();
							Events::updateCheckEventTypes($q->type, $check_stop - $check_start);
						}
					} elseif ($q->type == 2)//Odprawa lotu swobodnego
					{
						$check_start = microtime_float();
						$planeId = $q->parameters->where('key', '=', 'plane')->find()->value;
						$to = $q->parameters->where('key', '=', 'to')->find()->value;
						$from = $q->parameters->where('key', '=', 'from')->find()->value;
						$distance = $q->parameters->where('key', '=', 'distance')->find()->value;
						$timeInAir = $q->parameters->where('key', '=', 'czas')->find()->value;
						$flightId = $q->parameters->where('key', '=', 'flight')->find()->value;

						$plane = ORM::factory("UserPlane", $planeId);
						if ($plane->loaded() && $plane->user_id == $q->user->id) {
							$flight = ORM::factory("Flight", $flightId);
							if ($flight->loaded() && $flight->user_id == $q->user->id) {
								$newEvent = ORM::factory("Event");
								$newEvent->user_id = $q->user->id;
								$newEvent->when = $q->when + $timeInAir;
								$newEvent->type = 1;
								$newEvent->save();
								$event_id = $newEvent->id;

								$flight->checked = 1;
								$flight->event = $event_id;
								$flight->save();

								$pilotow = $plane->staff->where('type', '=', 'pilot')->count_all();
								$stanZaLot = round((($timeInAir / 60) * 0.025) / $pilotow, 2);
								$plane->updateStaffConditionFuture(-$stanZaLot);

								//Parametry
								$paramValues[] = '(' . $event_id . ', "distance", ' . round($distance) . ')';
								$paramValues[] = '(' . $event_id . ', "czas", ' . round($timeInAir) . ')';
							} else {
								errToDB("[User: " . $q->user->id . "] [Event: " . $q->id . "] [Type: " . $q->type . "] [EventLoop: 2]");
							}
						} else {
							errToDB("[User: " . $q->user->id . "] [Event: " . $q->id . "] [Type: " . $q->type . "] [EventLoop: 1]");
						}

						$needOneMore = true;
						$check_stop = microtime_float();
						Events::updateCheckEventTypes($q->type, $check_stop - $check_start);
					} elseif ($q->type == 3)// Deadline zlecenia
					{
						$check_start = microtime_float();
						$zlecenieId = $q->parameters->where('key', '=', 'zlecenie')->find()->value;

						$punish = false;
						$zlecenie = ORM::factory("UserOrder", $zlecenieId);
						if ($zlecenie->loaded() && $zlecenie->user_id == $q->user->id) {
							$order = $zlecenie->order;
							if ($zlecenie->flight_id != null) {
								$flight = ORM::factory("Flight", $zlecenie->flight_id);
								if ($flight->end > $order->deadline) {
									$punish = true;
								}
							} else {
								$punish = true;
							}

							if ($punish) {
								$punish = $order->cash * 1.2;
								$msg = "Zapłaciłeś karę - " . formatCash($punish) . " " . WAL . " za zlecenie z miasta " . Helper_Map::getCityName($order->from) . " do miasta " . Helper_Map::getCityName($order->to) . ".";
								$q->user->sendMiniMessage("Zapłaciłeś karę za niewykonanie zlecenia.", $msg, $order->deadline);
								$info = array('type' => Helper_Financial::Deadline, 'order_id' => $order->id);
								$q->user->niewykonanych++;
								$q->user->operateCash(-$punish, 'Kara za niewykonanie zlecenia (' . Helper_Map::getCityName($order->from) . ' -> ' . Helper_Map::getCityName($order->to) . ').', $q->when, $info);
								$zlecenie->done = 1;
								$zlecenie->punished = 1;
								$zlecenie->save();

								if ($zlecenie->flight_id != null) {
									$flight = ORM::factory("Flight", $zlecenie->flight_id);
									$event = ORM::factory("Event", $flight->event);
									if ($event->loaded()) {
										$event->done = 0;
										$event->save();
									}
								}
							}
						}

						$check_stop = microtime_float();
						Events::updateCheckEventTypes($q->type, $check_stop - $check_start);
					} elseif ($q->type == 4)//Odprawa lotu na zlecenie
					{
						$check_start = microtime_float();
						$planeId = $q->parameters->where('key', '=', 'plane')->find()->value;
						$zlecenieId = $q->parameters->where('key', '=', 'zlecenie')->find()->value;
						$distance = $q->parameters->where('key', '=', 'distance')->find()->value;
						$timeInAir = $q->parameters->where('key', '=', 'czas')->find()->value;

						$plane = ORM::factory("UserPlane", $planeId);
						if ($plane->loaded() && $plane->user_id == $q->user->id) {
							$zlecenie = ORM::factory("UserOrder", $zlecenieId);
							if ($zlecenie->loaded() && $zlecenie->user_id == $q->user->id) {
								$flight = $q->user->flights->where('id', '=', $zlecenie->flight_id)->find();
								if ($flight->loaded()) {
									$newEvent = ORM::factory("Event");
									$newEvent->user_id = $q->user->id;
									$newEvent->when = $q->when + $timeInAir;
									$newEvent->type = 5;
									$newEvent->save();
									$event_id = $newEvent->id;

									$pilotow = $plane->staff->where('type', '=', 'pilot')->count_all();
									if ($pilotow == 0) {
										$pilotow = 1;
									}

									$stanZaMinute = 0.025; // Tyle za kazda minute lotu
									$stanZaLot = round((($timeInAir / 60) * $stanZaMinute) / $pilotow, 2);
									$plane->updateStaffConditionFuture(-$stanZaLot);

									$staffArray = array();
									foreach ($plane->staff->find_all() as $s) {
										$staffArray[] = array($s->id, $s->condition, $s->conditionFuture);
									}

									$flight->checked = 1;
									$flight->event = $event_id;
									$flight->staff = json_encode($staffArray);
									$flight->save();

									//Parametry
									$paramValues[] = '(' . $event_id . ', "distance", ' . round($distance) . ')';
									$paramValues[] = '(' . $event_id . ', "czas", ' . round($timeInAir) . ')';
									$paramValues[] = '(' . $event_id . ', "zlecenie", ' . round($zlecenieId) . ')';

									$plane->position = $zlecenie->order->to;
									$plane->save();
								} else {
									errToDB("[User: " . $q->user->id . "] [Event: " . $q->id . "] [Type: " . $q->type . "] [EventLoop: 4]");
								}
							} else {
								errToDB("[User: " . $q->user->id . "] [Event: " . $q->id . "] [Type: " . $q->type . "] [EventLoop: 3]");
							}
						} else {
							errToDB("[User: " . $q->user->id . "] [Event: " . $q->id . "] [Type: " . $q->type . "] [EventLoop: 1]");
						}

						unset($plane);
						unset($flight);

						$needOneMore = true;
						$check_stop = microtime_float();
						Events::updateCheckEventTypes($q->type, $check_stop - $check_start);
					} elseif ($q->type == 5)//Lot na zlecenie
					{
						$check_start = microtime_float();
						$flight = $q->user->flights->where('event', '=', $q->id)->find();
						if ($flight->loaded() && $flight->canceled == 0 && $flight->user_id == $q->user->id) {
							$plane = $flight->UserPlane;
							$to = $flight->to;
							$distance = $q->parameters->where('key', '=', 'distance')->find()->value;
							$timeInAir = $q->parameters->where('key', '=', 'czas')->find()->value;
							$zlecenieId = $q->parameters->where('key', '=', 'zlecenie')->find()->value;

							if ($plane->loaded() && $plane->user_id == $q->user->id) {
								$zlecenie = ORM::factory("UserOrder", $zlecenieId);
								if ($zlecenie->loaded() && $zlecenie->user_id == $q->user->id) {
									if ($zlecenie->done == 0) {
										$order = $zlecenie->order;
										$mechanik = $plane->plane->mechanicy;
										$stan = round((rand(1, 10) / 100) * ceil($timeInAir / 1800) * 2 / sqrt($mechanik), 2);

										$xp = ceil(pow($order->count+3, 0.66) * pow(($distance / 100)+1, 0.66));
										if ($xp < 5) {
											$xp = mt_rand(1, 4);
										}

										$plane->updateStaffConditionToFuture($q->when);

										$msg = 'Samolot ' . $plane->fullName() . ' dotarł do miasta ' . Helper_Map::getCityName($to) . '.<br />Zapłata: ' . formatCash($order->cash) . ' ' . WAL . '<br />Spadek stanu samolotu: ' . $stan . '%<br />Dostałeś ' . $xp . ' punktów doświadczenia.';
										$q->user->sendMiniMessage('Samolot ' . $plane->rejestracja . ' dotarł do miasta ' . Helper_Map::getCityName($to), $msg, $flight->end);

										//DB::update('events')->set(array('done' => 1))->where('type', '=', 3)->and_where('when', '=', $zlecenie->deadline)->and_where('user', '=', $q->user->id)->execute();

										$zlecenie->done = 1;
										$zlecenie->save();

										$plane->km += $distance;
										$plane->hours += $timeInAir;
										$plane->stan -= $stan;
										$plane->updateStaffExperience($xp * 2);
										$plane->updateStaffPosition();
										$plane->save();

										$q->user->km += $distance;
										$q->user->hours += $timeInAir;
										$info = array('type' => Helper_Financial::LotZlecenie, 'plane_id' => $plane->id, 'order_id' => $order->id);
										$q->user->operateCash($order->cash, 'Zapłata za zlecenie (' . Helper_Map::getCityName($order->from) . ' -> ' . Helper_Map::getCityName($order->to) . ') wykonane samolotem - ' . $plane->fullName() . '.', $q->when, $info);

										$q->user->pasazerow += $order->count;
										$q->user->zlecen++;
										$q->user->addExperience($xp, $q->when);
										$q->user->save();
									} else {
										errToDB("[User: " . $q->user->id . "] [Event: " . $q->id . "] [Type: " . $q->type . "] [EventLoop: 4]");
									}
								} else {
									errToDB("[User: " . $q->user->id . "] [Event: " . $q->id . "] [Type: " . $q->type . "] [EventLoop: 2]");
								}
							} else {
								errToDB("[User: " . $q->user->id . "] [Event: " . $q->id . "] [Type: " . $q->type . "] [EventLoop: 1]");
							}

							unset($plane);
						} else {
							errToDB("[User: " . $q->user->id . "] [Event: " . $q->id . "] [Type: " . $q->type . "] [EventLoop: 3]");
						}

						// Possibly Double events cause error.
						unset($flight);

						$check_stop = microtime_float();
						Events::updateCheckEventTypes($q->type, $check_stop - $check_start);

					} elseif ($q->type == 6)//Usunięcie konta
					{
						$check_start = microtime_float();
						$q->user->delete();
						$check_stop = microtime_float();
						Events::updateCheckEventTypes($q->type, $check_stop - $check_start);

					} elseif ($q->type == 7)//Lot swobodny pracownika
					{
						$check_start = microtime_float();

						$pracId = $q->parameters->where('key', '=', 'pracId')->find()->value;
						$to = $q->parameters->where('key', '=', 'to')->find()->value;
						$planeId = $q->parameters->where('key', '=', 'plane')->find();
						if ($planeId->loaded()) {
							$planeId = $planeId->value;
						}

						$prac = ORM::factory("Staff", $pracId);
						$prac->position = $to;

						if ($planeId) {
							$plane = ORM::factory("UserPlane", $planeId);
							$planeModel = $plane->plane;
							$pilotow = $planeModel->piloci;
							$dodatkowej = $planeModel->zaloga_dodatkowa;
							$juzPilotow = $plane->staff->where('type', '=', 'pilot')->count_all();
							$juzDodatkowej = $plane->staff->where('type', '!=', 'pilot')->count_all();
							if ((($prac->type == 'pilot' && $juzPilotow < $pilotow) || ($prac->type == 'stewardessa' && $juzDodatkowej < $dodatkowej)) && ($prac->position == $plane->position) && ($prac->user_id == $plane->user_id)) {
								$prac->plane_id = $plane->id;
								$prac->save();
							}
							unset($plane);
						}
						$prac->save();
						unset($prac);

						$check_stop = microtime_float();
						Events::updateCheckEventTypes($q->type, $check_stop - $check_start);

					} elseif ($q->type == 8)//Obliczanie ceny paliwa
					{
						$check_start = microtime_float();
						$new = Helper_Oil::calculateOilCost();

						$mysqldate = date("Y-m-d H:i:s", $q->when);
						$duplicateOil = DB::select()->from('oil')->where('data', '=', $mysqldate)->execute()->as_array();
						if (empty($duplicateOil)) {
							DB::insert('oil')->columns(array('data', 'cena'))->values(array($mysqldate, $new))->execute();
						}

						$checkEvent = ORM::factory("Event")->where('when', '=', $q->when + 3600)->and_where('type', '=', 8)->count_all();
						if ($checkEvent == 0) {
							$newEvent = ORM::factory("Event");
							$newEvent->when = $q->when + 3600;
							$newEvent->type = 8;
							$newEvent->save();
						}

						$needOneMore = true;
						$check_stop = microtime_float();
						Events::updateCheckEventTypes($q->type, $check_stop - $check_start);

					} elseif ($q->type == 9)//Przeglad generalny
					{
						$check_start = microtime_float();
						$planeId = $q->parameters->where('key', '=', 'plane')->find()->value;
						$plane = ORM::factory("UserPlane", $planeId);
						if ($plane->loaded() && $plane->user_id == $q->user->id) {
							$stan = $plane->stan;
							$xp = round((100 - $stan) * 2);

							$msg = 'Samolot ' . $plane->fullName() . ' przeszedł przegląd generalny i został naprawiony.<br />Dostałeś ' . $xp . ' punktów doświadczenia.';
							$q->user->sendMiniMessage('Samolot ' . $plane->rejestracja . ' przeszedł przegląd generalny i został naprawiony.', $msg, $q->when);

							$plane->stan = 100;
							$plane->save();
							$q->user->exp += $xp;
							$q->user->save();
						} else {
							errToDB("[User: " . $q->user->id . "] [Event: " . $q->id . "] [Type: " . $q->type . "] [EventLoop: 1]");
						}

						unset($plane);
						$check_stop = microtime_float();
						Events::updateCheckEventTypes($q->type, $check_stop - $check_start);

					} elseif ($q->type == 10)//Rozpoczęcie odprawy zlecenia
					{
						$check_start = microtime_float();
						$planeId = $q->parameters->where('key', '=', 'plane')->find()->value;
						$distance = $q->parameters->where('key', '=', 'distance')->find()->value;
						$czas = $q->parameters->where('key', '=', 'czas')->find()->value;
						$zlecenieId = $q->parameters->where('key', '=', 'zlecenie')->find()->value;
						$paliwo = $q->parameters->where('key', '=', 'paliwo')->find()->value;
						$to = $q->parameters->where('key', '=', 'to')->find()->value;
						$odprawa = $q->parameters->where('key', '=', 'odprawa')->find()->value;

						$plane = ORM::factory("UserPlane", $planeId);
						if ($plane->loaded() && $plane->user_id == $q->user->id) {
							$zlecenie = ORM::factory("UserOrder", $zlecenieId);
							if ($zlecenie->loaded() && $zlecenie->user_id == $q->user->id && $zlecenie->done == 0) {
								$order = $zlecenie->order;
								$flight = $q->user->flights->where('id', '=', $zlecenie->flight_id)->find();
								if ($flight->loaded() && $flight->user_id == $q->user->id) {
									if ($plane->isBusy($q->when, $zlecenie->flight_id) == Helper_Busy::NotBusy && $plane->position == $order->from) {
										$accidentChance = $plane->getAccidentChance() * 100;
										$rand = rand(0, 10000);
										if ($rand <= $accidentChance) {
											$accident = $plane->doAccident($flight);
											$accident->odprawa_id = $q->id;
											$accident->save();
										}

										$newEvent = ORM::factory("Event");
										$newEvent->user_id = $q->user->id;
										$newEvent->when = $q->when + $odprawa;
										$newEvent->type = 4;
										$newEvent->save();
										$event_id = $newEvent->id;
										$flight->checked = 1;
										$flight->save();

										//Parametry
										$paramValues[] = '(' . $event_id . ', "distance", ' . round($distance) . ')';
										$paramValues[] = '(' . $event_id . ', "czas", ' . round($czas) . ')';
										$paramValues[] = '(' . $event_id . ', "plane", ' . round($planeId) . ')';
										$paramValues[] = '(' . $event_id . ', "zlecenie", ' . round($zlecenieId) . ')';
										$paramValues[] = '(' . $event_id . ', "paliwo", ' . round($paliwo) . ')';
										$paramValues[] = '(' . $event_id . ', "to", ' . round($to) . ')';
										$needOneMore = true;
									} elseif (!$q->user->isBot) {
										if ($flight->delayed >= 60) {
											$err = "";
											if ($plane->position != $order->from) {
												$err = "Samolot nie stawił się w miejscu startu";
											}

											$flight->cancel();
											$q->user->sendMiniMessage('Samolot ' . $plane->rejestracja . ' nie wystartował.', 'Samolot ' . $plane->fullName() . ' nie wystartował z ' . Helper_Map::getCityName($order->from) . ' do ' . Helper_Map::getCityName($order->to) . '. ' . $err, $q->when);
										} else {
											//$delay = ($flight->delayed == 0) ? 1 : $flight->delayed;
											//if($delay > 5)
											$delay = 5;
											$flight->delayed += $delay;
											$flight->started += $delay * 60;
											$flight->end += $delay * 60;
											$flight->save();
											$q->when += $delay * 60;
											$q->save();
											//$q->user->sendMiniMessage('Samolot '.$plane->rejestracja.' ma opóźnienie.', 'Lot samolotu '.$plane->fullName().' z '.Map::getCityName($zlecenie->from).' do '.Map::getCityName($zlecenie->to).' ma opóźnienie '.$flight->delayed.' minut.', 1, $q->when);
											$needOneMore = true;
											continue;
										}
									} else {
										$flight->cancelQuietly();
									}
								} else {
									errToDB("[User: " . $q->user->id . "] [Event: " . $q->id . "] [Type: " . $q->type . "] [EventLoop: 4]");
								}
							} else {
								errToDB("[User: " . $q->user->id . "] [Event: " . $q->id . "] [Type: " . $q->type . "] [EventLoop: 2] [Possibly punished]");
							}
						} else {
							errToDB("[User: " . $q->user->id . "] [Event: " . $q->id . "] [Type: " . $q->type . "] [EventLoop: 1]");
						}

						unset($plane);
						$check_stop = microtime_float();
						Events::updateCheckEventTypes($q->type, $check_stop - $check_start);

					} elseif ($q->type == 11)//Rozpoczęcie odprawy lotu swobodnego
					{
						$check_start = microtime_float();

						$planeId = $q->parameters->where('key', '=', 'plane')->find()->value;
						$distance = $q->parameters->where('key', '=', 'distance')->find()->value;
						$czas = $q->parameters->where('key', '=', 'czas')->find()->value;
						$paliwo = $q->parameters->where('key', '=', 'paliwo')->find()->value;
						$to = $q->parameters->where('key', '=', 'to')->find()->value;
						$odprawa = $q->parameters->where('key', '=', 'odprawa')->find()->value;
						$from = $q->parameters->where('key', '=', 'from')->find()->value;
						$flight = $q->parameters->where('key', '=', 'flight')->find()->value;

						$plane = $q->user->UserPlanes->where('id', '=', $planeId)->find();
						if ($plane->loaded() && $plane->isBusy() == Helper_Busy::NotBusy && $plane->position == $from) {
							$newEvent = ORM::factory("Event");
							$newEvent->user_id = $q->user->id;
							$newEvent->when = $q->when + $odprawa;
							$newEvent->type = 2;
							$newEvent->save();
							$event_id = $newEvent->id;

							//Parametry
							$paramValues[] = '(' . $event_id . ', "distance", ' . round($distance) . ')';
							$paramValues[] = '(' . $event_id . ', "czas", ' . round($czas) . ')';
							$paramValues[] = '(' . $event_id . ', "plane", ' . round($planeId) . ')';
							$paramValues[] = '(' . $event_id . ', "flight", ' . round($flight) . ')';
							$paramValues[] = '(' . $event_id . ', "paliwo", ' . round($paliwo) . ')';
							$paramValues[] = '(' . $event_id . ', "to", ' . round($to) . ')';
							$paramValues[] = '(' . $event_id . ', "from", ' . round($from) . ')';
						} elseif ($plane->loaded()) {
							$q->user->sendMiniMessage('Samolot ' . $plane->rejestracja . ' nie wystartował.', 'Samolot ' . $plane->fullName() . ' nie wystartował podczas lotu z ' . Helper_Map::getCityName($order->from) . ' do ' . Helper_Map::getCityName($order->to) . ', ponieważ nie stawił się na miejsce startu.', 1, $q->when);
						} else {
							errToDB("[User: " . $q->user->id . "] [Event: " . $q->id . "] [Type: " . $q->type . "] [EventLoop: 1]");
						}

						unset($plane);
						$check_stop = microtime_float();
						Events::updateCheckEventTypes($q->type, $check_stop - $check_start);

					} elseif ($q->type == 12)//Aukcja
					{
						$check_start = microtime_float();
						$auctionId = $q->parameters->where('key', '=', 'auction')->find()->value;

						$auction = ORM::factory('Auction', $auctionId);
						if ($auction->loaded()) {
							if ($auction->canceled == 0) {
								$highest = $auction->getHighestBid();
								if ($highest)//Samolot sprzedany
								{
									$auction->UserPlane->user_id = $highest->user_id;
									$auction->UserPlane->save();
									$msg = 'Wygrałeś licytacje za ' . $highest->price . '. Samolot od teraz należy do ciebie.';
									$highest->user->sendMiniMessage('Wygrałeś licytacje.', $msg, $q->when);
								} else {
									$msg = 'Nie udało ci się sprzedać samolotu na aukcjach.';
									$q->user->sendMiniMessage('Nie udało ci się sprzedać samolotu.', $msg, $q->when);
								}
							}
						} else {
							errToDB("[Event: " . $q->id . "] [Type: " . $q->type . "] [EventLoop: 1]");
						}

						unset($auction);
						$check_stop = microtime_float();
						Events::updateCheckEventTypes($q->type, $check_stop - $check_start);

					} elseif ($q->type == 13)// Regeneracja stanu zalogi
					{
						            /*$check_start = microtime_float();

					$zal = ORM::factory("Staff")->where('condition', '<', 100)->find_all();
					foreach($zal as $z)
					{
					if(!$z->isPracBusy($q->when))
					$z->regenerateCondition($q->when);
					}
					                        $checkEvent = ORM::factory("Event")->where('when' ,'=', $q->when + 900)->and_where('type', '=', 13)->count_all();
					                        if( $checkEvent == 0) {
					                            $newEvent = ORM::factory("Event");
					                            $newEvent->when = $q->when + 900;
					                            $newEvent->type = 13;
					                            $newEvent->save();
					                            unset($newEvent);
					                        }
					unset($zal);

					$check_stop = microtime_float();
					Events::updateCheckEventTypes($q->type, $check_stop - $check_start);
					$needOneMore = true;*/
					} elseif ($q->type == 14)// Awaria
					{
						$check_start = microtime_float();
						$accidentId = $q->parameters->where('key', '=', 'accident')->find()->value;

						$accident = ORM::factory("Accident", $accidentId);
						if ($accident->loaded() && $accident->user_id == $q->user_id) {
							$typ = $accident->getAccidentInfo();
							$efekt = $accident->getEffectInfo();
							$plane = $accident->plane;
							$flight = $accident->flight;

							$time = round($accident->delay / 1800) * 2;

							if ($typ['when'] == 0)// Przed startem
							{
								$plane->stan -= $accident->condition;
								$plane->save();
								$q->user->sendMiniMessage("Awaria", "Samolot " . $plane->fullName() . " miał awarię przed startem. Rozpoczęto naprawę - powinna zająć około " . $time . "h.", $q->when);
							} elseif ($typ['when'] == 1) {
								$plane->position = $flight->from;
								$plane->stan -= $accident->condition;
								$plane->save();
								$q->user->sendMiniMessage("Awaria", "Samolot " . $plane->fullName() . " miał awarię tuż po starcie i lądował awaryjnie na tym samym lotnisku. Rozpoczęto naprawę - powinna zająć około " . $time . "h.", $q->when);
							} elseif ($typ['when'] == 2) {
								$czasLotu = $flight->end - $flight->started;
								$czasAwarii = $accident->time - $flight->started;
								$przelecial = round($czasAwarii / $czasLotu * Helper_Map::getDistanceBetween($flight->from, $flight->to));
								$city = Helper_Map::findCityOnPath($flight->from, $flight->to, $przelecial);
								if (!$city) {
									// TODO: Crash
								} else {
									$plane->position = $city->id;
								}
								$plane->stan -= $accident->condition;
								$plane->save();
								$q->user->sendMiniMessage("Awaria", "Samolot " . $plane->fullName() . " miał awarię tuż po starcie i lądował awaryjnie na lotnisku - " . $city->name . ". Rozpoczęto naprawę - powinna zająć około " . $time . "h.", $q->when);
							}

							if ($accident->delay > 3600) {
								$flight->cancel();
							} else {
								$oldEvent = ORM::factory("Event", $accident->odprawa_id);
								if (!$oldEvent->loaded()) {
									$flight->cancel();
								} else {
									$newEvent = ORM::factory("Event");
									$newEvent->user_id = $q->user_id;
									$newEvent->when = $q->when + 60;
									$newEvent->type = 10;
									$newEvent->save();

									$event_id = $newEvent->id;

									$distance = $oldEvent->parameters->where('key', '=', 'distance')->find()->value;
									$czas = $oldEvent->parameters->where('key', '=', 'czas')->find()->value;
									$zlecenieId = $oldEvent->parameters->where('key', '=', 'zlecenie')->find()->value;
									$paliwo = $oldEvent->parameters->where('key', '=', 'paliwo')->find()->value;
									$to = $flight->to;
									$odprawa = $oldEvent->parameters->where('key', '=', 'odprawa')->find()->value;

									$params = array();
									$params[] = '(' . $event_id . ', "distance", ' . round($distance) . ')';
									$params[] = '(' . $event_id . ', "czas", ' . round($czas) . ')';
									$params[] = '(' . $event_id . ', "plane", ' . round($plane->id) . ')';
									$params[] = '(' . $event_id . ', "zlecenie", ' . round($zlecenieId) . ')';
									$params[] = '(' . $event_id . ', "paliwo", ' . round($paliwo) . ')';
									$params[] = '(' . $event_id . ', "to", ' . round($to) . ')';
									$params[] = '(' . $event_id . ', "odprawa", ' . round($odprawa) . ')';
									Events::insertEventParams($params);
								}
							}
						} else {
							errToDB("[Event: " . $q->id . "] [Type: " . $q->type . "] [EventLoop: 1]");
						}

						$check_stop = microtime_float();
						Events::updateCheckEventTypes($q->type, $check_stop - $check_start);
						$needOneMore = true;
					} elseif ($q->type == 15)//Boty
					{
						$check_start = microtime_float();

						$q->user->doBotStuff($q->when);

						$check_stop = microtime_float();
						Events::updateCheckEventTypes($q->type, $check_stop - $check_start);

					} else {
						//Nieobslugiwany typ
						{
							errToDb('[Exception][EventLoop][Unknown type: ' . $q->type . ']');
						}
					}

					Events::insertEventParams($paramValues);
					unset($paramValues);

					$q->done = 1;
					$q->save();
				}
				{
					// Sprawdzenie przypadkowego przerwania oleju lub regeneracji załogi
					$lastOil = ORM::factory("Event")->where('type', '=', 8)->order_by('when', 'DESC')->find();
					if ($lastOil->loaded()) {
						if ($lastOil->when < time() - 3650) {
							$newEvent = ORM::factory("Event");
							$newEvent->when = $lastOil->when + 3600;
							$newEvent->type = 8;
							$newEvent->save();
							$needOneMore = true;
						}
					}
					unset($lastOil);
				}
			} while ($needOneMore);

			$stop = microtime_float();
			$duration = $stop - $start;
			if ($checkEventsId != 0) {
				if ($cycles > 0) {
					DB::update('check_events')->set(array('time' => $duration, 'cycles' => $cycles))->where('id', '=', $checkEventsId)->execute();
				} else {
					DB::delete('check_events')->where('id', '=', $checkEventsId)->execute();
				}
			}

			DB::delete('events')->and_where('when', '<', time() - 604800)->and_where('done', '=', 1)->execute();
			DB::delete('events')->and_where('when', '<', time() - 86400)->and_where('type', '=', 8)->execute();

			$setting->value = 0;
			$setting->save();

		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
	}
};