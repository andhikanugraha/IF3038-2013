<?php
date_default_timezone_set('Asia/Jakarta');
// App
// The engine of the app

include_once "model/User.php";
include_once "model/Category.php";
include_once "model/Task.php";
include_once "model/Tag.php";
include_once "model/Comment.php";

class App 
{
	private $pagesDir = 'pages';
	private $templateDir = 'template';
	private $apiEndpoint = 'api';
	private $defaultPage = 'index';
	private $appName = 'MOA';
	private $appTagline = 'Multiuser Online Agenda';

	private $isPartial = false;
	private $javascripts = array();

	public $basePath;
	public $baseUrl;

	public $currentUserId;
	public $currentUser;
	public $loggedIn;

	// bootstrap
	public function bootstrap() 
	{
		error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

		$req = $_SERVER['REQUEST_URI'];
		$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

		$self = $_SERVER['PHP_SELF'];
		$root = str_replace('\\', '/', dirname($self));
		$selfFile = basename($self);
		$root = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], "/")+1);
		$path = substr($path, strlen($root));
		// $path = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], "/")+1);
		$fragments = explode('/', $path);

		// url-related params
		$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https" : "http";
		$this->basePath = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], "/")+1);
		$this->baseUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . $basePath;

		// Start session
		$this->startSession();

		// Check whether loading REST API or a page
		if ($fragments[0] == 'index.php') 
		{
			array_shift($fragments);
		}

		if ($fragments[0] == $this->apiEndpoint) 
		{
			// Load the REST API.
			require_once 'RestApi.php';

			$restApi = new RestApi($this);
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
				$restApi->params = $_POST;
			else
				$restApi->params = $_GET;

			$method = $fragments[1];

			$success = true;
			if (method_exists($restApi, $method)) 
			{
				// Method exists
				// TODO wrap this in try..catch (if tubes spec allows)
				try 
				{
					$output = call_user_func(array($restApi, $method), $restApi->params);
				}
				catch (Exception $e) 
				{
					$success = false;
					$output = array(
						'error_type' => 'exception',
						'message' => $e->getMessage()
					);
				}
			}
			else 
			{
				$success = false;
				$output = array(
					'error_type' => 'not_found',
					'message' => 'The REST API method ' . $method . ' does not exist.'
				);
			}

			header($success ? 'HTTP/1.1 200 OK' : 'HTTP/1.1 500 Internal Server Error');
			header('Content-type: application/json');
			echo json_encode($output);
		}
		else 
		{
			while ($fragments[0] == '' && count($fragments) > 1) 
			{
				array_shift($fragments);
			}
			if ($fragments[0] == 'partial') 
			{
				// Don't load the header; used for AJAX page navigation
				$this->isPartial = true;
				$page = count($fragments) > 1 ? $fragments[1] : $this->defaultPage;
			}
			else 
			{
				$page = $fragments[0] ? $fragments[0] : $this->defaultPage;
			}

			if (preg_match('/^(.*)\.php$/i', $page, $matches)) 
			{
				$page = $matches[1];
			}

			$this->loadPage($page);
		}
	}

	public function startSession() 
	{
		$session_time = 30*24*60*60;
		ini_set('session.gc-maxlifetime', $session_time);

		session_start();
		if ((!ISSET($_SESSION['base_url'])) || (!ISSET($_SESSION['full_url'])) || (!ISSET($_SESSION['full_path'])))
		{
			$folder = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], "/")+1);  
			$protocol = (ISSET($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https" : "http";
			$_SESSION['base_url'] = $folder;
			$_SESSION['full_url'] = $protocol . "://" . $_SERVER['HTTP_HOST'] . $folder;
			$_SESSION['full_path'] = $_SERVER['DOCUMENT_ROOT'].$folder;
		}

		$this->currentUserId = $_SESSION['user_id'];
		$this->currentUser = User::model()->find('id_user=' . $this->currentUserId, array("id_user","username","avatar","birthdate","fullname"));
		$this->loggedIn = (bool) $_SESSION['user_id'];
	}

	public function destroySession() 
	{
		session_destroy();
	}

	// Load the page specified in $page
	protected function loadPage($page) 
	{
		$file = $this->pagesDir . '/' . $page . '.php';

		if (file_exists($file))
			require_once($file);
		else
			require_once $this->pagesDir . '/404.php';
	}

	// Load the template specified in $tpl
	protected function loadTemplate($tpl) 
	{
		include $this->templateDir . '/' . $tpl . '.php';
	}

	// Require a JS file to be loaded in the footer
	// $js should not have '.js' appended to it
	protected function requireJS($js) 
	{
		if (!in_array($js, $this->javascripts))
			$this->javascripts[] = $js;
	}

	protected function header($title = '', $currentPage = '') 
	{
		if (!$this->isPartial) {
			$this->title = $title;
			$this->currentPage = $currentPage;
			$this->loadTemplate('header');
		}
	}

	protected function footer($breadcrumbs = array()) 
	{
		if (!$this->isPartial) {
			$this->loadTemplate('footer');
		}
	}

	protected function calendar() 
	{
		$this->loadTemplate('calendar');
	}
}
