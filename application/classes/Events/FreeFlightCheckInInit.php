<?php defined('SYSPATH') or die('No direct script access.');

class Events_FreeFlightCheckInInit extends Events_Event {
    protected function doWork() {
        $distance = $this->parameters['distance'];
        $czas = $this->parameters['czas'];
        $planeId = $this->parameters['plane'];
        $flight = $this->parameters['flight'];
        $paliwo = $this->parameters['paliwo'];
        $odprawa = $this->parameters['odprawa'];
        $to = $this->parameters['to'];
        $from = $this->parameters['from'];

        $plane = $this->event->user->UserPlanes->where('id', '=', $planeId)->find();
        if ($plane->loaded() && $plane->isBusy() == Helper_Busy::NotBusy && $plane->position == $from) {

            $newEvent = ORM::factory("Event");
            $newEvent->user_id = $this->event->user->id;
            $newEvent->when = $this->event->when + $odprawa;
            $newEvent->type = 2;
            $newEvent->save();
            $event_id = $newEvent->id;

            //Parametry
            $this->manager->addParam($event_id, 'distance', $distance);
            $this->manager->addParam($event_id, 'czas', $czas);
            $this->manager->addParam($event_id, 'plane', $planeId);
            $this->manager->addParam($event_id, 'flight', $flight);
            $this->manager->addParam($event_id, 'paliwo', $paliwo);
            $this->manager->addParam($event_id, 'to', $to);
            $this->manager->addParam($event_id, 'from', $from);

        } elseif ($plane->loaded()) {

            $this->event->user->sendMiniMessage('Samolot ' . $plane->rejestracja . ' nie wystartował.',
                'Samolot ' . $plane->fullName() .
                ' nie wystartował podczas lotu swobodnego z ' . Helper_Map::getCityName($from) . ' do
                ' . Helper_Map::getCityName($to) . ', ponieważ nie stawił się na miejsce startu.',
                1, $this->event->when);

        } else {
            $this->addInfo('EventLoop', 1);
        }
    }
}