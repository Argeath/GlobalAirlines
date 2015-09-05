<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Validation extends Controller
{
    public function before()
    {
        $this->auto_render = !$this->request->is_ajax();
        if ($this->auto_render === TRUE) {
            parent::before();
        }
    }

    public function action_index()
    {
        $user = Auth::instance()->get_user();
        if (!$user) {
            $this->redirect('user/login');
        }
    }

    public function action_register()
    {
        try {
            $valid = Validation::factory($this->request->post());
            foreach((new Model_User)->rules() as $key => $value)
                $valid->rules($key, $value);

            if ($valid->check()) {
                return json_encode(['status' => 'success']);
            }
        } catch(ORM_Validation_Exception $exception) {
            return json_encode($exception->errors('models'));
        }
    }
}