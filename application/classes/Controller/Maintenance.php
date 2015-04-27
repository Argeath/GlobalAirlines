<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Maintenance extends Controller {
	public function action_index()
	{
		$maintenance = false;
		$m = "Wracamy za chwilę.";
		try {
			$q = DB::select()->from('settings')->where('key', '=', 'maintenance')->execute()->as_array();
			if( ! empty($q))
			{
				$q = $q[0];
				if($q['value'] == 1)
				{
					$maintenance = true;
					$msg = DB::select()->from('settings')->where('key', '=', 'maintenance_msg')->execute()->as_array();
					if( ! empty($msg))
					{
						$msg = $msg[0];
						$m = $msg['value2'];
					}
				}
			}
		} catch(Exception $e)
		{
			$maintenance = true;
		}
		if($maintenance)
		{
			$view = View::factory('maintenance')
				->bind('msg', $m);
			$this->response->body($view);
		} else
			$this->redirect('Podglad');
	}
};