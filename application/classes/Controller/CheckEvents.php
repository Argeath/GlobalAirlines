<?php defined('SYSPATH') or die('No direct script access.');

class Controller_CheckEvents extends Controller {
	public function action_index() {
		//$benchmark = Profiler::start('CHECKEVENTS', __FUNCTION__);
		Events::checkEvents();
		//Profiler::stop($benchmark);

		//echo View::factory('profiler/stats');
	}
};