<?php

class Logger {

  public static function log($type, $message) {
    if (is_array($message)) $message = implode( ", ", $message );
    try {
      openlog("$type", LOG_PID | LOG_PERROR, LOG_LOCAL0);
      syslog(ErrorType::$$type || $INFO, $message);
      closelog();
    } catch (Error $e) {
      openlog("INFO", LOG_PID | LOG_PERROR, LOG_LOCAL0);
      syslog(ErrorType::$INFO, $message);
      closelog();
    }
  }

}

abstract class ErrorType {
  public static $EMERG = LOG_EMERG;
  public static $ALERT = LOG_ALERT;
  public static $ERROR = LOG_ERR;
  public static $WARNING = LOG_WARNING;
  public static $INFO = LOG_INFO;
  public static $DEBUG = LOG_DEBUG;
}

?>