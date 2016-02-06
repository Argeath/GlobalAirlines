<?php defined('SYSPATH') or die('No direct script access.');

class Events_WorkerFlight extends Events_Event {
    protected function doWork() {
        $pracId = $this->parameters['pracId'];
        $to = $this->parameters['to'];
        $planeId = $this->parameters['plane'];

        /*if ($planeId->loaded()) {
            $planeId = $planeId->value;
        }*/

        /** @var Model_Staff $worker */
        $worker = ORM::factory("Staff", $pracId);
        $worker->position = $to;

        if ($planeId) {
            $plane = ORM::factory("UserPlane", $planeId);
            $planeModel = $plane->plane;
            $pilotow = $planeModel->piloci;
            $dodatkowej = $planeModel->zaloga_dodatkowa;
            $juzPilotow = $plane->staff->where('type', '=', 'pilot')->count_all();
            $juzDodatkowej = $plane->staff->where('type', '!=', 'pilot')->count_all();

            if ( (($worker->type == 'pilot' && $juzPilotow < $pilotow)
                    || ($worker->type == 'stewardessa' && $juzDodatkowej < $dodatkowej))
                && ($worker->position == $plane->position)
                && ($worker->user_id == $plane->user_id) ) {

                $worker->plane_id = $plane->id;
                $worker->save();
            }
        }
        $worker->save();
    }
}