<?php

class Controller_Facebook_Callback extends Controller {

	function action_index()
	{
		$fb = FB::factory();
		if ($fb->getUser())
		{
			$fb->getAccessToken();
		}
		$this->response->body('<script>window.close();</script>');
	}
}
