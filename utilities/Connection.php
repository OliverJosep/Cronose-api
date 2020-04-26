<?php

class Connection {
  public static function make($config) {
    try {
      $pdo = new PDO('mysql:dbname='. $config['database']['name']
        .';host='.$config['database']['host'].';charset=utf8',
        $config['database']['username'],
        $config['database']['password']
      );
      return $pdo;
    } catch (PDOException $e) {
      die($e->getMessage());
    }
  }
}