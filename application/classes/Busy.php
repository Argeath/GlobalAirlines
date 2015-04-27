<?php defined('SYSPATH') or die('No direct script access.');

abstract class Busy extends BasicEnum {
	const NotBusy = 0;
	const InAir = 1;
	const OnAuction = 2;
	const Accident = 3;
	const Unidentified = 99;

	static function getText($x) {
		switch ($x) {
			case Busy::NotBusy:
				return "Wolny";
			case Busy::InAir:
				return "W powietrzu";
			case Busy::OnAuction:
				return "Na aukcji";
			case Busy::Accident:
				return "Awaria";
			case Busy::Unidentified:
			default:
				return "Zajęty";
		}
		return "";
	}
}