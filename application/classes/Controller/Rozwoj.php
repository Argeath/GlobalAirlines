<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Rozwoj extends Controller_Template {

	public function action_index()
	{
		$this->template->title = "Rozwój gry";
		$this->template->content = View::factory('rozwoj')
			->bind('username', $username);
		
		$user = Auth::instance()->get_user();
		if ( ! $user)
			$this->redirect('user/login');
			
		$username = $user->username;
	}
	
	public function action_success()
	{
		$this->template->title = "Rozwój gry";
		$this->template->content = View::factory('rozwojSuccess');
		
		$user = Auth::instance()->get_user();
		if ( ! $user)
			$this->redirect('user/login');
			
		
	}
	
};