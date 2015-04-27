<?php

define('FACEBOOK_PATH', dirname(__FILE__) . '/vendor/');

Route::set('FB-auth', 'facebook/auth')
->defaults(array(
	'directory' => 'Facebook',
	'controller' => 'Callback',
	'action' => 'index'
));

require_once FACEBOOK_PATH . 'src/facebook.php';