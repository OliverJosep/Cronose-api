<?php

require_once '../utilities/Connection.php';
require_once '../config/config.php';

class DB {
  public static function connect() {
    global $config;
    return Connection::make($config);
  }
}
