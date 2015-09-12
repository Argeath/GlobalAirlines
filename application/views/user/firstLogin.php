<div class="row">
	<div class="col-xs-12">
		<div class="well">
			<div class="page-header">
				<h1>Konto <small>Wybieranie nazwy</small></h1>
			</div>
			Wybór nazwy gracza:
			<?= Form::open('firstLogin', array('class' => "form_300")); ?>
			<input type="text" name="nazwa" class="form-control"/>
			<?= Form::submit('choose', 'Wybierz', array( 'class' => "btn btn-primary btn-block")); ?>
			<?= Form::close(); ?>
		</div>
	</div>
</div>