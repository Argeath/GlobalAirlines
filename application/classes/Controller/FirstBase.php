<?php defined('SYSPATH') or die('No direct script access.');

class Controller_FirstBase extends Controller_Template {
	public function action_index()
	{
		$this->template->title = "Wybór bazy początkowej";
		
		$this->template->content = View::factory('user/firstBase');
		
		$user = Auth::instance()->get_user();
		if ( ! $user)
		{
			$this->redirect('user/login');
		}
		
		$post = $this->request->post();
		if($post && isset($post))
		{
			if(isset($post['baza']) && !empty($post['baza']))
			{
				$city = ORM::factory("City", (int)$post['baza']);
				if($city->loaded())
				{
					$l = ORM::factory("Office");
					$l->user_id = $user->id;
					$l->city_id = $city->id;
					$l->save();
					$user->base_id = $l->id;
					$user->save();
					
					$this->redirect('podglad');
				}
			
			}		
		}
	}
};