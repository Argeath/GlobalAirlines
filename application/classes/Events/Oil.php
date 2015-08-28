<?php defined('SYSPATH') or die('No direct script access.');

class Events_Oil extends Events_Event {
    protected function doWork() {
        $new = Helper_Oil::calculateOilCost();

        $mysqlDate = date("Y-m-d H:i:s", $this->event->when);
        $duplicateOil = DB::select()->from('oil')->where('data', '=', $mysqlDate)->execute()->as_array();
        if (empty($duplicateOil)) {
            DB::insert('oil')->columns(array('data', 'cena'))->values(array($mysqlDate, $new))->execute();
        }

        $checkEvent = ORM::factory("Event")->where('when', '=', $this->event->when + 3600)->and_where('type', '=', 8)->count_all();
        if ($checkEvent == 0) {
            $newEvent = ORM::factory("Event");
            $newEvent->when = $this->event->when + 3600;
            $newEvent->type = 8;
            $newEvent->save();
        }

        $this->manager->needOneMoreCycle = true;
    }
}