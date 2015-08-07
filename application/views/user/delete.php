<div class="well">
	<div class="page-header">
		<h1>Konto <small>Usunięcie konta</small></h1>
	</div>
	Aby usunąć konto, potwierdź swój wybór poprzez podanie hasła do konta. Twoje konto zostanie trwale usunięte po 30 dniach.
	Przemyśl to. Usuwanie będzie można jedynie anulować poprzez kontakt z administratorem i podanie sensownego wyjaśnienia.<br /><br />
<?=Form::open('user/deleteAccount', array('class' => "form_300"));?>
	<?=Form::hidden('csrf', Security::token())?>
	<?=Form::password('password', NULL, array('class' => "form-control", 'placeholder' => "Hasło"));?>
	<br />
	<?=Prints::rusureButton("Usuń konto", 'delete', '', ['btn-danger']);?>
	<?=Form::close();?>
</div>