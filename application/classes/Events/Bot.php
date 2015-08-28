<?php defined('SYSPATH') or die('No direct script access.');

class Events_Bot extends Events_Event {

    protected function doWork()
    {
        $this->event->user->doBotStuff($this->event->when);
    }

}