<div class="well">
	<div class="page-header">
		<h1>Samolot <small>Zmiana rejestracji</small></h1>
	</div>
	<?= Form::open('samoloty/rejestracja/'.$planeId, array( 'class' => "form_300 well")); ?>
	Podaj nową rejestrację samolotu:
	<input type="text" class="form-control" name="nowa" placeholder="Nowa rejestracja"/>
	<?= Form::submit('send', 'Zmień', array( 'class' => "btn btn-primary btn-block")); ?>
	<?= Form::close(); ?>
</div>