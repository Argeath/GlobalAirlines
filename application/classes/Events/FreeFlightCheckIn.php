<?php defined('SYSPATH') or die('No direct script access.');

class Events_FreeFlightCheckIn extends Events_Event {
    protected function doWork() {
        $planeId = $this->parameters['plane'];
        $distance = $this->parameters['distance'];
        $timeInAir = $this->parameters['czas'];
        $flightId = $this->parameters['flight'];

        $plane = ORM::factory("UserPlane", $planeId);
        if ($plane->loaded() && $plane->user_id == $this->event->user->id) {
            $flight = ORM::factory("Flight", $flightId);
            if ($flight->loaded() && $flight->user_id == $this->event->user->id) {
                $newEvent = ORM::factory("Event");
                $newEvent->user_id = $this->event->user->id;
                $newEvent->when = $this->event->when + $timeInAir;
                $newEvent->type = 1;
                $newEvent->save();
                $event_id = $newEvent->id;

                $flight->checked = 1;
                $flight->event = $event_id;
                $flight->save();

                $pilotow = $plane->staff->where('type', '=', 'pilot')->count_all();
                $stanZaLot = round((($timeInAir / 60) * 0.025) / $pilotow, 2);
                $plane->updateStaffConditionFuture(-$stanZaLot);

                //Parametry
                $this->manager->addParam($event_id, 'distance', $distance);
                $this->manager->addParam($event_id, 'czas', $timeInAir);
            } else {
                $this->addInfo("EventIf", 2);
            }
        } else {
            $this->addInfo("EventIf", 1);
        }

        $this->manager->needOneMoreCycle = true;
    }

}