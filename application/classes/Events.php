<?php defined('SYSPATH') or die('No direct script access.');

class Events {
	static function checkEvents() {
		try {
			$user = Auth::instance()->get_user();
			do {
                $eventManager = new Events_EventManager();
				$qs = ORM::factory("Event")->where('done', '=', 0)->and_where('when', '<', time() - 5)->find_all();

                // Sprawdzenie przypadkowego przerwania oleju
                $lastOil = ORM::factory("Event")->where('type', '=', 8)->order_by('when', 'DESC')->find();
                if ($lastOil->loaded()) {
                    if ($lastOil->when < time() - 3650) {
                        $newEvent = ORM::factory("Event");
                        $newEvent->when = $lastOil->when + 3600;
                        $newEvent->type = 8;
                        $newEvent->save();
                        $eventManager->needOneMoreCycle = true;
                    }
                }

				foreach ($qs as $q) {
                    Events::route($q, $eventManager, $user);
				}

                $eventManager->commitParams();
                $eventManager->commitLogs();
			} while ($eventManager->needOneMoreCycle);

			DB::delete('events')->and_where('when', '<', time() - 604800)->and_where('done', '=', 1)->execute();
			DB::delete('events')->and_where('when', '<', time() - 86400)->and_where('type', '=', 8)->execute();

		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
	}

	public static function route(Model_Event $q, Events_EventManager $eventManager, $user = NULL)
	{
		if ($q->type == 1)//Lot swobodny
		{
			$event = new Events_FreeFlight($eventManager, $q, $user);
			$event->execute();

		} elseif ($q->type == 2)//Odprawa lotu swobodnego
		{
			$event = new Events_FreeFlightCheckIn($eventManager, $q, $user);
			$event->execute();

		} elseif ($q->type == 3)// Deadline zlecenia
		{
			$event = new Events_OrderDeadline($eventManager, $q, $user);
			$event->execute();

		} elseif ($q->type == 4)//Odprawa lotu na zlecenie
		{
			$event = new Events_OrderFlightCheckIn($eventManager, $q, $user);
			$event->execute();

		} elseif ($q->type == 5)//Lot na zlecenie
		{
			$event = new Events_OrderFlight($eventManager, $q, $user);
			$event->execute();

		} elseif ($q->type == 6)//Usunięcie konta
		{
			$event = new Events_DeleteUser($eventManager, $q, $user);
			$event->execute();

		} elseif ($q->type == 7)//Lot swobodny pracownika
		{
			$event = new Events_WorkerFlight($eventManager, $q, $user);
			$event->execute();

		} elseif ($q->type == 8)//Obliczanie ceny paliwa
		{
			$event = new Events_Oil($eventManager, $q, $user);
			$event->execute();

		} elseif ($q->type == 9)//Przeglad generalny
		{
			$event = new Events_PlaneInspection($eventManager, $q, $user);
			$event->execute();

		} elseif ($q->type == 10)//Rozpoczęcie odprawy zlecenia
		{
			$event = new Events_OrderFlightCheckInInit($eventManager, $q, $user);
			$event->execute();

		} elseif ($q->type == 11)//Rozpoczęcie odprawy lotu swobodnego
		{
			$event = new Events_FreeFlightCheckInInit($eventManager, $q, $user);
			$event->execute();

		} elseif ($q->type == 12)//Aukcja
		{
			$event = new Events_Auction($eventManager, $q, $user);
			$event->execute();

		} elseif ($q->type == 13)// Regeneracja stanu zalogi
		{

		} elseif ($q->type == 14)// Awaria
		{
			$event = new Events_FlightCrash($eventManager, $q, $user);
			$event->execute();

		} elseif ($q->type == 15) //Boty
		{
			$event = new Events_Bot($eventManager, $q, $user);
			$event->execute();
		} else {
			//Nieobslugiwany typ
			$eventManager->addLog('[Unknown type: ' . $q->type . ']');
		}

        $q->done = 1;
        $q->save();
	}
};