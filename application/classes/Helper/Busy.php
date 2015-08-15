<?php defined('SYSPATH') or die('No direct script access.');

abstract class Helper_Busy extends Helper_BasicEnum {
	const NotBusy = 0;
	const InAir = 1;
	const OnAuction = 2;
	const Accident = 3;
	const Unidentified = 99;

	static function getText($x) {
		switch ($x) {
			case Helper_Busy::NotBusy:
				return "Wolny";
			case Helper_Busy::InAir:
				return "W powietrzu";
			case Helper_Busy::OnAuction:
				return "Na aukcji";
			case Helper_Busy::Accident:
				return "Awaria";
			case Helper_Busy::Unidentified:
			default:
				return "Zajęty";
		}
	}
}