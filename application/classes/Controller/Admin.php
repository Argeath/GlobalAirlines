<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin extends Controller_Template {
	public function action_index()
	{
	
	}
	
	public function action_updates()
	{
		$this->template->title = "Czasy updatów";
		
		$this->template->content = View::factory('admin/updates')
			->bind('eves', $eves);
	
		$eves = DB::select()->from('check_eventTypes')->execute()->as_array();
	
	}
	
	public function action_zbanuj()
	{
		$user = Auth::instance()->get_user();
		if ( ! $user)
			$this->redirect('user/login');
			
		if( ! $user->isAdmin())
		{
			sendError('Nie masz dostępu.');
			$referrer = $this->request->referrer();
			$this->redirect($referrer);
		}	
			
		$post = $this->request->post();
		if($post && isset($post['user']) && isset($post['data']))
		{
			$gracz = ORM::factory("User")->where('username', '=', $post['user'])->find();
			
			if($gracz->loaded())
			{
				$czas = (int)$post['data'];
				$d = new DateTime();
				if( ! empty($czas))
					$d = new DateTime($czas);
				
				$time = $d->getTimestamp();
				if(($time+3600) < time())
					$time = time() + 3600;
					
				if($gracz->bannedTo > time())
					$gracz->bannedTo += ($time-time());
				else
					$gracz->bannedTo = $time;
				$gracz->save();
				sendMsg('Zbanowałeś gracza '.$gracz->username.' do '.Helper_TimeFormat::timestampToText($gracz->bannedTo, true).'.');
			}
		}
		$referrer = $this->request->referrer();
		$this->redirect($referrer);
	}
	
	public function action_odbanuj()
	{
		$user = Auth::instance()->get_user();
		if ( ! $user)
			$this->redirect('user/login');
		
		if( ! $user->isAdmin())
		{
			sendError('Nie masz dostępu.');
			$referrer = $this->request->referrer();
			$this->redirect($referrer);
		}
		
		$post = $this->request->post();
		if($post && isset($post['user']))
		{
			$gracz = ORM::factory("User")->where('username', '=', $post['user'])->find();
			
			if($gracz->loaded())
			{
				if($gracz->bannedTo > time()) {
					$gracz->bannedTo = NULL;
					$gracz->save();
					sendMsg('Odbanowałeś gracza '.$gracz->username.'.');
				} else {
					sendError('Gracz '.$gracz->username.' nie był zbanowany.');
				}
			}
		}
		$referrer = $this->request->referrer();
		$this->redirect($referrer);
	}
	
	public function action_zmutuj()
	{
		$user = Auth::instance()->get_user();
		if ( ! $user)
			$this->redirect('user/login');
		
		if( ! $user->isAdmin())
		{
			sendError('Nie masz dostępu.');
			$referrer = $this->request->referrer();
			$this->redirect($referrer);
		}
		
		$post = $this->request->post();
		if($post && isset($post['user']) && isset($post['data']))
		{
			$gracz = ORM::factory("User")->where('username', '=', $post['user'])->find();
			
			if($gracz->loaded())
			{
				$czas = (int)$post['data'];
				$d = new DateTime();
				if( ! empty($czas))
					$d = new DateTime($czas);
				
				$time = $d->getTimestamp();
				if(($time+60) < time())
					$time = time() + 60;
					
				if($gracz->mutedTo > time())
					$gracz->mutedTo += ($time-time());
				else
					$gracz->mutedTo = $time;
				$gracz->save();
				sendMsg('Zmutowałeś gracza '.$gracz->username.' do '.Helper_TimeFormat::timestampToText($gracz->mutedTo, true).'.');
			}
		}
		$referrer = $this->request->referrer();
		$this->redirect($referrer);
	}
	
	public function action_odmutuj()
	{
		$user = Auth::instance()->get_user();
		if ( ! $user)
			$this->redirect('user/login');
			
		if( ! $user->isAdmin())
		{
			sendError('Nie masz dostępu.');
			$referrer = $this->request->referrer();
			$this->redirect($referrer);
		}	
			
		$post = $this->request->post();
		if($post && isset($post['user']))
		{
			$gracz = ORM::factory("User")->where('username', '=', $post['user'])->find();
			
			if($gracz->loaded())
			{
				if($gracz->mutedTo > time()) {
					$gracz->mutedTo = NULL;
					$gracz->save();
					sendMsg('Odmutowałeś gracza '.$gracz->username.'.');
				} else {
					sendError('Gracz '.$gracz->username.' nie był zmutowany.');
				}
			}
		}
		$referrer = $this->request->referrer();
		$this->redirect($referrer);
	}
	
	public function action_kasa()
	{
		$user = Auth::instance()->get_user();
		if ( ! $user)
			$this->redirect('user/login');
		
		if( ! $user->isAdmin())
		{
			sendError('Nie masz dostępu.');
			$referrer = $this->request->referrer();
			$this->redirect($referrer);
		}
		
		$post = $this->request->post();
		if($post && isset($post['user']) && isset($post['kasa']))
		{
			$gracz = ORM::factory("User")->where('username', 'LIKE', $post['user'])->find();
			
			if($gracz->loaded())
			{
				$kasa = (int)$post['kasa'];
				$gracz->operateCash($kasa, 'Dotacja od admina.', false, array('type' => Helper_Financial::Dotacja));
				sendMsg('Dałeś graczowi '.$gracz->username.' '.formatCash($kasa).' '.WAL.'.');
			} else
				sendError('Nie znaleziono takiego gracza.');
		} else
			sendError('Wystąpił błąd. Spróbuj ponownie.');
		$referrer = $this->request->referrer();
		$this->redirect($referrer);
	}
};