<?php

namespace Controllers {

	use \System\Core\Controller;
	use \Models\IndexModel;

	class IndexController extends Controller {

		public function index () {

			$this->view('Index1');
			
		}

	}

}

?>
