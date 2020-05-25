<?php

require_once '../dao/Cancellation.dao.php';

class CancellationController {

  public static function getAll($lang) {
    return CancellationDAO::getAll($lang);
  }

  public static function get($id, $lang) {
    return CancellationDAO::get($id, $lang);
  }

}