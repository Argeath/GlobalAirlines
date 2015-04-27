<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
    'accidents' => array(
        // When:
        // 0 - przed startem
        // 1 - chwile po starcie (Samolot ląduje na tym samym lotnisku)
        // 2 - w trakcie lotu (Samolot ląduje na pobliskim lotnisku)
        0 => array(),
        1 => array(
            'name' => "Uszkodzenie poszycia",
            'effect' => 1,
            'when' => 0,
            'condition' => 90,
        ),
        2 => array(
            'name' => "Uszkodzenie silnika",
            'effect' => 1,
            'when' => 0,
            'condition' => 90,
        ),


        3 => array(
            'name' => "Przegrzanie silnika",
            'effect' => 4,
            'when' => 1,
            'condition' => 80,
        ),
        4 => array(
            'name' => "Uszkodzenie hydrauliki",
            'effect' => 2,
            'when' => 0,
            'condition' => 80,
        ),
        5 => array(
            'name' => "Uszkodzenie podwozia",
            'effect' => 2,
            'when' => 1,
            'condition' => 80,
        ),
        6 => array(
            'name' => "Uszkodzenie konstrukcji samolotu",
            'effect' => 2,
            'when' => 1,
            'condition' => 70,
        ),
        7 => array(
            'name' => "Uszkodzenie sterów",
            'effect' => 2,
            'when' => 0,
            'condition' => 70,
        ),


        8 => array(
            'name' => "Pożar silnika",
            'effect' => 3,
            'when' => 3,
            'condition' => 60,
        ),
        9 => array(
            'name' => "Zniszczenie podwozia",
            'effect' => 3,
            'when' => 2,
            'condition' => 60,
        ),
        10 => array(
            'name' => "Awaria hydrauliki",
            'effect' => 3,
            'when' => 2,
            'condition' => 60,
        ),
        11 => array(
            'name' => "Przerwanie poszycia",
            'effect' => 3,
            'when' => 2,
            'condition' => 60,
        ),
    ),
	
	
	
    'effects' => array(
        0 => array(),
        1 => array( //Lekkie uszkodzenie
            'minDelay' => 15,
            'maxDelay' => 25,
            'minCondition' => 2,
            'maxCondition' => 5,
        ),
        2 => array( //Poważne uszkodzenie
            'minDelay' => 60,
            'maxDelay' => 240,
            'minCondition' => 10,
            'maxCondition' => 25,
        ),
        3 => array( //Katastrofa
            'minDelay' => 240,
            'maxDelay' => 720,
            'minCondition' => 30,
            'maxCondition' => 60, // Jeżeli stan samolotu wyniesie 0% - samolot się rozbija
        ),
        4 => array(
            'minDelay' => 20,
            'maxDelay' => 60,
            'minCondition' => 0,
            'maxCondition' => 2,
        ),

    )
);