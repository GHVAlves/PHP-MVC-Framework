<?php

namespace System\Core {

	use \System\Core\Interfaces\ControllerInterface;
	use \System\Helpers\SessionHelper;
	use \System\Helpers\AlertHelper;

	abstract class Controller implements ControllerInterface {

		/**
		 * Private properties
		 */
		private $requestUri;
		private $adminRequest;
		private $controller;
		private $action;
		private $view;


		/**
		 * Public properties
		 */
		public $post;
		public $get;

		public function __construct(Array $request) {

			SessionHelper::start();

			$this->adminRequest = strtolower($request['AdminRequest']);
			$this->controller = strtolower($request['Controller']);
			$this->action = strtolower($request['Action']);
			$this->requestUri = $request['RequestUri'];

			$current = array(
				'adminRequest' => $this->adminRequest, 
				'controller' => $this->controller, 
				'action' => $this->action
			);

			$this->view = new View($current);

			$this->setPostData();
			$this->setGetData();
			$this->setFilesData();

		}

		private function setPostData() {

			$this->post = $_POST;

		}

		private function setGetData() {

			$parameters = $this->requestUri;

			for ($i = 0; $i <= (($this->adminRequest) ? 3 : 2); $i++) {

				unset($parameters[$i]);
				
			}

			if (($parameters != null && count($parameters) > 0)) {

				$parameters = array_values($parameters);

			}

			for ($i = 1; $i <= count($parameters); $i++) {

				if (($i - 1) < (count($parameters) - 1)) {

					if (is_float($i / 2)) {

						$this->get[$parameters[$i - 1]] = $parameters[$i];

					}

				}

			}

		}

		private function setFilesData() {

			$this->files = $_FILES;

		}

		public function view(string $view, Array $data = null) {

			$this->view->view($view, $data);

		}

		public function masterView(string $master, string $view, Array $data = null) {

			$this->view->masterView($master, $view, $data);

		}

		public function getParameter(string $parameter) {

			for ($i = 0; $i < count($this->requestUri); $i++) {

				if ($this->requestUri[$i] == $parameter) {

					return $this->requestUri[$i + 1];

				}

			}

		}

		public function redirect(string $url) {
			
			header("location: $url");
			exit;

		}

		public function returnJson(Array $array) {

			header('Content-type: application/json; charset=utf-8;');
			
			echo json_encode($array, JSON_UNESCAPED_UNICODE);
			exit;

		}

		public function checkLogin(string $url, string $message = null) {

			if (!SessionHelper::get('Logged')) {

				AlertHelper::toastWarning((($message) ? $message : 'Sessão encerrada, acesse o sistema novamente!'));
				$this->redirect($url);

			}

		}

		public function get($callback) {

			$callback();

		}

	}

}

?>
