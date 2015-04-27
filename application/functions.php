<?php defined('SYSPATH') or die('No direct script access.');

function cleanDB()
{
	DB::Delete('financials')->where('data', '<', strtotime('-1 month'))->execute();
	DB::Delete('auctions')->where('end', '<', strtotime('-1 month'))->execute();
	DB::Delete('flights')->where('end', '<', strtotime('-1 month'))->execute();
}

function optimizeDB()
{
	try {
		$alltables = DB::Query(DATABASE::SELECT, "SHOW TABLES")->execute()->as_array(null, 'Tables_in_ots');
		echo Debug::vars($alltables);
		foreach($alltables as $k => $table)
		{
			DB::query(Database::SELECT, 'OPTIMIZE TABLE '.$table)->execute();
		}
		return true;
	} catch(Exception $e)
	{
		errToDb('[Exception]['.__FILE__.']['.__FUNCTION__.'][Line: '.$e->getLine().']['.$e->getMessage().']');
	}
	return false;
}

function countOnlineUsers()
{
	try {
		global $users_online;
		//$users_online = DB::select()->from('sessions')->where('last_active', '>=', time()-300)->execute();
		$users_online = ORM::Factory("User")->where('last_login', '>=', time()-900)->find_all();
		return $users_online;
	} catch(Exception $e)
	{
		errToDb('[Exception]['.__FILE__.']['.__FUNCTION__.'][Line: '.$e->getLine().']['.$e->getMessage().']');
	}
	return 0;
}

function timestampToText($timestamp, $year = false)
{
	try {
		$rok = "";
		if($year)
			$rok = ".Y";
		return date("H:i d.m".$rok, $timestamp);
	} catch(Exception $e)
	{
		errToDb('[Exception]['.__FILE__.']['.__FUNCTION__.'][Line: '.$e->getLine().']['.$e->getMessage().']');
	}
	return '';
}


function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}


function sendError($text)
{
	try {
		if(is_array($text))
			$text = reset($text);
			
		$arr = Session::instance()->get('errs');
		if( ! isInArrayByElements($arr, array(0 => $text)))
			$arr[] = array($text, time()+10);
		
		Session::instance()->set('errs', $arr);
		return true;
	} catch(Exception $e)
	{
		errToDb('[Exception]['.__FILE__.']['.__FUNCTION__.'][Line: '.$e->getLine().']['.$e->getMessage().']');
	}
	return false;
}

function sendMsg($text)
{
	try {
		if(is_array($text))
			$text = reset($text);
			
		$arr = Session::instance()->get('msgs');
		if( ! isInArrayByElements($arr, array(0 => $text)))
			$arr[] = array($text, time()+10);
		
		Session::instance()->set('msgs', $arr);
		return true;
	} catch(Exception $e)
	{
		errToDb('[Exception]['.__FILE__.']['.__FUNCTION__.'][Line: '.$e->getLine().']['.$e->getMessage().']');
	}
	return false;
}


function errToDB($text)
{
	try {
		$check = ORM::factory("Error")->order_by('time', 'DESC')->limit(1)->find();
		if($check->loaded() && $check->text == $text)
		{
			$check->occurences++;
			$check->time = time();
			$check->save();
		} else {
			$c = ORM::factory("Error");
			$c->time = time();
			$c->text = $text;
			$c->save();	
		}
		return true;
	} catch(Exception $e)
	{
		errToDb('[Exception]['.__FILE__.']['.__FUNCTION__.'][Line: '.$e->getLine().']['.$e->getMessage().']');
	}
	return false;
}

function rome($N){
	switch($N)
	{
		case 0:
			return '';
		case 1:
			return 'I';
		case 2:
			return "II";
		case 3:
			return "III";
		case 4:
			return "IV";
		case 5:
			return "V";
	
	}
	return $N;
}

function getStatUnit($stat)
{
	try {
		switch($stat)
		{
			case 'spalanie':
				return 'kg/km';
				break;
			case 'zasieg':
				return 'km';
				break;
			case 'predkosc':
				return 'km/h';
				break;
			default:
				return '';
				break;
		}
	} catch(Exception $e)
	{
		errToDb('[Exception]['.__FILE__.']['.__FUNCTION__.'][Line: '.$e->getLine().']['.$e->getMessage().']');
	}
	return '';
}

