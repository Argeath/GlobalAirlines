<?php defined('SYSPATH') or die('No direct script access.');

session_start();

// -- Environment setup --------------------------------------------------------

// Load the core Kohana class
require SYSPATH . 'classes/Kohana/Core' . EXT;

if (is_file(APPPATH . 'classes/Kohana' . EXT)) {
	// Application extends the core
	require APPPATH . 'classes/Kohana' . EXT;
} else {
	// Load empty core extension
	require SYSPATH . 'classes/Kohana' . EXT;
}

/**
 * Set the default time zone.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/timezones
 */
date_default_timezone_set('Europe/Warsaw');

/**
 * Set the default locale.
 *
 * @link http://kohanaframework.org/guide/using.configuration
 * @link http://www.php.net/manual/function.setlocale
 */
setlocale(LC_ALL, 'pl_PL.utf-8', 'pl.utf-8', 'pl_PL', 'pl', 'Polish_Poland.28592');

/**
 * Enable the Kohana auto-loader.
 *
 * @link http://kohanaframework.org/guide/using.autoloading
 * @link http://www.php.net/manual/function.spl-autoload-register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Optionally, you can enable a compatibility auto-loader for use with
 * older modules that have not been updated for PSR-0.
 *
 * It is recommended to not enable this unless absolutely necessary.
 */
//spl_autoload_register(array('Kohana', 'auto_load_lowercase'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @link http://www.php.net/manual/function.spl-autoload-call
 * @link http://www.php.net/manual/var.configuration#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

/**
 * Set the mb_substitute_character to "none"
 *
 * @link http://www.php.net/manual/function.mb-substitute-character.php
 */
mb_substitute_character('none');

// -- Configuration and initialization -----------------------------------------

Cookie::$salt = 'foobar';

/**
 * Set the default language
 */
I18n::lang('pl');

if (isset($_SERVER['SERVER_PROTOCOL'])) {
	// Replace the default protocol.
	HTTP::$protocol = $_SERVER['SERVER_PROTOCOL'];
}

/**
 * Set Kohana::$environment if a 'KOHANA_ENV' environment variable has been supplied.
 *
 * Note: If you supply an invalid environment name, a PHP warning will be thrown
 * saying "Couldn't find constant Kohana::<INVALID_ENV_NAME>"
 */
Kohana::$environment = Kohana::PRODUCTION;

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - integer  cache_life  lifetime, in seconds, of items cached              60
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 * - boolean  expose      set the X-Powered-By header                        FALSE
 */

define('PATH', '/');
//define('WAL', '€');
define('WAL', '<div class="fa fa-money"></div>');

Session::$default = 'database';

Kohana::init(array(
	'base_url' => PATH . '/',
	'index_file' => FALSE,
	'profile' => TRUE,
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Log_File(APPPATH . 'logs'));
Log::$write_on_add = true;

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(
	'auth' 			=> MODPATH . 'auth', // Basic authentication
	'facebook' 		=> MODPATH . 'facebook', // Basic authentication
	'cache' 		=> MODPATH . 'cache', // Caching with multiple backends
	// 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
	'database' 		=> MODPATH . 'database', // Database access
	'image' 		=> MODPATH . 'image', // Image manipulation
	// 'minion'     => MODPATH.'minion',     // CLI Tasks
	'orm' 			=> MODPATH . 'orm', // Object Relationship Mapping
	// 'unittest'   => MODPATH.'unittest',   // Unit testing
	// 'userguide'  => MODPATH.'userguide',  // User guide and API documentation
	'sitemap' 		=> MODPATH . 'sitemap', // Sitemap
	'mysqli' 		=> MODPATH . 'mysqli',
	'email'			=> MODPATH . 'email' // Email (https://github.com/shadowhand/email)
));
include 'init.php';

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
Route::set('socketIO', '(socket.io)')
    ->defaults(array(
        'controller' => 'ajax',
        'action' => 'index',
    ));

Route::set('JournalSummation', '(summation(/<from>(/<to>(/<plane>))))', array('from' => '\d+', 'to' => '\d+', 'plane' => '\d+'))
	->defaults(array(
		'controller' => 'journal',
		'action' => 'summation',
	));

Route::set('Benchmark', '(benchmark(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'benchmark',
		'action' => 'index',
	));

