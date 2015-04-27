<?php defined('SYSPATH') or die('No direct script access.');

class GlobalArrays {
	static $cities = NULL;
	static $distances = NULL;

	static function getCities() {
		if (GlobalArrays::$cities == NULL) {
			GlobalArrays::$cities = ORM::factory("City")->find_all();
		}
		return GlobalArrays::$cities;
	}
}