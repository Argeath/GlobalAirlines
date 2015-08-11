<div class="well">
	<div class="page-header">
		<h1>Pracownik <small>Lot swobodny - potwierdzenie</small></h1>
	</div>
	<?= Form::open('kadry/lotswobodny'); ?>
	<table style="margin: 0 auto;" class="table table-striped">
		<tr><td style="min-width: 200px;">Z:</td> <td style="min-width: 150px;"><?= Map::getCityName($z); ?></td></tr>
		<tr><td>Do:</td> <td><?= Map::getCityName($dokad); ?></td></tr>
		<tr><td>Dystans:</td> <td><?= $distance; ?>km</td></tr>
		<tr><td>Przewidywany czas lotu:</td> <td><?= TimeFormat::secondsToText($czas) ?></td></tr>
		<tr><td>Koszt:</td> <td><?= $koszt; ?> <?= WAL ?></td></tr>
	</table>
	<input type="hidden" name="dokad" value="<?= $dokad; ?>"/>
	<input type="hidden" name="pracId" value="<?= $pracId; ?>"/>
	<?= Form::submit('send', 'Wyślij', array( 'class' => "btn btn-primary btn-block")); ?>
	<?= Form::close(); ?>
</div>