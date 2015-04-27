<div class="well">
	<div class="page-header">
		<h1>Samolot <small>Lot swobodny - wybór celu</small></h1>
	</div>
	<?= Form::open('samoloty/lotswobodny/'.$planeId, array( 'class' => "form_300 well")); ?>
	Wybierz cel lotu:
	<select name="dokad" class="form-control">
		<?= $citiesText; ?>
	</select>
	Planowany start:
	<div id="planowany_start" class="input-group date" style="float: none; margin: 0 auto;">
		<input id="planowany_start_input" data-format="dd/MM/yyyy hh:mm:ss" type="text" name="planowany_start" class="form-control"></input>
		<span class="input-group-addon add-on">
			<i id="planowany_start_button" data-time-icon="icon-time" data-date-icon="icon-calendar" class="glyphicon glyphicon-calendar"></i>
		</span>
	</div>
	<?= Form::submit('send', 'Kontynuuj', array( 'class' => "btn btn-primary btn-block")); ?>
	<?= Form::close(); ?>
</div>