<div class="well col-lg-4 col-lg-offset-4 col-sm-8 col-sm-offset-2">
	<div id="logo"></div>
	<div class="first panel_fw">
		<div class="page-header">
			<h1>Rejestracja</h1>
		</div>
		<div class="list-group">
<?=Form::open('user/create')?>
<?=Form::hidden('csrf', Security::token())?>
			<?=Form::input('username', HTML::chars(Arr::get($_POST, 'username')), array('class' => "list-group-item col-xs-12", 'placeholder' =>
"Login" . ((Arr::get($errors, 'username')) ? " (" . Arr::get($errors, 'username') . ")" : ""),
));?>

<?=Form::input('email', HTML::chars(Arr::get($_POST, 'email')), array('class' => "list-group-item col-xs-12", 'placeholder' => "Email" . ((Arr::get($errors, 'email')) ? " (" . Arr::get($errors, 'email') . ")" : "")))?>

<?=Form::password('password', NULL, array('class' => "list-group-item col-xs-12", 'placeholder' => "Hasło" . ((Arr::path($errors, '_external.password')) ? " (" . Arr::path($errors, '_external.password') . ")" : "")))?>

<?=Form::password('password_confirm', NULL, array('class' => "list-group-item col-xs-12", 'placeholder' => "Potwierdź hasło" . ((Arr::path($errors, '_external.password_confirm')) ? " (" . Arr::path($errors, '_external.password_confirm') . ")" : "")))?>

			<input type="text" class="list-group-item col-xs-12" placeholder="Polecający" value="<?=(isset($ref_user) && $ref_user != null) ? $ref_user->username : ''?>" readonly />

<?=Form::submit('create', 'Stwórz konto', array('class' => "btn btn-primary col-xs-12"));?>
			<?=Form::close();?>
</div>
		<div class="clearfix"></div>
		<br />
<?=HTML::anchor('user/login', '<button class="btn btn-small btn-default"><i class="glyphicon glyphicon-backward"></i> Wróć</button>');?>
	</div>
</div>