function printBlocked($xp, $obj)
{
	try {
		$menuLvls = Kohana::$config->load('lvls.biura');
		$lvl = Experience::getLevelByExp($xp);
		$required = $menuLvls[$obj];
		if($required > $lvl)
			return '<div class="blocked"><i class="glyphicon glyphicon-lock"></i> '.$required.' LVL</div>';
	} catch(Exception $e)
	{
		errToDb('[Exception]['.__FILE__.']['.__FUNCTION__.'][Line: '.$e->getLine().']['.$e->getMessage().']');
	}
	return '';
}

function isMenuBlocked($xp, $obj)
{
	try {
		$menuLvls = Kohana::$config->load('lvls.biura');
		
		$lvl = Experience::getLevelByExp($xp);
		$required = $menuLvls[$obj];
		if($required > $lvl)
			return true;
	} catch(Exception $e)
	{
		errToDb('[Exception]['.__FUNCTION__.'][Line: '.$e->getLine().'] '.$e->getMessage());
		return true;
	}
	return false;
	
}

function shortenString($str, $lb=2)
{
	$txt = substr($str, 0, $lb);
	$txt .= '.';
	return $txt;
}

function secondsToText($sec, $maxHours = false, $minMins = false)
{
	try {
		$text = "";
		if($sec < 60)
		{
			$text = $sec."s";
			return $text;
		}
		$min = floor($sec / 60);
		$sec = $sec % 60;
		if($min < 60)
		{
			$text = $min."m ".$sec."s";
			return $text;
		}
		$hr = floor($min / 60);
		$min = $min % 60;
		if( ! $maxHours)
		{
            if($hr < 24)
            {
                if($minMins)
                    $text = $hr."h ".$min."m";
                else
                    $text = $hr."h ".$min."m ".$sec."s";
                return $text;
            }
            $ds = floor($hr / 24);
            if($ds > 1)
                $minMins = true;

            $hr = $hr % 24;
            if($ds > 9)
                $text = $ds."d ".$hr."h";
            else {
                if ($minMins)
                    $text = $ds . "d " . $hr . "h " . $min . "m";
                else
                    $text = $ds . "d " . $hr . "h " . $min . "m " . $sec . "s";
            }
		} else {
            if($hr > 100)
                $text = $hr."h";
            else {
                if ($minMins)
                    $text = $hr . "h " . $min . "m";
                else
                    $text = $hr . "h " . $min . "m " . $sec . "s";
            }
		}
		return $text;
	} catch(Exception $e)
	{
		errToDb('[Exception]['.__FILE__.']['.__FUNCTION__.'][Line: '.$e->getLine().']['.$e->getMessage().']');
	}
	return false;
}



function compareByKey($key, $typ='ASC') {
    return function ($a, $b) use ($key, $typ) {
		if($typ == 'ASC')
			return strnatcmp($a[$key], $b[$key]);
		else
			return strnatcmp($b[$key], $a[$key]);
    };
}


function findInArrayByElements($tab, $elements)
{
	if(empty($tab))
		return false;
	try
	{
		foreach($tab as $arr)
		{
			$dobre = true;
			foreach($elements as $k => $v)
			{
				$ks = explode("/", $k);
				$check = $arr;
				foreach($ks as $kn)
					$check = $check[$kn];
				if($check != $v)
					$dobre = false;
			}
			if($dobre)
				return $arr;
		}
	} catch(Exception $e)
	{
		errToDb('[Exception]['.__FILE__.']['.__FUNCTION__.'][Line: '.$e->getLine().']['.$e->getMessage().']');
	}
	return false;
}

function isInArrayByElements($tab, $elements)
{
	$arr = findInArrayByElements($tab, $elements);
	if(empty($arr))
		return false;
	return true;
}


function paginationPrzesuniecie($ilosc, $act, $max)
{
	try {
		if($ilosc <= $max)
			return 0;
		
		$srodek = ceil($max/2);
		$poBokach = floor($max/2);
		
		if(($act - $poBokach) <= 0)
			return 0;
		
		if(($act + $poBokach) >= ($ilosc-1))
			return ($ilosc - $poBokach*2) - 1;
		
		return $act - $poBokach;
	} catch(Exception $e)
	{
		errToDb('[Exception]['.__FILE__.']['.__FUNCTION__.'][Line: '.$e->getLine().']['.$e->getMessage().']');
	}
	return false;
}


