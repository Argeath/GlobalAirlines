<div class="well">
	<div class="page-header">
		<h1>Samolot <small>Lot swobodny - potwierdzenie</small></h1>
	</div>
	<?= Form::open('samoloty/lotswobodnywyslij/'.$planeId); ?>
	<table class="table table-striped">
		<tr><td style="min-width: 200px;">Z:</td> <td style="min-width: 150px;"><?= Map::getCityName($z); ?></td></tr>
		<tr><td>Do:</td> <td><?= Map::getCityName($dokad); ?></td></tr>
		<tr><td>Dystans:</td> <td><?= $distance; ?>km</td></tr>
		<tr><td>Zasięg samolotu:</td> <td><? if($zasieg < $distance) echo "<span style='color: red;'>"; echo $zasieg; ?>km<? if($zasieg < $distance) echo "</span>"; ?></td></tr>
		<tr><td>Przewidywany czas odprawy:</td> <td><?= secondsToText($odprawa) ?></td></tr>
		<tr><td>Przewidywany czas lotu:</td> <td><?= secondsToText($czas) ?></td></tr>
		<tr style="border-bottom: 1px #FFF solid;"><td>Potrzebne paliwo:</td> <td><?= formatCash($paliwo); ?>kg</td></tr>
		<tr><td>Stan samolotu:</td> <td><?= $stan; ?>%</td></tr>
		<tr style="border-bottom: 1px #FFF solid;"><td>Załoga:<br />
			Pilotów: <?= $pilotow; ?>/<?= $juzPilotow; ?><br />
		</td>
		<td><?= $zalogaT; ?></td></tr>
		<tr><td>Opłata za paliwo:</td> <td><?= formatCash($kosztP); ?> <?= WAL ?></td></tr>
		<tr><td>Honorarium dla załogi:</td> <td><?= formatCash($kosztZ); ?> <?= WAL ?></td></tr>
		<tr><td>Razem:</td> <td><span style="color: <? if($razem>0) echo 'green'; else echo 'red'; ?>"> <?= formatCash($razem); ?> <?= WAL ?></p> </td></tr>
	</table>
	<input type="hidden" name="dokad" value="<?= $dokad; ?>"/>
	<input type="hidden" name="start" value="<?= $startTimestamp; ?>"/>
	<?= Form::submit('send', 'Wyślij', array( 'class' => "btn btn-primary btn-block")); ?>
	<?= Form::close(); ?>
</div>