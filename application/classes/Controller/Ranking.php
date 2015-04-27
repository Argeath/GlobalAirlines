<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Ranking extends Controller_Template {

	public function action_index()
	{
		$this->template->title = "Ranking";
		
		$this->template->content = View::factory('ranking')
			->bind('users', $users)
			->bind('strona', $strona)
			->bind('offset', $offset)
			->bind('ilosc', $ilosc)
			->bind('naStrone', $naStrone)
			->bind('miejsce', $miejsce)
			->bind('zaznacz', $miejsceUsr)
			->bind('szukany', $szukany)
			->bind('typ', $typ);
		
		$user = Auth::instance()->get_user();
		if ( ! $user)
			$this->redirect('user/login');
			
		$typ = (int)$this->request->param('id');
		$strona = (int)$this->request->param('offset');
		
		if($strona > 0)
			$strona--;
		
		$naStrone = 20;
		$ilosc = 0;
		
		$zaznacz = 0;
		$szukany = "";
		
		$post = $this->request->post();
		if($post && ! empty($post))
		{
			if(isset($post['ZnajdzGracza']))
			{
				if($post['ZnajdzGracza'] == 'Znajdź siebie')
					$post['nick'] = $user->username;
				
				if( ! empty($post['nick']))
				{
					$usr = ORM::Factory("User")->where('username', 'LIKE', $post['nick'])->find();
					if($usr->loaded())
					{
						if($typ == 0)
							$miejsceUsr = ORM::Factory("User")->where('exp', '>', $usr->exp)->count_all()+1;
						elseif($typ == 1)
							$miejsceUsr = ORM::Factory("User")->where('km', '>', $usr->km)->count_all()+1;
						elseif($typ == 2)
							$miejsceUsr = ORM::Factory("User")->where('hours', '>', $usr->hours)->count_all()+1;
						elseif($typ == 3)
							$miejsceUsr = ORM::Factory("User")->where('pasazerow', '>', $usr->pasazerow)->count_all()+1;
						elseif($typ == 4)
							$miejsceUsr = ORM::Factory("User")->where('zlecen', '>', $usr->zlecen)->count_all()+1;
						elseif($typ == 5)
							$miejsceUsr = ORM::Factory("User")->where('niewykonanych', '>', $usr->niewykonanych)->count_all()+1;
						elseif($typ == 6)
							$miejsceUsr = ORM::Factory("User")->where('wypadkow', '>', $usr->wypadkow)->count_all()+1;
					
						$strona = floor($miejsceUsr / $naStrone);
						$szukany = $usr->username;
					} else
						sendError('Nie znaleziono takiego gracza.');
					unset($usr);
				}
			}
		}
		
		$offset = $strona * $naStrone;
		
		$users = null;
		$miejsce = 0;
		if($typ == null || $typ == 0) // By Lvl
		{
			$ilosc = ORM::factory('User')->where('exp', '>', 0)->count_all();
			$users = ORM::factory('User')->where('exp', '>', 0)->order_by('exp', 'desc')->limit($naStrone)->offset($offset)->find_all();
			$miejsce = ORM::factory('User')->where('exp', '>', $user->exp)->count_all()+1;
		} else if($typ == 1) // By kms
		{
			$ilosc = ORM::factory('User')->where('km', '>', 0)->count_all();
			$users = ORM::factory('User')->where('km', '>', 0)->order_by('km', 'desc')->limit($naStrone)->offset($offset)->find_all();
			$miejsce = ORM::factory('User')->where('km', '>', $user->km)->count_all()+1;
		} else if($typ == 2) // By hrs
		{
			$ilosc = ORM::factory('User')->where('hours', '>', 0)->count_all();
			$users = ORM::factory('User')->where('hours', '>', 0)->order_by('hours', 'desc')->limit($naStrone)->offset($offset)->find_all();
			$miejsce = ORM::factory('User')->where('hours', '>', $user->hours)->count_all()+1;
		} else if($typ == 3) // By passagers
		{
			$ilosc = ORM::factory('User')->where('pasazerow', '>', 0)->count_all();
			$users = ORM::factory('User')->where('pasazerow', '>', 0)->order_by('pasazerow', 'desc')->limit($naStrone)->offset($offset)->find_all();
			$miejsce = ORM::factory('User')->where('pasazerow', '>', $user->pasazerow)->count_all()+1;
		} else if($typ == 4) // By zlecen
		{
			$ilosc = ORM::factory('User')->where('zlecen', '>', 0)->count_all();
			$users = ORM::factory('User')->where('zlecen', '>', 0)->order_by('zlecen', 'desc')->limit($naStrone)->offset($offset)->find_all();
			$miejsce = ORM::factory('User')->where('zlecen', '>', $user->zlecen)->count_all()+1;
		} else if($typ == 5) // By niewykonanych zlecen
		{
			$ilosc = ORM::factory('User')->where('niewykonanych', '>', 0)->count_all();
			$users = ORM::factory('User')->where('niewykonanych', '>', 0)->order_by('niewykonanych', 'desc')->limit($naStrone)->offset($offset)->find_all();
			$miejsce = ORM::factory('User')->where('niewykonanych', '>', $user->niewykonanych)->count_all()+1;
		} else if($typ == 6) // By wypadkow
		{
			$ilosc = ORM::factory('User')->where('wypadkow', '>', 0)->count_all();
			$users = ORM::factory('User')->where('wypadkow', '>', 0)->order_by('wypadkow', 'desc')->limit($naStrone)->offset($offset)->find_all();
			$miejsce = ORM::factory('User')->where('wypadkow', '>', $user->wypadkow)->count_all()+1;
		}
		$users = $users->as_array();
		
	}
	
};