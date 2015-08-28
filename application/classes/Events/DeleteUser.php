<?php defined('SYSPATH') or die('No direct script access.');

class Events_DeleteUser extends Events_Event {
    protected function doWork()
    {
        $this->event->user->delete();
    }
}