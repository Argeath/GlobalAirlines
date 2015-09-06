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

    public function action_getFrom() {
        if( ! $this->authenticated) {
            echo json_encode(['status' => 'fail', 'error' => 'Not authenticated']);
            $this->response->status(403);
            return false;
        }

        $id = (int)$this->request->param('id');

        $events = ORM::factory("Event")->where('done', '=', 0)->and_where('id', '>', $id)->find_all()->as_array('id', 'when');

        echo json_encode(['status' => 'success', 'data' => [ 'events' => $events ]]);
    }

    public function action_getTime() {
        if( ! $this->authenticated) {
            echo json_encode(['status' => 'fail', 'error' => 'Not authenticated']);
            $this->response->status(403);
        }

        echo json_encode(['status' => 'success', 'data' => [ 'time' => time() ]]);
    }

    public function action_execute() {
        if( ! $this->authenticated) {
            echo json_encode(['status' => 'fail', 'error' => 'Not authenticated']);
            $this->response->status(403);
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