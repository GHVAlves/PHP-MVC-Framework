<?php

namespace System\Helpers {

    use System\Helpers\SessionHelper;

	class RandomHelper {

        public static function number(int $length = 6) {
            
            $numbers = "0123456789";

            return substr(str_shuffle($numbers), 0, $length);

        }

        public static function string(int $length = 6) {
            
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

            return substr(str_shuffle($chars), 0, $length);

        }

	}

}

?>
