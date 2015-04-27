<?php defined('SYSPATH') or die('No direct script access.');

class Controller_FirstLogin extends Controller_Template {
	public function action_index()
	{
		$this->template->title = "Wybór nazwy gracza";
		
		$this->template->content = View::factory('user/firstLogin');
		
		$user = Auth::instance()->get_user();
		if ( ! $user)
		{
			$this->redirect('user/login');
		}
		
		
		if($user->username != NULL)
			$this->redirect('Podglad');
		
		$post = $this->request->post();
		if($post && isset($post))
		{
			if(isset($post['nazwa']) && !empty($post['nazwa']))
			{
				try
				{
					$user->username = $post['nazwa'];
					$user->save();
					sendMsg("Nazwa gracza została ustawiona.");
					$this->redirect("Podglad");
				}
				catch (ORM_Validation_Exception $e)
				{
					foreach($e->errors('models') as $err)
					{
						sendError($err);
					}
				}
			}
		}
	}
};