<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
	'login' => array(
		'<i class="glyphicon glyphicon-home"></i>' => array(
			'name' => 'Biuro',
			'<i class="fa fa-eye"></i>' => array('name' => 'Podgląd', 'url' => 'podglad'),
			'<i class="fa fa-th-list"></i>' => array('name' => 'Zlecenia', 'url' => 'zlecenia2', 'badge' => 'menu_zlecen'),
			'<i class="glyphicon glyphicon-briefcase"></i>' => array('name' => 'Kadry', 'url' => 'kadry'),
			'<i class="fa fa-calendar"></i>' => array('name' => 'Terminarz', 'url' => 'terminarz'),
			'<i class="fa fa-university"></i>' => array('name' => 'Finanse', 'url' => 'journal'),
			'<i class="fa fa-map-marker"></i>' => array('name' => 'Mapa', 'url' => 'map'),
			'<i class="glyphicon glyphicon-warning-sign"></i>' => array('name' => 'Powiadomienia', 'url' => 'powiadomienia', 'badge' => 'nowych_powiadomien'),
		),

		'<i class="glyphicon glyphicon-list-alt"></i>' => ['name' => 'Zlecenia', 'url' => 'orders'],

		'<i class="glyphicon glyphicon-wrench"></i>' => array(
			'name' => 'Hangar',
			'<i class="glyphicon glyphicon-plane"></i>' => array('name' => 'Samoloty', 'url' => 'samoloty'),
			'<i class="fa fa-cogs"></i>' => array('name' => 'Warsztat', 'url' => 'warsztat'),
			'<i class="fa fa-shopping-cart"></i>' => array('name' => 'Sklep', 'url' => 'sklep'),
			'<i class="fa fa-gavel"></i>' => array('name' => 'Aukcje', 'url' => 'sklep/aukcje'),
			'<i class="fa fa-tint"></i>' => array('name' => 'Paliwo', 'url' => 'paliwo'),
		),

		'<i class="glyphicon glyphicon-user"></i>' => array(
			'name' => 'Konto',
			'<i class="fa fa-users"></i>' => array('name' => 'Kontakty', 'url' => 'kontakty', 'badge' => 'nowych_kontaktow'),
			'<i class="fa fa-envelope"></i>' => array('name' => 'Poczta', 'url' => 'poczta', 'badge' => 'nowych_wiadomosci'),
			'<i class="fa fa-share-alt"></i>' => array('name' => 'Poleceni', 'url' => 'referrals'),
			'<i class="glyphicon glyphicon-user"></i>' => array('name' => 'Profil', 'url' => 'profil'),
			'<i class="glyphicon glyphicon-thumbs-up"></i>' => array('name' => 'Osiągnięcia', 'url' => 'Podglad'), //TODO
			'<i class="glyphicon glyphicon-wrench"></i>' => array('name' => 'Hasło', 'url' => 'user/changePassword'),
			'<i class="glyphicon glyphicon-user"></i>' => array('name' => 'Avatar', 'url' => 'user/avatar'),
			'<i class="glyphicon glyphicon-remove"></i>' => array('name' => 'Usuń konto', 'url' => 'user/deleteAccount'),
			'<i class="fa fa-cog"></i>' => array('name' => 'Ustawienia', 'url' => 'Podglad'), //TODO
		),

		'<i class="fa fa-plane"></i>' => array(
			'name' => 'Lotniska',
			'' => array('function' => 'bazy'),
			'Znajdź miasto' => array('function' => 'find_city'),
		),

		'<i class="glyphicon glyphicon-star"></i>' => ['name' => 'Ranking', 'url' => 'ranking'],

		'<i class="glyphicon glyphicon-search"></i>' => array(
			'name' => 'Szukaj',
			'' => array('function' => 'find_user'),
		),

		'<i class="fa fa-credit-card"></i>' => ['name' => 'Donacje', 'url' => 'rozwoj'],
		'<i class="glyphicon glyphicon-comment"></i>' => ['name' => 'Forum', 'url' => 'forum'],

		'<i class="glyphicon glyphicon-log-out"></i>' => ['name' => 'Wyloguj', 'url' => 'user/logout'],
	),
	'logout' => array(
		'<i class="glyphicon glyphicon-log-in"></i>' => ['name' => 'Logowanie', 'url' => 'user/login'],
		'<i class="glyphicon glyphicon-plus-sign"></i>' => ['name' => 'Rejestracja', 'url' => 'user/create'],
		'<i class="glyphicon glyphicon-comment"></i>' => ['name' => 'Forum', 'url' => 'forum'],
	),
);
?>