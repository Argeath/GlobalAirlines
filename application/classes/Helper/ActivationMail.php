<?php

class Helper_ActivationMail {

    private $user;

    public function __construct($user) {
        $this->user = $user;
    }

    public function send() {
        $view = View::factory('user/activationMail')
            ->bind('user', $this->user);

        Email::factory('Link aktywacyjny')
            ->message((string)$view, 'text/html')
            ->to($this->user->email)
            ->from('noreply@planes.vipserv.org', 'GlobalAirlinesSimulator')
            ->send();
    }
}