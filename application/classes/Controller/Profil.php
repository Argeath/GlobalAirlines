<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Profil extends Controller_Template {
	public function action_index()
	{
		$this->template->content = View::factory('profil')
			->bind('znajomi', $znajomi)
			->bind('planes', $planes)
			->bind('offices', $offices)
			->bind('chart1', $chart1)
			->bind('gracz', $gracz);
		
		$user = Auth::instance()->get_user();
		if ( ! $user)
			$this->redirect('user/login');
		
		$graczId = (int)$this->request->param('graczid');
		if($graczId == 0)
			$graczId = $user->id;
		$gracz = ORM::factory("User", $graczId);
		if( ! $gracz->loaded())
		{
			sendError("Nie znaleziono takiego gracza.");
			$this->redirect('Podglad');
		}
		
		$planes = $gracz->UserPlanes->order_by('rejestracja', 'ASC')->find_all();
		$offices = $gracz->bazy->find_all();
		
		$znajomi = $user->isInContactWith($gracz);
		$this->template->title = "Profil gracza - ".$gracz->username;
		
		$user->updateProfitHistory(time() - (31*24*60*60));
		
		$chart1A = array();
		$history = json_decode($gracz->profitHistory, true);
		$elements = count($history);
		$getElements = 30;
		if($elements < 30)
			$getElements = $elements;
			
			
		$coTrzeci = 0;
		$suma = 0;
		for($i = $elements - $getElements; $i < $elements; $i++)
		{
			$e = $history[$i];
			$dateT = date('Y-m-d H:i', $e['date']);
			if(++$coTrzeci >= 3)
			{
				$chart1A[] = "['".$dateT."', ".$suma."]";
				$suma = 0;
				$coTrzeci = 0;
			} else
				$suma += $e['profit'];
		}
		
		$todayD = getDate();
		$today = mktime(0, 0, 0, $todayD['mon'], $todayD['mday'], $todayD['year']);
		$fin = $gracz->financials->where('data', 'BETWEEN', array($today, time()))->find_all();
		$profit = 0;
		foreach($fin as $f)
			$profit += $f->change;
		$chart1A[] = "['".date('Y-m-d H:i')."', ".$profit."]";
		
		$chart1 = implode(",", $chart1A);
	}
	
	public function action_szukaj()
	{
		$this->template->title = "Wyniki wyszukiwania";
		
		$this->template->content = View::factory('szukaj')
			->bind('nick', $nick)
			->bind('strona', $strona)
			->bind('naStrone', $naStrone)
			->bind('offset', $offset)
			->bind('ilosc', $ilosc)
			->bind('players', $players);
		
		$user = Auth::instance()->get_user();
		if ( ! $user)
			$this->redirect('user/login');
		
		$strona = (int)$this->request->param('offset');
		
		if($strona > 0)
			$strona--;
		
		$naStrone = 50;
		$ilosc = 0;
		
		$offset = $strona * $naStrone;
		
		
		$nick = "";
		$post = $this->request->post();
		if( ! $post)
			$nick = $this->request->param('nick');
		else
			$nick = $post['nick'];
		
		
		$players = "";
		
		$ilosc = $q = ORM::factory("User")->where('username', 'LIKE', '%'.$nick.'%')->count_all();
		$q = ORM::factory("User")->where('username', 'LIKE', '%'.$nick.'%')->limit($naStrone)->offset($offset)->find_all();
		if(count($q) == 1 && $strona == 0)
		{
			$this->redirect('profil/'.$q[0]->id);
		} elseif(count($q) >= 1)
		{
			foreach($q as $p)
			{
				$avatar = $p->getAvatar();
				$text = HTML::anchor('profil/'.$p->id, "<img src='".$avatar."' class='img-thumbnail'/><br />".$p->username);
				$players .= "<div class='thumbnail col-lg-1 col-md-2 col-xs-4'>".$text."</div>";
			}
		} else
		{
			$players = "<tr><td colspan='2'>Nie znaleziono żadnego gracza</td></tr>";
		}
	}

};