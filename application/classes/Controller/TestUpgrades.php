<?php defined('SYSPATH') or die('No direct script access.');

class Controller_TestUpgrades extends Controller_Template {

	public function action_index()
	{
		$this->template->title = "Test ulepszeń";
		
		$this->template->content = View::factory('testUpgrades')
			->bind('classesKeys', $classesKeys)
			->bind('classesConfig', $classesConfig)
			->bind('classesZasiegConfig', $classesZasiegConfig)
			->bind('upgradesConfig', $upgradesConfig)
			->bind('id', $id)
			->bind('plane', $plane)
			->bind('plane_upgrades', $plane_upgrades)
			->bind('classes', $classes);
		
		$user = Auth::instance()->get_user();
		if ( ! $user || ! $user->isAdmin())
			$this->redirect('user/login');

		$classes = array();
		$classesConfig = (array)Kohana::$config->load('classes');
		$classesZasiegConfig = (array)Kohana::$config->load('classes-zasieg');
		$classesKeys = array_keys($classesConfig);
		
		$planes = ORM::Factory("Plane")->where('ukryty', '=', 0)->find_all();
		foreach($planes as $p)
		{
			if( ! isset($classes[$p->klasa]))
				$classes[$p->klasa] = array();
			$classes[$p->klasa][] = $p;
		}
		
		$upgradesConfig = (array)Kohana::$config->load('upgrades.upgrades');
		
		$id = (int)$this->request->param('id');
		
		if($id > 0)
		{
			$plane = ORM::Factory("Plane", $id);
			if( ! $plane->loaded())
			{
				sendError('Wystąpił błąd. Spróbuj ponownie.');
				$this->redirect('testUpgrades');
			}
			
			$post = $this->request->post();
			if($post && ! empty($post))
			{
				if(isset($post['option']) && $post['option'] == 'possible')
				{
					$upgrades = urldecode($post['upgrades']);
					$json = json_decode($upgrades, true);
					foreach($json as $c => $arr)
					{
						$ilosc = 0;
						foreach($arr as $e => $b)
						{
							if($b)
								$ilosc++;
						}
						if($ilosc == 0)
							unset($json[$c]);
					}
					$upgrades = json_encode($json);
					if(empty($json))
						$upgrades = NULL;
					$plane->upgrades = $upgrades;
					$plane->save();
				} elseif(isset($post['option']) && $post['option'] == 'maxes')
				{
					$plane->maxSpalanie = round($plane->spalanie * (1-($post['maxSpalanie']/100)), 2);
					$plane->maxZasieg = round($plane->zasieg * (($post['maxZasieg']/100)+1));
					$plane->maxPredkosc = round($plane->predkosc * (($post['maxPredkosc']/100)+1));
					$plane->maxMiejsc = round($plane->miejsc * (($post['maxMiejsc']/100)+1));
					$plane->komfort = $post['komfort'];
					$plane->save();
				}
			}
			$plane_upgrades = json_decode($plane->upgrades, true);
		}
	}
};