<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'login' => array(
		'<i class="glyphicon glyphicon-home"></i>Biuro' => array(
			'<i class="fa fa-eye"></i>Podgląd' => array('url' => 'podglad'),
			'<i class="fa fa-briefcase"></i>Zlecenia' => array('url' => 'zlecenia2', 'badge' => 'menu_zlecen'),
			'<i class="glyphicon glyphicon-briefcase"></i>Kadry' => array('url' => 'kadry'),
			'<i class="fa fa-calendar"></i>Terminarz' => array('url' => 'terminarz'),
			'<i class="fa fa-university"></i>Dziennik finansowy' => array('url' => 'journal'),
			'<i class="fa fa-map-marker"></i>Mapa połączeń' => array('url' => 'map'),
			'<i class="glyphicon glyphicon-warning-sign"></i>Powiadomienia' => array('url' => 'powiadomienia', 'badge' => 'nowych_powiadomien'),
		),

		/*'<i class="glyphicon glyphicon-list-alt"></i>Zlecenia' 		=> array(
		'<i class="fa fa-share"></i>Połączenia Regionalne' 			=> array('url' => 'zlecenia/1'),
		'<i class="fa fa-share"></i>Połączenia Krajowe' 			=> array('url' => 'zlecenia/2', 'blocked' => 2),
		'<i class="fa fa-share"></i>Połączenia Międzynarodowe'		=> array('url' => 'zlecenia/3', 'blocked' => 3),
		'<i class="fa fa-share"></i>Połączenia Międzykontynentalne'	=> array('url' => 'zlecenia/4', 'blocked' => 4),
		'<i class="fa fa-share"></i>Połączenia Biznesowe' 			=> array('url' => 'zlecenia/5', 'blocked' => 5),
		'<i class="fa fa-share"></i>Połączenia Ekskluzywne' 		=> array('url' => 'zlecenia/6', 'blocked' => 6),
		),*/

		'<i class="glyphicon glyphicon-list-alt"></i>Zlecenia' => 'orders',

		/*array(
		'<i class="fa fa-share"></i>A1' 			=> array('url' => 'zlecenia/1'),
		'<i class="fa fa-share"></i>A2' 			=> array('url' => 'zlecenia/2', 'blocked' => 2),
		'<i class="fa fa-share"></i>A3' 			=> array('url' => 'zlecenia/3', 'blocked' => 3),
		'<i class="fa fa-share"></i>A4' 			=> array('url' => 'zlecenia/4', 'blocked' => 4),
		'<i class="fa fa-share"></i>A5' 			=> array('url' => 'zlecenia/5', 'blocked' => 5),
		'<i class="fa fa-share"></i>A6' 			=> array('url' => 'zlecenia/6', 'blocked' => 6),
		'<i class="fa fa-share"></i>A7' 			=> array('url' => 'zlecenia/7', 'blocked' => 7),
		'<i class="fa fa-share"></i>A8' 			=> array('url' => 'zlecenia/8', 'blocked' => 8),
		'<i class="fa fa-share"></i>A9' 			=> array('url' => 'zlecenia/9', 'blocked' => 9),
		'<i class="fa fa-share"></i>A10' 			=> array('url' => 'zlecenia/10', 'blocked' => 10),
		'<i class="fa fa-share"></i>A11' 			=> array('url' => 'zlecenia/11', 'blocked' => 11),
		'<i class="fa fa-share"></i>A12' 			=> array('url' => 'zlecenia/12', 'blocked' => 12),
		'<i class="fa fa-gears"></i>Test' 			=> array('url' => 'testMath', 'admin' => 1),
		),*/

		'<i class="glyphicon glyphicon-wrench"></i>Hangar' => array(
			'<i class="glyphicon glyphicon-plane"></i>Samoloty' => array('url' => 'samoloty'),
			'<i class="fa fa-cogs"></i>Warsztat' => array('url' => 'warsztat'),
			'<i class="fa fa-shopping-cart"></i>Sklep' => array('url' => 'sklep'),
			'<i class="fa fa-gavel"></i>Aukcje' => array('url' => 'sklep/aukcje'),
			'<i class="fa fa-tint"></i>Paliwo' => array('url' => 'paliwo'),
			'<i class="fa fa-tint"></i>Test Upgrades' => array('url' => 'testUpgrades', 'admin' => 1),
		),

		'<i class="glyphicon glyphicon-user"></i>Konto' => array(
			'<i class="fa fa-users"></i>Kontakty' => array('url' => 'kontakty', 'badge' => 'nowych_kontaktow'),
			'<i class="fa fa-envelope"></i>Poczta' => array('url' => 'poczta', 'badge' => 'nowych_wiadomosci'),
			'<i class="fa fa-share-alt"></i>System Poleconych' => array('url' => 'referrals'),
			'<i class="glyphicon glyphicon-user"></i>Profil' => array('url' => 'profil'),
			'<i class="glyphicon glyphicon-thumbs-up"></i>Osiągnięcia' => array('url' => 'Podglad'), //TODO
			'<i class="glyphicon glyphicon-wrench"></i>Zmiana hasła' => array('url' => 'user/changePassword'),
			'<i class="glyphicon glyphicon-user"></i>Avatar' => array('url' => 'user/avatar'),
			'<i class="glyphicon glyphicon-remove"></i>Usuń konto' => array('url' => 'user/deleteAccount'),
			'<i class="fa fa-cog"></i>Ustawienia' => array('url' => 'Podglad'), //TODO
		),

		'<i class="fa fa-plane"></i>Lotniska' => array(
			'' => array('function' => 'bazy'),
			'Znajdź miasto' => array('function' => 'find_city'),
		),

		'<i class="glyphicon glyphicon-star"></i>Ranking' => 'ranking',

		'<i class="glyphicon glyphicon-search"></i>Znajdź gracza' => array(
			'' => array('function' => 'find_user'),
		),

		'<i class="fa fa-credit-card"></i>Donacje' => 'rozwoj',
		'<i class="glyphicon glyphicon-comment"></i>Forum' => 'forum',

		'<i class="glyphicon glyphicon-log-out"></i>Wyloguj' => 'user/logout',
	),
	'logout' => array(
		'<i class="glyphicon glyphicon-log-in"></i>Logowanie' => 'user/login',
		'<i class="glyphicon glyphicon-plus-sign"></i>Rejestracja' => 'user/create',
		'<i class="glyphicon glyphicon-comment"></i>Forum' => 'forum',
	),
);
?>