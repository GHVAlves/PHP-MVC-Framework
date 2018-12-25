<?php

namespace System\Helpers {

	class FileHelper {

        public static function upload(string $file, string $newFile) {
            
            return move_uploaded_file($file, ROOT . '\\' . $newFile);

        }

        public static function delete(string $file) {

            return unlink(ROOT . '\\' . $file);

        }

        public static function getContent(string $file) {

            return file_get_contents($file);

        }

	}

}

?>
