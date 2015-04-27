<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Warsztat extends Controller_Template {

	public function action_index()
	{
		$this->template->title = "Warsztat";
		$this->template->content = View::factory('hangar/warsztat')
			->bind('planesText', $planesText);
		
		$user = Auth::instance()->get_user();
		if ( ! $user)
		{
			$this->redirect('user/login');
		}
		
		$planes = $user->UserPlanes->find_all();
		$planesText = "";
		foreach($planes as $plane)
		{
			$typ = $plane->plane;
			$poz = $plane->city->name;
			$disabled = array();
			$disabledT = array();
			if($plane->isBusy() != Busy::NotBusy)
			{
				$disabled = array('disabled' => 'disabled');
				$disabledT = array('class' => 'disabled');
			}
			$upgradow = $plane->getUpgradesCount();
			if($upgradow == 0)
				$upgr = 100;
			else
				$upgr = ($plane->getUpgradedCount() / $plane->getUpgradesCount()) * 100;
			$planesText .= "<tr>
			<td><img src='".URL::base(TRUE)."assets/samoloty/".$plane->plane_id.".jpg' class='img-rounded hidden-xs' style='width: 100px;'/><br />".$plane->fullName()."<br />(".$poz.")</td>
			<td width='25%'>
				Ulepszeń: ".round($upgr, 2)."%<br />
				Stan: ".round($plane->stan, 0)."%<br />
				<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Mnożnik serwisowy' style='display:inline-block;width: 70px;'><i class='glyphicon glyphicon-wrench'></i> x".$typ->mechanicy."</div>
			</td>
			<td width='20%'>
				".HTML::anchor('warsztat/przeglad/'.$plane->id, Form::submit('opcje', 'Przegląd generalny', array_merge(array( 'class' => "btn btn-primary btn-block btn-success"), $disabled)), $disabledT)."
				".HTML::anchor('warsztat/ulepszenia/'.$plane->id, Form::submit('opcje', 'Ulepsz samolot', array_merge(array( 'class' => "btn btn-primary btn-block btn-primary"), $disabled)), $disabledT)."
			</td></tr>";
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
		{
			$this->redirect('user/login');
		}
		
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
		
		if( $plane->isBusy() != Busy::NotBusy)
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
		{
			$this->redirect('user/login');
		}
		
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
		
		if( $plane->isBusy() != Busy::NotBusy)
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
                        $info = array('plane_id' => $plane->id, 'type' => Financial::Warsztat);
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
                        $info = array('plane_id' => $plane->id, 'type' => Financial::Warsztat);
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