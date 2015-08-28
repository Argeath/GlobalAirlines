<?php defined('SYSPATH') or die('No direct script access.');

class Controller_MiniMessages extends Controller_Template {
	public function action_index() {
		$this->template->title = "Powiadomienia";

		$this->template->content = View::factory('biuro/powiadomienia')
		     ->bind('naStrone', $naStrone)
		     ->bind('strona', $strona)
		     ->bind('ilosc', $ilosc)
		     ->bind('powiadomienia', $powiadomienia);

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$strona = (int) $this->request->param('offset');

		if ($strona > 0) {
			$strona--;
		}

		$naStrone = 10;
		$ilosc = 0;

		$offset = $strona * $naStrone;

		$ilosc = ORM::factory("MiniMessage")->where('user_id', '=', $user->id)->count_all();
		$powiadomienia = ORM::factory("MiniMessage")->where('user_id', '=', $user->id)->order_by('data', 'desc')->limit($naStrone)->offset($offset)->find_all();

		$checked = 0;
		foreach ($powiadomienia as $mini) {
			$mini->checked = 1;
			$mini->save();
			$checked++;
		}

		GlobalVars::$nowych_powiadomien -= $checked;
		if (GlobalVars::$nowych_powiadomien < 0) {
			GlobalVars::$nowych_powiadomien = 0;
		}
	}
}
