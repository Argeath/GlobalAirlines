<div class="well">
	<div class="page-header">
		<h1>Twoje samoloty</h1>
	</div>
	<ul class="pagination pagination-xm">
		<li class='active'><?=HTML::anchor('samoloty', 'Samoloty');?>
		<li><?=HTML::anchor('warsztat', 'Warsztat');?>
	</ul>
	<div class="thumbnail">
	<table class='table table-striped'>
		<?
			if( ! empty($planesData)) {
				foreach($planesData as $data) {
					echo "<tr>
						<td><img src='" . URL::base(TRUE) . "assets/samoloty/" . $data['plane']->plane_id . ".jpg' class='img-rounded hidden-xs' style='width: 150px;'/><br />" . $data['plane']->fullName() . "<br />(" . $data['position'] . ")" . $data['accidentText'] . "</td>
						<td>
							<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Klasa samolotu' style='display:inline-block;width: 160px; margin-bottom: 30px;'>" . $data['klasa'] . "</div>
							<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Zasięg samolotu' style='display:inline-block;width: 120px; margin-bottom: 30px;'><i class='fa fa-arrows-h'></i> " . $data['typ']->zasieg . " km</div>
							<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Miejsca pasażerskie' style='display:inline-block;width: 70px; margin-bottom: 30px;'><i class='fa fa-users'></i> " . $data['typ']->miejsc . "</div>
							<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Spalanie silników' style='display:inline-block;width: 120px; margin-bottom: 30px;'><i class='fa fa-fire'></i> " . $data['typ']->spalanie . " kg/km</div>
							<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Prędkość maksymalna' style='display:inline-block;width: 120px; margin-bottom: 30px;'><i class='fa fa-tachometer'></i> " . $data['typ']->predkosc . " km/h</div>
							<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Maksymalna wartość samolotu' style='display:inline-block;width: 120px; margin-bottom: 30px;'>" . WAL . " " . formatCash($data['wartosc']) . "</div>
						</td>
						<td>
							<div class='text-rounded " . (($data['pilotow'] == $data['typ']->piloci) ? 'bg-blue' : 'bg-red') . " Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Wymaganych pilotów' style='display:inline-block;width: 70px; margin-bottom: 30px;'><i class='fa fa-user'></i> " . $data['pilotow'] . " / " . $data['typ']->piloci . "</div>
							<div class='text-rounded " . (($data['stewardess'] == $data['typ']->zaloga_dodatkowa) ? 'bg-blue' : 'bg-red') . " Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Wymaganych stewardess' style='display:inline-block;width: 70px; margin-bottom: 30px;'><i class='fa fa-female'></i> " . $data['stewardess'] . " / " . $data['typ']->zaloga_dodatkowa . "</div>
							<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Mnożnik serwisowy' style='display:inline-block;width: 70px; margin-bottom: 30px;'><i class='glyphicon glyphicon-wrench'></i> x" . $data['typ']->mechanicy . "</div>
							<div class='text-rounded bg-blue Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Preferowane doświadczenie pilotów' style='display:inline-block;width: 70px; margin-bottom: 30px;'><i class='fa fa-graduation-cap'></i> " . ($data['plane']->getPreferStaffExp() + 5) . "%</div>
						</td>
			
						<td>" . $data['plane']->drawConditionBar() . "" . $data['plane']->drawAccidentChanceBar() . "Pokonana trasa: " . formatCash($data['plane']->km) . "km<br />Czasu w powietrzu: " . secondsToText($data['plane']->hours) . "</td>
						<td class='list-group'>
							" . HTML::anchor('samoloty/lotswobodny/' . $data['plane']->id, Form::submit('opcje', 'Lot swobodny', array('class' => "btn btn-primary btn-block btn-success"))) . "
							" . HTML::anchor('samoloty/zaloga/' . $data['plane']->id, Form::submit('opcje', 'Załoga', array('class' => "btn btn-primary btn-block"))) . "
							" . HTML::anchor('samoloty/rejestracja/' . $data['plane']->id, Form::submit('opcje', 'Zmiana rejestracji', array('class' => "btn btn-default btn-block btn-success"))) . "
							" . HTML::anchor('samoloty/wystaw/' . $data['plane']->id, Form::submit('opcje', 'Wystaw na aukcji', array('class' => "btn btn-default btn-block btn-warning"))) . "
							" . HTML::anchor('samoloty/sprzedaj/' . $data['plane']->id, Form::submit('opcje', 'Sprzedaj', array('class' => "btn btn-default btn-block btn-danger"))) . "
						</td></tr>";
				}
			} else {
				echo "<tr><td>Nie posiadasz żadnych samolotów.<br />
		             " . HTML::anchor('sklep', "Kup samolot", array('class' => 'btn btn-primary btn-medium'));
			} ?>
	</table>
	</div>
</div>