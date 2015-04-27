<div class="well">
	<div class="page-header">
		<h1>Konto <small>Zmiana hasła</small></h1>
	</div>
	<?= Form::open('user/changePassword', array('class' => "form_300 well")); ?>
	<?= Form::password('old_password', NULL, array( 'class' => "form-control", 'placeholder' => "Stare hasło" )); ?>
	<?= Form::password('new_password', NULL, array( 'class' => "form-control", 'placeholder' => "Nowe hasło" )); ?>
	<?= Form::password('new2_password', NULL, array( 'class' => "form-control", 'placeholder' => "Powtórz nowe hasło" )); ?>
	
	<?= Form::submit('create', 'Zmień hasło', array( 'class' => "btn btn-primary btn-block")); ?>
	<?= Form::close(); ?>
</div>