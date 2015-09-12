<div class="row">
	<div class="col-xs-12">
		<div class="well">
			<div class="page-header">
				<h1>Samolot <small>Zarządzanie załogą</small></h1>
			</div>
			<div class='text-rounded <?=(($juzPilotow < $pilotow) ? 'bg-red' : 'bg-blue')?> Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Wymaganych pilotów' style='display:inline-block;width: 70px;'><i class='fa fa-user'></i> <?=$juzPilotow?> / <?=$pilotow?></div>
			<div class='text-rounded <?=(($juzDodatkowej < $dodatkowej) ? 'bg-red' : 'bg-blue')?> Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Wymaganych stewardess' style='display:inline-block;width: 70px;'><i class='fa fa-user'></i> <?=$juzDodatkowej?> / <?=$dodatkowej?></div>
			<table class="table table-striped">
			<thead>
				<tr>
					<th>Załogant</th>
					<th>Szansa na wypadek</th>
					<th>Doświadczenie</th>
					<th>Stan</th>
					<th>Opcje</th>
				</tr>
			</thead>
			<tbody>
			<?
				if( ! empty($staffData)) {
					foreach($staffData as $data) {
						echo "<tr>
								<td>" . $data['staff']->name . " (" . $data['staff']->type . ")</td>
								<td>" . $data['staff']->drawAccidentChanceBar() . "</td>
								<td>" . $data['staff']->drawExperienceBar() . "</td>
								<td>" . $data['staff']->drawConditionBar() . "</td>
								<td>" . Form::open('samoloty/zaloga/' . $planeId) . "<input type='hidden' name='pracId' value='" . $data['staff']->id . "'/>" . Form::submit('opcje', 'Odwołaj', array('class' => "btn btn-primary btn-block")) . "" . Form::close() . "</td>
							  </tr>";
					}
				} else {
					echo "<tr><td colspan='5'>Brak załogi przypisanej do tego samolotu</td></tr>";
				}
			?>
			</tbody>
			</table>
			<div class="box" style="width: 300px; margin: 20px auto;">
				Przypisz nowego załoganta:<br />
				<?=Form::open('samoloty/zaloga/' . $planeId);?>
				<select name="pracId" class="form-control">
					<? foreach($zaloga as $staff) {
						echo "<option value='" . $staff->id . "'>" . $staff->name . " (" . $staff->type . ")</option>";
					} ?>
				</select>
				<?=Form::submit('opcje', 'Przypisz', array('class' => "btn btn-primary btn-block"));?>
				<?=Form::close();?>
			</div>
			<?=HTML::anchor('kadry', "Zatrudnij", array('class' => 'btn btn-success btn-medium'));?>
			<?=HTML::anchor('samoloty', 'Wróć', array('class' => "btn btn-default btn-small"));?>
		</div>
	</div>
</div>