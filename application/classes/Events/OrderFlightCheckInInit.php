<?php defined('SYSPATH') or die('No direct script access.');

class Events_OrderFlightCheckInInit extends Events_Event {
    protected function doWork() {
        $planeId = $this->parameters['plane'];
        $distance = $this->parameters['distance'];
        $czas = $this->parameters['czas'];
        $zlecenieId = $this->parameters['zlecenie'];
        $paliwo = $this->parameters['paliwo'];
        $to = $this->parameters['to'];
        $odprawa = $this->parameters['odprawa'];

        /** @var Model_UserPlane $plane */
        $plane = ORM::factory("UserPlane", $planeId);
        if ($plane->loaded() && $plane->user_id == $this->event->user->id) {

            $userOrder = ORM::factory("UserOrder", $zlecenieId);

            if ($userOrder->loaded() && $userOrder->user_id == $this->event->user->id && $userOrder->done == 0) {

                $order = $userOrder->order;
                /** @var Model_Flight $flight */
                $flight = $this->event->user->flights->where('id', '=', $userOrder->flight_id)->find();

                if ($flight->loaded() && $flight->user_id == $this->event->user->id) {

                    if ($plane->isBusy($this->event->when, $userOrder->flight_id) == Helper_Busy::NotBusy && $plane->position == $order->from) {

                        $accidentChance = $plane->getAccidentChance() * 100;
                        $rand = rand(0, 10000);

                        if ($rand <= $accidentChance) {
                            $accident = $plane->doAccident($flight);
                            $accident->odprawa_id = $this->event->id;
                            $accident->save();
                        }

                        $newEvent = ORM::factory("Event");
                        $newEvent->user_id = $this->event->user->id;
                        $newEvent->when = $this->event->when + $odprawa;
                        $newEvent->type = 4;
                        $newEvent->save();

                        $event_id = $newEvent->id;
                        $flight->checked = 1;
                        $flight->save();

                        $this->manager->addParam($event_id, 'distance', $distance);
                        $this->manager->addParam($event_id, 'czas', $czas);
                        $this->manager->addParam($event_id, 'plane', $planeId);
                        $this->manager->addParam($event_id, 'zlecenie', $zlecenieId);
                        $this->manager->addParam($event_id, 'paliwo', $paliwo);
                        $this->manager->addParam($event_id, 'to', $to);

                        $this->manager->needOneMoreCycle = true;

                    } elseif ( ! $this->event->user->isBot) {

                        if ($flight->delayed >= 60) {
                            $err = "";

                            if ($plane->position != $order->from) {
                                $err = "Samolot nie stawił się w miejscu startu";
                            }

                            $flight->cancel();
                            $this->event->user->sendMiniMessage('Samolot ' . $plane->rejestracja . ' nie wystartował.',
                                'Samolot ' . $plane->fullName() . ' nie wystartował z ' .
                                Helper_Map::getCityName($order->from) . ' do ' .
                                Helper_Map::getCityName($order->to) . '. ' . $err, $this->event->when);

                        } else {
                            $delay = 5;
                            $flight->delayed += $delay;
                            $flight->started += $delay * 60;
                            $flight->end += $delay * 60;
                            $flight->save();
                            $this->event->when += $delay * 60;
                            $this->event->save();
                            
                            $this->manager->needOneMoreCycle = true;
                        }
                    } else
                        $flight->cancelQuietly();
                } else
                    $this->addInfo('EventLoop', 4);
            } else
                $this->addInfo('EventLoop', '2; Possibly punished]');
        } else
            $this->addInfo('EventLoop', 1);
    }
}