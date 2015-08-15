<?php defined('SYSPATH') or die('No direct script access.');

abstract class Helper_Financial extends Helper_BasicEnum {
    const LotSwobodny = 0;
    const OLotZlecenie = 1;
    const Przeglad = 2;
    const LotSwobodnyPrac = 3;
    const LotZlecenie = 4;
    const AukcjaZwrot = 5;
    const AukcjaSprzedaz = 6;
    const AukcjaZaplata = 7;
    const Sklep = 8;
    const LotniskoRozbudowa = 9;
    const Paliwo = 10;
    const Warsztat = 11;
	const SklepSprzedaz = 12;
	const Dotacja = 13;
	const Deadline = 14;
	const LotZwrot = 15;
    const LotniskoPunktOdpraw = 16;
    const LotniskoUlepszeniePunktOdpraw = 17;

	
	static function getText($x)
	{
		switch($x)
		{
			case Helper_Financial::LotSwobodny:
				return "Opłaty za loty swobodne";
			case Helper_Financial::OLotZlecenie:
				return "Opłaty za loty na zlecenia";
			case Helper_Financial::Przeglad:
				return "Przeglądy";
			case Helper_Financial::LotSwobodnyPrac:
				return "Opłaty za loty pracowników";
			case Helper_Financial::LotZlecenie:
				return "Zapłata za zlecenia";
			case Helper_Financial::AukcjaZwrot:
				return "Zwroty z aukcji";
			case Helper_Financial::AukcjaSprzedaz:
				return "Sprzedaż na aukcjach";
			case Helper_Financial::AukcjaZaplata:
				return "Kupno na aukcjach";
			case Helper_Financial::Sklep:
				return "Kupno w sklepie";
			case Helper_Financial::SklepSprzedaz:
				return "Sprzedaż samolotu";
			case Helper_Financial::LotniskoRozbudowa:
				return "Rozbudowa biura na lotnisku";
			case Helper_Financial::Paliwo:
				return "Paliwo w zbiornikach";
			case Helper_Financial::Warsztat:
				return "Ulepszenia samolotów";
			case Helper_Financial::Dotacja:
				return "Dotacje od admina";
			case Helper_Financial::Deadline:
				return "Niewykonanie zlecenia";
			case Helper_Financial::LotZwrot:
				return "Zwrot opłat za lot";
            case Helper_Financial::LotniskoPunktOdpraw:
                return "Punkty odpraw";
            case Helper_Financial::LotniskoUlepszeniePunktOdpraw:
                return "Ulepszenie punktu odpraw";
		}
		return "";
	}
}