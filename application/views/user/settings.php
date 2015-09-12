<div class="row">
    <div class="col-md-6 col-lg-4">
        <div class="well">
            <div class="page-header">
                <h1>Konto <small>Zmiana hasła</small></h1>
            </div>

            <?=Form::open('user/settings', ['id' => 'change_password_form']);?>
            <?=Form::hidden('csrf', Security::token())?>
            <div class="form-group">
                <label class="control-label">Aktualne hasło:</label>
                <?=Form::password('old_password', NULL, array('class' => "form-control", 'placeholder' => "Stare hasło"));?>
            </div>

            <div class="form-group">
                <label class="control-label">Nowe hasło:</label>
                <?=Form::password('new_password', NULL, array('class' => "form-control", 'placeholder' => "Nowe hasło"));?>
            </div>

            <div class="form-group">
                <label class="control-label">Powtórz nowe hasło:</label>
                <?=Form::password('password_confirm', NULL, array('class' => "form-control", 'placeholder' => "Powtórz nowe hasło"));?>
            </div>
            <input type="hidden" name="action" value="change-password"/>
            <?=Form::submit('create', 'Zmień hasło', array('class' => "btn btn-primary btn-block"));?>
            <?=Form::close();?>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="well">
            <div class="page-header">
                <h1>Konto <small>Zmiana avatara</small></h1>
            </div>
            <table class="table table-striped">
                <tr>
                    <td class="col-md-3 col-sm-6 col-xs-12">
                        <img src="<?= URL::base(TRUE) ?>uploads/<?= isSet($avatar) ? $avatar : 'avatar.jpg' ?>" alt="" style="max-width: 120px; height: auto; max-height: 120px;" class="img-thumbnail"/>
                    </td>
                    <td>
                        <form id="upload-form" action="<?= URL::site('user/settings')?>" method="post" enctype="multipart/form-data" onsubmit="return checkSize(2097152)">
                            <?=Form::hidden('csrf', Security::token())?>
                            Wybierz nowy awatar. Dopuszczalne rozrzeszenia: jpg, png, gif. Maksymalna rozdzielczość: 120x120px. Maksymalny rozmiar: 2MB.
                            <input type="hidden" name="MAX_FILE_SIZE" value="2097152" />
                            <input type="file" name="avatar" id="avatar" class="form-control" />
                            <input type="hidden" name="action" value="change-avatar"/>
                            <input type="submit" name="submit" id="submit" value="Wgraj nowy" class="btn btn-primary btn-block"/>
                        </form>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="col-md-6 col-lg-4">
        <div class="well">
            <div class="page-header">
                <h1>Konto <small>Usunięcie konta</small></h1>
            </div>
            Aby usunąć konto, potwierdź swój wybór poprzez podanie hasła do konta. Twoje konto zostanie trwale usunięte po 30 dniach.
            Przemyśl to. Usuwanie będzie można jedynie anulować poprzez kontakt z administratorem i podanie sensownego wyjaśnienia.<br /><br />
            <?=Form::open('user/settings', array('class' => "form_300"));?>
            <?=Form::hidden('csrf', Security::token())?>
            <?=Form::password('password', NULL, array('class' => "form-control", 'placeholder' => "Hasło"));?>
            <br />

            <input type="hidden" name="action" value="delete-account"/>
            <?=Helper_Prints::rusureButton("Usuń konto", 'delete', '', ['btn-danger']);?>
            <?=Form::close();?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        if($("#validation-errors").text().trim() == "") {
            $("#validation-errors").hide();
        }
        $("#change_password_form").formValidation({
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
                new_password: {
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
                            field: 'new_password',
                            message: 'Hasła nie są takie same.'
                        },
                        blank: {}
                    }
                }
            }
        });
    });
</script>

<script type="text/javascript">
    function checkSize(max_img_size)
    {
        var input = document.getElementById("upload");
        // check for browser support (may need to be modified)
        if(input.files && input.files.length == 1)
        {
            if (input.files[0].size > max_img_size)
            {
                alert("The file must be less than " + (max_img_size/1024/1024) + "MB");
                return false;
            }
        }

        return true;
    }
</script>