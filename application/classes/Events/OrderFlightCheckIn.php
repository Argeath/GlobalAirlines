<?php defined('SYSPATH') or die('No direct script access.');

class Events_OrderFlightCheckIn extends Events_Event {
    protected function doWork() {
        $planeId = $this->parameters['plane'];
        $zlecenieId = $this->parameters['zlecenie'];
        $timeInAir = $this->parameters['czas'];

        $plane = ORM::factory("UserPlane", $planeId);
        if ($plane->loaded() && $plane->user_id == $this->event->user->id) {
            $userOrder = ORM::factory("UserOrder", $zlecenieId);
            if ($userOrder->loaded() && $userOrder->user_id == $this->event->user->id) {
                $flight = $this->event->user->flights->where('id', '=', $userOrder->flight_id)->find();
                if ($flight->loaded()) {
                    $newEvent = ORM::factory("Event");
                    $newEvent->user_id = $this->event->user->id;
                    $newEvent->when = $this->event->when + $timeInAir;
                    $newEvent->type = 5;
                    $newEvent->save();
                    $event_id = $newEvent->id;

                    $pilotow = $plane->staff->where('type', '=', 'pilot')->count_all();
                    if ($pilotow == 0) {
                        $pilotow = 1;
                    }

                    $stanZaMinute = 0.025; // Tyle za kazda minute lotu
                    $stanZaLot = round((($timeInAir / 60) * $stanZaMinute) / $pilotow, 2);
                    $plane->updateStaffConditionFuture(-$stanZaLot);

                    $staffArray = array();
                    foreach ($plane->staff->find_all() as $s) {
                        $staffArray[] = array($s->id, $s->condition, $s->conditionFuture);
                    }

                    $flight->checked = 1;
                    $flight->event = $event_id;
                    $flight->staff = json_encode($staffArray);
                    $flight->save();

                    $distance = Helper_Map::getDistanceBetween($flight->from, $flight->to);

                    //Parametry
                    $this->manager->addParam($event_id, 'czas', $timeInAir);
                    $this->manager->addParam($event_id, 'zlecenie', $zlecenieId);
                    $this->manager->addParam($event_id, 'distance', $distance);

                    $plane->position = $userOrder->order->to;
                    $plane->save();
                } else {
                    $this->addInfo("EventLoop", 4);
                }
            } else {
                $this->addInfo("EventLoop", 3);
            }
        } else {
            $this->addInfo("EventLoop", 1);
        }
    }
}