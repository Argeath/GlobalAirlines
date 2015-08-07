<?php defined('SYSPATH') or die('No direct script access.');

class Controller_User extends Controller_Template {

	public function action_index() {
		$this->template->title = "Powitanie";
		$ref = (int) $this->request->param('ref');
		if ($ref > 0) {
			Session::instance()->bind('ref', $ref);
		}

		$this->template->title = "Strona powitalna";

		$this->template->content = View::factory('welcome');
		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$this->redirect('podglad');
	}

	public function action_create() {
		$this->template->title = "Rejestracja konta";
		$this->template->content = View::factory('user/create')
		     ->bind('errors', $errors)
		     ->bind('ref_user', $ref_user)
		     ->bind('message', $message);

		$user = Auth::instance()->get_user();
		if ($user) {
			$this->redirect('user/index');
		}

		$ref_user = null;
		$ref = (int) Session::instance()->get('ref', 0);
		if ($ref > 0) {
			$ref_user = ORM::Factory("User", $ref);
			if (!$ref_user->loaded()) {
				$ref_user = null;
			}
		}

		if (HTTP_Request::POST == $this->request->method()) {
			try {

				$valid = Validation::factory($this->request->post());
				$valid->rule('csrf', 'not_empty');
				$valid->rule('csrf', 'Security::check');
				if ($valid->check()) {
					// Create the user using form values
					$user = ORM::factory('User')->create_user($this->request->post(), array(
						'username',
						'password',
						'email',
					));

					// Grant user login role
					$user->add('roles', ORM::factory('role', array('name' => 'login')));

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

					// Reset values so form is not sticky
					$_POST = array();

					// Set success message
					$this->action_login();
				}

			} catch (ORM_Validation_Exception $e) {
				// Set errors using custom messages
				$errors = $e->errors('models');
			}
		}
	}

	public function action_login() {

		$this->template->title = "Strona Główna";
		$user = Auth::instance()->get_user();
		if ($user) {
			$this->redirect('user/index');
		}
		$this->template->content = View::factory('user/login')
		     ->bind('message', $message);

		if (HTTP_Request::POST == $this->request->method()) {
			// Attempt to login user
			//$remember = array_key_exists('remember', $this->request->post()) ? (bool) $this->request->post('remember') : FALSE;
			$remember = FALSE;

			$valid = Validation::factory($this->request->post());
			$valid->rule('csrf', 'not_empty');
			$valid->rule('csrf', 'Security::check');
			if ($valid->check()) {
				$user = Auth::instance()->login($this->request->post('username'), $this->request->post('password'), $remember);
				// If successful, redirect user
				if ($user) {
					$this->redirect('user/index');
				} else {
					$message = 'Niepoprawny login lub hasło.';
				}
			}
		}
	}

	public function action_logout() {
		// Log user out
		Auth::instance()->logout();

		session_destroy();

		// Redirect to login page
		$this->redirect('user/login');
	}

	public function action_changePassword() {
		$this->template->title = "Zmiana hasła";

		$this->template->content = View::factory('user/password');

		$user = Auth::instance()->get_user();

		if (!$user) {
			$this->redirect('user/login');
		}

		if ($user->facebook > 0) {
			sendError('Nie możesz zmieniać hasła do konta zalogowanego przez Facebook.');
			$this->redirect('podglad');
		}

		$post = $this->request->post();
		if ($post && !empty($post)) {
			$valid = Validation::factory($post);
			$valid->rule('csrf', 'not_empty');
			$valid->rule('csrf', 'Security::check');
			if ($valid->check()) {
				$hashed = Auth::instance()->hash_password($post['old_password']);
				if ($hashed === $user->password) {
					try
					{
						$values = array('password' => $post['new_password'], 'password_confirm' => $post['new2_password']);
						$user->update_user($values);
						sendMsg('Hasło zostało zmienione. Zaloguj się ponownie, używając nowego hasła.');
						$this->action_logout();
					} catch (ORM_Validation_Exception $e) {
						foreach ($e->errors('models') as $err) {
							sendError($err);
						}
					}
				} else {
					sendError('Złe hasło.');
				}
			}
		}
	}

	public function action_deleteAccount() {
		$this->template->title = "Usuwanie konta";

		$this->template->content = View::factory('user/delete');

		$user = Auth::instance()->get_user();

		if (!$user) {
			$this->redirect('user/login');
		}

		$post = $this->request->post();
		if ($post && !empty($post)) {
			$valid = Validation::factory($post);
			$valid->rule('csrf', 'not_empty');
			$valid->rule('csrf', 'Security::check');
			if ($valid->check()) {
				$hashed = Auth::instance()->hash_password($post['password']);
				if ($hashed === $user->password) {
					$newEvent = ORM::factory("Event");
					$newEvent->user_id = $user->id;
					$newEvent->when = time() + (30 * 24 * 60 * 60);
					$newEvent->type = 6;
					$newEvent->save();
					sendMsg('Twoje konto zostanie usunięte za 30 dni.');
					Auth::instance()->logout();
					$this->redirect('user/login');
				} else {
					sendError('Złe hasło.');
				}
			}
		}
	}

	public function action_avatar() {
		$this->template->title = "Zmiana awatara";

		$this->template->content = View::factory('user/avatar')
		     ->bind('avatar', $avatar);

		$user = Auth::instance()->get_user();
		$filename = NULL;
		$avatar = NULL;

		if (!$user) {
			$this->redirect('user/login');
		}

		$post = $this->request->post();
		if ($post && !empty($post)) {
			$valid = Validation::factory($post);
			$valid->rule('csrf', 'not_empty');
			$valid->rule('csrf', 'Security::check');
			if ($valid->check()) {
				if (isset($_FILES['avatar'])) {
					$filename = $this->_save_image($_FILES['avatar']);
				}

				if (!$filename) {
					sendError('There was a problem while uploading the image.
						Make sure it is uploaded and must be JPG/PNG/GIF file.');
				}
				$user->avatar = $filename;
				$user->save();
			}
			$this->redirect('user/avatar', 303);
		}
	}

	protected function _save_image($image) {
		if (
			!Upload::valid($image) OR
			!Upload::not_empty($image) OR
			!Upload::type($image, array('jpg', 'jpeg', 'png', 'gif'))) {
			return FALSE;
		}

		$directory = DOCROOT . 'uploads/';

		if ($file = Upload::save($image, NULL, $directory)) {
			$filename = strtolower(Text::random('alnum', 20)) . '.jpg';

			Image::factory($file)
				->resize(120, 120, Image::AUTO)
				->save($directory . $filename);

			// Delete the temporary file
			unlink($file);

			return $filename;
		}

		return FALSE;
	}
}
?>