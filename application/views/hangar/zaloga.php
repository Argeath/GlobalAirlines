<div class="well">
	<div class="page-header">
		<h1>Samolot <small>Zarządzanie załogą</small></h1>
	</div>
	<div class='text-rounded <?=(($juzPilotow < $pilotow) ? 'bg-red' : 'bg-blue') ?> Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Wymaganych pilotów' style='display:inline-block;width: 70px;'><i class='fa fa-user'></i> <?= $juzPilotow ?> / <?= $pilotow ?></div> 
	<div class='text-rounded <?=(($juzDodatkowej < $dodatkowej) ? 'bg-red' : 'bg-blue') ?> Jtooltip' data-container='.main' data-toggle='tooltip' data-placement='bottom' title='Wymaganych stewardess' style='display:inline-block;width: 70px;'><i class='fa fa-user'></i> <?= $juzDodatkowej ?> / <?= $dodatkowej ?></div> 
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
		<?= $kadry; ?>
	</tbody>
	</table>
	<div class="box" style="width: 300px; margin: 20px auto;">
		Przypisz nowego załoganta:<br />
		<?= Form::open('samoloty/zaloga/'.$planeId); ?>
		<select name="pracId" class="form-control">
			<?= $zaloga; ?>
		</select>
		<?= Form::submit('opcje', 'Przypisz', array( 'class' => "btn btn-primary btn-block")); ?>
		<?= Form::close(); ?>
	</div>
	<?= HTML::anchor('kadry', "Kadry", array('class' => 'btn btn-primary btn-medium')); ?>
	<?= HTML::anchor('samoloty', 'Wróć', array('class' => "btn btn-default btn-small")); ?>
</div>