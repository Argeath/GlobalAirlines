<?php defined('SYSPATH') or die('No direct script access.');

class Controller_JSConnect extends Controller {
	public function action_index()
	{
		$user = Auth::instance()->get_user();
		if ( ! $user)
		{
			$this->response->status(403);
			return false;
		}

		$clientID = "241338015";
		$secret = "c321c04970f362b3ee6725a85a62fb26";

		$userInfo = array();

		$userInfo['uniqueid'] = $user->id;
		$userInfo['name'] = $user->username;
		$userInfo['email'] = $user->email;
		$userInfo['photourl'] = $user->getAvatar();

		$secure = "sha1";
		$jsconnect = new JSConnect();
		$jsconnect->WriteJsConnect($userInfo, $_GET, $clientID, $secret, $secure);
	}
};