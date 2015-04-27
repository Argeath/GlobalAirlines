<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Journal extends Controller_Template {
	public function action_index()
	{
		$this->template->title = "Dziennik finansowy";
		$this->template->content = View::factory('biuro/journal')
			->bind('strona', $strona)
			->bind('naStrone', $naStrone)
			->bind('ilosc', $ilosc)
			->bind('financials', $financialsArr);
		
		$user = Auth::instance()->get_user();
		if ( ! $user)
			$this->redirect('user/login');
		
		$strona = (int)$this->request->param('offset');
		
		if($strona > 0)
			$strona--;
		
		$naStrone = 20;
		$ilosc = 0;
		
		$offset = $strona * $naStrone;
		
		$ilosc = $user->financials->count_all();
		$financials = $user->financials->order_by('data', 'DESC')->limit($naStrone)->offset($offset)->find_all();
		$financialsArr = array();
		foreach($financials as $f)
		{
			$arr = array();
			$arr['dzien'] = strftime("%A, %d.%m.%Y", $f->data);
			
			$arr['byl'] = false;
			//if(isInArrayByElements($financialsArr, array('dzien' => $arr['dzien'])))
			//	$arr['byl'] = true;
				
			$arr['godzina'] = strftime("%R", $f->data);
			$arr['description'] = $f->description;
			$arr['change'] = $f->change;
			$arr['balance'] = $f->balance;
			
			$financialsArr[] = $arr;
		}
	}
	
	public function action_summation()
	{
		$this->template->title = "Dziennik finansowy - podsumowanie";
		$this->template->content = View::factory('biuro/summation')
			->bind('dni', $dni)
			->bind('from', $from)
			->bind('to', $to)
			->bind('razem', $razem)
			->bind('poczatek', $poczatek)
			->bind('dane', $dane)
			->bind('chart1', $chart1)
			->bind('chart2', $chart2)
			->bind('plane_id', $plane_id)
			->bind('planes', $planes);
		
		$user = Auth::instance()->get_user();
		if ( ! $user)
			$this->redirect('user/login');
		
		$from = (int)$this->request->param('from');
		$to = (int)$this->request->param('to');
		$plane_id = (int)$this->request->param('plane');

		$date = getDate();
		$poczatekMiesiaca = mktime(0, 0, 0, $date['mon'], 1, $date['year']);
		$koniecMiesiaca = mktime(23, 59, 59, $date['mon']+1, 0, $date['year']);
		
		if($from < 1356998400)
			$from = $poczatekMiesiaca;
		if($to < 1356998400)
			$to = $koniecMiesiaca;
			
		$post = $this->request->post();
		if($post && ! empty($post))
		{
			$from = 0;
			$to = 0;
			if( isset($post['from']) && (int)$post['from'] > 0)
				$from = $post['from'];
			if( isset($post['to']) && (int)$post['to'] > 0)
				$to = $post['to'];

            $from = strtotime($from);
            $to = strtotime($to);
				
			if($from < 1356998400)
				$from = $poczatekMiesiaca;
			if($to < 1356998400)
				$to = $koniecMiesiaca;
				
			if($plane_id > 0)
				$this->redirect('summation/'.$from.'/'.$to.'/'.$plane_id);
			else
				$this->redirect('summation/'.$from.'/'.$to);
		}
			
		$dni = $to - $from / (24*60*60);
			
		$plane = null;
		if($plane_id > 0)
		{
			$plane = ORM::factory("UserPlane", $plane_id);
			if( ! $plane->loaded() || $plane->user_id != $user->id)
			{
				$plane = null;
			}
		}
		$poczatek = null;
		$razem = 0;
		
		$planes = $user->UserPlanes->find_all();
		
		$dane = array();
		
		$journals = $user->financials->where('data', 'BETWEEN', array($from, $to))->order_by('data', 'ASC')->find_all();
		foreach($journals as $j)
		{
			$info = $j->getInfo();
			
			if( ! isset($info['type']))
				continue;
			
			if($plane != null)
			{
				if( ! isset($info['plane_id']) || $info['plane_id'] != $plane_id)
					continue;
			}
			
			if($poczatek == null)
				$poczatek = $j->balance;
			$razem += $j->change;
			
			$typ = $info['type'];
				
			if( isset($dane[$typ]))
				$dane[$typ] += $j->change;
			else
				$dane[$typ] = $j->change;
		}
		
		$user->updateBalanceHistory(time() - (31*24*60*60));
		
		$chart1A = array();
		$history = json_decode($user->balanceHistory, true);
		$elements = count($history);
		$getElements = 30;
		if($elements < 30)
			$getElements = $elements;
		
		for($i = $elements - $getElements; $i < $elements; $i++)
		{
			$e = $history[$i];
			$dateT = date('Y-m-d H:i', $e['date']);
			$balance = $e['balance'];
			$chart1A[] = "['".$dateT."', ".$balance."]";
		}
		$chart1A[] = "['".date('Y-m-d H:i')."', ".$user->cash."]";
		$chart1 = implode(",", $chart1A);
		
		$user->updateProfitHistory(time() - (31*24*60*60));
		
		$chart2A = array();
		$history = json_decode($user->profitHistory, true);
		$elements = count($history);
		$getElements = 30;
		if($elements < 30)
			$getElements = $elements;
		
		for($i = $elements - $getElements; $i < $elements; $i++)
		{
			$e = $history[$i];
			$dateT = date('Y-m-d H:i', $e['date']);
			$balance = $e['profit'];
			$chart2A[] = "['".$dateT."', ".$balance."]";
		}
		
		$todayD = getDate();
		$today = mktime(0, 0, 0, $todayD['mon'], $todayD['mday'], $todayD['year']);
		$fin = $user->financials->where('data', 'BETWEEN', array($today, time()))->find_all();
		$profit = 0;
		foreach($fin as $f)
			$profit += $f->change;
		$chart2A[] = "['".date('Y-m-d H:i')."', ".$profit."]";
		
		$chart2 = implode(",", $chart2A);
		
	}
};