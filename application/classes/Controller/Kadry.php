<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Kadry extends Controller_Template {
	public function action_index()
	{
		$this->template->content = "";
		
		$user = Auth::instance()->get_user();
		if ( ! $user)
			$this->redirect('user/login');
		
		$this->redirect('kadry/zarzadzaj');
	}
	
	public function action_zatrudnij()
	{
		$this->template->content = "";
		$user = Auth::instance()->get_user();
		if ( ! $user)
			$this->redirect('user/login');
			
		$planeId = (int)$this->request->param('id');
		if($planeId > 0)
		{
			$plane = ORM::factory("UserPlane", $planeId);
			if($plane->user_id != $user->id)
				$this->redirect('kadry/zarzadzaj');
			$planeModel = $plane->plane;
		}
		$przypisal = false;
		
		$post = $this->request->post();
		if(isset($post))
		{
			if(!empty($post['type']) && ((int)$post['type'] == 1 || (int)$post['type'] == 2) && ((int)$post['experience'] >= 0 || (int)$post['experience'] <= 3))
			{
				$zalogant = $user->hireStaff((int)$post['type'], (int)$post['experience']);
				if($planeId > 0)
				{
					$pilotow = $planeModel->piloci;
					$dodatkowej = $planeModel->zaloga_dodatkowa;
					$juzPilotow = $plane->staff->where('type', '=', 'pilot')->count_all();
					$juzDodatkowej = $plane->staff->where('type', '!=', 'pilot')->count_all();
					if($zalogant->loaded() && (($zalogant->type == 'pilot' && ($juzPilotow < $pilotow)) || ($zalogant->type == 'stewardessa' && $juzDodatkowej < $dodatkowej)) && $zalogant->user_id == $user->id)
					{
						if( $zalogant->position == $plane->position)
						{
							$zalogant->plane_id = $plane->id;
							$zalogant->save();
							$przypisal = true;
						}
					}
				}
				sendMsg('Zatrudniłeś pracownika.');
				if( $planeId > 0 && $przypisal)
					$this->redirect('kadry/zarzadzaj/'.$planeId);
				$this->redirect('kadry/zarzadzaj');
			}		
		}
		$this->redirect('kadry/zarzadzaj');
	}
	
	public function action_zarzadzaj()
	{
		$this->template->title = "Zarządzanie kadrą";
		
		$this->template->content = View::factory('kadry/zarzadzaj')
			->bind('planeId', $planeId)
			->bind('plane', $plane)
			->bind('planes', $planes)
			->bind('kadry', $kadry);
			
		$user = Auth::instance()->get_user();
		if ( ! $user)
			$this->redirect('user/login');
			
		/*$sta = ORM::factory("Staff")->find_all();
		foreach($sta as $s)
			$s->onLevelAdvance();*/
		
		$post = $this->request->post();
		if(isset($post))
		{
			if(isset($post['pracId']) && (int)$post['pracId'] != 0 && isset($post['opcje']) && $post['opcje'] == 'Zwolnij')
			{
				$zalog = ORM::factory("Staff", (int)$post['pracId']);
				if( $zalog->loaded() ) {
					if($zalog->user_id == $user->id && $zalog->plane_id == 0)
					{
						$zalog->delete();
						sendMsg('Zwolniłeś pracownika.');
					} else
						sendError('Pracownik nie może być zwolniony, ponieważ jest przypisany do samolotu.');
				} else
					sendError('Nie ma takiego pracownika.');
			}
		}
		$nieprzypisani = false;
		$planeId = (int)$this->request->param('id');
		if($planeId == 0) {
			$nieprzypisani = true;
		} else {
			$plane = ORM::factory("UserPlane", $planeId);
			if(! $plane->loaded() || $plane->user_id != $user->id)
			{
				sendError('Wystąpił błąd. Spróbuj ponownie.');
				$this->redirect('kadry/zarzadzaj');
			}
		}
		
		$planes = $user->UserPlanes->find_all();
		if($nieprzypisani)
			$q = $user->staff->where('plane_id', 'IS', NULL)->order_by('type', 'ASC')->order_by('experience', 'DESC')->find_all();
		else
			$q = $plane->staff->order_by('type', 'ASC')->order_by('experience', 'DESC')->find_all();
		foreach($q as $r)
		{
			$plane = $r->UserPlane;
			$planeText = "";
			if($plane->loaded())
				$planeText = " (".$plane->rejestracja.")";
			if($r->isPracBusy())
				$planeText .= "<br />(W powietrzu)";
				
			$kadry .= "<tr>
						<td>".$r->name." (".$r->type.")<br />".Map::getCityName($r->position)."".$planeText."</td>
						<td>".$r->drawConditionBar()."</td>
						<td>".$r->drawExperienceBar()."</td>
						<td>".$r->drawSatisfactionBar()."</td>
						<td>
							<span class='wage'>".$r->wage."</span> ".WAL." / 100km";
			if( ! $r->isPracBusy())
				$kadry .= "<div id='slider_".$r->id."' staffId='".$r->id."'></div>
							<script>
								$(function() {
									$('#slider_".$r->id."').slider({
									  value:".$r->wage.",
									  min: ".($r->wantedWage-25) .",
									  max: ".($r->wantedWage+25) .",
									  step: 5,
									  slide: function( event, ui ) {
										$(this).parent().find('.wage').text(ui.value);
									  },
									  stop: function( event, ui ) {
										var arr = { wage: ui.value };
										ajaxManager.addReq({
										   type: 'POST',
										   url: url_base() + 'ajax/staffWage/' + ".$r->id.",
										   data: arr,
										   success: function(data){}
										});
									  }
									});
								});
							</script>";
							
			$kadry .=	"</td>
						<td>".Form::open('kadry/zarzadzaj')."<input type='hidden' name='pracId' value='".$r->id."'/>".Form::submit('opcje', 'Zwolnij', array( 'class' => "btn btn-primary btn-block btn-danger"))."".Form::close()."
						".Form::open('kadry/lotswobodny')."<input type='hidden' name='pracId' value='".$r->id."'/>".Form::submit('opcje', 'Lot', array( 'class' => "btn btn-default btn-block btn-success"))."".Form::close()."</td>
					   </tr>";		
		}
		if(empty($kadry))
			$kadry = "<tr><td colspan='6'>Brak przypisanej załogi.</td></tr>";
	}
	public function action_lotswobodny()
	{
		$this->template->title = "Lot swobodny pracownika";
		
		$this->template->content = View::factory('kadry/lotswobodny')
			->bind('pracId', $pracId)
			->bind('planesText', $planesText)
			->bind('citiesText', $citiesText);
		$pracId = 0;
		$dokad = 0;
		$citiesText = "";
		$planesText = "";
		
		$user = Auth::instance()->get_user();
		if ( ! $user)
			$this->redirect('user/login');
		
		$post = $this->request->post();
		
		if(isset($post))
		{
			if(isset($post['pracId']) && (int)$post['pracId'] != 0)
			{
				$zalog = ORM::factory("Staff", (int)$post['pracId']);
				if($zalog->user_id != $user->id)
				{
					sendError('To nie jest twój pracownik.');
					$this->redirect('kadry/zarzadzaj');
				}
				if($zalog->UserPlane->loaded())
				{
					sendError('Pracownik nie może być przypisany do samolotu.');
					$this->redirect('kadry/zarzadzaj');
				}
				$pracId = $zalog->id;
					
				if(isset($post['dokad']) && isset($post['send']) && $post['send'] == 'Kontynuuj')
				{
					$dokad = $post['dokad'];
					$this->template->content = View::factory('kadry/lotswobodny2')
						->bind('pracId', $pracId)
						->bind('z', $z)
						->bind('distance', $distance)
						->bind('czas', $czas)
						->bind('koszt', $koszt)
						->bind('dokad', $dokad);
						
					$z = $zalog->position;
					$distance = $zalog->city->getDistanceTo($dokad);
					
					$czas = $distance / 945;
					$czas = $czas * 3600;
					
					$koszt = $distance * 0.5;
					
				} elseif(isset($post['dokad']) && isset($post['send']) && $post['send'] == 'Wyślij')
				{
					$dokad = $post['dokad'];
					$planeId = false;
					if(isset($post['pracPrzypisz']) && (int)$post['pracPrzypisz'] > 0)
					{
						$plane = $user->UserPlanes->where('id', '=', $post['pracPrzypisz'])->find();
						if($plane->loaded())
							$planeId = $plane->id;
					}
					if($zalog->lotSwobodnyZaloga($dokad, $planeId))
						sendMsg('Pracownik wyruszył do miasta '.Map::getCityName($dokad).'.');
					else
						sendError('Wystąpił błąd. Spróbuj ponownie.');
					$this->redirect('kadry/zarzadzaj');
				} else {
					$cities = ORM::factory('City')->order_by('name', 'asc')->find_all();
					foreach($cities as $city)
					{
						if($city->id != $zalog->position)
							$citiesText .= "<option value='".$city->id."'>".$city->name."</option>";
					}
					
					$planes = $user->UserPlanes->find_all();
					foreach($planes as $p)
						$planesText .= "<option value='".$p->id."'>".$p->rejestracja."</option>";
				}
			} else {
				sendError('Błąd. Spróbuj ponownie.');
				$this->redirect('kadry/zarzadzaj');
			}
		} else {
			sendError('Błąd. Spróbuj ponownie.');
			$this->redirect('kadry/zarzadzaj');
		}
	}
}