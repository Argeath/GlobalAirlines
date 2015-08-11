<?php

try
{
    // TODO: Move all global variables to a class
    $menu_zlecen = 0;
    $nowych_wiadomosci = 0;
    $nowych_powiadomien = 0;
    $nowych_kontaktow = 0;
    $users_online = array();
    $fb_loginPath = "";
    $logged = false;
    $bazy = array();
    $profil = array();
    View::bind_global('menu_zlecen', $menu_zlecen);
    View::bind_global('nowych_wiadomosci', $nowych_wiadomosci);
    View::bind_global('nowych_powiadomien', $nowych_powiadomien);
    View::bind_global('nowych_kontaktow', $nowych_kontaktow);
    View::bind_global('fb_loginPath', $fb_loginPath);
    View::bind_global('logged', $logged);
    View::bind_global('bazy', $bazy);
    View::bind_global('profil', $profil);
    View::bind_global('users_online', $users_online);

    include_once 'functions.php';

    Route::set('login', '(user(/<action>(/<id>)))')
        ->defaults(array(
            'controller' => 'user',
            'action' => 'index',
        ));

    {
        $maintenance = false;
        try {
            $q = DB::select()->from('settings')->where('key', '=', 'maintenance')->execute()->as_array();
            if (!empty($q)) {
                $q = $q[0];
                if ($q['value'] == 1) {
                    $maintenance = true;
                }
            }
        } catch (Exception $e) {
            $maintenance = true;
        }
        if ($maintenance) {
            $user = Auth::instance()->get_user();
            if (!$user || !$user->isAdmin()) {
                Route::set('maintenance', '<catcher>', array('catcher' => '.*'))
                    ->defaults(array(
                        'controller' => 'maintenance',
                        'action' => 'index',
                    ));
            }
        }
    }

    Route::set('logout', 'user/logout')
        ->defaults(array(
            'controller' => 'user',
            'action' => 'logout',
        ));

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
            $fb_loginPath = $FB->getLoginUrl();
        } else {
            $FB->createSession(1);
        }

        $user = Auth::instance()->get_user();
        if ($user) {
            if ($user->username == NULL) {
                Route::set('wyborNicku', '<catcher>', array('catcher' => '.*'))
                    ->defaults(array(
                        'controller' => 'firstLogin',
                        'action' => 'index',
                    ));
            }

            if ($user->id == null) {
                Auth::instance()->logout();
                FB::instance()->destroySession();
            }

            $logged = true;
            Events::checkEvents();

            $user->doBotStuff();
            $user->menuZlecen();
            $user->nowychWiadomosci();
            $user->nowychPowiadomien();
            $user->nowychKontaktow();
            $user->bazy();
            $user->profil();
            countOnlineUsers();
            if ($user->base_id == 0) {
                Route::set('wyborBazy', '<catcher>', array('catcher' => '.*'))
                    ->defaults(array(
                        'controller' => 'firstBase',
                        'action' => 'index',
                    ));
            }
        }
    }

} catch (Exception $e) {
    errToDb('[Exception][Bootstrap][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
    echo Debug::vars('[Exception][Bootstrap][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
}