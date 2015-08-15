<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Warsztat extends Controller_Template {

	public function action_index()
	{
		$this->template->title = "Warsztat";
		$this->template->content = View::factory('hangar/warsztat')
			->bind('planesData', $planesData);
		
		$user = Auth::instance()->get_user();
		if ( ! $user)
			$this->redirect('user/login');
		
		$planes = $user->UserPlanes->find_all();

		$planesData = [];
		foreach($planes as $plane)
		{
			$typ = $plane->plane;
			$poz = $plane->city->name;
			$disabled = array();
			$disabledT = array();
			if($plane->isBusy() != Helper_Busy::NotBusy)
			{
				$disabled = array('disabled' => 'disabled');
				$disabledT = array('class' => 'disabled');
			}
			$upgradow = $plane->getUpgradesCount();
			if($upgradow == 0)
				$upgrades = 100;
			else
				$upgrades = ($plane->getUpgradedCount() / $plane->getUpgradesCount()) * 100;

			$planesData[] = [
				'plane' => $plane,
				'typ' => $typ,
				'poz' => $poz,
				'upgrades' => $upgrades,
				'disabled' => $disabled,
				'disabledText' => $disabledT
			];
		}
	}
	
	public function action_przeglad()
	{
		$this->template->title = "Przegląd generalny";
		
		$this->template->content = View::factory('hangar/przeglad_gen')
			->bind('name', $name)
			->bind('planeId', $planeId)
			->bind('stan', $stan)
			->bind('czas', $czas)
			->bind('cost', $cost);
			
		$user = Auth::instance()->get_user();
		if ( ! $user)
			$this->redirect('user/login');
		
		$cost = 0;
		$name = "";
		$stan = 0;
		$planeId = 0;
		$czas = 0;
		
		$planeId = (int)$this->request->param('id');
		if($planeId == 0)
		{
			sendError('Wystapił błąd. Spróbuj ponownie.');
			$this->redirect('warsztat');
		}
		
		$plane = ORM::factory("UserPlane", $planeId);
		if( ! $plane->loaded() || $user->id != $plane->user_id) {
			return sendError('Błąd. Nie ma takiego samolotu.');
			$this->redirect('warsztat');
		}
		
		if( $plane->isBusy() != Helper_Busy::NotBusy)
		{
			sendError('Samolot jest aktualnie w użyciu.');
			$this->redirect('warsztat');
		}
		$typ = $plane->plane;
			
			
		$name = $typ->producent.' '.$typ->model;
		$stan = $plane->stan;
		$cost = (100-$stan) * ($typ->cena/50); // 1% ubytku stanu * cena samolotu/50
		$czas = (100-$stan) * $typ->klasa * 600; // 1% ubytku stanu * klasa = 10 min
		
		$post = $this->request->post();
		if( ! empty($post) && isset($post['send']) && $post['send'] == 'Napraw')
		{
			$plane->przegladGeneralny();
			$this->redirect('podglad');
		}
	}
	
	public function action_ulepszenia() {
		$this->template->title = "Ulepszenia";
		
		$this->template->content = View::factory('hangar/ulepszenia')
			->bind('planeId', $planeId)
			->bind('upgrades', $upgrades)
			->bind('upgraded', $upgraded)
			->bind('config', $config)
			->bind('ulepszenia', $ulepszenia);
			
		$user = Auth::instance()->get_user();
		if ( ! $user)
			$this->redirect('user/login');
		
		$planeId = (int)$this->request->param('id');
		
		if($planeId == 0)
		{
			sendError('Wystąpił błąd. Spróbuj ponownie.');
			$this->redirect('warsztat');
		}

		$plane = ORM::factory("UserPlane", $planeId);
		if( ! $plane->loaded() || $user->id != $plane->user_id) {
			sendError('Błąd. Nie ma takiego samolotu.');
			$this->redirect('warsztat');
		}
		
		if( $plane->isBusy() != Helper_Busy::NotBusy)
		{
			sendError('Samolot jest aktualnie w użyciu.');
			$this->redirect('warsztat');
		}
		$model = $plane->getUpgradedModel();
		
		$config = (array)Kohana::$config->load('upgrades');
		$configUpgrades = $config['upgrades'];
		$upgraded = json_decode($plane->upgrades, true);
		$upgrades = json_decode($model->upgrades, true);
		
		$post = $this->request->post();
		if( ! empty($post))
		{
			try {
				$punkty = (isset($post['punkty']));
				$itemy = (isset($post['itemy']));
				$category = $post['category'];
				$element = $post['element'];
				$ul = (int)(isset($upgraded[$category][$element])) ? $upgraded[$category][$element] : 0;
				if($ul >= 5)
					return sendError('Już osiągnięto maksymalny poziom tego ulepszenia.');
				if($itemy)
				{
                    $cost = round($model->klasa * 2000 * pow(1.4, $ul + 1));
                    if($user->cash >= $cost) {
                        $info = array('plane_id' => $plane->id, 'type' => Helper_Financial::Warsztat);
                        $user->operateCash(-$cost, 'Ulepszenie samolotu - ' . $plane->rejestracja . '.', time(), $info);
                        $upgraded[$category][$element]++;
                        $plane->upgrades = json_encode($upgraded);
                        $plane->save();
                        sendMsg('Samolot został ulepszony.');
                        $user->profil();
                        $this->redirect('warsztat/ulepszenia/' . $planeId);
                    } else
                        sendError('Nie masz wystarczającej ilości pieniędzy.');
				} elseif($punkty)
				{
                    $cost = round($model->klasa * 2 * pow(1.3, $ul + 1));
                    if($user->premium_points >= $cost) {
                        $info = array('plane_id' => $plane->id, 'type' => Helper_Financial::Warsztat);
                        $user->operatePoints(-$cost, 'Ulepszenie samolotu - ' . $plane->rejestracja . '.', time(), $info);
                        $upgraded[$category][$element]++;
                        $plane->upgrades = json_encode($upgraded);
                        $plane->save();
                        sendMsg('Samolot został ulepszony.');
                        $user->profil();
                        $this->redirect('warsztat/ulepszenia/' . $planeId);
                    } else
                        sendError('Nie masz wystarczającej ilości punktów.');
				
				}
			} catch(Exception $e) {
				echo $e->getMessage();
			}
		}
		
		
		$this->template->modals = '<div class="modal fade" id="upgradeModal" tabindex="-1" role="dialog" aria-labelledby="upgradeModalLabel" aria-hidden="true">
					  <div class="modal-dialog">
						<div class="modal-content">
						</div>
					  </div>
					</div>';
	}
};