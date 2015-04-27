<div class="well">
	<div class="page-header">
		<h1>Pracownik <small>Lot swobodny - wybór celu</small></h1>
	</div>
	<?= Form::open('kadry/lotswobodny', array( 'class' => "form_300")); ?>
	<input type="hidden" name="pracId" value="<?= $pracId; ?>"/>
	Wybierz cel lotu:
	<select name="dokad" class="form-control">
		<?= $citiesText; ?>
	</select>
	<div class="well">
		<div class="checkbox">
			<label>
				<input type="checkbox" id="pracPrzypiszCheckbox" name="pracPrzypiszCheckbox"/>
				Czy chcesz automatycznie przypisać do samolotu?
			</label>
		</div>
		<div id="pracPrzypiszDiv" style="display: none;">
			<select name="pracPrzypisz" id="pracPrzypisz" style="form-control">
				<option>Wybierz samolot</option>
				<?= $planesText; ?>
			</select>
		</div>
	</div>
	<?= Form::submit('send', 'Kontynuuj', array( 'class' => "btn btn-primary btn-block")); ?>
	<?= Form::close(); ?>
</div>


<script>
$(function() {
	$('#pracPrzypiszCheckbox').click(
		function () {
			if($(this).is(':checked'))
				$('#pracPrzypiszDiv').show();
			else {
				$('#pracPrzypiszDiv').hide();
				$('#pracPrzypisz').val("Wybierz samolot");
			}
        });

});
</script>
