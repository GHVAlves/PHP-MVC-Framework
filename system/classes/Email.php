<?php

namespace System\Classes {

    use \PHPMailer\PHPMailer\PHPMailer;
    use \PHPMailer\PHPMailer\Exception;

    use System\Helpers\FileHelper;

    class Email {

        private $mail;
        private $template;
        private $templateValues;

        public function __construct() {

            $this->mail = new PHPMailer(true);

            $ini = parse_ini_file(ROOT . DIRECTORY_SEPARATOR . SYSTEM_PATH . '/config.ini', true);

            $this->mail->SMTPDebug = 0;                       // Enable verbose debug output
            $this->mail->isSMTP();                            // Set mailer to use SMTP
            $this->mail->Host = $ini['smtp']['host'];         // Specify main and backup SMTP servers
            $this->mail->SMTPAuth = true;                     // Enable SMTP authentication
            $this->mail->Username = $ini['smtp']['username']; // SMTP username
            $this->mail->Password = $ini['smtp']['password']; // SMTP password
            $this->mail->SMTPSecure = 'tls';                  // Enable TLS encryption, `ssl` also accepted
            $this->mail->Port = $ini['smtp']['port'];         // TCP port to connect to

        }

        public function setSubject(string $subject) {

            $this->mail->Subject = $subject;

        }

        public function setFrom(string $email, string $name = null) {

            $this->mail->setFrom($email, $name);

        }

        public function addAddress(string $email, string $name = null) {

            $this->mail->addAddress($email, $name);

        }

        public function addAttachment(string $file, string $name) {

            $this->mail->addAttachment($file, $name);

        }

        public function setContent() {

            $arguments = func_get_args();

            $content = '';

            if (count($arguments) === 1) {

                $this->mail->isHTML(false);

                $content = $arguments[0];

            }
            else if (count($arguments) === 2) {

                $this->mail->isHTML(true);

                $content = FileHelper::getContent(ROOT . DIRECTORY_SEPARATOR . APPLICATION_FOLDER . '/views/email/' . $arguments[0] . '.html');

                $parameters = $arguments[1];

                foreach ($parameters as $key => $value) {
                    
                    $content = str_replace("$$key", $value, $content);

                }

            }

            $this->mail->Body = $content;

        }

        public function setTemplate(string $template) {

            $this->template = $template;

        }

        public function send() {

            $this->mail->send();

        }


    }

}

?>
