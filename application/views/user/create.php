<div class="well col-lg-4 col-lg-offset-4 col-sm-8 col-sm-offset-2">
	<div id="logo"></div>
	<div class="first panel_fw">
		<div class="page-header">
			<h1>Rejestracja</h1>
		</div>
        <div id="validation-errors" class="alert alert-danger">
            <?= ((Arr::get($errors, 'username')) ? Arr::get($errors, 'username')."<br />"  : "") ?>
            <?= ((Arr::get($errors, 'email')) ? Arr::get($errors, 'email')."<br />" : "") ?>
            <?= ((Arr::path($errors, '_external.password')) ? Arr::path($errors, '_external.password')."<br />" : "") ?>
            <?= ((Arr::path($errors, '_external.password_confirm')) ? Arr::path($errors, '_external.password_confirm')."<br />" : "") ?>
        </div>

        <?=Form::open('user/create', ['id' => 'register_form'])?>
        <?=Form::hidden('csrf', Security::token())?>
        <div class="form-group">
            <label class="control-label">Nazwa konta:</label>
            <?=Form::input('username', HTML::chars(Arr::get($_POST, 'username')), array('class' => "form-control", 'placeholder' => "Login" ));?>
        </div>

        <div class="form-group">
            <label class="control-label">Adres email:</label>
            <?=Form::input('email', HTML::chars(Arr::get($_POST, 'email')), array('class' => "form-control", 'placeholder' => "Email"))?>
        </div>

        <div class="form-group">
            <label class="control-label">Hasło:</label>
            <?=Form::password('password', NULL, array('class' => "form-control", 'placeholder' => "Hasło"))?>
        </div>

        <div class="form-group">
            <label class="control-label">Powtórz hasło:</label>
            <?=Form::password('password_confirm', NULL, array('class' => "form-control", 'placeholder' => "Potwierdź hasło"))?>
        </div>

        <div class="form-group">
            <label class="control-label">Osoba polecająca:</label>
            <input type="text" class="form-control" placeholder="Polecający" value="<?=(isset($ref_user) && $ref_user != null) ? $ref_user->username : ''?>" readonly />
        </div>

        <?=Form::submit('create', 'Stwórz konto', array('class' => "btn btn-primary col-xs-12"));?>
        <?=Form::close();?>
    </div>

    <div class="clearfix"></div>
    <br />
    <?=HTML::anchor('user/login', '<i class="glyphicon glyphicon-backward"></i> Wróć', ['class' => "btn btn-small btn-default"]);?>
</div>

<script>
    $(function() {
        if($("#validation-errors").text().trim() == "") {
            $("#validation-errors").hide();
        }
        $("#register_form").formValidation({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            err: {
                container: 'tooltip'
            },
            fields: {
                username: {
                    validators: {
                        notEmpty: {
                            message: 'Nazwa użytkownika jest wymagana.'
                        },
                        stringLength: {
                            min: 3,
                            max: 32,
                            message: 'Nazwa użytkownika nie może być krótsza niż 3 i dłuższa niż 32 znaki.'
                        },
                        regexp: {
                            regexp: /^[a-zA-Z0-9_\s]+$/,
                            message: 'Nazwa użytkownika może zawierać jedynie litery, cyfry i odstępy.'
                        },
                        blank: {}
                    }
                },
                email: {
                    validators: {
                        notEmpty: {
                            message: 'Adres email jest wymagany.'
                        },
                        emailAddress: {
                            message: 'Podaj poprawny adres email.'
                        },
                        blank: {}
                    }
                },
                password: {
                    validators: {
                        notEmpty: {
                            message: 'Hasło jest wymagane'
                        },
                        stringLength: {
                            min: 8,
                            message: 'Hasło musi mieć przynajmniej 8 znaków.'
                        },
                        blank: {}
                    }
                },
                password_confirm: {
                    validators: {
                        notEmpty: {
                            message: 'Powtórz hasło'
                        },
                        identical: {
                            field: 'password',
                            message: 'Hasła nie są takie same.'
                        },
                        blank: {}
                    }
                }
            }
        });
    });
</script>