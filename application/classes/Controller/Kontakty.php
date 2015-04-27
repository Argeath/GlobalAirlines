<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Kontakty extends Controller_Template {
	private $unaccepted = array();
	private $unacceptedT = "";
	private $sent = array();
	private $sentT = "";
	private $contacts = array();
	private $contactsT = "";
	public function action_index()
	{
		$this->template->title = "Kontakty";
		
		$this->template->content = View::factory('biuro/kontakty/kontakty')
		->bind('unaccepted', $this->unacceptedT)
		->bind('contacts', $this->contactsT);
		
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
		
		$this->unaccepted = $user->getNotAcceptedContacts();
		$this->sent = $user->getSentContacts();
		$this->contacts = $user->getContacts();
		
		$this->drawUnaccepted();
		$this->drawContacts($user);
		
		
	}
	
	private function drawUnaccepted()
	{
		foreach($this->unaccepted as $con)
		{
			$gracz = $con->user;
			$avatar = $gracz->getAvatar();
			$this->unacceptedT .= '<div class="contact thumbnail">'.HTML::anchor('profil/'.$gracz->id, $gracz->username).'<br /><img src="'.$avatar.'"/><br />';
			$this->unacceptedT .= Form::open('kontakty').'
				<input type="hidden" name="typ" value="akceptuj"/>
				<input type="hidden" name="new" value="'.$gracz->id.'"/>
	
				'.Form::submit('create', 'Akceptuj', array( 'class' => "btn btn-primary btn-block")).'
				'.Form::close();
			$this->unacceptedT .= Form::open('kontakty').'
				<input type="hidden" name="typ" value="odrzuc"/>
				<input type="hidden" name="new" value="'.$gracz->id.'"/>
	
				'.Form::submit('create', 'Odrzuć', array( 'class' => "btn btn-primary btn-block")).' 
				'.Form::close().'</div>'; //Zmienic na czerwony kolor buttona
			unset($gracz);
		}
	}
	
	private function drawContacts($user)
	{
		foreach($this->contacts as $con)
		{
			$gracz = NULL;
			if($con->user_id == $user->id)
				$gracz = $con->user2;
			else
				$gracz = $con->user;
			$avatar = $gracz->getAvatar();
			$refT = "";
			if($gracz->referrer_id == $user->id || $user->referrer_id == $gracz->id)
				$refT = '<i class="fa fa-share-alt Jtooltip" data-container=".main" data-toggle="tooltip" data-placement="bottom" title="Polecony/Polecający" style="position: absolute; top: 7px; right: 10px;"></i>';
			$this->contactsT .= '<div class="contact thumbnail" style="position: relative;">'.$gracz->drawButton().' '.$refT.'<br /><img src="'.$avatar.'" style="width: 100px; height: 100px;" class="img-thumbnail"/>'.HTML::anchor('poczta/new/'.$gracz->id, "<i class='glyphicon glyphicon-envelope'></i> Wyślij wiadomość", array('class' => 'btn btn-xs btn-primary')).'</div>';
		}
	}
};