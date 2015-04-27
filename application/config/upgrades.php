<?php defined('SYSPATH') OR die('No direct access allowed.');

return array(
	'db' => array(
		'maxSpalanie', 'maxMiejsc', 'maxZasieg', 'maxPredkosc'
	),
	
	'effect' => array(
		'spalanie', 'miejsc', 'zasieg', 'predkosc'
	),
	
	'names' => array(
		'Aerodynamika' => array('Lotki', 'Stateczniki', 'Dziub', 'Kadłub', 'Skrzydła'),
		'Kabina pasażerska' => array(),
		'Zbiornik' => array(),
		'Silnik' => array('Turbina', 'Komora Spalania', 'Wlot powietrza', 'Wał', 'Sprężarka'),
	),
	
	'percents' => array(12, 16, 20, 24, 28, 0),
	
	'possible' => 1,
	
	'upgrades' => array(
		'Silnik' => array(
			'Łopatki' => array(
				'spalanie',
				'predkosc',
			),
			'Wał napędowy' => array(
				'spalanie',
				'predkosc',
			),
			'Turbina' => array(
				'spalanie',
				'zasieg',
				'predkosc',
			),
			'Komora spalania' => array(
				'spalanie',
				'zasieg',
				'predkosc',
			),
			'Kompresor' => array(
				'spalanie',
				'zasieg',
				'predkosc',
			),
		),
		'Kadłub' => array(
			'Stery' => array(
				'zasieg',
				'komfort',
			),
			'Kabina pasażerska' => array(
				'miejsc',
				'komfort',
			),
			'Dziub' => array(
				'spalanie',
				'zasieg',
				'komfort',
			),
			'Statecznik' => array(
				'spalanie',
				'zasieg',
				'komfort',
			),
			'Poszycie' => array(
				'spalanie',
				'zasieg',
				'predkosc',
				'komfort',
			),
		),
		'Skrzydła' => array(
			'Klapy' => array(
				'spalanie',
				'zasieg',
				'predkosc',
				'komfort',
			),
			'Lotki' => array(
				'spalanie',
				'zasieg',
				'predkosc',
				'komfort',
			),
			'Winglet' => array(
				'spalanie',
				'zasieg',
				'predkosc',
				'komfort',
			),
			'Zbiornik paliwa' => array(
				'zasieg',
			),
			'Hamulec aerodynamiczny' => array(
				'zasieg',
				'predkosc',
				'komfort',
			),
		),
		'Podwozie' => array(
			'Oś' => array(
				'komfort',
			),
			'Tłok' => array(
				'komfort',
			),
			'Łożyska' => array(
				'komfort',
			),
			'Opony' => array(
				'komfort',
			),
			'Siłownik' => array(
				'komfort',
			),
		),
	),

);