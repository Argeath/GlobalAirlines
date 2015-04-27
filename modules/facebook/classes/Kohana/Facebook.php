<?php defined('SYSPATH') or die('No direct script access.');

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookCanvasLoginHelper;
use Facebook\FacebookJavaScriptLoginHelper;
use Facebook\Entities\SignedRequest;

class Kohana_Facebook {
	protected static $instance = null;
	private $connectionType = 0;
	private $session;
	private $loginUrl = "";
	private $helper;
	
	public function __construct()
	{
		FacebookSession::setDefaultApplication('587990371267671', '4456eb7c01074ce4e551dbde7cca6f24');
	}
	
	private function getSession()
	{
		if ( isset( $_SESSION ) && isset( $_SESSION['fb_token'] ) ) {
		  $session = new FacebookSession( $_SESSION['fb_token'] );
		 
			try {
			  if ( ! $session->validate() ) {
				$session = null;
				$_SESSION['fb_token'] = null;
				$this->getSession();
			  }
			} catch ( Exception $e ) {
			  $session = null;
			  $_SESSION['fb_token'] = null;
			  $this->getSession();
			}
		} else {
		  // No session exists
		  try {
			if($this->connectionType == 0)
				$session = $this->helper->getSessionFromRedirect();
			else
				$session = $this->helper->getSession();
		  } catch( FacebookRequestException $ex ) {
		 
			// When Facebook returns an error
		  } catch( Exception $ex ) {
		 
			// When validation fails or other local issues
			echo $ex->message;
		  }
		}
		 
		// Check if a session exists
		if ( isset( $session ) ) {

			$_SESSION['fb_token'] = $session->getToken();

			$session = new FacebookSession( $session->getToken() );
		}
		$this->session = $session;
		return $session;
	}
	
	public function getMe()
	{
		if( ! $this->isLogged())
			return false;
		
		return (new FacebookRequest($this->session, 'GET', '/me'))->execute()->getGraphObject(GraphUser::className());
	}
	
	public function isLogged()
	{
		return ($this->session);
	}
	
	public function getLoginUrl()
	{
		$scope = array('email', 'user_likes');
		if($this->helper)
			return $this->helper->getLoginUrl($scope);
		return "";
	}
	
	public function getLogoutUrl($next)
	{
		if($this->helper && $this->isLogged() && $this->connectionType == 0)
			return $this->helper->getLogoutUrl($this->session, $next);
		return "";
	}
	
	public function createSession($connectionType = 0, $redirectUrl = false)
	{
		$this->connectionType = $connectionType;
		if($this->connectionType == 0) // Redirect
		{
			return $this->createRedirectSession($redirectUrl);
		}
		elseif($this->connectionType == 1) // Canvas
		{
			return $this->createCanvasSession();
		}
		elseif($this->connectionType == 2) // JS
		{
			return $this->createJavascriptSession();
		}
		return false;
	}
	
	private function createRedirectSession($redirectUrl)
	{
		$this->helper = new FacebookRedirectLoginHelper($redirectUrl);
		$this->getSession();
		return true;
	}
	
	private function createCanvasSession()
	{
		$this->helper = new FacebookCanvasLoginHelper();
		$this->getSession();
		return true;
	}
	
	private function createJavascriptSession()
	{
		$this->helper = new FacebookJavaScriptLoginHelper();
		$this->session = $this->helper->getSession();
		return true;
	}
	
	static function isCanvas()
	{
		if(isset($_SESSION['inCanvas']) && $_SESSION['inCanvas'] == true)
			return true;
		if( isset($_REQUEST['signed_request']) ) {
			$signed_request = $_REQUEST['signed_request'];
			$data = SignedRequest::parse($signed_request, null, '4456eb7c01074ce4e551dbde7cca6f24');
			if(isset($data['user_id']))
			{
				$_SESSION['inCanvas'] = true;
				return true;
			}
		}
		return false;
	}
	
	static function instance()
	{
		if (!(self::$instance instanceof Kohana_Facebook))
		{
			self::$instance = new Kohana_Facebook;
		}
		return self::$instance;
	}

	static function factory()
	{
		return new Kohana_Facebook;
	}

};