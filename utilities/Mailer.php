<?php

class Mailer {
  
  private static $defaultFrom = "cronose@cronose.dawman.info";

  public static function sendMailTo($subject, $message, $to, $from = null, $headers = "") {
    $from = $from ?? self::$defaultFrom;
    $headers.= "From: ${from}\r\n";
    $message = wordwrap($message, 70);
    mail($to, $subject, $message, $headers);
  }

}


?>