<?php defined('SYSPATH') OR die('No direct access allowed.');

abstract class Auth extends Kohana_Auth {
	public function get_user($default = NULL) {
		$usr = $this->_session->get($this->_config['session_key'], $default);
		if (!$usr) {
			$FB = Facebook::instance();
			if ($FB->isLogged()) {
				$me = $FB->getMe();
				$fb = $me->getId();

				$user = ORM::factory("User")->where('facebook', '=', $fb)->find();

				if (!$user->loaded()) {
					$user = ORM::factory("User");
					$user->facebook = $fb;
					$ref = Session::instance()->get('ref');
					if ($ref && (int) $ref > 0) {
						$referrer = ORM::Factory("User", $ref);
						if ($referrer->loaded()) {
							$user->referrer_id = $referrer->id;
							$user->cash = 75000;
							$user->save();

							$contact = ORM::Factory("Contact");
							$contact->user_id = $referrer->id;
							$contact->user2_id = $user->id;
							$contact->accepted = 1;
							$contact->save();
						}
					}
					$user->email = $me->getProperty('email');
					if (ORM::factory("User")->where('email', '=', $me->getProperty('email'))->count_all() > 0) {
						$user->email = "duplicateEmail" . rand() % 10000 . "@facebook.com";
					}
					$user->password = Auth::instance()->hash_password(md5(rand()));
					$user->save();
				}
				$export = var_export($me, TRUE);
				$user->updateFbData($export);
				if ($user->loaded()) {
					$usr = $user;
				}
			}
		}

		if ($usr) {
			$dlt = $usr->isBeingDeleted();
			if ($dlt) {
				sendError('Twoje konto jest w trakcie usuwania. Zostanie usunięte: ' . timestampToText($dlt) . '. Aby anulować usuwanie skontaktuj się z administratorem.');
				return $default;
			}
			if ($usr->bannedTo > time()) {
				sendError('Twoje konto jest zbanowane do ' . timestampToText($usr->bannedTo, true) . '.');
				return $default;
			}
			if ($usr->last_login <= time() - 900) {
				$usr->last_login = time();
				$usr->save();
			}
			return $usr;
		}
		return $default;
	}
}