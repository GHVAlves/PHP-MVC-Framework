<?php

namespace System\Core {

    use \System\Helpers\AlertHelper;

	class View {

        public $current;

        public function __construct(Array $current) {

            $this->current = $current;

        }

        public function view(string $view, Array $data = null) {

            $current = $this->current;
            
			$view = ROOT . DIRECTORY_SEPARATOR . APPLICATION_FOLDER . DIRECTORY_SEPARATOR . '\views\\' . $view . 'View.phtml';

			if (file_exists($view)) {

				require_once $view;				
				AlertHelper::showToast();

			}
			else {

				die("View '$view' not found.");

			}

		}

		public function masterView(string $master, string $view, Array $data = null) {

			$data['view'] = $view . 'View.phtml';

			$this->view($master, $data);

		}

		public function exists(Array $data = null, string $key = null) {

			if ($data && isset($data[$key])) {

				return $data[$key];

			}

		}

    }

}

?>
