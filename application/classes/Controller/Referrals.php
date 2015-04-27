<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Referrals extends Controller_Template {
	public function action_index()
	{
		$this->template->title = "System poleconych";
		
		$this->template->content = View::factory('biuro/referrals')
			->bind('url', $url)
			->bind('milestones', $milestones)
			->bind('referrals', $referrals);
		
		$user = Auth::instance()->get_user();
		if ( ! $user)
			$this->redirect('user/login');
			
		$referrals = $user->referrals->find_all();
		
		$milestones = $user->ref_milestones;
		
		$url = URL::base('http').'ref/'.$user->id;
		
		$test = ORM::factory("User", 171);
		$test->addExperience(5000);
		
	}
};