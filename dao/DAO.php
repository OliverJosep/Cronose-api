<?php

require_once '../utilities/DB.php';

class DAO {

  protected static $DB;

  public function __construct() {
    self::$DB = DB::connect();
  }

}
