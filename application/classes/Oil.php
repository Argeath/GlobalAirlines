<?php defined('SYSPATH') or die('No direct script access.');

class Oil {
	static function getOilCost($ilosc = 1)
	{
		$q = DB::select()->from('oil')->order_by('data', 'DESC')->limit(1)->execute()->as_array();
		if(empty($q)) return 5 * $ilosc;
		return $q[0]['cena'] * $ilosc;
	}

	static function speculateOilDemand()
	{
		$lastWeek = DB::select()->from('oil_demand')->where('startOfWeek', 'BETWEEN', array(time()-Date::WEEK*2, time()-Date::WEEK))->execute()->get('demand', 0);
		if($lastWeek == 0)
			$lastWeek = DB::select()->from('oil_demand')->where('startOfWeek', '<=', time()-Date::WEEK)->order_by('startOfWeek', 'DESC')->limit(1)->execute()->get('demand', 0);
		$thisWeek = DB::select()->from('oil_demand')->where('startOfWeek', 'BETWEEN', array(time()-Date::WEEK, time()))->execute()->as_array();
		if( ! empty($thisWeek))
		{
			$thisWeek = $thisWeek[0];
			$timeExpired = time() - $thisWeek['startOfWeek'];
			$timeToWeekEnd = Date::WEEK - $timeExpired;
			$timePercent = ($timeToWeekEnd / Date::WEEK);
			
			if($timePercent == 0) 
				return $thisWeek['demand'];
				
			if($timePercent > 0.9)
				return $lastWeek * (rand(97, 103)/100);
				
			$speculate = $thisWeek['demand'] / (1-$timePercent);
			return $speculate;	
		}
		return $lastWeek;
	}

	static function getOilAverageCostLastWeek()
	{
		$lastWeek = DB::select()->from('oil_demand')->where('startOfWeek', 'BETWEEN', array(time()-Date::WEEK*2, time()-Date::WEEK))->execute()->get('startOfWeek', 0);
		if($lastWeek == 0)
			$lastWeek = DB::select()->from('oil_demand')->where('startOfWeek', '<=', time()-Date::WEEK)->order_by('startOfWeek', 'DESC')->limit(1)->execute()->get('startOfWeek', 0);
		$week1 = date('Y-m-d H:i:s', $lastWeek);
		$week2 = date('Y-m-d H:i:s', strtotime('+7 days', $lastWeek));
		$oilCosts = DB::select()->from('oil')->where('data', 'BETWEEN', array($week1, $week2))->execute()->as_array();
		if(empty($oilCosts))
			return 5;
		$suma = 0;
		$ilosc = 0;
		foreach($oilCosts as $oC)
		{
			$suma += $oC['cena'];
			$ilosc++;
		}
		return round($suma/$ilosc, 2);
	}

	static function getOilDemandChange()
	{
		$lastWeek = DB::select()->from('oil_demand')->where('startOfWeek', 'BETWEEN', array(time()-Date::WEEK*2, time()-Date::WEEK))->execute()->get('demand', 0);
		if($lastWeek == 0)
			$lastWeek = DB::select()->from('oil_demand')->where('startOfWeek', '<=', time()-Date::WEEK)->order_by('startOfWeek', 'DESC')->limit(1)->execute()->get('demand', 0);
		$thisWeek = Oil::speculateOilDemand();
		if($thisWeek == 0 || $lastWeek == 0) return 1;
		
		$change = $lastWeek / $thisWeek;
		
		return $change;
	}

	static function getOilLastCosts()
	{
		try {
			return DB::select()->from('oil')->where('data', 'BETWEEN', array( date('Y-m-d H:i:s', strtotime('-7 days')), date('Y-m-d H:i:s')))->order_by('data', 'DESC')->execute()->as_array();
		} catch(Exception $e)
		{
			errToDb('['.__FUNCTION__.'][Line: '.$e->getLine().'] '.$e->getMessage());
			return array();
		}
	}

