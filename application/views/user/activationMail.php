<p>Witaj <?= $user->username ?>!</p>

<p>Dziękujemy za rejestrację w serwisie Global Airlines Simulator.</p>

<p>
    Twoje dane:<br />
    Login: <?= $user->username ?><br />
    Email: <?= $user->email ?><br />
    Hasło: (podane przy rejestracji)
</p>

<p>
    W celu ukończenia rejestracji konta i jego aktywacji, prosimy kliknąć w poniższy link:<br />
    <a href="<?= URL::base(TRUE, TRUE) ?>user/activate/<?= $user->activation_hash ?>"><?= URL::base(TRUE, TRUE) ?>user/activate/<?= $user->activation_hash ?></a><br />
    Jeśli link nie działa, prosimy o kontakt z administracją.</p>

<p>
    Jeśli nie rejestrowałeś(aś) się w serwisie Global Airlines Simulator, należy zignorować tę wiadomość.<br />
    Wiadomość została wygenerowana automatycznie. Twoja odpowiedź na ten email nie zostanie nigdy odczytana.
</p>

<p>
    Życzymy miłej gry!
</p>