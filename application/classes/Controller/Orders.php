<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Orders extends Controller_Template {
	public function action_index() {
		$this->template->content = View::factory('orders')
		     ->bind('planes', $planes)
		     ->bind('citiess', $cities);

		$this->template->title = "Biuro podróży";

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$planes = $user->UserPlanes->find_all();

		$showAll = 0;

		if ($showAll == 0) {
			$regions = $user->getActiveRegions();
		} else {
			$regions = Helper_Map::getRegions();
		}

		if ($showAll == 0) {
			$contiregions = $user->getActiveContiRegions();
		} else {
			$contiregions = Helper_Map::getContinents();
		}

		if ($showAll == 0) {
			$cities = $user->getActiveCities();
		} else {
			$cities = ORM::factory('City')->select('id')->order_by('region', 'asc')->order_by('name', 'asc')->find_all()->as_array();
		}
	}

}