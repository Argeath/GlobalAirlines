<?php defined('SYSPATH') or die('No direct script access.');

class Experience {
	static function getNeededExp($L)
	{
		$a=0;
		for($x=1; $x<$L; $x++) {
			$a += floor($x+300*pow(2, ($x/6)));
		}
		return floor($a/8);
	}

	static function getLevelByExp($E)
	{
		$a=0;
		for($x=1; $x<100; $x++) {
			$a += floor($x+300*pow(2, ($x/6)));
			$needed = floor($a/8);
			if($needed >= $E)
				return $x;
		}
		return 99;
	}

	static function getPercentOfLevel($xp)
	{
		$lvl = Experience::getLevelByExp($xp);
		$previous = Experience::getNeededExp($lvl);
		$next = Experience::getNeededExp($lvl+1);
		$next -= $previous;
		$xp -= $previous;
		$per = round(($xp / $next)*100);
		return $per;
	}

	static function getExpLabel($xp)
	{
		$lvl = Experience::getLevelByExp($xp);
		$previous = Experience::getNeededExp($lvl);
		$next = Experience::getNeededExp($lvl+1);
		$next -= $previous;
		$xp -= $previous;
		return $xp.' / '.$next;
	}
};