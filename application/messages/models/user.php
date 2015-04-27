<?php defined('SYSPATH') OR die('No direct script access.');

return array(
	'username' => array(
		'unique' => 'Podana nazwa gracza jest już w użyciu.',
		'not_empty' => 'Nazwa gracza nie może być pusta.',
		'min_length' => 'Nazwa gracza nie może być krótsza niż :param2 znaków.',
		'max_length' => 'Nazwa gracza nie może być dłuższa niż :param2 znaków.',
		'username_available' => 'Podana nazwa gracza jest już w użyciu.',
		'alpha_dash' => 'Nazwa gracza może zawierać jedynie litery, cyfry i spacje.',
	),
	'email' => array(
		'unique' => 'Podany adres email został już użyty.',
		'not_empty' => 'Musisz podać email.',
		'min_length' => 'Email nie może być krótszy niż :param2 znaków.',
		'max_length' => 'Email nie może być dłuższy niż :param2 znaków.',
		'email' => 'Podaj prawidłowy adres email.',
		'email_available' => 'Podany adres email został już użyty.',
	),

	'password' => array(
		'not_empty' => 'Hasło nie może być puste.',
		'min_length' => 'Hasło nie może być krótsze niż :param2 znaków.',
		'max_length' => 'Hasło nie może być dłuższe niż :param2 znaków.',
	),
	'password_confirm' => array(
		'matches' => 'Hasła się nie zgadzają.',
	),
);
?>