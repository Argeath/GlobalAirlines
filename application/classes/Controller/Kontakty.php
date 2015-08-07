<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Kontakty extends Controller_Template {
	private $sent = array();
	private $sentT = "";
	public function action_index()
	{
		$this->template->title = "Kontakty";
		
		$this->template->content = View::factory('biuro/kontakty')
			->bind('unaccepted', $unaccepted)
			//->bind('sent', $sent)
			->bind('contacts', $contacts);
		
		$user = Auth::instance()->get_user();
		if ( ! $user)
		{
			$this->redirect('user/login');
		}
		$post = $this->request->post();
		if($post && !empty($post))
		{
			$new = ORM::factory("User", $post['new']);
			if($new->loaded())
			{
				if($post['typ'] == 'nowy')
					$user->addContact($new);
				if($post['typ'] == 'akceptuj')
					$user->acceptContact($new);
				if($post['typ'] == 'odrzuc')
					$user->refuseContact($new);
			}
		}		
		
		$unaccepted = $user->getNotAcceptedContacts()->as_array();
		//$sent = $user->getSentContacts()->as_array();
		$contacts = $user->getContacts()->as_array();
	}
};