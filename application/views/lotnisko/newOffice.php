<div class="well">
	<div class="page-header">
		<h1>Wykup biuro na lotnisku <small><?= $lotnisko->getName(); ?></small></h1>
	</div>
	
	<?= Form::open('airport/newOffice/'.$city_id, array( 'class' => "form_300 well")); ?>
	<input type="checkbox" name="potwierdzenie" value="tak"/> Czy jesteś pewien, że chcesz wykupić biuro?<br /><br />
	<button class="btn btn-primary btn-block">Koszt: <?= formatCash($cena).' '.WAL ?></button>
	<?= Form::close(); ?>
</div>