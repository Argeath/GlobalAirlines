<?php defined('SYSPATH') or die('No direct script access.');

class Events_FreeFlight extends Events_Event {
    protected function doWork() {
        $flight = $this->user->flights->where('event', '=', $this->event->id)->find();
        if ($flight->loaded()) {
            $plane = $flight->UserPlane;
            $to = $flight->to;
            $distance = $this->parameters['distance'];
            $timeInAir = $this->parameters['czas'];

            $hours = round($timeInAir / 3600);

            $stan = round((rand(1, 5) / 100) * $hours, 2);

            $this->event->user->sendMiniMessage("Samolot " . $plane->rejestracja . " dotar³ do miasta " .
                Helper_Map::getCityName($to), "Samolot " . $plane->fullName() . " dotar³ do miasta " .
                Helper_Map::getCityName($to) . ".<br />Spadek stanu samolotu: " . $stan . "%", 1, $flight->end);

            $plane->position = $to;
            $plane->km += $distance;
            $plane->hours += $timeInAir;
            $plane->stan -= $stan;
            $plane->save();

            $plane->updateStaffConditionToFuture($this->event->when);

            $this->event->user->km += $distance;
            $this->event->user->hours += $timeInAir;
            $this->event->user->save();
        }
    }

}