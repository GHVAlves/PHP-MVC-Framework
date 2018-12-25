<?php

namespace System\Helpers {

    use \System\Helpers\SessionHelper;

	class AlertHelper {

        public static function toast(string $type, string $title) {
            
            $toast = array('type' => $type, 'title' => $title);

            SessionHelper::create("ToastSession", $toast);

        }

        public static function toastSuccess(string $title) {

            self::toast('success', $title);

        }

        public static function toastError(string $title) {

            self::toast('error', $title);

        }

        public static function toastWarning(string $title) {

            self::toast('warning', $title);

        }

        public static function toastInfo(string $title) {

            self::toast('info', $title);

        }

        public static function showToast() {

            if (SessionHelper::exists('ToastSession')) {

                $toast = SessionHelper::get('ToastSession');

                echo "<script type='text/javascript'>app.toast('" . $toast['type'] . "', '" . $toast['title'] . "');</script>";

                SessionHelper::remove('ToastSession');

            }

        }

	}

}

?>
