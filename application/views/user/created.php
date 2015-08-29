<div class="well col-lg-4 col-lg-offset-4 col-sm-8 col-sm-offset-2">
    <div id="logo"></div>
    <div class="first panel_fw">
        <div class="page-header">
            <h1>Konto zostało założone</h1>
        </div>
        <div class="list-group">
            Twoje konto zostało założone. Na podany adres email został wysłany link aktywacyjny. Kliknij go, aby aktywować konto.
        </div>
        <div class="clearfix"></div>
        <br />
        <?=HTML::anchor('user/resendActivation', 'Wyślij ponownie link aktywacyjny', ['class' => "btn btn-small btn-warning"]);?><br /><br />
        <?=HTML::anchor('user/logout', '<i class="glyphicon glyphicon-backward"></i> Wróć', ['class' => "btn btn-small btn-default"]);?>
    </div>
</div>