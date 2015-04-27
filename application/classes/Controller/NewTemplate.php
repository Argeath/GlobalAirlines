<?php defined('SYSPATH') or die('No direct script access.');

class Controller_NewTemplate extends Controller {
	public function action_index()
	{
		$this->response->body(View::factory('newtemplate'));
	}
};