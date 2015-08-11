<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Sklep extends Controller_Template {

	public function action_index() {

		$this->template->title = "Sklep";

		$this->template->content = View::factory('hangar/sklep')
		     ->bind('activeKlasa', $activeKlasa)
		     ->bind('klasa', $klasa)
		     ->bind('action', $action)
		     ->bind('klasyText', $klasyText)
		     ->bind('ilosc', $ilosc)
		     ->bind('naStrone', $naStrone)
		     ->bind('strona', $strona)
		     ->bind('offset', $offset)
             ->bind('warning', $warning)
		     ->bind('planesData', $planesData);

		$user = Auth::instance()->get_user();
		if (!$user)
			$this->redirect('user/login');

		$action = "index";
		$this->klasy = (array) Kohana::$config->load('classes');
		$this->klasyKeys = array_keys($this->klasy);
		$klasa = (int) $this->request->param('klasa');
		if (!isset($this->klasyKeys[$klasa])) {
			$klasa = 0;
		}

		$klasyText = "";
		foreach ($this->klasyKeys as $k => $v) {
			$klasyText .= '<li ' . (($k == $klasa) ? "class='active'" : "") . '>' . HTML::anchor('sklep/' . $k . '/' . $action, $v) . '</li>';
		}

		$strona = (int) $this->request->param('id');

		if ($strona > 0) {
			$strona--;
		}

		$naStrone = 20;
		$ilosc = 0;

		$offset = $strona * $naStrone;

        $lvls = Kohana::$config->load('lvls')['biura'];
        $requiredLvl = $lvls[$klasa+1];

		$planes = ORM::factory("Plane")->where('klasa', '=', $klasa + 1)->and_where('ukryty', '=', 0)->order_by("cena", "ASC")->find_all();

		$warning = '. Uwaga! Ten parametr odbiega od normy tej klasy samolotów. Może to spowodować podwyższenie kosztów eksloatacji.';

		$planesData = [];

		foreach ($planes as $plane) {
			$planesData[] = [
				'plane' => $plane,
                'level' => $user->getLevel(),
                'requiredLevel' => $requiredLvl
			];
		}

		$post = $this->request->post();
		if ($post && !empty($post)) {
			if ($post['action'] == "buy_wal" && (int) $post['plane'] > 0) {
				$plane = ORM::factory("Plane", (int) $post['plane']);
				if (!empty($plane)) {
                    $lvls = Kohana::$config->load('lvls')['biura'];
                    $requiredLvl = $lvls[$klasa+1];
                    if($user->getLevel() >= $requiredLvl) {
                        if ($user->cash >= $plane->cena) {
                            $p = ORM::factory("UserPlane");
                            $p->user_id = $user->id;
                            $p->plane_id = $plane->id;
                            $p->position = $user->base->city->id;
                            $p->save();
                            $info = array('type' => Financial::Sklep, 'plane_id' => $p->id);
                            $user->operateCash(-$plane->cena, 'Zakup samolotu - ' . $plane->producent . ' ' . $plane->model . '.', time(), $info);
                            sendMsg("Kupiłeś " . $plane->producent . " " . $plane->model . " za " . formatCash($plane->cena) . " " . WAL . ".");
                            $this->redirect('samoloty/rejestracja/' . $p->id);
                        } else {
                            sendError('Nie masz wystarczającej ilości pieniędzy.');
                        }
                    } else {
                        sendError("Nie osiągnąłeś wystarczającego poziomu konta.");
                    }
				} else {
					sendError('Nie ma takiego samolotu o id: ' . (int) $post['plane'] . '.');
				}
			} else if ($post['action'] == "buy_pkt" && (int) $post['plane'] > 0) {
				$plane = ORM::factory("Plane", (int) $post['plane']);
				if (!empty($plane)) {
                    $lvls = Kohana::$config->load('lvls')['biura'];
                    $requiredLvl = $lvls[$klasa+1];
                    if($user->getLevel() >= $requiredLvl) {
                        $cena = round(round(sqrt($plane->cena) / 40) * 20);
                        if ($user->premium_points >= $cena) {
                            $p = ORM::factory("UserPlane");
                            $p->user_id = $user->id;
                            $p->plane_id = $plane->id;
                            $p->position = $user->base->city->id;
                            $p->save();
                            $info = array('type' => Financial::Sklep, 'plane_id' => $p->id);
                            $user->operatePoints(-$cena, 'Zakup samolotu - ' . $plane->producent . ' ' . $plane->model . '.', time(), $info);
                            sendMsg("Kupiłeś " . $plane->producent . " " . $plane->model . " za " . formatCash($cena) . " PP.");
                            $this->redirect('samoloty/rejestracja/' . $p->id);
                        } else {
                            sendError('Nie masz wystarczającej ilości pieniędzy.');
                        }
                    } else {
                        sendError("Nie osiągnąłeś wystarczającego poziomu konta.");
                    }
				} else {
					sendError('Nie ma takiego samolotu.');
				}
			}
		}
	}

	public function action_aukcje() {
		$this->template->title = "Aukcje";

		$this->template->content = View::factory('hangar/sklep')
		     ->bind('activeKlasa', $activeKlasa)
		     ->bind('klasa', $klasa)
		     ->bind('action', $action)
		     ->bind('klasyText', $klasyText)
		     ->bind('ilosc', $ilosc)
		     ->bind('naStrone', $naStrone)
		     ->bind('strona', $strona)
		     ->bind('offset', $offset)
		     ->bind('samoloty', $samoloty);

		$user = Auth::instance()->get_user();
		if (!$user)
			$this->redirect('user/login');

		$action = "aukcje";
		$this->klasy = (array) Kohana::$config->load('classes');
		$this->klasyKeys = array_keys($this->klasy);
		$klasa = (int) $this->request->param('klasa');
		if (!isset($this->klasyKeys[$klasa]) && $klasa != 15 && $klasa != 16) {
			$klasa = 0;
		}

		$klasyText = "";
		foreach ($this->klasyKeys as $k => $v) {
			$klasyText .= '<li ' . (($k == $klasa) ? "class='active'" : "") . '>' . HTML::anchor('sklep/' . $k . '/' . $action, $v) . '</li>';
		}

		$post = $this->request->post();
		if ($post && !empty($post)) {
			if ($post['action'] == "Licytuj" && (int) $post['auction'] > 0) {
				$auctionId = (int) $post['auction'];
				$price = (int) $post['price'];
				$auction = ORM::factory("Auction", $auctionId);
				if ($auction->loaded()) {
					$plane = $auction->UserPlane;
					if ($user->id != $auction->user->id) {
						if ($auction->end > time()) {
							$highest = $auction->getHighestBid();
							$minim = $auction->minprice;
							if ($highest) {
								$minim = $highest->price * 1.01;
							}

							if ($price >= $minim) {
								if ($user->cash >= $price) {
									$info = array('type' => Financial::AukcjaZaplata, 'auction_id' => $auction->id);
									$user->operateCash(-$price, 'Licytacja samolotu - ' . $plane->fullName(false) . '.');

									$post = ORM::factory('AuctionPost');
									$post->user = $user;
									$post->auction = $auction;
									$post->price = $price;
									$post->save();
									sendMsg('Zalicytowaleś.');

									if ($highest) {
										$info = array('type' => Financial::AukcjaZwrot, 'auction_id' => $auction->id);
										$highest->user->operateCash($post->price, 'Zwrot z niewygranej aukcji.', time(), $info);
										$long = "Zostałeś przelicytowany na aukcji (" . $plane->fullName(false) . "). Twoje pieniądze wracają do ciebie.";
										$highest->user->sendMiniMessage("Zostałeś przelicytowany.", $long);
									}
									$this->redirect("sklep/" . $klasa . "/aukcje");
								} else {
									sendError('Nie masz wystarczającej ilości pieniędzy.');
								}
							} else {
								sendError('Musisz zalicytować za więcej niż wynosi aktualna najwyższa oferta');
							}
						} else {
							sendError('Aukcja jest juz skonczona.');
						}
					} else {
						sendError('Nie możesz licytować swoich własnych aukcji.');
					}
				}
			} elseif ($post['action'] == "Anuluj" && (int) $post['auction'] > 0) {
				$auctionId = (int) $post['auction'];
				$auction = ORM::factory("Auction", $auctionId);
				if ($auction->loaded()) {
					$plane = $auction->UserPlane;
					if ($user->id != $auction->user->id) {
						if ($auction->end > time()) {
							$auction->canceled = 1;
							$auction->save();

							$planeName = $auction->UserPlane->fullName();

							$long = "Aukcja (" . $planeName . "), którą licytowałeś została odwołana przez wystawiającego.";

							$posts = $auction->auctionPosts->where('user_id', '!=', $user->id)->find_all();
							foreach ($posts as $p) {
								$p->user->sendMiniMessage("Aukcja została odwołana.", $long);
							}

							$highest = $auction->getHighestBid();
							if ($highest) {
								$info = array('type' => Financial::AukcjaZwrot, 'auction_id' => $auction->id);
								$post->user->operateCash($highest->price, 'Zwrot z anulowanej aukcji.', time(), $info);
							}
						}
					}
				}
			} elseif ($post['action'] == "Przypnij" && (int) $post['auction'] > 0) {
				$auctionId = (int) $post['auction'];
				$auction = ORM::factory("Auction", $auctionId);
				if ($auction->loaded()) {
					$plane = $auction->UserPlane;
					if ($auction->end > time() && $user->isAdmin()) {
						$auction->pinned = 1;
						$auction->save();

						sendMsg('Przypiąłeś licytacje.');
					}
				}
			} elseif ($post['action'] == "Odepnij" && (int) $post['auction'] > 0) {
				$auctionId = (int) $post['auction'];
				$auction = ORM::factory("Auction", $auctionId);
				if ($auction->loaded()) {
					$plane = $auction->UserPlane;
					if ($auction->end > time() && $user->isAdmin()) {
						$auction->pinned = 0;
						$auction->save();

						sendMsg('Odpiąłeś licytacje.');
					}
				}
			}
		}

		$strona = (int) $this->request->param('id');

		if ($strona > 0) {
			$strona--;
		}

		$naStrone = 20;
		$ilosc = 0;

		$offset = $strona * $naStrone;

		if ($klasa == 15) {
			$auctions = $user->auctions->order_by('pinned', 'DESC')->order_by('end', 'DESC')->find_all();
		} elseif ($klasa == 16) {
			$auctions = ORM::factory("Auction")->where('end', '<', time())->order_by('pinned', 'DESC')->order_by('end', 'DESC')->limit($naStrone)->offset($offset)->find_all();
		} else {
			$auctions = array();
			$q = ORM::factory("Auction")->where('end', '>=', time())->order_by('pinned', 'DESC')->order_by('end', 'ASC')->limit($naStrone)->offset($offset)->find_all();
			foreach ($q as $a) {
				$plane = $a->UserPlane->plane;
				if ($plane->klasa == $klasa + 1) {
					$auctions[] = $a;
				}
			}
		}

		$samoloty = "";
		$ilosc = count($auctions);

        // TODO: Move HTML to view

		foreach ($auctions as $a) {
			$usr = $a->user;
			$highest = $a->getHighestBid();
			$highestBid = "";
			$highestBidder = "";
			$minim = $a->minprice;
			$toEnd = $a->end - time();
			if ($highest != NULL) {
				$highestBid = formatCash($highest->price) . " " . WAL;
				$highestBidder = $highest->user->username;
				$minim = $highest->price * 1.01;
			}
			$pinned = ($a->pinned == 1) ? " class='pinned'" : "";

			$p = $a->UserPlane;
			if (!$p->loaded() && $highest != NULL) {
				$samoloty .= "<tr>
					<td>" . $usr->drawButton() . "<br />Nieznany</td>
					<td></td>
					<td class='text-left'>Cena minimalna: " . formatCash($a->minprice) . " " . WAL . "<br />";
				if ($highest != NULL) {
					$samoloty .= "Wygrał: " . $highestBidder . "<br />
						Cena: " . $highestBid . "<br />";
				} else {
					$samoloty .= "Brak licytujących.<br />";
				}
				$samoloty .= "<br />
						Koniec: " . $a->getEndDate() . "<br />
					</td>
					<td></td>
					</tr>";
				continue;
			} elseif (!$p->loaded()) {
				continue;
			}

			$wartosc = $p->getCost();
			$plane = $p->getUpgradedModel();
			$klasaPlane = (isset($this->klasyKeys[$plane->klasa - 1])) ? $this->klasyKeys[$plane->klasa - 1] : "";
			$samoloty .= "<tr" . $pinned . ">
				<td><img src='" . URL::base(TRUE) . "assets/samoloty/" . $plane->id . ".jpg' class='img-rounded hidden-xs' style='width: 150px;'/><br />" . $usr->drawButton() . "<br />" . $plane->producent . " " . $plane->model . "</td>
				<td>
				    <div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Klasa samolotu' style='display:inline-block;width: 160px; margin-bottom: 30px;'>" . $klasaPlane . "</div>
					<div class='text-rounded bg-blue Jtooltip inline' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Zasięg samolotu' style='display:inline-block;width: 120px; margin-bottom: 30px;'><i class='fa fa-arrows-h'></i> " . $plane->zasieg . " km</div>
					<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Miejsca pasażerskie' style='display:inline-block;width: 70px; margin-bottom: 30px;'><i class='fa fa-users'></i> " . $plane->miejsc . "</div>
					<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Spalanie silników' style='display:inline-block;width: 120px; margin-bottom: 30px;'><i class='fa fa-fire'></i> " . $plane->spalanie . " kg/km</div>
					<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Prędkość maksymalna' style='display:inline-block;width: 120px; margin-bottom: 30px;'><i class='fa fa-tachometer'></i> " . $plane->predkosc . " km/h</div>
				</td>
				<td>
					<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Wymaganych pilotów' style='display:inline-block;width: 70px; margin-bottom: 30px;'><i class='fa fa-user'></i> " . $plane->piloci . "</div>
					<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Wymaganych stewardess' style='display:inline-block;width: 70px; margin-bottom: 30px;'><i class='fa fa-female'></i> " . $plane->zaloga_dodatkowa . "</div>
					<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Mnożnik serwisowy' style='display:inline-block;width: 70px; margin-bottom: 30px;'><i class='glyphicon glyphicon-wrench'></i> x" . $plane->mechanicy . "</div>
				</td>
				<td>
					" . $p->drawConditionBar() . "<br />
					Pokonana trasa: " . formatCash($p->km) . " km<br />
					Czasu w powietrzu: " . TimeFormat::secondsToText($p->hours) . "<br />
					Maksymalna wartość samolotu: " . formatCash($wartosc) . " " . WAL . "
				</td>


				<td class='text-left'>Cena minimalna: " . formatCash($a->minprice) . " " . WAL . "<br />";
			if ($highest != NULL) {
				$samoloty .= "Cena wygrywająca: " . $highestBid . "<br />
					Wygrywający: " . $highestBidder . "<br />";
			} else {
				$samoloty .= "Brak licytujących.<br />";
			}
			$samoloty .= "<br />
					Koniec: " . $a->getEndDate() . "<br />";

			if ($toEnd > 0) {
				$samoloty .= "<br /><span class='zegarCountdown text-rounded bg-blue' czas='" . $a->end . "' now='" . time() . "'>" . TimeFormat::secondsToText($toEnd) . "</span>";
			}

			$samoloty .= "</td><td>";

			if ($toEnd > 0 && $usr->id != $user->id) {
				$samoloty .= "<form method='post'><input type='hidden' name='auction' value='" . $a->id . "'/><input type='text' name='price' class='form-control' placeholder='min. " . formatCash($minim) . "'/> <input type='submit' name='action' value='Licytuj' class='btn btn-primary btn-block'/></form>";
			}

			if ($toEnd > 0 && $usr->id == $user->id) {
				$samoloty .= "<form method='post'><input type='hidden' name='auction' value='" . $a->id . "'/><input type='submit' name='action' value='Anuluj' class='btn btn-danger btn-block'/></form>";
			}

			if ($toEnd > 0 && $user->isAdmin() && $a->pinned == 0) {
				$samoloty .= "<form method='post'><input type='hidden' name='auction' value='" . $a->id . "'/><input type='submit' name='action' value='Przypnij' class='btn btn-success btn-block'/></form>";
			} elseif ($toEnd > 0 && $user->isAdmin() && $a->pinned == 1) {
				$samoloty .= "<form method='post'><input type='hidden' name='auction' value='" . $a->id . "'/><input type='submit' name='action' value='Odepnij' class='btn btn-success btn-block'/></form>";
			}

			$samoloty .= "</td>
				</tr>";

		}
		if (empty($samoloty)) {
			$samoloty = "<tr><td colspan='6'>Brak</td></tr>";
		}
	}
}
