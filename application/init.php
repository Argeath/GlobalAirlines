<?php defined('SYSPATH') or die('No direct script access.');
include_once 'functions.php';

try {
    Route::set('login', '(user(/<action>(/<id>)))')
        ->defaults(array(
            'controller' => 'user',
            'action' => 'index',
        ));

    if (Maintenance::check()) {
        $user = Auth::instance()->get_user();
        if (!$user || !$user->isAdmin()) {
            Route::set('maintenance', '<catcher>', array('catcher' => '.*'))
                ->defaults(array(
                    'controller' => 'maintenance',
                    'action' => 'index' ));
        }
    }

    Route::set('logout', 'user/logout')
        ->defaults(array(
            'controller' => 'user',
            'action' => 'logout' ));

    $secure_connection = false;
    if (isset($_SERVER['HTTPS'])) {
        if ($_SERVER["HTTPS"] == "on") {
            $secure_connection = true;
        }
    }
    $protocol = ($secure_connection) ? 'https' : 'http';

    //Facebook
    {
        $FB = Facebook::instance();
        if (!Facebook::isCanvas()) {
            $FB->createSession(0, URL::base($protocol));
            GlobalVars::$fb_loginPath = $FB->getLoginUrl();
        } else {
            $FB->createSession(1);
        }

        $user = Auth::instance()->get_user();
        if ($user) {
            if ($user->username == NULL)
                Route::set('wyborNicku', '<catcher>', array('catcher' => '.*'))
                    ->defaults(array(
                        'controller' => 'firstLogin',
                        'action' => 'index' ));

            if ($user->activation_hash != NULL)
                Route::set('unactivated', '<catcher>', array('catcher' => '.*'))
                    ->defaults(array(
                        'controller' => 'user',
                        'action' => 'created' ));

            if ($user->id == null) {
                Auth::instance()->logout();
                FB::instance()->destroySession();
            }

            GlobalVars::$logged = true;
            Events::checkEvents();

            $user->doBotStuff();
            $user->menuZlecen();
            $user->nowychWiadomosci();
            $user->nowychPowiadomien();
            $user->nowychKontaktow();
            $user->bazy();
            $user->profil();
            countOnlineUsers();

            if ($user->base_id == 0)
                Route::set('wyborBazy', '<catcher>', array('catcher' => '.*'))
                    ->defaults(array(
                        'controller' => 'firstBase',
                        'action' => 'index' ));

        }
    }
} catch (Exception $e) {
    errToDb('[Exception][Bootstrap][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
}