<?php

class Core_FB extends Facebook {
	public $config;
	protected static $instance = null;

	public function __construct()
	{
		$this->config = Kohana::$config->load('FB')->as_array();

		parent::__construct($this->config);
	}

	public function getLoginUrl($params = array())
	{
		if (empty($params))
		{
			$params = array(
				'display' => 'popup',
				'redirect_uri' => URL::site(Route::get('FB-auth')->uri(), "https"),
				'scope' => 'email'
			);
		}
		return parent::getLoginUrl($params);
	}

	static function instance()
	{
		if (!(self::$instance instanceof Core_FB))
		{
			self::$instance = new Core_FB;
		}
		return self::$instance;
	}

	static function factory()
	{
		return new Core_FB;
	}
}
