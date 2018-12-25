<?php

namespace System\Core {

	class Bootstrap {

		/**
		 * Private properties;
		 */		

		/**
		 * Public properties;
		 */
		public $requestUri;
		public $adminRequest;
		public $controller;
		public $action;

		public function __construct() {

			switch (ENVIRONMENT) {

				case 'development': {

					ini_set('display_errors', 1);
					ini_set('display_startup_erros', 1);
					error_reporting(E_ALL);

				}
				break;

				case 'production': {

					ini_set('display_errors', 0);
					ini_set('display_startup_erros', 0);
					error_reporting(0);

				}
				break;

				default: {

					exit('The application environment is not set correctly.');

				}

			}

			$this->setRequestUri();
			$this->setTypeRequest();
			$this->setController();
			$this->setAction();

		}

		private function setRequestUri() {

			$this->requestUri = explode('/', $_SERVER['REQUEST_URI']);

		}

		private function setTypeRequest() {

			$this->adminRequest = (($this->requestUri[1] === 'admin') ? true : false);
			
		}

		private function setController() {

			@$currentController = $this->requestUri[(($this->adminRequest) ? 2 : 1)];

			$this->controller = ((isset($currentController) && $currentController != '') ? $currentController : 'Index');

		}

		private function setAction() {

			@$currentAction = $this->requestUri[(($this->adminRequest) ? 3 : 2)];

			$this->action = ((isset($currentAction) && $currentAction != '') ? $currentAction : 'Index');

		}

		public function getRequestUri() {

			return $this->requestUri;

		}

		public function getController() {

			return $this->controller;

		}

		public function getAction() {

			return $this->action;

		}

		public function run() {

			if ($this->adminRequest) {
				
				$controller = 'controllers' . DIRECTORY_SEPARATOR . 'Admin' . $this->controller . 'Controller'; 

			}
			else {

				$controller = 'controllers' . DIRECTORY_SEPARATOR . $this->controller . 'Controller'; 

			}

			if (!file_exists(APPLICATION_FOLDER . DIRECTORY_SEPARATOR . $controller . '.php')) {

				die("Controller '$controller' not found.");

			}			
			else {

				$request = array(
					'RequestUri' => $this->requestUri,
					'AdminRequest' => $this->adminRequest,
					'Controller' => $this->controller,
					'Action' => $this->action
				);

				$controller = new $controller($request);
				$action = $this->action;

				if (!method_exists($controller, $action)) {

					die("Action '$action' not found.");

				}

				$controller->$action();

			}

		}

	}

}

?>
