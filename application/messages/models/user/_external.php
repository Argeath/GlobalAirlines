<?php

return array(
	'password' => array(
		'not_empty' => 'Hasło nie może być puste.',
		'min_length' => 'Hasło nie może być krótsze niż :param2 znaków.',
		'max_length' => 'Hasło nie może być dłuższe niż :param2 znaków.',
	),
	'password_confirm' => array(
		'matches' => 'Hasła się nie zgadzają.',
	),
);