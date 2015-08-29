<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Paliwo extends Controller_Template {
	public function action_index()
	{
		$this->template->title = "Paliwo";
		
		$this->template->content = View::factory('biuro/paliwo')
			->bind('razemP', $razemP)
			->bind('razemW', $razemW)
			->bind('cenyT', $cenyT)
			->bind('bazyT', $bazyT);
		
		$user = Auth::instance()->get_user();
		if ( ! $user)
			$this->redirect('user/login');
		
		$airportConfig = Kohana::$config->load('airport');
		$tank = $airportConfig['tank'];
		
		$post = $this->request->post();
		if($post && isset($post))
		{
			if( ! empty($post['bazaId']) && ! empty($post['ilosc']))
			{
				$bazaId = (int)$post['bazaId'];
				$ilosc = (int)$post['ilosc'];
				$koszt = Helper_Oil::getOilCost($ilosc);
				
				if($bazaId > 0 && $ilosc > 0)
				{
					$baza = ORM::factory("Office", $bazaId);
					if($baza->loaded() && $baza->user->id == $user->id)
					{
						//Maksymalna pojemnosc paliwa
						if(($baza->cysterny*$tank['volume']) < ($baza->oil+$ilosc))
						{
							$ilosc = ($baza->cysterny*$tank['volume']) - $baza->oil;
							$koszt = getOilCost($ilosc);
						}
						if($user->cash >= $koszt)
						{
							$baza->oil += $ilosc;
							$baza->save();
							Helper_Oil::updateOilDemand($ilosc);
							$info = array('type' => Helper_Financial::Paliwo, 'office_id' => $baza->id);
							$user->operateCash(-$koszt, 'Zakup '.formatCash($ilosc).'l paliwa na lotnisku - '.$baza->getName().'.', time(), $info);
							sendMsg('Kupileś '.$ilosc.' litrów paliwa do bazy w mieście '.$baza->getName().'.');
							$this->redirect('paliwo');
						} else
							sendError('Nie masz wystarczajaco pieniedzy.');
					} else
						sendError('Niepoprawna baza.');
				} else
					sendError('Nie poprawna ilosc.');
			}
		}

		$ceny = Helper_Oil::getOilLastCosts();
		$cenyA = array();
		$cenyT = "";
		foreach($ceny as $cena)
		{
			$phpdate = strtotime($cena['data']);
			$mysqldate = date('Y-m-d H:i', $phpdate);
			$cenyA[] = "['".$mysqldate."', ".$cena['cena']."]";
		}
		$cenyT = implode(",", $cenyA);


		GlobalVars::$bazy = $user->bazy->find_all();
		$razemP = $razemW = 0;

		// TODO: Move to View
		$bazyT = "";
		foreach(GlobalVars::$bazy as $baza)
		{
			$wartosc = Helper_Oil::getOilCost($baza->oil);
			$bazyT .= "<tr><td>".$baza->city->name."</td><td>".formatCash($baza->oil, 2)." / ".formatCash($baza->cysterny*$tank['volume']) ." l</td><td>".formatCash($wartosc, 0, true)." ".WAL."</td><td>".HTML::anchor('paliwo/kup/'.$baza->id, 'Kup', ['class' => "btn btn-primary btn-block"])."</td></tr>";
			$razemP += $baza->oil;
			$razemW += $wartosc;
			unset($lotnisko);
		}
	}
	
	public function action_kup()
	{
		$this->template->title = "Kupowanie paliwa";
		
		$this->template->content = View::factory('biuro/paliwoKup')
			->bind('max', $max)
			->bind('baza', $baza);
		
		$bazaId = 0;
		$max = 0;
		$user = Auth::instance()->get_user();
		if ( ! $user)
		{
			$this->redirect('user/login');
		}
		
		$bazaId = (int)$this->request->param('id');
		$baza = ORM::factory('Office', $bazaId);
		
		if( ! $baza->loaded() || $user->id != $baza->user->id)
			return sendError('Niepoprawna baza.');
		
		$max = $baza->cysterny*5000 - $baza->oil;
	}
};