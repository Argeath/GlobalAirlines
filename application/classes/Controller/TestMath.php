<?php defined('SYSPATH') or die('No direct script access.');

class Controller_TestMath extends Controller_Template {

	public $klasy = array();
	public $klasyKeys = array();
	public $biura = array();
	
	public $Wzlecenia = "";
	public $Woplaty = "";
	
	public $notWorkingZ = false;
	public $notWorkingO = false;
	
	public function action_index()
	{
		$this->template->title = "Test";
		
		$this->template->content = View::factory('testMath')
			->bind('klasa', $klasa)
			->bind('biuraText', $biuraText)
			->bind('klasyText', $klasyText)
			->bind('Wzlecenia', $Wzlecenia)
			->bind('Woplaty', $Woplaty)
			->bind('orders', $orders)
			->bind('distArr', $distArr)
			->bind('notWorkingZ', $notWorkingZ)
			->bind('notWorkingO', $notWorkingO)
			->bind('averagePerHour', $averagePerHour)
			->bind('averageCost', $averageCost)
			->bind('samoloty', $samoloty);
		
		$user = Auth::instance()->get_user();
		if ( ! $user || ! $user->isAdmin())
			$this->redirect('user/login');
			
		$distArr = array();
		$notWorkingZ = false;
		$notWorkingO = false;
		
		$this->biura = (array)Kohana::$config->load('biura');
		$this->klasy = (array)Kohana::$config->load('classes');
		$this->klasyKeys = array_keys($this->klasy);
		
		$biuroId = (int)$this->request->param('biuropodrozy');
		if($biuroId == 0)
			$biuroId = 1;
		
		if($biuroId > 0)
		{
			if( $biuroId > count($this->biura))
			{
				sendError('Błąd. Złe biuro.');
				$this->redirect("Zlecenia");
			}
			$biuro = $this->biura[$biuroId];
		}
		$biuraText = "";
		foreach($this->biura as $k => $v)
		{
			$biuraText .= '<li '.(($k == $biuroId) ? "class='active'" : "").'>'.HTML::anchor('testMath/'.$k, $v).'</li>';
		}
		
		$klasa = (int)$this->request->param('klasa');
		
		$klasyZlecenia = Kohana::$config->load('zlecenia.zlecenia');
		$klasyZlecenia = $klasyZlecenia[$biuroId][1];
		$mozliweKlasy = array();
		$klasyText = "";
		foreach($klasyZlecenia as $k => $v)
		{
			if($v == 0) continue;
			$mozliweKlasy[] = $k;
		}
		if( ! in_array($klasa, $mozliweKlasy))
			$klasa = $mozliweKlasy[0];
		if(count($mozliweKlasy) > 1)
		{
			foreach($klasyZlecenia as $k => $v)
			{
				if($v == 0) continue;
				$klasyText .= '<li '.(($k == $klasa) ? "class='active'" : "").'>'.HTML::anchor('testMath/'.$biuroId.'/'.$k, $this->klasyKeys[$k]).'</li>';
			}
		}
		
		$testMath = ORM::factory("TestMath")->where('biuro', '=', $biuroId)->and_where('klasa', '=', $klasa)->find();
		if( ! $testMath->loaded())
		{
			$testMath = ORM::factory("TestMath");
			$testMath->biuro = $biuroId;
			$testMath->klasa = $klasa;
			$testMath->Wzlecenia = "1";
			$testMath->Woplaty = "1";
			$testMath->save();
		}
		$this->Wzlecenia = $testMath->Wzlecenia;
		$this->Woplaty = $testMath->Woplaty;
		
		$post = $this->request->post();
		if(isset($post) && ! empty($post))
		{
			if(isset($post['Wzlecenia']) && isset($post['Woplaty']))
			{
				if($post['Wzlecenia'] == "")
					$post['Wzlecenia'] = "1";
				if($post['Woplaty'] == "")
					$post['Woplaty'] = "1";
				$testMath->Wzlecenia = $post['Wzlecenia'];
				$testMath->Woplaty = $post['Woplaty'];
				$testMath->notWorkingO = 0;
				$testMath->notWorkingZ = 0;
				$testMath->save();
				$this->resetOrders($biuroId);
				$this->redirect('testMath/'.$biuroId.'/'.$klasa);
			}
		}
		
		$Wzlecenia = $this->Wzlecenia;
		$Woplaty = $this->Woplaty;
		$notWorkingZ = (bool)$testMath->notWorkingZ;
		$this->notWorkingZ = (bool)$testMath->notWorkingZ;
		$notWorkingO = (bool)$testMath->notWorkingO;
		$this->notWorkingO = (bool)$testMath->notWorkingO;
		
		$this->generuj($biuroId, $klasa);
		
		
		$orders = ORM::factory("Order")->where('biuro', '=', $biuroId)->and_where('count', 'BETWEEN', $this->klasy[$this->klasyKeys[$klasa]])->and_where('test', '=', 1)->limit(15)->find_all();
			
		$oilCost = 5;
		
		//Zoptymalizować - cache etc
		$averageSpeedSum = 0;
		$averageSpalanieSum = 0;
		$planes = ORM::factory("Plane")->cached(60)->where('klasa', '=', $klasa+1)->and_where('ukryty', '=', 0)->order_by('cena', 'ASC')->find_all();
		
		foreach($planes as $p)
		{
			$averageSpeedSum += $p->predkosc;			
			$averageSpalanieSum += $p->spalanie;			
		}
		$averageSpeed = $averageSpeedSum / count($planes);
		$averageSpalanie = $averageSpalanieSum / count($planes);
		
		$klasaS = $klasa + 1;
		
		$averagePerHour = 0;
		$averageCost = 0;
		$averageCostSum = 0;
		$averagePerHourSum = 0;
		$averageCount = 0;
		
		
		$samoloty = array();
		foreach($planes as $p)
		{
			$samolot = array();
			$samolot['name'] = $p->fullName()."<br />
				<div class='text-rounded bg-blue Jtooltip inline' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Zasięg samolotu' style='display:inline-block;width: 80px; margin-bottom: 5px;font-size: 10px;'><i class='fa fa-arrows-h'></i> ".$p->zasieg." km</div> 
				<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Miejsca pasażerskie' style='display:inline-block;width: 50px; margin-bottom: 5px;font-size: 10px;'><i class='fa fa-users'></i> ".$p->miejsc."</div> 
				<div class='text-rounded ".(($p->wyrozSpalanie == 1) ? 'bg-orange' : 'bg-blue')." Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Spalanie silników' style='display:inline-block;width: 100px; margin-bottom: 5px;font-size: 10px;'><i class='fa fa-fire'></i> ".$p->spalanie." kg/km</div>";
			$staffCost = $p->getPreferStaffCost();
			$samolot['staff'] = $staffCost." ".WAL;
			$samolot['orders'] = array();
			$spalanieS = $oilCost*$p->spalanie;
			foreach($orders as $n => $a)
			{
				if( ! isset($distArr[$a->from]) || ! isset($distArr[$a->from][$a->to]))
				{
					$from = ORM::factory("City", $a->from);
					if( ! isset($distArr[$a->from]))
						$distArr[$a->from] = $from->getDistances();
				}
				$distance = $distArr[$a->from][$a->to];
				if($distance > $p->zasieg || $a->count > $p->miejsc || $this->notWorkingO)
				{
					$samolot['orders'][$a->id] = "";
					continue;
				}
				
				$kadryCost = -round(($distance/100)*$staffCost);
				$paliwoCost = -round($spalanieS*$distance);
				try {
					$math = new Math();
					$math->registerVariable('D', $distance);
					$math->registerVariable('P', $a->count);
					$math->registerVariable('M', $p->miejsc);
					$math->registerVariable('S', $p->spalanie);
					$math->registerVariable('Z', $p->zasieg);
					$math->registerVariable('V', $p->predkosc);
					$math->registerVariable('AV', $averageSpeed);
					$math->registerVariable('AS', $averageSpalanie);
					$math->registerVariable('K', $klasaS);
					$math->registerVariable('B', $biuroId);
					$dodatkowe = -$math->evaluate($this->Woplaty);
				} catch(Exception $e)
				{
					$testMath->notWorkingO = 1;
					$testMath->save();
					$this->redirect('testMath/'.$r.'/'.$k);
				}
				$cost = $kadryCost+$paliwoCost+$dodatkowe;
				$zysk = $a->cash+$cost;
				$czas = ($distance / ($p->predkosc*0.85)) + (ceil($p->miejsc/75)/4);
				$phour = $zysk / $czas;
				$czas = $czas * 3600;
				$czasT = secondsToText($czas);
				
				$order = "";
				$order .= "Kadry: ".Prints::colorNumber($kadryCost)." ".WAL."<br />";
				$order .= "Oil: ".Prints::colorNumber($paliwoCost)." ".WAL."<br />";
				$order .= "Dod.: ".Prints::colorNumber($dodatkowe)." ".WAL."<br />";
				$order .= "Zl.: ".Prints::colorNumber($a->cash)." ".WAL."<br /><br />";
				$order .= Prints::colorBgNumber($zysk);
				$order .= "<br /><br />".$czasT;
				$order .= "<br />".round($phour)." /h";
				
				$averagePerHourSum += round($phour);
				$averageCostSum += round($cost);
				$averageCount++;
				
			
				$samolot['orders'][$a->id] = $order;
			}
		
			$samoloty[] = $samolot;
		}
		if($averageCount > 0)
		{
			$averagePerHour = round($averagePerHourSum / $averageCount);
			$averageCost = round($averageCostSum / $averageCount);
		}
	}
	
