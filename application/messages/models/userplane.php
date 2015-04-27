<?php defined('SYSPATH') OR die('No direct script access.');

return array(
	'rejestracja' => array(
		'not_empty' => 'Rejestracja nie może być pusta.',
        'min_length' => 'Rejestracja nie może być krótsza niż :param2 znaki.',
        'max_length' =>'Rejestracja nie może być dłuższa niż :param2 znaków.',
		'regex' => 'Nazwa gracza może zawierać jedynie litery, cyfry oraz znaki specjalne("-", ".", "_", spacje).', 
    )
);
?>