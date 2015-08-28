<?php defined('SYSPATH') or die('No direct script access.');

class Events_PlaneInspection extends Events_Event {
    protected function doWork() {
        $planeId = $this->parameters['plane'];
        $plane = ORM::factory("UserPlane", $planeId);
        if ($plane->loaded() && $plane->user_id == $this->event->user->id) {
            $stan = $plane->stan;
            $xp = round((100 - $stan) * 2);

            $msg = 'Samolot ' . $plane->fullName() .
                ' przeszed� przegl�d generalny i zosta� naprawiony.<br />Dosta�e� ' .
                $xp . ' punkt�w do�wiadczenia.';

            $this->event->user->sendMiniMessage('Samolot ' . $plane->rejestracja .
                ' przeszed� przegl�d generalny i zosta� naprawiony.', $msg, $this->event->when);

            $plane->stan = 100;
            $plane->save();
            $this->event->user->exp += $xp;
            $this->event->user->save();
        } else {
            $this->addInfo('EventLoop', 1);
        }
    }
}