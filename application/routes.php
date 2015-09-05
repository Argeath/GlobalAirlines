<?php defined('SYSPATH') OR die('No direct access allowed.');

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