	public function generuj($r, $k)
	{
		try {
		
			$ilosc = ORM::factory("Order")->where('biuro', '=', $r)->and_where('count', 'BETWEEN', $this->klasy[$this->klasyKeys[$k]])->and_where('test', '=', 1)->count_all();
			if($ilosc >= 15 || $this->notWorkingO)
				return;
			set_time_limit(0);
			$arrvals = array();
			$regions = array();
			$ilosciConfig = Kohana::$config->load('zlecenia.zlecenia');
			switch($r) {
				case 1:
				case 2:
					$regions = Map::getRegions();
					break;
				case 3:
				case 4:
				case 5:
				case 6:
					$regions = Map::getContinents();
					break;
				case 7:
				case 8:
				case 9:
				case 10:
				case 11:
				case 12:
					$regions = array('WR');
					break;
			}
			
			$testMath = ORM::factory("TestMath")->where('biuro', '=', $r)->and_where('klasa', '=', $k)->find();
			if( ! $testMath->loaded())
				continue;
			
			$onlyBig = false;
			if($r == 8 || $r == 9 || $r == 10 || $r == 11 || $r == 12)
				$onlyBig = true;
				
			
			//Zoptymalizować - cache etc
			$averageSpeedSum = 0;
			$averageSpalanieSum = 0;
			$averageRangeSum = 0;
			$averageMiejscSum = 0;
			$planes = ORM::factory("Plane")->where('klasa', '=', $k+1)->and_where('ukryty', '=', 0)->find_all();
			foreach($planes as $p)
			{
				$averageSpeedSum += $p->predkosc;
				$averageSpalanieSum += $p->spalanie;
				$averageRangeSum += $p->zasieg;
				$averageMiejscSum += $p->miejsc;
			}
			$averageSpeed = round($averageSpeedSum / count($planes));
			$averageSpalanie = round($averageSpalanieSum / count($planes));
			$averageRange = round($averageRangeSum / count($planes));
			$averageMiejsc = round($averageMiejscSum / count($planes));
			
			
			//foreach($regions as $regionKey => $region)
			$key = array_keys($regions);
			$size = sizeOf($key);
			for ($reg=0; $reg<$size; $reg++)
			{
				$zlecen = 0;
				$region = &$regions[$key[$reg]];
				$cities = Map::getRegionCities($region, $onlyBig);
				$miast = $cities->count() - 1;
				for($ci = 0; $ci <= $miast; $ci++)
				{
					$city = $cities[$ci];
					if( ! $city->loaded())
					{
						errToDB('[Zlecenia][Biuro: '.$r.'][Rand: '.$rand.'][City: '.$city->id.']');
						continue;
					}
					
					$mozliweMiasta = array();
					if($r == 1)
						$mozliweMiasta = $city->getCitiesInRange($cities, 150, 2000);
					elseif($r == 2)
						$mozliweMiasta = $city->getCitiesInRange($cities, 200, 2000);
					elseif($r == 3)
						$mozliweMiasta = $city->getCitiesInRange($cities, 500, 2500);
					elseif($r == 4)
						$mozliweMiasta = $city->getCitiesInRange($cities, 500, 4500);
					elseif($r == 5)
						$mozliweMiasta = $city->getCitiesInRange($cities, 1000, 4500);
					elseif($r == 6)
						$mozliweMiasta = $city->getCitiesInRange($cities, 1000, 4000);
					elseif($r == 7)
						$mozliweMiasta = $city->getCitiesInRange($cities, 2500, 4500);
					elseif($r == 8)
						$mozliweMiasta = $city->getCitiesInRange($cities, 5000, 10000);
					elseif($r == 9)
						$mozliweMiasta = $city->getCitiesInRange($cities, 3000, 12000);
					elseif($r == 10)
						$mozliweMiasta = $city->getCitiesInRange($cities, 6000, 14000);
					elseif($r == 11)
						$mozliweMiasta = $city->getCitiesInRange($cities, 2000, 5000);
					elseif($r == 12)
						$mozliweMiasta = $city->getCitiesInRange($cities, 2000, 8500);
					
					$mozliwychMiast = count($mozliweMiasta) - 1;
					if($mozliwychMiast < 1)
						continue;
						
					$mozliwychKeys = array_keys($mozliweMiasta);
						
					$distances = $city->getDistances();
					$ilosci = $ilosciConfig[$r][$city->rozmiar];
					$vi = 1;
					$il = DB::select(array(DB::expr('COUNT(`id`)'), 'total_orders'))->from('zlecenia')->where('from', '=', $city->id)->and_where('count', 'BETWEEN', $this->klasy[$this->klasyKeys[$k]])->and_where('region', '=', $region)->and_where('biuro', '=', $r)->and_where('test', '=', 1)->execute()->get('total_orders', 0);
					if($il < $vi)
					{
						$brakujacych = $vi - $il;
						$i = 1;
						while($i <= $brakujacych)
						{
							$toId = rand(0, $mozliwychMiast);
							$dystans = $mozliwychKeys[$toId];
							$to = $mozliweMiasta[$mozliwychKeys[$toId]];
							if( ! $to->loaded())
							{
								errToDB('[Zlecenia][Biuro: '.$r.'][Region: '.$region.'][CitiesCount: '.$mozliwychMiast.']');
								continue;
							}
							if($city->id == $to->id)
								continue;
							
							$miejsc = rand($this->klasy[$this->klasyKeys[$k]][0], $this->klasy[$this->klasyKeys[$k]][1]);
							
								
							try {
							$math = new Math();
							$klasaS = $k + 1;
							$math->registerVariable('D', $dystans);
							$math->registerVariable('P', $miejsc);
							$math->registerVariable('V', $averageSpeed);
							$math->registerVariable('S', $averageSpalanie);
							$math->registerVariable('R', $averageRange);
							$math->registerVariable('M', $averageMiejsc);
							$math->registerVariable('K', $klasaS);
							$math->registerVariable('B', $r);
							$kasa = $math->evaluate($this->Wzlecenia);
							$kara = $kasa * 1.3;
							} catch(Exception $e)
							{
								$testMath->notWorkingZ = 1;
								$testMath->save();
								$this->redirect('testMath/'.$r.'/'.$k);
							}
							
							$arrvals[] = "('".$region."', ".$r.", ".$city->id.", ".$to->id.", ".round($kasa).", ".round($kara).", ".$miejsc.", ".$_SERVER['REQUEST_TIME'].", ".($_SERVER['REQUEST_TIME'] + rand(172800, 345600)).", 1)";
							$zlecen++;
							if($zlecen >= (15/$size))
								break;
							$i++;
						}
						if($zlecen >= (15/$size))
							break;
					}
					unset($distances);
				}
				unset($region);
				unset($cities);
			}
			unset($regions);
		} catch(Exception $e)
		{
			errToDB('['.__CLASS__.'][Exception: '.$e->getMessage().'][Line: '.$e->getLine().']');
			return false;
		}
		$vals = implode(',', $arrvals);
		if( ! empty($vals))
			DB::query(Database::INSERT, 'INSERT INTO `zlecenia` (`region`, `biuro`, `from`, `to`, `cash`, `punish`, `count`, `started`, `deadline`, `test`) VALUES '.$vals.'')->execute();
		unset($arrvals);
		unset($vals);
	}
	
	public function resetOrders($r)
	{
		DB::query(Database::DELETE, 'DELETE FROM `zlecenia` WHERE `test` = 1 AND `biuro` = '.$r.'')->execute();
	}
};