function HexToRGB($hex) {
	$hex = preg_replace("/\#/", "", $hex);
	$color = array();

	if(strlen($hex) == 3) {
		$color['r'] = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
		$color['g'] = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
		$color['b'] = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
	}
	else if(strlen($hex) == 6) {
		$color['r'] = hexdec(substr($hex, 0, 2));
		$color['g'] = hexdec(substr($hex, 2, 2));
		$color['b'] = hexdec(substr($hex, 4, 2));
	}

	return $color;
}

function RGBToHex($r, $g, $b) {
	$hex = "#";
	$hex.= str_pad(dechex($r), 2, "0", STR_PAD_LEFT);
	$hex.= str_pad(dechex($g), 2, "0", STR_PAD_LEFT);
	$hex.= str_pad(dechex($b), 2, "0", STR_PAD_LEFT);
	return $hex;
}

function cleanString($string, $toLower = true, $space = '_'){ 
    $chars=array( 
        chr(195).chr(128) => 'A', chr(195).chr(129) => 'A', 
        chr(195).chr(130) => 'A', chr(195).chr(131) => 'A', 
        chr(195).chr(132) => 'A', chr(195).chr(133) => 'A', 
        chr(195).chr(135) => 'C', chr(195).chr(136) => 'E', 
        chr(195).chr(137) => 'E', chr(195).chr(138) => 'E', 
        chr(195).chr(139) => 'E', chr(195).chr(140) => 'I', 
        chr(195).chr(141) => 'I', chr(195).chr(142) => 'I', 
        chr(195).chr(143) => 'I', chr(195).chr(145) => 'N', 
        chr(195).chr(146) => 'O', chr(195).chr(147) => 'O', 
        chr(195).chr(148) => 'O', chr(195).chr(149) => 'O', 
        chr(195).chr(150) => 'O', chr(195).chr(153) => 'U', 
        chr(195).chr(154) => 'U', chr(195).chr(155) => 'U', 
        chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y', 
        chr(195).chr(159) => 's', chr(195).chr(160) => 'a', 
        chr(195).chr(161) => 'a', chr(195).chr(162) => 'a', 
        chr(195).chr(163) => 'a', chr(195).chr(164) => 'a', 
        chr(195).chr(165) => 'a', chr(195).chr(167) => 'c', 
        chr(195).chr(168) => 'e', chr(195).chr(169) => 'e', 
        chr(195).chr(170) => 'e', chr(195).chr(171) => 'e', 
        chr(195).chr(172) => 'i', chr(195).chr(173) => 'i', 
        chr(195).chr(174) => 'i', chr(195).chr(175) => 'i', 
        chr(195).chr(177) => 'n', chr(195).chr(178) => 'o', 
        chr(195).chr(179) => 'o', chr(195).chr(180) => 'o', 
        chr(195).chr(181) => 'o', chr(195).chr(182) => 'o', 
        chr(195).chr(182) => 'o', chr(195).chr(185) => 'u', 
        chr(195).chr(186) => 'u', chr(195).chr(187) => 'u', 
        chr(195).chr(188) => 'u', chr(195).chr(189) => 'y', 
        chr(195).chr(191) => 'y', 
        chr(196).chr(128) => 'A', chr(196).chr(129) => 'a', 
        chr(196).chr(130) => 'A', chr(196).chr(131) => 'a', 
        chr(196).chr(132) => 'A', chr(196).chr(133) => 'a', 
        chr(196).chr(134) => 'C', chr(196).chr(135) => 'c', 
        chr(196).chr(136) => 'C', chr(196).chr(137) => 'c', 
        chr(196).chr(138) => 'C', chr(196).chr(139) => 'c', 
        chr(196).chr(140) => 'C', chr(196).chr(141) => 'c', 
        chr(196).chr(142) => 'D', chr(196).chr(143) => 'd', 
        chr(196).chr(144) => 'D', chr(196).chr(145) => 'd', 
        chr(196).chr(146) => 'E', chr(196).chr(147) => 'e', 
        chr(196).chr(148) => 'E', chr(196).chr(149) => 'e', 
        chr(196).chr(150) => 'E', chr(196).chr(151) => 'e', 
        chr(196).chr(152) => 'E', chr(196).chr(153) => 'e', 
        chr(196).chr(154) => 'E', chr(196).chr(155) => 'e', 
        chr(196).chr(156) => 'G', chr(196).chr(157) => 'g', 
        chr(196).chr(158) => 'G', chr(196).chr(159) => 'g', 
        chr(196).chr(160) => 'G', chr(196).chr(161) => 'g', 
        chr(196).chr(162) => 'G', chr(196).chr(163) => 'g', 
        chr(196).chr(164) => 'H', chr(196).chr(165) => 'h', 
        chr(196).chr(166) => 'H', chr(196).chr(167) => 'h', 
        chr(196).chr(168) => 'I', chr(196).chr(169) => 'i', 
        chr(196).chr(170) => 'I', chr(196).chr(171) => 'i', 
        chr(196).chr(172) => 'I', chr(196).chr(173) => 'i', 
        chr(196).chr(174) => 'I', chr(196).chr(175) => 'i', 
        chr(196).chr(176) => 'I', chr(196).chr(177) => 'i', 
        chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij', 
        chr(196).chr(180) => 'J', chr(196).chr(181) => 'j', 
        chr(196).chr(182) => 'K', chr(196).chr(183) => 'k', 
        chr(196).chr(184) => 'k', chr(196).chr(185) => 'L', 
        chr(196).chr(186) => 'l', chr(196).chr(187) => 'L', 
        chr(196).chr(188) => 'l', chr(196).chr(189) => 'L', 
        chr(196).chr(190) => 'l', chr(196).chr(191) => 'L', 
        chr(197).chr(128) => 'l', chr(197).chr(129) => 'L', 
        chr(197).chr(130) => 'l', chr(197).chr(131) => 'N', 
        chr(197).chr(132) => 'n', chr(197).chr(133) => 'N', 
        chr(197).chr(134) => 'n', chr(197).chr(135) => 'N', 
        chr(197).chr(136) => 'n', chr(197).chr(137) => 'N', 
        chr(197).chr(138) => 'n', chr(197).chr(139) => 'N', 
        chr(197).chr(140) => 'O', chr(197).chr(141) => 'o', 
        chr(197).chr(142) => 'O', chr(197).chr(143) => 'o', 
        chr(197).chr(144) => 'O', chr(197).chr(145) => 'o', 
        chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe', 
        chr(197).chr(148) => 'R', chr(197).chr(149) => 'r', 
        chr(197).chr(150) => 'R', chr(197).chr(151) => 'r', 
        chr(197).chr(152) => 'R', chr(197).chr(153) => 'r', 
        chr(197).chr(154) => 'S', chr(197).chr(155) => 's', 
        chr(197).chr(156) => 'S', chr(197).chr(157) => 's', 
        chr(197).chr(158) => 'S', chr(197).chr(159) => 's', 
        chr(197).chr(160) => 'S', chr(197).chr(161) => 's', 
        chr(197).chr(162) => 'T', chr(197).chr(163) => 't', 
        chr(197).chr(164) => 'T', chr(197).chr(165) => 't', 
        chr(197).chr(166) => 'T', chr(197).chr(167) => 't', 
        chr(197).chr(168) => 'U', chr(197).chr(169) => 'u', 
        chr(197).chr(170) => 'U', chr(197).chr(171) => 'u', 
        chr(197).chr(172) => 'U', chr(197).chr(173) => 'u', 
        chr(197).chr(174) => 'U', chr(197).chr(175) => 'u', 
        chr(197).chr(176) => 'U', chr(197).chr(177) => 'u', 
        chr(197).chr(178) => 'U', chr(197).chr(179) => 'u', 
        chr(197).chr(180) => 'W', chr(197).chr(181) => 'w', 
        chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y', 
        chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z', 
        chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z', 
        chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z', 
        chr(197).chr(190) => 'z', chr(197).chr(191) => 's', 
        chr(226).chr(130).chr(172) => 'E', 
        chr(194).chr(163) => '', 
        ' ' => $space 
  ); 

    $string = strtr($string, $chars); 
   if ($toLower && function_exists('mb_strtolower')) { 
     return mb_strtolower($string); 
   } else { 
     return strtolower($string); 
   }
}

