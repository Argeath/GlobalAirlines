<div class="well">
	<div class="page-header">
	  <h1>Warsztat <small>Przegląd generalny samolotu</small></h1>
	</div>
	<table class='table table-striped'>
		<tr><td style="min-width: 200px;">Samolot</td><td style="min-width: 150px;"><?= $name; ?></td></tr>
		<tr><td>Stan</td><td><?= round($stan, 2); ?>%</td></tr>
		<tr><td>Koszt napraw</td><td><?= formatCash($cost, 0, false); ?> <?= WAL; ?></td></tr>
		<tr><td>Czas napraw</td><td><?= TimeFormat::secondsToText($czas, true); ?> </td></tr>
	</table>
	<?= Form::open('warsztat/przeglad/'.$planeId); ?>
		<?= Form::submit('send', 'Napraw', array( 'class' => "btn btn-primary")); ?>
	</form>

</div>