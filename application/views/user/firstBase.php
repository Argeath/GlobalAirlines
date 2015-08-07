<div class="well">
	<div class="page-header">
		<h1>Konto <small>Wybór siedziby</small></h1>
	</div>
	Wybór miejsca siedziby - pierwszej(głównej) bazy. W głównej bazie będziesz zatrudniał załogę.
	<?=Form::open('firstBase', array('class' => "form_300"));?>
	<select name="baza" class="selectpickerflag">
		<?
		foreach($cities as $city)
		{
			echo "<option value='".$city->id."' data-flag='".strtolower($city->getCountry())."'>".$city->name."</option>";
		}
		?>
	</select>
	<?=Form::submit('choose', 'Wybierz', array('class' => "btn btn-primary btn-block"));?>
	<?=Form::close();?>

</div>