<?php defined('SYSPATH') or die('No direct script access.');

class Helper_Menu {
	static function show() {
		$user = Auth::instance()->get_user();
		GlobalVars::$logged = false;
		if ($user) {
			GlobalVars::$logged = true;
			GlobalVars::$menu_zlecen = $user->menuZlecen();
			GlobalVars::$nowych_powiadomien = $user->nowychPowiadomien();
			GlobalVars::$nowych_kontaktow = $user->nowychKontaktow();
			GlobalVars::$nowych_wiadomosci = $user->nowychWiadomosci();
			GlobalVars::$bazy = $user->bazy();
		}

		$secure_connection = false;
		if (isset($_SERVER['HTTPS'])) {
			if ($_SERVER["HTTPS"] == "on") {
				$secure_connection = true;
			}
		}
		$protocol = ($secure_connection) ? 'https' : 'http';

		$FB = Facebook::instance();
		if (!Facebook::isCanvas()) {
			$fb_logoutPath = $FB->getLogoutUrl(URL::base($protocol) . 'user/logout');
		}

		echo '<nav class="new-menu" id="menu" onselectstart="return false" onselect="return false">
            <ul>';

		$menus = Kohana::$config->load('menu');
		if (GlobalVars::$logged) {
			$menus = $menus['login'];
		} else {
			$menus = $menus['logout'];
		}

		foreach ($menus as $name => $param) {
			$categoryName = "";
			$categoryUrl = "";
			if (is_array($param)) {
				echo '<li>';
				echo '<ul>';
				$badges = 0;
				foreach ($param as $pname => $pparam) {
					if ($pname == 'name') {
						$categoryName = $pparam;
					}
					if ($pname == 'url') {
						$categoryUrl = $pparam;
					}
					if (is_array($pparam)) {
						if (isset($pparam['function'])) {
							if ($pparam['function'] == 'bazy') {
								foreach (GlobalVars::$bazy as $b) {
									echo '<li><div>' . HTML::anchor('airport/office/' . $b->id, $b->city->code) . '<small>' . $b->city->name . '</small></div></li>';
								}
							} elseif ($pparam['function'] == 'find_city') {
								echo Form::open('airport/find');
								echo '<div class="form-group row">
										<div class="col-xs-offset-1 col-xs-7" style="padding:0;">
											<input type="text" name="nick" class="form-control" placeholder="Wyszukaj lotnisko"/>
										</div>
										<div class="col-xs-3"  style="padding:0;">';
								echo Form::submit('send', 'Szukaj', array('class' => "btn btn-primary btn-block"));
								echo '</div>
									</div>';
								echo Form::close();
							} elseif ($pparam['function'] == 'find_user') {
								echo Form::open('profil/znajdz');
								echo '<div class="form-group row">
										<div class="col-xs-offset-1 col-xs-7" style="padding:0;">
											<input type="text" name="nick" class="form-control" placeholder="Cały lub część nicku"/>
										</div>
										<div class="col-xs-3"  style="padding:0;">';
								echo Form::submit('send', 'Szukaj', array('class' => "btn btn-primary btn-block"));
								echo '</div>
									</div>';
								echo Form::close();
							}
							continue;
						}
						$badge = "";
						if (isset($pparam['badge'])) {
							$z = GlobalVars::${$pparam['badge']};
							if (isset($z) && (int) $z > 0) {
								$badge = '<div class="badge badgeDiv">' . $z . '</div>';
								$badges += $z;
							}
						}

						if (isset($pparam['admin']) && $pparam['admin'] == 1 && isset(GlobalVars::$profil['admin']) && GlobalVars::$profil['admin'] != 1) {
							continue;
						}

						if (isset($pparam['url'])) {
							echo '<li><div>' . HTML::anchor("/" . $pparam['url'], $pname) . $badge . '<small>' . $pparam['name'] . '</small></div></li>';
						}
					}
				}
				echo '</ul>';
				echo '<a href="' . (($categoryUrl != null) ? "/" . $categoryUrl : '#') . '">';
				echo $name . '</a><small class="category-horizontal">' . $categoryName . '</small><div class="rotate category-vertical">' . $categoryName . '</div>';
				if ($badges > 0) {
					echo '<div class="badge badgeDiv">' . $badges . '</div>';
				}
				echo '</li>';
			}
		}
		echo '</ul></nav>';
	}
}