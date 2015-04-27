<?php defined('SYSPATH') or die('No direct script access.');

abstract class Financial extends BasicEnum {
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
			case Financial::LotSwobodny:
				return "Opłaty za loty swobodne";
			case Financial::OLotZlecenie:
				return "Opłaty za loty na zlecenia";
			case Financial::Przeglad:
				return "Przeglądy";
			case Financial::LotSwobodnyPrac:
				return "Opłaty za loty pracowników";
			case Financial::LotZlecenie:
				return "Zapłata za zlecenia";
			case Financial::AukcjaZwrot:
				return "Zwroty z aukcji";
			case Financial::AukcjaSprzedaz:
				return "Sprzedaż na aukcjach";
			case Financial::AukcjaZaplata:
				return "Kupno na aukcjach";
			case Financial::Sklep:
				return "Kupno w sklepie";
			case Financial::SklepSprzedaz:
				return "Sprzedaż samolotu";
			case Financial::LotniskoRozbudowa:
				return "Rozbudowa biura na lotnisku";
			case Financial::Paliwo:
				return "Paliwo w zbiornikach";
			case Financial::Warsztat:
				return "Ulepszenia samolotów";
			case Financial::Dotacja:
				return "Dotacje od admina";
			case Financial::Deadline:
				return "Niewykonanie zlecenia";
			case Financial::LotZwrot:
				return "Zwrot opłat za lot";
            case Financial::LotniskoPunktOdpraw:
                return "Punkty odpraw";
            case Financial::LotniskoUlepszeniePunktOdpraw:
                return "Ulepszenie punktu odpraw";
		}
		return "";
	}
}