function cmp_balanceHistory($a, $b) {
  return $a["date"] - $b["date"];
}

function build_sorter($key) {
    return function ($a, $b) use ($key) {
        return strcmp($a[$key], $b[$key]);
    };
}

function findClosestValue($arr, $x)
{
	try {
		$closest = -1;
		foreach($arr as $k => $v)
		{
			if($x >= $v && $closest < $k)
				$closest = $k;
		}
		return $closest;
	} catch(Exception $e)
	{
		errToDb('[Exception]['.__FILE__.']['.__FUNCTION__.'][Line: '.$e->getLine().']['.$e->getMessage().']');
	}
	return 0;
}

function formatCash($int, $miejsc = 0, $shorten = false)
{
	try {
		$ks = 0;
		if($shorten)
		{
			if($int >= 100000)
			{
				$int = $int / 1000;
				$ks++;
			}
			if($int >= 10000)
			{
				$int = $int / 1000;
				$ks++;
			}
		}
		$nbr = number_format($int, $miejsc, ',', ' ');
		$text = $nbr.'';
		for($i=0; $i < $ks; $i++)
		{
			$text .= 'k';
		}
		return $text;
	} catch(Exception $e)
	{
		errToDb('[Exception]['.__FILE__.']['.__FUNCTION__.'][Line: '.$e->getLine().']['.$e->getMessage().']');
	}
	return '';
}


