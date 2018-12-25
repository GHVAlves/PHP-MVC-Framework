<?php

namespace System\Helpers {

	class SessionHelper {

        public static function start() {

            if (session_status() == PHP_SESSION_NONE) {

                session_start();

            }

        }
        
        public static function create(string $name, $value) {

            $_SESSION[$name] = $value;

        }

        public static function remove(string $name) {

            if (self::exists($name)) {
                
                unset($_SESSION[$name]);

            }

        }

        public static function get(string $name) {

            if (self::exists($name)) {
                
                return $_SESSION[$name];

            }
            else {

                return null;

            }

        }

        public static function exists(string $name) {

            return ((isset($_SESSION[$name])) ? true : false);

        }

        public static function removeAll() {

            session_destroy();

        }

	}

}

?>
