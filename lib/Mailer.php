<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Markdownify\Converter;

class Mailer {

    protected $mailer;
    protected $transport;
    protected $options;

    public function __construct($transport = 'mail', $options = array()) {

        $this->transport = $transport;
        $this->options = $options;
    }

    public function mail($to, $subject, $message, $options = []) {

        $options = array_merge($this->options, is_array($options) ? $options: []);

        $message = $this->createMessage($to, $subject, $message, $options);

        if (isset($options['from'])) {
            $message->setFrom($options['from'], $options['from_name'] ?? '');
        }

        if (isset($options['reply_to'])) {
            $message->addReplyTo($options['reply_to']);
        }

        return $message->send();
    }

    public function createMessage($to, $subject, $message, $options=[]) {

        $mail = new PHPMailer();

        if ($this->transport == 'smtp') {

            $mail->isSMTP();

            if (isset($this->options['host']) && $this->options['host'])      {
                $mail->Host = $this->options['host']; // Specify main and backup server
            }

            if (isset($this->options['auth']) && $this->options['auth']) {
                $mail->SMTPAuth = $this->options['auth']; // Enable SMTP authentication
            }

            if (isset($this->options['user']) && $this->options['user']) {
                $mail->Username = $this->options['user']; // SMTP username
            }

            if (isset($this->options['password']) && $this->options['password']) {
                $mail->Password = $this->options['password']; // SMTP password
            }

            if (isset($this->options['port']) && $this->options['port']) {
                $mail->Port = $this->options['port']; // smtp port
            }

            if (isset($this->options['encryption']) && $this->options['encryption']) {
                $mail->SMTPSecure = $this->options['encryption']; // Enable encryption: 'ssl' , 'tls' accepted
            }

            // Extra smtp options
            if (isset($this->options['smtp']) && is_array($this->options['smtp'])) {
                $mail->SMTPOptions = $this->options['smtp'];
            }
        }

        $markdown = new Converter();

        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = $markdown->parseString($message);
        $mail->CharSet = 'utf-8';

        $to_array = explode(",", $to);

        foreach ($to_array as $to_single) {
            $mail->addAddress($to_single);
        }

        if ($mail->Body != $mail->AltBody) {
            $mail->IsHTML(true); // Set email format to HTML
        }

        if (isset($options['embedded'])) {
            foreach ($options['embedded'] as $id => $file) {
                $mail->AddEmbeddedImage($file, $id);
            }
        }

        $msg = new Mailer_Message($mail);

        return $msg;
    }

}

class Mailer_Message {

    public $mail;

    public function __construct($mail) {
        $this->mail = $mail;
    }

    public function setCharset($charset) {
        $this->mail->CharSet = $charset;
    }

    public function setSubject($subject) {
        $this->mail->Subject = $subject;
    }

    public function setFrom($email, $name=false) {
        $this->mail->From = $email;
        $this->mail->FromName = $name ? $name : $email;
    }

    public function addReplyTo($email, $name='') {
        $this->mail->addReplyTo($email, $name);
    }

    public function addTo($email, $name = '') {
        $this->mail->AddAddress($email, $name);
    }

    public function addCC($email, $name = '') {
        $this->mail->AddCC($email, $name);
    }

    public function send() {
        return $this->mail->Send();
    }

    public function attach($file, $alias='') {
        return $this->mail->AddAttachment($file, $alias);
    }
}
