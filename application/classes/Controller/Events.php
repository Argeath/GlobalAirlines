<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Events extends Controller {

    public $authenticated = false;

    public function before() {
        $this->auto_render = !$this->request->is_ajax();
        if ($this->auto_render === TRUE) {
            parent::before();
        }
        $token = $this->request->headers('X-AUTH-TOKEN');
        if($token) {
            if($token === 'aOlxW6PCtqwfwKTQhM9u') {
                $this->authenticated = true;
            }
        }
    }

    public function action_index() {
        throw new Kohana_HTTP_Exception_404();
    }

    public function action_getAll() {
        if( ! $this->authenticated) {
            throw new Kohana_HTTP_Exception_404();
        }

        $events = ORM::factory("Event")->where('done', '=', 0)->find_all()->as_array('id', 'when');

        echo json_encode($events);
    }

    public function action_execute() {
        if( ! $this->authenticated) {
            throw new Kohana_HTTP_Exception_404();
        }

        $id = (int)$this->request->post('event_id');

        if($id == 0) {
            echo json_encode(['status' => 'fail']);
            return false;
        }

        /** @var $event Model_Event */
        $event = ORM::factory("Event", $id);

        if($event->when > time()) {
            echo json_encode(['status' => 'early']);
            return false;
        }

        $eventManager = new Events_EventManager();

        Events::route($event, $eventManager);

        $eventManager->commitParams();
        $eventManager->commitLogs();

        echo json_encode(['status' => 'success']);
        return true;
    }


}