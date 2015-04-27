<?php defined('SYSPATH') OR die('No direct script access.');
return array(
    'native' => array(
        'name' => 'airlines_session',
        'lifetime' => 86400,
    ),
    'cookie' => array(
        'name' => 'airlines_cookie',
        'encrypted' => TRUE,
        'lifetime' => 86400,
    ),
    'database' => array(
        'name' => 'airlines_database',
        'encrypted' => TRUE,
        'lifetime' => 86400,
        'group' => 'default',
        'table' => 'sessions',
        'columns' => array(
            'session_id'  => 'session_id',
            'last_active' => 'last_active',
            'contents'    => 'contents'
        ),
        'gc' => 500,
    ),
);
