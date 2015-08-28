<?php defined('SYSPATH') or die('No direct script access.');

class Events_OrderDeadline extends Events_Event {
    protected function doWork() {
        $zlecenieId = $this->parameters['zlecenie'];

        $punish = false;
        $userOrder = ORM::factory("UserOrder", $zlecenieId);
        if ($userOrder->loaded() && $userOrder->user_id == $this->event->user->id) {
            $order = $userOrder->order;
            if ($userOrder->flight_id != null) {
                $flight = ORM::factory("Flight", $userOrder->flight_id);
                if ($flight->end > $order->deadline) {
                    $punish = true;
                }
            } else {
                $punish = true;
            }

            if ($punish) {
                $punish = $order->cash * 1.2;
                $msg = "Zap³aci³eœ karê - " . formatCash($punish) . " " . WAL . " za zlecenie z miasta " .
                    Helper_Map::getCityName($order->from) . " do miasta " .
                    Helper_Map::getCityName($order->to) . ".";

                $this->event->user->sendMiniMessage("Zap³acono karê za niewykonanie zlecenia.", $msg, $order->deadline);
                $info = array('type' => Helper_Financial::Deadline, 'order_id' => $order->id);

                $this->event->user->niewykonanych++;
                $this->event->user->operateCash(-$punish, 'Kara za niewykonanie zlecenia (' .
                    Helper_Map::getCityName($order->from) . ' -> ' .
                    Helper_Map::getCityName($order->to) . ').', $this->event->when, $info);

                $userOrder->done = 1;
                $userOrder->punished = 1;
                $userOrder->save();

                if ($userOrder->flight_id != null) {
                    $flight = ORM::factory("Flight", $userOrder->flight_id);
                    $event = ORM::factory("Event", $flight->event);
                    if ($event->loaded()) {
                        $event->done = 0;
                        $event->save();
                    }
                }
            }
        }
    }
}