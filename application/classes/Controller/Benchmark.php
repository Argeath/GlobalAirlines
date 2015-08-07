<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Benchmark extends Controller_Template {
	public function action_index() {
		$this->template->title = "Benchmark";

		$this->template->content = View::factory('benchmark')
		     ->bind('durations', $durations);

		/*$user = Auth::instance()->get_user();
		if (!$user || !$user->isAdmin()) {
		$this->redirect('user/login');
		}*/

		//echo Debug::vars(optimizeDB());

		set_time_limit(0);
		$durations = array();

		$start = microtime_float();
		$this->first();
		$stop = microtime_float();
		$durations[] = $stop - $start;

		$start = microtime_float();
		$this->second();
		$stop = microtime_float();
		$durations[] = $stop - $start;
	}

	private function first() {

		/*$cities = ORM::factory("City")->find_all();
		$cities2 = ORM::factory("City")->find_all();
		foreach ($cities as $c) {
		foreach ($cities2 as $c2) {
		$q = DB::select('id')->from('distances')->where('from', '=', $c->id)->and_where('to', '=', $c2->id)->execute()->as_array();
		if (count($q) > 1) {
		DB::delete('distances')->where('id', '=', $q[1]['id'])->execute();
		}
		}
		}*/

		/*$cities = ORM::factory("City")->find_all();
	foreach($cities as $c)
	{
	$w = ($c->rozmiar == 1) ? 4 : 2;
	$server = ORM::factory("User", 0);
	$checkins = $server->checkins->where('city_id', '=', $c->id)->count_all();
	$w -= $checkins;
	if($w < 1)
	continue;
	for($i=$checkins+1; $i <= $checkins + $w; $i++)
	{
	$check = ORM::factory("Checkin");
	$check->user_id = 0;
	$check->city_id = $c->id;
	$check->office_id = 0;
	$check->public = 1;
	$check->cost = 5 + (5*$i);
	$check->reservations = 86400 * 3;
	$check->minCheckin = 0;
	$check->maxCheckin = $i * 3600;
	$check->level = $i;
	$check->save();
	}
	}*/
	}

	private function second() {
	}

	public function action_phpInfo() {
		phpinfo();
	}
};