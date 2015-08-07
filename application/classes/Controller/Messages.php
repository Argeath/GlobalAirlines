<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Messages extends Controller_Template {
	private $received = array();
	private $sent = array();

	private $receivedText = "";
	private $sentText = "";

	public function action_index() {
		$this->template->title = "Wiadomości";

		$this->template->content = View::factory('biuro/poczta/poczta')
		     ->bind('typ', $typ)
		     ->bind('strona', $strona)
		     ->bind('offset', $offset)
		     ->bind('ilosc', $ilosc)
		     ->bind('naStrone', $naStrone)
		     ->bind('messages', $messages);

		$typ = $this->request->param('typ');

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$strona = (int) $this->request->param('offset');

		if ($strona > 0) {
			$strona--;
		}

		$naStrone = 10;
		$ilosc = 0;

		$offset = $strona * $naStrone;

		if ($typ == 1)//Odebrane
		{
			$ilosc = ORM::factory("Message")->where('user_id', '=', $user->id)->and_where('typ', '=', 1)->and_where('deleted', '=', 0)->count_all();
			$this->received = ORM::factory("Message")->where('user_id', '=', $user->id)->and_where('typ', '=', 1)->and_where('deleted', '=', 0)->order_by('data', 'desc')->limit($naStrone)->offset($offset)->find_all();
			$this->genReceived();
			$messages = $this->receivedText;
		} elseif ($typ == 2)//Wysłane
		{
			$ilosc = ORM::factory("Message")->where('user_id', '=', $user->id)->and_where('typ', '=', 2)->and_where('deleted', '=', 0)->count_all();
			$this->sent = ORM::factory("Message")->where('user_id', '=', $user->id)->and_where('typ', '=', 2)->and_where('deleted', '=', 0)->order_by('data', 'desc')->limit($naStrone)->offset($offset)->find_all();
			$this->genSent();
			$messages = $this->sentText;
		} else {
			//Zapisane
			{
				//TODO
			}
		}
	}

    // TODO: Move HTML to views
	private function genReceived() {
		$this->receivedText = "";
		foreach ($this->received as $rec) {
			$gracz = ORM::factory("User", $rec->sender);
			$this->receivedText .= '
				<tr class="poczta_tr clickableRow" wiadId="' . $rec->id . '" href="' . URL::site("poczta/show/" . $rec->id) . '">
					<td class="non-clickable"><input type="checkbox" name="selected[]" value="' . $rec->id . '"/></td>
					<td>' . timestampToText($rec->data) . '</td>
					<td>' . $gracz->drawButton() . '</td>
					<td width="50%">' . strip_tags($rec->title) . '</td>
				</tr>';
		}
		if (empty($this->receivedText)) {
			$this->receivedText = '<tr><td colspan="4">Brak wiadomości</td></tr>';
		}
	}

    // TODO: Move HTML to views
	private function genSent() {
		$this->sentText = "";
		foreach ($this->sent as $rec) {
			$gracz = ORM::factory("User", $rec->sender);
			$this->sentText .= '
				<tr class="poczta_tr clickableRow" wiadId="' . $rec->id . '" href="' . URL::site("poczta/show/" . $rec->id) . '">
					<td class="non-clickable"><input type="checkbox" name="selected[]" value="' . $rec->id . '"/></td>
					<td>' . timestampToText($rec->data) . '</td>
					<td>' . $gracz->drawButton() . '</td>
					<td width="50%">' . strip_tags($rec->title) . '</td>
				</tr>';
		}
		if (empty($this->sentText)) {
			$this->sentText = '<tr><td colspan="4">Brak wiadomości</td></tr>';
		}
	}

	public function action_new() {
		$this->template->title = "Nowa wiadomość";
		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$nickname = NULL;
		$typ = (int) $this->request->param('typ');
		if ($typ > 0) {
			$typGracz = ORM::factory("User", $typ);
			if ($typGracz->loaded()) {
				$nickname = $typGracz->username;
			}
		}

		$post = $this->request->post();
		if ($post && isset($post['message']) && isset($post['title']) && (isset($post['receiver']) || isset($post['receiver2']))) {
			$nick = "";
			if (!empty($post['receiver2'])) {
				$nick = $post['receiver2'];
			} elseif (!empty($post['receiver'])) {
				$nick = $post['receiver'];
			}

			if (!empty($nick)) {
				$gracz = ORM::factory("User")->where('username', 'LIKE', $nick)->find();
				if ($gracz->loaded()) {
					$msg = ORM::factory("Message");
					$msg->user_id = $user->id;
					$msg->sender_id = $gracz->id;
					$msg->data = time();
					$msg->title = strip_tags($post['title']);
					$msg->message = strip_tags($post['message']);
					$msg->typ = 2;
					$msg->save();

					$msg2 = ORM::factory("Message");
					$msg2->user_id = $gracz->id;
					$msg2->sender_id = $user->id;
					$msg2->data = time();
					$msg2->title = strip_tags($post['title']);
					$msg2->message = strip_tags($post['message']);
					$msg2->typ = 1;
					$msg2->save();

					sendMsg('Wiadomość została wysłana.');
					$this->redirect('poczta');
				} else {
					sendError('Nie znaleziono takiego gracza.');
				}
			}
		}

		$this->template->content = View::factory('biuro/poczta/new')
		     ->bind('contacts', $contacts)
		     ->bind('nickname', $nickname);

		$contacts = "";
		$kontakty = $user->getContacts();
		foreach ($kontakty as $contact) {
			if ($contact->user_id == $user->id) {
				$gracz = $contact->user2;
			} else {
				$gracz = $contact->user;
			}

			$contacts .= '<option>' . $gracz->username . '</option>';
		}
	}

	public function action_delete() {
		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$post = $this->request->post();

		if (!empty($post['selected'])) {
			foreach ($post['selected'] as $sel) {
				$msg = ORM::factory("Message", $sel);
				if (!$msg->loaded() || $msg->deleted == 1) {
					sendError("Nie ma takiej wiadomości.");
					return $this->redirect('poczta');
				}
				if ($msg->user_id != $user->id) {
					sendError("To nie jest twoja wiadomość.");
					return $this->redirect('poczta');
				}
				$msg->deleted = 1;
				$msg->save();
			}
		}
		sendMsg("Zaznaczone wiadomości zostały usunięte.");
		$this->redirect('poczta');
	}

	public function action_show() {
		$this->template->title = "Szczegóły wiadomości";

		$this->template->content = View::factory('biuro/poczta/show')
		     ->bind('sender', $sender)
		     ->bind('message', $msg);

		$user = Auth::instance()->get_user();
		if (!$user) {
			$this->redirect('user/login');
		}

		$id = (int) $this->request->param('typ');
		if (!$id || $id == 0) {
			sendError('Wystapil blad. Sprobuj ponownie.');
			$this->redirect('poczta');
			return false;
		}

		$msg = ORM::factory("Message", $id);
		if (!$msg->loaded() || !$msg->user_id == $user->id) {
			sendError('Wystapil blad. Sprobuj ponownie.');
			$this->redirect('poczta');
			return false;
		}
		$sender = ORM::factory("User", $msg->sender);

		if ($msg->checked == 0) {
			$msg->checked = 1;
			$msg->save();

		}
	}
};