<?php defined('SYSPATH') or die('No direct script access.');

class Events_FlightCrash extends Events_Event {
    protected function doWork() {
        $accidentId = $this->parameters['accident'];

        /** @var Model_Accident $accident */
        $accident = ORM::factory("Accident", $accidentId);
        if ($accident->loaded() && $accident->user_id == $this->event->user_id) {
            $typ = $accident->getAccidentInfo();
            //$efekt = $accident->getEffectInfo();
            $plane = $accident->plane;
            $flight = $accident->flight;

            $time = round($accident->delay / 1800) * 2;

            if ($typ['when'] == 0)// Przed startem
            {
                $plane->stan -= $accident->condition;
                $plane->save();
                $this->event->user->sendMiniMessage("Awaria", "Samolot " . $plane->fullName() .
                    " miał awarię przed startem. Rozpoczęto naprawę - powinna zająć około " . $time . "h.",
                    $this->event->when);

            } elseif ($typ['when'] == 1) {
                $plane->position = $flight->from;
                $plane->stan -= $accident->condition;
                $plane->save();
                $this->event->user->sendMiniMessage("Awaria", "Samolot " . $plane->fullName() .
                    " miał awarię tuż po starcie i lądował awaryjnie na tym samym lotnisku." .
                    " Rozpoczęto naprawę - powinna zająć około " . $time . "h.",
                    $this->event->when);

            } elseif ($typ['when'] == 2) {
                $czasLotu = $flight->end - $flight->started;
                $czasAwarii = $accident->time - $flight->started;
                $przelecial = round($czasAwarii / $czasLotu * Helper_Map::getDistanceBetween($flight->from, $flight->to));
                $city = Helper_Map::findCityOnPath($flight->from, $flight->to, $przelecial);
                if (!$city) {
                    // TODO: Crash
                } else {
                    $plane->position = $city->id;
                }
                $plane->stan -= $accident->condition;
                $plane->save();
                $this->event->user->sendMiniMessage("Awaria", "Samolot " . $plane->fullName() .
                    " miał awarię tuż po starcie i lądował awaryjnie na lotnisku -
                    " . $city->name . ". Rozpoczęto naprawę - powinna zająć około " . $time . "h.",
                    $this->event->when);

            }
            if ($accident->delay > 3600) {
                $flight->cancel();
            } else {

                $oldEvent = ORM::factory("Event", $accident->odprawa_id);
                if (!$oldEvent->loaded()) {
                    $flight->cancel();
                } else {

                    $newEvent = ORM::factory("Event");
                    $newEvent->user_id = $this->event->user_id;
                    $newEvent->when = $this->event->when + 60;
                    $newEvent->type = 10;
                    $newEvent->save();

                    $event_id = $newEvent->id;

                    $distance = $oldEvent->parameters->where('key', '=', 'distance')->find()->value;
                    $czas = $oldEvent->parameters->where('key', '=', 'czas')->find()->value;
                    $zlecenieId = $oldEvent->parameters->where('key', '=', 'zlecenie')->find()->value;
                    $paliwo = $oldEvent->parameters->where('key', '=', 'paliwo')->find()->value;
                    $to = $flight->to;
                    $odprawa = $oldEvent->parameters->where('key', '=', 'odprawa')->find()->value;

                    $this->manager->addParam($event_id, 'distance', $distance);
                    $this->manager->addParam($event_id, 'czas', $czas);
                    $this->manager->addParam($event_id, 'plane', $plane);
                    $this->manager->addParam($event_id, 'zlecenie', $zlecenieId);
                    $this->manager->addParam($event_id, 'paliwo', $paliwo);
                    $this->manager->addParam($event_id, 'to', $to);
                    $this->manager->addParam($event_id, 'odprawa', $odprawa);
                }
            }
        } else
           $this->addInfo('EventLoop', 1);

        $this->manager->needOneMoreCycle = true;
    }
}