<?php

namespace Models {

	use \System\Core\Model;

	class IndexModel extends Model {

		public function selectCountry() {

			$sql = ' SELECT * FROM dbo.Country ';

			return $this->executeSelect($sql);

		}

		public function selectState(Array $parameters = null) {

			return $this->executeProcedure('STP_SelectState', $parameters);

		}

	}

}

?>