function getContrastYIQ($hexcolor){
	$r = hexdec(substr($hexcolor,0,2));
	$g = hexdec(substr($hexcolor,2,2));
	$b = hexdec(substr($hexcolor,4,2));
	$yiq = (($r*299)+($g*587)+($b*114))/1000;
	return ($yiq >= 128) ? 'black' : 'white';
}

function percent2Color($value,$brightness = 255, $max = 100,$min = 0, $thirdColorHex = '00')
{       
    // Calculate first and second color (Inverse relationship)
    $first = (1-($value/$max))*$brightness;
    $second = ($value/$max)*$brightness;

    // Find the influence of the middle color (yellow if 1st and 2nd are red and green)
    $diff = abs($first-$second);    
    $influence = ($brightness-$diff)/2;     
    $first = intval($first + $influence);
    $second = intval($second + $influence);

    // Convert to HEX, format and return
    $firstHex = str_pad(dechex($first),2,0,STR_PAD_LEFT);     
    $secondHex = str_pad(dechex($second),2,0,STR_PAD_LEFT); 

    return $firstHex . $secondHex . $thirdColorHex ; 

    // alternatives:
    // return $thirdColorHex . $firstHex . $secondHex; 
    // return $firstHex . $thirdColorHex . $secondHex;

}


function eventTypeToName($typ)
{
	try {
		$types = array(
			'',
			'Lot swobodny',
			'Odprawa lotu swobodnego',
			'Deadline zlecenia',
			'Odprawa lotu na zlecenie',
			'Lot na zlecenie',
			'Usunięcie konta',
			'Lot pracownika',
			'Aktualizacja ceny paliwa',
			'Przegląd generalny samolotu',
			'Przewidywana odprawa lotu na zlecenie',
			'Przewidywana odprawa lotu swobodnego',
			'Aukcje',
			'Regeneracja stanu załogi');
		return (isset($types[$typ])) ? $types[$typ] : 'Nieznany';
	} catch(Exception $e)
	{
		errToDb('[Exception]['.__FILE__.']['.__FUNCTION__.'][Line: '.$e->getLine().']['.$e->getMessage().']');
	}
	return 'Nieznany';
}

function eventTypeToShort($typ)
{
	try {
		$types = array(
			'',
			'LS',
			'OS',
			'DZ',
			'OZ',
			'LZ',
			'UK',
			'LP',
			'CP',
			'PG',
			'PZ',
			'PS');
		return (isset($types[$typ])) ? $types[$typ] : 'NN';
	} catch(Exception $e)
	{
		errToDb('[Exception]['.__FILE__.']['.__FUNCTION__.'][Line: '.$e->getLine().']['.$e->getMessage().']');
	}
	return 'NN';
}

?>