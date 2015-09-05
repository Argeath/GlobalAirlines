<?php defined('SYSPATH') or die('No direct script access.');

class Events_OrderFlight extends Events_Event {
    protected function doWork() {
        $flight = $this->event->user->flights->where('event', '=', $this->event->id)->find();
        if ($flight->loaded() && $flight->canceled == 0 && $flight->user_id == $this->event->user->id) {
            $plane = $flight->UserPlane;
            $to = $flight->to;
            $distance = $this->parameters['distance'];
            $timeInAir = $this->parameters['czas'];
            $zlecenieId = $this->parameters['zlecenie'];

            if ($plane->loaded() && $plane->user_id == $this->event->user->id) {
                $userOrder = ORM::factory("UserOrder", $zlecenieId);
                if ($userOrder->loaded() && $userOrder->user_id == $this->event->user->id) {
                    if ($userOrder->done == 0) {
                        $order = $userOrder->order;
                        $mechanik = $plane->plane->mechanicy;
                        $stan = round((rand(1, 10) / 100) * ceil($timeInAir / 1800) * 2 / sqrt($mechanik), 2);

                        $xp = ceil(pow($order->count + 3, 0.66) * pow(($distance / 100) + 1, 0.66));
                        if ($xp < 5) {
                            $xp = mt_rand(1, 4);
                        }

                        $plane->updateStaffConditionToFuture($this->event->when);

                        $msg = 'Samolot ' . $plane->fullName() . ' dotarł do miasta ' .
                            Helper_Map::getCityName($to) . '.<br />Zapłata: ' .
                            formatCash($order->cash) . ' ' . WAL . '<br />Spadek stanu samolotu: ' .
                            $stan . '%<br />Otrzymano ' . $xp . ' punktów doświadczenia.';

                        $this->event->user->sendMiniMessage('Samolot ' . $plane->rejestracja . ' dotarł do miasta ' .
                            Helper_Map::getCityName($to), $msg, $flight->end);

                        $userOrder->done = 1;
                        $userOrder->save();

                        $plane->km += $distance;
                        $plane->hours += $timeInAir;
                        $plane->stan -= $stan;
                        $plane->updateStaffExperience($xp * 2);
                        $plane->updateStaffPosition();
                        $plane->save();

                        $this->event->user->km += $distance;
                        $this->event->user->hours += $timeInAir;
                        $info = array('type' => Helper_Financial::LotZlecenie,
                            'plane_id' => $plane->id,
                            'order_id' => $order->id);

                        $this->event->user->operateCash($order->cash, 'Zapłata za zlecenie (' .
                            Helper_Map::getCityName($order->from) . ' -> ' .
                            Helper_Map::getCityName($order->to) . ') wykonane samolotem - ' .
                            $plane->fullName() . '.', $this->event->when, $info);

                        $this->event->user->pasazerow += $order->count;
                        $this->event->user->zlecen++;
                        $this->event->user->addExperience($xp, $this->event->when);
                        $this->event->user->save();
                    } else
                        $this->addInfo('EventLoop', 4);
                } else
                    $this->addInfo('EventLoop', 2);
            } else
                $this->addInfo('EventLoop', 1);
        } else
            $this->addInfo('EventLoop', 3);
    }
}