Route::set('Journal', '(journal(/<offset>))', array('offset' => '\d+'))
	->defaults(array(
		'controller' => 'journal',
		'action' => 'index',
	));

Route::set('powiadomienia', '(powiadomienia(/<offset>))', array('offset' => '\d+'))
	->defaults(array(
		'controller' => 'MiniMessages',
		'action' => 'index',
	));

Route::set('ranking', '(ranking(/<id>(/<offset>)))', array('offset' => '\d+'))
	->defaults(array(
		'controller' => 'ranking',
		'action' => 'index',
	));

Route::set('airportFind', '(airport/find(/<name>(/<offset>)))')
	->defaults(array(
		'controller' => 'airport',
		'action' => 'find',
	));

Route::set('airportParams', '(airport/activity(/<id>(/<id2>)))', array('id2' => '\d+'))
	->defaults(array(
		'controller' => 'airport',
		'action' => 'activity',
	));

Route::set('airport', '(airport(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'airport',
		'action' => 'index',
	));

Route::set('terminarz', '(terminarz(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'terminarz',
		'action' => 'index',
	));

Route::set('generate', '(generate(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'generate',
		'action' => 'index',
	));

Route::set('checkingEvents', '(checkEvents(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'checkEvents',
		'action' => 'index',
	));

Route::set('paliwo', '(paliwo(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'paliwo',
		'action' => 'index',
	));

Route::set('warsztat', '(warsztat(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'warsztat',
		'action' => 'index',
	));

Route::set('sklep', '(sklep(/<klasa>(/<action>(/<id>))))', array('klasa' => '\d+'))
	->defaults(array(
		'controller' => 'sklep',
		'action' => 'index',
	));

Route::set('kadry', '(kadry(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'kadry',
		'action' => 'index',
	));

Route::set('szukaj', '(profil/znajdz(/<nick>(/<offset>)))', array('offset' => '\d+'))
	->defaults(array(
		'controller' => 'profil',
		'action' => 'szukaj',
	));

Route::set('profil', '(profil(/<graczid>))', array('graczid' => '\d+'))
	->defaults(array(
		'controller' => 'profil',
		'action' => 'index',
	));

Route::set('kontakty', '(kontakty(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'kontakty',
		'action' => 'index',
	));

Route::set('poczta', '(poczta(/<action>(/<typ>(/<offset>))))', array('typ' => '\d+'))
	->defaults(array(
		'typ' => 1,
		'controller' => 'messages',
		'action' => 'index',
	));

Route::set('samoloty', '(samoloty(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'samoloty',
		'action' => 'index',
	));

Route::set('mapa', '(map(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'map',
		'action' => 'index',
	));

Route::set('podglad', '(podglad(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'podglad',
		'action' => 'index',
	));

Route::set('testUpgrades', '(testUpgrades(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'testUpgrades',
		'action' => 'index',
	));

Route::set('testMath', '(testMath(/<biuropodrozy>(/<klasa>(/<action>(/<id>)))))', array('biuropodrozy' => '\d+', 'klasa' => '\d+'))
	->defaults(array(
		'controller' => 'testMath',
		'action' => 'index',
	));

Route::set('zlecenie', '(zlecenie(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'zlecenia2',
		'action' => 'index',
	));

Route::set('orders', '(orders(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'orders',
		'action' => 'index',
	));

Route::set('zlecenia', '(zlecenia(/<biuropodrozy>(/<klasa>(/<action>(/<id>)))))', array('biuropodrozy' => '\d+', 'klasa' => '\d+'))
	->defaults(array(
		'controller' => 'zlecenia',
		'action' => 'index',
	));

Route::set('validations', '(validation(/<action>))')
	->defaults(array(
		'controller' => 'validation',
		'action' => 'index',
	));

Route::set('ajaxy', '(ajax(/<action>(/<id>(/<param>(/<param2>)))))')
	->defaults(array(
		'controller' => 'ajax',
		'action' => 'index',
	));

Route::set('events', '(eventManager/events(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'events',
		'action' => 'index',
	));

Route::set('JSConnect', '(jsconnect)')
	->defaults(array(
		'controller' => 'JSConnect',
		'action' => 'index',
	));

Route::set('ref', '(ref(/<ref>))', array('ref' => '\d+'))
	->defaults(array(
		'controller' => 'user',
		'action' => 'index',
	));

Route::set('default', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'user',
		'action' => 'index',
	));
