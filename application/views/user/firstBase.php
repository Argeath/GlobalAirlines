<div class="well">
	<div class="page-header">
		<h1>Konto <small>Wybór siedziby</small></h1>
	</div>
	Wybór miejsca siedziby - pierwszej(głównej) bazy. W głównej bazie będziesz zatrudniał załogę. 	
	<?= Form::open('firstBase', array('class' => "form_300")); ?>
	<select name="baza" class="form-control">
		<?
		$cities = ORM::factory('City')->order_by('name', 'desc')->find_all();	
		foreach($cities as $city)
		{
			echo "<option value='".$city->id."'>".$city->name."</option>";
		}
		?>
	</select>
	<?= Form::submit('choose', 'Wybierz', array( 'class' => "btn btn-primary btn-block")); ?>
	<?= Form::close(); ?>

</div>