	static function calculateOilCost($act = false)
	{
		//Easy Version - Random cost
		return Oil::calculateRandomOilCost($act);
		
		if($act == false)
			$last = Oil::getOilCost();
		else
			$last = $act;
		$srednia = Oil::getOilAverageCostLastWeek();
		$change = Oil::getOilDemandChange();
		$minMax = $srednia / $change;
		if($minMax < 3.5)
			$minMax = 3.5;
		elseif($minMax > 6)
			$minMax = 6;
		$roznica = $minMax / $last;
		$rand = 0;
		if($change > 1)
		{
			if($roznica > 1.05 || ($minMax+0.05) >= $last)
				$roznica = 1.05;
			elseif($roznica < 0.95)
				$roznica = 0.95;
			$roznica = $roznica - 1;
			$rand = rand(($roznica + 0.95)*100, ($roznica + 1)*100) / 100;
		} else {
			if($roznica < 0.95 || ($minMax-0.05) <= $last)
				$roznica = 0.95;
			elseif($roznica > 1.05)
				$roznica = 1.05;
			$roznica = $roznica - 1;
			$rand = rand(($roznica + 1)*100, ($roznica + 1.05)*100) / 100;
		}
		
		$new = round($last + ($rand-1), 2);
		if( $new != 0 && $last != 0 && (! $new/$last > 0.9 || ! $last/$new > 0.9))
			$new = $last;
			
		/*if($new <= 3.5)
			$new = $new + 0.1;
		elseif($new >= 6.5)
			$new = $new - 0.1;*/

		return $new;
	}

	static function speculateOilCost()
	{
		$thisWeek = DB::select()->from('oil_demand')->where('startOfWeek', 'BETWEEN', array(time()-Date::WEEK, time()))->execute()->as_array();
		if( ! empty($thisWeek))
		{
			$thisWeek = $thisWeek[0];
			$startOfWeek = $thisWeek['startOfWeek'];
			$timeExpired = time() - $thisWeek['startOfWeek'];
			$timeToWeekEnd = Date::WEEK - $timeExpired;
			$interval = 3600;
			$fors = floor($timeToWeekEnd / $interval);
			$cost = Oil::getOilCost();
			for($i = 0; $i < $fors; $i++)
				$cost = Oil::calculateOilCost($cost);
			return $cost;
		}
	}

	static function debugOil()
	{
		return true;
		$srednia = Oil::getOilAverageCostLastWeek();
		$last = DB::select()->from('oil_demand')->where('startOfWeek', 'BETWEEN', array(time()-Date::WEEK*2, time()-Date::WEEK))->execute()->get('demand', 0);
		if($last == 0)
			$last = DB::select()->from('oil_demand')->where('startOfWeek', '<=', time()-Date::WEEK)->order_by('startOfWeek', 'DESC')->limit(1)->execute()->get('demand', 0);
		$speculate = Oil::speculateOilDemand();
		$change = Oil::getOilDemandChange();
		if($change == 0)
			$change = 0.01;
		$minMax = $srednia / $change;
		if($minMax < 3.5)
			$minMax = 3.5;
		elseif($minMax > 6)
			$minMax = 6;
		$speculateC = Oil::speculateOilCost();
		$next = Oil::calculateOilCost();
		echo "<br />Średnia(poprz. tydz.): ".$srednia.WAL."<br />";
		echo "Poprzedni tydz.: ".formatCash($last)."l<br />";
		echo "Spekulacja: ".formatCash($speculate)."l<br />";
		echo "Zmiana: ".formatCash(($change-1)*100, 2)."% (".formatCash($change, 4).")<br />";
		echo "MinMax: ".formatCash($minMax, 2).WAL."<br />";
		echo "Nastepna: ".formatCash($next, 2).WAL."<br />";
		echo "Spekulacja: ".formatCash($speculateC, 2).WAL."<br />";
	}
	
	static function calculateRandomOilCost($act = false)
	{
		if($act == false)
			$last = Oil::getOilCost();
		else
			$last = $act;
			
		$rand = (mt_rand(95, 105)-100)/100;
		if($last >= 5.95)
			$rand = (mt_rand(95, 100)-100)/100;
		elseif($last <= 3.55)
			$rand = (mt_rand(100, 105)-100)/100;
			
		$new = $last + $rand;
		return $new;
	}
	
	static function updateOilDemand($vol)
	{
		try {
			$eves = DB::select()->from('oil_demand')->where('startOfWeek', '>', time()-Date::WEEK)->execute()->as_array();
			if( ! empty($eves))
			{
				$eves = $eves[0];
				$demand = $eves['demand'];
				$demand += $vol;
				DB::update('oil_demand')->set(array('demand' => $demand))->where('id', '=', $eves['id'])->execute();
			} else {
				DB::insert('oil_demand')->columns(array('startOfWeek', 'demand'))->values(array(time(), $vol))->execute();
			}
		} catch(Exception $e)
		{
			errToDb('[Exception]['.__FUNCTION__.'] '.$e->getMessage());
		}
		return true;
	}

};