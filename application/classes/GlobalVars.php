<?php defined('SYSPATH') or die('No direct script access.');

class GlobalVars {
	static $cities = NULL;
	static $distances = NULL;

    static $menu_zlecen = 0;
    static $nowych_wiadomosci = 0;
    static $nowych_powiadomien = 0;
    static $nowych_kontaktow = 0;
    static $users_online = array();
    static $fb_loginPath = "";
    static $logged = false;
    static $bazy = array();
    static $profil = array();

	static function getCities() {
		if (GlobalVars::$cities == NULL) {
			GlobalVars::$cities = ORM::factory("City")->find_all();
		}
		return GlobalVars::$cities;
	}
}